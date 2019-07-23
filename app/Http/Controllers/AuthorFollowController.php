<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
// use App\Notifications\UserSuscribedToSeries;
// use Illuminate\Support\Facades\Notification;

class AuthorFollowController extends Controller
{
    /**
     * Display all the authors a user is following.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        return auth()->user()->following(User::class)
        ->paginate(RESULTS_PER_PAGE);
    }

    /**
     * Display all the followers of a author.
     *
     * @return \Illuminate\Http\Response
     */
    public function authorIndex($userId)
    {
        $author = User::find($userId);

        if(!$author) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        return $author->followers()->get();
    }

    /**
     * Makes the logged-in user follower of a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function follow($userId)
    {
        $author = User::find($userId);

        if(!$author) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        $userWasFollower = DB::table('followables')
        ->where('followable_type', '=', 'App\User')
        ->where('relation', '=', 'follow')
        ->where('user_id', '=', auth()->user()->id)
        ->where('followable_id', '=', $author->id)
        ->count()>0;

        DB::beginTransaction();
        try {
            if(!$userWasFollower) {
                // Send notification.
                /*
                $authors = $serie->authors()->get();
                Notification::send($authors, new UserSuscribedToSeries(auth()->user(), $serie));
                */
            }

            auth()->user()->follow($author);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        return response()->json(null, 200);
    }

    /**
     * Removes the follower relation between the logged-in user and a author.
     *
     * @return \Illuminate\Http\Response
     */
    public function unfollow($userId)
    {
        $author = User::find($userId);

        if(!$author) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        auth()->user()->unfollow($author);

        return response()->json(null, 200);
    }
}
