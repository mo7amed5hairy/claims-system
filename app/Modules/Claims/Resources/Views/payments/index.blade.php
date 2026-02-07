@extends('claims::layouts.app')

@section('title', 'أوامر الدفع')

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
            $('#paymentsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
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
                    }
                ],
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
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.operations') }}" class="breadcrumb-item">العمليات</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">أوامر الدفع</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-credit-card"></i> أوامر الدفع</h1>
        @can('create', App\Modules\Claims\Models\PaymentOrder::class)
            <a href="{{ route('payments.create') }}" class="btn btn-primary" style="margin-top: 0;">
                <i class="fa-solid fa-plus"></i> إضافة أمر دفع
            </a>
        @endcan
    </div>

    <div class="table-container">
        <table id="paymentsTable" class="table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>أضيف بواسطة</th>
                    <th>رقم الـ GP / الشيك</th>
                    <th>رقم الفاتورة الإلكترونية</th>
                    <th>المستشفى / القسم</th>
                    <th>نوع الحساب</th>
                    <th>المبلغ</th>
                    <th>تاريخ الاستحقاق</th>
                    <th>الجهة</th>
                    <th style="text-align: center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $isNew = $order->created_at && $order->created_at->gt(now()->subMinutes(5));
                    @endphp
                    <tr style="{{ $isNew ? 'background-color: #f0fdf4; border-right: 4px solid #22c55e;' : '' }}">
                        <td><span style="font-weight: 700; color: var(--primary-color);">#{{ $order->id }}</span></td>
                        <td>
                            <div style="font-size: 11px; font-weight: 600; color: #475569;">
                                <i class="fa-solid fa-user-pen" style="font-size: 10px; color: #94a3b8;"></i>
                                {{ $order->user->name ?? 'النظام' }}
                            </div>
                            <div style="font-size: 9px; color: #94a3b8;">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
                        </td>
                        <td style="font-weight: 600;">{{ $order->gp_number }}</td>
                        <td>{{ $order->electronic_invoice_no ?? '-' }}</td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                {{ $order->payeeHospital->name ?? '-' }}
                            </div>
                            <div style="color: #64748b; font-size: 11px;"><i class="fa-solid fa-stethoscope"></i>
                                {{ $order->department->name ?? '-' }}
                            </div>
                        </td>
                        <td><span class="badge"
                                style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-size: 12px;">{{ $order->account_type }}</span>
                        </td>
                        <td style="color: #10b981; font-weight: 600;">{{ number_format($order->amount, 2) }} ج.م</td>
                        <td>{{ $order->due_date->format('Y-m-d') }}</td>
                        <td>{{ $order->payerEntity->name ?? '-' }}</td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                @can('update', $order)
                                    <a href="{{ route('payments.edit', $order->id) }}" class="btn-action"
                                        style="background: #eff6ff; color: #3b82f6;" title="تعديل">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan

                                @can('delete', $order)
                                    <form action="{{ route('payments.destroy', $order->id) }}" method="POST"
                                        style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف أمر الدفع هذا؟');">
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