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

        // Fetch regular claims with their relationships
        $claims = \App\Modules\Claims\Models\Claim::with(['payments', 'hospital', 'department', 'entity'])
            ->orderBy('id', 'desc')
            ->get();

        $monthsAr = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

        $paymentOrders = [];
        foreach ($claims as $claim) {
            $cYear = $claim->claim_date?->year ?? date('Y');
            $cMonth = $claim->month;

            if ($claim->payments->isEmpty()) {
                $paymentOrders[] = (object) [
                    'id' => $claim->id,
                    'is_payment' => false,
                    'claim' => $claim,
                    'payeeHospital' => $claim->hospital ? (object) ['name' => $claim->hospital->name] : (object) ['name' => '-'],
                    'department' => $claim->department ? (object) ['name' => $claim->department->name] : (object) ['name' => '-'],
                    'entity' => $claim->entity ? (object) ['name' => $claim->entity->name] : (object) ['name' => '-'],
                    'claim_number' => $claim->claim_number,
                    'amount_after_review' => $claim->reviewed_value ?? 0,
                    'amount' => 0,
                    'electronic_invoice_no' => $claim->electronic_invoice_no ?? '-',
                    'gp_number' => null,
                    'invoice_no' => null,
                    'due_date' => null,
                    'created_at' => $claim->created_at,
                    'invoice_count_after_review' => $claim->reviewed_value > 0 ? $claim->invoice_count : 0,
                    'rendered_year' => $cYear,
                    'rendered_month' => $cMonth . ' ' . $cYear,
                ];
            } else {
                foreach ($claim->payments as $payment) {
                    $y = $payment->due_date?->year ?? $payment->created_at?->year ?? $cYear;
                    $mn = $payment->due_date?->month ?? $payment->created_at?->month ?? 0;
                    $paymentOrders[] = (object) [
                        'id' => $claim->id . '_' . $payment->id,
                        'is_payment' => true,
                        'claim' => $claim,
                        'payeeHospital' => $payment->payeeHospital ?? $claim->hospital ?? (object) ['name' => '-'],
                        'department' => $payment->department ?? $claim->department ?? (object) ['name' => '-'],
                        'entity' => $claim->entity ? (object) ['name' => $claim->entity->name] : (object) ['name' => '-'],
                        'claim_number' => $claim->claim_number,
                        'amount_after_review' => $payment->amount_after_review ?? 0,
                        'amount' => $payment->amount ?? 0,
                        'electronic_invoice_no' => $payment->electronic_invoice_no,
                        'gp_number' => $payment->gp_number,
                        'invoice_no' => $payment->invoice_no,
                        'due_date' => $payment->due_date,
                        'created_at' => $payment->created_at,
                        'invoice_count_after_review' => $payment->invoice_count_after_review ?? 0,
                        'rendered_year' => $y,
                        'rendered_month' => ($mn > 0 ? $monthsAr[$mn] : '-') . ' ' . $y,
                    ];
                }
            }
        }

        // All hospitals from DB
        $allHospitals = Hospital::orderBy('name')->pluck('name');

        // All Contracting Entities
        $allEntities = \App\Modules\Claims\Models\ClaimEntity::orderBy('name')->pluck('name');

        // Add virtual entities for reporting
        $allEntities->push('قوائم الانتظار');

        // Sub-departments of others hospital ("باقي المستشفيات" or "باقى المستشفيات")
        $othersHospital = Hospital::where('name', 'like', '%باق%')->first();
        $othersDepartments = $othersHospital ? $othersHospital->departments()->orderBy('name')->pluck('name') : collect();

        // Entity sub-filters for report (governorates for insurance entities, types for waiting lists)
        $entitySubFilters = [
            'الهيئة العامة للتأمين الصحي' => ['القاهرة', 'الجيزة', 'رئاسة الهيئة', 'القليوبية', 'مدينة نصر'],
            'الهيئة العامة للتأمين الصحي الشامل' => ['السويس', 'اسماعيلية', 'الأقصر', 'أسوان', 'جنوب سيناء', 'بورسعيد'],
            'قوائم الانتظار' => ['قوائم انتظار التأمين الصحي', 'قوائم انتظار مديرية الشئون الصحية'],
        ];

        $allMonths = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

        return view('claims::reports.index', compact('paymentOrders', 'allHospitals', 'allMonths', 'allEntities', 'othersDepartments', 'entitySubFilters'));
    }

}