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
            return Auth::user()->notifications()->where('type', 'App\Notifications\UserLikedSerie')->get();
            break;
          case 'subscriptions':
            return Auth::user()->notifications()->where('type', 'App\Notifications\UserSuscribedToSerie')->get();
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
