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
                                <option value="{{ $hosp->id }}" {{ (isset($hospital) && $hospital->id == $hosp->id) ? 'selected' : '' }}>
                                    {{ $hosp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('payee_hospital_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-stethoscope"></i> القسم</label>
                        <select name="department_id" id="departmentSelect" class="form-control select2" required>
                            <option value="">اختر القسم</option>
                            <!-- Departments filled by JS -->
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
                                <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)' {{ old('payer_entity_id') == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
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
            const hospitalSelect = $('#hospitalSelect');
            const departmentSelect = $('#departmentSelect');
            
            // Initial Values (note input name payee_hospital_id)
            const initialHospitalId = "{{ old('payee_hospital_id', $hospital->id ?? '') }}";
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

            hospitalSelect.on('change', function() {
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
                    fillSubOptions(metadata, 'NO_BRANCH', '{{ old("location") }}');
                }
            }

            function fillSubOptions(metadata, branchVal, selectedSub = '') {
                subSelect.empty().append('<option value="">اختر الاختيار</option>');
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                lawsContainer.slideUp(300);

                if (!branchVal && branchVal !== 'SKIP_BRANCH' && branchVal !== 'NO_BRANCH') return;

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

            // Initialize if old values exist
            const initialEntity = '{{ old("payer_entity_id") }}';
            const initialBranch = '{{ old("branch") }}';
            // sub/law handled by triggers if we set initialBranch correctly inside flow
            // But we can call logic manually to be safe

            if (initialEntity) {
                const selectedOption = entitySelect.find('option:selected');
                if (selectedOption.length) {
                    const metadata = selectedOption.data('metadata');
                    fillBranchOptions(metadata, initialBranch);
                    // Dependent calls are handled by branchSelect change or manual check inside fillBranchOptions?
                    // fillBranchOptions triggers change if selectedBranch is passed. 
                    // But if selectedBranch is empty (skipped), we called fillSubOptions directly.
                }
            }
        });
    </script>
@endsection