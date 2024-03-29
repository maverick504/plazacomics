<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravelista\Comments\Commentable;

class Chapter extends Model
{
    use Commentable;
    
    protected $table = 'chapters';
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'relase_date'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at');

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('serie_id', 'title', 'slug', 'thumbnail_url', 'relase_date', 'pages_count');

    /**
     * Scope a query to only include relased (public) chapters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelased($query)
    {
        return $query->where('relase_date', '<=', date('Y-m-d'));
    }

    /**
     * Checks is the serie is owned by a user.
     *
     * @param  int  $userId
     * @return boolean
     */
    public function isOwnedBy($userId)
    {
        $serie = $this->serie()->first();
        $authors = $serie->authors()->get();
        foreach($authors as $author) {
          if($author->id == $userId) {
            return true;
          }
        }
        return false;
    }

    /**
     * Checks is the serie has been relased.
     *
     * @param  int  $userId
     * @return boolean
     */
    public function hasBeenRelased()
    {
        if (strtotime(date_format($this->relase_date, "Y-m-d")) > strtotime("Y-m-d")) {
            return true;
        }
        return false;
    }

    public function serie()
    {
        return $this->belongsTo('App\Serie', 'serie_id', 'id');
    }

    public function pages()
    {
        return $this->hasMany('App\Page', 'chapter_id', 'id')->orderBy('order', 'asc');
    }
}
