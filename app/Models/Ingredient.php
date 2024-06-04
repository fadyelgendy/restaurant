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
        'status'
    ];

    public function lowStockReached(): bool
    {
        return (($this->consumed * 100) / $this->initial) >= 50;
    }

    public function isAvailable(): bool
    {
        return $this->status === \App\Enums\Status::AVAILABLE->value;
    }
}
