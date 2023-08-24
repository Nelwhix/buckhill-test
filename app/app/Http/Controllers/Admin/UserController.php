<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: "/api/v1/admin/user-listing",
        summary: "List all non-admin users",
        security: [
            [
                'bearerAuth' => []
            ]
        ],
        tags: ["Admin"],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "users retrieved success"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "not found"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function index(): \Illuminate\Http\Response
    {
        $users = User::where('is_admin', 0)->get();
        return response([
           'status' => 'success',
           'message' => 'users retrieved successfully',
           'data' => [
               'total' => count($users),
               'users' => $users
           ]
        ]);
    }


    #[OA\Put(
        path: "/api/v1/admin/user-edit/{uuid}",
        summary: "Edit a non-admin User account",
        security: [
            [
                'bearerAuth' => []
            ]
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'first_name', description: "User first name", type: "string"),
                        new OA\Property(property: 'last_name', description: "User last name", type: "string"),
                        new OA\Property(property: 'address', description: "User address", type: "string"),
                        new OA\Property(property: 'phone_number', description: "User phone number", type: "string"),
                        new OA\Property(property: 'avatar', description: "User profile picture UUID", type: "string"),
                        new OA\Property(property: 'marketing', description: "marketing preferences", type: "string")
                    ]
                ))),
        tags: ["Admin"],
        parameters: [
            new OA\Parameter(name: "uuid", description: "user's uuid", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "Updated Successfully"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Not found"),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: "action not allowed"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function update(UpdateUserRequest $request, string $uuid): \Illuminate\Http\Response
    {
        $formFields = $request->validated();

        $user = User::where('uuid', $uuid)->firstOrFail();
        if ($user->is_admin === 1) {
            return response([
                'status' => 'failed',
                'message' => 'not allowed',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->first_name = $formFields['first_name'] ?? $user->first_name;
        $user->last_name = $formFields['last_name'] ?? $user->last_name;
        $user->address = $formFields['address'] ?? $user->address;
        $user->phone_number = $formFields['phone_number'] ?? $user->phone_number;
        $user->avatar = $formFields['avatar'] ?? $user->avatar;
        $user->is_marketing = $formFields['marketing'] === "yes" ? 1 : 0;
        $user->save();

        return response([
            'status' => 'success',
            'message' => 'user details updated successfully',
        ]);

    }

    #[OA\Delete(
        path: "/api/v1/admin/user-delete/{uuid}",
        summary: "Delete a non-admin User account",
        security: [
            [
                'bearerAuth' => []
            ]
        ],
        tags: ["Admin"],
        parameters: [
            new OA\Parameter(name: "uuid", description: "user's uuid", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "Deleted Successfully"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Not found"),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: "action not allowed"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function destroy(string $uuid): \Illuminate\Http\Response
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        if ($user->is_admin === 1) {
            return response([
                'status' => 'failed',
                'message' => 'not allowed',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->delete();

        return response([
            'status' => 'success',
            'message' => 'user details deleted successfully',
        ]);
    }
}
