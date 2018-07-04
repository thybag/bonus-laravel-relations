<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Product extends Model
{
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'amount',
        'value',
        'source_region_id',
        'shop_id'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function sourceRegion()
    {
        return $this->belongsTo(Region::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
