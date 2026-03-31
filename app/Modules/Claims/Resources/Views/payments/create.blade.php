@extends('claims::layouts.app')

@section('title', 'تسجيل أمر دفع')


<style>
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr !important;
        gap: 24px;
        margin-bottom: 24px;
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
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <!-- Hospital and Department Row -->
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
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-list-check"></i> نوع الحساب</label>
                        <select name="account_type" class="form-control select2" required>
                            <option value="">اختر النوع</option>
                            <option value="بنكى" {{ old('account_type') == 'بنكى' ? 'selected' : '' }}>بنكى</option>
                            <option value="أمر دفع برقم مؤسسى" {{ old('account_type') == 'أمر دفع برقم مؤسسى' ? 'selected' : '' }}>أمر دفع برقم مؤسسى</option>
                            <option value="شيك نقدى" {{ old('account_type') == 'شيك نقدى' ? 'selected' : '' }}>شيك نقدى
                            </option>
                        </select>
                        @error('account_type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم الـ GP / الشيك</label>
                        <input type="text" name="gp_number" class="form-control" value="{{ old('gp_number') }}" required>
                        @error('gp_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> المبلغ (ج.م)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}"
                            required>
                        @error('amount') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
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

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" class="form-control"
                            value="{{ old('electronic_invoice_no') }}">
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
                    <textarea name="notes" class="form-control" rows="3"
                        placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-actions" style="margin-top: 32px;">
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
            
            // Initial Values (note input name payee_hospital_id)
            const initialHospitalId = "{{ old('payee_hospital_id', $hospital->id ?? '') }}";
            const initialDepartmentId = "{{ old('department_id', $department->id ?? '') }}";
            const isWaitingListHospital = initialHospitalId && !$.isNumeric(initialHospitalId);

            function populateDepartments(hospitalId, selectedDeptId = '') {
                departmentSelect.empty().append('<option value="">اختر القسم</option>');
                
                // If it's a waiting list hospital (non-numeric), show ALL departments from DB
                if (hospitalId && !$.isNumeric(hospitalId)) {
                    // First, if there's a selected department from waiting list (string), add it as selected
                    if (selectedDeptId && !$.isNumeric(selectedDeptId)) {
                        departmentSelect.append(`<option value="${selectedDeptId}" selected>${selectedDeptId}</option>`);
                    }
                    
                    // Populate all departments from allDepartmentsData
                    allDepartmentsData.forEach(dept => {
                        // Skip if this is the already-added selected department
                        if (selectedDeptId && dept.name === selectedDeptId) {
                            return;
                        }
                        const isSelected = selectedDeptId == dept.id ? 'selected' : '';
                        departmentSelect.append(`<option value="${dept.id}" ${isSelected}>${dept.name}</option>`);
                    });
                    departmentSelect.trigger('change');
                    return;
                }
                
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
            
            // Flow type from PHP session
            const flowType = '{{ $entityType ?? '' }}';
            const preselectedBranch = '{{ $branch ?? '' }}';
            const preselectedLocation = '{{ $location ?? '' }}';
            
            console.log('Flow Type:', flowType);
            console.log('Preselected Branch:', preselectedBranch);
            console.log('Preselected Location:', preselectedLocation);

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
            console.log('Preselected values:', { branch: preselectedBranch, location: preselectedLocation });
            
            if (initialEntity) {
                entitySelect.val(initialEntity).trigger('change');
                
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
                                handleLocation(metadata, preselectedLocation);
                            }, 300);
                        } 
                        // Case 2: Entity has NO branches but has locations/governorates (e.g., Insurance, Comprehensive)
                        else if (preselectedLocation && !preselectedBranch) {
                            console.log('No branch, handling location directly');
                            handleLocation(metadata, preselectedLocation);
                        }
                    }, 300);
                }
            }
            
            // Helper function to set location
            function handleLocation(metadata, locationVal) {
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
                    subSelect.val(locationVal).trigger('change');
                    console.log('Location set to:', locationVal);
                } else {
                    console.log('Location not found in dropdown, options are:', subSelect.find('option').map(function() { return $(this).val(); }).get());
                }
            }
        });
    </script>
@endsection