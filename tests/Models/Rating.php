<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Rating extends Model
{
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'score',
        'created_at'
    ];

    public function noteable()
    {
        return $this->morphTo();
    }
}
