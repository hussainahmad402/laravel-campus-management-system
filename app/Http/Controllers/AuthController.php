<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HasApiTokens, HasFactory, Notifiable;


    public function register(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'error' => $validator->errors()
                ], 200);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // Assign default role
            if (!Role::where('name', 'Student')->exists()) {
                Role::create(['name' => 'Student']);
            }
            $user->assignRole('Student');

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => new UserResource($user)
            ], 200);

        } catch (QueryException $qe) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'type' => 'required|string|in:student,faculty',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $user = User::where('email', $request->email)->first();

            if (!$user || !\Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid email or password'
                ], 401);
            }

            if ($request->type === 'student' && !$user->hasRole('Student')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only students can login here'
                ], 403);
            }

            if ($request->type === 'faculty' && $user->hasRole('Student')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only faculty can login here'
                ], 403);
            }
            $token = $user->createToken('auth_token')->plainTextToken;


            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function admincreateuser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|string|exists:roles,name',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);

            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->role);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => new UserResource($user)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
