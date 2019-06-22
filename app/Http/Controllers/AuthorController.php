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
      return User::with([
          'series' => function($query) {
              $query->select('series.id', 'series.name');
          }
      ])->simplePaginate(RESULTS_PER_PAGE);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      $user = User::find($id);

      if(!$user) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      return $user;
  }
}
