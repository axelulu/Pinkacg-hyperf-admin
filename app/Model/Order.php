<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property int $user_id 
 * @property int $post_id 
 * @property string $type 
 * @property string $download_key 
 * @property int $credit 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 */
class Order extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'post_id', 'type', 'download_key', 'credit', 'updated_at', 'created_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'post_id' => 'integer', 'credit' => 'integer', 'updated_at' => 'datetime', 'created_at' => 'datetime'];
}