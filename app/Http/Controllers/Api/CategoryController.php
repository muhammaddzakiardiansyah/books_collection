<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class CategoryController extends Controller
{
    public function findAllCategories(Request $request): JsonResponse
    {
        if($request->query()) {
            $categories = Category::where('category_name', 'like', "%{$request->query('q')}%")->orderBy('created_at', $request->query('order-by') ?? 'asc')->paginate(10);
        } else {
            $categories = Category::paginate(10);
        }

        return response()->json([
            'success' => true,
            'message' => 'Find all categories successfully',
            'statusCode' => 200,
            'data' => $categories,
        ], 200);
    }

    public function findDetailCategory(string $id): JsonResponse
    {
        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Find detail category successfully',
            'stautsCode' => 200,
            'data' => $category,
        ]);
    }

    public function addCategory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_name' => ['required', 'string', 'min:3', 'max:100', 'unique:categories,category_name'],
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
            $category = Category::create([
                'category_name' => $request->category_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Add category successfully',
                'statusCode' => 201,
                'data' => $category,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error ocurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }

    public function editCategory(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_name' => ['required', 'string', 'min:3', 'max:100', 'unique:categories,category_name'],
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

        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        try {
            $category->update([
                'category_name' => $request->category_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Edit category successfully',
                'statusCode' => 200,
                'data' => $category,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error ocurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }

    public function deleteCategory(string $id): JsonResponse
    {
        if(auth()->guard('api')->user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'forbidden',
                'statusCode' => 404,
            ], 404);
        }

        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        try {
            $category->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Delete category successfully',
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
