@extends('claims::layouts.app')

@section('title', 'إنشاء مطالبة جديدة')

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

                <!-- Hospital and Department Row -->
                <div class="form-row-4 form-section">

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="hospital_id" id="hospitalSelect" class="form-control select2" required>
                            <option value="">اختر المستشفى</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}">
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


                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-building"></i> الجهة</label>
                        <select name="entity_id" id="entitySelect" class="form-control select2" required>
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)'>
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


                </div>

                <!-- Main Info Row: 4 Columns -->
                <div class="form-row-4 form-section">


                    <!-- قائمة المستفيدين / القوانين -->
                    <div class="form-group" id="lawsContainer" style="display: none;">
                        <label class="form-label"><i class="fa-solid fa-file-lines"></i> المستفيدين / القوانين</label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2">
                            <option value="">اختر المستفيد</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-calculator"></i> عدد الفواتير
                        </label>
                        <input type="number" name="invoice_count" class="form-control" min="1"
                            value="{{ old('invoice_count') }}" required>
                        @error('invoice_count') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-calendar"></i> الشهر
                        </label>
                        <select name="month" class="form-control select2" required>
                            <option value="">اختر الشهر</option>
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




                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-calendar-days"></i> تاريخ المطالبة
                        </label>
                        <input type="date" name="claim_date" class="form-control" value="{{ old('claim_date') }}" required>
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
                                value="{{ old('claim_value') }}" required oninput="calculateDiff()">
                            @error('claim_value') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-check-circle"></i> المبلغ بعد المراجعة (ج.م)
                            </label>
                            <input type="number" step="0.01" id="reviewedValue" name="reviewed_value" class="form-control"
                                min="0" value="{{ old('reviewed_value') }}" oninput="calculateDiff()">
                            @error('reviewed_value') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-equals"></i> الفرق (ج.م)
                            </label>
                            <input type="number" step="0.01" id="differenceValue" name="difference" class="form-control"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-user-check"></i> اسم المراجع
                            </label>
                            <input type="text" name="reviewer_name" class="form-control" value="{{ old('reviewer_name') }}">
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
                                value="{{ old('electronic_invoice_no') }}">
                            @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-receipt"></i> رقم المطالبة التأمينية
                            </label>
                            <input type="text" name="insurance_claim_number" class="form-control"
                                value="{{ old('insurance_claim_number') }}">
                            @error('insurance_claim_number') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-note-sticky"></i> ملاحظات
                        </label>
                        <textarea name="notes" class="form-control" rows="4"
                            placeholder="أضف أي ملاحظات تتعلق بالمطالبة...">{{ old('notes') }}</textarea>
                        @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Delivery Information Section -->
                <div class="form-section"
                    style="padding: 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; margin-bottom: 25px;">
                    <h3 class="section-title" style="color: #334155;">
                        <i class="fa-solid fa-truck-fast"></i> بيانات تسليم المطالبة
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-calendar-check"></i> تاريخ التسليم
                            </label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
                            @error('delivery_date') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-upload"></i> مرفقات التسليم
                            </label>
                            <input type="file" name="delivery_attachments[]" class="form-control" multiple>
                            <small class="text-muted" style="font-size: 10px;">يمكنك اختيار ملفات متعددة (صور، PDF،
                                إكسل)</small>
                        </div>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fa-solid fa-paperclip"></i> المرفقات (صور، PDF، Excel)
                    </h3>

                    <div class="drop-zone" id="dropZone">
                        <div class="drop-zone-content">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p class="drop-zone-text">اسحب وأفلت الملفات هنا أو اضغط للاختيار</p>
                            <p class="drop-zone-hint">يمكنك رفع ملفات متعددة (الحد الأقصى 10 ميجا لكل ملف)</p>
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
                        <i class="fa-solid fa-save"></i> حفظ المطالبة
                    </button>
                    <a href="{{ route('flow.operations') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-right"></i> العودة
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
<!-- 
@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Consolidated Select2 Initialization
            $('.select2').select2({
                dir: "rtl",
                width: '100%',
                closeOnSelect: true
            }).on('select2:select', function (e) {
                $(this).select2('close');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Select2 auto-close handled in app.blade.php globally

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
                // We need to wait for change to fire or call manually, but we also need to pass the dept ID
                // Trigger change calls the listener which clears the dept. So we must call populate directly.
                populateDepartments(initialHospitalId, initialDepartmentId);
            }


            // Entity & Dynamic Fields Logic
            const entitySelect = $('#entitySelect');
            const branchContainer = $('#branchContainer');
            const branchSelect = $('#branchSelect');
            const subContainer = $('#subContainer');
            const subSelect = $('#subSelect');
            const subLabel = $('#subLabel');
            const lawsContainer = $('#lawsContainer');
            const lawsSelect = $('#lawsSelect');

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
                branchContainer.slideUp(300);
                subContainer.slideUp(300);
                lawsContainer.slideUp(300);

                if (!metadata) return;

                // === لو الهيئة العامة للتأمين الصحي أو التأمين الصحي الشامل ===
                if (metadata.laws) {
                    // 1. Fill Branches (فروع / قوائم انتظار)
                    if (metadata.branches && metadata.branches.length > 0) {
                        branchSelect.empty().append('<option value="">اختر الفرع</option>');
                        metadata.branches.forEach(branch => {
                            branchSelect.append(`<option value="${branch}">${branch}</option>`);
                        });
                        branchContainer.slideDown(300);
                    }

                    // مستمع الفرع → يعرض المحافظات / المواقع
                    branchSelect.off('change').on('change', function () {
                        subSelect.empty().append('<option value="">اختر المحافظة / الموقع</option>');
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        lawsContainer.slideUp(300);

                        const selectedBranch = $(this).val();
                        if (!selectedBranch) {
                            subContainer.slideUp(300);
                            return;
                        }

                        let subItems = metadata.locations || metadata.governorates || [];
                        if (!subItems.length) {
                            subContainer.slideUp(300);
                            return;
                        }

                        subItems.forEach(item => {
                            subSelect.append(`<option value="${item}">${item}</option>`);
                        });
                        subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                        subContainer.slideDown(300);
                    });

                    // مستمع المحافظة → يعرض المستفيدين / القوانين
                    subSelect.off('change').on('change', function () {
                        lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                        const selectedSub = $(this).val();
                        if (!selectedSub || !metadata.laws) {
                            lawsContainer.slideUp(300);
                            return;
                        }

                        metadata.laws.forEach(law => {
                            lawsSelect.append(`<option value="${law}">${law}</option>`);
                        });
                        lawsContainer.slideDown(300);
                    });

                    return; // انتهى المعالجة الخاصة بالهيئة
                }

                // === باقي الحالات العادية ===
                fillBranchOptions(metadata);
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

            // ==== تهيئة الصفحة عند التحميل فقط إذا فيه اختيارات سابقة موجودة ====
            const initialEntity = '{{ session("flow_options.entity_id", "") }}';
            const initialBranch = '{{ session("flow_options.branch", "") }}';
            const initialSub = '{{ session("flow_options.location", "") }}';
            const initialLaw = '{{ session("flow_options.law", "") }}';

            if (initialEntity) {
                entitySelect.val(initialEntity).trigger('change');
                const selectedOption = entitySelect.find('option:selected');
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
@endsection -->





@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Consolidated Select2 Initialization
            $('.select2').select2({
                dir: "rtl",
                width: '100%',
                closeOnSelect: true
            }).on('select2:select', function (e) {
                $(this).select2('close');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Select2 auto-close handled in app.blade.php globally

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


            // Entity & Dynamic Fields Logic
            const entitySelect = $('#entitySelect');
            const branchContainer = $('#branchContainer');
            const branchSelect = $('#branchSelect');
            const subContainer = $('#subContainer');
            const subSelect = $('#subSelect');
            const subLabel = $('#subLabel');
            const lawsContainer = $('#lawsContainer');
            const lawsSelect = $('#lawsSelect');

            function updateSelectData(selectElement, placeholder, optionsArray) {
                let html = '<option value="">' + placeholder + '</option>';
                if (optionsArray && optionsArray.length) {
                    optionsArray.forEach(item => {
                        html += '<option value="' + item + '">' + item + '</option>';
                    });
                }

                // تدمير Select2 الحالي لإعادة بنائه بشكل نظيف
                try {
                    selectElement.select2('destroy');
                } catch (e) { }

                selectElement.html(html);

                // إعادة التهيئة
                selectElement.select2({ dir: "rtl", width: '100%', closeOnSelect: true });
            }

            function clearLowerFields(level) {
                if (level <= 1) {
                    updateSelectData(branchSelect, 'اختر الفرع', []);
                    branchContainer.slideUp(300);
                }
                if (level <= 2) {
                    updateSelectData(subSelect, 'اختر المحافظة / الموقع', []);
                    subContainer.slideUp(300);
                }
                if (level <= 3) {
                    updateSelectData(lawsSelect, 'اختر المستفيد', []);
                    lawsContainer.slideUp(300);
                }
            }

            entitySelect.on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const metadata = selectedOption.data('metadata');

                clearLowerFields(1);

                if (!metadata) return;

                if (metadata.branches && metadata.branches.length > 0) {
                    updateSelectData(branchSelect, 'اختر الفرع', metadata.branches);
                    branchContainer.slideDown(300);
                } else {
                    let subItems = metadata.locations || metadata.governorates || [];
                    if (subItems.length > 0) {
                        updateSelectData(subSelect, 'اختر المحافظة / الموقع', subItems);
                        subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                        subContainer.slideDown(300);
                    }
                }
            });

            branchSelect.on('change', function () {
                const branchVal = $(this).val();
                const metadata = entitySelect.find('option:selected').data('metadata');

                clearLowerFields(2);

                if (!branchVal || !metadata) return;

                let subItems = metadata.locations || metadata.governorates || [];
                if (subItems.length > 0) {
                    updateSelectData(subSelect, 'اختر المحافظة / الموقع', subItems);
                    subLabel.text(metadata.governorates ? 'المحافظات' : 'المواقع');
                    subContainer.slideDown(300);
                }
            });

            subSelect.on('change', function () {
                const subVal = $(this).val();
                const metadata = entitySelect.find('option:selected').data('metadata');

                clearLowerFields(3);

                if (!subVal || !metadata) return;

                if (metadata.laws && metadata.laws.length > 0) {
                    updateSelectData(lawsSelect, 'اختر المستفيد', metadata.laws);
                    lawsContainer.slideDown(300);
                }
            });

            const initialEntity = '{{ session("flow_options.entity_id", "") }}';
            const initialBranch = '{{ session("flow_options.branch", "") }}';
            const initialSub = '{{ session("flow_options.location", "") }}';
            const initialLaw = '{{ session("flow_options.law", "") }}';

            if (initialEntity) {
                entitySelect.val(initialEntity).trigger('change');
                if (initialBranch) {
                    branchSelect.val(initialBranch).trigger('change');
                }
                if (initialSub) {
                    subSelect.val(initialSub).trigger('change');
                }
                if (initialLaw) {
                    lawsSelect.val(initialLaw).trigger('change');
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
            div.setAttribute('data-name', file.name);

            const extension = file.name.split('.').pop().toLowerCase();
            let icon = 'fa-file-lines';
            let color = '#3b82f6';

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

            fileItem.uploadInterval = interval;
        }

        function syncInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;

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