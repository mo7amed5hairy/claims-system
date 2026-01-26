<?php

namespace App\Modules\Claims\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Interfaces\HospitalRepositoryInterface;

/**
 * Hospital Repository
 * 
 * Handles all database operations for hospitals
 */
class HospitalRepository extends BaseRepository implements HospitalRepositoryInterface
{
    public function __construct(Hospital $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Get hospital with its departments
     */
    public function getWithDepartments($hospitalId)
    {
        return $this->model->with('departments')->find($hospitalId);
    }
    
    /**
     * Get all hospitals with departments
     */
    public function allWithDepartments()
    {
        return $this->model->with('departments')->get();
    }
}
