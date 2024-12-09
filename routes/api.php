<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function() {
    // authentications route
    Route::prefix('auth')->group(function() {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    });
    // books route
    Route::prefix('books')->middleware('auth:api')->group(function() {
        Route::get('/', [BookController::class, 'findAllBooks'])->name('findAllBooks');
        Route::get('/{id}', [BookController::class, 'findDetailBook'])->name('findDetailBook');
        Route::post('/', [BookController::class, 'addBook'])->name('addBook');
        Route::post('/{id}', [BookController::class, 'editBook'])->name('editBook');
        Route::delete('/{id}', [BookController::class, 'deleteBook'])->name('deleteBook');
    });
    // categories route
    Route::prefix('categories')->middleware('auth:api')->group(function() {
        Route::get('/', [CategoryController::class, 'findAllCategories'])->name('findAllCategories');
        Route::get('/{id}', [CategoryController::class, 'findDetailCategory'])->name('findDetailCategory');
        Route::post('/', [CategoryController::class, 'addCategory'])->name('addCategory');
        Route::post('/{id}', [CategoryController::class, 'editCategory'])->name('editCategory');
        Route::delete('/{id}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');
    });
    // users route
    Route::prefix('users')->middleware('auth:api')->group(function() {
        Route::get('/', [UserController::class, 'findAllUsers'])->name('findAllUsers');
        Route::get('/{id}', [UserController::class, 'findDetailUser'])->name('findDetailUser');
        Route::post('/', [UserController::class, 'addUser'])->name('addUser');
        Route::post('/{id}', [UserController::class, 'editUser'])->name('editUser');
        Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('deleteUser');
    });
});
