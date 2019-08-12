<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Serie;
use App\Chapter;

class ScheduleController extends Controller
{
    /**
     * Displays series updates by week.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $weekday = request('weekday');
        $weekdays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        if (!in_array($weekday, $weekdays)) {
            return response()->json(['message'=>'El dia de la semana otorgado no es válido.'], 400);
        }

        return Serie::select(['series.*', 'chapters.relase_date'])
        ->distinct('series.id')
        ->with('genre1', 'genre2')
        ->join('chapters', 'chapters.serie_id', '=', 'series.id')
        ->where('series.state', '!=', SERIE_STATE_DRAFT)
        ->where('chapters.relase_date', '=', date("Y-m-d", strtotime($weekday . ' this week')))
        ->orderBy('chapters.relase_date')
        ->get();
    }
}
