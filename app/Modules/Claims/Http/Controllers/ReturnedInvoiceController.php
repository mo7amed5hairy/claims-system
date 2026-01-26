<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\ReturnedInvoice;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Models\Department;
use App\Modules\Claims\Services\ReturnedInvoiceService;
use Illuminate\Http\Request;

/**
 * Returned Invoice Controller
 * 
 * Handles returned invoices operations
 */
class ReturnedInvoiceController extends Controller
{
    protected $returnedInvoiceService;

    public function __construct(ReturnedInvoiceService $returnedInvoiceService)
    {
        $this->returnedInvoiceService = $returnedInvoiceService;
    }

    /**
     * Display list of returned invoices
     */
    public function index()
    {
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');

        $invoices = ReturnedInvoice::with(['hospital', 'department', 'entity'])->get();

        return view('claims::returns.index', compact('invoices'));
    }

    /**
     * Show create returned invoice form
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

        return view('claims::returns.create', compact('hospital', 'department', 'entities'));
    }

    /**
     * Store new returned invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'return_date' => 'required|date',
            'value' => 'required|numeric|min:0',
            'returned_invoice_count' => 'required|numeric|min:0',
            'reviewed_value' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'electronic_invoice_no' => 'required|string|max:255',
            'entity_id' => 'required|exists:claim_entities,id',
            'reviewer_name' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
        ]);

        // Smart Validation for Electronic Invoice Number
        $invoiceNo = $request->electronic_invoice_no;
        $selectedEntityId = (int)$request->entity_id;

        $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $invoiceNo)->first();

        if (!$claim) {
            return back()->withErrors(['electronic_invoice_no' => 'عفواً، رقم الفاتورة الإلكترونية هذا غير مسجل في نظام المطالبات. يرجى التأكد من الرقم الصحيح.'])->withInput();
        }

        if ((int)$claim->entity_id !== $selectedEntityId) {
            $correctEntity = \App\Modules\Claims\Models\ClaimEntity::find($claim->entity_id);
            $entityName = $correctEntity ? $correctEntity->name : 'جهة أخرى';
            return back()->withErrors([
                'electronic_invoice_no' => "هذا الرقم مسجل بالفعل في نظام المطالبات ولكن لجهة مختلفة (<strong>{$entityName}</strong>). يرجى اختيار الجهة الصحيحة أو مراجعة رقم الفاتورة."
            ])->withInput();
        }

        // Business Logic Validation: Invoice Count
        $returnedCount = (int)$request->returned_invoice_count;
        $originalCount = (int)$claim->invoice_count;

        if ($returnedCount > $originalCount) {
            return back()->withErrors([
                'returned_invoice_count' => "عدد الفواتير المرتجعة (<strong>{$returnedCount}</strong>) يتجاوز عدد الفواتير في المطالبة الأصلية (<strong>{$originalCount}</strong>). يرجى التأكد من العدد الصحيح."
            ])->withInput();
        }

        // Business Logic Validation: Return Value
        $returnedValue = (float)$request->value;
        $originalValue = (float)$claim->claim_value;

        if ($returnedValue > $originalValue) {
            return back()->withErrors([
                'value' => "قيمة المرتجع (<strong>" . number_format($returnedValue, 2) . " ج.م</strong>) تتجاوز قيمة المطالبة الأصلية (<strong>" . number_format($originalValue, 2) . " ج.م</strong>). يرجى التأكد من المبلغ الصحيح."
            ])->withInput();
        }

        // Business Logic Validation: Final Amount vs Reviewed Value
        $finalAmount = (float)$request->final_amount;
        $claimReviewedValue = (float)$claim->reviewed_value;

        if ($claimReviewedValue > 0 && $finalAmount > $claimReviewedValue) {
            return back()->withErrors([
                'final_amount' => "المبلغ النهائي (<strong>" . number_format($finalAmount, 2) . " ج.م</strong>) يتجاوز قيمة المراجعة في المطالبة (<strong>" . number_format($claimReviewedValue, 2) . " ج.م</strong>). لا يمكن خصم مبلغ أكبر من القيمة المتاحة."
            ])->withInput();
        }

        $data = $request->all();
        $data['hospital_id'] = session('flow_hospital_id');
        $data['department_id'] = session('flow_department_id');

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $invoiceModel = new ReturnedInvoice();
            $data['attachments'] = $invoiceModel->uploadMultipleMedia($request->file('attachments'));
        }

        $this->returnedInvoiceService->createReturnedInvoice($data);

        return redirect()->route('returns.index')
            ->with('success', trans('messages.returned_invoice_created_successfully'));
    }

    /**
     * Show edit returned invoice form
     */
    public function edit(ReturnedInvoice $return)
    {
        $entities = ClaimEntity::all();
        return view('claims::returns.edit', compact('return', 'entities'));
    }

