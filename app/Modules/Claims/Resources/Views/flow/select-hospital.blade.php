@extends('claims::layouts.app')
@section('title', 'اختيار المستشفى والقسم')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <a href="{{ route('flow.options') }}" class="breadcrumb-item">
        الخيارات
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <span class="breadcrumb-item active">اختيار المستشفى والقسم</span>
</div>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-hospital"></i> اختيار المستشفى والقسم</h1>
    <p class="page-subtitle">اختر المستشفى والقسم المطلوب</p>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('flow.store-hospital') }}" id="hospitalForm">
        @csrf

        <div class="selection-sections">
            <!-- Section 1: Hospitals -->
            <div class="selection-column">
                <div class="column-header">
                    <h2 class="column-title">
                        <i class="fa-solid fa-hospital"></i> المستشفيات
                    </h2>
                    <p class="column-subtitle">اختر مستشفى واحدة من القائمة التالية</p>
                </div>

                <div class="hospitals-grid">
                    @foreach($hospitals as $hospital)
                    <label class="selection-card hospital-card" data-hospital="{{ $hospital->id }}">
                        <input type="radio" name="hospital_id" value="{{ $hospital->id }}"
                            class="hospital-checkbox" required>

                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fa-solid fa-hospital"></i>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title">{{ $hospital->name }}</h3>
                            </div>
                            <div class="card-indicator"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Section 2: Departments -->
            <div class="selection-column" id="departmentsColumn" style="display: none; border-top: 1px solid #eee; padding-top: 30px;">
                <div class="column-header">
                    <h2 class="column-title">
                        <i class="fa-solid fa-list-ul"></i> الأقسام المتاحة
                    </h2>
                    <p class="column-subtitle">اختر القسم المختص للمتابعة</p>
                </div>

                <div class="departments-grid" id="departmentsContainer">
                    <!-- Departments populated by JS -->
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 40px;">
            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                <i class="fa-solid fa-arrow-left"></i> التالي
            </button>
            <a href="{{ route('flow.options') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-right"></i> العودة
            </a>
        </div>
    </form>
</div>

<!-- Hidden data for JavaScript -->
<script type="application/json" id="hospitalsData">
{
    @foreach($hospitals as $hospital)
        "{{ $hospital->id }}": {
            "name": "{{ $hospital->name }}",
            "departments": [
                @foreach($hospital->departments as $dept)
                    {
                        "id": "{{ $dept->id }}",
                        "name": "{{ $dept->name }}"
                    }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        }{{ !$loop->last ? ',' : '' }}
    @endforeach
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hospitalCheckboxes = document.querySelectorAll('.hospital-checkbox');
        const departmentsColumn = document.getElementById('departmentsColumn');
        const departmentsContainer = document.getElementById('departmentsContainer');
        const submitBtn = document.getElementById('submitBtn');
        const hospitalsData = JSON.parse(document.getElementById('hospitalsData').textContent);

        hospitalCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    const hospitalId = this.value;
                    const hospital = hospitalsData[hospitalId];

                    // Update departments
                    departmentsContainer.innerHTML = '';

                    if (hospital && hospital.departments.length > 0) {
                        hospital.departments.forEach(dept => {
                            const label = document.createElement('label');
                            label.className = 'selection-card department-card';
                            label.innerHTML = `
                            <input type="radio" name="department_id" value="${dept.id}" 
                                   class="department-checkbox" required>
                            
                            <div class="card-content">
                                <div class="card-icon">
                                    <i class="fa-solid fa-stethoscope"></i>
                                </div>
                                <div class="card-info">
                                    <h3 class="card-title">${dept.name}</h3>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        `;
                            departmentsContainer.appendChild(label);
                        });
                    } else {
                        departmentsContainer.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle"></i>
                            <span>لا توجد أقسام متاحة لهذه المستشفى</span>
                        </div>
                    `;
                    }

                    departmentsColumn.style.display = 'block';
                    submitBtn.disabled = true;

                    // Add listener to department checkboxes
                    const deptCheckboxes = departmentsContainer.querySelectorAll('.department-checkbox');
                    deptCheckboxes.forEach(deptCheckbox => {
                        deptCheckbox.addEventListener('change', function() {
                            submitBtn.disabled = !this.checked;
                        });
                    });
                }
            });
        });
    });
</script>
@endsection
