# Bonus Eloquent relations for Laravel

A selection of weird & wonderful additional relation types for laravel's eloquent ORM I've ended up needing.

Many of these are experimental and may behave in unexpected & none standard ways.

On the bright side it is tested.
[![Build Status](https://travis-ci.org/thybag/bonus-laravel-relations.svg?branch=master)](https://travis-ci.org/thybag/bonus-laravel-relations)
 
All licensed under the MIT license.

## Usage.

1. Install via composer using `composer install thybag/bonus-laravel-relations`
2. Include the `use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;` in to your model (or base model if you want them everywhere.)
3. Use the relations as you would any other.

Relations can be added one by one if only if you would prefer not to have all of them..

# Relations 

### BelongsToMorph
Get polymorphic relations of a single type.

```php
public function shop()
{
    return $this->belongsToMorph(Shop::class, 'noteable');
}
```

### HasManyViaMany
Define a relation via an unlimited amount of middle tables.

```php
public function products()
{
    return $this->hasManyViaMany(Product::class)->via(Shop::class)->via(Franchise::class);
}
```

### HasAggregate
Get an aggregate result as a relation. The main benefit of this approach is it allows easy lazy loading of this data on collections + interaction with the results in a more eloquent like way.

```php
public function productTotals()
{
    return $this->hasAggregate(Product::class)->selectRaw('
        COUNT(DISTINCT products.id) AS unique_products,
        SUM(products.amount) * AVG(products.value) AS stock_value,
        SUM(products.amount) AS total_products,
        AVG(products.value) AS average_product_value
    ');
}
```

### HasMethod
Use a local method as if it were a relation.

```php
public function totalValue()
{
    return $this->hasMethod(function () {
        return ['total' => ($this->amount * $this->value)];
    });
}
```
