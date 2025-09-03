<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Programme extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($programme) {
            if (empty($programme->id)) {
                $programme->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'id',
        'nom',
        'description',
        'starting',
        'ending',
        'when',
        'genre',
        'couverture',
    ];

    // protected $hidden = [
     
    // ];
}
