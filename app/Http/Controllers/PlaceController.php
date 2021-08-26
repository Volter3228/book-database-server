<?php

namespace App\Http\Controllers;

use App\Models\Bookcase;
use App\Models\Box;

class PlaceController extends Controller
{
    public function index(): \Illuminate\Support\Collection
    {
        $bookcases = Bookcase::with('shelves.books')->get()
            ->map(function ($item) {
                $item['type'] = 'bookcase';
                $shelves = collect($item['shelves']);
                $item['shelves_count'] = $shelves->count();
                $item['books_count'] = $shelves->pluck('books')
                    ->filter(function ($value) {
                        return $value->count();
                    })->count();

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

    public function store()
    {
        $data = request()->validate([
            'info' => 'required|max:999',
            'type' => 'required',
            'shelvesCount' => 'required_if:type,==,bookcase|min:1',
        ]);

        $type = $data['type'];
        $shelvesCount = $data['shelvesCount'];
        $info = $data['info'];

        if (!in_array($type, ['bookcase', 'box'])) {
            return response()->json(['message' => 'Wrong Type'], 400);
        }

        if ($type === 'bookcase' && (!$shelvesCount || $shelvesCount < 1)) {
            return response()->json(['message' => 'Wrong shelves count'], 400);
        }

        if ($type === 'bookcase') {
            $bookcase = Bookcase::create(['info' => $info]);

            foreach (range(1, $shelvesCount) as $number) {
                $bookcase->shelves()->create(['number' => $number]);
            }

            $bookcase['books_count'] = 0;
            $bookcase['shelves_count'] = $shelvesCount;
            $bookcase['type'] = 'bookcase';

            return $bookcase;
        }

        $box = Box::create(['info' => $info]);
        $box['books_count'] = 0;
        $box['type'] = 'box';

        return $box;
    }
}
