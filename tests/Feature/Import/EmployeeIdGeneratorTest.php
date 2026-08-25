<?php

use App\Models\User;
use App\Services\Import\EmployeeIdGenerator;

test('generates sequential EMP- ids based on the highest existing number', function () {
    User::factory()->create(['employee_id' => 'EMP-0003']);
    User::factory()->create(['employee_id' => 'EMP-0001']);

    $generator = new EmployeeIdGenerator;

    expect($generator->next('employee'))->toBe('EMP-0004');
});

test('generates sequential CLIENT- ids independently of EMP- numbering', function () {
    User::factory()->create(['employee_id' => 'EMP-0099']);
    User::factory()->create(['employee_id' => 'CLIENT-0002']);

    $generator = new EmployeeIdGenerator;

    expect($generator->next('client'))->toBe('CLIENT-0003');
    expect($generator->next('employee'))->toBe('EMP-0100');
});

test('starts at 0001 when no employee_id with that prefix exists yet', function () {
    $generator = new EmployeeIdGenerator;

    expect($generator->next('employee'))->toBe('EMP-0001');
});

test('honors already-claimed numbers from the same in-progress pass', function () {
    User::factory()->create(['employee_id' => 'EMP-0001']);

    $generator = new EmployeeIdGenerator;

    expect($generator->next('employee', ['EMP' => 5]))->toBe('EMP-0006');
});
