<?php
namespace thybag\BonusLaravelRelations\Test;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use thybag\BonusLaravelRelations\Test\Models\Shop;
use thybag\BonusLaravelRelations\Test\Models\Rating;

class TestBelongsToOne extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    public function testtBelongsToOneLatestRatingReturnsLatest()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);

        $shop->latestRating()->save(Rating::make(['score' => 3, 'created_at' => Carbon::now()->subDay(4)]));
        $shop->latestRating()->save(Rating::make(['score' => 4, 'created_at' => Carbon::now()->subDay(2)]));
        $shop->latestRating()->save(Rating::make(['score' => 5, 'created_at' => Carbon::now()->subDay(1)]));

        $this->assertEquals(5, $shop->latestRating->score);
        $this->assertEquals(3, $shop->ratings->count());
    }

    public function testtBelongsToOneLatestRatingReturnsLatestWithEager()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);

        $shop->latestRating()->save(Rating::make(['score' => 3, 'created_at' => Carbon::now()->subDay(4)]));
        $shop->latestRating()->save(Rating::make(['score' => 4, 'created_at' => Carbon::now()->subDay(2)]));
        $shop->latestRating()->save(Rating::make(['score' => 5, 'created_at' => Carbon::now()->subDay(1)]));

        $shop = Shop::create(['name' => 'PieExpress']);

        $shop->latestRating()->save(Rating::make(['score' => 10, 'created_at' => Carbon::now()->subDay(4)]));
        $shop->latestRating()->save(Rating::make(['score' => 9, 'created_at' => Carbon::now()->subDay(2)]));
        $shop->latestRating()->save(Rating::make(['score' => 8, 'created_at' => Carbon::now()->subDay(1)]));

        $results = $shop->with('latestRating')->get();

        $this->assertEquals(5, $results->get(0)->latestRating->score);
        $this->assertEquals(8, $results->get(1)->latestRating->score);
    }
}
