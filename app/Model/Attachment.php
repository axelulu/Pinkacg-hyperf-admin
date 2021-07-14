<?php

declare (strict_types=1);
namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id
 * @property string $title
 * @property string $original_name 
 * @property string $filename 
 * @property string $path 
 * @property string $type 
 * @property string $cat 
 * @property int $size 
 * @property string $user_id 
 * @property Carbon $created_at 
 * @property Carbon $updated_at 
 */
class Attachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'cat' => 'integer', 'size' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}