<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/signup",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *        response=201,
     *        description="User registered successfully",
     *        @OA\JsonContent(
     *           @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *           @OA\Property(property="access_token", type="string", description="Bearer token for authentication"),
     *        ),
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     * )
     */

    public function signup(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($request->password);
        $user = User::create($validated);
        if ($user) {
            return response()->json([
                'user' => new UserResource($user),
                'access_token' => $user->createToken($validated['email'])->plainTextToken
            ], 201);
        }

        return response()->json(null, 422);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Authenticate user and generate JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Login successful",
     *        @OA\JsonContent(
     *           @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *           @OA\Property(property="access_token", type="string", description="Bearer token for authentication"),
     *        ),
     *        @OA\Header(
     *           header="Authorization",
     *           description="Bearer {access_token}",
     *           @OA\Schema(type="string"),
     *        ),
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials"),
     * )
     */

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken($validated['email'])->plainTextToken
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout user",
     *     @OA\Response(response="200", description="Successfully logged out"),
     * )
     */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }
}
