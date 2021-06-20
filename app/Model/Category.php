<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Model\SoftDeletes;
/**
 * @property int $id 
 * @property string $label 
 * @property string $value 
 * @property string $son 
 * @property string $icon 
 * @property int $sort 
 * @property int $check 
 * @property int $delete 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class Category extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'label', 'value', 'son', 'icon', 'status', 'deleted_at', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'sort' => 'integer', 'check' => 'integer', 'delete' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}