<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Anggelika Septia Ningrum',
            'email' => 'anggelika@gmail.com',
            'password' => 'Anggelika13#',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Azmya Nadine Ardiningrum',
            'email' => 'azmya@gmail.com',
            'password' => 'Azmya20#',
            'role' => 'user',
        ]);

        User::factory(10)->create();

        Category::factory(6)->create();

        Book::factory(20)->create();

    }
}
