<?php

namespace App\Http\Controllers;

use App\Data\LaravelData;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'topics' => LaravelData::topics(),
        ]);
    }
}
