<?php

namespace thybag\BonusLaravelRelations\Traits;

use thybag\BonusLaravelRelations\Models\InertModel;

trait InertModelTrait
{
    /**
     * Get basic model instance to return as result.
     *
     * @return Model
     */
    protected function getInertModelInstance()
    {
        // Attempt to use provided inertModel if one is set.
        $model = config('bonus-laravel-relationships.inertModel');
        return !empty($model) ? new $model() : new InertModel();
    }
}
