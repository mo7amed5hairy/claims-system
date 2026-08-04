<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Services\ClaimService;
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Models\Department;
use App\Modules\Claims\Models\ClaimEntity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

/**
 * Claims Controller
 * 
 * Handles all claim-related operations (CRUD)
 */
class ClaimController extends Controller
{
    protected $claimService;

    public function __construct(ClaimService $claimService)
    {
        $this->claimService = $claimService;

        // Check if user can access non-payments modules (reviewers or admins only)
        if (!auth()->user()->canAccessNonPayments()) {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Display claims list
     */
    public function index()
    {
        $this->authorize('viewAny', Claim::class);
        $claims = Claim::with(['entity', 'hospital', 'department', 'payments', 'user'])->orderBy('id', 'desc')->get();

        return view('claims::claims.index', compact('claims'));
    }

    /**
     * Show create claim form
     */
    public function create()
    {
        $this->authorize('create', Claim::class);

        // Get all session data
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');
        $waitingListType = session('flow_waiting_list_type');
        $flowOptions = session('flow_options', []);
        $entityType = $flowOptions['entity_type'] ?? null;

        // DEBUG: Log all session data
        \Log::info('=== CLAIM CREATE SESSION DATA ===');
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
        $law = null;

        // Handle Hospital & Department (common for all flows)
        if ($hospitalId) {
            if (is_numeric($hospitalId)) {
                $hospital = Hospital::find($hospitalId);
            } else {
                // String value from waiting list
                $hospital = (object) ['id' => $hospitalId, 'name' => $this->getHospitalName($hospitalId)];
            }
        }

        if ($deptId) {
            if (is_numeric($deptId)) {
                $department = Department::find($deptId);
            } else {
                // String value from waiting list
                $department = (object) ['id' => $deptId, 'name' => $deptId];
            }
        }

        // Handle different flow types
        if ($waitingListType) {
            // Waiting Lists Flow (insurance or ministry)
            if ($waitingListType === 'insurance') {
                $selectedEntity = ClaimEntity::find(36); // تأمين صحي فروع
                $selectedEntityId = $selectedEntity ? $selectedEntity->id : null;
            } elseif ($waitingListType === 'ministry') {
                $selectedEntity = ClaimEntity::find(3); // قوائم انتظار وزارة صحة
                $selectedEntityId = $selectedEntity ? $selectedEntity->id : null;
            }
            $law = session('flow_law');
            \Log::info('Waiting List Flow - Entity ID: ' . ($selectedEntityId ?? 'NULL') . ', Law: ' . ($law ?? 'NULL'));

        } elseif ($entityType) {
            // Regular flows (contracts, insurance, ministry, comprehensive)
            $selectedEntityId = $flowOptions['entity_id'] ?? null;
            $branch = $flowOptions['branch'] ?? null;
            $location = $flowOptions['location'] ?? null;
            $law = $flowOptions['law'] ?? null;
            \Log::info('Regular Flow - Entity ID: ' . ($selectedEntityId ?? 'NULL') . ', Branch: ' . ($branch ?? 'NULL') . ', Location: ' . ($location ?? 'NULL') . ', Law: ' . ($law ?? 'NULL'));
        }

        // DEBUG: Log final values being passed to view
        \Log::info('=== PASSING TO VIEW ===');
        \Log::info('selectedEntityId: ' . ($selectedEntityId ?? 'NULL'));
        \Log::info('branch: ' . ($branch ?? 'NULL'));
        \Log::info('location: ' . ($location ?? 'NULL'));
        \Log::info('law: ' . ($law ?? 'NULL'));

        // Check if user is reviewer (for showing extra fields in waiting lists insurance flow)
        $user = auth()->user();
        $isReviewer = $user->isReviewer(); // User has 'مراجع' in user_type
        $isFinancialOnly = $user->isFinancial() && !$user->isReviewer(); // Financial only
        $showWaitingListFields = ($isReviewer && !$isFinancialOnly) && ($waitingListType === 'insurance');
        \Log::info('User Type - isReviewer: ' . ($isReviewer ? 'YES' : 'NO') . ', isFinancialOnly: ' . ($isFinancialOnly ? 'YES' : 'NO'));
        \Log::info('Show Waiting List Fields: ' . ($showWaitingListFields ? 'YES' : 'NO'));

        $allHospitals = Hospital::with('departments')->get();
        $allDepartments = Department::all();
        $entities = ClaimEntity::all();

        return view('claims::claims.create', compact(
            'hospital',
            'department',
            'entities',
            'allHospitals',
            'allDepartments',
            'selectedEntityId',
            'law',
            'branch',
            'location',
            'entityType',
            'showWaitingListFields'
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
     * Store new claim
     */
    public function store(Request $request)
    {
        $this->authorize('create', Claim::class);

        // Check if hospital_id is numeric (DB) or string (waiting list)
        $hospitalId = $request->input('hospital_id');
        $deptId = $request->input('department_id');
        $isWaitingListHospital = $hospitalId && !is_numeric($hospitalId);

        // Validate inputs - for waiting lists, don't check exists rule
        $rules = [
            'claim_number' => 'required|string|max:255|unique:claims,claim_number',
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'electronic_invoice_no' => 'nullable|string|max:255|unique:claims,electronic_invoice_no',
            'entity_id' => 'required|exists:claim_entities,id',
            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_date' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'claim_description' => 'nullable|string',
            'electronic_invoice_date' => 'nullable|date',
        ];

        // For waiting lists, don't require exists in DB
        if ($isWaitingListHospital) {
            $rules['hospital_id'] = 'required|string';
            $rules['department_id'] = 'required|string';
        } else {
            $rules['hospital_id'] = 'required|exists:hospitals,id';
            $rules['department_id'] = 'required|exists:departments,id';
        }

        $data = $request->validate($rules);


        // Data already validated and in $data
        // Store session for next time convenience if needed? 
        // session(['flow_hospital_id' => $data['hospital_id'], 'flow_department_id' => $data['department_id']]);

        // Remove session reliance here, use form data directly.
        // $data['hospital_id'] = session('flow_hospital_id'); // REMOVED
        // $data['department_id'] = session('flow_department_id'); // REMOVED

        // Flow Options: take directly from form inputs
        $data['branch'] = $request->input('branch');          // من الفورم
        $data['location'] = $request->input('location');      // من الفورم
        $data['beneficiary'] = $request->input('beneficiary');// من الفورم

        $data['user_id'] = auth()->id();

        // Handle file uploads if any
        if ($request->hasFile('attachments')) {
            $claimModel = new Claim(); // Temp instance to use trait methods if needed
            $data['attachments'] = $claimModel->uploadMultipleMedia($request->file('attachments'));
        }

        if ($request->hasFile('delivery_attachments')) {
            $claimModel = new Claim();
            $data['delivery_attachments'] = $claimModel->uploadMultipleMedia($request->file('delivery_attachments'));
        }

        // Create the claim
        try {
            $claim = $this->claimService->createClaim($data);
        } catch (QueryException $e) {
            \Log::error('Claim create DB error: ' . $e->getMessage());
            return back()->withErrors([
                'entity_id' => 'حدث خطأ أثناء حفظ المطالبة. تأكد من اختيار الجهة المتعاقدة بشكل صحيح وحاول مرة أخرى.'
            ])->withInput();
        }

        return redirect()->route('claims.index')
            ->with('success', trans('messages.claim_created_successfully'));
    }


    /**
     * Show edit claim form
     */
    public function edit(Claim $claim)
    {
        $this->authorize('update', $claim);
        $entities = ClaimEntity::all();
        $allHospitals = Hospital::with('departments')->get();
        $allDepartments = Department::all();
        // Maybe also departments for the selected hospital?
        $departments = $claim->hospital ? $claim->hospital->departments : collect();

        return view('claims::claims.edit', compact('claim', 'entities', 'allHospitals', 'allDepartments', 'departments'));
    }

    /**
     * Update claim
     */

    public function update(Request $request, Claim $claim)
    {
        $this->authorize('update', $claim);

        $rules = [
            'claim_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('claims', 'claim_number')->ignore($claim->id),
            ],
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'electronic_invoice_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('claims', 'electronic_invoice_no')->ignore($claim->id),
            ],
            'entity_id' => 'required|exists:claim_entities,id',
            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_date' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
        ];

        // Check if hospital_id is numeric (DB hospital) or string (waiting list)
        $hospitalId = $request->input('hospital_id');
        if ($hospitalId && !is_numeric($hospitalId)) {
            // Waiting list hospital - accept string
            $rules['hospital_id'] = 'required|string|max:255';
            $rules['department_id'] = 'required|string|max:255';
        } else {
            // Regular hospital - must exist in DB
            $rules['hospital_id'] = 'required|exists:hospitals,id';
            $rules['department_id'] = 'required|exists:departments,id';
        }

        $data = $request->validate($rules);

        // Handle file uploads/replacements
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $claim->replaceMultipleMedia(
                $request->file('attachments'),
                $claim->attachments ?? []
            );
        }

        if ($request->hasFile('delivery_attachments')) {
            $data['delivery_attachments'] = $claim->replaceMultipleMedia(
                $request->file('delivery_attachments'),
                $claim->delivery_attachments ?? []
            );
        }

        try {
            $this->claimService->updateClaim($claim->id, $data);
        } catch (QueryException $e) {
            \Log::error('Claim update DB error: ' . $e->getMessage());
            return back()->withErrors([
                'entity_id' => 'حدث خطأ أثناء تحديث المطالبة. تأكد من اختيار الجهة المتعاقدة بشكل صحيح وحاول مرة أخرى.'
            ])->withInput();
        }

        return redirect()->route('claims.index')
            ->with('success', trans('messages.claim_updated_successfully'));
    }


    /**
     * Delete claim
     */
    public function destroy(Claim $claim)
    {
        $this->authorize('delete', $claim);
        $this->claimService->deleteClaim($claim->id);

        return redirect()->route('claims.index')
            ->with('success', trans('messages.claim_deleted_successfully'));
    }

    /**
     * Toggle claim type from standard to prepaid
     */
    public function toggleType(Claim $claim)
    {
        $this->authorize('update', $claim);

        // Switch to prepaid
        $claim->is_prepaid = 1;
        $claim->save();

        // Also update associated payment orders to is_prepaid = 1
        \DB::table('payment_orders')
            ->where('claim_number', $claim->claim_number)
            ->update(['is_prepaid' => 1]);

        // Also update associated discounted invoices to is_prepaid = 1
        \DB::table('discounted_invoices')
            ->where('claim_id', $claim->id)
            ->update(['is_prepaid' => 1]);

        return redirect()->route('claims.index')
            ->with('success', 'تم تحويل المطالبة إلى مطالبة مسبقة الدفع بنجاح وصارت تظهر في قسم المطالبات مسبقة الدفع.');
    }
}

