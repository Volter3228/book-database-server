<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    use HasFactory;

    protected $fillable = ['number'];

    public function bookcase() {
        return $this->belongsTo(Bookcase::class);
    }

    public function books() {
        return $this->hasMany(Book::class, 'place_id', 'id');
    }
}
