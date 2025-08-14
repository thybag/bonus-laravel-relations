<?php

namespace thybag\BonusLaravelRelations\Traits;

use Illuminate\Database\Eloquent\Model;
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
     * @param $sql - aggregate sql query. Equivalent to selectRaw
     * @param $returnModel - model to be used in place of intertModel
     *
     * @return HasAggregate Relation
     */
    public function hasAggregate(string $related, string $relation_key = null, string $sql = null, ?string $returnModel = null)
    {
        $instance = new $related;

        if (empty($relation_key)) {
            $relation_key = $this->getForeignKey();
        }

        return new HasAggregate($instance->newQuery(), $this, $relation_key, $sql, $returnModel);
    }
}
