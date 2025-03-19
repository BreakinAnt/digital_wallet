<?php
namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(UserLoginRequest $request, UserService $userServ)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            try {
                $user = $userServ->validateUser($credentials['email'], $credentials['password']);
            } catch (UserException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 401);
            } catch (Exception $e) {
                report($e);
                
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred.',
                ], 500);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * Handle user registration.
     */
    public function register(UserRegisterRequest $request, UserService $userServ)
    {
        $data = $request->only('name', 'email', 'password');

        $user = $userServ->registerUser($data['name'], $data['email'], $data['password']);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user details.
     */
    public function user()
    {
        return response()->json([
            'success' => true,
            'user' => Auth::user('api'),
        ]);
    }
}