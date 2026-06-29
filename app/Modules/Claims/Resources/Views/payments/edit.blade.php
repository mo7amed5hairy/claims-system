@extends('claims::layouts.app')

@section('title', 'تعديل أمر دفع')

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
        .form-label {
            font-size: 11px !important;
        }
    }
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        .page-title {
            font-size: 16px !important;
        }
    }
    @media (max-width: 576px) {
        .form-row {
            grid-template-columns: repeat(2, 1fr) !important;
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
        <a href="{{ route('payments.index') }}" class="breadcrumb-item">أوامر الدفع</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">تعديل أمر دفع</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-edit"></i> تعديل أمر دفع</h1>
    </div>

    <div class="form-container">
        <div class="form-card">
            <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Main 9 Fields Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="payee_hospital_id" id="hospitalSelect" class="form-control select2" required>
                            <option value="">اختر المستشفى</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}" {{ (old('payee_hospital_id', $payment->payee_hospital_id) == $hosp->id) ? 'selected' : '' }}>
                                    {{ $hosp->name }}
                                </option>
                            @endforeach
                            @php
                                $hospitalNames = [
                                    'ain_shams' => 'مستشفى عين شمس',
                                    'children' => 'مستشفى الأطفال',
                                    'women' => 'مستشفى النساء',
                                    'other' => 'أخرى'
                                ];
                            @endphp
                            @if($payment->payee_hospital_id && !is_numeric($payment->payee_hospital_id))
                                <option value="{{ $payment->payee_hospital_id }}" selected>
                                    {{ $hospitalNames[$payment->payee_hospital_id] ?? $payment->payee_hospital_id }}
                                </option>
                            @endif
                        </select>
                        @error('payee_hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required>
                            <option value="">اختر القسم</option>
                            @if($payment->department_id && !is_numeric($payment->department_id))
                                <option value="{{ $payment->department_id }}" selected>
                                    {{ $payment->department_id }}
                                </option>
                            @endif
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-list-check"></i> نوع الحساب</label>
                        <select name="account_type" class="form-control select2" required>
                            <option value="">اختر النوع</option>
                            <option value="بنكى" {{ old('account_type', $payment->account_type) == 'بنكى' ? 'selected' : '' }}>بنكى</option>
                            <option value="أمر دفع برقم مؤسسى" {{ old('account_type', $payment->account_type) == 'أمر دفع برقم مؤسسى' ? 'selected' : '' }}>أمر دفع</option>
                            <option value="شيك نقدى" {{ old('account_type', $payment->account_type) == 'شيك نقدى' ? 'selected' : '' }}>شيك نقدى</option>
                        </select>
                        @error('account_type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم الـ GP</label>
                        <input type="text" name="gp_number" class="form-control"
                            value="{{ old('gp_number', $payment->gp_number) }}" required>
                        @error('gp_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> مبلغ التحصيل</label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                            value="{{ old('amount', $payment->amount) }}" required>
                        @error('amount') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-minus-circle"></i> خصم / اشعار دائن</label>
                        <input type="number" step="0.01" name="deduction" class="form-control" value="{{ old('deduction', $payment->deduction) }}" min="0">
                        @error('deduction') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-percent"></i> ضرائب</label>
                        <input type="number" step="0.01" name="taxes" class="form-control" value="{{ old('taxes', $payment->taxes) }}" min="0">
                        @error('taxes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calendar-check"></i> تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control"
                            value="{{ old('due_date', $payment->due_date->format('Y-m-d')) }}" required>
                        @error('due_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-building"></i> الجهة المسددة</label>
                        <select name="payer_entity_id" id="entitySelect" class="form-control select2" required>
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)' {{ old('payer_entity_id', $payment->payer_entity_id) == $entity->id ? 'selected' : '' }}>
                                    {{ $entity->name }}
                                </option>
                            @endforeach
                    </select>
                </div>

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
                        <input type="text" name="claim_number" id="claimNumberInput" class="form-control" value="{{ old('claim_number', $payment->claim_number) }}" placeholder="رقم المطالبة...">
                        <small class="text-muted"><i class="fa-solid fa-info-circle"></i> اكتب للبحث</small>
                        @error('claim_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    {{-- Electronic Invoice Search (for regular flows, insurance, or dynamic) --}}
                    @if($showElectronicInvoice)
                    <div class="form-group" id="electronicInvoiceContainer">
                        <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" id="electronicInvoiceInput" class="form-control" value="{{ old('electronic_invoice_no', $payment->electronic_invoice_no ?? $payment->claim_number) }}" placeholder="رقم الفاتورة الإلكترونية...">
                        @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    {{-- Empty placeholder if both hidden --}}
                    @if(!$showClaimNumber && !$showElectronicInvoice)
                    <div class="form-group"></div>
                    @endif
                </div>

                {{-- Claim Details Section (shown when editing existing payment with claim) --}}
                @if($claim)
                <div id="claimDetailsSection" style="display: block; background: #fefce8; border: 1px dashed #f59e0b; border-radius: 6px; padding: 5px 8px; margin: 4px 0;">
                    <h4 style="color: #d97706; margin-bottom: 4px; font-size: 10px;"><i class="fa-solid fa-clipboard-list"></i> بيانات المطالبة</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">رقم المطالبة</label>
                            <input type="text" id="claimNumberDisplay" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ $claim->claim_number }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">عدد الفواتير</label>
                            <input type="text" id="claimInvoiceCount" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ $claim->invoice_count }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">قيمة المطالبة</label>
                            <input type="text" id="claimValue" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ $claim->claim_value }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;">رقم الفاتورة</label>
                            <input type="text" id="claimElectronicInvoice" class="form-control" readonly style="background: #f1f5f9; font-size: 9px;" value="{{ $claim->electronic_invoice_no }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;"><i class="fa-solid fa-calculator"></i> فواتير بعد المراجعة</label>
                            <input type="number" name="invoice_count_after_review" id="invoiceCountAfterReview" class="form-control" value="{{ old('invoice_count_after_review', $payment->invoice_count_after_review) }}" min="0" style="font-size: 9px; padding: 2px 4px;">
                            @error('invoice_count_after_review') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 8px;"><i class="fa-solid fa-coins"></i> المبلغ بعد المراجعة</label>
                            <input type="number" step="0.01" name="amount_after_review" id="amountAfterReview" class="form-control" value="{{ old('amount_after_review', $payment->amount_after_review) }}" min="0" style="font-size: 9px; padding: 2px 4px;">
                            @error('amount_after_review') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    {{-- Hidden fields to store claim data --}}
                    <input type="hidden" data-name="electronic_invoice_no" id="electronicInvoiceNoField" value="{{ $claim->electronic_invoice_no }}">
                    <input type="hidden" data-name="payee_hospital_id" id="hospitalIdField" value="{{ old('payee_hospital_id', $payment->payee_hospital_id ?? '') }}">
                    <input type="hidden" data-name="department_id" id="departmentIdField" value="{{ old('department_id', $payment->department_id ?? '') }}">
                    <input type="hidden" data-name="payer_entity_id" id="entityIdField" value="{{ old('payer_entity_id', $payment->payer_entity_id ?? '') }}">
                </div>
                @endif

                <!-- Dynamic Fields for Entity (Branch, Location, Laws) -->
                <div class="form-row" style="gap: 4px; margin: 0; margin-bottom: 4px;">
                    <div class="form-group" id="branchContainer" style="display: none; margin: 0;">
                        <label class="form-label" style="font-size: 12px;"><i class="fa-solid fa-layer-group"></i> الفروع</label>
                        <select name="branch" id="branchSelect" class="form-control select2">
                            <option value="">اختر الفرع</option>
                        </select>
                    </div>

                    <div class="form-group" id="subContainer" style="display: none; margin: 0;">
                        <label class="form-label" id="subLabel" style="font-size: 12px;"><i class="fa-solid fa-map-marker-alt"></i> المحافظات</label>
                        <select name="location" id="subSelect" class="form-control select2">
                            <option value="">اختر المحافظة</option>
                        </select>
                    </div>

                    <div class="form-group" id="lawsContainer" style="display: none; margin: 0;">
                        <label class="form-label" style="font-size: 12px;"><i class="fa-solid fa-file-lines"></i> المستفيدين</label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2">
                            <option value="">اختر المستفيد</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 6px;">
                    <label class="form-label" style="font-size: 13px;"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3" style="min-height: 100px; height: 100px; font-size: 14px; resize: vertical;"
                        placeholder="أي ملاحظات إضافية...">{{ old('notes', $payment->notes) }}</textarea>
                    @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <!-- Attachments Section -->
                <div class="form-section" style="padding: 10px !important; margin: 8px 0; border: 1px dashed #cbd5e1; border-radius: 6px; background: #f8fafc;">
                    <h3 class="section-title" style="font-size: 13px; margin-bottom: 8px; color: #334155;">
                        <i class="fa-solid fa-paperclip"></i> المرفقات (صور، PDF، Excel)
                    </h3>

                    {{-- Existing Attachments --}}
                    @if(!empty($payment->attachments) && is_array($payment->attachments) && count($payment->attachments) > 0)
                        <div style="margin-bottom: 10px;">
                            <label class="form-label" style="font-size: 11px; color: #64748b;">المرفقات الحالية:</label>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 5px;">
                                @foreach($payment->attachments as $index => $attachment)
                                    <div style="display: flex; align-items: center; gap: 5px; background: #fff; padding: 5px 10px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" style="font-size: 12px; color: #3b82f6; text-decoration: none;">
                                            <i class="fa-solid fa-file"></i> ملف {{ $loop->iteration }}
                                        </a>
                                        <label style="font-size: 11px; color: #64748b; cursor: pointer; margin: 0;">
                                            <input type="checkbox" name="remove_attachments[]" value="{{ $index }}" style="margin-left: 3px;"> حذف
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="drop-zone" id="dropZone" style="padding: 15px !important; margin-bottom: 8px; border: 2px dashed #94a3b8; border-radius: 8px; text-align: center; cursor: pointer; background: #fff; transition: all 0.3s;">
                        <div class="drop-zone-content">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #64748b;"></i>
                            <p class="drop-zone-text" style="font-size: 13px; margin: 5px 0; color: #475569;">اسحب وأفلت الملفات هنا أو اضغط لإضافة مرفقات جديدة</p>
                            <p class="drop-zone-hint" style="font-size: 11px; margin: 0; color: #94a3b8;">يمكنك رفع ملفات متعددة (الحد الأقصى 10 ميجا لكل ملف)</p>
                        </div>
                        <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                    </div>

                    <div class="file-list" id="fileList" style="margin-top: 8px;">
                        <!-- New files will appear here dynamically -->
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
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fa-solid fa-save"></i> حفظ التعديلات
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

            // Initial Values (note input name payee_hospital_id)
            const initialHospitalId = "{{ old('payee_hospital_id', $payment->payee_hospital_id) }}";
            const initialDepartmentId = "{{ old('department_id', $payment->department_id) }}";
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

            hospitalSelect.on('change', function () {
                const hospId = $(this).val();
                populateDepartments(hospId);
            });

            if (initialHospitalId) {
                // Manually trigger population because blade 'selected' handles visual selection 
                // but we need to fill the dependent dropdown
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

            function fillBranchOptions(metadata, selectedBranch = '') {
                branchSelect.empty().append('<option value="">اختر الفرع</option>');

                // Special Case: Universal Health Insurance -> Skip Branches
                // Special Case: Universal Health Insurance (Detected by laws in metadata) -> Skip Branches
                if (metadata && metadata.laws) {
                    branchContainer.slideUp(300);
                    fillSubOptions(metadata, 'SKIP_BRANCH', '{{ old("location", $payment->location) }}');
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
                    fillSubOptions(metadata, 'NO_BRANCH', '{{ old("location", $payment->location) }}');
                }
            }

            function fillSubOptions(metadata, branchVal, selectedSub = '') {
                subSelect.empty().append('<option value="">اختر الاختيار</option>');
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                lawsContainer.slideUp(300);

                if (!branchVal && branchVal !== 'SKIP_BRANCH' && branchVal !== 'NO_BRANCH') return;

                if (!metadata) {
                    subContainer.slideUp(300);
                    return;
                }

                let subItems = [];
                let labelText = 'المحافظات / المواقع';

                if (metadata.laws) {
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

            entitySelect.on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const metadata = selectedOption.data('metadata');
                fillBranchOptions(metadata);
                // resetSub(); // Handled by flow
            });

            branchSelect.on('change', function () {
                const branchVal = $(this).val() || '';
                const metadata = entitySelect.find('option:selected').data('metadata');
                fillSubOptions(metadata, branchVal);
            });

            subSelect.on('change', function () {
                const subVal = $(this).val() || '';
                const metadata = entitySelect.find('option:selected').data('metadata');
                fillLawsOptions(metadata, subVal);
            });

            // Initialize with existing values
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
            
            console.log('Search Mode:', searchMode);
            console.log('Is Dynamic Mode:', isDynamicMode);

            const initialEntity = '{{ old("payer_entity_id", $payment->payer_entity_id) }}';
            const initialBranch = '{{ old("branch", $payment->branch) }}';
            const initialSub = '{{ old("location", $payment->location) }}';
            const initialLaw = '{{ old("beneficiary", $payment->beneficiary) }}';

            if (initialEntity) {
                entitySelect.val(initialEntity).trigger('change');
                const selectedOption = entitySelect.find('option:selected');
                
                if (selectedOption.length) {
                    const metadata = selectedOption.data('metadata');

                    // Case 1: Has branch (Ministry flow)
                    if (initialBranch && metadata && metadata.branches && metadata.branches.includes(initialBranch)) {
                        fillBranchOptions(metadata, initialBranch);
                        setTimeout(() => {
                            fillSubOptions(metadata, initialBranch, initialSub);
                        }, 100);
                    }
                    // Case 2: No branch but has location (Insurance, Comprehensive)
                    else if (initialSub && !initialBranch) {
                        // Skip branch, go straight to location
                        branchContainer.slideUp(300);
                        fillSubOptions(metadata, 'SKIP_BRANCH', initialSub);
                    }
                    // Case 3: Has law but no location (Waiting lists)
                    else if (initialLaw && metadata && metadata.laws && metadata.laws.includes(initialLaw)) {
                        // For waiting lists, populate laws directly
                        branchContainer.slideUp(300);
                        subContainer.slideUp(300);
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        metadata.laws.forEach(item => {
                            const isSelected = (item === initialLaw) ? 'selected' : '';
                            lawsSelect.append(`<option value="${item}" ${isSelected}>${item}</option>`);
                        });
                        lawsContainer.slideDown(300);
                    }
                    // Case 4: Just entity selected, no other fields
                    else {
                        fillBranchOptions(metadata, '');
                    }
                }
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            // Auto-calculate amount_after_review
            function autoCalcAmountAfterReview() {
                const amount = parseFloat($('input[name="amount"]').val()) || 0;
                const deduction = parseFloat($('input[name="deduction"]').val()) || 0;
                const taxes = parseFloat($('input[name="taxes"]').val()) || 0;
                if (amount > 0) {
                    const net = amount - (amount * deduction / 100) - (amount * taxes / 100);
                    $('#amountAfterReview').val(Math.max(0, net.toFixed(2)));
                }
            }

            $('input[name="amount"], input[name="deduction"], input[name="taxes"]').on('input', function () {
                if ($('#claimDetailsSection').is(':visible')) {
                    autoCalcAmountAfterReview();
                }
            });

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