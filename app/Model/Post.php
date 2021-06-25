<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $author 
 * @property string $title 
 * @property string $content 
 * @property string $excerpt 
 * @property string $type 
 * @property string $status 
 * @property string $comment_status 
 * @property string $password 
 * @property string $name 
 * @property string $menu 
 * @property string $tag 
 * @property string $guid 
 * @property int $comment_count 
 * @property string $download 
 * @property string $music 
 * @property string $video 
 * @property string $header_img 
 * @property int $views 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Post extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'author', 'title', 'content', 'excerpt', 'type', 'status', 'comment_status', 'password', 'name', 'menu', 'tag', 'guid', 'comment_count', 'download_status', 'download', 'music', 'video', 'header_img', 'views', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'author' => 'integer', 'comment_count' => 'integer', 'views' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}