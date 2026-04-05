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
        
        // Check if user can access payments (financial users or admins only)
        if (!auth()->user()->canAccessPayments()) {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Display list of payment orders
     */
    public function index()
    {
        $this->authorize('viewAny', PaymentOrder::class);
        $orders = PaymentOrder::with(['payerEntity', 'payeeHospital', 'department', 'user'])->orderBy('id', 'desc')->get();

        return view('claims::payments.index', compact('orders'));
    }

    /**
     * Show create payment order form
     */
    public function create()
    {
        $this->authorize('create', PaymentOrder::class);
        
        // Get all session data
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');
        $waitingListType = session('flow_waiting_list_type');
        $flowOptions = session('flow_options', []);
        $entityType = $flowOptions['entity_type'] ?? null;
        
        // DEBUG: Log all session data
        \Log::info('=== PAYMENT CREATE SESSION DATA ===');
        \Log::info('flow_hospital_id: ' . ($hospitalId ?? 'NULL'));
        \Log::info('flow_department_id: ' . ($deptId ?? 'NULL'));
        \Log::info('flow_waiting_list_type: ' . ($waitingListType ?? 'NULL'));
        \Log::info('flow_entity_type: ' . ($entityType ?? 'NULL'));
        \Log::info('flow_options: ' . json_encode($flowOptions));
        
        // Initialize variables
        $hospital = null;
        $department = null;
        $selectedEntityId = null;
        $branch = null;
        $location = null;
        $law = null; // For waiting lists
        
        // Handle Hospital & Department (common for all flows)
        if ($hospitalId) {
            if (is_numeric($hospitalId)) {
                $hospital = Hospital::find($hospitalId);
            } else {
                // String value from waiting list
                $hospital = (object)['id' => $hospitalId, 'name' => $this->getHospitalName($hospitalId)];
            }
        }
        
        if ($deptId) {
            if (is_numeric($deptId)) {
                $department = Department::find($deptId);
            } else {
                // String value from waiting list
                $department = (object)['id' => $deptId, 'name' => $deptId];
            }
        }
        
        // Handle different flow types
        if ($waitingListType) {
            // Waiting Lists Flow (insurance or ministry)
            if ($waitingListType === 'insurance') {
                $selectedEntity = ClaimEntity::where('name', 'الهيئة العامة للتأمين الصحي')->first();
                $selectedEntityId = $selectedEntity ? $selectedEntity->id : null;
            } elseif ($waitingListType === 'ministry') {
                $selectedEntity = ClaimEntity::where('name', 'وزارة الصحة والسكان')->first();
                $selectedEntityId = $selectedEntity ? $selectedEntity->id : null;
            }
            // For waiting lists, get law from session directly (flow_law)
            $law = session('flow_law') ?? null;
            \Log::info('Waiting List Flow - Entity ID: ' . ($selectedEntityId ?? 'NULL') . ', Law: ' . ($law ?? 'NULL'));
            
        } elseif ($entityType) {
            // Regular flows (contracts, insurance, ministry, comprehensive)
            $selectedEntityId = $flowOptions['entity_id'] ?? null;
            $branch = $flowOptions['branch'] ?? null;
            $location = $flowOptions['location'] ?? null;
            \Log::info('Regular Flow - Entity ID: ' . ($selectedEntityId ?? 'NULL') . ', Branch: ' . ($branch ?? 'NULL') . ', Location: ' . ($location ?? 'NULL'));
        }
        
        // DEBUG: Log final values being passed to view
        \Log::info('=== PASSING TO VIEW ===');
        \Log::info('selectedEntityId: ' . ($selectedEntityId ?? 'NULL'));
        \Log::info('branch: ' . ($branch ?? 'NULL'));
        \Log::info('location: ' . ($location ?? 'NULL'));
        \Log::info('law: ' . ($law ?? 'NULL'));
        
        $entities = ClaimEntity::all();
        $allHospitals = Hospital::with('departments')->get();
        $allDepartments = Department::all();

        return view('claims::payments.create', compact(
            'hospital', 'department', 'entities', 'allHospitals', 'allDepartments', 
            'selectedEntityId', 'branch', 'location', 'law', 'entityType'
        ));
    }

    /**
     * Helper method to get hospital name from key
     */
    private function getHospitalName($key)
    {
        $names = [
            'children' => 'مستشفى الأطفال',
            'women' => 'مستشفى النساء والتوليد',
            'ain_shams' => 'مستشفى عين شمس الباطنة',
            'others' => 'باقى المستشفيات',
        ];
        return $names[$key] ?? $key;
    }

    /**
     * Store new payment order
     */
    public function store(Request $request)
    {
        $this->authorize('create', PaymentOrder::class);
        
        $rules = [
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
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
        ];
        
        // Check if hospital_id is numeric (DB hospital) or string (waiting list)
        $hospitalId = $request->input('payee_hospital_id');
        if ($hospitalId && !is_numeric($hospitalId)) {
            // Waiting list hospital - accept string
            $rules['payee_hospital_id'] = 'required|string|max:255';
            $rules['department_id'] = 'required|string|max:255';
        } else {
            // Regular hospital - must exist in DB
            $rules['payee_hospital_id'] = 'required|exists:hospitals,id';
            $rules['department_id'] = 'required|exists:departments,id';
        }
        
        $data = $request->validate($rules, [
            'electronic_invoice_no.exists' => 'رقم الفاتورة الإلكترونية غير موجود في نظام المطالبات للجهة المختارة.',
        ]);

        $data['invoice_no'] = $data['electronic_invoice_no'];

        // Business Logic Validation: Amount vs Claim Reviewed Value
        $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $request->electronic_invoice_no)->first();

        if ($claim) {
            $maxAmount = (float) $claim->reviewed_value;
            $paymentAmount = (float) $request->amount;

            if ($paymentAmount > $maxAmount) {
                return back()->withErrors([
                    'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المراجعة للمطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                ])->withInput();
            }
        }

        $data['user_id'] = auth()->id();

        $this->paymentOrderService->createPaymentOrder($data);

        return redirect()->route('payments.index')
            ->with('success', trans('messages.payment_order_created_successfully'));
    }

    /**
     * Show edit payment order form
     */
    public function edit(PaymentOrder $payment)
    {
        $this->authorize('update', $payment);
        $entities = ClaimEntity::all();
        $allHospitals = Hospital::with('departments')->get();
        $allDepartments = Department::all();
        // $departments = $payment->payeeHospital ? $payment->payeeHospital->departments : collect(); // loaded via JS data or simple check

        return view('claims::payments.edit', compact('payment', 'entities', 'allHospitals', 'allDepartments'));
    }

    /**
     * Update payment order
     */
    public function update(Request $request, PaymentOrder $payment)
    {
        $this->authorize('update', $payment);
        
        $rules = [
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
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
        ];
        
        // Check if hospital_id is numeric (DB hospital) or string (waiting list)
        $hospitalId = $request->input('payee_hospital_id');
        if ($hospitalId && !is_numeric($hospitalId)) {
            // Waiting list hospital - accept string
            $rules['payee_hospital_id'] = 'required|string|max:255';
            $rules['department_id'] = 'required|string|max:255';
        } else {
            // Regular hospital - must exist in DB
            $rules['payee_hospital_id'] = 'required|exists:hospitals,id';
            $rules['department_id'] = 'required|exists:departments,id';
        }
        
        $data = $request->validate($rules, [
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
                $maxAmount = (float) $claim->reviewed_value;
                $paymentAmount = (float) $request->amount;

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
        $this->authorize('delete', $payment);
        $this->paymentOrderService->deletePaymentOrder($payment->id);

        return redirect()->route('payments.index')
            ->with('success', trans('messages.payment_order_deleted_successfully'));
    }
}
