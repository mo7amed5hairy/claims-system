@extends('claims::layouts.app')

@section('title', 'المطالبات')

@section('scripts')
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            color: white !important;
            border: none !important;
            border-radius: 4px;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }
    </style>

    <script>
        $(document).ready(function () {
            $('#claimsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i> تصدير إكسل',
                    className: 'btn-excel',
                    attr: {
                        style: 'background-color: #198754; color: white; border: none; padding: 5px 15px; border-radius: 4px; font-family: Cairo; margin-bottom: 10px; cursor: pointer;'
                    },
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('sheetViews sheetView', sheet).attr('rightToLeft', '1');
                    }
                }],
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
        @can('create', App\Modules\Claims\Models\Claim::class)
        <a href="{{ route('claims.create') }}" class="btn btn-primary" style="margin-top: 0;">
            <i class="fa-solid fa-plus"></i> إضافة مطالبة جديدة
        </a>
        @endcan
    </div>

    <div class="table-container">
        <table id="claimsTable" class="table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>أضيف بواسطة</th>
                    <th>المستشفى / القسم</th>
                    <th>الشهر</th>
                    <th>عدد الفواتير</th>
                    <th>تاريخ المطالبة</th>
                    <th>رقم الفاتورة الإلكترونية</th>
                    <th>تاريخ التسليم</th>
                    <th>الحالة</th>
                    <th>الجهة</th>
                    <th>قيمة المطالبة</th>
                    <th>المبلغ بعد المراجعة</th>
                    <th>المراجع</th>
                    <th>الفرق</th>
                    <th>المرفقات</th>
                    <th style="text-align: center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($claims as $claim)
                    @php
                        $isNew = $claim->created_at->gt(now()->subMinutes(5));
                    @endphp
                    <tr style="{{ $isNew ? 'background-color: #f0fdf4; border-right: 4px solid #22c55e;' : '' }}">
                        <td><span style="font-weight: 700; color: var(--primary-color);">#{{ $claim->id }}</span></td>
                        <td>
                            <div style="font-size: 12px; font-weight: 600; color: #475569;">
                                <i class="fa-solid fa-user-pen" style="font-size: 10px; color: #94a3b8;"></i> 
                                {{ $claim->user->name ?? 'النظام' }}
                            </div>
                            <div style="font-size: 10px; color: #94a3b8;">{{ $claim->created_at->format('Y-m-d H:i') }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $claim->hospital->name ?? '-' }}
                            </div>
                            <div style="color: #64748b; font-size: 11px;"><i class="fa-solid fa-stethoscope"></i>
                                {{ $claim->department->name ?? '-' }}</div>
                        </td>
                        <td>{{ $claim->month }}</td>
                        <td>{{ $claim->invoice_count }}</td>
                        <td>{{ $claim->claim_date->toDateString() }}</td>
                        <td style="font-weight: 500;">
                            {{ $claim->electronic_invoice_no ?? '-' }}
                        </td>
                        <td>
                            @if($claim->delivery_date)
                                <div style="font-size: 12px; font-weight: 600;">{{ $claim->delivery_date->toDateString() }}</div>
                                @if($claim->delivery_attachments && is_array($claim->delivery_attachments))
                                    <div style="display: flex; gap: 4px; margin-top: 4px;">
                                        @foreach($claim->delivery_attachments as $path)
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" style="color: #64748b; font-size: 10px;"><i class="fa-solid fa-paperclip"></i></a>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $status = $claim->payment_status;
                                $badgeColor = '#ef4444'; // Red for Unpaid
                                $badgeIcon = 'fa-times-circle';
                                $badgeText = 'غير مسددة';
                                
                                if($status == 'paid') {
                                    $badgeColor = '#10b981'; // Green
                                    $badgeIcon = 'fa-check-circle';
                                    $badgeText = 'مسددة';
                                } elseif($status == 'partial') {
                                    $badgeColor = '#f59e0b'; // Amber
                                    $badgeIcon = 'fa-hourglass-half'; 
                                    $badgeText = 'مسددة جزئياً';
                                }
                            @endphp
                            <span style="background-color: {{ $badgeColor }}20; color: {{ $badgeColor }}; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid {{ $badgeIcon }}"></i> {{ $badgeText }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:800;font-size:12px;">
                                {{ $claim->entity->name ?? '-' }}
                            </div>

                            @if(!empty($claim->branch) || !empty($claim->location))
                                <div style="font-size:11px;color:#64748b;">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ trim(($claim->branch ?? '') . ' - ' . ($claim->location ?? ''), ' -') }}
                                </div>
                            @endif
                        </td>
                        <td style="color: #0f172a; font-weight: 600;">{{ number_format($claim->claim_value, 2) }} ج.م</td>
                        <td style="color: #10b981;">
                            {{ $claim->reviewed_value ? number_format($claim->reviewed_value, 2) . ' ج.م' : '-' }}
                        </td>
                        <td>{{ $claim->reviewer_name ?? '-' }}</td>
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
                                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                $icon = 'fa-image';
                                                $class = 'badge-image';
                                            } elseif ($ext == 'pdf') {
                                                $icon = 'fa-file-pdf';
                                                $class = 'badge-pdf';
                                            } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                                $icon = 'fa-file-excel';
                                                $class = 'badge-excel';
                                            }
                                        @endphp
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank" class="attachment-badge {{ $class }}"
                                            title="عرض المرفق">
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
                                @can('update', $claim)
                                <a href="{{ route('claims.edit', $claim->id) }}" class="btn-action"
                                    style="background: #eff6ff; color: #3b82f6;" title="تعديل">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete', $claim)
                                <form action="{{ route('claims.destroy', $claim->id) }}" method="POST" style="display: inline;"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه المطالبة؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-remove" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection