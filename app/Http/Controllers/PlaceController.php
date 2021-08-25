<?php

namespace App\Http\Controllers;

use App\Models\Bookcase;
use App\Models\Box;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        Log::info($bookcases);

        $boxes = Box::get()->map(function ($item) {
            $item['type'] = 'box';
            return $item;
        });
        return $boxes->toBase()->merge($bookcases)->sortByDesc('updated_at')->values();
    }
}
