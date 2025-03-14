<?php
namespace thybag\BonusLaravelRelations\Test;

use Orchestra\Testbench\TestCase;
use thybag\BonusLaravelRelations\Test\Models\Shop;
use thybag\BonusLaravelRelations\Test\Models\Product;
use thybag\BonusLaravelRelations\Test\Models\Note;

class BelongsToMorphTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/database');
    }

    public function testBelongsToMorphExists()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $shop->notes()->save(Note::make(['note' => 'Hmmm']));
        $shop->notes()->save(Note::make(['note' => 'Odd?']));

        $product = Product::create(['name' => 'Cheese', 'amount' => 5, 'value' => 5, 'shop_id' => 1]);
        $product->notes()->save(Note::make(['note' => 'cheesey']));

        $product = Product::create(['name' => 'MegaCheese', 'amount' => 5, 'value' => 5, 'shop_id' => 1]);
        $product->notes()->save(Note::make(['note' => 'Less chessy']));
        $product->notes()->save(Note::make(['note' => 'Actually - its okay']));

        $this->assertEquals(3, Note::whereHas('product')->get()->count());
        $this->assertEquals(2, Note::whereHas('shop')->get()->count());
        $this->assertEquals(5, Note::whereHas('noteable')->get()->count());
    }

    public function testBelongsToMorphGetSingleResults()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $note = Note::make(['note' => 'Hmmm']);
        $shop->notes()->save($note);

        $product = Product::create(['name' => 'Chedder', 'amount' => 5, 'value' => 5, 'shop_id' => 1]);
        $note2 = Note::make(['note' => 'Ah']);
        $product->notes()->save($note2);


        $this->assertNull($note->product);
        $this->assertEquals('CheeseExpress', $note->shop->name);

        $this->assertNull($note2->shop);
        $this->assertEquals('Chedder', $note2->product->name);
    }

    public function testBelongsToMorphEagerLoad()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $note = Note::make(['note' => 'Hmmm']);
        $shop->notes()->save($note);

        $product = Product::create(['name' => 'Chedder', 'amount' => 5, 'value' => 5, 'shop_id' => 1]);
        $note2 = Note::make(['note' => 'Ah']);
        $product->notes()->save($note2);

        $notes = Note::with('product')->get();

        $this->assertNull($notes->get(0)->product);
        $this->assertEquals('Chedder', $notes->get(1)->product->name);
    }

    public function testBelongsToMorphAltColumns()
    {
        $shop = Shop::create(['name' => 'CheeseExpress']);
        $note = Note::make(['note' => 'Hmmm']);

        $note->fake_id = $shop->id;
        $note->fake_type = Shop::class;

        $this->assertEquals('CheeseExpress', $note->shopOverrideCols->name);
        $this->assertNull($note->shop);
    }
}
