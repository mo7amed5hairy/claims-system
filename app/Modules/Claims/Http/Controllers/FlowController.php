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

class FlowController extends Controller
{
    /**
     * Step 2: Select Entity Type
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
        return view('claims::flow.select-options', ['type' => $type]);
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
            'flow_department_id' => $request->department_id,
        ]);

        return redirect()->route('flow.operations');
    }

    public function operations()
    {
        $hospitalId = session('flow_hospital_id');
        $deptId = session('flow_department_id');

        if (!$hospitalId || !$deptId) {
            return redirect()->route('dashboard');
        }

        // Calculate Totals for the current context (Hospital & Dept)
        $totalClaims = Claim::where('hospital_id', $hospitalId)
                           ->where('department_id', $deptId)
                           ->sum('claim_value');

        $totalReturns = ReturnedInvoice::where('hospital_id', $hospitalId)
                                      ->where('department_id', $deptId)
                                      ->sum('value');

        $netClaims = $totalClaims - $totalReturns;

        return view('claims::flow.operations', compact('totalClaims', 'totalReturns', 'netClaims'));
    }
}
