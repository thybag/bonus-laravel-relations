<?php
namespace thybag\BonusLaravelRelations\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Include all bonus relation types
 */
trait BonusRelationsTrait
{
    use HasMethodRelationTrait;
    use BelongsToOneRelationTrait;
    use HasAggregateRelationTrait;
    use BelongsToMorphRelationTrait;
    use HasManyViaManyRelationTrait;
}
