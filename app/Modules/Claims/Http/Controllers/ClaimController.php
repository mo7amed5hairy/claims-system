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
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');

        if ($hospitalId) {
            $hospital = Hospital::find($hospitalId);
        } else {
            $hospital = null;
        }

        if ($deptId) {
            $department = Department::find($deptId);
        } else {
            $department = null;
        }

        $hospitals = Hospital::all();
        // We might want to filter departments via AJAX based on hospital, but for now pass all or empty?
        // User said "dropdown list of departments". Usually dependent.
        // Let's pass all departments for now or handle in view.
        // Actually, if hospital is selected, we might want its departments. 
        // But if creating fresh, we need all hospitals.
        // Let's pass all hospitals. For departments, maybe empty unless hospital selected?
        // Create view is likely expecting $hospital object for display.
        // User wants dropdowns. So we need to pass $hospitals.

        $allHospitals = Hospital::with('departments')->get();
        $entities = ClaimEntity::all();

        return view('claims::claims.create', compact('hospital', 'department', 'entities', 'allHospitals'));
    }

    /**
     * Store new claim
     */
    public function store(Request $request)
    {
        $this->authorize('create', Claim::class);
        // Validate inputs
        $data = $request->validate([
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'reviewed_value' => 'nullable|numeric|min:0',

            'electronic_invoice_no' => 'nullable|string|max:255|unique:claims,electronic_invoice_no',

            'entity_id' => 'required|exists:claim_entities,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',

            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_date' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
        ]);


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
        $claim = $this->claimService->createClaim($data);

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
        // Maybe also departments for the selected hospital?
        $departments = $claim->hospital ? $claim->hospital->departments : collect();

        return view('claims::claims.edit', compact('claim', 'entities', 'allHospitals', 'departments'));
    }

    /**
     * Update claim
     */

    public function update(Request $request, Claim $claim)
    {
        $this->authorize('update', $claim);
        $data = $request->validate([
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'reviewed_value' => 'nullable|numeric|min:0',

            'electronic_invoice_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('claims', 'electronic_invoice_no')->ignore($claim->id),
            ],

            'entity_id' => 'required|exists:claim_entities,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',

            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'delivery_date' => 'nullable|date',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
        ]);

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

        $this->claimService->updateClaim($claim->id, $data);

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
}
