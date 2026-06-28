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
                            style: 'background: #198754 !important; color: white; border: none; padding: 5px 15px; border-radius: 4px; font-family: Cairo; margin-bottom: 10px; cursor: pointer;'
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                            format: {
                                body: function(data, row, column, node) {
                                    var text = data;
                                    if (typeof text === 'string') {
                                        if (column === 6 || column === 7 || column === 8) {
                                            text = text.replace(' ج.م', '');
                                        }
                                        var temp = document.createElement('div');
                                        temp.innerHTML = text;
                                        text = temp.textContent || temp.innerText || '';
                                        return text.trim();
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('sheetViews sheetView', sheet).attr('rightToLeft', '1');

                            var total = 0;
                            $('row c[r^="G"]', sheet).each(function() {
                                var val = $(this).text();
                                if (val && !isNaN(parseFloat(val.replace(/,/g, '')))) {
                                    total += parseFloat(val.replace(/,/g, ''));
                                }
                            });

                            var rowCount = $('row', sheet).length;

                            var emptyRow = '<row r="' + (rowCount + 1) + '"></row>';
                            $('sheetData', sheet).append(emptyRow);

                            var totalRowNum = rowCount + 2;
                            var totalRow = '<row r="' + totalRowNum + '">';
                            totalRow += '<c r="F' + totalRowNum + '" t="inlineStr"><is><t>الإجمالي</t></is></c>';
                            totalRow += '<c r="G' + totalRowNum + '" t="n"><v>' + total.toFixed(2) + '</v></c>';
                            totalRow += '</row>';

                            $('sheetData', sheet).append(totalRow);
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
                "order": [[0, "desc"]],
                "columnDefs": [
                    {
                        "targets": 0,
                        "type": "num",
                        "render": function(data, type, row) {
                            if (type === 'sort' || type === 'type') {
                                return parseInt(data.replace('#', '')) || 0;
                            }
                            return data;
                        }
                    },
                ],
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
                    <th>خصم / اشعار دائن</th>
                    <th>ضرائب</th>
                    <th>المبلغ بعد الخصم</th>
                    <th>تاريخ الاستحقاق</th>
                    <th>الجهة</th>
                    <th>المرفقات</th>
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
                        <td>{{ $order->electronic_invoice_no ?? $order->claim_number ?? '-' }}</td>
                        <td>
                            @php
                                // Map waiting list hospital IDs to Arabic names
                                $hospitalNames = [
                                    'ain_shams' => 'مستشفى عين شمس',
                                    'children' => 'مستشفى الأطفال',
                                    'women' => 'مستشفى النساء',
                                    'other' => 'أخرى'
                                ];
                                
                                $hospitalName = $order->payeeHospital->name ?? null;
                                if (!$hospitalName && $order->payee_hospital_id) {
                                    $hospitalName = is_numeric($order->payee_hospital_id) 
                                        ? '-' 
                                        : ($hospitalNames[$order->payee_hospital_id] ?? $order->payee_hospital_id);
                                }
                                $deptName = $order->department->name ?? null;
                                if (!$deptName && $order->department_id) {
                                    $deptName = is_numeric($order->department_id) ? '-' : $order->department_id;
                                }
                            @endphp
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                {{ $hospitalName ?? '-' }}
                            </div>
                            <div style="color: #64748b; font-size: 11px;"><i class="fa-solid fa-stethoscope"></i>
                                {{ $deptName ?? '-' }}
                            </div>
                        </td>
                        <td><span class="badge"
                                style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-size: 12px;">{{ $order->account_type }}</span>
                        </td>
                        <td style="color: #10b981; font-weight: 600;">{{ number_format($order->amount, 2) }} ج.م</td>
                        <td style="color: #f59e0b; font-weight: 600;">{{ $order->deduction ? number_format($order->deduction, 2) . ' ج.م' : '-' }}</td>
                        <td style="color: #ef4444; font-weight: 600;">{{ $order->taxes ? number_format($order->taxes, 2) . ' ج.م' : '-' }}</td>
                        @php
                            $netAmt = max(0, $order->amount - ($order->amount * ($order->deduction ?? 0) / 100) - ($order->amount * ($order->taxes ?? 0) / 100));
                        @endphp
                        <td style="color: #6366f1; font-weight: 600;">{{ number_format($netAmt, 2) }} ج.م</td>
                        <td>{{ $order->due_date->format('Y-m-d') }}</td>
                        <td>{{ $order->payerEntity->name ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if(!empty($order->attachments) && is_array($order->attachments) && count($order->attachments) > 0)
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; justify-content: center;">
                                    @foreach($order->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="btn-action" style="background: #fef3c7; color: #d97706; padding: 3px 8px; font-size: 11px; text-decoration: none;" title="عرض الملف">
                                            <i class="fa-solid fa-file"></i> {{ $loop->iteration }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: #94a3b8; font-size: 11px;">-</span>
                            @endif
                        </td>
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