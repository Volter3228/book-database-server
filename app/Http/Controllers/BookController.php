<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['authors', 'topics', 'placeType'])->get()->map(function ($item) {
            if ($item->placeType['name'] === 'shelf') {
                Log::info("JOPA");
                $item['shelf_number'] = $item->place()->number;
            }
            return collect($item)->except(['place_type']);
        });
        return $books->values();
    }
}
