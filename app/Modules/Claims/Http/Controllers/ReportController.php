<?php
namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\PaymentOrder;
use App\Modules\Claims\Models\Hospital;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->canAccessPayments()) {
            abort(403, 'Unauthorized access');
        }

        $paymentOrders = PaymentOrder::with(['claim', 'payeeHospital', 'department'])
            ->orderBy('id', 'desc')
            ->get();

        // كل المستشفيات من DB بغض النظر عن المطالبات
        $allHospitals = Hospital::orderBy('name')->pluck('name');

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

        return view('claims::reports.index', compact('paymentOrders', 'allHospitals', 'allMonths'));
    }
}