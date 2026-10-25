<?php

namespace App\Modules\Claims\Services;

use App\Modules\Claims\Repositories\PaymentOrderRepository;

/**
 * Payment Order Service
 * 
 * Handles business logic for payment orders
 */
class PaymentOrderService
{
    protected $paymentOrderRepository;

    public function __construct(PaymentOrderRepository $paymentOrderRepository)
    {
        $this->paymentOrderRepository = $paymentOrderRepository;
    }

    /**
     * Create a new payment order
     */
    public function createPaymentOrder(array $data)
    {
        $paymentOrder = $this->paymentOrderRepository->create($data);

        // Update claim status based on identifiers available
        if (!empty($data['claim_number'])) {
            $this->updateClaimStatusByClaimNumber($data['claim_number'], $data['is_prepaid'] ?? 0);
        } elseif (!empty($data['electronic_invoice_no'])) {
            $this->updateClaimStatus($data['electronic_invoice_no'], $data['is_prepaid'] ?? 0);
        }

        return $paymentOrder;
    }

    /**
     * Find all claims matching an electronic invoice number (table-wide).
     */
    public function findInvoiceClaims($invoiceNo, $isPrepaid = 0)
    {
        return \App\Modules\Claims\Models\Claim::withoutGlobalScopes()
            ->where('is_prepaid', $isPrepaid)
            ->where('electronic_invoice_no', $invoiceNo)
            ->get();
    }

    /**
     * Resolve the target claim for a payment order.
     *
     * Returns [Claim|null, bool $ambiguous]. $ambiguous is true only when the
     * electronic invoice is shared by more than one claim and no claim number
     * was given to disambiguate (same entity reusing the invoice).
     */
    public function resolveClaimForPayment($invoiceNo = null, $claimNumber = null, $isPrepaid = 0)
    {
        if (!empty($claimNumber)) {
            $claim = \App\Modules\Claims\Models\Claim::withoutGlobalScopes()
                ->where('is_prepaid', $isPrepaid)
                ->where('claim_number', $claimNumber)
                ->first();

            if ($claim && !empty($invoiceNo) && $claim->electronic_invoice_no !== $invoiceNo) {
                $claim = null;
            }

            return [$claim, false];
        }

        if (!empty($invoiceNo)) {
            $claims = $this->findInvoiceClaims($invoiceNo, $isPrepaid);
            if ($claims->count() === 1) {
                return [$claims->first(), false];
            }
            if ($claims->count() > 1) {
                return [null, true];
            }
        }

        return [null, false];
    }

    /**
     * Update a payment order
     */
    public function updatePaymentOrder($orderId, array $data, $model = null)
    {
        if ($model) {
            $oldOrder = $model;
            $model->update($data);
            $paymentOrder = $model->fresh();
        } else {
            $oldOrder = $this->paymentOrderRepository->find($orderId);
            $paymentOrder = $this->paymentOrderRepository->update($orderId, $data);
        }

        if ($oldOrder) {
            // If invoice number changed, revert old claim status and update new one
            if ($oldOrder->electronic_invoice_no !== ($data['electronic_invoice_no'] ?? null)) {
                if (!empty($oldOrder->electronic_invoice_no)) {
                    $this->updateClaimStatus($oldOrder->electronic_invoice_no);
                }
                if (!empty($oldOrder->claim_number)) {
                    $this->updateClaimStatusByClaimNumber($oldOrder->claim_number);
                }
            }
            if ($oldOrder->claim_number !== ($data['claim_number'] ?? null)) {
                if (!empty($oldOrder->claim_number)) {
                    $this->updateClaimStatusByClaimNumber($oldOrder->claim_number);
                }
            }
        }

        if (!empty($data['electronic_invoice_no'])) {
            $this->updateClaimStatus($data['electronic_invoice_no']);
        }
        if (!empty($data['claim_number'])) {
            $this->updateClaimStatusByClaimNumber($data['claim_number']);
        }

        return $paymentOrder;
    }

    /**
     * Delete a payment order
     */
    public function deletePaymentOrder($orderId, $model = null)
    {
        if ($model) {
            $order = $model;
            $model->delete();
            $result = true;
        } else {
            $order = $this->paymentOrderRepository->find($orderId);
            $result = $this->paymentOrderRepository->delete($orderId);
        }

        // Revert claim status when payment order is deleted
        if ($order) {
            if (!empty($order->electronic_invoice_no)) {
                $this->updateClaimStatus($order->electronic_invoice_no);
            }
            if (!empty($order->claim_number)) {
                $this->updateClaimStatusByClaimNumber($order->claim_number);
            }
        }

        return $result;
    }

    /**
     * Helper to update claim status
     *
     * Invoice-based status is only applied when the invoice maps to exactly one
     * claim. When the same invoice is shared by several claims (same entity,
     * different claim numbers), status must be driven by claim number instead.
     */
    protected function updateClaimStatus($invoiceNo, $isPrepaid = 0)
    {
        $claims = \App\Modules\Claims\Models\Claim::withoutGlobalScopes()
            ->where('electronic_invoice_no', $invoiceNo)
            ->get();

        if ($claims->count() !== 1) {
            return;
        }

        $claim = $claims->first();

        // Calculate total payments using the claim identifiers to avoid missing any
        $totalPaid = \App\Modules\Claims\Models\PaymentOrder::withoutGlobalScopes()
            ->where(function ($query) use ($invoiceNo, $claim) {
                $query->where('electronic_invoice_no', $invoiceNo);
                if (!empty($claim->claim_number)) {
                    $query->orWhere('claim_number', $claim->claim_number);
                }
            })
            ->sum('amount');

        $newStatus = ($totalPaid >= ($claim->claim_value - 0.1)) ? 'paid' : 'unpaid';
        $claim->update(['status' => $newStatus]);
    }

    /**
     * Helper to update claim status by claim number
     *
     * Payments are summed by claim number. When the claim's invoice is unique, it
     * also counts invoices recorded without a claim number. When the invoice is
     * shared by another claim, only the claim number is trusted.
     */
    protected function updateClaimStatusByClaimNumber($claimNumber, $isPrepaid = 0)
    {
        $claim = \App\Modules\Claims\Models\Claim::withoutGlobalScopes()
            ->where('claim_number', $claimNumber)
            ->first();

        if ($claim) {
            $totalPaid = \App\Modules\Claims\Models\PaymentOrder::withoutGlobalScopes()
                ->where(function ($query) use ($claimNumber, $claim) {
                    $query->where('claim_number', $claimNumber);
                    if (!empty($claim->electronic_invoice_no)) {
                        $duplicateCount = \App\Modules\Claims\Models\Claim::withoutGlobalScopes()
                            ->where('electronic_invoice_no', $claim->electronic_invoice_no)
                            ->count();
                        if ($duplicateCount <= 1) {
                            $query->orWhere('electronic_invoice_no', $claim->electronic_invoice_no);
                        }
                    }
                })
                ->sum('amount');

            $newStatus = ($totalPaid >= ($claim->claim_value - 0.1)) ? 'paid' : 'unpaid';
            $claim->update(['status' => $newStatus]);
        }
    }

    /**
     * Get payment orders with filters
     */
    public function getFilteredPaymentOrders($filters = [])
    {
        return $this->paymentOrderRepository->getFiltered($filters);
    }

    /**
     * Get payment orders by hospital and department
     */
    public function getPaymentOrdersByHospitalAndDepartment($hospitalId, $departmentId)
    {
        return $this->paymentOrderRepository->getByHospitalAndDepartment($hospitalId, $departmentId);
    }
}
