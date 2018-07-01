<?php
namespace thybag\BonusLaravelRelations\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToOne extends BelongsToMany
{
    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param array $models
     * @param Collection $results
     * @param string $relation
     * @return array
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
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  Collection  $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        // Overides build direction to no longer create an array of results
        $foreign = $this->foreignPivotKey;
        $dictionary = [];
        foreach ($results as $result) {
            // Add first result to dict only - don't override with later ones if
            // we ended up with multiple
            $key = $result->pivot->$foreign;
            if (!isset($dictionary[$key])) {
                $dictionary[$key] = $result;
            }
        }
        return $dictionary;
    }

    /**
     * Get the results of the relationship.
     *
     * @return Model|null
     */
    public function getResults()
    {
        return $this->first();
    }

    /**
     * Init relations to null
     *
     * @return array Models
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
        return $models;
    }
}
