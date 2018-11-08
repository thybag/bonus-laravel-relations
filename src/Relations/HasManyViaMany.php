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

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * Set up many via many
     *
     * @param Builder $query         [description]
     * @param Model   $parent        [description]
     * @param [type]  $foreignKey    [description]
     * @param [type]  $finalKey      [description]
     * @param array   $relationArray [description]
     */
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
                $leftColumn = $instance->getForeignKey();
            }

            // No right col? Grab the current table & the prev models key
            if (empty($rightColumn)) {
                $rightColumn = $this->previousModel->getKeyName();
            }

            // Attempt to auto qualify any table keys
            $leftColumn = $this->qualifyKey($leftColumn, $this->previousModel->getTable());
            $rightColumn = $this->qualifyKey($rightColumn, $table);

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

    protected function qualifyKey($key, $table)
    {
        return strpos($key, '.') !== false ? $key : "$table.$key";
    }

    /**
     * @param  Class|string $table - class of table name
     * @param  string $leftColumn for join
     * @param  string $rightColumn for join
     * @return Relation
     */
    public function via($table, $leftColumn = null, $rightColumn = null)
    {
        // Push to joins array
        $this->relationArray[] = [$table, $leftColumn, $rightColumn];
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
     * Select required columns from tables
     *
     * @param  array  $columns
     * @return array  $columns
     */
    protected function applySelects($columns = ['*'])
    {
        // No columns? default to *
        if (!$this->query->getQuery()->columns) {
            $columns = $columns == ['*'] ? 'DISTINCT ' . $this->related->getTable() . '.*' : $columns;
            $this->query->selectRaw($columns);
        }

        // Add selection key
        $this->query->selectRaw($this->finalKey . ' as parent_key');

        return $columns;
    }

    /**
     * @param  Actually grab data
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        // Finalize & get
        $this->applySelects($columns);
        $this->initJoins();

        return $this->query->get($columns);
    }

    /**
     * @param  Actually grab data
     * @return Collection
     */
    public function first($columns = ['*'])
    {
        // Finalize & get
        $this->applySelects($columns);
        $this->initJoins();

        return $this->query->first($columns);
    }

    /**
     * Init joins before ToSqling to provide realistic output
     *
     * @param  array  $columns [description]
     * @return [type]          [description]
     */
    public function toSql($columns = ['*'])
    {
        // Finalize & get
        $this->applySelects($columns);
        $this->initJoins();

        return parent::toSql();
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $columns = $this->applySelects($columns);
        $this->initJoins();
        return $this->query->paginate($perPage, $columns, $pageName, $page);
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
        // Store existing joins and clear list
        $joins = !empty($this->query->getQuery()->joins) ? $this->query->getQuery()->joins : [];
        $this->query->getQuery()->joins = [];

        // Apply relation joins
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

        // Re-add existing joins to the end (we need to ensure relation joins run first)
        $this->query->getQuery()->joins = array_merge($this->query->getQuery()->joins, $joins);
    }

    /**
     * Add constraints
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->finalKey, $this->parent->{$this->parent->getKeyName()});
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
        if ($parentQuery->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        // Apply joins
        $this->initJoins();

        // Normal behavior
        return $this->query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(),
            '=',
            $this->finalKey
        );
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Builder  $parentQuery
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationExistenceQueryForSelfRelation(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        $this->query->from($this->related->getTable() . ' as ' . $hash = $this->getRelationCountHash());
        $this->related->setTable($hash);

        // Apply joins
        $this->initJoins();

        return $this->query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(),
            '=',
            $this->finalKey
        );
    }

    /**
     * Get a relationship join table hash.
     *
     * @return string
     */
    public function getRelationCountHash()
    {
        return 'laravel_reserved_' . static::$selfJoinCount++;
    }
}
