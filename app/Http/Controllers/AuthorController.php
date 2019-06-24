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
      ->simplePaginate(RESULTS_PER_PAGE);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      $user = User::whereHas('series', function($q){
          $q->public();
      })
      ->where('id', '=', $id)
      ->first();

      if(!$user) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      return $user;
  }
}
