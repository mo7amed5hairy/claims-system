<?php

namespace App\Modules\Claims\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Interfaces\ClaimEntityRepositoryInterface;

/**
 * Entity Repository
 * 
 * Handles all database operations for claim entities
 */
class EntityRepository extends BaseRepository implements ClaimEntityRepositoryInterface
{
    public function __construct(ClaimEntity $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Get entities by type
     */
    public function getByType($type)
    {
        return $this->model->where('type', $type)->get();
    }
}
