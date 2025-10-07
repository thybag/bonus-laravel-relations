<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Relations\HasMethod;

trait HasMethodRelationTrait
{
    /**
     * Calls a method or callback as a relationship
     *
     * @param $method - model method to call
     * @param bool $collection - If true results are returned as a collection rather than singular.
     * @param string|null $model - class path of model to be used in place of intertModel
     *
     * @return HasMethod
     */
    public function hasMethod($method, bool $collection = false, ?string $model = null)
    {
        return new HasMethod($this, $method, $collection, $model);
    }
}
