<?php

namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use thybag\BonusLaravelRelations\Test\Models\Aisle;
use thybag\BonusLaravelRelations\Test\Models\Shop;

class StockLocation extends Pivot
{

    public $incrementing = true;

    protected $table = 'stock_locations';

    protected $fillable = [
        'shop_id',
        'aisle_id',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function aisle()
    {
        return $this->belongsTo(Aisle::class);
    }
}