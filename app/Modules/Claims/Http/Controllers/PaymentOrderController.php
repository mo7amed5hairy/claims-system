<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\PaymentOrder;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Models\Department;
use App\Modules\Claims\Services\PaymentOrderService;
use Illuminate\Http\Request;

/**
 * Payment Order Controller
 * 
 * Handles payment orders operations
 */
class PaymentOrderController extends Controller
{
    protected $paymentOrderService;

    public function __construct(PaymentOrderService $paymentOrderService)
    {
        $this->paymentOrderService = $paymentOrderService;
    }

    /**
     * Display list of payment orders
     */
    public function index()
    {
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');

        $orders = PaymentOrder::with(['payerEntity', 'payeeHospital'])->get();

        return view('claims::payments.index', compact('orders'));
    }

    /**
     * Show create payment order form
     */
    public function create()
    {
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');

        if (!$hospitalId || !$deptId) {
            return redirect()->route('flow.hospital')
                ->with('error', trans('messages.select_hospital_first'));
        }

        $hospital = Hospital::find($hospitalId);
        $department = Department::find($deptId);
        $entities = ClaimEntity::all();

        return view('claims::payments.create', compact('hospital', 'department', 'entities'));
    }

    /**
     * Store new payment order
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'account_type' => 'required|in:بنكى,أمر دفع برقم مؤسسى,شيك نقدى',
            'gp_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'payer_entity_id' => 'required|exists:claim_entities,id',
            'electronic_invoice_no' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::exists('claims', 'electronic_invoice_no')->where(function ($query) use ($request) {
                    return $query->where('entity_id', $request->payer_entity_id);
                }),
            ],
            'notes' => 'nullable|string',
        ], [
            'electronic_invoice_no.exists' => 'رقم الفاتورة الإلكترونية غير موجود في نظام المطالبات للجهة المختارة.',
        ]);

        $data['payee_hospital_id'] = session('flow_hospital_id');
        $data['invoice_no'] = $data['electronic_invoice_no'];

        if (!$data['payee_hospital_id']) {
            return back()->with('error', 'يجب تحديد المستشفى أولاً');
        }

        // Business Logic Validation: Amount vs Claim Reviewed Value
        $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $request->electronic_invoice_no)->first();

        if ($claim) {
            $maxAmount = (float)$claim->reviewed_value;
            $paymentAmount = (float)$request->amount;

            if ($paymentAmount > $maxAmount) {
                return back()->withErrors([
                    'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المراجعة للمطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                ])->withInput();
            }
        }

        $this->paymentOrderService->createPaymentOrder($data);

        return redirect()->route('payments.index')
            ->with('success', trans('messages.payment_order_created_successfully'));
    }

    /**
     * Show edit payment order form
     */
    public function edit(PaymentOrder $payment)
    {
        $entities = ClaimEntity::all();
        return view('claims::payments.edit', compact('payment', 'entities'));
    }

    /**
     * Update payment order
     */
    public function update(Request $request, PaymentOrder $payment)
    {
        $data = $request->validate([
            'account_type' => 'required|in:بنكى,أمر دفع برقم مؤسسى,شيك نقدى',
            'gp_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'payer_entity_id' => 'required|exists:claim_entities,id',
            'electronic_invoice_no' => [
                'nullable',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::exists('claims', 'electronic_invoice_no')->where(function ($query) use ($request) {
                    return $query->where('entity_id', $request->payer_entity_id);
                }),
            ],
            'notes' => 'nullable|string',
        ], [
            'electronic_invoice_no.exists' => 'رقم الفاتورة الإلكترونية غير موجود في نظام المطالبات للجهة المختارة.',
        ]);

        // Update invoice_no if electronic_invoice_no is present
        if (!empty($data['electronic_invoice_no'])) {
            $data['invoice_no'] = $data['electronic_invoice_no'];
        }

        // Add Flow Options (Branch, Location, Beneficiary) if Entity Matches
        $flowOptions = session('flow_options', []);
        if (isset($flowOptions['entity_id']) && $data['payer_entity_id'] == $flowOptions['entity_id']) {
            $data['branch'] = $flowOptions['branch'] ?? null;
            $data['location'] = $flowOptions['location'] ?? null;
            $data['beneficiary'] = $flowOptions['law'] ?? null;
        }

        // Business Logic Validation: Amount vs Claim Reviewed Value
        if (!empty($request->electronic_invoice_no)) {
            $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $request->electronic_invoice_no)->first();

            if ($claim) {
                $maxAmount = (float)$claim->reviewed_value;
                $paymentAmount = (float)$request->amount;

                if ($paymentAmount > $maxAmount) {
                    return back()->withErrors([
                        'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المراجعة للمطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                    ])->withInput();
                }
            }
        }

        $this->paymentOrderService->updatePaymentOrder($payment->id, $data);

        return redirect()->route('payments.index')
            ->with('success', trans('messages.payment_order_updated_successfully'));
    }

    /**
     * Delete payment order
     */
    public function destroy(PaymentOrder $payment)
    {
        $this->paymentOrderService->deletePaymentOrder($payment->id);

        return redirect()->route('payments.index')
            ->with('success', trans('messages.payment_order_deleted_successfully'));
    }
}
