<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieAuthor extends Model
{
    protected $table = 'series_authors';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('serie_id', 'user_id');
}
