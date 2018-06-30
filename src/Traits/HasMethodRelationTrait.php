<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Relations\HasMethod;

trait HasMethodRelationTrait
{
    /**
     * Calls a method or callback as a relationship
     *
     * @param $method - model method to call
     * @return HasMethod
     */
    public function hasMethod($method, $collection = false)
    {
        return new HasMethod($this, $method, $collection);
    }
}
