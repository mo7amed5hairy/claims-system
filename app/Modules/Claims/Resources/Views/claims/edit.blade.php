@extends('claims::layouts.app')

@section('title', 'تعديل مطالبة')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('claims.index') }}" class="breadcrumb-item">
            المطالبات
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">تعديل مطالبة</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-edit"></i> تعديل مطالبة <span
                style="color: var(--primary-color);">#{{ $claim->id }}</span></h1>
        <p class="page-subtitle">
            <span><i class="fa-solid fa-hospital"></i> {{ $claim->hospital->name ?? '-' }}</span>
            <span style="margin: 0 8px;">•</span>
            <span><i class="fa-solid fa-stethoscope"></i> {{ $claim->department->name ?? '-' }}</span>
        </p>
    </div>

    <div class="form-container">
        <div class="form-card">
            <form action="{{ route('claims.update', $claim->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Main Info Row: 4 Columns -->
                <div class="form-row-4">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-calculator"></i> عدد الفواتير
                        </label>
                        <input type="number" name="invoice_count" class="form-control" min="1"
                            value="{{ old('invoice_count', $claim->invoice_count) }}" required>
                        @error('invoice_count') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-calendar"></i> الشهر
                        </label>
                        <select name="month" class="form-control select2" required>
                            <option value="">اختر الشهر</option>
                            @foreach(['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] as $m)
                                <option value="{{ $m }}" {{ old('month', $claim->month) == $m ? 'selected' : '' }}>{{ $m }}
                                </option>
                            @endforeach
                        </select>
                        @error('month') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-building"></i> الجهة
                        </label>
                        <select name="entity_id" id="entitySelect" class="form-control select2" required>
                                <option value="">اختر الجهة</option>
                                @foreach($entities as $entity)
                                    <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)'
                                        {{ old('entity_id', $claim->entity_id) == $entity->id ? 'selected' : '' }}>
                                        {{ $entity->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('entity_id') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <!-- قائمة الفروع -->
                        <div class="form-group" id="branchContainer" style="display: none;">
                            <label class="form-label"><i class="fa-solid fa-layer-group"></i> الفروع</label>
                            <select name="branch" id="branchSelect" class="form-control select2">
                                <option value="">اختر الفرع</option>
                            </select>
                        </div>

                        <!-- قائمة المحافظات / المواقع -->
                        <div class="form-group" id="subContainer" style="display: none;">
                            <label class="form-label" id="subLabel"><i class="fa-solid fa-map-marker-alt"></i> المحافظات /
                                المواقع</label>
                            <select name="location" id="subSelect" class="form-control select2">
                                <option value="">اختر المحافظة / الموقع</option>
                            </select>
                        </div>

                        <!-- قائمة المستفيدين / القوانين -->
                        <div class="form-group" id="lawsContainer" style="display: none;">
                            <label class="form-label"><i class="fa-solid fa-file-lines"></i> المستفيدين / القوانين</label>
                            <select name="beneficiary" id="lawsSelect" class="form-control select2">
                                <option value="">اختر المستفيد</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-calendar-days"></i> تاريخ المطالبة
                            </label>
                            <input type="date" name="claim_date" class="form-control"
                                value="{{ old('claim_date', $claim->claim_date->format('Y-m-d')) }}" required>
                            @error('claim_date') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Financial Section -->
                    <div class="form-section" style="padding: 20px;">
                        <h3 class="section-title">
                            <i class="fa-solid fa-money-bill"></i> البيانات المالية
                        </h3>

                        <div class="form-row-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-coins"></i> قيمة المطالبة (ج.م)
                                </label>
                                <input type="number" step="0.01" id="claimValue" name="claim_value" class="form-control" min="0"
                                    value="{{ old('claim_value', $claim->claim_value) }}" required oninput="calculateDiff()">
                                @error('claim_value') <span class="error-message">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-check-circle"></i> المبلغ بعد المراجعة (ج.م)
                                </label>
                                <input type="number" step="0.01" id="reviewedValue" name="reviewed_value" class="form-control"
                                    min="0" value="{{ old('reviewed_value', $claim->reviewed_value) }}"
                                    oninput="calculateDiff()">
                                @error('reviewed_value') <span class="error-message">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-equals"></i> الفرق (ج.م)
                                </label>
                                <input type="number" step="0.01" id="differenceValue" name="difference" class="form-control"
                                    value="{{ $claim->difference }}" readonly>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-user-check"></i> اسم المراجع
                                </label>
                                <input type="text" name="reviewer_name" class="form-control"
                                    value="{{ old('reviewer_name', $claim->reviewer_name) }}">
                                @error('reviewer_name') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="form-section" style="padding: 20px;">
                        <h3 class="section-title">
                            <i class="fa-solid fa-file-lines"></i> معلومات إضافية
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية
                                </label>
                                <input type="text" name="electronic_invoice_no" class="form-control"
                                    value="{{ old('electronic_invoice_no', $claim->electronic_invoice_no) }}">
                                @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-receipt"></i> رقم المطالبة التأمينية
                                </label>
                                <input type="text" name="insurance_claim_number" class="form-control"
                                    value="{{ old('insurance_claim_number', $claim->insurance_claim_number) }}">
                                @error('insurance_claim_number') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-note-sticky"></i> ملاحظات
                            </label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $claim->notes) }}</textarea>
                            @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Existing Attachments Section -->
                    @if($claim->attachments && count($claim->attachments) > 0)
                        <div class="form-section">
                            <h3 class="section-title text-success">
                                <i class="fa-solid fa-folder-open"></i> المرفقات الحالية
                            </h3>
                            <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">
                                <i class="fa-solid fa-info-circle"></i> يمكنك الإبقاء عليها أو إضافة مرفقات جديدة لاستبدالها.
                            </p>

                            <div class="file-list" style="margin-top: 10px;">
                                @foreach($claim->attachments as $path)
                                    @php
                                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                                        $icon = 'fa-file-lines';
                                        $typeClass = 'icon-default';
                                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            $icon = 'fa-file-image';
                                            $typeClass = 'icon-image';
                                        } elseif ($ext == 'pdf') {
                                            $icon = 'fa-file-pdf';
                                            $typeClass = 'icon-pdf';
                                        } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                            $icon = 'fa-file-excel';
                                            $typeClass = 'icon-excel';
                                        }
                                    @endphp
                                    <div class="file-item">
                                        <div class="file-icon {{ $typeClass }}">
                                            <i class="fa-solid {{ $icon }}"></i>
                                        </div>
                                        <div class="file-details">
                                            <div class="file-name">{{ basename($path) }}</div>
                                            <div class="file-size">ملف مخزن مسبقاً</div>
                                        </div>
                                        <div class="file-actions">
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" class="attachment-badge"
                                                style="background: #f1f5f9; color: #64748b; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- New Attachments Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fa-solid fa-paperclip"></i> إضافة مرفقات جديدة (استبدال)
                        </h3>

                        <div class="drop-zone" id="dropZone">
                            <div class="drop-zone-content">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p class="drop-zone-text">اسحب وأفلت الملفات هنا لاستبدال المرفقات</p>
                                <p class="drop-zone-hint">تحميل ملفات جديدة سيؤدي إلى حذف الملفات القديمة تلقائياً</p>
                            </div>
                            <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                        </div>

                        <div class="file-list" id="fileList">
                            <!-- Files will appear here dynamically -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions" style="margin-top: 32px;">
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="fa-solid fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('claims.index') }}" class="btn btn-secondary">
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
                    $('.select2').select2({ dir: "rtl", width: '100%' });

                    const entitySelect = $('#entitySelect');
                    const branchContainer = $('#branchContainer');
                    const branchSelect = $('#branchSelect');
                    const subContainer = $('#subContainer');
                    const subSelect = $('#subSelect');
                    const subLabel = $('#subLabel');
                    const lawsContainer = $('#lawsContainer');
                    const lawsSelect = $('#lawsSelect');

                    function resetSub() {
                        subSelect.empty().append('<option value="">اختر الاختيار</option>');
                        subContainer.hide();

                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        lawsContainer.hide();
                    }

                    function fillBranchOptions(metadata, selectedBranch = '') {
                        branchSelect.empty().append('<option value="">اختر الفرع</option>');
                        if (metadata?.branches?.length) {
                            metadata.branches.forEach(branch => {
                                branchSelect.append(`<option value="${branch}">${branch}</option>`);
                            });
                            branchContainer.show();
                            if (selectedBranch) branchSelect.val(selectedBranch).trigger('change');
                        } else {
                            branchContainer.hide();
                        }
                    }

                    function fillSubOptions(metadata, branchVal, selectedSub = '') {
                        subSelect.empty().append('<option value="">اختر الاختيار</option>');
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        lawsContainer.hide();

                        if (!branchVal) return;

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
                            subContainer.show();
                            if (selectedSub) subSelect.val(selectedSub).trigger('change');
                        }
                    }

                    function fillLawsOptions(metadata, subVal, selectedLaw = '') {
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        if (!metadata.laws || !subVal) return;

                        metadata.laws.forEach(item => lawsSelect.append(`<option value="${item}">${item}</option>`));
                        lawsContainer.show();
                        if (selectedLaw) lawsSelect.val(selectedLaw).trigger('change');
                    }

                    entitySelect.on('change', function () {
                        const selectedOption = $(this).find('option:selected');
                        const metadata = selectedOption.data('metadata');

                        // ملء الفروع فقط إذا موجودة
                        fillBranchOptions(metadata);
                        resetSub();
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

                    // ==== تهيئة الصفحة عند التحميل ====
                    const initialEntity = '{{ old("entity_id", $claim->entity_id) }}';
                    const initialBranch = '{{ old("branch", $claim->branch) }}';
                    const initialSub = '{{ old("location", $claim->location) }}';
                    const initialLaw = '{{ old("beneficiary", $claim->beneficiary) }}';

                    if (initialEntity) {
                        entitySelect.val(initialEntity).trigger('change');
                        const selectedOption = entitySelect.find('option:selected');
                        if (selectedOption.length) {
                            const metadata = selectedOption.data('metadata');

                            // We need a slight timeout or wait for change event propagation if we were relying on it, 
                            // but here we are calling functions directly so it should be fine.
                            // However, calling trigger('change') on entitySelect above calls fillBranchOptions and resetSub.
                            // So we need to override that with the initial values.

                            // Wait for the first change event to finish? Or just call logic manually.
                            // The 'change' handler calls fillBranchOptions(metadata) without selectedBranch.
                            // So we should re-call it with initialBranch.

                            fillBranchOptions(metadata, initialBranch);
                            if (initialBranch) fillSubOptions(metadata, initialBranch, initialSub);
                            if (initialSub) fillLawsOptions(metadata, initialSub, initialLaw);
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
@endsection