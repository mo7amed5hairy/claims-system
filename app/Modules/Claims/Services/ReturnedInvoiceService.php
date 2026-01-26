<?php

namespace App\Modules\Claims\Services;

use App\Modules\Claims\Repositories\ReturnedInvoiceRepository;
use App\Modules\Claims\Models\Claim;

/**
 * Returned Invoice Service
 * 
 * Handles business logic for returned invoices
 */
class ReturnedInvoiceService
{
    protected $returnedInvoiceRepository;

    public function __construct(ReturnedInvoiceRepository $returnedInvoiceRepository)
    {
        $this->returnedInvoiceRepository = $returnedInvoiceRepository;
    }

    /**
     * Create a new returned invoice
     */
    public function createReturnedInvoice(array $data)
    {
        // Create the returned invoice
        $returnedInvoice = $this->returnedInvoiceRepository->create($data);

        // Update the original claim
        $this->updateClaimAfterReturn($data['electronic_invoice_no'], $data['returned_invoice_count'], $data['final_amount']);

        return $returnedInvoice;
    }

    /**
     * Update a returned invoice
     */
    public function updateReturnedInvoice($invoiceId, array $data)
    {
        // Get the old returned invoice data
        $oldReturnedInvoice = $this->returnedInvoiceRepository->find($invoiceId);

        // Update the returned invoice
        $updatedInvoice = $this->returnedInvoiceRepository->update($invoiceId, $data);

        // Reverse the old deduction
        $this->reverseClaimDeduction(
            $oldReturnedInvoice->electronic_invoice_no,
            $oldReturnedInvoice->returned_invoice_count,
            $oldReturnedInvoice->final_amount
        );

        // Apply the new deduction
        $this->updateClaimAfterReturn(
            $data['electronic_invoice_no'],
            $data['returned_invoice_count'],
            $data['final_amount']
        );

        return $updatedInvoice;
    }

    /**
     * Delete a returned invoice
     */
    public function deleteReturnedInvoice($invoiceId)
    {
        // Get the returned invoice data before deletion
        $returnedInvoice = $this->returnedInvoiceRepository->find($invoiceId);

        // Delete the returned invoice
        $result = $this->returnedInvoiceRepository->delete($invoiceId);

        // Reverse the deduction from the claim
        $this->reverseClaimDeduction(
            $returnedInvoice->electronic_invoice_no,
            $returnedInvoice->returned_invoice_count,
            $returnedInvoice->final_amount
        );

        return $result;
    }

    /**
     * Update claim after creating/updating a return
     * Deduct the returned count and amount from the claim
     */
    protected function updateClaimAfterReturn($electronicInvoiceNo, $returnedCount, $returnedAmount)
    {
        $claim = Claim::where('electronic_invoice_no', $electronicInvoiceNo)->first();

        if ($claim) {
            // Deduct the returned invoice count
            $claim->invoice_count = max(0, $claim->invoice_count - $returnedCount);

            // Deduct the returned amount from reviewed_value
            $claim->reviewed_value = max(0, $claim->reviewed_value - $returnedAmount);

            // Recalculate the difference
            $claim->difference = $claim->reviewed_value - $claim->claim_value;

            $claim->save();
        }
    }

    /**
     * Reverse the deduction when updating or deleting a return
     * Add back the returned count and amount to the claim
     */
    protected function reverseClaimDeduction($electronicInvoiceNo, $returnedCount, $returnedAmount)
    {
        $claim = Claim::where('electronic_invoice_no', $electronicInvoiceNo)->first();

        if ($claim) {
            // Add back the returned invoice count
            $claim->invoice_count = $claim->invoice_count + $returnedCount;

            // Add back the returned amount to reviewed_value
            $claim->reviewed_value = $claim->reviewed_value + $returnedAmount;

            // Recalculate the difference
            $claim->difference = $claim->reviewed_value - $claim->claim_value;

            $claim->save();
        }
    }

    /**
     * Get returned invoices with filters
     */
    public function getFilteredReturnedInvoices($filters = [])
    {
        return $this->returnedInvoiceRepository->getFiltered($filters);
    }

    /**
     * Get returned invoices by hospital and department
     */
    public function getReturnedInvoicesByHospitalAndDepartment($hospitalId, $departmentId)
    {
        return $this->returnedInvoiceRepository->getByHospitalAndDepartment($hospitalId, $departmentId);
    }
}
