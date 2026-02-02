@extends('claims::layouts.app')

@section('title', 'الفواتير العائدة')

@section('scripts')
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            color: white !important;
            border: none !important;
            border-radius: 4px;
        }

        .badge-attachments {
            background: #f0fdf4;
            color: #16a34a;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #dcfce7;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }
    </style>

    <script>
        $(document).ready(function () {
            $('#returnsTable').DataTable({
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
                "info": true,
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endsection

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.operations') }}" class="breadcrumb-item">العمليات</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">الفواتير العائدة</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-undo"></i> الفواتير العائدة</h1>
        <a href="{{ route('returns.create') }}" class="btn btn-primary" style="margin-top: 0;">
            <i class="fa-solid fa-plus"></i> إضافة فاتورة عائدة
        </a>
    </div>

    <div class="table-container">
        <table id="returnsTable" class="table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المستشفى / القسم</th>
                    <th>الشهر</th>
                    <th>تاريخ الفاتورة</th>
                    <th>الجهة</th>
                    <th>عدد الفواتير</th>
                    <th>خصومات</th>
                    <th>ضرائب</th>
                    <th>المبلغ النهائي</th>
                    <th>المراجع</th>
                    <th>المرفقات</th>
                    <th style="text-align: center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr>
                        <td>
                            <span style="font-weight:700;color:var(--primary-color);">
                                #{{ $invoice->id }}
                            </span>
                        </td>

                        <td>
                            <div style="font-weight:600;font-size:13px;">
                                {{ $invoice->hospital->name ?? '-' }}
                            </div>
                            <div style="font-size:11px;color:#64748b;">
                                <i class="fa-solid fa-stethoscope"></i>
                                {{ $invoice->department->name ?? '-' }}
                            </div>
                        </td>

                        <td>{{ $invoice->month }}</td>

                        <td>{{ $invoice->return_date?->toDateString() }}</td>

                        <td>

                            <div style="font-weight:600;font-size:13px;">
                                {{ $invoice->entity->name ?? '-' }}
                            </div>

                            @if(!empty($invoice->branch) || !empty($invoice->location))
                                <div style="font-size:11px;color:#64748b;">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ trim(($invoice->branch ?? '') . ' - ' . ($invoice->location ?? ''), ' -') }}
                                </div>
                            @endif

                        </td>

                        <td>
                            <span class="badge">
                                {{ $invoice->returned_invoice_count }}
                            </span>
                        </td>

                        <td>{{ $invoice->discount_amount }}</td>

                        <td>{{ $invoice->tax_amount }}</td>

                        <td style="color:#ef4444;font-weight:600;">
                            {{ number_format($invoice->final_amount, 2) }} ج.م
                        </td>

                        <td>{{ $invoice->reviewer_name ?? '-' }}</td>

                        <td>
                            @if($invoice->attachments?->count())
                                <span class="badge-attachments">
                                    <i class="fa-solid fa-paperclip"></i>
                                    {{ $invoice->attachments->count() }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <td style="text-align:center;">
                            <div style="display:flex;gap:8px;justify-content:center;">
                                <a href="{{ route('returns.edit', $invoice->id) }}" class="btn-action" title="تعديل">
                                    <i class="fa-solid fa-edit"></i>
                                </a>

                                <form action="{{ route('returns.destroy', $invoice->id) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد؟');">
                                    @csrf @method('DELETE')
                                    <button class="btn-remove">
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