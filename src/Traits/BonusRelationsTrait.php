<?php
namespace thybag\BonusLaravelRelations\Traits;

use App\Core\Models\Relations\BelongsToOne;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Include all bonus relation types
 */
trait BonusRelationsTrait
{
    use HasMethodRelationTrait;
    use HasAggregateRelationTrait;
    use BelongsToMorphRelationTrait;
    use HasManyViaManyRelationTrait;
}
