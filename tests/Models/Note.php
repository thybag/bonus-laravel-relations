<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Note extends Model
{
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'note',
        'noteable_id',
        'noteable_type',
    ];

    public function noteable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsToMorph(Product::class, 'noteable');
    }

    public function shop()
    {
        return $this->belongsToMorph(Shop::class, 'noteable');
    }

    public function shopOverrideCols()
    {
        return $this->belongsToMorph(Shop::class, 'noteable', 'fake_type', 'fake_id');
    }
}
