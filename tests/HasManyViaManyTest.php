<?php
namespace thybag\BonusLaravelRelations\Test;

use Orchestra\Testbench\TestCase;
use thybag\BonusLaravelRelations\Test\Models\Shop;
use thybag\BonusLaravelRelations\Test\Models\Region;
use thybag\BonusLaravelRelations\Test\Models\Product;
use thybag\BonusLaravelRelations\Test\Models\Franchise;

class TestHasManyViaMany extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    public function testHasManyViaManyBasic()
    {
        [$uk, $usa] = $this->setUpData();

        // what we can do before
        $this->assertEquals(2, $uk->franchises->count());
        $this->assertEquals(4, $uk->shops->count());

        // And... overkill :D
        $this->assertEquals(7, $uk->products->count());
        $this->assertEquals(2, $usa->products->count());
    }

    public function testHasManyViaManyJoins()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsSpecificJoins->count());
        $this->assertEquals(2, $usa->productsSpecificJoins->count());
    }

    public function testHasManyViaManyArrayjoins()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsUsingArrayJoins->count());
        $this->assertEquals(2, $usa->productsUsingArrayJoins->count());
    }

    public function testHasManyViaManySpecificJoinKeys()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsSpecificJoinKeys->count());
        $this->assertEquals(2, $usa->productsSpecificJoinKeys->count());
    }


    public function testHasManyViaManyArrayModels()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsUsingArrayModels->count());
        $this->assertEquals(2, $usa->productsUsingArrayModels->count());
    }

    public function testHasManyViaManyMixedOkay()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsMixedOkay->count());
        $this->assertEquals(2, $usa->productsMixedOkay->count());
    }

    public function testHasManyViaManyUsingArrayJoinsWithModels()
    {
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsUsingArrayJoinsWithModels->count());
        $this->assertEquals(2, $usa->productsUsingArrayJoinsWithModels->count());
    }


    public function testHasManyViaManyMixedBad()
    {
        $this->expectException(\InvalidArgumentException::class);
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsMixedBad->count());
        $this->assertEquals(2, $usa->productsMixedBad->count());
    }

    public function testHasManyViaManyWithMysteryObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        [$uk, $usa] = $this->setUpData();

        $this->assertEquals(7, $uk->productsWithMysteryObject->count());
        $this->assertEquals(2, $usa->productsWithMysteryObject->count());
    }

    public function testHasManyViaManyEagerLoad()
    {
        $this->setUpData();

        $regions = Region::with('products')->get();

        $this->assertEquals(7, $regions->get(0)->products->count());
        $this->assertEquals(2, $regions->get(1)->products->count());
    }

    public function testHasManyViaManyWhereHas()
    {
        $this->setUpData();
        $regions = Region::whereHas('products')->get();
        $this->assertEquals(2, $regions->count());

        $regions = Region::whereHas('productsUsingArrayJoins')->get();
        $this->assertEquals(2, $regions->count());
    }

    protected function setUpData()
    {
         // Setup all the links
        $uk = Region::create(['name' => 'Uk']);
        $usa = Region::create(['name' => 'USA']);
        $france = Region::create(['name' => 'France']);

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
