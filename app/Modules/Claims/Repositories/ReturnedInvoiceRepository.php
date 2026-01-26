<?php

namespace App\Modules\Claims\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Claims\Models\ReturnedInvoice;

/**
 * Returned Invoice Repository
 * 
 * Handles all database operations for returned invoices
 */
class ReturnedInvoiceRepository extends BaseRepository
{
    public function __construct(ReturnedInvoice $model)
    {
        parent::__construct($model);
    }

    /**
     * Get returned invoices by hospital and department
     */
    public function getByHospitalAndDepartment($hospitalId, $departmentId)
    {
        return $this->model
            ->where('hospital_id', $hospitalId)
            ->where('department_id', $departmentId)
            ->get();
    }

    /**
     * Get returned invoices with filters
     */
    public function getFiltered($filters = [])
    {
        $query = $this->model;

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
