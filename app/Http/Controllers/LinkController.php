<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Http\Requests\StoreLinkRequest;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function store(StoreLinkRequest $request)
    {
        $link = Link::create([
            'original_url' => $request->input('original_url'),
            'short_code' => Str::random(5),
        ]);

        return response()->json([
            'message' => 'Link criado com sucesso!',
            'link' => $link,
        ], 201);
    }

    public function index()
    {
        $links = Link::all();

        return response()->json([
            'links' => $links,
        ]);
    }

    public function destroy(Link $link)
    {
        $link->delete();

        return response()->json([
            'message' => 'Link deletado com sucesso!',
        ]);
    }

    public function redirect(string $code)
    {
        $link = Link::where('short_code', $code)->first();

        $original_url = $link->original_url;

        $link->increment('visits');

        return redirect()->away($original_url);
    }
}
