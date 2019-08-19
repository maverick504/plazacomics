<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Serie;

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

        return Serie::select('series.*')
        ->with([
            'genre1',
            'genre2',
            'authors'
        ])->public()
        ->distinct('series.id')
        ->join('chapters', 'chapters.serie_id', '=', 'series.id')
        ->where('chapters.relase_date', '=', date("Y-m-d", strtotime($weekday . ' this week')))
        ->get();
    }
}
