@extends('claims::layouts.app')

@section('title', 'الفواتير المخصمة')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
    <div class="page-header"
        style="padding: 10px 15px; margin-bottom: 6px; background: white; border-bottom: 1px solid #e2e8f0;">
        <h1 class="page-title" style="font-size: 18px; margin: 0;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary-color);"></i> الفواتير المخصمة
        </h1>
        <span style="font-size: 12px; color: #64748b;">
            <i class="fa-solid fa-info-circle"></i> يتم إنشاؤها تلقائياً عند إضافة أوامر الدفع المسبق
        </span>
    </div>

    <div class="content-wrapper" style="padding: 0 15px;">
        @if(session('success'))
            <div class="alert alert-success" style="margin: 10px 0;">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-card"
            style="padding: 15px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="table-container">
                <table id="discountedInvoicesTable" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم المطالبة</th>
                            <th>عدد الفواتير الأصلي</th>
                            <th>عدد الفواتير المخصمة</th>
                            <th>المبلغ الأصلي</th>
                            <th>المبلغ بعد الخصم</th>
                            <th>خصم / اشعار دائن</th>
                            <th>ضرائب</th>
                            <th>المبلغ الغير مسدد</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discountedInvoices as $invoice)
                            <tr>
                                <td><span style="font-weight: 700; color: var(--primary-color);">#{{ $invoice->id }}</span></td>
                                <td>{{ $invoice->claim_number }}</td>
                                <td>{{ $invoice->original_invoice_count }}</td>
                                <td style="color: #dc2626;">{{ $invoice->discounted_invoice_count }}</td>
                                <td>{{ number_format($invoice->original_amount, 2) }} ج.م</td>
                                <td>{{ number_format($invoice->discounted_amount, 2) }} ج.م</td>
                                <td style="color: #f59e0b; font-weight: 600;">
                                    {{ $invoice->deduction_amount > 0 ? number_format($invoice->deduction_amount, 2) . ' ج.م' : '-' }}
                                </td>
                                <td style="color: #ef4444; font-weight: 600;">
                                    {{ $invoice->taxes_amount > 0 ? number_format($invoice->taxes_amount, 2) . ' ج.م' : '-' }}
                                </td>
                                <td style="color: #dc2626; font-weight: bold;">{{ number_format($invoice->unpaid_amount, 2) }}
                                    ج.م</td>
                                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <!-- JSZip for Excel export -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#discountedInvoicesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i> تصدير إكسل',
                    className: 'btn-excel',
                    attr: {
                        style: 'background: #198754 !important; color: white; border: none; padding: 5px 15px; border-radius: 4px; font-family: Cairo; margin-bottom: 10px; cursor: pointer;'
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                        format: {
                            body: function (data, row, column, node) {
                                var text = data;
                                if (typeof text === 'string') {
                                    if (column === 4 || column === 5 || column === 6 || column === 7 || column === 8) {
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
                    }
                }],
                language: {
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
                pageLength: 10,
                ordering: true,
                info: true
            });
        });
    </script>
@endsection