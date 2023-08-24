<?php declare(strict_types=1);

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('gets all non-admin users', function () {
    $token = getAdminToken();

    $this->withHeaders([
        'Authorization' => "Bearer $token"
    ])->get('api/v1/admin/user-listing')->assertStatus(Response::HTTP_OK);
});

it('can edit a non-admin user account', function () {
    $token = getAdminToken();
    $user = User::factory()->createOne();

    $updatedUser = [
      ...$user->toArray(),
      'first_name' => 'Matthew',
      'last_name' => 'Stevenson',
        'password' => 'test',
        'password_confirmation' => 'test',
        'marketing' => 'no'
    ];

    $this->withHeaders([
        'Authorization' => "Bearer $token"
    ])->put("api/v1/admin/user-edit/$user->uuid", $updatedUser)->assertStatus(Response::HTTP_OK);
});

it('can delete a non-admin user account', function () {
    $token = getAdminToken();
    $user = User::factory()->createOne();

    $this->withHeaders([
        'Authorization' => "Bearer $token"
    ])->delete("api/v1/admin/user-delete/$user->uuid")->assertStatus(Response::HTTP_OK);
    $this->assertModelMissing($user);
});
