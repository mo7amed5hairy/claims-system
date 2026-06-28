<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\FinancialReceipt;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Models\Hospital;
use Illuminate\Http\Request;

class FinancialReceiptController extends Controller
{
    public function __construct()
    {
        if (!auth()->user()->canAccessPayments()) {
            abort(403, 'Unauthorized access');
        }
    }

    public function index()
    {
        $receipts = FinancialReceipt::with('user', 'hospital', 'department')->orderBy('id', 'desc')->get();
        $entities = ClaimEntity::all();
        $hospitals = Hospital::with('departments')->get();

        return view('claims::financial-receipts.index', compact('receipts', 'entities', 'hospitals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payer_entity_name' => 'required|string|max:255',
            'payee_hospital_id' => 'required|exists:hospitals,id',
            'payee_department_id' => 'nullable|exists:departments,id',
            'amount' => 'required|numeric|min:0',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['remaining_amount'] = $validated['amount'];
        $validated['user_id'] = auth()->id();

        FinancialReceipt::create($validated);

        return redirect()->route('financial-receipts.index')
            ->with('success', 'تم تسجيل استلام الدفعة المالية بنجاح');
    }

    public function update(Request $request, FinancialReceipt $financialReceipt)
    {
        $validated = $request->validate([
            'payer_entity_name' => 'required|string|max:255',
            'payee_hospital_id' => 'required|exists:hospitals,id',
            'payee_department_id' => 'nullable|exists:departments,id',
            'amount' => 'required|numeric|min:0',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $financialReceipt->amount;
        $difference = $validated['amount'] - $oldAmount;
        $validated['remaining_amount'] = $financialReceipt->remaining_amount + $difference;

        $financialReceipt->update($validated);

        return redirect()->route('financial-receipts.index')
            ->with('success', 'تم تحديث استلام الدفعة المالية بنجاح');
    }

    public function destroy(FinancialReceipt $financialReceipt)
    {
        $financialReceipt->delete();

        return redirect()->route('financial-receipts.index')
            ->with('success', 'تم حذف استلام الدفعة المالية بنجاح');
    }
}
