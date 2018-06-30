<?php
namespace thybag\BonusLaravelRelations\Test;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facade\Schema;
use thybag\BonusLaravelRelations\Test\Models\Shop;
use thybag\BonusLaravelRelations\Test\Models\Product;

class TestHasAggregate extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    public function testHasAggregateThreeParam()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $shop->products()->save(Product::make(['name' => 'Cheese', 'amount' => 5, 'value' => 5]));
        $shop->products()->save(Product::make(['name' => 'Ham', 'amount' => 5, 'value' => 2]));
        $shop->products()->save(Product::make(['name' => 'Eggs', 'amount' => 10, 'value' => 2]));

        $this->assertEquals(20, $shop->productTotals->total_products);
        $this->assertEquals(3, $shop->productTotals->unique_products);
        $this->assertEquals(3, $shop->productTotals->average_product_value);
    }

    public function testHasAggregateProductTotalsViaRaw()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $shop->products()->save(Product::make(['name' => 'Cheese', 'amount' => 5, 'value' => 5]));
        $shop->products()->save(Product::make(['name' => 'Ham', 'amount' => 5, 'value' => 2]));
        $shop->products()->save(Product::make(['name' => 'Eggs', 'amount' => 10, 'value' => 2]));

        $this->assertEquals(20, $shop->productTotalsViaRaw->total_products);
        $this->assertEquals(3, $shop->productTotalsViaRaw->unique_products);
        $this->assertEquals(3, $shop->productTotalsViaRaw->average_product_value);
    }


    public function testHasAggregateWithLazyLoad()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $shop->products()->save(Product::make(['name' => 'Cheese', 'amount' => 5, 'value' => 5]));
        $shop->products()->save(Product::make(['name' => 'Ham', 'amount' => 5, 'value' => 2]));
        $shop->products()->save(Product::make(['name' => 'Eggs', 'amount' => 10, 'value' => 2]));

        $shop = Shop::create(['name' => 'PieLand']);
        $shop->products()->save(Product::make(['name' => 'Cool pie', 'amount' => 10, 'value' => 1]));
        $shop->products()->save(Product::make(['name' => 'Nice pie', 'amount' => 10, 'value' => 1]));

        $results = Shop::with('productTotals')->get();

        $shop1 = $results->get(0);
        $shop2 = $results->get(1);

        $this->assertEquals(20, $shop1->productTotals->total_products);
        $this->assertEquals(3, $shop1->productTotals->unique_products);

        $this->assertEquals(20, $shop2->productTotals->total_products);
        $this->assertEquals(2, $shop2->productTotals->unique_products);
    }


    public function testHasAggregateExists()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $shop->products()->save(Product::make(['name' => 'Cheese', 'amount' => 5, 'value' => 5]));

        $shop = Shop::create(['name' => 'BaconPlanet']);
        $shop->products()->save(Product::make(['name' => 'Bacon', 'amount' => 1, 'value' => 90]));

        $shop = Shop::create(['name' => 'PieLand']);


        $this->assertEquals(2, Shop::whereHas('productTotals')->count());

        $results = Shop::whereHas('productTotals')->get();

        $this->assertEquals(2, $results->count());
    }
}
