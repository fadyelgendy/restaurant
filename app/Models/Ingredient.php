<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    protected $fillable = [
        'name',
        'initial',
        'stock',
        'consumed',
        'remaining',
        'status'
    ];
}
