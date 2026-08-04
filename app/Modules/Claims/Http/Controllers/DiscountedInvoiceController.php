<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\DiscountedInvoice;
use Illuminate\Http\Request;

class DiscountedInvoiceController extends Controller
{
    /**
     * Display a listing of discounted invoices (read-only, auto-generated from payment orders)
     */
    public function index()
    {
        // Only reviewers or financial users can access
        $user = auth()->user();
        if (!$user->isReviewer() && !$user->isFinancial()) {
            abort(403, 'Unauthorized access');
        }

        $discountedInvoices = DiscountedInvoice::with(['claim', 'user', 'paymentOrder'])
            ->orderBy('id', 'desc')
            ->get();

        return view('claims::discounted-invoices.index', compact('discountedInvoices'));
    }

    /**
     * Update the specified discounted invoice.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isReviewer() && !$user->isFinancial()) {
            abort(403, 'Unauthorized access');
        }

        $invoice = DiscountedInvoice::findOrFail($id);

        $data = $request->validate([
            'original_invoice_count' => 'nullable|integer|min:0',
            'discounted_invoice_count' => 'nullable|integer|min:0',
            'original_amount' => 'nullable|numeric|min:0',
            'discounted_amount' => 'nullable|numeric|min:0',
            'deduction_amount' => 'nullable|numeric|min:0',
            'taxes_amount' => 'nullable|numeric|min:0',
            'unpaid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['user_id'] = $user->id;
        $invoice->update($data);

        return redirect()->route('discounted-invoices.index')
            ->with('success', 'تم تحديث الفاتورة بنجاح');
    }
}
