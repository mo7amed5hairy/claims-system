@extends('claims::layouts.app')

@section('title', 'تعديل مطالبة')

<style>
    /* Remove side margins from form container */
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
    /* 9 columns per row for main fields */
    .form-row {
        display: grid;
        grid-template-columns: repeat(9, 1fr) !important;
        gap: 4px !important;
        margin-bottom: 4px !important;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 4px !important;
        margin-bottom: 4px !important;
    }
    .form-row-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 4px !important;
        margin-bottom: 4px !important;
    }
    .form-row-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 4px !important;
        margin-bottom: 4px !important;
    }
    .form-group {
        margin-bottom: 2px !important;
    }
    /* Uniform labels - final size increase */
    .form-label {
        font-size: 13px !important;
        margin-bottom: 4px !important;
        padding: 3px 6px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Uniform inputs and selects - final increase */
    .form-control {
        height: 34px !important;
        padding: 5px 10px !important;
        font-size: 14px !important;
        min-height: 34px !important;
    }
    /* Select2 - match new height */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 34px !important;
        min-height: 34px !important;
        max-height: 34px !important;
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
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
        width: 24px !important;
        top: 0 !important;
        right: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-width: 5px !important;
        margin-top: -2px !important;
    }
    .select2-dropdown {
        font-size: 14px !important;
        border-radius: 4px !important;
    }
    .select2-results__option {
        padding: 6px 12px !important;
        font-size: 14px !important;
        min-height: 26px !important;
        line-height: 22px !important;
    }
    /* Notes textarea - final size */
    textarea.form-control {
        min-height: 100px !important;
        height: 100px !important;
        font-size: 14px !important;
    }
    /* Error messages - final size */
    .error-message {
        font-size: 11px !important;
    }
    /* Small text - final size */
    small.text-muted {
        font-size: 10px !important;
        display: block;
        line-height: 1.2;
    }
    /* Page header - final size */
    .page-header {
        padding: 10px 15px !important;
        margin-bottom: 6px !important;
    }
    .page-title {
        font-size: 18px !important;
    }
    .page-subtitle {
        font-size: 13px !important;
    }
    /* Breadcrumb - final size */
    .breadcrumb-nav {
        padding: 8px 15px !important;
        margin-bottom: 6px !important;
    }
    /* Form sections compact */
    .form-section {
        padding: 10px !important;
        margin-bottom: 8px !important;
    }
    .section-title {
        font-size: 14px !important;
        margin-bottom: 10px !important;
    }
    /* Form actions - buttons on right - AGGRESSIVE OVERRIDE */
    .form-actions {
        margin-top: 10px !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 8px !important;
        width: 100% !important;
        flex-direction: row !important;
    }
    .form-actions .btn,
    .form-actions button.btn,
    .form-actions a.btn {
        padding: 5px 14px !important;
        font-size: 12px !important;
        height: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        line-height: 22px !important;
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
        padding: 20px !important;
        margin-bottom: 10px !important;
    }
    .drop-zone-text {
        font-size: 14px !important;
    }
    .drop-zone-hint {
        font-size: 12px !important;
    }
    /* Responsive Design */
    @media (max-width: 1400px) {
        .form-row {
            grid-template-columns: repeat(6, 1fr) !important;
        }
        .form-row-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .form-row-3 {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        .form-row-4 {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }
    @media (max-width: 992px) {
        .form-row {
            grid-template-columns: repeat(4, 1fr) !important;
        }
        .form-row-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .form-row-3 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .form-row-4 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .form-label {
            font-size: 11px !important;
        }
    }
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        .form-row-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .form-row-3 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .form-row-4 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .page-title {
            font-size: 16px !important;
        }
    }
    @media (max-width: 576px) {
        .form-row, .form-row-2, .form-row-3, .form-row-4 {
            grid-template-columns: repeat(1, 1fr) !important;
        }
        .form-control, .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 13px !important;
        }
        .form-label {
            font-size: 11px !important;
        }
        .form-actions {
            flex-direction: column !important;
            gap: 6px !important;
        }
        .form-actions .btn {
            width: 100% !important;
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
        <a href="{{ route('prepaid-claims.index') }}" class="breadcrumb-item">
            مطالبات الدفع المسبق
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">تعديل مطالبة</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-edit"></i> تعديل مطالبة <span
                style="color: var(--primary-color);">#{{ $claim->id }}</span></h1>
    </div>

    <div class="form-container">
        <div class="form-card">
            <form action="{{ route('prepaid-claims.update', $claim->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="claim_number" value="{{ $claim->claim_number }}">

                <!-- Main Fields Row 1 (9 columns) -->
                <div class="form-row form-section" style="padding: 8px !important;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="hospital_id" id="hospitalSelect" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر المستشفى</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}" {{ (old('hospital_id', $claim->hospital_id) == $hosp->id) ? 'selected' : '' }}>{{ $hosp->name }}</option>
                            @endforeach
                            @if($claim->hospital_id && !is_numeric($claim->hospital_id))
                                <option value="{{ $claim->hospital_id }}" selected>{{ $hospitalNames[$claim->hospital_id] ?? $claim->hospital_id }}</option>
                            @endif
                        </select>
                        @error('hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر القسم</option>
                            @if($claim->department_id && !is_numeric($claim->department_id))
                                <option value="{{ $claim->department_id }}" selected>{{ $claim->department_id }}</option>
                            @endif
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calculator"></i> عدد الفواتير</label>
                        <input type="number" name="invoice_count" class="form-control" min="1" value="{{ old('invoice_count', $claim->invoice_count) }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('invoice_count') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calendar"></i> الشهر</label>
                        <select name="month" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر</option>
                            @foreach(['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] as $m)
                                <option value="{{ $m }}" {{ old('month', $claim->month) == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('month') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calendar-days"></i> تاريخ المطالبة</label>
                        <input type="date" name="claim_date" class="form-control" value="{{ old('claim_date', $claim->claim_date->format('Y-m-d')) }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('claim_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-coins"></i> قيمة المطالبة</label>
                        <input type="number" step="0.01" id="claimValue" name="claim_value" class="form-control" min="0" value="{{ old('claim_value', $claim->claim_value) }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('claim_value') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" class="form-control" value="{{ old('electronic_invoice_no', $claim->electronic_invoice_no) }}" required style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-calendar-check"></i> تاريخ التسليم</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $claim->delivery_date ? $claim->delivery_date->format('Y-m-d') : '') }}" style="height: 30px !important; font-size: 13px; padding: 4px 8px;">
                        @error('delivery_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin: 0;"></div>
                </div>

                <!-- Entity Fields Row (9 columns) -->
                <div class="form-row form-section" style="padding: 8px !important;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-building"></i> الجهة</label>
                        <select name="entity_id" id="entitySelect" class="form-control select2" required style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)' {{ old('entity_id', $claim->entity_id) == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
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

                    <div class="form-group" id="lawsContainer" style="{{ $claim->beneficiary ? 'display: block;' : 'display: none;' }}; margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-file-lines"></i> المستفيدين</label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2" style="height: 30px !important; font-size: 13px;">
                            <option value="">اختر المستفيد</option>
                            @if($claim->beneficiary)
                                <option value="{{ $claim->beneficiary }}" selected>{{ $claim->beneficiary }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 2px;"><i class="fa-solid fa-upload"></i> مرفقات التسليم</label>
                        <input type="file" name="delivery_attachments[]" class="form-control" multiple style="height: 30px !important; font-size: 12px; padding: 4px 8px;">
                        <small class="text-muted" style="font-size: 9px;">ملفات متعددة</small>
                    </div>

                    <div class="form-group" style="margin: 0;"></div>
                </div>

                <!-- Notes Section (Full Width) -->
                <div class="form-section" style="padding: 8px !important;">
                    <div class="form-group" style="margin-bottom: 4px;">
                        <label class="form-label" style="font-size: 13px; margin-bottom: 4px;"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" style="min-height: 80px; height: 80px; font-size: 14px; resize: vertical;">{{ old('notes', $claim->notes) }}</textarea>
                        @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Existing Attachments Section -->
                @if($claim->attachments && count($claim->attachments) > 0)
                    <div class="form-section" style="padding: 10px !important;">
                        <h3 class="section-title" style="font-size: 13px; margin-bottom: 8px;">
                            <i class="fa-solid fa-folder-open"></i> المرفقات الحالية
                        </h3>
                        <p style="font-size: 12px; color: #64748b; margin-bottom: 10px;">
                            <i class="fa-solid fa-info-circle"></i> يمكنك الإبقاء عليها أو إضافة مرفقات جديدة لاستبدالها.
                        </p>
                        <div class="file-list" style="margin-top: 8px;">
                            @foreach($claim->attachments as $path)
                                @php
                                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                                    $icon = 'fa-file-lines';
                                    $typeClass = 'icon-default';
                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) { $icon = 'fa-file-image'; $typeClass = 'icon-image'; }
                                    elseif ($ext == 'pdf') { $icon = 'fa-file-pdf'; $typeClass = 'icon-pdf'; }
                                    elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'fa-file-excel'; $typeClass = 'icon-excel'; }
                                @endphp
                                <div class="file-item" style="padding: 6px 10px; margin-bottom: 4px; font-size: 12px;">
                                    <div class="file-icon {{ $typeClass }}">
                                        <i class="fa-solid {{ $icon }}"></i>
                                    </div>
                                    <div class="file-details">
                                        <div class="file-name">{{ basename($path) }}</div>
                                    </div>
                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" class="attachment-badge" style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 6px; font-size: 11px; text-decoration: none;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Attachments Section -->
                <div class="form-section" style="padding: 10px !important;">
                    <h3 class="section-title" style="font-size: 13px; margin-bottom: 8px;">
                        <i class="fa-solid fa-paperclip"></i> إضافة مرفقات جديدة (استبدال)
                    </h3>
                    <div class="drop-zone" id="dropZone" style="padding: 15px !important; margin-bottom: 8px;">
                        <div class="drop-zone-content">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px;"></i>
                            <p class="drop-zone-text" style="font-size: 13px; margin: 5px 0;">اسحب وأفلت الملفات هنا لاستبدال المرفقات</p>
                            <p class="drop-zone-hint" style="font-size: 11px; margin: 0;">تحميل ملفات جديدة سيؤدي إلى حذف الملفات القديمة تلقائياً</p>
                        </div>
                        <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                    </div>
                    <div class="file-list" id="fileList"></div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions" style="margin-top: 8px;">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fa-solid fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('prepaid-claims.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-right"></i> الغاء
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
            // Select2 auto-close handled in app.blade.php globally

            // Hospital & Department Logic
            const hospitalsData = @json($allHospitals);
            const allDepartmentsData = @json($allDepartments ?? []);
            const hospitalSelect = $('#hospitalSelect');
            const departmentSelect = $('#departmentSelect');
            
            // Hospital name mapping for waiting list hospitals
            const hospitalNames = {
                'ain_shams': 'مستشفى عين شمس',
                'children': 'مستشفى الأطفال',
                'women': 'مستشفى النساء',
                'other': 'أخرى'
            };
            
            // Waiting list hospitals departments mapping
            const waitingListHospitalDepartments = {
                'children': ['قسم الأطفال العام', 'قسم الأطفال حديثي الولادة', 'قسم الأطفال غير المستقر', 'قسم جراحة الأطفال'],
                'women': ['قسم النساء العام', 'قسم النساء الحوامل', 'قسم النساء غير المستقر', 'قسم جراحة النساء'],
                'ain_shams': ['قسم الباطنة', 'قسم الجراحة العامة', 'قسم النساء والتوليد', 'قسم الأطفال', 'قسم العظام', 'قسم المخ والأعصاب', 'قسم العيون', 'قسم الأنف والأذن والحنجرة', 'قسم السكتة الدماغية', 'قسم القلب', 'قسم الجهاز الهضمي', 'قسم الكلى', 'قسم الصدر'],
                'other': []
            };
            
            // Initial Values
            const initialHospitalId = "{{ old('hospital_id', $claim->hospital_id) }}";
            const initialDepartmentId = "{{ old('department_id', $claim->department_id) }}";
            const isWaitingListHospital = initialHospitalId && !$.isNumeric(initialHospitalId);

            function populateDepartments(hospitalId, selectedDeptId = '') {
                departmentSelect.empty().append('<option value="">اختر القسم</option>');
                
                // If it's a waiting list hospital (non-numeric), show specific departments
                if (hospitalId && !$.isNumeric(hospitalId)) {
                    const departments = waitingListHospitalDepartments[hospitalId] || [];
                    
                    if (departments.length > 0) {
                        departments.forEach(deptName => {
                            const isSelected = selectedDeptId === deptName ? 'selected' : '';
                            departmentSelect.append(`<option value="${deptName}" ${isSelected}>${deptName}</option>`);
                        });
                    } else {
                        // Fallback: show all DB departments
                        allDepartmentsData.forEach(dept => {
                            const isSelected = selectedDeptId == dept.id ? 'selected' : '';
                            departmentSelect.append(`<option value="${dept.id}" ${isSelected}>${dept.name}</option>`);
                        });
                    }
                    
                    // If there's a selected department that doesn't exist in the list, add it
                    if (selectedDeptId && !$.isNumeric(selectedDeptId)) {
                        const exists = departments.includes(selectedDeptId);
                        if (!exists) {
                            departmentSelect.append(`<option value="${selectedDeptId}" selected>${selectedDeptId}</option>`);
                        }
                    }
                    
                    // Trigger change to update any dependent fields
                    departmentSelect.trigger('change');
                    return;
                }
                
                // Regular DB hospital
                const hospital = hospitalsData.find(h => h.id == hospitalId);
                if (hospital && hospital.departments) {
                    hospital.departments.forEach(dept => {
                        const isSelected = selectedDeptId == dept.id ? 'selected' : '';
                        departmentSelect.append(`<option value="${dept.id}" ${isSelected}>${dept.name}</option>`);
                    });
                }
                
                if (selectedDeptId && $.isNumeric(selectedDeptId)) {
                    departmentSelect.val(selectedDeptId).trigger('change');
                }
            }

            hospitalSelect.on('change', function() {
                const hospId = $(this).val();
                populateDepartments(hospId);
            });

            // Set initial hospital and department
            if (initialHospitalId) {
                populateDepartments(initialHospitalId, initialDepartmentId);
            }


            const entitySelect = $('#entitySelect');
            const branchContainer = $('#branchContainer');
            const branchSelect = $('#branchSelect');
            const subContainer = $('#subContainer');
            const subSelect = $('#subSelect');
            const subLabel = $('#subLabel');
            const lawsContainer = $('#lawsContainer');
            const lawsSelect = $('#lawsSelect');

                subContainer.slideUp(300);
                lawsContainer.slideUp(300);

            function fixMetadata(metadata, entityName) {
                if (!metadata) return null;
                // Forcefully ensure "التأمين الصحي" entities have all 3 levels
                if (entityName.includes('التأمين الصحي')) {
                    if (!metadata.laws || metadata.laws.length === 0) {
                        metadata.laws = ['طلبة', 'مواليد', 'منتفعين', 'امرأة معيلة'];
                    }
                    if (!metadata.branches || metadata.branches.length === 0) {
                        metadata.branches = ['فروع', 'قوائم انتظار'];
                    }
                    if (!metadata.locations || metadata.locations.length === 0) {
                        metadata.locations = metadata.governorates || ['القاهرة', 'الجيزة', 'رئاسة الحي', 'القليوبية', 'الأقصر', 'الإسماعيلية', 'بورسعيد'];
                    }
                }
                return metadata;
            }

            function fillBranchOptions(metadata, selectedBranch = '') {
                branchSelect.empty().append('<option value="">اختر الفرع</option>');
                
                if (metadata && metadata.branches && metadata.branches.length > 0) {
                    metadata.branches.forEach(branch => {
                        branchSelect.append(`<option value="${branch}">${branch}</option>`);
                    });
                    branchContainer.slideDown(300);
                    if (selectedBranch) branchSelect.val(selectedBranch).trigger('change');
                } else {
                    branchContainer.slideUp(300);
                    fillSubOptions(metadata, 'NO_BRANCH', '{{ old("location", $claim->location) }}');
                }
            }

            function fillSubOptions(metadata, branchVal, selectedSub = '') {
                subSelect.empty().append('<option value="">اختر الاختيار</option>');
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                lawsContainer.slideUp(300);

                if (!branchVal && branchVal !== 'NO_BRANCH') return;
                if (!metadata) {
                    subContainer.slideUp(300);
                    return;
                }

                let subItems = metadata.locations || metadata.governorates || [];
                let labelText = metadata.locations ? 'المواقع' : 'المحافظات';

                if (subItems.length > 0) {
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
                if (!metadata || !metadata.laws || !metadata.laws.length || !subVal) {
                    lawsContainer.slideUp(300);
                    return;
                }

                metadata.laws.forEach(item => lawsSelect.append(`<option value="${item}">${item}</option>`));
                lawsContainer.slideDown(300);
                if (selectedLaw) lawsSelect.val(selectedLaw).trigger('change');
            }

            entitySelect.on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const entityName = selectedOption.text().trim();
                let metadata = selectedOption.data('metadata');
                
                metadata = fixMetadata(metadata, entityName);
                selectedOption.data('metadata', metadata); // Cache the fix
                
                fillBranchOptions(metadata);
            });

            branchSelect.on('change', function () {
                const branchVal = $(this).val() || '';
                const selectedOption = entitySelect.find('option:selected');
                const metadata = fixMetadata(selectedOption.data('metadata'), selectedOption.text().trim());
                fillSubOptions(metadata, branchVal);
            });

            subSelect.on('change', function () {
                const subVal = $(this).val() || '';
                const selectedOption = entitySelect.find('option:selected');
                const metadata = fixMetadata(selectedOption.data('metadata'), selectedOption.text().trim());
                fillLawsOptions(metadata, subVal);
            });

            // ==== تهيئة الصفحة عند التحميل ====
            const initialEntity = '{{ old("entity_id", $claim->entity_id) }}';
            const initialBranch = '{{ old("branch", $claim->branch) }}';
            const initialSub = '{{ old("location", $claim->location) }}';
            const initialLaw = '{{ old("beneficiary", $claim->beneficiary) }}';

            console.log('=== EDIT FORM INITIALIZATION ===');
            console.log('Entity:', initialEntity);
            console.log('Branch:', initialBranch);
            console.log('Sub:', initialSub);
            console.log('Law:', initialLaw);

            if (initialEntity) {
                entitySelect.val(initialEntity);
                const selectedOption = entitySelect.find('option:selected');
                let metadata = selectedOption.data('metadata');
                const entityName = selectedOption.text().trim();
                
                if (selectedOption.length) {
                    metadata = fixMetadata(metadata, entityName);
                    selectedOption.data('metadata', metadata);

                    // Case 3: Has law but no location (Waiting lists) - CHECK FIRST
                    if (initialLaw && metadata.laws && metadata.laws.includes(initialLaw) && !initialSub) {
                        console.log('Case 3: Waiting list flow - showing laws directly');
                        // For waiting lists, populate laws directly
                        branchContainer.slideUp(300);
                        subContainer.slideUp(300);
                        
                        // Show laws container and populate
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        metadata.laws.forEach(item => {
                            const isSelected = item === initialLaw ? 'selected' : '';
                            lawsSelect.append(`<option value="${item}" ${isSelected}>${item}</option>`);
                        });
                        lawsContainer.slideDown(300);
                        
                        // Set the value explicitly
                        lawsSelect.val(initialLaw);
                    }
                    // Case 1: Has branch (Ministry flow)
                    else if (initialBranch && metadata.branches && metadata.branches.includes(initialBranch)) {
                        console.log('Case 1: Ministry flow with branch');
                        fillBranchOptions(metadata, initialBranch);
                        setTimeout(() => {
                            fillSubOptions(metadata, initialBranch, initialSub);
                            if (initialSub) {
                                setTimeout(() => {
                                    fillLawsOptions(metadata, initialSub, initialLaw);
                                }, 100);
                            }
                        }, 100);
                    }
                    // Case 2: No branch but has location (Insurance, Comprehensive)
                    else if (initialSub && !initialBranch) {
                        console.log('Case 2: Insurance/Comprehensive flow');
                        branchContainer.slideUp(300);
                        fillSubOptions(metadata, 'NO_BRANCH', initialSub);
                        if (initialLaw && metadata.laws) {
                            setTimeout(() => {
                                fillLawsOptions(metadata, initialSub, initialLaw);
                            }, 100);
                        }
                    }
                    // Case 4: Just entity selected, no other fields
                    else {
                        console.log('Case 4: Just entity');
                        fillBranchOptions(metadata, '');
                    }
                }
            }
        });
            </script>


    <script>
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
                if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) return;
                selectedFiles.push(file);
                const fileItem = createFileItem(file);
                fileList.appendChild(fileItem);
                simulateProgress(fileItem);
            });
            syncInput();
        }

        function createFileItem(file) {
            const div = document.createElement('div');
            div.className = 'file-item';

            const extension = file.name.split('.').pop().toLowerCase();
            let icon = 'fa-file-lines',
                color = '#3b82f6';
            if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                icon = 'fa-file-image';
                color = '#10b981';
            } else if (extension === 'pdf') {
                icon = 'fa-file-pdf';
                color = '#ef4444';
            } else if (['xls', 'xlsx', 'csv'].includes(extension)) {
                icon = 'fa-file-excel';
                color = '#059669';
            }

            div.innerHTML = `
                            <div class="file-icon" style="background: ${color}15; color: ${color};">
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
                div.style.transform = 'translateY(-10px)';
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
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحميل...';

            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        container.style.opacity = '0';
                        setTimeout(() => {
                            container.style.display = 'none';
                            const remaining = document.querySelectorAll('.progress-container[style*="display: block"]');
                            if (remaining.length === 0) {
                                saveBtn.disabled = false;
                                saveBtn.innerHTML = '<i class="fa-solid fa-save"></i> حفظ التعديلات';
                            }
                        }, 500);
                    }, 500);
                } else {
                    width += Math.random() * 10;
                    if (width > 100) width = 100;
                    bar.style.width = width + '%';
                }
            }, 150);
            fileItem.uploadInterval = interval;
        }

        function syncInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
            const remaining = document.querySelectorAll('.progress-container[style*="display: block"]');
            if (remaining.length === 0) {
                const saveBtn = document.getElementById('saveBtn');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-save"></i> حفظ التعديلات';
            }
        }
    </script>

    <script>
        $(document).ready(function () {
            $('form').on('submit', function (e) {
                var valid = true;

                $(this).find('select[required].select2').each(function () {
                    if (!$(this).val()) {
                        valid = false;
                        $(this).next('.select2-container').find('.select2-selection').css('border-color', '#dc3545');
                    } else {
                        $(this).next('.select2-container').find('.select2-selection').css('border-color', '');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    return false;
                }

                var $btn = $('#saveBtn');
                $btn.prop('disabled', true);
                $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> جارى الحفظ ...');
            });
        });
    </script>
@endsection