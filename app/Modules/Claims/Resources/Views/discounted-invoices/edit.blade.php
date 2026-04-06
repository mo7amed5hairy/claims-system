@extends('claims::layouts.app')

@section('title', 'تعديل فاتورة مخصمة')

@section('content')
<style>
    .form-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }
    .form-group {
        margin-bottom: 10px;
    }
    .form-label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: 600;
    }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .btn {
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-primary {
        background: var(--primary-color);
        color: white;
    }
    .btn-secondary {
        background: #e2e8f0;
        color: #475569;
        text-decoration: none;
    }
    .btn:hover {
        opacity: 0.9;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
    .error-message {
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
    }
</style>

<div class="page-header" style="padding: 10px 15px; margin-bottom: 6px; background: white; border-bottom: 1px solid #e2e8f0;">
    <h1 class="page-title" style="font-size: 18px; margin: 0;">
        <i class="fa-solid fa-edit" style="color: var(--primary-color);"></i> تعديل فاتورة مخصمة #{{ $discountedInvoice->id }}
    </h1>
</div>

<div class="content-wrapper" style="padding: 0 15px;">
    <div class="form-card" style="padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form action="{{ route('discounted-invoices.update', $discountedInvoice) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label"><i class="fa-solid fa-receipt"></i> المطالبة</label>
                    <select name="claim_id" id="claimSelect" class="form-control select2" required>
                        <option value="">اختر المطالبة</option>
                        @foreach($claims as $claim)
                            <option value="{{ $claim->id }}" data-invoice-count="{{ $claim->invoice_count }}" data-claim-value="{{ $claim->claim_value }}" {{ $discountedInvoice->claim_id == $claim->id ? 'selected' : '' }}>
                                {{ $claim->claim_number }} - {{ $claim->hospital->name ?? $claim->hospital_id }} ({{ number_format($claim->claim_value, 2) }} ج.م)
                            </option>
                        @endforeach
                    </select>
                    @error('claim_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-calculator"></i> عدد الفواتير الأصلي</label>
                    <input type="number" name="original_invoice_count" id="originalInvoiceCount" class="form-control" min="0" value="{{ old('original_invoice_count', $discountedInvoice->original_invoice_count) }}" required readonly>
                    @error('original_invoice_count') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-minus-circle"></i> عدد الفواتير المخصمة</label>
                    <input type="number" name="discounted_invoice_count" id="discountedInvoiceCount" class="form-control" min="0" value="{{ old('discounted_invoice_count', $discountedInvoice->discounted_invoice_count) }}" required>
                    @error('discounted_invoice_count') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-coins"></i> المبلغ الأصلي</label>
                    <input type="number" step="0.01" name="original_amount" id="originalAmount" class="form-control" min="0" value="{{ old('original_amount', $discountedInvoice->original_amount) }}" required readonly>
                    @error('original_amount') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-tag"></i> المبلغ بعد الخصم</label>
                    <input type="number" step="0.01" name="discounted_amount" id="discountedAmount" class="form-control" min="0" value="{{ old('discounted_amount', $discountedInvoice->discounted_amount) }}" required>
                    @error('discounted_amount') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" style="color: #dc2626;"><i class="fa-solid fa-exclamation-triangle"></i> المبلغ الغير مسدد</label>
                    <input type="number" step="0.01" name="unpaid_amount" id="unpaidAmount" class="form-control" min="0" value="{{ old('unpaid_amount', $discountedInvoice->unpaid_amount) }}" required readonly style="background: #fef2f2; color: #dc2626; font-weight: bold;">
                    @error('unpaid_amount') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: span 4;">
                    <label class="form-label"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $discountedInvoice->notes) }}</textarea>
                    @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('discounted-invoices.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        dir: 'rtl',
        width: '100%'
    });

    $('#claimSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const invoiceCount = selectedOption.data('invoice-count');
        const claimValue = selectedOption.data('claim-value');

        $('#originalInvoiceCount').val(invoiceCount || 0);
        $('#originalAmount').val(claimValue || 0);
        calculateUnpaid();
    });

    $('#discountedAmount, #originalAmount').on('input', function() {
        calculateUnpaid();
    });

    function calculateUnpaid() {
        const original = parseFloat($('#originalAmount').val()) || 0;
        const discounted = parseFloat($('#discountedAmount').val()) || 0;
        const unpaid = original - discounted;
        $('#unpaidAmount').val(unpaid.toFixed(2));
    }
});
</script>
@endsection
