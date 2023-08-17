<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use phpDocumentor\Reflection\DocBlock\Tags\Property;

class RegisteredUserController extends Controller
{
    #[OA\Post(
        path: "/api/v1/admin/create",
        summary: "Create an Admin Account",
        requestBody: new OA\RequestBody(required: true,
                content: new OA\MediaType(mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(required: ["first_name", "last_name", "email", "password", "password_confirmation", "address", "phone_number", "avatar"],
                        properties: [
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                            new OA\Property(property: 'first_name', description: "User first name", type: "string")
                    ]
                ))),
        tags: ["Admin"],
        responses: [
            new OA\Response(response: 201, description: "Register Successfully"),
            new OA\Response(response: 422, description: "Unprocessable entity"),
            new OA\Response(response: 400, description: "Bad Request"),
            new OA\Response(response: 500, description: "Server Error")
        ]
    )]
    public function store(RegisterRequest $request): Response
    {
        $formFields = $request->validated();

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'first_name' => $formFields['first_name'],
            'last_name' => $formFields['last_name'],
            'is_admin' => 1,
            'email' => $formFields['email'],
            'password' => Hash::make($formFields['password']),
            'address' => $formFields['address'],
            'phone_number' => $formFields['phone_number'],
            'avatar' => $formFields['avatar'],
            'is_marketing' => $request->has('marketing') ? 1 : 0
        ]);

        return response([
            'status' => 'success',
            'message' => 'admin account created successfully!',
            'data' => [
                ...$user->toArray(),
            ]
        ], \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
