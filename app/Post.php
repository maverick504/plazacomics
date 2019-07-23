<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;

class Post extends Model
{
    use CanBeLiked;

    const TYPE_ILLUSTRATION = 1;

    protected $table = 'posts';
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
    protected $fillable = array('title', 'description', 'explicit_content');

    protected $casts = [
        'images' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
