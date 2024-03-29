<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Genre;

use App\Http\Controllers\Controller;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Genre::all();
    }
}
