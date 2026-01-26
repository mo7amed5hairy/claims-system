<?php

namespace App\Modules\Claims\Services;

use App\Modules\Claims\Repositories\EntityRepository;

/**
 * Entity Service
 * 
 * Handles business logic for claim entities
 */
class EntityService
{
    protected $repo;

    public function __construct(EntityRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Create a new entity
     */
    public function createEntity(array $data)
    {
        return $this->repo->create($data);
    }

    /**
     * Update an entity
     */
    public function updateEntity($entityId, array $data)
    {
        return $this->repo->update($entityId, $data);
    }

    /**
     * Delete an entity
     */
    public function deleteEntity($entityId)
    {
        return $this->repo->delete($entityId);
    }

    /**
     * Get entities by type
     */
    public function getEntitiesByType($type)
    {
        return $this->repo->getByType($type);
    }

    /**
     * Get all entities
     */
    public function getAllEntities()
    {
        return $this->repo->all();
    }
}
