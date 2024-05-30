<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Process user login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|exists:users',
            'password' => 'required|min:6|string',
        ], [
            'email.required'    => 'The email is required.',
            'email.email'       => 'Please enter a valid email address.',
            'email.exists'      => 'The specified email does not exist in our records.',
        ]);

        // Extract email and password from the request
        $credential = $request->only('email', 'password');

        // Extract email and password from the request
        $user = User::where('email', $credential['email'])->first();

        // Check if the user exists and the provided password matches
        if ($user && Hash::check($credential['password'], $user->password)) {

            // Attempt authentication using the credentials
            if (Auth::attempt($credential)) {

                // Create a token for the authenticated user
                $token = $user->createToken('AuthToken')->plainTextToken;

                // Return success response with user and token information
                return ok(__('strings.success', ['name' => 'User login']), [
                    'user'  => $user,
                    'token' => $token
                ]);
            }
        }

        // Return error response for invalid credentials or user not found
        return error(__('strings.invalid_credentials'), [], 'loginCase');
    }


    /**
     * Handle user registration.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing user data.
     * @return \Illuminate\Http\Response Response indicating the result of the registration process.
     */
    public function registration(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password ? Hash::make($request->password) : Hash::make(config('constant.TEMP_PASSWORD')),
        ]);

        // Return success response after account creation
        return ok(__('strings.success', ['name' => 'Account create']), [
            'user'  => $user
        ]);
    }


    /**
     * Logs out the authenticated user by revoking all tokens associated with their session.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return ok(__('strings.success', ['name' => 'Logout']));
    }
}
