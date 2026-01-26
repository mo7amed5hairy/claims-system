@extends('claims::layouts.app')

@section('title', 'تسجيل أمر دفع')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <a href="{{ route('flow.operations') }}" class="breadcrumb-item">العمليات</a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <span class="breadcrumb-item active">تسجيل أمر دفع</span>
</div>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-credit-card"></i> تسجيل أمر دفع</h1>
    <p class="page-subtitle">
        <span><i class="fa-solid fa-hospital"></i> {{ $hospital->name }}</span>
        <span style="margin: 0 8px;">•</span>
        <span><i class="fa-solid fa-stethoscope"></i> {{ $department->name }}</span>
    </p>
</div>

<div class="form-container">
    <div class="form-card">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-list-check"></i> نوع الحساب</label>
                    <select name="account_type" class="form-control" required>
                        <option value="">اختر النوع</option>
                        <option value="بنكى" {{ old('account_type') == 'بنكى' ? 'selected' : '' }}>بنكى</option>
                        <option value="أمر دفع برقم مؤسسى" {{ old('account_type') == 'أمر دفع برقم مؤسسى' ? 'selected' : '' }}>أمر دفع برقم مؤسسى</option>
                        <option value="شيك نقدى" {{ old('account_type') == 'شيك نقدى' ? 'selected' : '' }}>شيك نقدى</option>
                    </select>
                    @error('account_type') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم الـ GP / الشيك</label>
                    <input type="text" name="gp_number" class="form-control" value="{{ old('gp_number') }}" required>
                    @error('gp_number') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> المبلغ (ج.م)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
                    @error('amount') <span class="error-message">{!! $message !!}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-calendar-check"></i> تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                    @error('due_date') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-building"></i> الجهة المسددة</label>
                    <select name="payer_entity_id" class="form-control" required>
                        <option value="">اختر الجهة</option>
                        @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" {{ old('payer_entity_id') == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
                        @endforeach
                    </select>
                    @error('payer_entity_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                    <input type="text" name="electronic_invoice_no" class="form-control" value="{{ old('electronic_invoice_no') }}">
                    @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                @error('notes') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions" style="margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> تأكيد أمر الدفع
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة
                </a>
            </div>
        </form>
    </div>
</div>
@endsection