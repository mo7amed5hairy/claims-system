@extends('claims::layouts.app')

@section('title', 'المطالبات')

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-color) !important;
        color: white !important;
        border: none !important;
        border-radius: 4px;
    }
</style>

<script>
    $(document).ready(function() {
        $('#claimsTable').DataTable({
            "language": {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                "sSearch": "ابحث:",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            },
            "pageLength": 10,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-file-invoice-dollar"></i> المطالبات</h1>
    <a href="{{ route('claims.create') }}" class="btn btn-primary" style="margin-top: 0;">
        <i class="fa-solid fa-plus"></i> إضافة مطالبة جديدة
    </a>
</div>

<div class="table-container">
    <table id="claimsTable" class="table" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>المستشفى / القسم</th>
                <th>الشهر</th>
                <th>الجهة</th>
                <th>قيمة المطالبة</th>
                <th>المبلغ بعد المراجعة</th>
                <th>الفرق</th>
                <th>المرفقات</th>
                <th style="text-align: center;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($claims as $claim)
            <tr>
                <td><span style="font-weight: 700; color: var(--primary-color);">#{{ $claim->id }}</span></td>
                <td>
                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $claim->hospital->name ?? '-' }}</div>
                    <div style="color: #64748b; font-size: 11px;"><i class="fa-solid fa-stethoscope"></i> {{ $claim->department->name ?? '-' }}</div>
                </td>
                <td>{{ $claim->month }}</td>
                <td><span style="font-weight: 500;">{{ $claim->entity->name ?? '-' }}</span></td>
                <td style="color: #0f172a; font-weight: 600;">{{ number_format($claim->claim_value, 2) }} ج.م</td>
                <td style="color: #10b981;">{{ $claim->reviewed_value ? number_format($claim->reviewed_value, 2) . ' ج.م' : '-' }}</td>
                <td class="{{ ($claim->difference < 0) ? 'text-danger' : 'text-success' }}" style="font-weight: 600;">
                    {{ $claim->difference ? number_format($claim->difference, 2) . ' ج.م' : '-' }}
                </td>
                <td>
                    <div class="attachment-badges">
                        @if($claim->attachments && is_array($claim->attachments))
                        @foreach($claim->attachments as $path)
                        @php
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $icon = 'fa-file';
                        $class = '';
                        if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) { $icon = 'fa-image'; $class = 'badge-image'; }
                        elseif($ext == 'pdf') { $icon = 'fa-file-pdf'; $class = 'badge-pdf'; }
                        elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'fa-file-excel'; $class = 'badge-excel'; }
                        @endphp
                        <a href="{{ asset('storage/' . $path) }}" target="_blank" class="attachment-badge {{ $class }}" title="عرض المرفق">
                            <i class="fa-solid {{ $icon }}"></i>
                        </a>
                        @endforeach
                        @else
                        <span style="color: #cbd5e1; font-size: 12px;">لا يوجد</span>
                        @endif
                    </div>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <a href="{{ route('claims.edit', $claim->id) }}" class="btn-action" style="background: #eff6ff; color: #3b82f6;" title="تعديل">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <form action="{{ route('claims.destroy', $claim->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المطالبة؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-remove" title="حذف">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection