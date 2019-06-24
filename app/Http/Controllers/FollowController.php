<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Serie;
use Auth;

class FollowController extends Controller
{
    /**
     * Display all the series a user is following.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        return Auth::user()->followings(Serie::class)->with([
            'genre1',
            'genre2'
        ])
        ->public()
        ->get();
    }

    /**
     * Display all the followers of a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function serieIndex($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        return $serie->followers()->get();
    }

    /**
     * Makes the logged-in follower of a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function follow($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        Auth::user()->follow($serie);

        return response()->json(null, 200);
    }

    /**
     * Removes the follower relation between the logged-in user and a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function unfollow($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        Auth::user()->unfollow($serie);

        return response()->json(null, 200);
    }
}
