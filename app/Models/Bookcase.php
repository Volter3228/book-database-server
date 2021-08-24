<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookcase extends Model
{
    use HasFactory;

    protected $fillable = ['info'];

    public function shelves() {
        return $this->hasMany(Shelf::class);
    }
}
