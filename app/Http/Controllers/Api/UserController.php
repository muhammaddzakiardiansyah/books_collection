<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function findAllUsers(Request $request): JsonResponse
    {
        if(auth()->guard('api')->user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'forbidden',
                'statusCode' => 404,
            ], 404);
        }

        if($request->query()) {
            $users = User::where('name', 'like', "%{$request->query('q')}%")
            ->orWhere('email', 'like', "%{$request->query('q')}%")
            ->orWhere('role', 'like', "%{$request->query('q')}%")
            ->orderBy('created_at', $request->query('order-by') ?? 'asc')->paginate(10);
        } else {
            $users = User::paginate(10);
        }

        return response()->json([
            'success' => true,
            'message' => 'Find all users successfully',
            'statusCode' => 200,
            'data' => $users,
        ], 200);
    }

    public function findDetailUser(string $id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Find detail user successfully',
            'stautsCode' => 200,
            'data' => $user,
        ]);
    }

    public function addUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/'],
            'role' => ['required', 'string', 'in:admin,user'],
        ], [
            'password.regex' => 'The password should have a-z, A-Z, 0-9 and special caracters',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(auth()->guard('api')->user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'forbidden',
                'statusCode' => 404,
            ], 404);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Add user successfully',
                'statusCode' => 201,
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error ocurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }

    public function editUser(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/'],
            'role' => ['required', 'string', 'in:admin,user'],
        ], [
            'password.regex' => 'The password should have a-z, A-Z, 0-9 and special caracters',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(auth()->guard('api')->user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'forbidden',
                'statusCode' => 404,
            ], 404);
        }

        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Edit user successfully',
                'statusCode' => 200,
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error ocurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }

    public function deleteUser(string $id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

         // check if the book does not belong to the user and the user is not an admin
         if (auth()->guard('api')->user()->id !== $user->id && auth()->guard('api')->user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'forbidden',
                'statusCode' => 403,
            ], 403);
        }

        try {
            $user->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Delete user successfully',
                'statusCode' => 200,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error ocurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }
}
