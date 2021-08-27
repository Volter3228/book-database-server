<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Bookcase;
use App\Models\Box;
use App\Models\PlaceType;
use App\Models\Shelf;
use App\Models\Topic;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['authors', 'topics', 'placeType'])->get()->map(function ($item) {
            if ($item->placeType['name'] === 'shelf') {
                $item['placeType'] = 'bookcase';
                $item['place_id'] = $item->place()->bookcase_id;
                $item['shelf_number'] = $item->place()->number;
            } else {
                $item['placeType'] = 'box';
            }

            return collect($item)->except(['place_type']);
        })->sortByDesc('created_at');
        return $books->values();
    }

    public function store()
    {
        $data = request()->validate([
            'title' => 'required|max:255',
            'authors' => 'required|array|min:1',
            'authors.*' => 'required|string|min:1',
            'topics' => 'required|array|min:1',
            'topics.*' => 'required|string|min:1',
            'placeId' => 'required|integer',
            'placeType' => 'required',
            'shelfNumber' => 'required_if:placeType,==,bookcase|min:1',
            'description' => 'required',
        ]);

        [
            'title' => $title, 'authors' => $authors, 'topics' => $topics, 'placeId' => $placeId,
            'placeType' => $placeType, 'shelfNumber' => $shelfNumber, 'description' => $description
        ] = $data;

        if (!in_array($placeType, ['bookcase', 'box'])) {
            return response()->json(['message' => 'Wrong Type'], 400);
        }

        if ($placeType === 'bookcase') {
            $bookcase = Bookcase::findOrFail($placeId);
            if (!$shelfNumber || $shelfNumber < 1 || $bookcase->shelves()->count() < $shelfNumber) {
                return response()->json(['message' => 'Wrong shelves count'], 400);
            }
            $placeId = Shelf::where('bookcase_id', $placeId)->where('number', $shelfNumber)->first()->id;
            $placeType = 'shelf';
        } else {
          $box = Box::findOrFail($placeId);
        }

        $placeTypeId = PlaceType::where('name', $placeType)->first()->id;

        $book = Book::create([
            'title' => $title,
            'description' => $description,
            'place_id' => $placeId,
            'place_type_id' => $placeTypeId,
        ]);

        foreach($authors as $a) {
            $author = Author::firstOrCreate(['name' => $a]);
            $author->books()->attach($book->id);
        }

        foreach($topics as $t) {
            $topic = Topic::firstOrCreate(['name' => $t]);
            $topic->books()->attach($book->id);
        }

        $book = Book::with(['authors', 'topics', 'placeType'])
            ->where('id', $book->id)
            ->get()
            ->map(function ($item) use ($shelfNumber) {
                if ($item->placeType['name'] === 'shelf') {
                    $item['shelf_number'] = $shelfNumber;
                }
            return collect($item)->except(['place_type']);
        })->first();

        return $book;
    }
}
