<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\PaymentOrder;
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Models\Department;
use App\Modules\Claims\Services\PaymentOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        
        // Check user type for showing claim number search (financial users only)
        $user = auth()->user();
        $isFinancialUser = $user->isFinancial() || ($user->isFinancial() && $user->isReviewer());
        $isReviewerOnly = $user->isReviewer() && !$user->isFinancial();
        $showClaimSearch = $isFinancialUser && !$isReviewerOnly;
        
        // DEBUG: Log session values
        \Log::info('=== SESSION DEBUG ===');
        \Log::info('flow_options: ' . json_encode(session('flow_options', [])));
        \Log::info('flow_entity_type: ' . (session('flow_entity_type') ?? 'NULL'));
        \Log::info('flow_waiting_list_type: ' . (session('flow_waiting_list_type') ?? 'NULL'));
        
        // Determine search mode based on flow type
        // 'electronic' = search by electronic_invoice_no (regular flows: contracts, insurance, comprehensive, ministry)
        // 'claim' = search by claim_number (waiting list - ministry)
        // 'both' = show both fields (waiting list - insurance)
        // 'dynamic' = no flow selected, determine by entity dropdown
        if ($waitingListType) {
            // This is a waiting list flow
            if ($waitingListType === 'ministry') {
                $searchMode = 'claim';
            } elseif ($waitingListType === 'insurance') {
                $searchMode = 'both';
            } else {
                $searchMode = 'claim'; // default for other waiting list types
            }
        } elseif ($entityType) {
            // Regular flow selected (contracts, insurance, comprehensive, ministry)
            // These all use electronic invoice search
            $searchMode = 'electronic';
        } else {
            // No flow selected - will be determined by entity dropdown
            $searchMode = 'dynamic';
        }
        
        \Log::info('User Type - isFinancial: ' . ($user->isFinancial() ? 'YES' : 'NO') . ', isReviewer: ' . ($user->isReviewer() ? 'YES' : 'NO'));
        \Log::info('Show Claim Search: ' . ($showClaimSearch ? 'YES' : 'NO'));
        \Log::info('Search Mode: ' . $searchMode);
        
        $entities = ClaimEntity::all();
        $allHospitals = Hospital::with('departments')->get();
        $allDepartments = Department::all();

        return view('claims::payments.create', compact(
            'hospital', 'department', 'entities', 'allHospitals', 'allDepartments', 
            'selectedEntityId', 'branch', 'location', 'law', 'entityType', 'showClaimSearch', 'searchMode'
        ));
    }

    /**
     * Search claims by claim number (AJAX endpoint for financial users)
     */
    public function searchClaims(Request $request)
    {
        $claimNumber = $request->input('claim_number');
        
        if (!$claimNumber) {
            return response()->json(['success' => false, 'message' => 'رقم المطالبة مطلوب']);
        }
        
        $claim = Claim::where('claim_number', $claimNumber)->first();
        
        if (!$claim) {
            return response()->json(['success' => false, 'message' => 'المطالبة غير موجودة']);
        }
        
        // Load hospital relation if exists
        $hospitalName = null;
        if ($claim->hospital_id) {
            if (is_numeric($claim->hospital_id)) {
                $hospital = Hospital::find($claim->hospital_id);
                $hospitalName = $hospital ? $hospital->name : null;
            } else {
                $hospitalName = $this->getHospitalName($claim->hospital_id);
            }
        }
        
        return response()->json([
            'success' => true,
            'claim' => [
                'id' => $claim->id,
                'claim_number' => $claim->claim_number,
                'invoice_count' => $claim->invoice_count,
                'claim_value' => $claim->claim_value,
                'electronic_invoice_no' => $claim->electronic_invoice_no,
                'hospital_id' => $claim->hospital_id,
                'hospital_name' => $hospitalName,
                'department_id' => $claim->department_id,
                'entity_id' => $claim->entity_id,
                'reviewed_value' => $claim->reviewed_value,
            ]
        ]);
    }

    /**
     * Search claims by electronic invoice number (AJAX endpoint)
     */
    public function searchByElectronicInvoice(Request $request)
    {
        $electronicInvoiceNo = $request->input('electronic_invoice_no');
        
        if (!$electronicInvoiceNo) {
            return response()->json(['success' => false, 'message' => 'رقم الفاتورة الإلكترونية مطلوب']);
        }
        
        $claim = Claim::where('electronic_invoice_no', $electronicInvoiceNo)->first();
        
        if (!$claim) {
            return response()->json(['success' => false, 'message' => 'المطالبة غير موجودة']);
        }
        
        // Load hospital relation if exists
        $hospitalName = null;
        if ($claim->hospital_id) {
            if (is_numeric($claim->hospital_id)) {
                $hospital = Hospital::find($claim->hospital_id);
                $hospitalName = $hospital ? $hospital->name : null;
            } else {
                $hospitalName = $this->getHospitalName($claim->hospital_id);
            }
        }
        
        return response()->json([
            'success' => true,
            'claim' => [
                'id' => $claim->id,
                'claim_number' => $claim->claim_number,
                'invoice_count' => $claim->invoice_count,
                'claim_value' => $claim->claim_value,
                'electronic_invoice_no' => $claim->electronic_invoice_no,
                'hospital_id' => $claim->hospital_id,
                'hospital_name' => $hospitalName,
                'department_id' => $claim->department_id,
                'entity_id' => $claim->entity_id,
                'reviewed_value' => $claim->reviewed_value,
            ]
        ]);
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
            'invoice_count_after_review' => 'nullable|numeric|min:0',
            'amount_after_review' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'payer_entity_id' => 'required|exists:claim_entities,id',
            'claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240',
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
        
        // Determine validation rules based on flow/entity
        $waitingListType = session('flow_waiting_list_type');
        $flowOptions = session('flow_options', []);
        $entityType = $flowOptions['entity_type'] ?? null;
        
        // Check if entity dropdown was used (when no flow selected)
        $selectedEntityId = $request->payer_entity_id;
        $selectedEntity = null;
        if ($selectedEntityId) {
            $selectedEntity = ClaimEntity::find($selectedEntityId);
        }
        
        // Determine if this is a waiting list scenario
        $isWaitingListMinistry = ($waitingListType === 'ministry');
        $isWaitingListInsurance = ($waitingListType === 'insurance');
        
        // Check if entity is "مديرية الشئون الصحية" (Health Directorate)
        $isHealthDirectorate = $selectedEntity && 
            (str_contains($selectedEntity->name, 'مديرية') || 
             str_contains($selectedEntity->name, 'شئون صحية'));
        
        // For regular flows (contracts, insurance, comprehensive, ministry - not waiting lists)
        // require electronic_invoice_no, not claim_number
        if (!$waitingListType && !$isHealthDirectorate) {
            // Regular entity selected from dropdown or regular flow
            $rules['electronic_invoice_no'] = [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::exists('claims', 'electronic_invoice_no')->where(function ($query) use ($request) {
                    return $query->where('entity_id', $request->payer_entity_id);
                }),
            ];
            $rules['claim_number'] = 'nullable|string|max:255';
        } elseif ($isWaitingListMinistry || $isHealthDirectorate) {
            // Waiting list - ministry OR Health Directorate selected
            $rules['claim_number'] = 'required|string|max:255|exists:claims,claim_number';
            $rules['electronic_invoice_no'] = 'nullable|string|max:255';
        } elseif ($isWaitingListInsurance) {
            // Waiting list - insurance: both fields available
            // At least one should be provided
            $rules['claim_number'] = 'nullable|string|max:255|exists:claims,claim_number';
            $rules['electronic_invoice_no'] = 'nullable|string|max:255';
            
            // Custom validation: at least one of them must be filled
            if (empty($request->claim_number) && empty($request->electronic_invoice_no)) {
                return back()->withErrors([
                    'claim_number' => 'يجب إدخال رقم المطالبة أو رقم الفاتورة الإلكترونية على الأقل.',
                    'electronic_invoice_no' => 'يجب إدخال رقم المطالبة أو رقم الفاتورة الإلكترونية على الأقل.'
                ])->withInput();
            }
        }
        
        $data = $request->validate($rules, [
            'electronic_invoice_no.exists' => 'رقم الفاتورة الإلكترونية غير موجود في نظام المطالبات للجهة المختارة.',
            'claim_number.exists' => 'رقم المطالبة غير موجود في النظام.',
        ]);

        // Set invoice_no based on electronic_invoice_no or claim_number
        if (!empty($data['electronic_invoice_no'])) {
            $data['invoice_no'] = $data['electronic_invoice_no'];
        } elseif (!empty($data['claim_number'])) {
            $data['invoice_no'] = $data['claim_number'];
        } else {
            $data['invoice_no'] = null;
        }

        // Business Logic Validation: Amount vs Claim Value (for regular flows)
        if (!empty($request->electronic_invoice_no)) {
            $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $request->electronic_invoice_no)->first();
            
            if ($claim) {
                $maxAmount = (float) $claim->claim_value;  // Changed from reviewed_value to claim_value
                $paymentAmount = (float) $request->amount;
                
                // Validate against claim_value
                if ($paymentAmount > $maxAmount) {
                    return back()->withErrors([
                        'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                    ])->withInput();
                }
            }
        }
        
        // For waiting lists - validate against claim by claim_number
        $waitingListType = session('flow_waiting_list_type');
        if ($waitingListType && !empty($request->claim_number)) {
            $claim = \App\Modules\Claims\Models\Claim::where('claim_number', $request->claim_number)->first();
            
            if ($claim) {
                $maxAmount = (float) $claim->claim_value;
                $paymentAmount = (float) $request->amount;
                
                if ($paymentAmount > $maxAmount) {
                    return back()->withErrors([
                        'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                    ])->withInput();
                }
            }
        }

        $data['user_id'] = auth()->id();

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('payment_attachments/' . date('Y/m'), 'public');
                $attachmentPaths[] = $path;
            }
            $data['attachments'] = $attachmentPaths;
        }

        $paymentOrder = $this->paymentOrderService->createPaymentOrder($data);

        // Auto-create discounted invoice if review values are provided
        $this->handleDiscountedInvoice($paymentOrder, $request);

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
        
        // Load the associated claim data if exists
        $claim = null;
        if ($payment->claim_number) {
            $claim = \App\Modules\Claims\Models\Claim::where('claim_number', $payment->claim_number)->first();
        } elseif ($payment->electronic_invoice_no) {
            $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $payment->electronic_invoice_no)->first();
        }
        
        // Determine search mode for edit (similar to create)
        $waitingListType = session('flow_waiting_list_type');
        $flowOptions = session('flow_options', []);
        $entityType = $flowOptions['entity_type'] ?? null;
        
        if ($waitingListType) {
            if ($waitingListType === 'ministry') {
                $searchMode = 'claim';
            } elseif ($waitingListType === 'insurance') {
                $searchMode = 'both';
            } else {
                $searchMode = 'claim';
            }
        } elseif ($entityType) {
            $searchMode = 'electronic';
        } else {
            $searchMode = 'dynamic';
        }

        return view('claims::payments.edit', compact('payment', 'claim', 'entities', 'allHospitals', 'allDepartments', 'searchMode'));
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
            'invoice_count_after_review' => 'nullable|numeric|min:0',
            'amount_after_review' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'payer_entity_id' => 'required|exists:claim_entities,id',
            'claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'integer',
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
        
        // Check if waiting list flow - use claim_number instead of electronic_invoice_no
        $waitingListType = session('flow_waiting_list_type');
        if (!$waitingListType) {
            $rules['electronic_invoice_no'] = [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::exists('claims', 'electronic_invoice_no')->where(function ($query) use ($request) {
                    return $query->where('entity_id', $request->payer_entity_id);
                }),
            ];
        } else {
            // For waiting lists, claim_number is required
            $rules['claim_number'] = 'required|string|max:255|exists:claims,claim_number';
            $rules['electronic_invoice_no'] = 'nullable|string|max:255';
        }
        
        $data = $request->validate($rules, [
            'electronic_invoice_no.exists' => 'رقم الفاتورة الإلكترونية غير موجود في نظام المطالبات للجهة المختارة.',
            'claim_number.exists' => 'رقم المطالبة غير موجود في النظام.',
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

        // Business Logic Validation: Amount vs Claim Value (for regular flows)
        if (!empty($request->electronic_invoice_no)) {
            $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $request->electronic_invoice_no)->first();

            if ($claim) {
                $maxAmount = (float) $claim->claim_value;  // Changed from reviewed_value to claim_value
                $paymentAmount = (float) $request->amount;

                // Validate against claim_value
                if ($paymentAmount > $maxAmount) {
                    return back()->withErrors([
                        'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                    ])->withInput();
                }
            }
        }

        // For waiting lists - validate against claim by claim_number
        $waitingListType = session('flow_waiting_list_type');
        if ($waitingListType && !empty($request->claim_number)) {
            $claim = \App\Modules\Claims\Models\Claim::where('claim_number', $request->claim_number)->first();

            if ($claim) {
                $maxAmount = (float) $claim->claim_value;
                $paymentAmount = (float) $request->amount;

                if ($paymentAmount > $maxAmount) {
                    return back()->withErrors([
                        'amount' => "قيمة أمر الدفع (<strong>" . number_format($paymentAmount, 2) . " ج.م</strong>) تتجاوز قيمة المطالبة (<strong>" . number_format($maxAmount, 2) . " ج.م</strong>). يرجى التأكد من المبلغ."
                    ])->withInput();
                }
            }
        }

        // Handle attachments - remove selected ones
        $existingAttachments = $payment->attachments ?? [];
        if ($request->has('remove_attachments')) {
            foreach ($request->remove_attachments as $index) {
                if (isset($existingAttachments[$index])) {
                    // Delete file from storage
                    if (Storage::disk('public')->exists($existingAttachments[$index])) {
                        Storage::disk('public')->delete($existingAttachments[$index]);
                    }
                    unset($existingAttachments[$index]);
                }
            }
            $existingAttachments = array_values($existingAttachments); // Re-index array
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('payment_attachments/' . date('Y/m'), 'public');
                $existingAttachments[] = $path;
            }
        }
        $data['attachments'] = $existingAttachments;

        $this->paymentOrderService->updatePaymentOrder($payment->id, $data);

        // Auto-create or update discounted invoice if review values are provided
        $this->handleDiscountedInvoice($payment, $request, true);

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

    /**
     * Handle auto-creation or update of discounted invoice record
     */
    private function handleDiscountedInvoice($paymentOrder, Request $request, $isUpdate = false)
    {
        // Only proceed if review values are provided
        if (empty($request->invoice_count_after_review) && empty($request->amount_after_review)) {
            return;
        }

        // If both are 0, means full collection - no discount
        $invoiceCountAfterReview = $request->invoice_count_after_review ?? 0;
        $amountAfterReview = $request->amount_after_review ?? 0;
        if ($invoiceCountAfterReview == 0 && $amountAfterReview == 0) {
            return;
        }

        // Get the claim details
        $claim = null;
        if (!empty($request->claim_number)) {
            $claim = \App\Modules\Claims\Models\Claim::where('claim_number', $request->claim_number)->first();
        } elseif (!empty($request->electronic_invoice_no)) {
            $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $request->electronic_invoice_no)->first();
        }

        if (!$claim) {
            return;
        }

        $originalInvoiceCount = $claim->invoice_count ?? 0;
        $originalAmount = $claim->claim_value ?? 0;

        // If after review equals original, means full collection - no discount
        if ($invoiceCountAfterReview >= $originalInvoiceCount && $amountAfterReview >= $originalAmount) {
            return;
        }

        // Calculate discounted values
        $discountedInvoiceCount = max(0, $originalInvoiceCount - $invoiceCountAfterReview);
        $unpaidAmount = max(0, $originalAmount - $amountAfterReview);

        // Only create if there's an actual discount
        if ($discountedInvoiceCount > 0 || $unpaidAmount > 0) {
            $discountedInvoiceData = [
                'claim_id' => $claim->id,
                'claim_number' => $claim->claim_number,
                'original_invoice_count' => $originalInvoiceCount,
                'discounted_invoice_count' => $discountedInvoiceCount,
                'original_amount' => $originalAmount,
                'discounted_amount' => $amountAfterReview,
                'unpaid_amount' => $unpaidAmount,
                'notes' => 'تم إنشاء تلقائياً من أمر دفع #' . $paymentOrder->id,
                'user_id' => auth()->id(),
            ];

            if ($isUpdate) {
                // Update existing record if found
                \App\Modules\Claims\Models\DiscountedInvoice::updateOrCreate(
                    ['claim_id' => $claim->id],
                    $discountedInvoiceData
                );
            } else {
                // Create new record (only if not exists)
                \App\Modules\Claims\Models\DiscountedInvoice::firstOrCreate(
                    ['claim_id' => $claim->id],
                    $discountedInvoiceData
                );
            }
        }
    }
}
