<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'book_name',
        'user_id',
        'image',
        'book_file',
        'description',
        'category_id',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => empty($model->id) ? $model->id = rand(10000, 100000) : '');
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($image) => url('/storage/book_images/' . $image),
        );
    }

    public function bookFile(): Attribute
    {
        return Attribute::make(
            get: fn($bookFile) => url('/storage/book_files/' . $bookFile),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category(): HasMany
    {
        return $this->hasMany(Category::class, 'id', 'category_id');
    }
}
