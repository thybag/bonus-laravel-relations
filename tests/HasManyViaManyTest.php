<?php
namespace thybag\BonusLaravelRelations\Test;

use Orchestra\Testbench\TestCase;
use thybag\BonusLaravelRelations\Test\Models\Shop;
use thybag\BonusLaravelRelations\Test\Models\Product;
use thybag\BonusLaravelRelations\Test\Models\Franchise;
use thybag\BonusLaravelRelations\Test\Models\Region;

class TestHasManyViaMany extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    public function testHasManyViaMany_basic()
    {
        [$uk, $usa] = $this->setUpData();

        // what we can do before
        $this->assertEquals(2, $uk->franchises->count());
        $this->assertEquals(4, $uk->shops->count());

        // And... overkill :D
        $this->assertEquals(7, $uk->products->count());
        $this->assertEquals(2, $usa->products->count());
    }

    public function testHasManyViaMany_joins()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->products_specificJoins->count());
        $this->assertEquals(2, $usa->products_specificJoins->count());
    }

    public function testHasManyViaMany_arrayjoins()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->products_usingArray_joins->count());
        $this->assertEquals(2, $usa->products_usingArray_joins->count());
    }

    public function testHasManyViaMany_SpecificJoinKeys()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->products_specificJoinKeys->count());
        $this->assertEquals(2, $usa->products_specificJoinKeys->count());
    }


    public function testHasManyViaMany_arrayModels()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->products_usingArray_models->count());
        $this->assertEquals(2, $usa->products_usingArray_models->count());
    }

    public function testHasManyViaMany_mixedOkay()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->products_mixedOkay->count());
        $this->assertEquals(2, $usa->products_mixedOkay->count());
    }

    public function testHasManyViaMany_mixedBad()
    {
        $this->expectException(\InvalidArgumentException::class);
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->products_mixedBad->count());
        $this->assertEquals(2, $usa->products_mixedBad->count());
    }

    public function testHasManyViaMany_eagerLoad()
    {
        $this->setUpData();

        $regions = Region::with('products')->get();

        $this->assertEquals(7, $regions->get(0)->products->count());
        $this->assertEquals(2,  $regions->get(1)->products->count());
    }

    protected function setUpData() {
         // Setup all the links
        $uk = Region::create(['name' => 'Uk']);
        $usa = Region::create(['name' => 'USA']);

        $shop1 = Shop::create(['name' => 'CheeseExpress']);
        $shop1->products()->save(Product::make(['name' => 'Cheese', 'amount' => 5, 'value' => 5]));
        $shop1->products()->save(Product::make(['name' => 'Ham', 'amount' => 5, 'value' => 2]));
        $shop1->products()->save(Product::make(['name' => 'Eggs', 'amount' => 10, 'value' => 2]));

        $shop2 = Shop::create(['name' => 'CheeseCo']);
        $shop2->products()->save(Product::make(['name' => 'MegaCheese', 'amount' => 5, 'value' => 5]));

        $shop3 = Shop::create(['name' => 'BatCheese']);
        $shop3->products()->save(Product::make(['name' => 'Hyper cheese', 'amount' => 5, 'value' => 5]));

        $franchise1 = Franchise::create(['name' => 'Cheese Inc']);
        $franchise1->shops()->save($shop1);
        $franchise1->shops()->save($shop2);
        $franchise1->shops()->save($shop3);

        $franchise2 = Franchise::create(['name' => 'Pie Inc']);

        $shop = Shop::create(['name' => 'PieLand']);
        $shop->products()->save(Product::make(['name' => 'Cool pie', 'amount' => 10, 'value' => 1]));
        $shop->products()->save(Product::make(['name' => 'Nice pie', 'amount' => 10, 'value' => 1]));
        $franchise2->shops()->save($shop);

        $uk->franchises()->save($franchise1);
        $uk->franchises()->save($franchise2);

        $franchise = Franchise::create(['name' => 'Pie Inc USA']);

        $shop = Shop::create(['name' => 'American PieLand']);
        $shop->products()->save(Product::make(['name' => 'Cool pie', 'amount' => 10, 'value' => 1]));
        $shop->products()->save(Product::make(['name' => 'Nice pie', 'amount' => 10, 'value' => 1]));
        $franchise->shops()->save($shop);

        $usa->franchises()->save($franchise);

        return [$uk, $usa];
    }



}