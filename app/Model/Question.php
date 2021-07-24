<?php

declare (strict_types=1);
namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $question 
 * @property string $A 
 * @property string $B 
 * @property string $C 
 * @property string $D 
 * @property string $answer
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @property string $deleted_at
 */
class Question extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'category', 'question', 'A', 'B', 'C', 'D', 'answer', 'updated_at', 'created_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'updated_at' => 'datetime'];
}