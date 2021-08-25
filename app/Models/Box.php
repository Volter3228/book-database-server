<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = ['info'];

    public function books() {
        return $this->hasMany(Book::class, 'place_id', 'id');
    }
}
