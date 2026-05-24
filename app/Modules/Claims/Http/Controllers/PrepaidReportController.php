<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\PrepaidPaymentOrder;
use Illuminate\Http\Request;

class PrepaidReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->canAccessPayments()) {
            abort(403, 'Unauthorized access');
        }

        $paymentOrders = PrepaidPaymentOrder::with(['claim', 'payeeHospital', 'department'])
            ->orderBy('id', 'desc')
            ->get();

        // كل المستشفيات من DB بغض النظر عن المطالبات
        $allHospitals = \App\Modules\Claims\Models\Hospital::orderBy('name')->pluck('name');

        // كل شهور السنة ثابتة
        $allMonths = [
            'يناير',
            'فبراير',
            'مارس',
            'أبريل',
            'مايو',
            'يونيو',
            'يوليو',
            'أغسطس',
            'سبتمبر',
            'أكتوبر',
            'نوفمبر',
            'ديسمبر'
        ];

        return view('claims::prepaid-reports.index', compact('paymentOrders', 'allHospitals', 'allMonths'));
    }
}
