<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller {
    public function index() {
        $albums = Album::all();
        return view('albums.index', compact('albums'));
    }

    public function create() {
        return view('albums.create');
    }

    public function store(Request $request) {
        Album::create($request->all());
        return redirect()->route('albums.index');
    }
}

