<?php

namespace thybag\BonusLaravelRelations\Relations;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Returns models via an unlimited number of joins
 */
class HasManyViaMany extends Relation
{
    protected $relationArray = [];
    protected $finalKey;
    protected $foreignKey;

    // Try and be smart
    protected $previousModel = false;

    public function __construct(Builder $query, Model $parent, $foreignKey = null, $finalKey = null, $relationArray = [])
    {
        $this->foreignKey = $foreignKey; // model key
        $this->finalKey = $finalKey ?  $finalKey : $parent->getForeignKey(); // name spaced key to use as parent
        $this->relationArray = $relationArray; // array of joins

        parent::__construct($query, $parent);
    }

    /**
     * @param  Class|string $table - class of table name
     * @param  string $leftColumn for join
     * @param  string $rightColumn for join
     * @return void
     */
    protected function handleJoinVia(string $table, string $leftColumn = null, string $rightColumn = null)
    {

        // If previousModel === false, we are starting a new query
        if ($this->previousModel === false) {
            $this->previousModel = $this->related;
        }

        // They passed a class? We an try and be smart
        if (!empty($this->previousModel) && class_exists($table)) {
            // Get current model
            $instance = new $table;
            $table = $instance->getTable();

            // No left col, grab the prev model & current foreignKey
            if (empty($leftColumn)) {
                $leftColumn = $this->previousModel->getTable() . '.' . $instance->getForeignKey();
            }

            // No right col? Grab the current table & the prev models key
            if (empty($rightColumn)) {
                $rightColumn = $table . '.' . $this->previousModel->getKeyName();
            }

            // Update previous model pointer
            $this->previousModel = $instance;
        } else {
            // Can't be smart if we don't know what came before
            $this->previousModel = null;
        }

        if (empty($leftColumn) || empty($rightColumn)) {
            throw new InvalidArgumentException('Unable to guess correct columns for manyViaMany join.');
        }

        // Apply join
        $this->query = $this->query->leftJoin($table, $leftColumn, $rightColumn);
    }

    /**
     * @param  Class|string $table - class of table name
     * @param  string $leftColumn for join
     * @param  string $rightColumn for join
     * @return Relation
     */
    public function via($table, $leftColumn = null, $rightColumn = null)
    {
        $this->handleJoinVia($table, $leftColumn, $rightColumn);
        return $this;
    }

    /**
     * *
     * @param array Models
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->whereIn($this->finalKey, $this->getKeys($models, $this->parent->getKeyName()));
    }

    /**
     * @param  Actually grab data
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        // Finalize
        $this->query->selectRaw($this->related->getTable() . '.*, ' . $this->finalKey . ' as parent_key');

        return $this->query->get($columns);
    }

    /**
     * Get results
     * @return Collection
     */
    public function getResults()
    {
        return $this->get();
    }

    /**
     * Handle predefined joins array
     * @return void
     */
    protected function initJoins()
    {
        foreach ($this->relationArray as $join) {
            // Array? or just a raw class
            if (is_array($join)) {
                call_user_func_array([$this, 'handleJoinVia'], $join);
            } elseif (class_exists($join)) {
                $this->handleJoinVia($join);
            } else {
                throw new InvalidArgumentException('Unknown join type passed to manyViaMany via array.');
            }
        }
    }

    /**
     * Add constraints
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->initJoins();
            $this->query->where('parent_key', $this->parent->{$this->parent->getKeyName()});
        }
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }
        return $models;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $dictionary = [];

        // First we will create a dictionary of models keyed by the foreign key of the
        // relationship as this will allow us to quickly access all of the related
        // models without having to do nested looping which will be quite slow.
        foreach ($results as $result) {
            $dictionary[$result->parent_key][] = $result;
        }

        return $dictionary;
    }

        /**
         * Match the eagerly loaded results to their parents.
         *
         * @param  array   $models
         * @param  \Illuminate\Database\Eloquent\Collection  $results
         * @param  string  $relation
         * @return array
         */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            $key = $model->getKey();

            if (isset($dictionary[$key])) {
                $value = $this->related->newCollection($dictionary[$key]);

                $model->setRelation($relation, $value);
            }
        }

        return $models;
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like whereColumn.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Builder  $parentQuery
     * @param  array|mixed $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {   
        $this->initJoins();

        return $this->query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(), '=', $this->finalKey
        );
    }
}
