@extends('claims::layouts.app')

@section('title', 'إدارة جهات المطالبة')

@section('scripts')
<style>
    /* تحسين شكل الجدول */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-color) !important;
        color: white !important;
        border: none !important;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f0f0f0 !important;
        border: 1px solid #ddd !important;
        color: black !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
        margin-right: 10px;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }

    table.dataTable thead th {
        border-bottom: 2px solid #eee !important;
        background: #f8fafc;
    }

    .table-container {
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }
    
    .dt-buttons {
        margin-bottom: 15px;
    }
</style>

<script>
    $(document).ready(function() {
        $('#entitiesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i> تصدير إكسل',
                    className: 'btn-excel',
                    attr: {
                        style: 'background: #198754 !important; color: white; border: none; padding: 5px 15px; border-radius: 4px; font-family: Cairo; margin-bottom: 10px; cursor: pointer;'
                    },
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('sheetViews sheetView', sheet).attr('rightToLeft', '1');
                    }
                }
            ],
            "language": {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                "sInfoPostFix": "",
                "sSearch": "ابحث:",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            },
            "pageLength": 10,
            "ordering": true,
            "info": true,
            "lengthChange": true
        });
    });

    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    }
</script>
@endsection

@section('content')
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <span class="breadcrumb-item active">جهات المطالبة</span>
</div>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-building-user"></i> إدارة جهات المطالبة</h1>
    <button class="btn btn-primary" onclick="toggleModal('addEntityModal')" style="margin-top: 0;">
        <i class="fa-solid fa-plus"></i> إضافة جهة جديدة
    </button>
</div>

<div class="table-container">
    <table id="entitiesTable" class="table display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>النوع</th>
                <th style="text-align: center;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $entity)
            <tr>
                <td>{{ $entity->name }}</td>
                <td>{{ $entity->type }}</td>
                <td style="text-align: center;">
                    <a href="{{ route('entities.edit', $entity->id) }}" class="btn" style="font-size: 12px;"><i class="fa-solid fa-edit"></i> تعديل</a>
                    <form action="{{ route('entities.destroy', $entity->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="font-size: 12px; color: red;"><i class="fa-solid fa-trash"></i> حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="addEntityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div class="card" style="width: 100%; max-width: 850px; margin: 0; background: white; border-radius: 15px; overflow: hidden; display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">

        <!-- Illustration Section (Right Side) -->
        <div style="flex: 1; background: #ffffff; display: flex; align-items: center; justify-content: center; padding: 20px; text-align: center;">
            <div style="width: 100%; max-width: 350px;">
                <img src="{{ asset('images/org_illustration.png') }}" alt="Institution" style="width: 100%; height: auto; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="color: #0056b3; margin: 0; font-size: 18px;">تنظيم البيانات</h3>
                <p style="color: #777; font-size: 13px; line-height: 1.6; margin-top: 10px;">
                    إضافة الجهات يساعد في تنظيم المطالبات المالية بشكل دقيق وتتبع الموازنات الخاصة بكل مؤسسة.
                </p>
            </div>
        </div>

        <!-- Form Section (Left Side) -->
        <div style="flex: 1; padding: 40px; border-right: 1px solid #eee;">
            <div style="text-align: right; margin-bottom: 30px;">
                <h2 style="margin: 0; color: #333; font-size: 24px;">إضافة جهة جديدة</h2>
                <p style="color: #666; font-size: 14px; margin-top: 5px;">أدخل بيانات المؤسسة أو الجهة الرسمية</p>
            </div>

            <form action="{{ route('entities.store') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">اسم الجهة</label>
                    <input type="text" name="name" class="form-control" placeholder="مثلاً: وزارة الصحة" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">النوع</label>
                    <input type="text" name="type" class="form-control" placeholder="مثلاً: جهة حكومية" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 30px; border-radius: 8px; font-weight: 600;">
                        <i class="fa-solid fa-save"></i> حفظ الجهة
                    </button>
                    <button type="button" class="btn" style="background: #f4f4f4; color: #666; padding: 10px 25px; border-radius: 8px;" onclick="toggleModal('addEntityModal')">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>

        </p>
    </div>
</div>

</div>
</div>

@endsection