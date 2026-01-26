<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\Hospital;
use Illuminate\Http\Request;

/**
 * Hospital Controller
 * 
 * Handles hospitals and departments management
 */
class HospitalController extends Controller
{
    /**
     * Display list of hospitals with departments
     */
    public function index()
    {
        $hospitals = Hospital::with('departments')->paginate(15);
        return view('claims::hospitals.index', compact('hospitals'));
    }

    /**
     * Store new hospital
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:hospitals,name',
        ]);

        Hospital::create($data);

        return redirect()->back()
            ->with('success', trans('messages.hospital_created_successfully'));
    }

    /**
     * Store new department
     */
    public function storeDepartment(Request $request, Hospital $hospital)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,null,null,hospital_id,' . $hospital->id,
        ]);

        $hospital->departments()->create($data);

        return redirect()->back()
            ->with('success', trans('messages.department_created_successfully'));
    }
}
