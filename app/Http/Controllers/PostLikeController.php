<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
// use App\Notifications\UserLikedSeries;
// use Illuminate\Support\Facades\Notification;

class PostLikeController extends Controller
{
  /**
   * Creates a like relation between the logged-in user and a post.
   *
   * @return \Illuminate\Http\Response
   */
  public function like($postId)
  {
      $post = Post::find($postId);

      if(!$post) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      $userLikedPreviously = DB::table('followables')
      ->where('followable_type', '=', 'App\Post')
      ->where('relation', '=', 'like')
      ->where('user_id', '=', auth()->user()->id)
      ->where('followable_id', '=', $post->id)
      ->count()>0;

      DB::beginTransaction();
      try {
          if(!$userLikedPreviously) {
              // Send notification.
              // $authors = $serie->authors()->get();
              // Notification::send($authors, new UserLikedSeries(auth()->user(), $serie));
          }

          auth()->user()->like($post);

          DB::commit();
      } catch (\Exception $e) {
          DB::rollback();

          return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
      }

      return response()->json(null, 200);
  }

  /**
   * Destroys a like relation between the logged-in user and a post.
   *
   * @return \Illuminate\Http\Response
   */
  public function unlike($postId)
  {
      $post = Post::find($postId);

      if(!$post) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      auth()->user()->unlike($post);

      return response()->json(null, 200);
  }
}
