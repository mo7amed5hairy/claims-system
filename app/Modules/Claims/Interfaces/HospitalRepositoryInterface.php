<?php

namespace App\Modules\Claims\Interfaces;

use App\Interfaces\RepositoryInterface;

/**
 * Hospital Repository Interface
 */
interface HospitalRepositoryInterface extends RepositoryInterface
{
    /**
     * Get hospital with its departments
     */
    public function getWithDepartments($hospitalId);

    /**
     * Get all hospitals with departments
     */
    public function allWithDepartments();
}
