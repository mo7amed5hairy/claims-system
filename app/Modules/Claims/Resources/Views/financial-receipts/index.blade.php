@extends('claims::layouts.app')
@section('title', 'استلام دفعة مالية')

<style>
    .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }

    .form-container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .form-card {
        padding: 8px 10px !important;
        margin: 0 !important;
        border-radius: 0 !important;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-bottom: 8px;
    }

    .form-group {
        margin-bottom: 2px;
    }

    .form-label {
        font-size: 13px !important;
        margin-bottom: 4px !important;
        font-weight: 600;
        color: #697484;
    }

    .form-control {
        height: 34px !important;
        padding: 5px 10px !important;
        font-size: 14px !important;
        min-height: 34px !important;
    }

    textarea.form-control {
        min-height: 60px !important;
        height: 60px !important;
        font-size: 13px !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        min-height: 34px !important;
        padding: 0 10px !important;
        font-size: 14px !important;
        line-height: 32px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        padding-left: 2px !important;
        padding-right: 2px !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    .page-header {
        margin-bottom: 6px !important;
    }

    .page-title {
        font-size: 18px !important;
    }

    .form-actions {
        margin-top: 10px !important;
        display: flex !important;
        gap: 8px !important;
    }

    .form-actions .btn {
        padding: 5px 14px !important;
        font-size: 12px !important;
        height: 32px !important;
        min-height: 32px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    #receiptFormContainer {
        display: none;
    }

    .table-container {
        margin-top: 8px;
    }

    .remaining-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .remaining-positive {
        background: #d1fae5;
        color: #065f46;
    }

    .remaining-zero {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

@section('content')
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item"><i class="fa-solid fa-home"></i> الرئيسية</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.payment-type') }}" class="breadcrumb-item">نوع الدفع</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.prepaid_operations') }}" class="breadcrumb-item">لوحة الدفع المسبق</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">استلام دفعة مالية</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-hand-holding-dollar"></i> استلام دفعة مالية</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success"
            style="padding: 10px 16px; border-radius: 8px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; margin-bottom: 12px; font-size: 14px;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="form-container">
        <div class="form-card">
            <div
                style="display: flex; flex-direction: column; gap: 1rem; justify-content: space-between !important; align-items: center !important; margin-bottom: 12px !important;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                    <i class="fa-solid fa-list"></i> سجل الدفعات المالية المستلمة
                </h3>
                <button id="showFormBtn" class="btn btn-primary"
                    style="width: auto; min-width: 160px; height: 36px; font-size: 13px; padding: 0 16px;">
                    <i class="fa-solid fa-plus"></i> إضافة استلام دفعة
                </button>
            </div>

            <div id="receiptFormContainer">
                <form id="receiptForm" method="POST" action="{{ route('financial-receipts.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="receipt_id" id="receiptId" value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-building"></i> جهة التسليم</label>
                            <select name="payer_entity_name" id="entitySelect" class="form-control" required>
                                <option value="">اختر جهة التسليم أو اكتب اسم جديد</option>
                                @foreach($entities as $entity)
                                    <option value="{{ $entity->name }}">{{ $entity->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-hospital"></i> المستشفى (جهة الاستلام)</label>
                            <select name="payee_hospital_id" id="hospitalSelect" class="form-control" required>
                                <option value="">اختر المستشفى</option>
                                @foreach($hospitals as $hosp)
                                    <option value="{{ $hosp->id }}">{{ $hosp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                            <select name="payee_department_id" id="departmentSelect" class="form-control">
                                <option value="">اختر القسم</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> المبلغ المستلم</label>
                            <input type="number" step="0.01" name="amount" id="amountInput" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-calendar"></i> تاريخ الاستلام</label>
                            <input type="date" name="receipt_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="grid-column: span 3;">
                            <label class="form-label"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                            <textarea name="notes" class="form-control" placeholder="ملاحظات إضافية..."></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fa-solid fa-check"></i>
                            حفظ</button>
                        <button type="button" id="cancelFormBtn" class="btn btn-secondary"><i class="fa-solid fa-times"></i>
                            إلغاء</button>
                    </div>
                </form>
                <hr style="margin: 16px 0; border-color: #e2e8f0;">
            </div>

            <div class="table-container">
                <table class="table" id="receiptsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>جهة التسليم</th>
                            <th>المستشفى (جهة الاستلام)</th>
                            <th>القسم</th>
                            <th>المبلغ المستلم</th>
                            <th>المبلغ المتبقي</th>
                            <th>تاريخ الاستلام</th>
                            <th>ملاحظات</th>
                            <th>مسجل بواسطة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr data-id="{{ $receipt->id }}" data-entity="{{ $receipt->payer_entity_name }}"
                                data-hospital_id="{{ $receipt->payee_hospital_id }}"
                                data-department_id="{{ $receipt->payee_department_id }}" data-amount="{{ $receipt->amount }}"
                                data-receipt_date="{{ $receipt->receipt_date->format('Y-m-d') }}"
                                data-notes="{{ $receipt->notes }}">
                                <td>{{ $receipt->id }}</td>
                                <td>{{ $receipt->payer_entity_name }}</td>
                                <td>{{ $receipt->hospital?->name ?? '—' }}</td>
                                <td>{{ $receipt->department?->name ?? '—' }}</td>
                                <td>{{ number_format($receipt->amount, 2) }}</td>
                                <td>
                                    @php $remaining = (float) $receipt->remaining_amount; @endphp
                                    <span
                                        class="remaining-badge {{ $remaining > 0 ? 'remaining-positive' : 'remaining-zero' }}">
                                        {{ number_format($remaining, 2) }}
                                    </span>
                                </td>
                                <td>{{ $receipt->receipt_date->format('Y-m-d') }}</td>
                                <td>{{ $receipt->notes ?? '—' }}</td>
                                <td>{{ $receipt->user?->username ?? $receipt->user?->name ?? '—' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn"
                                        style="padding: 4px 10px; font-size: 12px; background: #f59e0b; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <form action="{{ route('financial-receipts.destroy', $receipt) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            style="padding: 4px 10px; font-size: 12px; background: #ef4444; color: #fff; border: none; border-radius: 4px; cursor: pointer;"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا الاستلام؟')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 24px; color: #94a3b8;">
                                    <i class="fa-solid fa-inbox"
                                        style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                    لا توجد دفعات مالية مسجلة حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            const hospitalsData = @json($hospitals);
            const $entitySelect = $('#entitySelect');
            const $hospitalSelect = $('#hospitalSelect');
            const $departmentSelect = $('#departmentSelect');
            const $receiptForm = $('#receiptForm');
            const $formMethod = $('#formMethod');
            const $receiptId = $('#receiptId');
            const $submitBtn = $('#submitBtn');
            const $formContainer = $('#receiptFormContainer');
            const $showFormBtn = $('#showFormBtn');
            const $cancelFormBtn = $('#cancelFormBtn');

            $entitySelect.select2({ dir: "rtl", width: '100%', tags: true, placeholder: 'اختر جهة التسليم أو اكتب اسم جديد' });
            $hospitalSelect.select2({ dir: "rtl", width: '100%', placeholder: 'اختر المستشفى' });
            $departmentSelect.select2({ dir: "rtl", width: '100%', placeholder: 'اختر القسم' });

            $hospitalSelect.on('change', function () {
                const hospitalId = $(this).val();
                $departmentSelect.empty().append('<option value="">اختر القسم</option>');
                if (hospitalId) {
                    const hospital = hospitalsData.find(h => h.id == hospitalId);
                    if (hospital && hospital.departments) {
                        hospital.departments.forEach(dept => {
                            $departmentSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                        });
                    }
                }
                $departmentSelect.trigger('change');
            });

            $showFormBtn.on('click', function () {
                resetForm();
                $formContainer.slideDown(300);
                $showFormBtn.hide();
            });

            $cancelFormBtn.on('click', function () {
                $formContainer.slideUp(300);
                $showFormBtn.show();
                resetForm();
            });

            $(document).on('click', '.edit-btn', function () {
                const $row = $(this).closest('tr');
                const id = $row.data('id');
                const entity = $row.data('entity');
                const hospitalId = $row.data('hospital_id');
                const departmentId = $row.data('department_id');
                const amount = $row.data('amount');
                const receiptDate = $row.data('receipt_date');
                const notes = $row.data('notes');

                $formMethod.val('PUT');
                $receiptId.val(id);
                $submitBtn.html('<i class="fa-solid fa-save"></i> تحديث');
                $receiptForm.attr('action', '{{ url('dashboard/financial-receipts') }}/' + id);

                $entitySelect.val(entity).trigger('change');
                $hospitalSelect.val(hospitalId).trigger('change');

                setTimeout(function () {
                    if (departmentId) {
                        $departmentSelect.val(departmentId).trigger('change');
                    }
                }, 300);

                $('#amountInput').val(amount);
                $('input[name="receipt_date"]').val(receiptDate);
                $('textarea[name="notes"]').val(notes);

                $formContainer.slideDown(300);
                $showFormBtn.hide();
            });

            function resetForm() {
                $formMethod.val('POST');
                $receiptId.val('');
                $submitBtn.html('<i class="fa-solid fa-check"></i> حفظ');
                $receiptForm.attr('action', '{{ route('financial-receipts.store') }}');
                $receiptForm[0].reset();
                $entitySelect.val('').trigger('change');
                $hospitalSelect.val('').trigger('change');
                $departmentSelect.val('').trigger('change');
                $('textarea[name="notes"]').val('');
            }
        });
    </script>
@endsection