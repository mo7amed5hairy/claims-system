@extends('claims::layouts.app')

@section('title', 'إدارة المستشفيات والأقسام')

@section('content')
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item"><i class="fa-solid fa-home"></i> الرئيسية</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">المستشفيات والأقسام</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-hospital"></i> إدارة المستشفيات والأقسام</h1>
        @can('create', App\Modules\Claims\Models\Hospital::class)
            <button class="btn btn-primary" onclick="$('#addHospitalModal').fadeIn()">
                <i class="fa-solid fa-plus"></i> إضافة مستشفى وأقسام
            </button>
        @endcan
    </div>

    <div class="hospital-grid"
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px;">
        @foreach($hospitals as $hospital)
            <div class="form-card" style="margin-bottom: 0;">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #1e293b; font-size: 18px;">
                        <i class="fa-solid fa-h-square" style="color: #3b82f6;"></i> {{ $hospital->name }}
                    </h3>
                    <div class="actions">
                        @can('update', $hospital)
                            <button class="btn-icon" title="إضافة أقسام"
                                onclick="addDept('{{ $hospital->id }}', '{{ $hospital->name }}')">
                                <i class="fa-solid fa-plus-circle text-success"></i>
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="dept-list">
                    <h4 style="font-size: 13px; color: #64748b; margin-bottom: 10px;">الأقسام المتاحة:</h4>
                    @if($hospital->departments->count() > 0)
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach($hospital->departments as $dept)
                                <span class="badge"
                                    style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #e2e8f0;">
                                    <i class="fa-solid fa-stethoscope" style="font-size: 10px; color: #64748b;"></i> {{ $dept->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size: 12px; color: #94a3b8; font-style: italic;">لا يوجد أقسام مضافة بعد</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 24px;">
        {{ $hospitals->links() }}
    </div>

    <!-- Add Hospital Modal -->
    <div id="addHospitalModal" class="modal-overlay"
        style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: none; align-items: center; justify-content: center;">
        <div class="form-card"
            style="margin: 8rem 30rem !important; width: 100%; max-width: 550px; margin: 0; max-height: 90vh; overflow-y: auto;">
            <div class="card-header">
                <h3 style="margin: 0;"><i class="fa-solid fa-hospital"></i> إضافة مستشفى وأقسامها</h3>
            </div>
            <form action="{{ route('hospitals.store') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-top: 20px;">
                    <label class="form-label">اسم المستشفى</label>
                    <input type="text" name="name" class="form-control" placeholder="أدخل اسم المستشفى" required>
                </div>

                <div class="departments-wrapper" style="margin-top: 20px;">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                        الأقسام
                        <button type="button" class="btn btn-sm btn-outline-success add-dept-row"
                            onclick="cloneDeptRow('#addHospitalModal .dept-rows')"
                            style="padding: 4px 8px; font-size: 12px;">
                            <i class="fa-solid fa-plus-circle"></i> إضافة قسم آخر
                        </button>
                    </label>
                    <div class="dept-rows" style="margin-top: 10px;">
                        <div class="dept-input-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="text" name="departments[]" class="form-control" placeholder="اسم القسم" required>
                            <button type="button" class="btn btn-outline-danger remove-dept-row"
                                onclick="$(this).parent().remove()" style="padding: 0 12px; display: none;">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">حفظ الكل</button>
                    <button type="button" class="btn btn-secondary"
                        onclick="$('#addHospitalModal').fadeOut()">إغلاق</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Dept Modal (For Existing Hospital) -->
    <div id="addDeptModal" class="modal-overlay"
        style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: none; align-items: center; justify-content: center;">
        <div class="form-card" style="width: 100%; max-width: 550px; margin: 0; max-height: 90vh; overflow-y: auto;">
            <div class="card-header">
                <h3 style="margin: 0;"><i class="fa-solid fa-stethoscope"></i> إضافة أقسام لـ <span id="hospName"></span>
                </h3>
            </div>
            <form id="deptForm" method="POST">
                @csrf
                <div class="departments-wrapper" style="margin-top: 20px;">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                        الأقسام
                        <button type="button" class="btn btn-sm btn-outline-success add-dept-row"
                            onclick="cloneDeptRow('#addDeptModal .dept-rows')" style="padding: 4px 8px; font-size: 12px;">
                            <i class="fa-solid fa-plus-circle"></i> إضافة قسم آخر
                        </button>
                    </label>
                    <div class="dept-rows" style="margin-top: 10px;">
                        <div class="dept-input-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="text" name="departments[]" class="form-control" placeholder="اسم القسم" required>
                            <button type="button" class="btn btn-outline-danger remove-dept-row"
                                onclick="$(this).parent().remove()" style="padding: 0 12px; display: none;">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">حفظ الأقسام</button>
                    <button type="button" class="btn btn-secondary" onclick="$('#addDeptModal').fadeOut()">إغلاق</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function cloneDeptRow(containerSelector) {
            const container = $(containerSelector);
            const row = container.find('.dept-input-row:first').clone();
            row.find('input').val('').prop('required', true);
            row.find('.remove-dept-row').show();
            container.append(row);
        }

        function addDept(id, name) {
            $('#hospName').text(name);
            $('#deptForm').attr('action', `/hospitals/${id}/departments`);
            // Reset modal rows to one
            $('#addDeptModal .dept-rows').html(`
                                    <div class="dept-input-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                        <input type="text" name="departments[]" class="form-control" placeholder="اسم القسم" required>
                                        <button type="button" class="btn btn-outline-danger remove-dept-row" onclick="$(this).parent().remove()" style="padding: 0 12px; display: none;">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                `);
            $('#addDeptModal').css('display', 'flex').fadeIn();
        }

        $(document).ready(function () {
            $('.modal-overlay').on('click', function (e) {
                if (e.target === this) $(this).fadeOut();
            });
        });
    </script>
@endsection