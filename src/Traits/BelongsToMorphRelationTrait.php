<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Relations\BelongsToMorph;

trait BelongsToMorphRelationTrait
{
    /**
     * Returns polymorphic relations of a specific type
     *
     * @param  string|class
     * @param  string - name of relation
     * @param  string - type column
     * @param  string - id column
     * @param  string - external key
     * @param  string - relation name
     * @return BelongsToMorph
     */
    public function belongsToMorph($related, $name, $type = null, $id = null, $otherKey = null, $relation = null)
    {
        $instance = new $related;

        if (is_null($relation)) {
            $relation = $this->getRelations();
        }

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        list($type, $id) = $this->getMorphs($name, $type, $id);
        $localKey = $otherKey ?: $this->getKeyName();

        return new BelongsToMorph($instance->newQuery(), $this, $instance->getMorphClass(), $type, $id, $localKey, $relation);
    }
}
