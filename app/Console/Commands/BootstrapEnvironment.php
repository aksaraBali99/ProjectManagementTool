<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Services\Import\EmployeeIdGenerator;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator as ValidatorContract;

/**
 * Sets up a fresh environment (dev or production) with just the two
 * things every environment needs before anyone can log in: the 5 system
 * roles + permissions, and exactly one Owner account. Deliberately does
 * NOT seed companies, departments, projects, tasks, or any other
 * business/demo data — that's DatabaseSeeder's job for local dev, not
 * this command's. Interactive (not a plain `db:seed` run) because the
 * Owner's real identity has to differ per environment.
 *
 * `--reset-owner` is a separate mode entirely: break-glass recovery for
 * when every owner/super_admin is locked out and there's no way in
 * through the web UI. See "Emergency access recovery" in CLAUDE.md.
 */
class BootstrapEnvironment extends Command
{
    protected $signature = 'solva:bootstrap
        {--reset-owner : Break-glass recovery — reset an existing Owner/Super Admin\'s password instead of creating a new account}';

    protected $description = 'Seeds reference data (roles + permissions) and creates exactly one Owner account — no companies, departments, projects, or test users.';

    public function __construct(private readonly EmployeeIdGenerator $employeeIdGenerator)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('reset-owner')) {
            return $this->handleResetOwner();
        }

        $this->info('Solva environment bootstrap');
        $this->line('Seeds the 5 system roles + permissions, then creates one Owner account. Never touches companies, departments, projects, or tasks.');
        $this->newLine();

        $this->seedReferenceData();

        if (! $this->confirmProceedingIfOwnerExists()) {
            $this->warn('Aborted — no owner account created.');

            return self::SUCCESS;
        }