    /**
     * Update returned invoice
     */
    public function update(Request $request, ReturnedInvoice $return)
    {
        $request->validate([
            'month' => 'required|string',
            'return_date' => 'required|date',
            'value' => 'required|numeric|min:0',
            'returned_invoice_count' => 'required|numeric|min:0',
            'reviewed_value' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'electronic_invoice_no' => 'required|string|max:255',
            'entity_id' => 'required|exists:claim_entities,id',
            'reviewer_name' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
        ]);

        // Smart Validation for Electronic Invoice Number
        $invoiceNo = $request->electronic_invoice_no;
        $selectedEntityId = (int)$request->entity_id;

        $claim = \App\Modules\Claims\Models\Claim::where('electronic_invoice_no', $invoiceNo)->first();

        if (!$claim) {
            return back()->withErrors(['electronic_invoice_no' => 'عفواً، رقم الفاتورة الإلكترونية هذا غير مسجل في نظام المطالبات. يرجى التأكد من الرقم الصحيح.'])->withInput();
        }

        if ((int)$claim->entity_id !== $selectedEntityId) {
            $correctEntity = \App\Modules\Claims\Models\ClaimEntity::find($claim->entity_id);
            $entityName = $correctEntity ? $correctEntity->name : 'جهة أخرى';
            return back()->withErrors([
                'electronic_invoice_no' => "هذا الرقم مسجل بالفعل في نظام المطالبات ولكن لجهة مختلفة (<strong>{$entityName}</strong>). يرجى اختيار الجهة الصحيحة أو مراجعة رقم الفاتورة."
            ])->withInput();
        }

        // Business Logic Validation: Invoice Count
        $returnedCount = (int)$request->returned_invoice_count;
        $originalCount = (int)$claim->invoice_count;

        if ($returnedCount > $originalCount) {
            return back()->withErrors([
                'returned_invoice_count' => "عدد الفواتير المرتجعة (<strong>{$returnedCount}</strong>) يتجاوز عدد الفواتير في المطالبة الأصلية (<strong>{$originalCount}</strong>). يرجى التأكد من العدد الصحيح."
            ])->withInput();
        }

        // Business Logic Validation: Return Value
        $returnedValue = (float)$request->value;
        $originalValue = (float)$claim->claim_value;

        if ($returnedValue > $originalValue) {
            return back()->withErrors([
                'value' => "قيمة المرتجع (<strong>" . number_format($returnedValue, 2) . " ج.م</strong>) تتجاوز قيمة المطالبة الأصلية (<strong>" . number_format($originalValue, 2) . " ج.م</strong>). يرجى التأكد من المبلغ الصحيح."
            ])->withInput();
        }

        // Business Logic Validation: Final Amount vs Reviewed Value (considering old return values)
        $finalAmount = (float)$request->final_amount;
        $oldFinalAmount = (float)$return->final_amount;
        $claimReviewedValue = (float)$claim->reviewed_value + $oldFinalAmount; // Add back old value for comparison

        if ($claimReviewedValue > 0 && $finalAmount > $claimReviewedValue) {
            return back()->withErrors([
                'final_amount' => "المبلغ النهائي (<strong>" . number_format($finalAmount, 2) . " ج.م</strong>) يتجاوز قيمة المراجعة المتاحة في المطالبة (<strong>" . number_format($claimReviewedValue, 2) . " ج.م</strong>). لا يمكن خصم مبلغ أكبر من القيمة المتاحة."
            ])->withInput();
        }

        $data = $request->all();

        // Handle file uploads/replacements
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $return->replaceMultipleMedia(
                $request->file('attachments'),
                $return->attachments ?? []
            );
        }

        $this->returnedInvoiceService->updateReturnedInvoice($return->id, $data);

        return redirect()->route('returns.index')
            ->with('success', trans('messages.returned_invoice_updated_successfully'));
    }

    /**
     * Delete returned invoice
     */
    public function destroy(ReturnedInvoice $return)
    {
        $this->returnedInvoiceService->deleteReturnedInvoice($return->id);

        return redirect()->route('returns.index')
            ->with('success', trans('messages.returned_invoice_deleted_successfully'));
    }
}
