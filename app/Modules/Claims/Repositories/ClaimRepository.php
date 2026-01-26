<?php

namespace App\Modules\Claims\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Interfaces\ClaimRepositoryInterface;

/**
 * Claim Repository
 * 
 * Handles all database operations for claims
 */
class ClaimRepository extends BaseRepository implements ClaimRepositoryInterface
{
    public function __construct(Claim $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Get claims by hospital and department
     */
    public function getByHospitalAndDepartment($hospitalId, $departmentId)
    {
        return $this->model
            ->where('hospital_id', $hospitalId)
            ->where('department_id', $departmentId)
            ->get();
    }

    /**
     * Get claims by month
     */
    public function getByMonth($month)
    {
        return $this->model->where('month', $month)->get();
    }

    /**
     * Get claims with filters
     */
    public function getFiltered($filters = [])
    {
        $query = $this->model;

        if (!empty($filters['month'])) {
            $query = $query->where('month', $filters['month']);
        }

        if (!empty($filters['hospital_id'])) {
            $query = $query->where('hospital_id', $filters['hospital_id']);
        }

        if (!empty($filters['department_id'])) {
            $query = $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['entity_id'])) {
            $query = $query->where('entity_id', $filters['entity_id']);
        }

        return $query->paginate(15);
    }
}
