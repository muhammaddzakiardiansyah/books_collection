<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::pluck('id')->random();
        $categoryId = Category::pluck('id')->random();

        return [
            'book_name' => fake()->sentence(),
            'user_id' => $userId,
            'image' => 'fake-image.jpg',
            'book_file' => 'fake-book-file.pdf',
            'description' => implode('\n', fake()->paragraphs()),
            'category_id' => $categoryId,
        ];
    }
}
