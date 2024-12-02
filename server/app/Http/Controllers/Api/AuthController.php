<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // User registration
    public function register(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:8',
                'phone' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'surname' => $request->surname,
                'phone' => $request->phone,
            ]);


            return response()->json([
                'user' => $user,
                'status' => 'success',
                 'message' => 'User created success'
            ]);
        }catch (ValidationException $e){
            return response()->json([
               'status' => 'error',
               'message' => $e
            ]);
        }

    }

    // User login

    /**
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'device_name' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken($request->device_name)->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        try {
            // Check if the user is authenticated
            $user = Auth::user();

            if ($user) {
                // Revoke the token for the current user
                $user->tokens->each(function ($token) {
                    $token->delete();
                });

                return response()->json([
                    'status' => 'success',
                    'message' => 'User logged out successfully',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'User is not authenticated',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while logging out',
            ], 500);
        }
    }
}
