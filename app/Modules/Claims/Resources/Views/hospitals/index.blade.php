@extends('layouts.app')

@section('title', 'إدارة المستشفيات')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span>قائمة المستشفيات والأقسام</span>
        <button class="btn" onclick="toggleModal('addHospitalModal')">إضافة مستشفى</button>
    </div>

    <div style="margin-top: 1rem;">
        @foreach($hospitals as $hospital)
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; margin-bottom: 1rem; padding: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                <h3 style="color: var(--primary-color);">{{ $hospital->name }}</h3>
                <a href="#" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">+ إضافة قسم</a>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($hospital->departments as $dept)
                    <span style="background: white; border: 1px solid #d1d5db; padding: 5px 10px; border-radius: 1rem; font-size: 0.9rem;">
                        {{ $dept->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Add Hospital -->
<div id="addHospitalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 400px; margin: 0;">
        <div class="card-header">إضافة مستشفى جديدة</div>
        <form action="{{ route('hospitals.store') }}" method="POST">
            @csrf
            <div class="form-group"><label class="form-label">الاسم</label><input type="text" name="name" class="form-control" required></div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn" style="background: var(--secondary-color);" onclick="toggleModal('addHospitalModal')">إلغاء</button>
                <button type="submit" class="btn">حفظ</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Dept -->
<div id="addDeptModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 400px; margin: 0;">
        <div class="card-header">إضافة قسم لـ <span id="targetHospitalName"></span></div>
        <form id="deptForm" method="POST">
            @csrf
            <div class="form-group"><label class="form-label">اسم القسم</label><input type="text" name="name" class="form-control" required></div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn" style="background: var(--secondary-color);" onclick="toggleModal('addDeptModal')">إلغاء</button>
                <button type="submit" class="btn">إضافة</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
}
function addDept(id, name) {
    document.getElementById('targetHospitalName').textContent = name;
    document.getElementById('deptForm').action = "/hospitals/" + id + "/departments";
    toggleModal('addDeptModal');
}
</script>
@endsection
