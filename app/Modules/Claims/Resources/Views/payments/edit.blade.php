@extends('claims::layouts.app')

@section('title', 'تعديل أمر دفع')

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
            <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Hospital and Department Row -->
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
                            {{-- Add waiting list hospital if not in DB --}}
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
                            {{-- Add waiting list department if not numeric --}}
                            @if($payment->department_id && !is_numeric($payment->department_id))
                                <option value="{{ $payment->department_id }}" selected>
                                    {{ $payment->department_id }}
                                </option>
                            @endif
                        </select>
                        @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-list-check"></i> نوع الحساب</label>
                        <select name="account_type" class="form-control select2" required>
                            <option value="">اختر النوع</option>
                            <option value="بنكى" {{ old('account_type', $payment->account_type) == 'بنكى' ? 'selected' : '' }}>بنكى</option>
                            <option value="أمر دفع برقم مؤسسى" {{ old('account_type', $payment->account_type) == 'أمر دفع برقم مؤسسى' ? 'selected' : '' }}>أمر دفع برقم مؤسسى</option>
                            <option value="شيك نقدى" {{ old('account_type', $payment->account_type) == 'شيك نقدى' ? 'selected' : '' }}>شيك نقدى</option>
                        </select>
                        @error('account_type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم الـ GP / الشيك</label>
                        <input type="text" name="gp_number" class="form-control"
                            value="{{ old('gp_number', $payment->gp_number) }}" required>
                        @error('gp_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> المبلغ (ج.م)</label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                            value="{{ old('amount', $payment->amount) }}" required>
                        @error('amount') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calendar-check"></i> تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control"
                            value="{{ old('due_date', $payment->due_date->format('Y-m-d')) }}" required>
                        @error('due_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
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
                        @error('payer_entity_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" class="form-control"
                            value="{{ old('electronic_invoice_no', $payment->electronic_invoice_no) }}">
                        @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Dynamic Fields for Entity -->
                <div class="form-row d-flex" style="gap: 8px; margin: 0; margin-bottom: 15px;">

                    <div class="form-group" id="branchContainer" style="display: none; flex: 1; margin: 0;">
                        <label class="form-label">
                            <i class="fa-solid fa-layer-group"></i> الفروع
                        </label>
                        <select name="branch" id="branchSelect" class="form-control select2">
                            <option value="">اختر الفرع</option>
                        </select>
                    </div>

                    <div class="form-group" id="subContainer" style="display: none; flex: 1; margin: 0;">
                        <label class="form-label" id="subLabel">
                            <i class="fa-solid fa-map-marker-alt"></i> المحافظات / المواقع
                        </label>
                        <select name="location" id="subSelect" class="form-control select2">
                            <option value="">اختر المحافظة / الموقع</option>
                        </select>
                    </div>

                    <div class="form-group" id="lawsContainer" style="display: none; flex: 1; margin: 0;">
                        <label class="form-label">
                            <i class="fa-solid fa-file-lines"></i> المستفيدين / القوانين
                        </label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2">
                            <option value="">اختر المستفيد</option>
                        </select>
                    </div>

                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                    @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-actions" style="margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">
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
@endsection