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
                $item['shelvesCount'] = $shelves->count();
                $item['booksCount'] = $shelves->pluck('books')->count();

                return collect($item)->except(['shelves']);
            })
            ->except(['shelves']);

        $boxes = Box::with('books')->get()
            ->map(function ($item) {
            $item['type'] = 'box';
            $item['booksCount'] = collect($item['books'])->count();
            return collect($item)->except('books');
        });
        return $boxes->toBase()->merge($bookcases)->sortByDesc('updated_at')->values();
    }
}
