<?php

namespace thybag\BonusLaravelRelations\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Eloquent\Relations\Relation;
use thybag\BonusLaravelRelations\Traits\InertModelTrait;

/**
 * Get Aggregate query data as a relation.
 * Allow lazy load and existence checks via whereHas.
 */
class HasAggregate extends Relation
{
    use InertModelTrait;

    // Key to link relation on
    protected $relation_key = null;
    // Aggregate sql - can be supplied by selectRaw instead.
    protected $aggregate_sql = '';

    /**
     * Set up relation
     *
     * @param Builder
     * @param Model
     * @param string
     * @param string|null
     */
    public function __construct(Builder $query, Model $parent, string $relation_key, string $sql = null)
    {
        // The builder provided is for an instance of the real model. We want to swap this out for our
        // inert model in order to return custom attributes based on the query. To do this we grab a copy
        // of our InertModel and give it the table from the real one.
        $query = $this->getInertModelInstance()->setTable($query->getModel()->getTable())->newQuery();

        // Set relation key
        $this->relation_key = $relation_key;
        $this->aggregate_sql = $sql;

        // As normal
        parent::__construct($query, $parent);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
        return $models;
    }

    /**
     * Add constraints for a lazy load
     *
     * @param array $models
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->
            selectRaw($this->relation_key . ' as id')
            ->whereIn($this->relation_key, $this->getKeys($models, 'id'))
            ->groupBy($this->relation_key);

        if (!empty($this->aggregate_sql)) {
            $this->query->selectRaw($this->aggregate_sql);
        }
    }

    /**
     * Add constraints for a standard load
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->relation_key, '=', $this->parent->getKey())
                ->selectRaw($this->relation_key . ' as id')
                ->groupBy($this->relation_key);

            if (!empty($this->aggregate_sql)) {
                $this->query->selectRaw($this->aggregate_sql);
            }
        }
    }

    /**
     * Match lazy loaded results to their parent models
     *
     * @param  array Models
     * @param  Collection Loaded aggregate models.
     * @param  string $relation
     * @return array Models
     */
    public function match(array $models, Collection $results, $relation)
    {
        // Overides match so it only returns one model as the relation.
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->getKey()])) {
                $model->setRelation($relation, $dictionary[$key]);
            }
        }
        return $models;
    }

    /**
     * Get results for aggregate relation called on single model
     *
     * @return Model
     */
    public function getResults()
    {
        return $this->first();
    }

    /**
     * Build model dictionary keyed by the relation_key
     *
     * @param  Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[$result->id] =  $result;
        }

        return $dictionary;
    }

    /**
     * Ensure relation can be used in whereHas's
     * Remember to use having not where on aggregate params
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        // Set up basic query
        $query->groupBy($this->relation_key)
        ->whereColumn(
            $this->getQualifiedParentKeyName(),
            '=',
            $this->relation_key
        );

        // Get select sql from builder (if set via selectRaw)
        if (!empty($this->query->getQuery()->columns)) {
             $select = implode(
                 ', ',
                 array_map(
                     fn ($column) => $column instanceof Expression ? $column->getValue($this->getGrammar()) : $column,
                     $this->query->getQuery()->columns
                 )
             );
             $query->selectRaw($select);
        }

        // Get via aggregate sql val if set using that method
        if (!empty($this->aggregate_sql)) {
             $query->selectRaw($this->aggregate_sql);
        }
       
        return $query;
    }
}
