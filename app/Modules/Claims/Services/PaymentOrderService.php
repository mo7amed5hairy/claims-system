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

        // Update claim status to paid
        if (!empty($data['electronic_invoice_no'])) {
            $this->updateClaimStatus($data['electronic_invoice_no'], 'paid');
        }

        return $paymentOrder;
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
            if ($oldOrder->electronic_invoice_no !== $data['electronic_invoice_no']) {
                if (!empty($oldOrder->electronic_invoice_no)) {
                    $this->updateClaimStatus($oldOrder->electronic_invoice_no, 'unpaid');
                }
            }
        }

        if (!empty($data['electronic_invoice_no'])) {
            $this->updateClaimStatus($data['electronic_invoice_no'], 'paid');
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

        // Revert claim status to unpaid
        if ($order && !empty($order->electronic_invoice_no)) {
            $this->updateClaimStatus($order->electronic_invoice_no, 'unpaid');
        }

        return $result;
    }

    /**
     * Helper to update claim status
     */
    protected function updateClaimStatus($invoiceNo, $status)
    {
        $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $invoiceNo)->first();

        if ($claim) {
            // Calculate total payments for this claim
            $totalPaid = \App\Modules\Claims\Models\PaymentOrder::where('electronic_invoice_no', $invoiceNo)->sum('amount');

            // Determine status based on total payments vs reviewed value
            // Allow a small margin of error for float comparisons (0.1)
            $newStatus = ($totalPaid >= ($claim->reviewed_value - 0.1)) ? 'paid' : 'unpaid';

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
