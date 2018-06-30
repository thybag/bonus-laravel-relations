<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Shop extends Model {
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'franchise_id'
    ];

    protected $productTotalsSql = '
        COUNT(DISTINCT products.id) AS unique_products,
        SUM(products.amount) * AVG(products.value) AS stock_value,
        SUM(products.amount) AS total_products,
        AVG(products.value) AS average_product_value
    ';

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productTotals()
    {
        return $this->hasAggregate(Product::class, 'products.shop_id', $this->productTotalsSql);
    }

    public function productTotalsViaRaw()
    {
        return $this->hasAggregate(Product::class)->selectRaw($this->productTotalsSql);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}