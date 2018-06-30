<?php
namespace thybag\BonusLaravelRelations\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * InertModel for use when dealing with none standard objects (aggregates)
 */
class InertModel extends Model
{
    protected $fillable = ['*'];
    public $incrementing = false;

    // Disable save for inert models
    public function save(array $options = [])
    {
        return false;
    }
}
