@extends('claims::layouts.app')

@section('title', 'تسجيل أمر دفع')


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
    /* Compact claim details section - increased */
    #claimDetailsSection {
        padding: 10px 12px !important;
        margin: 8px 0 !important;
    }
    #claimDetailsSection h4 {
        font-size: 13px !important;
        margin-bottom: 8px !important;
    }
    #claimDetailsSection .form-row {
        grid-template-columns: repeat(6, 1fr) !important;
        gap: 8px !important;
        margin-bottom: 6px !important;
    }
    #claimDetailsSection .form-label {
        font-size: 11px !important;
    }
    #claimDetailsSection .form-control {
        height: 28px !important;
        font-size: 12px !important;
        padding: 3px 6px !important;
    }
    /* Notes textarea - increased height */
    textarea.form-control {
        min-height: 80px !important;
        height: 80px !important;
        font-size: 13px !important;
    }
    /* Error messages - increased */
    .error-message {
        font-size: 11px !important;
    }
    /* Small text - increased */
    small.text-muted {
        font-size: 10px !important;
        display: block;
        line-height: 1.2;
    }
    /* Page header - increased */
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
    /* Breadcrumb - increased */
    .breadcrumb-nav {
        padding: 8px 15px !important;
        margin-bottom: 6px !important;
    }
    /* Form actions - buttons on right - AGGRESSIVE OVERRIDE */
    .form-actions {
        margin-top: 10px !important;
        display: flex !important;
        justify-content: flex-start !important;
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
    /* Responsive Design */
    @media (max-width: 1400px) {
        .form-row {
            grid-template-columns: repeat(6, 1fr) !important;
        }
    }
    @media (max-width: 992px) {
        .form-row {
            grid-template-columns: repeat(4, 1fr) !important;
        }
        #claimDetailsSection .form-row {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        .form-label {
            font-size: 10px !important;
        }
    }
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        #claimDetailsSection .form-row {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .page-title {
            font-size: 16px !important;
        }
    }
    @media (max-width: 576px) {
        .form-row {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        #claimDetailsSection .form-row {
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
        <a href="{{ route('flow.operations') }}" class="breadcrumb-item">العمليات</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">تسجيل أمر دفع</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-credit-card"></i> تسجيل أمر دفع</h1>

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
            <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Main 9 Fields Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="payee_hospital_id" id="hospitalSelect" class="form-control select2" required>
                            <option value="">اختر المستشفى</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}" {{ (isset($hospital) && is_numeric($hospital->id) && $hospital->id == $hosp->id) ? 'selected' : '' }}>
                                    {{ $hosp->name }}
                                </option>
                            @endforeach
                            @if(isset($hospital) && !is_numeric($hospital->id))
                                <option value="{{ $hospital->id }}" selected>{{ $hospital->name }}</option>
                            @endif
                        </select>
                        @error('payee_hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required>
                            <option value="">اختر القسم</option>
                            @foreach($allDepartments as $dept)
                                @php
                                    $isSelected = (old('department_id') == $dept->id) || (isset($department) && is_numeric($department->id) && $department->id == $dept->id) || (isset($department) && !is_numeric($department->id) && $department->id == $dept->name);
                                @endphp
                                <option value="{{ $dept->id }}" {{ $isSelected ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                            @if(isset($department) && !is_numeric($department->id))
                                <option value="{{ $department->id }}" selected>{{ $department->name }}</option>
                            @endif
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-list-check"></i> نوع الحساب</label>
                        <select name="account_type" class="form-control select2" required>
                            <option value="">اختر النوع</option>
                            <option value="بنكى" {{ old('account_type') == 'بنكى' ? 'selected' : '' }}>بنكى</option>
                            <option value="أمر دفع برقم مؤسسى" {{ old('account_type') == 'أمر دفع برقم مؤسسى' ? 'selected' : '' }}>أمر دفع</option>
                            <option value="شيك نقدى" {{ old('account_type') == 'شيك نقدى' ? 'selected' : '' }}>شيك نقدى</option>
                        </select>
                        @error('account_type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم الـ GP</label>
                        <input type="text" name="gp_number" class="form-control" value="{{ old('gp_number') }}" required>
                        @error('gp_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> مبلغ التحصيل</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
                        @error('amount') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-minus-circle"></i> خصم / اشعار دائن</label>
                        <input type="number" step="0.01" name="deduction" class="form-control" value="{{ old('deduction') }}" min="0">
                        @error('deduction') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-percent"></i> ضرائب</label>
                        <input type="number" step="0.01" name="taxes" class="form-control" value="{{ old('taxes') }}" min="0">
                        @error('taxes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calendar-check"></i> تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                        @error('due_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-building"></i> الجهة المسددة</label>
                        <select name="payer_entity_id" id="entitySelect" class="form-control select2" required>
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                                @php
                                    $isSelected = (old('payer_entity_id') == $entity->id) || (isset($selectedEntityId) && $selectedEntityId == $entity->id);
                                @endphp
                                <option value="{{ $entity->id }}" {{ $isSelected ? 'selected' : '' }} data-metadata='@json($entity->metadata)'>{{ $entity->name }}</option>
                            @endforeach
                        </select>
                        @error('payer_entity_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    {{-- Search Mode Variables --}}
                    @php
                        $searchMode = $searchMode ?? 'electronic';
                        $showClaimNumber = ($searchMode === 'claim' || $searchMode === 'both');
                        $showElectronicInvoice = ($searchMode === 'electronic' || $searchMode === 'both' || $searchMode === 'dynamic');
                        $isDynamicMode = ($searchMode === 'dynamic');
                    @endphp

                    {{-- Claim Number Search (for waiting list ministry, insurance, or dynamic) --}}
                    @if($showClaimNumber || $isDynamicMode)
                    <div class="form-group" id="claimNumberContainer">
                        <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم المطالبة</label>
                        <input type="text" name="claim_number" id="claimNumberInput" class="form-control" value="{{ old('claim_number') }}" placeholder="رقم المطالبة...">
                        <small class="text-muted"><i class="fa-solid fa-info-circle"></i> اكتب للبحث</small>
                        @error('claim_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    {{-- Electronic Invoice Search (for regular flows, insurance, or dynamic) --}}
                    @if($showElectronicInvoice)
                    <div class="form-group" id="electronicInvoiceContainer">
                        <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" id="electronicInvoiceInput" class="form-control" value="{{ old('electronic_invoice_no') }}" placeholder="رقم الفاتورة الإلكترونية...">
                        @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    {{-- Empty placeholder if both hidden --}}
                    @if(!$showClaimNumber && !$showElectronicInvoice)
                    <div class="form-group"></div>
                    @endif
                </div>

                {{-- Claim Details Section (Hidden by default, shown when claim is found) --}}
                @if($showClaimSearch)
                <div id="claimDetailsSection" style="display: {{ old('claim_number') ? 'block' : 'none' }}; background: #fefce8; border: 1px dashed #f59e0b; border-radius: 6px; padding: 5px 8px; margin: 4px 0;">
                    <h4 style="color: #d97706; margin-bottom: 4px; font-size: 10px;"><i class="fa-solid fa-clipboard-list"></i> بيانات المطالبة</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">رقم المطالبة</label>
                            <input type="text" id="claimNumberDisplay" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ old('claim_number') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">عدد الفواتير</label>
                            <input type="text" id="claimInvoiceCount" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ old('invoice_count') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">قيمة المطالبة</label>
                            <input type="text" id="claimValue" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ old('claim_value') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">رقم الفاتورة</label>
                            <input type="text" id="claimElectronicInvoice" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ old('electronic_invoice_no') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;"><i class="fa-solid fa-calculator"></i> فواتير بعد المراجعة</label>
                            <input type="number" name="invoice_count_after_review" id="invoiceCountAfterReview" class="form-control" value="{{ old('invoice_count_after_review') }}" min="0" style="font-size: 9px; padding: 2px 4px;">
                            @error('invoice_count_after_review') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;"><i class="fa-solid fa-coins"></i> المبلغ بعد المراجعة</label>
                            <input type="number" step="0.01" name="amount_after_review" id="amountAfterReview" class="form-control" value="{{ old('amount_after_review') }}" min="0" style="font-size: 9px; padding: 2px 4px;">
                            @error('amount_after_review') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    {{-- Hidden fields to store claim data --}}
                    <input type="hidden" name="electronic_invoice_no" id="electronicInvoiceNoField" value="{{ old('electronic_invoice_no') }}">
                    <input type="hidden" name="payee_hospital_id" id="hospitalIdField" value="{{ old('payee_hospital_id', $hospital->id ?? '') }}">
                    <input type="hidden" name="department_id" id="departmentIdField" value="{{ old('department_id', $department->id ?? '') }}">
                    <input type="hidden" name="payer_entity_id" id="entityIdField" value="{{ old('payer_entity_id', $selectedEntityId ?? '') }}">
                </div>
                @endif

                <!-- Dynamic Fields for Entity (Branch, Location, Laws) -->
                <div class="form-row" style="gap: 4px; margin: 0; margin-bottom: 4px;">
                    <div class="form-group" id="branchContainer" style="display: none; margin: 0;">
                        <label class="form-label" style="font-size: 9px;"><i class="fa-solid fa-layer-group"></i> الفروع</label>
                        <select name="branch" id="branchSelect" class="form-control select2">
                            <option value="">اختر الفرع</option>
                        </select>
                    </div>

                    <div class="form-group" id="subContainer" style="display: none; margin: 0;">
                        <label class="form-label" id="subLabel" style="font-size: 9px;"><i class="fa-solid fa-map-marker-alt"></i> المحافظات</label>
                        <select name="location" id="subSelect" class="form-control select2">
                            <option value="">اختر المحافظة</option>
                        </select>
                    </div>

                    <div class="form-group" id="lawsContainer" style="display: none; margin: 0;">
                        <label class="form-label" style="font-size: 9px;"><i class="fa-solid fa-file-lines"></i> المستفيدين</label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2">
                            <option value="">اختر المستفيد</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 6px;">
                    <label class="form-label" style="font-size: 9px;"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3" style="min-height: 50px; height: 50px; font-size: 10px; resize: vertical;"
                        placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <!-- Attachments Section -->
                <div class="form-section" style="padding: 10px !important; margin: 8px 0; border: 1px dashed #cbd5e1; border-radius: 6px; background: #f8fafc;">
                    <h3 class="section-title" style="font-size: 13px; margin-bottom: 8px; color: #334155;">
                        <i class="fa-solid fa-paperclip"></i> المرفقات (صور، PDF، Excel)
                    </h3>

                    <div class="drop-zone" id="dropZone" style="padding: 15px !important; margin-bottom: 8px; border: 2px dashed #94a3b8; border-radius: 8px; text-align: center; cursor: pointer; background: #fff; transition: all 0.3s;">
                        <div class="drop-zone-content">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #64748b;"></i>
                            <p class="drop-zone-text" style="font-size: 13px; margin: 5px 0; color: #475569;">اسحب وأفلت الملفات هنا أو اضغط للاختيار</p>
                            <p class="drop-zone-hint" style="font-size: 11px; margin: 0; color: #94a3b8;">يمكنك رفع ملفات متعددة (الحد الأقصى 10 ميجا لكل ملف)</p>
                        </div>
                        <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                    </div>

                    <div class="file-list" id="fileList" style="margin-top: 8px;">
                        <!-- Files will appear here dynamically -->
                    </div>
                </div>

                <style>
                    .drop-zone:hover, .drop-zone.dragover {
                        border-color: #3b82f6 !important;
                        background: #eff6ff !important;
                    }
                    .file-item {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 6px 10px;
                        background: #fff;
                        border: 1px solid #e2e8f0;
                        border-radius: 4px;
                        margin-bottom: 4px;
                        font-size: 12px;
                    }
                    .file-item i {
                        color: #3b82f6;
                        margin-left: 8px;
                    }
                    .file-item .remove-file {
                        color: #ef4444;
                        cursor: pointer;
                        padding: 2px 6px;
                        border-radius: 3px;
                    }
                    .file-item .remove-file:hover {
                        background: #fee2e2;
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const dropZone = document.getElementById('dropZone');
                        const fileInput = document.getElementById('fileInput');
                        const fileList = document.getElementById('fileList');
                        let files = [];

                        // Click to select files
                        dropZone.addEventListener('click', function(e) {
                            if (e.target !== fileInput) {
                                fileInput.click();
                            }
                        });

                        // File input change
                        fileInput.addEventListener('change', function(e) {
                            handleFiles(e.target.files);
                        });

                        // Drag and drop events
                        dropZone.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            this.classList.add('dragover');
                        });

                        dropZone.addEventListener('dragleave', function(e) {
                            e.preventDefault();
                            this.classList.remove('dragover');
                        });

                        dropZone.addEventListener('drop', function(e) {
                            e.preventDefault();
                            this.classList.remove('dragover');
                            handleFiles(e.dataTransfer.files);
                        });

                        function handleFiles(newFiles) {
                            for (let i = 0; i < newFiles.length; i++) {
                                files.push(newFiles[i]);
                            }
                            updateFileList();
                            updateFileInput();
                        }

                        function updateFileList() {
                            fileList.innerHTML = '';
                            files.forEach((file, index) => {
                                const fileItem = document.createElement('div');
                                fileItem.className = 'file-item';
                                fileItem.innerHTML = `
                                    <span><i class="fa-solid fa-file"></i> ${file.name} (${formatFileSize(file.size)})</span>
                                    <span class="remove-file" data-index="${index}"><i class="fa-solid fa-times"></i></span>
                                `;
                                fileList.appendChild(fileItem);
                            });

                            // Add remove event listeners
                            document.querySelectorAll('.remove-file').forEach(btn => {
                                btn.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    const index = parseInt(this.dataset.index);
                                    files.splice(index, 1);
                                    updateFileList();
                                    updateFileInput();
                                });
                            });
                        }

                        function updateFileInput() {
                            const dataTransfer = new DataTransfer();
                            files.forEach(file => dataTransfer.items.add(file));
                            fileInput.files = dataTransfer.files;
                        }

                        function formatFileSize(bytes) {
                            if (bytes === 0) return '0 Bytes';
                            const k = 1024;
                            const sizes = ['Bytes', 'KB', 'MB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                        }
                    });
                </script>

                <div class="form-actions" style="margin-top: 8px;">
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



@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                dir: "rtl",
                width: '100%',
                closeOnSelect: true
            }).on('select2:select', function (e) {
                $(this).select2('close');
            });

            // Hospital & Department Logic
            const hospitalsData = @json($allHospitals);
            const allDepartmentsData = @json($allDepartments);
            const hospitalSelect = $('#hospitalSelect');
            const departmentSelect = $('#departmentSelect');
            
            // Waiting list hospitals departments mapping
            const waitingListHospitalDepartments = {
                'children': ['قسم الأطفال العام', 'قسم الأطفال حديثي الولادة', 'قسم الأطفال غير المستقر', 'قسم جراحة الأطفال'],
                'women': ['قسم النساء العام', 'قسم النساء الحوامل', 'قسم النساء غير المستقر', 'قسم جراحة النساء'],
                'ain_shams': ['قسم الباطنة', 'قسم الجراحة العامة', 'قسم النساء والتوليد', 'قسم الأطفال', 'قسم العظام', 'قسم المخ والأعصاب', 'قسم العيون', 'قسم الأنف والأذن والحنجرة', 'قسم السكتة الدماغية', 'قسم القلب', 'قسم الجهاز الهضمي', 'قسم الكلى', 'قسم الصدر'],
                'other': []
            };
            
            // Initial Values (note input name payee_hospital_id)
            const initialHospitalId = "{{ old('payee_hospital_id', $hospital->id ?? '') }}";
            const initialDepartmentId = "{{ old('department_id', $department->id ?? '') }}";
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

            if (initialHospitalId) {
                hospitalSelect.val(initialHospitalId).trigger('change');
                // For waiting list hospitals, populate all departments
                if (isWaitingListHospital) {
                    populateDepartments(initialHospitalId, initialDepartmentId);
                } else {
                    populateDepartments(initialHospitalId, initialDepartmentId);
                }
            }

            const entitySelect = $('#entitySelect');
            const branchContainer = $('#branchContainer');
            const branchSelect = $('#branchSelect');
            const subContainer = $('#subContainer');
            const subSelect = $('#subSelect');
            const subLabel = $('#subLabel');
            const lawsContainer = $('#lawsContainer');
            const lawsSelect = $('#lawsSelect');
            
            // Search mode from PHP (dynamic means no flow selected, determine by entity)
            const searchMode = '{{ $searchMode ?? 'electronic' }}';
            const isDynamicMode = (searchMode === 'dynamic');
            
            // Entity names that should use claim number search (Health Directorate)
            const claimNumberEntities = ['مديرية', 'شئون صحية', 'مديرية الشئون الصحية'];
            
            // Dynamic search mode handling - show/hide fields based on entity selection
            if (isDynamicMode) {
                entitySelect.on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const entityName = selectedOption.text().trim();
                    
                    // Check if entity is a Health Directorate
                    const isHealthDirectorate = claimNumberEntities.some(keyword => entityName.includes(keyword));
                    
                    if (isHealthDirectorate) {
                        // Show claim number, hide electronic invoice
                        $('#claimNumberContainer').show();
                        $('#electronicInvoiceContainer').hide();
                        $('#electronicInvoiceInput').prop('disabled', true);
                        $('#claimNumberInput').prop('disabled', false);
                    } else {
                        // Show electronic invoice, hide claim number
                        $('#claimNumberContainer').hide();
                        $('#electronicInvoiceContainer').show();
                        $('#electronicInvoiceInput').prop('disabled', false);
                        $('#claimNumberInput').prop('disabled', true);
                    }
                });
                
                // Trigger on initial load if entity pre-selected
                const initialEntityId = entitySelect.val();
                if (initialEntityId) {
                    entitySelect.trigger('change');
                }
            }
            
            // Flow type from PHP session
            const flowType = '{{ $entityType ?? '' }}';
            const preselectedBranch = '{{ $branch ?? '' }}';
            const preselectedLocation = '{{ $location ?? '' }}';
            const preselectedLaw = '{{ $law ?? '' }}';
            
            console.log('Search Mode:', searchMode);
            console.log('Is Dynamic Mode:', isDynamicMode);
            console.log('Flow Type:', flowType);
            console.log('Preselected Branch:', preselectedBranch);
            console.log('Preselected Location:', preselectedLocation);
            console.log('Preselected Law:', preselectedLaw);

                subContainer.slideUp(300);
                lawsContainer.slideUp(300);

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
                    if (metadata.branches && metadata.branches.length > 0) {
                        branchSelect.empty().append('<option value="">اختر الفرع</option>');
                        metadata.branches.forEach(branch => {
                            branchSelect.append(`<option value="${branch}">${branch}</option>`);
                        });
                        branchSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                        branchContainer.show();
                    } else {
                        let subItems = metadata.governorates || metadata.locations || [];
                        if (subItems.length > 0) {
                            subSelect.empty().append('<option value="">اختر الاختيار</option>');
                            subItems.forEach(item => {
                                subSelect.append(`<option value="${item}">${item}</option>`);
                            });
                            subSelect.select2('destroy').select2({ dir: "rtl", width: '100%', closeOnSelect: true });
                            subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                            subContainer.show();
                        }
                    }
                } else {
                    // === باقي الحالات العادية ===
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

            // Initialize entity on page load if pre-selected
            const initialEntity = '{{ old("payer_entity_id", $selectedEntityId ?? "") }}';
            console.log('Initial Entity from PHP:', initialEntity);
            console.log('Preselected values:', { branch: preselectedBranch, location: preselectedLocation, law: preselectedLaw });
            
            if (initialEntity) {
                entitySelect.val(initialEntity).trigger('change');
                
                // Get metadata for the selected entity
                const selectedOption = entitySelect.find('option:selected');
                const metadata = selectedOption.data('metadata');
                
                console.log('Selected entity metadata:', metadata);
                
                if (metadata) {
                    // Wait for the change event to populate the dropdowns
                    setTimeout(() => {
                        // Case 1: Has branch (e.g., Ministry flow)
                        if (preselectedBranch && metadata.branches && metadata.branches.includes(preselectedBranch)) {
                            console.log('Setting branch to:', preselectedBranch);
                            branchSelect.val(preselectedBranch).trigger('change');
                            
                            // After branch change, wait for locations to populate
                            setTimeout(() => {
                                handleLocation(metadata, preselectedLocation);
                            }, 300);
                        } 
                        // Case 2: Has NO branch but has location (e.g., Insurance, Comprehensive)
                        else if (preselectedLocation && !preselectedBranch) {
                            console.log('No branch, handling location directly');
                            handleLocation(metadata, preselectedLocation);
                        }
                        // Case 3: Waiting lists - has law but no branch/location
                        else if (preselectedLaw && metadata.laws && metadata.laws.includes(preselectedLaw)) {
                            console.log('Waiting list - showing laws directly');
                            branchContainer.slideUp(300);
                            subContainer.slideUp(300);
                            
                            // Populate laws dropdown directly
                            lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                            metadata.laws.forEach(law => {
                                const isSelected = (law === preselectedLaw) ? 'selected' : '';
                                lawsSelect.append(`<option value="${law}" ${isSelected}>${law}</option>`);
                            });
                            lawsContainer.slideDown(300);
                        }
                    }, 300);
                }
            }
            
            // Check if there's old claim data (after validation error)
            const oldClaimNumber = "{{ old('claim_number') }}";
            const oldElectronicInvoice = "{{ old('electronic_invoice_no') }}";
            if (oldClaimNumber && oldElectronicInvoice) {
                // Show claim details section and review fields
                $('#claimDetailsSection').show();
                // Restore the review fields if they have values
                if ("{{ old('invoice_count_after_review') }}" || "{{ old('amount_after_review') }}") {
                    $('#reviewFieldsRow').show();
                }
            }
            
            // Claim Number Search Functionality (for financial users) - Auto search on input
            let searchTimeout;
            const claimNumberInput = $('#claimNumberInput');
            
            claimNumberInput.on('input', function() {
                const claimNumber = $(this).val().trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Hide sections if input is empty
                if (!claimNumber) {
                    $('#claimDetailsSection').hide();
                    $('#reviewFieldsRow').hide();
                    return;
                }
                
                // Debounce search - wait 500ms after user stops typing
                searchTimeout = setTimeout(function() {
                    searchClaim(claimNumber);
                }, 500);
            });
            
            // Also search on blur (when user leaves the field)
            claimNumberInput.on('blur', function() {
                const claimNumber = $(this).val().trim();
                if (claimNumber) {
                    clearTimeout(searchTimeout);
                    searchClaim(claimNumber);
                }
            });
            
            // Function to search claim by claim number
            function searchClaim(claimNumber) {
                // AJAX request to search for claim
                $.ajax({
                    url: '{{ route("payments.search-claims") }}',
                    method: 'GET',
                    data: { claim_number: claimNumber },
                    success: function(response) {
                        if (response.success) {
                            // Display claim details
                            $('#claimNumberDisplay').val(response.claim.claim_number);
                            $('#claimInvoiceCount').val(response.claim.invoice_count);
                            $('#claimValue').val(response.claim.claim_value + ' ج.م');
                            $('#claimElectronicInvoice').val(response.claim.electronic_invoice_no);
                            
                            // Set hidden fields
                            $('#electronicInvoiceNoField').val(response.claim.electronic_invoice_no);
                            $('#hospitalIdField').val(response.claim.hospital_id);
                            $('#departmentIdField').val(response.claim.department_id);
                            $('#entityIdField').val(response.claim.entity_id);
                            
                            // Update hospital and department dropdowns
                            if (response.claim.hospital_id) {
                                // Check if hospital exists in dropdown, if not add it
                                const hospitalExists = hospitalSelect.find('option[value="' + response.claim.hospital_id + '"]').length > 0;
                                if (!hospitalExists && response.claim.hospital_name) {
                                    hospitalSelect.append('<option value="' + response.claim.hospital_id + '">' + response.claim.hospital_name + '</option>');
                                }
                                
                                hospitalSelect.val(response.claim.hospital_id).trigger('change');
                                hospitalSelect.trigger('change.select2'); // Refresh Select2
                                
                                // Small delay to let hospital update before setting department
                                setTimeout(function() {
                                    populateDepartments(response.claim.hospital_id, response.claim.department_id);
                                }, 100);
                            }
                            
                            // Update entity dropdown
                            if (response.claim.entity_id) {
                                entitySelect.val(response.claim.entity_id).trigger('change');
                            }
                            
                            // Show claim details section
                            $('#claimDetailsSection').slideDown(300);
                            // Show review fields
                            $('#reviewFieldsRow').slideDown(300);
                        } else {
                            // Claim not found - hide sections but don't alert
                            $('#claimDetailsSection').hide();
                            $('#reviewFieldsRow').hide();
                        }
                    },
                    error: function() {
                        // Error - hide sections
                        $('#claimDetailsSection').hide();
                        $('#reviewFieldsRow').hide();
                    }
                });
            }
            
            // Electronic Invoice Search Functionality - Auto search on input
            const electronicInvoiceInput = $('#electronicInvoiceInput');
            let electronicSearchTimeout;
            
            electronicInvoiceInput.on('input', function() {
                const electronicInvoiceNo = $(this).val().trim();
                
                // Clear previous timeout
                clearTimeout(electronicSearchTimeout);
                
                // Hide sections if input is empty
                if (!electronicInvoiceNo) {
                    $('#claimDetailsSection').hide();
                    $('#reviewFieldsRow').hide();
                    return;
                }
                
                // Debounce search - wait 500ms after user stops typing
                electronicSearchTimeout = setTimeout(function() {
                    searchByElectronicInvoice(electronicInvoiceNo);
                }, 500);
            });
            
            // Also search on blur (when user leaves the field)
            electronicInvoiceInput.on('blur', function() {
                const electronicInvoiceNo = $(this).val().trim();
                if (electronicInvoiceNo) {
                    clearTimeout(electronicSearchTimeout);
                    searchByElectronicInvoice(electronicInvoiceNo);
                }
            });
            
            function searchByElectronicInvoice(electronicInvoiceNo) {
                // AJAX request to search for claim by electronic invoice
                $.ajax({
                    url: '{{ route("payments.search-by-electronic-invoice") }}',
                    method: 'GET',
                    data: { electronic_invoice_no: electronicInvoiceNo },
                    success: function(response) {
                        if (response.success) {
                            // Display claim details
                            $('#claimNumberDisplay').val(response.claim.claim_number);
                            $('#claimInvoiceCount').val(response.claim.invoice_count);
                            $('#claimValue').val(response.claim.claim_value + ' ج.م');
                            $('#claimElectronicInvoice').val(response.claim.electronic_invoice_no);
                            
                            // Set hidden fields
                            $('#electronicInvoiceNoField').val(response.claim.electronic_invoice_no);
                            $('#hospitalIdField').val(response.claim.hospital_id);
                            $('#departmentIdField').val(response.claim.department_id);
                            $('#entityIdField').val(response.claim.entity_id);
                            
                            // Update hospital and department dropdowns
                            if (response.claim.hospital_id) {
                                // Check if hospital exists in dropdown, if not add it
                                const hospitalExists = hospitalSelect.find('option[value="' + response.claim.hospital_id + '"]').length > 0;
                                if (!hospitalExists && response.claim.hospital_name) {
                                    hospitalSelect.append('<option value="' + response.claim.hospital_id + '">' + response.claim.hospital_name + '</option>');
                                }
                                
                                hospitalSelect.val(response.claim.hospital_id).trigger('change');
                                hospitalSelect.trigger('change.select2'); // Refresh Select2
                                
                                // Small delay to let hospital update before setting department
                                setTimeout(function() {
                                    populateDepartments(response.claim.hospital_id, response.claim.department_id);
                                }, 100);
                            }
                            
                            // Update entity dropdown
                            if (response.claim.entity_id) {
                                entitySelect.val(response.claim.entity_id).trigger('change');
                            }
                            
                            // Show claim details section
                            $('#claimDetailsSection').slideDown(300);
                            // Show review fields
                            $('#reviewFieldsRow').slideDown(300);
                        } else {
                            // Claim not found - hide sections but don't alert
                            $('#claimDetailsSection').hide();
                            $('#reviewFieldsRow').hide();
                        }
                    },
                    error: function() {
                        // Error - hide sections
                        $('#claimDetailsSection').hide();
                        $('#reviewFieldsRow').hide();
                    }
                });
            }
        });
    </script>
@endsection