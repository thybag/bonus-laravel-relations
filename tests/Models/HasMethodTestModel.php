<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\traits\BonusRelationsTrait;

class HasMethodTestModel extends Model {
    use BonusRelationsTrait;

    public $fillable = [
        'amount',
        'value',
        'product'
    ];

    public function getInfo()
    {
        return [
            'product' => ucfirst($this->product),
            'totalValue' => $this->amount * $this->value
        ];
    }

    public function asCallback()
    {
        return $this->hasMethod(function(){
            return [
                'product' => ucfirst($this->product),
                'totalValue' => $this->amount * $this->value
            ];
        });
    }

    public function asMethod()
    {
        return $this->hasMethod('getInfo');
    }

    public function asMethodForceCollection()
    {
        return $this->hasMethod('getInfo', true);
    }

    public function asCallback_returnCollection()
    {
        return $this->hasMethod(function(){
            return collect([
                'test' => 1,
                'test2' => 2,
                'test3' => 3
            ]);
        }, true);
    }


    public function callbackNull()
    {
        return $this->hasMethod(function(){
            return null;
        });
    }

    public function callbackEmptyString()
    {
        return $this->hasMethod(function(){
            return '';
        });
    }

    public function callbackEmpty()
    {
        return $this->hasMethod(function(){

        });
    }

    public function callbackAsArray()
    {
        return $this->hasMethod(function(){
            return ['a' => 'one','b' => 'two'];
        });
    }

        public function callbackAsObject()
    {
        return $this->hasMethod(function(){
            $test = new \stdClass();
            $test->a = 'one';
            $test->b = 'two';
            return $test;
        });
    }
}