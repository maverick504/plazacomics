<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Licence;

use App\Http\Controllers\Controller;

class LicenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Licence::all();
    }
}
