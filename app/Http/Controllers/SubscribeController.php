<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Serie;
use App\Notifications\UserSuscribedToSeries;
use Illuminate\Support\Facades\Notification;
use Auth;

class SubscribeController extends Controller
{
    /**
     * Display all the series a user is subscribed.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        return Auth::user()->subscriptions(Serie::class)->with([
            'genre1',
            'genre2'
        ])
        ->public()
        ->get();
    }

    /**
     * Display all the subscribers of a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function serieIndex($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        return $serie->subscribers()->get();
    }

    /**
     * Makes the logged-in subscriber of a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        $userWasSubscribed = DB::table('followables')
        ->where('followable_type', '=', 'App\Serie')
        ->where('relation', '=', 'subscribe')
        ->where('user_id', '=', Auth::user()->id)
        ->where('followable_id', '=', $serie->id)
        ->count()>0;

        DB::beginTransaction();
        try {
            if(!$userWasSubscribed) {
                // Send notification.
                $authors = $serie->authors()->get();
                Notification::send($authors, new UserSuscribedToSeries(Auth::user(), $serie));
            }

            Auth::user()->subscribe($serie);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        return response()->json(null, 200);
    }

    /**
     * Removes the subscriber relation between the logged-in user and a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        Auth::user()->unsubscribe($serie);

        return response()->json(null, 200);
    }
}