        $details = $this->promptForOwnerDetails();
        if ($details === null) {
            $this->error('Aborted — the details entered did not pass validation. Re-run the command to try again.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('About to create:');
        $this->line("  Username:    {$details['username']}");
        $this->line("  Name:        {$details['name']}");
        $this->line("  Employee ID: {$details['employee_id']}");
        $this->line("  Email:       {$details['email']}");
        $this->line("  Phone:       {$details['phone']}");
        $this->line('  Roles:       Super Admin, Owner (both global)');
        $this->newLine();

        if (! $this->confirm('Create this owner account?', true)) {
            $this->warn('Aborted — no owner account created.');

            return self::SUCCESS;
        }

        $this->createOwner($details);

        return self::SUCCESS;
    }

    private function seedReferenceData(): void
    {
        $this->line('Seeding reference data (roles + permissions)...');

        // Both seeders already use updateOrCreate() keyed by slug — safe
        // to run against a database that's partially or fully seeded
        // already, without duplicating or erroring on existing rows.
        (new RoleSeeder)->run();
        (new PermissionSeeder)->run();

        $this->info('Reference data is up to date.');
        $this->newLine();
    }

    private function confirmProceedingIfOwnerExists(): bool
    {
        $existingOwnerCount = User::whereHas('roles', fn ($query) => $query->where('slug', Role::OWNER))->count();

        if ($existingOwnerCount === 0) {
            return true;
        }

        $this->warn("This environment already has {$existingOwnerCount} account(s) holding the Owner role.");

        // Defaults to "no" — some environments legitimately want more
        // than one owner, so this isn't a hard block, but it must be an
        // explicit choice, not something a second accidental run of this
        // command slips past.
        return $this->confirm('Are you sure you want to add another owner account?', false);
    }

    /**
     * @return array{username: string, name: string, employee_id: string, email: string, phone: string, password: string}|null
     */
    private function promptForOwnerDetails(): ?array
    {
        $username = $this->ask('Username');
        $name = $this->ask('Full Name');

        $employeeId = $this->ask('Employee ID (leave blank to auto-generate)');
        if (empty($employeeId)) {
            $employeeId = $this->employeeIdGenerator->next('employee');
            $this->line("Auto-generated Employee ID: {$employeeId}");
        }

        $email = $this->ask('Email');
        $phone = $this->ask('Phone (with country code)');

        $identityValidator = Validator::make(
            [
                'username' => $username,
                'name' => $name,
                'employee_id' => $employeeId,
                'email' => $email,
                'phone' => $phone,
            ],
            [
                'username' => ['required', 'string', 'max:255', 'unique:users,username'],
                'name' => ['required', 'string', 'max:255'],
                'employee_id' => ['required', 'string', 'max:255', 'unique:users,employee_id'],
                'email' => ['required', 'email:rfc,filter', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'string', 'max:30', new ValidPhoneNumber],
            ],
            [
                'email.email' => 'Please enter a valid email address.',
                'email.regex' => 'Please enter a valid email address.',
            ],
            ['employee_id' => 'Employee ID'],
        );

        if ($identityValidator->fails()) {
            $this->printErrors($identityValidator);

            return null;
        }

        $password = $this->promptForPassword();

        return [
            'username' => $username,
            'name' => $name,
            'employee_id' => $employeeId,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ];
    }

    /**
     * Re-prompts on a weak password instead of failing the whole command
     * — the identity fields above are validated in one pass since a typo
     * there is rare and worth stopping to double-check, but a first
     * password attempt not meeting the complexity bar is common enough
     * to just ask again immediately.
     */
    private function promptForPassword(): string
    {
        while (true) {
            $password = $this->secret('Password (hidden input)');

            $validator = Validator::make(
                ['password' => $password],
                ['password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()]],
            );

            if ($validator->passes()) {
                return $password;
            }

            $this->error('Password does not meet the requirements:');
            foreach ($validator->errors()->get('password') as $message) {
                $this->line("  - {$message}");
            }
        }
    }

    private function printErrors(ValidatorContract $validator): void
    {
        $this->error('Could not create the owner account:');
        foreach ($validator->errors()->all() as $message) {
            $this->line("  - {$message}");
        }
    }

    /**
     * @param  array{username: string, name: string, employee_id: string, email: string, phone: string, password: string}  $details
     */
    private function createOwner(array $details): void
    {
        $user = User::create([
            'username' => $details['username'],
            'name' => $details['name'],
            'employee_id' => $details['employee_id'],
            'email' => $details['email'],
            'phone' => $details['phone'],
            'password' => $details['password'],
            'is_active' => true,
        ]);

        // Global roles, via user_roles — not org_members — same as
        // DatabaseSeeder's UserSeeder sets up its "founder" account.
        $user->roles()->sync(Role::whereIn('slug', Role::GLOBAL_SLUGS)->pluck('id'));

        $this->newLine();
        $this->info('Owner account created.');
        $this->line("  Username: {$user->username}");
        $this->line("  Email:    {$user->email}");
        $this->newLine();
        $this->warn('The password you just entered was typed into this terminal session — treat it as sensitive. Clear your terminal scrollback/history now if this session is shared, logged, or recorded.');
    }

    // -- --reset-owner ------------------------------------------------

    /**
     * Break-glass recovery: reset an existing Owner/Super Admin's
     * password with no dependency on a working web session — this has
     * to be reachable purely via SSH + artisan even if the application
     * itself is throwing errors, so it never touches HTTP/session state.
     */
    private function handleResetOwner(): int
    {
        $this->info('Solva owner recovery — reset an existing Owner/Super Admin\'s password');
        $this->line('This resets the password on an EXISTING account. It cannot create a new owner.');
        $this->newLine();

        $candidates = User::whereHas('roles', fn ($query) => $query->whereIn('slug', Role::GLOBAL_SLUGS))
            ->with('roles')
            ->orderBy('username')
            ->get();

        if ($candidates->isEmpty()) {
            $this->error('No account currently holds the Owner or Super Admin role — there is nothing to reset.');
            $this->line('Run "php artisan solva:bootstrap" without --reset-owner to create the first owner instead.');

            return self::FAILURE;
        }

        $this->line('Accounts currently holding Owner or Super Admin:');
        foreach ($candidates as $index => $candidate) {
            $roleNames = $candidate->roles->whereIn('slug', Role::GLOBAL_SLUGS)->pluck('name')->implode(', ');
            $this->line(sprintf('  [%d] %s — %s <%s> (%s)', $index + 1, $candidate->username, $candidate->name, $candidate->email, $roleNames));
        }
        $this->newLine();

        $target = $candidates[$this->askForAccountNumber($candidates) - 1];
        $password = $this->promptForConfirmedPassword();

        $this->newLine();
        $this->line('About to reset the password for:');
        $this->line("  Username: {$target->username}");
        $this->line("  Email:    {$target->email}");
        $this->newLine();
        $this->warn('This is a break-glass recovery action and will be recorded in the Audit Trail.');

        if ($this->ask('Type RESET to confirm') !== 'RESET') {
            $this->warn('Aborted — password was not changed.');

            return self::SUCCESS;
        }

        $this->applyPasswordReset($target, $password);

        $this->newLine();
        $this->info('Password reset.');
        $this->line("  Username: {$target->username}");
        $this->line("  Email:    {$target->email}");
        $this->line('  This account must set a new password on next login.');
        $this->newLine();
        $this->warn('The password you just entered was typed into this terminal session — treat it as sensitive. Clear your terminal scrollback/history now if this session is shared, logged, or recorded.');

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, User>  $candidates
     */
    private function askForAccountNumber(Collection $candidates): int
    {
        while (true) {
            $answer = $this->ask('Which account do you want to reset? Enter the number.');

            if (ctype_digit((string) $answer) && (int) $answer >= 1 && (int) $answer <= $candidates->count()) {
                return (int) $answer;
            }

            $this->error("Please enter a number between 1 and {$candidates->count()}.");
        }
    }

    /**
     * Same complexity rules as UpdateUserPasswordRequest (the in-app
     * Change Password popup), including the 'confirmed' rule — Laravel
     * matches a `password` field against `password_confirmation`
     * automatically, so the two secret() prompts below are named to
     * match that convention exactly.
     */
    private function promptForConfirmedPassword(): string
    {
        while (true) {
            $password = $this->secret('New password (hidden input)');
            $confirmation = $this->secret('Confirm new password (hidden input)');

            $validator = Validator::make(
                ['password' => $password, 'password_confirmation' => $confirmation],
                ['password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()]],
            );

            if ($validator->passes()) {
                return $password;
            }

            $this->error('Rejected — either the password does not meet the requirements, or the two entries did not match:');
            foreach ($validator->errors()->get('password') as $message) {
                $this->line("  - {$message}");
            }
        }
    }

    /**
     * There's no authenticated web session to attribute this to (the
     * whole point of this flag is recovering access when normal login is
     * broken), so organization_id/user_id are left null rather than
     * forcing an artificial value — the Audit Trail view already
     * null-guards both. `source`/`triggered_from` in `changes` is what
     * actually identifies this as a CLI recovery action, distinct from a
     * normal in-app password change.
     */
    private function applyPasswordReset(User $target, string $password): void
    {
        DB::transaction(function () use ($target, $password) {
            $target->update([
                'password' => $password,
                'must_change_password' => true,
            ]);

            AuditLog::create([
                'organization_id' => null,
                'user_id' => null,
                'action' => 'user.password_reset_via_cli',
                'entity_type' => 'user',
                'entity_id' => $target->id,
                'changes' => [
                    'source' => 'cli_recovery',
                    'triggered_from' => 'solva:bootstrap --reset-owner',
                ],
            ]);
        });
    }
}
