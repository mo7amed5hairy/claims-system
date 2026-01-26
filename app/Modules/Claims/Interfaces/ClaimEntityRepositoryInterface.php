<?php

namespace App\Modules\Claims\Interfaces;

use App\Interfaces\RepositoryInterface;

/**
 * ClaimEntity Repository Interface
 */
interface ClaimEntityRepositoryInterface extends RepositoryInterface
{
    /**
     * Get entities by type
     */
    public function getByType($type);
}
