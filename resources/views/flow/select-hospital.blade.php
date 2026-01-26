@extends('layouts.app')

@section('title', 'اختيار المستشفى والقسم')

@section('content')
<div class="card" style="max-width: 600px; margin: 2rem auto;">
    <div class="card-header">
        اختيار المستشفى والقسم
    </div>

    <form method="POST" action="{{ route('flow.store-hospital') }}">
        @csrf
        
        <div class="form-group">
            <label class="form-label">المستشفى</label>
            <select id="hospitalSelect" name="hospital_id" class="form-control" required style="padding: 10px;">
                <option value="">اختر المستشفى...</option>
                @foreach($hospitals as $hospital)
                    <option value="{{ $hospital->id }}" data-depts="{{ $hospital->departments }}">
                        {{ $hospital->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">القسم</label>
            <select id="deptSelect" name="department_id" class="form-control" required style="padding: 10px;" disabled>
                <option value="">اختر القسم اولاً...</option>
            </select>
        </div>

        <div style="margin-top: 2rem; text-align: left;">
            <button type="submit" class="btn">
                 بدء العمليات
                <i class="fa-solid fa-check-circle" style="margin-right: 8px;"></i>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hospitalSelect = document.getElementById('hospitalSelect');
        const deptSelect = document.getElementById('deptSelect');

        hospitalSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const depts = JSON.parse(selectedOption.getAttribute('data-depts') || '[]');
            
            deptSelect.innerHTML = '<option value="">اختر القسم...</option>';
            
            if (depts.length > 0) {
                deptSelect.disabled = false;
                depts.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    deptSelect.appendChild(option);
                });
            } else {
                deptSelect.disabled = true;
            }
        });
    });
</script>
@endsection
