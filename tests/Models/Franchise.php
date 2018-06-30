<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\traits\BonusRelationsTrait;

class Franchise extends Model {
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'region_id'
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}