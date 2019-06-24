<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Suggestion;

class SuggestionController extends Controller
{
    /**
     * Store a newly created suggestion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Store the suggestion.
        $suggestion = new Suggestion();
        $suggestion->body = $request->get('body');
        $suggestion->url = $request->get('url');
        $suggestion->save();
        
        return response()->json(null, 201);
    }
}
