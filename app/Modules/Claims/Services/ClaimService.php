<?php

namespace App\Modules\Claims\Services;

use App\Modules\Claims\Repositories\ClaimRepository;

/**
 * Claim Service
 * 
 * Handles business logic for claims
 */
class ClaimService
{
    protected $claimRepository;

    public function __construct(ClaimRepository $claimRepository)
    {
        $this->claimRepository = $claimRepository;
    }

    /**
     * Create a new claim
     */
    public function createClaim(array $data)
    {
        // Calculate difference if reviewed_value is provided
        if (!empty($data['reviewed_value'])) {
            $data['difference'] = $data['reviewed_value'] - $data['claim_value'];
        }
        
        return $this->claimRepository->create($data);
    }

    /**
     * Update an existing claim
     */
    public function updateClaim($claimId, array $data)
    {
        // Calculate difference if reviewed_value is provided
        if (!empty($data['reviewed_value'])) {
            $data['difference'] = $data['reviewed_value'] - $data['claim_value'];
        }
        
        return $this->claimRepository->update($claimId, $data);
    }

    /**
     * Delete a claim
     */
    public function deleteClaim($claimId)
    {
        return $this->claimRepository->delete($claimId);
    }

    /**
     * Get claims with filters
     */
    public function getFilteredClaims($filters = [])
    {
        return $this->claimRepository->getFiltered($filters);
    }

    /**
     * Get claims by hospital and department
     */
    public function getClaimsByHospitalAndDepartment($hospitalId, $departmentId)
    {
        return $this->claimRepository->getByHospitalAndDepartment($hospitalId, $departmentId);
    }
}
