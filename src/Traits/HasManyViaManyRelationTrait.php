<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Relations\HasManyViaMany;

trait HasManyViaManyRelationTrait
{
    /**
     * Define a relation via an unlimited amount of middle tables.
     * 
     * @param  string|class - Results to return
     * @param  string - foreignKey
     * @param  string - finalKey for joins
     * @param  array - Array of vias - this is mostly for legacy reasons now.
     * @return HasManyViaMany
     */
    public function hasManyViaMany($related, string $foreignKey = null, string $finalKey = null, array $relationArray = [])
    {
        $instance = new $related;

        // Guess local key
        if (empty($foreignKey)) {
            $foreignKey = $instance->getKeyName();
        }

        return new HasManyViaMany($instance->newQuery(), $this, $foreignKey, $finalKey, $relationArray);
    }
}