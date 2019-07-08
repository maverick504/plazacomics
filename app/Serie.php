<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Overtrue\LaravelFollow\Traits\CanBeSubscribed;
use Overtrue\LaravelFollow\Traits\CanBeLiked;

class Serie extends Model
{
    use CanBeSubscribed;
    use CanBeLiked;

    protected $table = 'series';
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('name', 'slug', 'state', 'genre1', 'genre2', 'licence', 'synopsis', 'explicit_content', 'cover_url', 'banner_url');

    /**
     * Scope a query to only include series owned by a specified user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->whereHas('authors', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        });
    }

    /**
     * Scope a query to only include public series.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('state', '!=', SERIE_STATE_DRAFT);
    }

    /**
     * Checks is the serie is owned by a user.
     *
     * @param  int  $userId
     * @return boolean
     */
    public function isOwnedBy($userId)
    {
        $authors = $this->authors()->get();
        foreach($authors as $author) {
          if($author->id == $userId) {
            return true;
          }
        }
        return false;
    }

    /**
     * Checks is the serie is public.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return $this->state !== SERIE_STATE_DRAFT;
    }

    /**
     * Checks is the serie is owned by a user.
     */
    public function recountChaptersAndPages()
    {
        $totals = Serie::select(
            DB::raw("SUM(1) AS total_chapters"),
            DB::raw("SUM(chapters.total_pages) AS total_pages"))
        ->join('chapters', 'series.id', '=', 'chapters.serie_id')
        ->where('series.id', $this->id)
        ->first();
        $this->total_chapters = $totals->total_chapters;
        $this->total_pages = $totals->total_pages;
        $this->save();
    }

    public function genre1()
    {
        return $this->hasOne('App\Genre', 'id', 'genre1');
    }

    public function genre2()
    {
        return $this->hasOne('App\Genre', 'id', 'genre2');
    }

    public function licence()
    {
        return $this->hasOne('App\Licence', 'id', 'licence');
    }

    public function authors()
    {
        return $this->hasManyThrough('App\User', 'App\SerieAuthor', 'serie_id', 'id', 'id', 'user_id');
    }
}
