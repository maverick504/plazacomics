<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filter = 'all';

        if(array_key_exists('filter', $_GET)) {
            $filter = $_GET['filter'];
        }

        switch($filter) {
          case 'unread':
            return Auth::user()->unreadNotifications()->get();
            break;
          case 'likes':
            return Auth::user()->notifications()->where('type', 'App\Notifications\UserLikedSeries')->get();
            break;
          case 'subscriptions':
            return Auth::user()->notifications()->where('type', 'App\Notifications\UserSuscribedToSeries')->get();
            break;
          case 'comments':
            return Auth::user()->notifications()->where('type', 'App\Notifications\NewComment')->get();
            break;
          default:
            return Auth::user()->notifications()->get();
        };
    }

    /**
     * Display the specified notification.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Auth::user()->notifications()->where('id', $id)->get();
    }

    /**
     * Marks a notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markMultipleAsRead()
    {
        $filter = 'all';

        if(array_key_exists('filter', $_GET)) {
            $filter = $_GET['filter'];
        }

        switch($filter) {
          case 'unread':
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
            break;
          case 'likes':
            Auth::user()->notifications()->where('type', 'App\Notifications\UserLikedSeries')->update(['read_at' => now()]);
            break;
          case 'subscriptions':
            Auth::user()->notifications()->where('type', 'App\Notifications\UserSuscribedToSeries')->update(['read_at' => now()]);
            break;
          case 'comments':
            Auth::user()->notifications()->where('type', 'App\Notifications\UserCommentedChapter')->update(['read_at' => now()]);
            break;
          default:
            Auth::user()->notifications()->update(['read_at' => now()]);
        };

        return response()->json(null, 200);
    }

    /**
     * Marks a notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if(!$notification) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        $notification->update(['read_at' => now()]);

        return response()->json($notification, 200);
    }

    /**
     * Unmarks a notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unmarkAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if(!$notification) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        $notification->update(['read_at' => null]);

        return response()->json($notification, 200);
    }

    /**
     * Remove the notification from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if(!$notification) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        $notification->delete();

        return response()->json(null, 204);
    }
}
