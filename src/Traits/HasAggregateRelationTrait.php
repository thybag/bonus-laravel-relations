<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Models\InertModel;
use thybag\BonusLaravelRelations\Relations\HasAggregate;

trait HasAggregateRelationTrait
{
    /**
     * Return aggregated results in a relation like way.
     * Allows eager loading of aggregate results.
     *
     * @param $related - parent model name
     * @param $relation_key - key to group on
     *
     * @return HasAggregate Relation
     */
    public function hasAggregate(string $related, string $relation_key = null, string $sql = null)
    {
        $base = new $related;
        $instance = new InertModel();

        if (empty($relation_key)) {
            $relation_key = $this->getForeignKey();
        }

        return new HasAggregate($instance->setTable($base->getTable())->newQuery(), $this, $relation_key, $sql);
    }
}
