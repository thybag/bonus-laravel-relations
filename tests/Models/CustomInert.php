<?php
namespace thybag\BonusLaravelRelations\Test\Models;

use thybag\BonusLaravelRelations\Models\InertModel;

/**
 * Test mode to checkl replacement of inertModel in relationships
 */
class CustomInert extends InertModel
{
    public function hello()
    {
        return "hi";
    }
}
