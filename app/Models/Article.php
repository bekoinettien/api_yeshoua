<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
      use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($article) {
            if (empty($article->id)) {
                $article->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'id',
        'title',
        'author',
        'category',
        'status',
        'contenu',
        'feature_image',
    ];
}
 