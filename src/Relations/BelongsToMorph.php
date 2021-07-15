<?php

namespace thybag\BonusLaravelRelations\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Returns polymorphic relations of a specific type
 */
class BelongsToMorph extends BelongsTo
{
    // Class or morph name of type to return
    protected $morphName;

    // Col containing morph type
    protected $morphType;

    public function __construct(Builder $query, Model $parent, string $name, string $type, string $id, string $otherKey, $relation)
    {
        $this->morphName = $name;
        $this->morphType = $type;

        parent::__construct($query, $parent, $id, $otherKey, $relation);
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @return Builder
     */
    public function getRelationQuery()
    {
        $table = $this->getParent()->getTable();
        $query = parent::getRelationQuery();

        return $query->where("{$table}.{$this->morphType}", '=', $this->morphName);
    }

    /**
     * Ensure relation can be used in whereHas's
     *
     * @param  Builder  $query
     * @param  Builder  $parentQuery
     * @param  array|mixed  $columns
     * @return Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        // Grab parent version
        $query = parent::getRelationExistenceQuery($query, $parentQuery, $columns);

        // Limit to valid morph types
        return $query->where(
            $this->getParent()->getTable() . "." . $this->morphType,
            '=',
            $this->morphName
        );
    }

    /**
     * Get the result of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        // if we are getting a single result, check the morph type of the current parent matches
        // what we are trying to extract.
        if ($this->getParent()->{$this->morphType} === $this->morphName) {
            return $this->query->first();
        }

        return null;
    }

    /**
     * Only attempt to load relations where the items morph type matches what we are looking for.
     *
     * @param  array Models.
     * @return array
     */
    protected function getEagerModelKeys(array $models)
    {
        $keys = [];
        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            // Filter collection down so we don't match keys that happen to exist in both this model and another poly model
            if (! is_null($value = $model->{$this->foreignKey}) && $model->{$this->morphType} == $this->morphName) {
                $keys[] = $value;
            }
        }

        // If there are no keys that were not null we will just return an array with null
        // so this query wont fail plus returns zero results, which should be what the
        // developer expects to happen in this situation. Otherwise we'll sort them.
        if (count($keys) === 0) {
            return [null];
        }

        sort($keys);

        return array_values(array_unique($keys));
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $foreign = $this->foreignKey;

        $owner = $this->ownerKey;

        // Create dict of morph type:id
        $dictionary = [];
        foreach ($results as $result) {
            $dictionary[$result->getMorphClass() . ':' . $result->getAttribute($owner)] = $result;
        }

        // For each model found, get its requested morph type + id to build key in order to check
        // against the above rows
        foreach ($models as $model) {
            $key = $model->{$this->morphType} . ':' . $model->{$foreign};
            if (isset($dictionary[$key])) {
                $model->setRelation($relation, $dictionary[$key]);
            }
        }

        return $models;
    }
}
