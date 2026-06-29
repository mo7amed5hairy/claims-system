@extends('claims::layouts.app')

@section('title', 'قوائم انتظار مديرية الشئون الصحية')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.waiting-lists') }}" class="breadcrumb-item">قوائم الانتظار</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">مديرية الشئون الصحية</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-hospital"></i> قوائم انتظار مديرية الشئون الصحية</h1>
        <p class="page-subtitle">اختر المستشفى والقسم</p>
    </div>

    <div style="max-width: 700px; margin: 0 auto; padding: 0 16px;">
        <div class="form-card">
            <form method="POST" action="{{ route('flow.store-waiting-lists-ministry') }}">
                @csrf

                <!-- Hospital Selection -->
                <div class="form-group" id="hospital_container">
                    <label class="form-label">
                        <i class="fa-solid fa-hospital"></i> المستشفى
                    </label>
                    <select name="hospital" id="hospital_select" class="form-control select2" required>
                        <option value="">اختر المستشفى...</option>
                        <option value="children">مستشفى الأطفال</option>
                        <option value="women">مستشفى النساء والتوليد</option>
                        <option value="ain_shams">مستشفى عين شمس الباطنة</option>
                        <option value="others">باقى المستشفيات</option>
                    </select>
                </div>

                <!-- Department Selection -->
                <div id="departments_container" class="form-group" style="display:none;">
                    <label class="form-label">
                        <i class="fa-solid fa-building-user"></i> القسم / المستشفى
                    </label>
                    <select name="department" id="department_select" class="form-control select2">
                        <option value="">اختر...</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" id="next_btn" class="btn btn-primary" disabled>
                        <i class="fa-solid fa-arrow-left"></i> التالي
                    </button>
                    <a href="{{ route('flow.waiting-lists') }}" class="btn btn-secondary">
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

            const nextBtn = $('#next_btn');

            function enableNext() {
                nextBtn.prop('disabled', false).css('opacity', '1').css('cursor', 'pointer');
            }

            function disableNext() {
                nextBtn.prop('disabled', true).css('opacity', '0.6').css('cursor', 'not-allowed');
            }

            function validateFlow() {
                const hospital = $('#hospital_select').val();
                const department = $('#department_select').val();

                if (!hospital) {
                    disableNext();
                    return;
                }

                // Check if department is required (for hospitals that have departments)
                const hospitalsWithDepartments = ['children', 'women', 'ain_shams'];
                const requiresDepartment = hospitalsWithDepartments.includes(hospital);

                if (requiresDepartment && !department) {
                    disableNext();
                    return;
                }

                // For "others" hospital, department contains the hospital name
                if (hospital === 'others' && !department) {
                    disableNext();
                    return;
                }

                enableNext();
            }

            // Hospital Departments Data
            const departmentsData = {
                'children': [
                    'قسم الأطفال العام',
                    'قسم حديثي الولادة',
                    'قسم العناية المركزة للأطفال'
                ],
                'women': [
                    'قسم النساء العام',
                    'قسم الولادة',
                    'قسم العناية المركزة للنساء'
                ],
                'ain_shams': [
                    'قسم القلب والرعاية المركزة',
                    'قسم السكتة الدماغية'
                ],
                'others': [
                    'مستشفى الدمرداش الجراحى',
                    'مستشفى العبور',
                    'مستشفى أمراض وجراحات القلب'
                ]
            };

            $('#hospital_select').on('change', function () {
                const hospital = $(this).val();

                // Reset and hide containers
                $('#departments_container').slideUp(400);
                $('#department_select').val('').trigger('change.select2');

                if (!hospital) {
                    validateFlow();
                    return;
                }

                // Populate departments dropdown
                const departments = departmentsData[hospital] || [];
                if (departments.length > 0) {
                    $('#department_select').empty();

                    if (hospital === 'others') {
                        $('#department_select').append('<option value="">اختر المستشفى...</option>');
                    } else {
                        $('#department_select').append('<option value="">اختر القسم...</option>');
                    }

                    departments.forEach(dept => {
                        $('#department_select').append(new Option(dept, dept));
                    });

                    $('#departments_container').stop(true, true).delay(400).slideDown(400);
                }

                validateFlow();
            });

            $('#department_select').on('change', function () {
                validateFlow();
            });

            // Initial validation
            validateFlow();
        });
    </script>
@endsection
