<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Services\ClaimService;
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Models\Department;
use App\Modules\Claims\Models\ClaimEntity;
use Illuminate\Http\Request;

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
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');

        $claims = Claim::with(['entity', 'hospital', 'department'])->get();

        return view('claims::claims.index', compact('claims'));
    }

    /**
     * Show create claim form
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

        return view('claims::claims.create', compact('hospital', 'department', 'entities'));
    }

    /**
     * Store new claim
     */
    public function store(Request $request)
    {
        // Validate inputs
        $data = $request->validate([
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'reviewed_value' => 'nullable|numeric|min:0',
            'electronic_invoice_no' => 'nullable|string|max:255',
            'entity_id' => 'required|exists:claim_entities,id',
            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
            'branch' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
        ]);

        // Hospital & Department from session
        $data['hospital_id'] = session('flow_hospital_id');
        $data['department_id'] = session('flow_department_id');

        // Flow Options: take directly from form inputs
        $data['branch'] = $request->input('branch');          // من الفورم
        $data['location'] = $request->input('location');      // من الفورم
        $data['beneficiary'] = $request->input('beneficiary');// من الفورم

        // Handle file uploads if any
        if ($request->hasFile('attachments')) {
            $claimModel = new Claim(); // Temp instance to use trait methods if needed
            $data['attachments'] = $claimModel->uploadMultipleMedia($request->file('attachments'));
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
        $entities = ClaimEntity::all();
        return view('claims::claims.edit', compact('claim', 'entities'));
    }

    /**
     * Update claim
     */
    public function update(Request $request, Claim $claim)
    {
        $data = $request->validate([
            'invoice_count' => 'required|numeric|min:1',
            'month' => 'required|string',
            'claim_date' => 'required|date',
            'claim_value' => 'required|numeric|min:0',
            'reviewer_name' => 'nullable|string|max:255',
            'reviewed_value' => 'nullable|numeric|min:0',
            'electronic_invoice_no' => 'nullable|string|max:255',
            'entity_id' => 'required|exists:claim_entities,id',
            'insurance_claim_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:10240',
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

        $this->claimService->updateClaim($claim->id, $data);

        return redirect()->route('claims.index')
            ->with('success', trans('messages.claim_updated_successfully'));
    }

    /**
     * Delete claim
     */
    public function destroy(Claim $claim)
    {
        $this->claimService->deleteClaim($claim->id);

        return redirect()->route('claims.index')
            ->with('success', trans('messages.claim_deleted_successfully'));
    }
}
