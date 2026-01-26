<?php

namespace App\Modules\Claims\Interfaces;

use App\Interfaces\RepositoryInterface;

/**
 * Claim Repository Interface
 */
interface ClaimRepositoryInterface extends RepositoryInterface
{
    /**
     * Get claims by hospital and department
     */
    public function getByHospitalAndDepartment($hospitalId, $departmentId);

    /**
     * Get claims by month
     */
    public function getByMonth($month);

    /**
     * Get claims with filters
     */
    public function getFiltered($filters = []);
}
