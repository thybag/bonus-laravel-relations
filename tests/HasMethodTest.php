<?php
namespace thybag\BonusLaravelRelations\Test;

use Orchestra\Testbench\TestCase;
use thybag\BonusLaravelRelations\Test\Models\HasMethodTestModel;

class TestHasMethod extends TestCase
{
    public function testHasMethod_withPublicMethod()
    {
        $test = HasMethodTestModel::make([
            'amount' => 5,
            'value' => 2,
            'product' => 'cake'
        ]);

        $this->assertEquals('Cake', $test->asMethod->product);
        $this->assertEquals(10, $test->asMethod->totalValue);
    }

    public function testHasMethod_withCallback()
    {
        $test = HasMethodTestModel::make([
            'amount' => 5,
            'value' => 3,
            'product' => 'pie'
        ]);

        $this->assertEquals('Pie', $test->asCallback->product);
        $this->assertEquals(15, $test->asCallback->totalValue);
    }

    public function testHasMethod_withPublicMethod_forceCollection()
    {
        $test = HasMethodTestModel::make([
            'amount' => 5,
            'value' => 3,
            'product' => 'pie'
        ]);

        $this->assertEquals('Pie', $test->asMethodForceCollection->get('product'));
        $this->assertEquals(2, $test->asMethodForceCollection->count());
    }

    public function testHasMethod_withCallback_returnedCollection()
    {
        $test = HasMethodTestModel::make();

        $this->assertEquals(1, $test->asCallback_returnCollection->first());
        $this->assertEquals(3, $test->asCallback_returnCollection->count());
    }

    public function testHasMethod_withCallback_badResults()
    {
        $test = HasMethodTestModel::make();

        $this->assertNull($test->callbackNull);
        $this->assertNull($test->callbackEmptyString);
        $this->assertNull($test->callbackEmpty);
    }

    public function testHasMethod_withCallback_array()
    {
        $test = HasMethodTestModel::make();
        $this->assertEquals('one', $test->callbackAsArray->a);
    }

    public function testHasMethod_withCallback_object()
    {
        $test = HasMethodTestModel::make();
        $this->assertEquals('one', $test->callbackAsObject->a);
    }
}