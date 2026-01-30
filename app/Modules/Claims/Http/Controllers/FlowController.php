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
        
        return view('claims::flow.select-type');
    }

    public function storeType(Request $request)
    {
        $request->validate(['entity_type' => 'required|string']);
        session(['flow_entity_type' => $request->entity_type]);

        return redirect()->route('flow.options');
    }

    public function showOptions()
    {
        $type = session('flow_entity_type');
        $query = ClaimEntity::query();

        // Filtering Logic based on User Requirements
        if ($type === 'contracts') {
            // Exclude specific entities
            $query->whereNotIn('name', [
                'الهيئة العامة للتأمين الصحي',
                'وزارة الصحة والسكان',
                'الهيئة العامة للتأمين الصحي الشامل'
            ]);
        } elseif ($type === 'ministry') {
            // Only Ministry of Health
            $query->where('name', 'وزارة الصحة والسكان');
        } elseif ($type === 'insurance') {
            // Only Health Insurance Authority
            $query->where('name', 'الهيئة العامة للتأمين الصحي');
        } elseif ($type === 'comprehensive') {
            // Only Comprehensive Health Insurance
            $query->where('name', 'الهيئة العامة للتأمين الصحي الشامل');
        }

        $entities = $query->get();

        return view('claims::flow.select-options', ['type' => $type, 'entities' => $entities]);
    }

    public function storeOptions(Request $request)
    {
        $data = $request->except(['_token']);
        session(['flow_options' => $data]);

        return redirect()->route('flow.hospital');
    }

    public function showHospital()
    {
        $hospitals = Hospital::with('departments')->get();
        return view('claims::flow.select-hospital', compact('hospitals'));
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

        return redirect()->route('flow.operations');
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
}
