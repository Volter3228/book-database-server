<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function authors() {
        return $this->belongsToMany(Author::class);
    }

    public function topics() {
        return $this->belongsToMany(Topic::class);
    }

    public function placeType() {
        return $this->belongsTo(PlaceType::class);
    }

    public function shelf() {
        return $this->hasOne(Shelf::class, 'id', 'place_id');
    }

    public function box() {
        return $this->hasOne(Box::class, 'id', 'place_id');
    }

    public function scopeProfile($query)
    {
        return $query
            ->when($this->placeType->name === 'shelf', function ($q) {
                return $q->with('shelf');
            }, function ($q) {
                return $q->with('box');
            });
    }
}
