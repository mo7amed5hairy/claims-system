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
    </div>

    <div class="form-container">
        <div class="form-card">
            <form action="{{ route('claims.update', $claim->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Hospital and Department Row -->
                <div class="form-row">

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hospital"></i> المستشفى</label>
                        <select name="hospital_id" id="hospitalSelect" class="form-control select2" required>
                            <option value="">اختر المستشفى</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}" {{ (old('hospital_id', $claim->hospital_id) == $hosp->id) ? 'selected' : '' }}>
                                    {{ $hosp->name }}
                                </option>
                            @endforeach
                            {{-- Add waiting list hospital if not in DB --}}
                            @php
                                $hospitalNames = [
                                    'ain_shams' => 'مستشفى عين شمس',
                                    'children' => 'مستشفى الأطفال',
                                    'women' => 'مستشفى النساء',
                                    'other' => 'أخرى'
                                ];
                            @endphp
                            @if($claim->hospital_id && !is_numeric($claim->hospital_id))
                                <option value="{{ $claim->hospital_id }}" selected>
                                    {{ $hospitalNames[$claim->hospital_id] ?? $claim->hospital_id }}
                                </option>
                            @endif
                        </select>
                        @error('hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required>
                            <option value="">اختر القسم</option>
                            {{-- Add waiting list department if not numeric --}}
                            @if($claim->department_id && !is_numeric($claim->department_id))
                                <option value="{{ $claim->department_id }}" selected>
                                    {{ $claim->department_id }}
                                </option>
                            @endif
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

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

                <!-- Delivery Information Section -->
                <div class="form-section" style="padding: 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; margin-bottom: 25px;">
                    <h3 class="section-title" style="color: #334155;">
                        <i class="fa-solid fa-truck-fast"></i> بيانات تسليم المطالبة
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-calendar-check"></i> تاريخ التسليم
                            </label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $claim->delivery_date ? $claim->delivery_date->format('Y-m-d') : '') }}">
                            @error('delivery_date') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-upload"></i> مرفقات التسليم (استبدال)
                            </label>
                            <input type="file" name="delivery_attachments[]" class="form-control" multiple>
                            <small class="text-muted" style="font-size: 10px;">تحميل ملفات جديدة سيستبدل المرفقات القديمة لبيانات التسليم.</small>
                        </div>
                    </div>

                    @if($claim->delivery_attachments && count($claim->delivery_attachments) > 0)
                        <div class="form-section" style="margin-top: 15px;">
                            <label class="form-label" style="font-size: 12px; color: #64748b;">مرفقات التسليم الحالية:</label>
                            <div class="file-item" style="display: flex; justify-content: flex-start; flex-direction: row; gap: 2rem;">
                                @foreach($claim->delivery_attachments as $path)
                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" class="fa-solid fa-paperclip" style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 11px; text-decoration: none; border: 1px solid #e2e8f0;">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
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