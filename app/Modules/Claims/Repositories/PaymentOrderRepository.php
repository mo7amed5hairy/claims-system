<?php

namespace App\Modules\Claims\Repositories;

use App\Repositories\BaseRepository;
use App\Modules\Claims\Models\PaymentOrder;

/**
 * Payment Order Repository
 * 
 * Handles all database operations for payment orders
 */
class PaymentOrderRepository extends BaseRepository
{
    public function __construct(PaymentOrder $model)
    {
        parent::__construct($model);
    }

    /**
     * Get payment orders by hospital and department
     */
    public function getByHospitalAndDepartment($hospitalId, $departmentId)
    {
        return $this->model
            ->where('hospital_id', $hospitalId)
            ->where('department_id', $departmentId)
            ->get();
    }

    /**
     * Get payment orders with filters
     */
    public function getFiltered($filters = [])
    {
        $query = $this->model;

        if (!empty($filters['payer_entity_id'])) {
            $query = $query->where('payer_entity_id', $filters['payer_entity_id']);
        }

        if (!empty($filters['hospital_id'])) {
            $query = $query->where('hospital_id', $filters['hospital_id']);
        }

        if (!empty($filters['department_id'])) {
            $query = $query->where('department_id', $filters['department_id']);
        }

        return $query->paginate(15);
    }
}
