<?php

namespace App\Http\Controllers;

use App\Models\Bookcase;
use App\Models\Box;

class PlaceController extends Controller
{
    public function getPlaces()
    {
        $bookcases = Bookcase::with('shelves.books')->get()
            ->map(function ($item) {
                $item['type'] = 'bookcase';
                $shelves = collect($item['shelves']);
                $item['shelves_count'] = $shelves->count();
                $item['books_count'] = $shelves->pluck('books')->count();

                return collect($item)->except(['shelves']);
            });

        $boxes = Box::with('books')->get()
            ->map(function ($item) {
            $item['type'] = 'box';
            $item['books_count'] = collect($item['books'])->count();
            return collect($item)->except('books');
        });
        return $boxes->toBase()->merge($bookcases)->sortByDesc('updated_at')->values();
    }
}
