<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'category_name',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => empty($model->id) ? $model->id = rand(10000, 100000) : '');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'category_id', 'id');
    }
}
