<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use App\Http\Controllers\Controller;

class AuthorController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      return User::whereHas('series', function($q){
          $q->public();
      })
      ->with([
          'series' => function($query) {
              $query->select('series.id', 'series.name')->public();
          }
      ])
      ->paginate(RESULTS_PER_PAGE);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      $author = User::where('id', '=', $id)->first();

      if(!$author) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      if(auth()->user()) {
          $author->user_is_follower = $author->isFollowedBy(auth()->user());
      }
      $author->followers_count = $author->followers()->count();

      $author->visits = visits($author)->count();
      visits($author)->increment();

      return $author;
  }
}
