<?php declare(strict_types=1);

use App\Models\User;

it('can create an admin account', function () {
    $userFactory = User::factory()->makeOne();

    $params = [
        ...$userFactory->toArray(),
        'password' => 'test',
        'password_confirmation' => 'test'
    ];
    $this->post('/api/v1/admin/create', $params);
    $this->assertDatabaseCount('users', 1);
});
