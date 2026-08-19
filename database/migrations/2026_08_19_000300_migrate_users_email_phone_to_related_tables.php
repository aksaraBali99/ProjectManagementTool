<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->select('id', 'email', 'phone')->orderBy('id')->each(function ($user) {
            DB::table('user_emails')->insert([
                'user_id' => $user->id,
                'email' => $user->email,
                'label' => 'Email',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($user->phone) {
                DB::table('user_phones')->insert([
                    'user_id' => $user->id,
                    'phone' => $user->phone,
                    'label' => 'Phone number',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn(['email', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->after('employee_id');
            $table->string('phone')->nullable()->after('email');
        });

        DB::table('user_emails')->orderBy('id')->get()->groupBy('user_id')->each(function ($rows, $userId) {
            DB::table('users')->where('id', $userId)->update(['email' => $rows->first()->email]);
        });

        DB::table('user_phones')->orderBy('id')->get()->groupBy('user_id')->each(function ($rows, $userId) {
            DB::table('users')->where('id', $userId)->update(['phone' => $rows->first()->phone]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
