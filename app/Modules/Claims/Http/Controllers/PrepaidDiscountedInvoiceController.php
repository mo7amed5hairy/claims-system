<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\PrepaidDiscountedInvoice as DiscountedInvoice;

class PrepaidDiscountedInvoiceController extends Controller
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

        return view('claims::prepaid-discounted-invoices.index', compact('discountedInvoices'));
    }
}
