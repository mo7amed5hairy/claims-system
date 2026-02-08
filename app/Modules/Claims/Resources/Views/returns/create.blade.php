@extends('claims::layouts.app')

@section('title', 'تسجيل فاتورة عائدة')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.operations') }}" class="breadcrumb-item">العمليات</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">تسجيل فاتورة عائدة</span>

    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-undo"></i> تسجيل فاتورة عائدة</h1>

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
            <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Hospital and Department Row -->
                <div class="form-row form-section">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="hospital_id" id="hospitalSelect" class="form-control select2" required>
                            <option value="">اختر المستشفى</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}" {{ old('hospital_id', $hospital->id ?? '') == $hosp->id ? 'selected' : '' }}>
                                    {{ $hosp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required>
                            <option value="">اختر القسم</option>
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Main Info Section: 3 Columns -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-info-circle"></i> المعلومات الأساسية
                    </h3>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-calendar"></i> شهر المطالبة</label>
                            <select name="month" class="form-control select2" required>
                                <option value="">اختر الشهر</option>
                                @foreach(['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] as $m)
                                    <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('month') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-calendar-days"></i> تاريخ العودة</label>
                            <input type="date" name="return_date" class="form-control"
                                value="{{ old('return_date', date('Y-m-d')) }}" required>
                            @error('return_date') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-building"></i> جهة المطالبة</label>
                            <select name="entity_id" id="entitySelect" class="form-control select2" required>
                                <option value="">اختر الجهة</option>
                                @foreach($entities as $entity)
                                    <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)' {{ old('entity_id', session('flow_options.entity_id')) == $entity->id ? 'selected' : '' }}>
                                        {{ $entity->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('entity_id') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="form-row d-flex" style="gap: 8px; margin: 0; margin-bottom: 15px;">
                        <!-- قائمة الفروع -->
                        <div class="form-group" id="branchContainer" style="display: none; flex: 1; margin: 0;">
                            <label class="form-label"><i class="fa-solid fa-layer-group"></i> الفروع</label>
                            <select name="branch" id="branchSelect" class="form-control select2">
                                <option value="">اختر الفرع</option>
                            </select>
                        </div>

                        <!-- قائمة المحافظات / المواقع -->
                        <div class="form-group" id="subContainer" style="display: none; flex: 1; margin: 0;">
                            <label class="form-label" id="subLabel"><i class="fa-solid fa-map-marker-alt"></i> المحافظات /
                                المواقع</label>
                            <select name="location" id="subSelect" class="form-control select2">
                                <option value="">اختر المحافظة / الموقع</option>
                            </select>
                        </div>

                        <!-- قائمة المستفيدين / القوانين -->
                        <div class="form-group" id="lawsContainer" style="display: none; flex: 1; margin: 0;">
                            <label class="form-label"><i class="fa-solid fa-file-lines"></i> المستفيدين / القوانين</label>
                            <select name="beneficiary" id="lawsSelect" class="form-control select2">
                                <option value="">اختر المستفيد</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-3">

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                            <input type="text" name="electronic_invoice_no" class="form-control"
                                value="{{ old('electronic_invoice_no') }}" required placeholder="مثلاً: 25-100-2026">
                            @error('electronic_invoice_no') <span class="error-message">{!! $message !!}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-calculator"></i> عدد الفواتير المرتجعة</label>
                            <input type="number" name="returned_invoice_count" class="form-control"
                                value="{{ old('returned_invoice_count', 1) }}" min="1" required>
                            @error('returned_invoice_count') <span class="error-message">{!! $message !!}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-user-tag"></i> اسم المراجع</label>
                            <input type="text" name="reviewer_name" class="form-control" value="{{ old('reviewer_name') }}"
                                placeholder="اسم المراجع المختص">
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
                            <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value') }}"
                                required>
                            @error('value') <span class="error-message">{!! $message !!}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-check-double"></i> المبلغ بعد المراجعة
                                (ج.م)</label>
                            <input type="number" step="0.01" id="reviewed_value" name="reviewed_value" class="form-control"
                                value="{{ old('reviewed_value') }}" required oninput="calculateFinal()">
                            @error('reviewed_value') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-minus-circle"></i> قيمة الخصم (ج.م)</label>
                            <input type="number" step="0.01" id="discount_amount" name="discount_amount"
                                class="form-control" value="{{ old('discount_amount', 0) }}" oninput="calculateFinal()">
                            @error('discount_amount') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-percent"></i> قيمة الضرائب (ج.م)</label>
                            <input type="number" step="0.01" id="tax_amount" name="tax_amount" class="form-control"
                                value="{{ old('tax_amount', 0) }}" oninput="calculateFinal()">
                            @error('tax_amount') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-equals"></i> المبلغ النهائي (ج.م)</label>
                            <input type="number" step="0.01" id="final_amount" name="final_amount" class="form-control"
                                readonly value="{{ old('final_amount') }}">
                            @error('final_amount') <span class="error-message">{!! $message !!}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Attachments & Notes: Different Layout -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-paperclip"></i> المرفقات والملاحظات
                    </h3>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-comment-dots"></i> سبب العودة / ملاحظات</label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="أدخل سبب ارتجاع الفاتورة أو أي ملاحظات إضافية...">{{ old('reason') }}</textarea>
                        @error('reason') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label"><i class="fa-solid fa-cloud-upload-alt"></i> المرفقات (صور، PDF،
                            Excel)</label>
                        <div class="drop-zone" id="dropZone">
                            <div class="drop-zone-content">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p class="drop-zone-text">اسحب وأفلت الملفات هنا أو اضغط للاختيار</p>
                                <p class="drop-zone-hint">يمكنك رفع ملفات متعددة (الحد الأقصى 10 ميجا لكل ملف)</p>
                            </div>
                            <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;">
                        </div>
                        <div class="file-list" id="fileList"></div>
                        @error('attachments') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 32px;">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fa-solid fa-save"></i> حفظ العائد
                    </button>
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">
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
            const hospitalSelect = $('#hospitalSelect');
            const departmentSelect = $('#departmentSelect');

            // Initial Values
            const initialHospitalId = "{{ old('hospital_id', $hospital->id ?? '') }}";
            const initialDepartmentId = "{{ old('department_id', $department->id ?? '') }}";

            function populateDepartments(hospitalId, selectedDeptId = '') {
                departmentSelect.empty().append('<option value="">اختر القسم</option>');

                const hospital = hospitalsData.find(h => h.id == hospitalId);
                if (hospital && hospital.departments) {
                    hospital.departments.forEach(dept => {
                        departmentSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                    });
                }

                if (selectedDeptId) {
                    departmentSelect.val(selectedDeptId).trigger('change');
                }
            }

            hospitalSelect.on('change', function () {
                const hospId = $(this).val();
                populateDepartments(hospId);
            });

            // Set initial hospital and department
            if (initialHospitalId) {
                hospitalSelect.val(initialHospitalId).trigger('change');
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
                    fillSubOptions(metadata, 'SKIP_BRANCH', '{{ old("location", session("flow_options.location")) }}');
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
                    fillSubOptions(metadata, 'NO_BRANCH', '{{ old("location", session("flow_options.location")) }}');
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

            // Initialize with session or old values
            const initialEntity = '{{ old("entity_id", session("flow_options.entity_id")) }}';
            const initialBranch = '{{ old("branch", session("flow_options.branch")) }}';
            const initialSub = '{{ old("location", session("flow_options.location")) }}';
            const initialLaw = '{{ old("beneficiary", session("flow_options.law")) }}';

            if (initialEntity) {
                const selectedOption = entitySelect.find('option:selected');
                if (selectedOption.length) {
                    const metadata = selectedOption.data('metadata');

                    if (initialBranch) {
                        fillBranchOptions(metadata, initialBranch);
                    } else if (metadata && metadata.laws) {
                        // For UHI, skip branch and fill sub directly
                        fillSubOptions(metadata, 'SKIP_BRANCH', initialSub);
                    }

                    if (initialSub && (!metadata || !metadata.laws)) {
                        fillSubOptions(metadata, initialBranch || 'NO_BRANCH', initialSub);
                    }

                    if (initialLaw) fillLawsOptions(metadata, initialSub, initialLaw);
                }
            }
        });
    </script>

    <script>
        function calculateFinal() {
            const reviewed = parseFloat(document.getElementById('reviewed_value').value) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const tax = parseFloat(document.getElementById('tax_amount').value) || 0;

            // Final Amount calculation
            const final = reviewed - discount - tax;
            document.getElementById('final_amount').value = final.toFixed(2);
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