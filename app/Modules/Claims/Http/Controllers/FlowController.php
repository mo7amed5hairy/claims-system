<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Modules\Claims\Models\Hospital;
use App\Modules\Claims\Models\Department;
use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Models\ReturnedInvoice;
use App\Modules\Claims\Models\ClaimEntity;

class FlowController extends Controller
{
    /**
     * Step 1: Select Entity Type
     */
    public function index()
    {
        Log::info('=== FLOW INDEX (DASHBOARD) ACCESSED ===');
        Log::info('Is authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
        if (Auth::check()) {
            Log::info('Current user: ' . Auth::user()->username);
        }
        Log::info('Session ID: ' . session()->getId());

        $user = Auth::user();
        if ($user && $user->hasBoundFlow()) {
            return $this->applyBoundFlow($user);
        }

        return view('claims::flow.select-type');
    }

    /**
     * Apply a user's bound flow session and redirect them to the right step
     */
    private function applyBoundFlow(\App\Models\User $user)
    {
        $bf = $user->bound_flow;

        if (($bf['entity_type'] ?? null) === 'waiting_lists') {
            session([
                'flow_waiting_list_type' => $bf['waiting_list_type'] ?? null,
                'flow_hospital_id' => $bf['hospital'] ?? null,
                'flow_department_id' => $bf['department'] ?? null,
                'flow_law' => $bf['law'] ?? null,
            ]);
            session()->forget(['flow_entity_type', 'flow_options']);

            return redirect()->route('flow.payment-type');
        }

        $type = $bf['entity_type'] ?? null;
        session([
            'flow_entity_type' => $type,
            'flow_options' => [
                'entity_type' => $type,
                'entity_id' => $bf['entity_id'] ?? null,
                'location' => $bf['location'] ?? null,
                'law' => $bf['law'] ?? null,
            ],
        ]);
        session()->forget(['flow_waiting_list_type', 'flow_law']);

        return redirect()->route('flow.hospital');
    }

    public function storeType(Request $request)
    {
        $request->validate(['entity_type' => 'required|string']);
        session(['flow_entity_type' => $request->entity_type]);

        // Special handling for waiting lists - redirect to waiting lists selection page
        if ($request->entity_type === 'waiting_lists') {
            return redirect()->route('flow.waiting-lists');
        }

        // مديرية الشئون الصحية: skip options page, go straight to hospital with entity pre-selected
        if ($request->entity_type === 'ministry') {
            session(['flow_options' => [
                'entity_type' => 'ministry',
                'entity_id' => 84, // مديريه الشؤون الصحيه
            ]]);
            return redirect()->route('flow.hospital');
        }

        return redirect()->route('flow.options');
    }

    public function showOptions()
    {
        $type = session('flow_entity_type');

        // مديرية الشئون الصحية no longer uses the options page
        if ($type === 'ministry') {
            return redirect()->route('flow.hospital');
        }

        $query = ClaimEntity::query();

        // Filtering Logic based on User Requirements
        if ($type === 'contracts') {
            // Exclude specific entities (by ID, immune to renames)
            $query->whereNotIn('id', [36, 3, 12]);
        } elseif ($type === 'ministry') {
            // Only Ministry of Health (قوائم انتظار وزارة صحة)
            $query->where('id', 3);
        } elseif ($type === 'insurance') {
            // Only Health Insurance Authority (تأمين صحي فروع)
            $query->where('id', 36);
        } elseif ($type === 'comprehensive') {
            // Only Comprehensive Health Insurance
            $query->where('id', 12);
        }

        $entities = $query->get();

        // For ministry, get the pre-selected entity
        $selectedEntityId = null;
        if ($type === 'ministry' && $entities->count() > 0) {
            $selectedEntityId = $entities->first()->id;
        }

        return view('claims::flow.select-options', [
            'type' => $type,
            'entities' => $entities,
            'selectedEntityId' => $selectedEntityId
        ]);
    }

    public function storeOptions(Request $request)
    {
        $data = $request->except(['_token']);

        // Also store the entity type for reference in create forms
        $data['entity_type'] = session('flow_entity_type');

        session(['flow_options' => $data]);

        return redirect()->route('flow.hospital');
    }

    public function showHospital()
    {
        $hospitals = Hospital::with('departments')->get();
        $skipOptions = session('flow_entity_type') === 'ministry';
        return view('claims::flow.select-hospital', compact('hospitals', 'skipOptions'));
    }

    public function storeHospital(Request $request)
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        session([
            'flow_hospital_id' => $request->hospital_id,
            'flow_department_id' => $request->department_id
        ]);

        return redirect()->route('flow.payment-type');
    }

    public function showOperations()
    {
        // Ensure flow is complete
        if (!session('flow_hospital_id') || !session('flow_department_id')) {
            return redirect()->route('dashboard');
        }

        $hospital = Hospital::find(session('flow_hospital_id'));
        $department = Department::find(session('flow_department_id'));

        return view('claims::flow.operations', compact('hospital', 'department'));
    }

    public function showPaymentType()
    {
        // Ensure flow is complete
        if (!session('flow_hospital_id') || !session('flow_department_id')) {
            return redirect()->route('dashboard');
        }

        return view('claims::flow.payment-type');
    }

    public function showPrepaidOperations()
    {
        // Ensure flow is complete
        if (!session('flow_hospital_id') || !session('flow_department_id')) {
            return redirect()->route('dashboard');
        }

        $hospital = Hospital::find(session('flow_hospital_id'));
        $department = Department::find(session('flow_department_id'));

        return view('claims::flow.prepaid_operations', compact('hospital', 'department'));
    }

    /**
     * Waiting Lists Flow Methods
     */

    /**
     * Show waiting lists selection page (Insurance vs Ministry)
     */
    public function showWaitingLists()
    {
        return view('claims::flow.waiting-lists');
    }

    /**
     * Show Health Insurance waiting lists options (Hospitals -> Departments -> Laws)
     */
    public function showWaitingListsInsurance()
    {
        return view('claims::flow.waiting-list-options');
    }

    /**
     * Store Health Insurance waiting lists options
     */
    public function storeWaitingListsInsurance(Request $request)
    {
        $request->validate([
            'hospital' => 'required|string',
            'department' => 'required|string',
            'law' => 'required|string',
        ]);

        session([
            'flow_waiting_list_type' => 'insurance',
            'flow_hospital_id' => $request->hospital,
            'flow_department_id' => $request->department,
            'flow_law' => $request->law,
        ]);

        return redirect()->route('flow.payment-type');
    }

    /**
     * Show Ministry of Health waiting lists options (Hospitals -> Departments)
     */
    public function showWaitingListsMinistry()
    {
        return view('claims::flow.ministry-waiting-list');
    }

    /**
     * Store Ministry of Health waiting lists options
     */
    public function storeWaitingListsMinistry(Request $request)
    {
        $request->validate([
            'hospital' => 'required|string',
            'department' => 'required|string',
        ]);

        session([
            'flow_waiting_list_type' => 'ministry',
            'flow_hospital_id' => $request->hospital,
            'flow_department_id' => $request->department,
        ]);

        return redirect()->route('flow.payment-type');
    }
}
