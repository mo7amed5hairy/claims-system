<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hospital Controller
 * 
 * Handles hospitals and departments management
 */
class HospitalController extends Controller
{
    /**
     * HospitalController constructor.
     */
    public function __construct()
    {
        // Check if user can access non-payments modules (reviewers or admins only)
        if (!auth()->user()->canAccessNonPayments()) {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Display list of hospitals with departments
     */
    public function index()
    {
        $this->authorize('viewAny', Hospital::class);
        $hospitals = Hospital::with('departments')->paginate(15);
        return view('claims::hospitals.index', compact('hospitals'));
    }

    /**
     * Store new hospital with departments
     */
    public function store(Request $request)
    {
        $this->authorize('create', Hospital::class);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:hospitals,name',
            'departments' => 'nullable|array',
            'departments.*' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $hospital = Hospital::create(['name' => $data['name']]);

            if (!empty($data['departments'])) {
                foreach ($data['departments'] as $deptName) {
                    if (trim($deptName) !== '') {
                        $hospital->departments()->create(['name' => $deptName]);
                    }
                }
            }
        });

        return redirect()->back()
            ->with('success', trans('messages.hospital_created_successfully'));
    }

    /**
     * Store departments for an existing hospital
     */
    public function storeDepartment(Request $request, Hospital $hospital)
    {
        $this->authorize('update', $hospital);

        $data = $request->validate([
            'departments' => 'required|array|min:1',
            'departments.*' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($hospital, $data) {
            foreach ($data['departments'] as $deptName) {
                if (trim($deptName) !== '') {
                    $hospital->departments()->create(['name' => $deptName]);
                }
            }
        });

        return redirect()->back()
            ->with('success', trans('messages.department_created_successfully'));
    }
}
