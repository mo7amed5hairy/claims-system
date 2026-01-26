@extends('claims::layouts.app')

@section('title', 'تعديل فاتورة عائدة')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <a href="{{ route('returns.index') }}" class="breadcrumb-item">الفواتير العائدة</a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <span class="breadcrumb-item active">تعديل فاتورة</span>
</div>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-edit"></i> تعديل فاتورة عائدة</h1>
    <p class="page-subtitle">
        <span><i class="fa-solid fa-hospital"></i> {{ $return->hospital->name ?? '-' }}</span>
        <span style="margin: 0 8px;">•</span>
        <span><i class="fa-solid fa-stethoscope"></i> {{ $return->department->name ?? '-' }}</span>
    </p>
</div>

<div class="form-container">
    <div class="form-card">
        <form action="{{ route('returns.update', $return->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Main Info Section: 3 Columns -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fa-solid fa-info-circle"></i> المعلومات الأساسية
                </h3>
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calendar"></i> شهر المطالبة</label>
                        <select name="month" class="form-control" required>
                            <option value="">اختر الشهر</option>
                            @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $m)
                            <option value="{{ $m }}" {{ old('month', $return->month) == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('month') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calendar-days"></i> تاريخ العودة</label>
                        <input type="date" name="return_date" class="form-control" value="{{ old('return_date', $return->return_date->format('Y-m-d')) }}" required>
                        @error('return_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-building"></i> جهة المطالبة</label>
                        <select name="entity_id" class="form-control" required>
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                            <option value="{{ $entity->id }}" {{ old('entity_id', $return->entity_id) == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
                            @endforeach
                        </select>
                        @error('entity_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" class="form-control" value="{{ old('electronic_invoice_no', $return->electronic_invoice_no) }}" required>
                        @error('electronic_invoice_no') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calculator"></i> عدد الفواتير المرتجعة</label>
                        <input type="number" name="returned_invoice_count" class="form-control" value="{{ old('returned_invoice_count', $return->returned_invoice_count) }}" min="1" required>
                        @error('returned_invoice_count') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-user-tag"></i> اسم المراجع</label>
                        <input type="text" name="reviewer_name" class="form-control" value="{{ old('reviewer_name', $return->reviewer_name) }}">
                        @error('reviewer_name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Data Section: 3 Columns -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fa-solid fa-money-bill-wave"></i> البيانات المالية
                </h3>
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-coins"></i> قيمة المرتجع (ج.م)</label>
                        <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $return->value) }}" required>
                        @error('value') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-check-double"></i> المبلغ بعد المراجعة (ج.م)</label>
                        <input type="number" step="0.01" id="reviewed_value" name="reviewed_value" class="form-control" value="{{ old('reviewed_value', $return->reviewed_value) }}" required oninput="calculateFinal()">
                        @error('reviewed_value') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-minus-circle"></i> قيمة الخصم (ج.م)</label>
                        <input type="number" step="0.01" id="discount_amount" name="discount_amount" class="form-control" value="{{ old('discount_amount', $return->discount_amount) }}" oninput="calculateFinal()">
                        @error('discount_amount') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-percent"></i> قيمة الضرائب (ج.م)</label>
                        <input type="number" step="0.01" id="tax_amount" name="tax_amount" class="form-control" value="{{ old('tax_amount', $return->tax_amount) }}" oninput="calculateFinal()">
                        @error('tax_amount') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-equals"></i> المبلغ النهائي (ج.م)</label>
                        <input type="number" step="0.01" id="final_amount" name="final_amount" class="form-control" readonly value="{{ old('final_amount', $return->final_amount) }}">
                        @error('final_amount') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Attachments & Notes -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fa-solid fa-paperclip"></i> المرفقات والملاحظات
                </h3>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-comment-dots"></i> سبب العودة / ملاحظات</label>
                    <textarea name="reason" class="form-control" rows="3">{{ old('reason', $return->reason) }}</textarea>
                    @error('reason') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <!-- Existing Attachments -->
                @if($return->attachments && count($return->attachments) > 0)
                <div class="form-group" style="margin-top: 20px;">
                    <label class="form-label"><i class="fa-solid fa-folder-open"></i> المرفقات الحالية</label>
                    <div class="attachment-badges">
                        @foreach($return->attachments as $attachment)
                        <div class="attachment-badge">
                            <i class="fa-solid fa-file-alt"></i>
                            <span class="file-name">{{ $attachment['original_name'] }}</span>
                            <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="btn-view" title="عرض">
                                <i class="fa-solid fa-external-link"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <p class="help-text" style="font-size: 12px; color: var(--text-secondary); margin-top: 8px;">
                        بمجرد رفع ملفات جديدة، سيتم استبدال المرفقات الحالية بالكامل.
                    </p>
                </div>
                @endif

                <div class="form-group" style="margin-top: 20px;">
                    <label class="form-label"><i class="fa-solid fa-cloud-upload-alt"></i> رفع مرفقات جديدة</label>
                    <div class="drop-zone" id="dropZone">
                        <div class="drop-zone-content">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p class="drop-zone-text">اسحب وأفلت الملفات هنا أو اضغط للاختيار</p>
                        </div>
                        <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                    </div>
                    <div class="file-list" id="fileList"></div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 32px;">
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <i class="fa-solid fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateFinal() {
        const reviewed = parseFloat(document.getElementById('reviewed_value').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const final = reviewed - discount - tax;
        document.getElementById('final_amount').value = final.toFixed(2);
    }

    // Attachment Management (Reuse selection logic from create)
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
        });
        syncInput();
    }

    function createFileItem(file) {
        const div = document.createElement('div');
        div.className = 'file-item';
        div.innerHTML = `
            <div class="file-icon"><i class="fa-solid fa-file"></i></div>
            <div class="file-details">
                <div class="file-name">${file.name}</div>
                <div class="file-size">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
            </div>
            <div class="file-actions">
                <button type="button" class="btn-remove"><i class="fa-solid fa-trash"></i></button>
            </div>
        `;
        div.querySelector('.btn-remove').onclick = () => {
            selectedFiles = selectedFiles.filter(f => f !== file);
            div.remove();
            syncInput();
        };
        return div;
    }

    function syncInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
</script>
@endsection