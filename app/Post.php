<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;

class Post extends Model
{
    use CanBeLiked;

    const TYPE_ILLUSTRATION = 1;
    const TYPE_NEW_CHAPTER = 2;

    const TYPES = array(
        self::TYPE_ILLUSTRATION => 'illustration',
        self::TYPE_NEW_CHAPTER => 'new_chapter'
    );

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

    public function serie()
    {
        return $this->hasOne('App\Serie', 'id', 'serie_id');
    }

    public function chapter()
    {
        return $this->hasOne('App\Chapter', 'id', 'chapter_id');
    }

    /**
     * Get the post's likes count.
     *
     * @param  string  $value
     * @return string
     */
    public function getLikesCountAttribute($value)
    {
        return intval($value);
    }

    /**
     * If the user liked the post.
     *
     * @param  string  $value
     * @return string
     */
    public function getUserLikedAttribute($value)
    {
        return boolval($value);
    }

    /**
     * If the user liked the post.
     *
     * @param  string  $value
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return self::TYPES[$value];
    }
}
