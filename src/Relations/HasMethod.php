<?php

namespace thybag\BonusLaravelRelations\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use thybag\BonusLaravelRelations\Models\InertModel;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Allows a method to be returned as if it was a relation.
 * Supports both callbacks & public functions.
 */
class HasMethod extends Relation
{
    protected $method;
    protected $parent;

    // Should this result be returned as a collection? Will default to inert model.
    protected $returnCollection;

    /**
     * @param Model $parent
     * @param string|callback - method to run
     * @param boolean - Should result be a collection?
     */
    public function __construct(Model $parent, $method, $collection = false)
    {
        $this->parent = $parent;
        $this->method = $method;
        $this->returnCollection = $collection;
    }

    /**
     * Get basic model instance to return as result.
     *
     * @return Model
     */
    protected function getInertModelInstance()
    {
        // @todo allow custom model via config
        return new InertModel();
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @param  array $models
     * @param  Model $relation
     * @return array Models
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
        return $models;
    }

    /**
     * Handle running hasMethod calls on model collection (for eager loading)
     *
     * @param  array $models - Models to eager load on to
     * @param  Collection $results - empty as no querying needed when just calling a method
     * @param  string $relation - Name of relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        foreach ($models as $model) {
            // Resolve the call & Set it
            $result = $this->resolveMethod($model);
            $model->setRelation($relation, $result);
        }

        return $models;
    }

    /**
     * Handle hasMethod call for single model.
     *
     * @return Model|Collection
     */
    public function getResults()
    {
        return $this->resolveMethod($this->parent);
    }

    /**
     * Figure out how to actually run the relations method.
     *
     * @param  Model model
     * @return Model|Collection $result
     */
    public function resolveMethod(Model $model)
    {
        // Is it a callback or public method
        if (is_callable($this->method)) {
            $results = call_user_func($this->method, $model);
        } else {
            $results = $model->{$this->method}();
        }

        if (empty($results)) {
            return null;
        } elseif ($this->returnCollection) {
            // Are we forcing to collection?
            if (is_array($results)) {
                // Map it in to one if its an array
                $results = collect($results);
            }
            return $results;
        } elseif (is_a($results, 'Illuminate\Database\Eloquent\Collection') || is_a($results, 'Illuminate\Database\Eloquent\Model')) {
            // Is it already a real eloquent model or collection? return as is if so.
            return $results;
        } elseif (is_a($results, 'Illuminate\Support\Collection')) {
            // Standard collection - make it a model.
            $result = $this->getInertModelInstance();
            $result->forceFill($results->toArray());
            return $result;
        } elseif (is_array($results) || is_object($results)) {
            // Object or array - model again
            $result = $this->getInertModelInstance();
            $result->forceFill((array) $results);
            return $result;
        }

        // Unsure how to deal with this?
        return null;
    }

    // Ignored (required by abstract)
    public function addConstraints()
    {
    }
    public function addEagerConstraints(array $models)
    {
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
         return $dictionary;
    }

    public function getEager()
    {
        return new Collection();
    }
}
