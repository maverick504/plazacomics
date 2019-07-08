<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Serie;
use App\Notifications\UserLikedSeries;
use Illuminate\Support\Facades\Notification;
use Auth;

class LikeController extends Controller
{
  /**
   * Creates a like relation between the logged-in user and a serie.
   *
   * @return \Illuminate\Http\Response
   */
  public function like($serieId)
  {
      $serie = Serie::find($serieId);

      if(!$serie) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      $userLikedPreviously = DB::table('followables')
      ->where('followable_type', '=', 'App\Serie')
      ->where('relation', '=', 'like')
      ->where('user_id', '=', Auth::user()->id)
      ->where('followable_id', '=', $serie->id)
      ->count()>0;

      DB::beginTransaction();
      try {
          if(!$userLikedPreviously) {
              // Send notification.
              $authors = $serie->authors()->get();
              Notification::send($authors, new UserLikedSeries(Auth::user(), $serie));
          }

          Auth::user()->like($serie);

          DB::commit();
      } catch (\Exception $e) {
          DB::rollback();

          return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
      }

      return response()->json(null, 200);
  }

  /**
   * Destroys a like relation between the logged-in user and a serie.
   *
   * @return \Illuminate\Http\Response
   */
  public function unlike($serieId)
  {
      $serie = Serie::find($serieId);

      if(!$serie) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      Auth::user()->unlike($serie);

      return response()->json(null, 200);
  }
}
