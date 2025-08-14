<?php

namespace thybag\BonusLaravelRelations\Traits;

use Illuminate\Database\Eloquent\Model;
use thybag\BonusLaravelRelations\Models\InertModel;

trait InertModelTrait
{
    /**
     * Get basic model instance to return as result.
     *
     * @return Model
     */
    protected function getInertModelInstance(string $useModel = null): Model
    {
        // If explict model is provided. Use it
        if (!empty($useModel)) {
            return new $useModel();
        }

        // Otherwise attempt to use inertModel from config, or local one
        // if none is defined
        $model = config('bonus-laravel-relationships.inertModel');
        return !empty($model) ? new $model() : new InertModel();
    }
}
