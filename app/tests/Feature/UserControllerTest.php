<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

it('gets all non-admin users', function () {
    $token = getAdminToken();

    $this->withHeaders([
        'Authorization' => "Bearer $token"
    ])->get('api/v1/admin/user-listing')->assertStatus(Response::HTTP_OK);
});
