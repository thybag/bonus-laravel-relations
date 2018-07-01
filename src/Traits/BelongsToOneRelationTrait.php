<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Relations\BelongsToOne;

trait BelongsToOneRelationTrait
{
    /**
     * Define a one-to-one through a pivot table relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $parentKey
     * @param  string  $relation
     * @param  string  $relatedKey
     * @return BelongsToOne
     */
    public function belongsToOne(
        $related,
        $table = null,
        $foreignKey = null,
        $parentKey = null,
        $relation = null,
        $parentOtherKey = null,
        $relatedKey = null
    ) {
        if (is_null($relation)) {
            $relation = $this->getRelations();
        }

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        $instance = new $related;
        $relatedPivotKey = (empty($parentKey)) ? $instance->getForeignKey() : $parentKey;

        return new BelongsToOne(
            $instance->newQuery(),
            $this,
            $table,
            $foreignKey ?: $this->getForeignKey(),
            $relatedPivotKey,
            $parentOtherKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(),
            $relation
        );
    }
}
