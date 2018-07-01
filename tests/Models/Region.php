<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;

class Region extends Model
{
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

    public function productsSpecificJoinKeys()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via(Shop::class, 'shops.id', 'products.shop_id')
            ->via(Franchise::class, 'shops.franchise_id', 'franchises.id');
    }

    public function productsSpecificJoins()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via('shops', 'shops.id', 'products.shop_id')
            ->via('franchises', 'shops.franchise_id', 'franchises.id');
    }

    public function productsUsingArrayJoins()
    {
        return $this->hasManyViaMany(
            Product::class,
            'id',
            'franchises.region_id',
            [
                ['shops','shops.id','products.shop_id'],
                ['franchises', 'shops.franchise_id', 'franchises.id']
            ]
        );
    }

    public function productsUsingArrayJoinsWithModels()
    {
        return $this->hasManyViaMany(
            Product::class,
            'id',
            'franchises.region_id',
            [
                [Shop::class, 'shops.id', 'products.shop_id'],
                [Franchise::class]
            ]
        );
    }

    public function productsWithMysteryObject()
    {
        return $this->hasManyViaMany(
            Product::class,
            'id',
            'franchises.region_id',
            [
                [Shop::class, 'shops.id', 'products.shop_id'],
                new Product
            ]
        );
    }

    public function productsUsingArrayModels()
    {
        return $this->hasManyViaMany(
            Product::class,
            null,
            null,
            [
            Shop::class,
            Franchise::class
            ]
        );
    }

    public function productsMixedOkay()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via(Shop::class)
            ->via('franchises', 'shops.franchise_id', 'franchises.id');
    }

    public function productsMixedBad()
    {
        return $this->hasManyViaMany(Product::class, 'id', 'franchises.region_id')
            ->via('shops', 'shops.id', 'products.shop_id')
            ->via(Franchise::class);
    }
}
