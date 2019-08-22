<?php


namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Aisle extends Model
{
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'aisle_code',
    ];
}
