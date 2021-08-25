<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $hidden = ['place_type_id'];
    protected $fillable = ['title', 'place_id', 'place_type_id'];

    public function authors() {
        return $this->belongsToMany(Author::class)->withTimestamps();
    }

    public function topics() {
        return $this->belongsToMany(Topic::class)->withTimestamps();
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

    public function scopePlace($query)
    {
        return $query
            ->when($this->placeType(), function ($q) {
                return $q->with('shelf')->first()->shelf;
            }, function ($q) {
                return $q->with('box')->first()->shelf;
            });
    }
}
