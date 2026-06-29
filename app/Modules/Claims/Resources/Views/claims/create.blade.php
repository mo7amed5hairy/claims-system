@extends('claims::layouts.app')

@section('title', 'إنشاء مطالبة جديدة')

<style>
    /* Make form fit in single screen without scroll */
    .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
    }
    .form-container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        max-height: calc(100vh - 120px) !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }
    .form-card {
        padding: 4px 8px !important;
        margin: 0 !important;
        border-radius: 0 !important;
    }
    /* Compact rows */
    .form-row {
        display: grid;
        grid-template-columns: repeat(9, 1fr) !important;
        gap: 3px !important;
        margin-bottom: 2px !important;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 3px !important;
        margin-bottom: 2px !important;
    }
    .form-row-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 3px !important;
        margin-bottom: 2px !important;
    }
    .form-row-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 3px !important;
        margin-bottom: 2px !important;
    }
    .form-group {
        margin-bottom: 1px !important;
    }
    /* Compact labels - slightly larger */
    .form-label {
        font-size: 12px !important;
        margin-bottom: 3px !important;
        padding: 3px 4px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Compact inputs - slightly larger */
    .form-control {
        height: 32px !important;
        padding: 4px 8px !important;
    }
    /* Select2 slightly larger */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 36px !important;
        min-height: 36px !important;
        max-height: 36px !important;
        padding: 0 10px !important;
        font-size: 14px !important;
        line-height: 34px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        padding-left: 2px !important;
        padding-right: 2px !important;
        font-size: 14px !important;
        height: 34px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
        width: 24px !important;
        top: 0 !important;
        right: 2px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-width: 4px !important;
        margin-top: -2px !important;
    }
    .select2-dropdown {
        font-size: 12px !important;
        border-radius: 4px !important;
    }
    .select2-results__option {
        padding: 4px 8px !important;
        font-size: 12px !important;
        min-height: 22px !important;
        line-height: 18px !important;
    }
    /* Compact textarea */
    textarea.form-control {
        min-height: 45px !important;
        height: 45px !important;
        font-size: 12px !important;
    }
    /* Error messages */
    .error-message {
        font-size: 9px !important;
    }
    /* Small text */
    small.text-muted {
        font-size: 8px !important;
        display: block;
        line-height: 1.1;
    }
    /* Page header compact */
    .page-header {
        padding: 6px 10px !important;
        margin-bottom: 4px !important;
    }
    .page-title {
        font-size: 16px !important;
    }
    .page-subtitle {
        font-size: 11px !important;
    }
    /* Breadcrumb compact */
    .breadcrumb-nav {
        padding: 4px 10px !important;
        margin-bottom: 4px !important;
    }
    /* Form sections very compact */
    .form-section {
        padding: 4px !important;
        margin-bottom: 2px !important;
    }
    .section-title {
        font-size: 12px !important;
        margin-bottom: 4px !important;
    }
    /* Buttons compact */
    .form-actions {
        margin-top: 4px !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 6px !important;
        width: 100% !important;
        flex-direction: row !important;
    }
    /* Buttons slightly larger */
    .form-actions .btn,
    .form-actions button.btn,
    .form-actions a.btn {
        padding: 6px 16px !important;
        font-size: 13px !important;
        height: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        line-height: 20px !important;
        width: auto !important;
        min-width: 100px !important;
        max-width: 140px !important;
        flex: 0 0 auto !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
    }
    /* Drop zone compact */
    .drop-zone {
        padding: 8px !important;
        margin-bottom: 4px !important;
    }
    .drop-zone-text {
        font-size: 11px !important;
    }
    .drop-zone-hint {
        font-size: 9px !important;
    }
    /* File list styling - show full items */
    .file-list {
        max-height: 120px !important;
        overflow-y: auto !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    .file-list::-webkit-scrollbar {
        display: none !important; /* Chrome, Safari, Opera */
    }
    .file-item {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 6px 8px !important;
        background: #f8fafc !important;
        border-radius: 4px !important;
        margin-bottom: 4px !important;
        width: 100% !important;
    }
    .file-icon {
        font-size: 16px !important;
        flex-shrink: 0 !important;
    }
    .file-details {
        flex: 1 !important;
        min-width: 0 !important;
    }
    .file-name {
        font-size: 11px !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .file-size {
        font-size: 9px !important;
        color: #64748b !important;
    }
    .file-actions {
        flex-shrink: 0 !important;
    }
    .btn-remove {
        background: none !important;
        border: none !important;
        color: #ef4444 !important;
        cursor: pointer !important;
        font-size: 12px !important;
        padding: 4px !important;
    }
    /* Hide any arrows/buttons in file list */
    .file-list .file-item > *:not(.file-icon):not(.file-details):not(.file-actions) {
        display: none !important;
    }
    /* Responsive Design */
    @media (max-width: 1400px) {
        .form-row:not([style*="padding"]) {
            grid-template-columns: repeat(6, 1fr) !important;
        }
    }
    @media (max-width: 1200px) {
        .form-row {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }
    @media (max-width: 992px) {
        .form-row {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 576px) {
        .form-row, .form-row-2, .form-row-3, .form-row-4 {
            grid-template-columns: repeat(1, 1fr) !important;
        }
    }
    @media (max-width: 480px) {
        .form-row, .form-row-2, .form-row-3, .form-row-4 {
            grid-template-columns: repeat(1, 1fr) !important;
        }
        .form-row .form-group,
        .form-row-2 .form-group,
        .form-row-3 .form-group,
        .form-row-4 .form-group {
            min-width: 0 !important;
        }
        .form-label {
            font-size: 10px !important;
            white-space: normal !important;
        }
        .form-control {
            font-size: 12px !important;
        }
    }
</style>

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.operations') }}" class="breadcrumb-item">
            العمليات
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">مطالبة جديدة</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-file-invoice-dollar"></i> إنشاء مطالبة جديدة</h1>
        @if($hospital && $department)
            <p class="page-subtitle">
                <span><i class="fa-solid fa-hospital"></i> {{ $hospital->name }}</span>
                <span style="margin: 0 8px;">•</span>
                <span><i class="fa-solid fa-stethoscope"></i> {{ $department->name }}</span>
            </p>
        @endif
    </div>

    <div class="form-container">
        <div class="form-card">
            <form action="{{ route('claims.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Row 1: All Basic Fields (8 fields) -->
                <div class="form-row form-section" style="padding: 8px !important; grid-template-columns: 0.9fr 0.6fr 0.7fr 0.9fr 0.9fr 0.9fr 1.2fr 1.2fr; gap: 8px !important;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-hashtag"></i> رقم المطالبة <span class="text-danger">*</span></label>
                        <input type="text" name="claim_number" class="form-control" value="{{ old('claim_number') }}" required placeholder="رقم المطالبة" style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('claim_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calculator"></i> عدد الفواتير</label>
                        <input type="number" name="invoice_count" class="form-control" min="1" value="{{ old('invoice_count') }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('invoice_count') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calendar"></i> الشهر</label>
                        <select name="month" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر</option>
                            <option value="يناير" {{ old('month') == 'يناير' ? 'selected' : '' }}>يناير</option>
                            <option value="فبراير" {{ old('month') == 'فبراير' ? 'selected' : '' }}>فبراير</option>
                            <option value="مارس" {{ old('month') == 'مارس' ? 'selected' : '' }}>مارس</option>
                            <option value="أبريل" {{ old('month') == 'أبريل' ? 'selected' : '' }}>أبريل</option>
                            <option value="مايو" {{ old('month') == 'مايو' ? 'selected' : '' }}>مايو</option>
                            <option value="يونيو" {{ old('month') == 'يونيو' ? 'selected' : '' }}>يونيو</option>
                            <option value="يوليو" {{ old('month') == 'يوليو' ? 'selected' : '' }}>يوليو</option>
                            <option value="أغسطس" {{ old('month') == 'أغسطس' ? 'selected' : '' }}>أغسطس</option>
                            <option value="سبتمبر" {{ old('month') == 'سبتمبر' ? 'selected' : '' }}>سبتمبر</option>
                            <option value="أكتوبر" {{ old('month') == 'أكتوبر' ? 'selected' : '' }}>أكتوبر</option>
                            <option value="نوفمبر" {{ old('month') == 'نوفمبر' ? 'selected' : '' }}>نوفمبر</option>
                            <option value="ديسمبر" {{ old('month') == 'ديسمبر' ? 'selected' : '' }}>ديسمبر</option>
                        </select>
                        @error('month') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calendar-days"></i> تاريخ المطالبة</label>
                        <input type="date" name="claim_date" class="form-control" value="{{ old('claim_date') }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('claim_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-coins"></i> قيمة المطالبة</label>
                        <input type="number" step="0.01" id="claimValue" name="claim_value" class="form-control" min="0" value="{{ old('claim_value') }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('claim_value') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-user-check"></i> اسم المراجع</label>
                        <input type="text" name="reviewer_name" class="form-control" value="{{ old('reviewer_name') }}" style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('reviewer_name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="hospital_id" id="hospitalSelect" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر المستشفى</option>
                            @php
                                $isWaitingListHospital = isset($hospital) && !is_numeric($hospital->id ?? '');
                                if ($isWaitingListHospital) {
                                    $hospitalId = $hospital->id ?? '';
                                } else {
                                    $hospitalId = old('hospital_id', $hospital->id ?? '');
                                }
                                $hospitalName = $hospital->name ?? '';
                                $hospitalFound = false;
                            @endphp
                            @foreach($allHospitals as $hosp)
                                @if($hospitalId == $hosp->id)
                                    @php $hospitalFound = true; @endphp
                                @endif
                                <option value="{{ $hosp->id }}" {{ ($hospitalId == $hosp->id) ? 'selected' : '' }}>{{ $hosp->name }}</option>
                            @endforeach
                            @if($hospitalId && !$hospitalFound)
                                <option value="{{ $hospitalId }}" selected>{{ $hospitalName ?: $hospitalId }}</option>
                            @endif
                        </select>
                        @error('hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر القسم</option>
                            @php
                                $isWaitingListDept = isset($department) && !is_numeric($department->id ?? '');
                                if ($isWaitingListDept) {
                                    $deptId = $department->id ?? '';
                                } else {
                                    $deptId = old('department_id', $department->id ?? '');
                                }
                                $deptName = $department->name ?? '';
                                $deptFound = false;
                            @endphp
                            @foreach($allDepartments as $dept)
                                @if($deptId == $dept->id)
                                    @php $deptFound = true; @endphp
                                @endif
                                <option value="{{ $dept->id }}" {{ ($deptId == $dept->id) ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                            @if($deptId && !$deptFound)
                                <option value="{{ $deptId }}" selected>{{ $deptName ?: $deptId }}</option>
                            @endif
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Row 2: Entity & Related Fields -->
                <div class="form-row form-section" style="padding: 8px !important; grid-template-columns: 1.5fr 1fr 1fr 1fr 1.5fr 0.9fr 0.9fr; gap: 8px !important;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-building"></i> الجهة</label>
                        <select name="entity_id" id="entitySelect" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                                @php
                                    $isSelected = (old('entity_id') == $entity->id) || (isset($selectedEntityId) && $selectedEntityId == $entity->id);
                                @endphp
                                <option value="{{ $entity->id }}" {{ $isSelected ? 'selected' : '' }} data-metadata='@json($entity->metadata)' data-selected="{{ $isSelected ? 'true' : 'false' }}">{{ $entity->name }}</option>
                            @endforeach
                        </select>
                        @error('entity_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" id="branchContainer" style="display: none; margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-layer-group"></i> الفروع</label>
                        <select name="branch" id="branchSelect" class="form-control select2" style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر الفرع</option>
                        </select>
                    </div>

                    <div class="form-group" id="subContainer" style="display: none; margin: 0;">
                        <label class="form-label" id="subLabel" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-map-marker-alt"></i> المحافظة</label>
                        <select name="location" id="subSelect" class="form-control select2" style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر</option>
                        </select>
                    </div>

                    <div class="form-group" id="lawsContainer" style="{{ $law ? 'display: block;' : 'display: none;' }}; margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-file-lines"></i> المستفيدين</label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2" style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر المستفيد</option>
                            @if($law)
                                <option value="{{ $law }}" selected>{{ $law }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" class="form-control" value="{{ old('electronic_invoice_no') }}" style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calendar-check"></i> تاريخ التسليم</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}" style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('delivery_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-upload"></i> مرفقات التسليم</label>
                        <input type="file" name="delivery_attachments[]" class="form-control" multiple style="height: 30px !important; font-size: 12px; padding: 4px 8px;">
                        <small class="text-muted" style="font-size: 9px;">ملفات متعددة</small>
                    </div>
                </div>

                <!-- Row 3: Notes & Attachments (Side by Side) -->
                <div class="form-row-2 form-section" style="padding: 6px !important; gap: 10px !important; margin-bottom: 4px !important;">
                    <!-- Notes -->
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 3px; padding: 2px 4px;">
                            <i class="fa-solid fa-note-sticky"></i> ملاحظات
                        </label>
                        <textarea name="notes" class="form-control" rows="2" style="min-height: 140px !important; height: 140px !important; font-size: 13px; padding: 6px 8px; resize: none;"
                            placeholder="ملاحظات..."></textarea>
                        @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <!-- Attachments -->
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 3px; padding: 2px 4px;">
                            <i class="fa-solid fa-paperclip"></i> المرفقات
                        </label>
                        <div class="drop-zone" id="dropZone" style="padding: 10px !important; margin-bottom: 0 !important; min-height: 140px !important; height: 140px !important; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <div class="drop-zone-content" style="display: flex; align-items: center; gap: 10px;">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 20px;"></i>
                                <p class="drop-zone-text" style="font-size: 13px; margin: 0;">اسحب الملفات هنا أو اضغط للاختيار</p>
                            </div>
                            <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                        </div>
                        <div class="file-list" id="fileList" style="max-height: 40px; overflow-y: auto;"></div>
                    </div>
                </div>

                {{-- Waiting Lists Fields (Inline) --}}
                @if($showWaitingListFields)
                <div class="form-row-2 form-section" style="padding: 4px 6px !important; gap: 8px !important; margin-bottom: 4px !important; border-top: 1px dashed #cbd5e1;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 11px; margin-bottom: 2px;">
                            <i class="fa-solid fa-file-text"></i> وصف المطالبة
                        </label>
                        <textarea name="claim_description" rows="1" style="min-height: 30px !important; height: 30px !important; font-size: 12px; padding: 4px 6px; resize: none;" placeholder="وصف..."></textarea>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 11px; margin-bottom: 2px;">
                            <i class="fa-solid fa-calendar-day"></i> تاريخ الفاتورة
                        </label>
                        <input type="date" name="electronic_invoice_date" style="height: 30px !important; font-size: 12px; padding: 4px 6px;">
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="form-actions" style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn btn-primary" id="saveBtn" style="height: 28px !important; font-size: 12px; padding: 4px 12px;">
                        <i class="fa-solid fa-save"></i> حفظ
                    </button>
                    <a href="{{ route('flow.operations') }}" class="btn btn-secondary" style="height: 28px !important; font-size: 12px; padding: 4px 12px;">
                        <i class="fa-solid fa-arrow-right"></i> عودة
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection



@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                dir: "rtl",
                width: '100%'
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Hospital & Department Logic
            const hospitalsData = @json($allHospitals);
            const allDepartmentsData = @json($allDepartments);
            const hospitalSelect = $('#hospitalSelect');
            const departmentSelect = $('#departmentSelect');

            console.log('All Departments Data:', allDepartmentsData);
            console.log('Hospitals Data:', hospitalsData);

            // Initial Values - check if numeric (DB ID) or string (waiting list)
            const initialHospitalId = "{{ old('hospital_id', $hospital->id ?? '') }}";
            const initialDepartmentId = "{{ old('department_id', $department->id ?? '') }}";
            const rawDepartmentId = "{{ $department->id ?? 'EMPTY' }}";
            const isWaitingListHospital = initialHospitalId && !$.isNumeric(initialHospitalId);

            console.log('=== INITIAL VALUES DEBUG ===');
            console.log('Initial Hospital ID:', initialHospitalId, 'Type:', typeof initialHospitalId);
            console.log('Initial Department ID:', initialDepartmentId, 'Type:', typeof initialDepartmentId);
            console.log('Raw Department ID from PHP:', rawDepartmentId);
            console.log('Is Waiting List Hospital:', isWaitingListHospital);
            console.log('Full $department object:', "{{ json_encode($department ?? null) }}");

            // Waiting list hospitals departments mapping (from waiting-list-options.blade.php)
            const waitingListHospitalDepartments = {
                'children': ['قسم الأطفال العام', 'قسم الأطفال حديثي الولادة', 'قسم الأطفال غير المستقر', 'قسم جراحة الأطفال'],
                'women': ['قسم النساء العام', 'قسم النساء الحوامل', 'قسم النساء غير المستقر', 'قسم جراحة النساء'],
                'ain_shams': ['قسم الباطنة', 'قسم الجراحة العامة', 'قسم النساء والتوليد', 'قسم الأطفال', 'قسم العظام', 'قسم المخ والأعصاب', 'قسم العيون', 'قسم الأنف والأذن والحنجرة', 'قسم السكتة الدماغية', 'قسم القلب', 'قسم الجهاز الهضمي', 'قسم الكلى', 'قسم الصدر'],
                'other': []
            };

            function populateDepartments(hospitalId, selectedDeptId = '') {
                console.log('=== POPULATE DEPARTMENTS CALLED ===');
                console.log('Hospital ID:', hospitalId, 'Selected Dept ID:', selectedDeptId);
                departmentSelect.empty().append('<option value="">اختر القسم</option>');

                // If it's a waiting list hospital (non-numeric), show specific departments for that hospital
                if (hospitalId && !$.isNumeric(hospitalId)) {
                    console.log('Waiting list hospital detected:', hospitalId);
                    
                    // Get departments for this waiting list hospital
                    const departments = waitingListHospitalDepartments[hospitalId] || [];
                    console.log('Departments for this hospital:', departments);
                    
                    if (departments.length > 0) {
                        console.log('Adding departments from mapping...');
                        // Add departments from the waiting list mapping
                        departments.forEach(deptName => {
                            const isSelected = selectedDeptId === deptName ? 'selected' : '';
                            console.log(`  Adding: ${deptName}, selected: ${isSelected}`);
                            departmentSelect.append(`<option value="${deptName}" ${isSelected}>${deptName}</option>`);
                        });
                    } else {
                        console.log('No mapping found, falling back to DB departments');
                        // Fallback: show all DB departments if no mapping found
                        allDepartmentsData.forEach(dept => {
                            const isSelected = selectedDeptId == dept.id ? 'selected' : '';
                            departmentSelect.append(`<option value="${dept.id}" ${isSelected}>${dept.name}</option>`);
                        });
                    }
                    
                    // If there's a selected department that doesn't exist in the list, add it
                    if (selectedDeptId && !$.isNumeric(selectedDeptId)) {
                        const exists = departments.includes(selectedDeptId);
                        console.log(`Selected dept "${selectedDeptId}" exists in list:`, exists);
                        if (!exists) {
                            console.log('Adding missing selected department');
                            departmentSelect.append(`<option value="${selectedDeptId}" selected>${selectedDeptId}</option>`);
                        }
                    }
                    
                    // Reinitialize Select2 to show the new options
                    console.log('Reinitializing Select2...');
                    departmentSelect.select2('destroy').select2({ dir: "rtl", width: '100%' });
                    
                    // Trigger change to update Select2
                    departmentSelect.trigger('change');
                    console.log('=== POPULATE DEPARTMENTS DONE ===');
                    return;
                }

                const hospital = hospitalsData.find(h => h.id == hospitalId);
                console.log('Found hospital:', hospital);
                if (hospital && hospital.departments) {
                    hospital.departments.forEach(dept => {
                        const isSelected = selectedDeptId == dept.id ? 'selected' : '';
                        departmentSelect.append(`<option value="${dept.id}" ${isSelected}>${dept.name}</option>`);
                    });
                }

                // Reinitialize Select2 after populating
                departmentSelect.select2('destroy').select2({ dir: "rtl", width: '100%' });
                
                if (selectedDeptId && $.isNumeric(selectedDeptId)) {
                    departmentSelect.val(selectedDeptId).trigger('change');
                }
            }

            // Store initial department for use in change event
            let pendingDepartmentSelection = initialDepartmentId;

            hospitalSelect.on('change', function () {
                const hospId = $(this).val();
                console.log('Hospital changed to:', hospId);
                console.log('Pending department selection:', pendingDepartmentSelection);
                // Small delay to ensure Select2 is ready
                setTimeout(() => {
                    populateDepartments(hospId, pendingDepartmentSelection);
                    // Clear pending selection after first use
                    pendingDepartmentSelection = '';
                }, 50);
            });

            // Set initial hospital and department
            if (initialHospitalId) {
                console.log('=== SETTING INITIAL HOSPITAL ===');
                console.log('Setting initial hospital:', initialHospitalId);
                console.log('Pending dept will be used by change event:', pendingDepartmentSelection);
                hospitalSelect.val(initialHospitalId).trigger('change');
                // The change event will handle populating departments with pendingDepartmentSelection
            } else {
                console.log('No initial hospital ID found!');
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            // Entity & Dynamic Fields Logic
            const entitySelect = $('#entitySelect');
            const branchContainer = $('#branchContainer');
            const branchSelect = $('#branchSelect');
            const subContainer = $('#subContainer');
            const subSelect = $('#subSelect');
            const subLabel = $('#subLabel');
            const lawsContainer = $('#lawsContainer');
            const lawsSelect = $('#lawsSelect');
            
            // Flow type from PHP session
            const flowType = '{{ $entityType ?? '' }}';
            const preselectedBranch = '{{ $branch ?? '' }}';
            const preselectedLocation = '{{ $location ?? '' }}';
            const preselectedLaw = '{{ $law ?? '' }}';
            
            console.log('Flow Type:', flowType);
            console.log('Preselected Branch:', preselectedBranch);
            console.log('Preselected Location:', preselectedLocation);
            console.log('Preselected Law:', preselectedLaw);

            function resetSub() {
                branchContainer.slideUp(300);
                subContainer.slideUp(300);
                lawsContainer.slideUp(300);
            }

            function fillBranchOptions(metadata, selectedBranch = '') {
                branchSelect.empty().append('<option value="">اختر الفرع</option>');

                // Special Case: Universal Health Insurance -> Skip Branches
                // Check name of selected entity
                const selectedEntityName = entitySelect.find('option:selected').text().trim();
                // Special Case: Universal Health Insurance (Detected by laws in metadata) -> Skip Branches
                if (metadata && metadata.laws) {
                    branchContainer.slideUp(300);
                    // If we skip branches, we must trigger the next step (Sub/Governorates) directly.
                    // But fillSubOptions usually expects a branchVal. 
                    // However, if we skip branches, logic implies we go straight to governorates using metadata.
                    // We can pass a dummy value or modify fillSubOptions to not require branchVal if it's this entity.
                    fillSubOptions(metadata, 'SKIP_BRANCH', '{{ old("location") }}');
                    return;
                }

                if (metadata?.branches?.length) {
                    metadata.branches.forEach(branch => {
                        branchSelect.append(`<option value="${branch}">${branch}</option>`);
                    });
                    branchContainer.slideDown(300);
                    if (selectedBranch) branchSelect.val(selectedBranch).trigger('change');
                } else {
                    branchContainer.slideUp(300);
                    // If no branches, try to show next level
                    fillSubOptions(metadata, 'NO_BRANCH', '{{ old("location") }}');
                }
            }

            function fillSubOptions(metadata, branchVal, selectedSub = '') {
                subSelect.empty().append('<option value="">اختر الاختيار</option>');
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                lawsContainer.slideUp(300);

                // Allow processing if branchVal is a special indicator (SKIP_BRANCH, NO_BRANCH)
                // or if it's a valid branch value.
                if (!branchVal && branchVal !== 'SKIP_BRANCH' && branchVal !== 'NO_BRANCH') return;

                if (!metadata) {
                    subContainer.slideUp(300);
                    return;
                }

                let subItems = [];
                let labelText = 'المحافظات / المواقع';

                if (metadata.laws) { // التأمين الصحي الشامل
                    subItems = metadata.governorates || [];
                    labelText = 'المحافظات';
                } else if (metadata.governorates) {
                    subItems = metadata.governorates;
                    labelText = 'المحافظات';
                } else if (metadata.locations) {
                    subItems = metadata.locations;
                    labelText = 'المواقع';
                }

                if (subItems.length) {
                    subItems.forEach(item => subSelect.append(`<option value="${item}">${item}</option>`));
                    subLabel.text(labelText);
                    subContainer.slideDown(300);
                    if (selectedSub) subSelect.val(selectedSub).trigger('change');
                } else {
                    subContainer.slideUp(300);
                }
            }

            function fillLawsOptions(metadata, subVal, selectedLaw = '') {
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                if (!metadata.laws || !subVal) {
                    lawsContainer.slideUp(300);
                    return;
                }

                metadata.laws.forEach(item => lawsSelect.append(`<option value="${item}">${item}</option>`));
                lawsContainer.slideDown(300);
                if (selectedLaw) lawsSelect.val(selectedLaw).trigger('change');
            }

            // entitySelect.on('change', function () {
            //     const selectedOption = $(this).find('option:selected');
            //     const metadata = selectedOption.data('metadata');

            //     // Fill Branches (or skip if Universal Health)
            //     fillBranchOptions(metadata);
            //     // resetSub is called inside fillBranchOptions indirectly if we show branches, 
            //     // but if we skip, we call fillSubOptions directly.
            //     // Actually resetSub was doing cleanup. Let's keep it safe.
            //     // resetSub(); // Removed because fillBranchOptions handles flow
            // });



            entitySelect.on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const metadata = selectedOption.data('metadata');

                // Reset all containers
                branchContainer.hide();
                subContainer.hide();
                lawsContainer.hide();

                if (!metadata) return;

                // === لو الهيئة العامة للتأمين الصحي أو التأمين الصحي الشامل ===
                if (metadata.laws) {
                    // 1. Fill Branches (فروع / قوائم انتظار)
                    if (metadata.branches && metadata.branches.length > 0) {
                        branchSelect.empty().append('<option value="">اختر الفرع</option>');
                        metadata.branches.forEach(branch => {
                            branchSelect.append(`<option value="${branch}">${branch}</option>`);
                        });
                        branchSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                        branchContainer.show();
                    } else {
                        // لو مفيش فروع، نعرض المحافظة مباشرة
                        let subItems = metadata.governorates || [];
                        if (subItems.length > 0) {
                            subSelect.empty().append('<option value="">اختر المحافظة</option>');
                            subItems.forEach(item => {
                                subSelect.append(`<option value="${item}">${item}</option>`);
                            });
                            subSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                            subLabel.text('المحافظات');
                            subContainer.show();
                        }
                    }
                } else {
                    // === باقي الحالات العادية (غير التأمين الصحي الشامل) ===
                    if (metadata.branches && metadata.branches.length > 0) {
                        branchSelect.empty().append('<option value="">اختر الفرع</option>');
                        metadata.branches.forEach(branch => {
                            branchSelect.append(`<option value="${branch}">${branch}</option>`);
                        });
                        branchSelect.select2('destroy').select2({ dir: "rtl", width: '100%' });
                        branchContainer.show();
                    } else {
                        branchContainer.hide();
                        let subItems = metadata.locations || metadata.governorates || [];
                        if (subItems.length > 0) {
                            subSelect.empty().append('<option value="">اختر المحافظة / الموقع</option>');
                            subItems.forEach(item => {
                                subSelect.append(`<option value="${item}">${item}</option>`);
                            });
                            subSelect.select2('destroy').select2({ dir: "rtl", width: '100%' });
                            subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                            subContainer.show();
                        } else {
                            subContainer.hide();
                        }
                    }
                }
            });

            // مستمع الفرع → يعرض المحافظة
            branchSelect.on('change', function () {
                const selectedBranch = $(this).val();
                const metadata = entitySelect.find('option:selected').data('metadata');
                
                if (!selectedBranch || !metadata) {
                    subContainer.hide();
                    return;
                }

                let subItems = metadata.locations || metadata.governorates || [];
                if (!subItems.length) {
                    subContainer.hide();
                    return;
                }

                subSelect.empty().append('<option value="">اختر المحافظة / الموقع</option>');
                subItems.forEach(item => {
                    subSelect.append(`<option value="${item}">${item}</option>`);
                });
                subSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                subContainer.show();
            });

            // مستمع المحافظة → يعرض المستفيدين
            subSelect.on('change', function () {
                const selectedSub = $(this).val();
                const metadata = entitySelect.find('option:selected').data('metadata');
                
                if (!selectedSub || !metadata || !metadata.laws) {
                    lawsContainer.hide();
                    return;
                }

                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                metadata.laws.forEach(law => {
                    lawsSelect.append(`<option value="${law}">${law}</option>`);
                });
                lawsSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                lawsContainer.show();
            });

            // Trigger entity change on page load if entity is pre-selected
            const initialEntityId = "{{ old('entity_id', $selectedEntityId ?? '') }}";
            console.log('Initial Entity ID from PHP:', initialEntityId);
            console.log('Preselected values:', { branch: preselectedBranch, location: preselectedLocation, law: preselectedLaw });
            
            if (initialEntityId) {
                console.log('Setting entity value to:', initialEntityId);
                entitySelect.val(initialEntityId).trigger('change');
                
                // Get metadata for the selected entity
                const selectedOption = entitySelect.find('option:selected');
                const metadata = selectedOption.data('metadata');
                
                console.log('Selected entity metadata:', metadata);
                
                if (metadata) {
                    // Wait for the change event to populate the dropdowns
                    setTimeout(() => {
                        // Case 1: Entity has branches (e.g., Ministry flow)
                        if (preselectedBranch && metadata.branches && metadata.branches.includes(preselectedBranch)) {
                            console.log('Setting branch to:', preselectedBranch);
                            branchSelect.val(preselectedBranch).trigger('change');
                            
                            // After branch change, wait for locations to populate
                            setTimeout(() => {
                                handleLocationAndLaw(metadata, preselectedLocation, preselectedLaw);
                            }, 300);
                        } 
                        // Case 2: Entity has NO branches but has locations/governorates (e.g., Insurance, Comprehensive)
                        else if (preselectedLocation && !preselectedBranch) {
                            console.log('No branch, handling location directly');
                            handleLocationAndLaw(metadata, preselectedLocation, preselectedLaw);
                        }
                        // Case 3: Waiting Lists - has law but no location (e.g., Insurance waiting lists)
                        else if (preselectedLaw && metadata.laws && metadata.laws.includes(preselectedLaw)) {
                            console.log('Waiting list - populating law without location');
                            populateLawsOnly(metadata, preselectedLaw);
                        }
                    }, 300);
                }
            }
            
            // Helper function to populate laws only (for waiting lists)
            function populateLawsOnly(metadata, lawVal) {
                if (!metadata.laws || metadata.laws.length === 0) return;
                
                console.log('Populating laws only (waiting list):', metadata.laws);
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                metadata.laws.forEach(law => {
                    const isSelected = (law === lawVal) ? 'selected' : '';
                    lawsSelect.append(`<option value="${law}" ${isSelected}>${law}</option>`);
                });
                lawsSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                lawsContainer.show();
                console.log('Laws populated for waiting list with selected:', lawVal);
            }
            
            // Helper function to set location and then law
            function handleLocationAndLaw(metadata, locationVal, lawVal) {
                // For waiting lists with law but no location, handle differently
                if (!locationVal && lawVal && metadata.laws && metadata.laws.includes(lawVal)) {
                    console.log('No location but has law - using waiting list logic');
                    populateLawsOnly(metadata, lawVal);
                    return;
                }
                
                if (!locationVal) return;
                
                // First populate and show the location dropdown
                let subItems = metadata.governorates || metadata.locations || [];
                if (subItems.length > 0) {
                    subSelect.empty().append('<option value="">اختر المحافظة / الموقع</option>');
                    subItems.forEach(item => {
                        const isSelected = (item === locationVal) ? 'selected' : '';
                        subSelect.append(`<option value="${item}" ${isSelected}>${item}</option>`);
                    });
                    subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                    subSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                    subContainer.show();
                    console.log('Location dropdown populated and shown');
                }
                
                // Check if location exists in dropdown
                const locationExists = subSelect.find('option[value="' + locationVal + '"]').length > 0;
                console.log('Location exists in dropdown:', locationExists, 'Value:', locationVal);
                
                if (locationExists) {
                    // Set the location value
                    subSelect.val(locationVal);
                    console.log('Location set to:', locationVal);
                    
                    // For entities with laws (Insurance), populate laws
                    if (metadata.laws && metadata.laws.length > 0) {
                        console.log('Populating laws from metadata:', metadata.laws);
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        metadata.laws.forEach(law => {
                            const isSelected = (law === lawVal) ? 'selected' : '';
                            lawsSelect.append(`<option value="${law}" ${isSelected}>${law}</option>`);
                        });
                        lawsSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                        lawsContainer.show();
                        console.log('Laws populated and container shown with selected:', lawVal);
                    }
                    
                    // Trigger change on subSelect ONLY if no laws (to avoid overwriting law selection)
                    if (!metadata.laws || metadata.laws.length === 0) {
                        subSelect.trigger('change');
                    }
                } else {
                    console.log('Location not found in dropdown, options are:', subSelect.find('option').map(function() { return $(this).val(); }).get());
                }
            }
        });
    </script>

    <script>
        function calculateDiff() {
            const claim = parseFloat(document.getElementById('claimValue').value) || 0;
            const reviewed = parseFloat(document.getElementById('reviewedValue').value) || 0;
            document.getElementById('differenceValue').value = (reviewed - claim).toFixed(2);
        }

        // Attachment Management
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        let selectedFiles = [];

        dropZone.onclick = () => fileInput.click();

        fileInput.onchange = (e) => handleFiles(e.target.files);

        dropZone.ondragover = (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        };

        dropZone.ondragleave = () => dropZone.classList.remove('dragover');

        dropZone.ondrop = (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        };

        function handleFiles(files) {
            Array.from(files).forEach(file => {
                // Check if file already added
                if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) return;

                selectedFiles.push(file);
                const fileItem = createFileItem(file);
                fileList.appendChild(fileItem);

                // Simulate progression
                simulateProgress(fileItem);
            });
            syncInput();
        }

        function createFileItem(file) {
            const div = document.createElement('div');
            div.className = 'file-item';
            div.setAttribute('data-name', file.name);

            const extension = file.name.split('.').pop().toLowerCase();
            let icon = 'fa-file-lines';
            let color = '#3b82f6'; // Default blue

            if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                icon = 'fa-file-image';
                color = '#10b981'; // Green
            } else if (extension === 'pdf') {
                icon = 'fa-file-pdf';
                color = '#ef4444'; // Red
            } else if (['xls', 'xlsx', 'csv'].includes(extension)) {
                icon = 'fa-file-excel';
                color = '#059669'; // Dark Green
            }

            div.innerHTML = `
                <div class="file-icon" style="color: ${color};">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <div class="file-details">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size"><i class="fa-solid fa-hard-drive" style="font-size: 10px;"></i> ${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                    <div class="progress-container" style="display: block;">
                        <div class="progress-bar"></div>
                    </div>
                </div>
                <div class="file-actions">
                    <button type="button" class="btn-remove" title="حذف وإلغاء">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;

            div.querySelector('.btn-remove').onclick = () => {
                if (div.uploadInterval) clearInterval(div.uploadInterval);
                selectedFiles = selectedFiles.filter(f => f !== file);
                div.style.transition = 'all 0.3s ease';
                div.style.opacity = '0';
                div.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    div.remove();
                    syncInput();
                }, 300);
            };

            return div;
        }

        function simulateProgress(fileItem) {
            const bar = fileItem.querySelector('.progress-bar');
            const container = fileItem.querySelector('.progress-container');
            let width = 0;

            // Disable save button while uploading
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.style.opacity = '0.7';
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحميل...';

            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        container.style.transition = 'all 0.5s ease';
                        container.style.opacity = '0';
                        setTimeout(() => {
                            container.style.display = 'none';
                            // Check if all files are done
                            const remainingBars = document.querySelectorAll('.progress-container[style*="display: block"]');
                            if (remainingBars.length === 0) {
                                saveBtn.disabled = false;
                                saveBtn.style.opacity = '1';
                                saveBtn.innerHTML = '<i class="fa-solid fa-save"></i> حفظ المطالبة';
                            }
                        }, 500);
                    }, 500);
                } else {
                    width += Math.random() * 10;
                    if (width > 100) width = 100;
                    bar.style.width = width + '%';
                }
            }, 150);

            // Store interval on item for cancellation
            fileItem.uploadInterval = interval;
        }

        function syncInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;

            // Reset save button if no files uploading
            const remainingBars = document.querySelectorAll('.progress-container[style*="display: block"]');
            if (remainingBars.length === 0) {
                const saveBtn = document.getElementById('saveBtn');
                saveBtn.disabled = false;
                saveBtn.style.opacity = '1';
                saveBtn.innerHTML = '<i class="fa-solid fa-save"></i> حفظ المطالبة';
            }
        }
    </script>
@endsection