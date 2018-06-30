<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Region extends Model {
    use BonusRelationsTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
    ];

    public function franchises()
    {
        return $this->hasMany(Franchise::class);
    }

    public function shops()
    {
        return $this->hasManyThrough(Shop::class, Franchise::class);
    }

    public function products()
    {
        return $this->hasManyViaMany(Product::class)->via(Shop::class)->via(Franchise::class);
    }

    public function products_specificJoinKeys()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via(Shop::class, 'shops.id', 'products.shop_id')
            ->via(Franchise::class, 'shops.franchise_id', 'franchises.id');
    }

    public function products_specificJoins()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via('shops','shops.id','products.shop_id')
            ->via('franchises', 'shops.franchise_id', 'franchises.id');
    }

    public function products_usingArray_joins()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id',
            [
                ['shops','shops.id','products.shop_id'],
                ['franchises', 'shops.franchise_id', 'franchises.id']
            ]);
    }

    public function products_usingArray_models()
    {
        return $this->hasManyViaMany(Product::class, null, null,
        [
            Shop::class,
            Franchise::class
        ]);
    }

    public function products_mixedOkay()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via(Shop::class)
            ->via('franchises', 'shops.franchise_id', 'franchises.id');
    }

    public function products_mixedBad()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via('shops','shops.id','products.shop_id')
            ->via(Franchise::class);
    }

}