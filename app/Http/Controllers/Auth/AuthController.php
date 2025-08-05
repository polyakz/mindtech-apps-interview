<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;
use App\Http\Controllers\AbstractController;

#[OA\Info(
    title: "Auth controller",
    version: "1.0.0",
)]
class AuthController extends AbstractController
{
    #[OA\Post(
        path: "/auth/register",
        tags: ['Auth'],
        summary: "Register a new user",
        description: "Creates a new user account",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                        new OA\Property(property: "email", type: "string", example: "john@example.com"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-08-05T10:00:00Z")
                    ]
                )
            ),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    #[OA\Post(
        path: "/auth/login",
        tags: ['Auth'],
        summary: "User login",
        description: "Authenticate user and return an access token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful login",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "user",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                            ]
                        ),
                        new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9")

                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Invalid login credentials")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            $request->session()->put('user_token', bin2hex(random_bytes(16)));

            return response()->json([
                'user' => $user,
                'token' => $request->session()->get('user_token')
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    #[OA\Get(
        path: "/auth/who-am-i",
        tags: ['Auth'],
        summary: "Get currently authenticated user",
        description: "Returns the session related user",
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Authenticated user returned successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                        new OA\Property(property: "email", type: "string", example: "john@example.com"),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No user is authenticated.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "No user is authenticated.")
                    ]
                )
            )
        ]
    )]
    public function whoAmI(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No user is authenticated.'], 401);
        }

        return response()->json($user);
    }
}
