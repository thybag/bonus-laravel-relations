# Bonus Eloquent relations for Laravel

A selection of weird & wonderful additional relation types for laravel's eloquent ORM I've ended up needing.

Many of these are experimental and may behave in unexpected & none standard ways.

On the bright side - it's tested!       
[![Build Status](https://travis-ci.org/thybag/bonus-laravel-relations.svg?branch=master)](https://travis-ci.org/thybag/bonus-laravel-relations)
 
All licensed under the MIT license.

## Usage.

1. Install via composer using `composer require thybag/bonus-laravel-relations`
2. Include the `use thybag\BonusLaravelRelations\Traits\BonusRelationsTrait;` in to your model (or base model if you want them everywhere.)
3. Use the relations as you would any other.

The relation traits can also be added individually if you prefer that.

* Run tests with `composer test`
* Run lint with `composer lint`

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
Define a relation via an unlimited number of middle tables.

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

### BelongsToOne
Define a one-to-one relation through a pivot table.

```php
public function latestRating()
{
    return $this->belongsToOne(Rating::class, 'shop_rating')->latest('created_at');
}
```

# InertModel

As the relationships `HasMethod` and `HasAggregate` don't return a traditional model from the database, a special model called type `InertModel` is used. This allows the relationship to fill the model with arbitrary attributes without the risk of causing unexpected behavior.

If you would like to use a custom InertModel rather than the one provided, create a config called `bonus-laravel-relationships.php` in your config folder, then set the value `inertModel` with the class path to the model you would like to use instead. I would recommend whatever model you use inherits from the base IntertModel to avoid unexpected behavior.