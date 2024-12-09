<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function findAllBooks(Request $request): JsonResponse
    {
        if ($request->query()) {
            $books = Book::with(['user:id,name,email', 'category:id,category_name'])->where('book_name', 'like', "%{$request->query('q')}%")
                ->orWhere('description', 'like', "%{$request->query('q')}%")
                ->orWhereHas('category', function ($query) use ($request) {
                    $query->where('category_name', 'like', "%{$request->query('q')}%");
                })
                ->orWhereHas('user', function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->query('q')}%");
                })
                ->orderBy('created_at', $request->query('order-by') ?? 'asc')->paginate(10);
        } else {
            $books = Book::with(['user:id,name,email', 'category:id,category_name'])->paginate(10);
        }

        return response()->json([
            'success' => true,
            'message' => 'Find all books successfully',
            'statucCode' => 200,
            'data' => $books,
        ], 200);
    }

    public function findDetailBook(string $id): JsonResponse
    {
        $book = Book::with(['user:id,name,email', 'category:id,category_name'])->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Find detail book successfully',
            'statusCode' => 200,
            'data' => $book,
        ], 200);
    }

    public function addBook(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'book_name' => ['required', 'string', 'min:3', 'unique:books,book_name'],
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
            'book_file' => ['required', 'file', 'mimes:pdf,docx,txt,pptx', 'max:2048'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // store file
            $image = $request->file('image');
            $bookFile = $request->file('book_file');
            $image->storeAs('book_images', $image->hashName());
            $bookFile->storeAs('book_files', $bookFile->hashName());

            $book = Book::create([
                'book_name' => $request->book_name,
                'image' => $image->hashName(),
                'book_file' => $bookFile->hashName(),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'user_id' => auth()->guard('api')->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Add book successfully',
                'statusCode' => 201,
                'data' => $book,
            ], 201);
        } catch (Exception $e) {
            // delete file if request field
            $image = $request->file('image');
            $bookFile = $request->file('book_file');
            Storage::delete('book_images/' . $image->hashName());
            Storage::delete('book_files/' . $bookFile->hashName());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }

    public function editBook(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'book_name' => ['required', 'string', 'min:3', 'unique:books,book_name'],
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
            'book_file' => ['required', 'file', 'mimes:pdf,docx,txt,pptx', 'max:2048'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $getBook = Book::find($id);

        if (!$getBook) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        // check if the book does not belong to the user and the user is not an admin
        if (auth()->guard('api')->user()->id !== $getBook->user_id && auth()->guard('api')->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'statusCode' => 403,
            ], 403);
        }

        try {
            $image = $request->file('image');
            $bookFile = $request->file('book_file');

            // check if there are any new file
            if ($image) {
                $getImage = explode('/', $getBook->image);
                Storage::delete('book_images/' . end($getImage));
                $image->storeAs('book_images', $image->hashName());
            }
            if ($bookFile) {
                $getBookFile = explode('/', $getBook->book_file);
                Storage::delete('book_files/' . end($getBookFile));
                $bookFile->storeAs('book_files', $bookFile->hashName());
            }

            $getBook->update([
                'book_name' => $request->book_name,
                'image' => $image->hashName(),
                'book_file' => $bookFile->hashName(),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'user_id' => auth()->guard('api')->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Edit book successfully',
                'statusCode' => 200,
                'data' => $getBook,
            ], 200);
        } catch (Exception $e) {
            // delete file if request field
            $image = $request->file('image');
            $bookFile = $request->file('book_file');
            Storage::delete('book_images/' . $image->hashName());
            Storage::delete('book_files/' . $bookFile->hashName());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred ' . $e->getMessage(),
                'statusCode' => 409,
            ], 409);
        }
    }

    public function deleteBook(string $id): JsonResponse
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found:(',
                'statusCode' => 404,
                'data' => [],
            ], 404);
        }

        // check if the book does not belong to the user and the user is not an admin
        if (auth()->guard('api')->user()->id !== $book->user_id && auth()->guard('api')->user()->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'forbidden',
                'statusCode' => 403,
            ], 403);
        }

        // delete file in storage
        $getBookImage = explode('/', $book->image);
        $getBookFile = explode('/', $book->book_file);
        Storage::delete(['book_images/' . end($getBookImage), 'book_files/' . end($getBookFile)]);

        try {
            $book->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Delete book successfully',
                'statusCode' => 200,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred ' . $e->getMessage(),
            ]);
        }
    }
}
