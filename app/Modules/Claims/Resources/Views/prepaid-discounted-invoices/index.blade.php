@extends('claims::layouts.app')

@section('title', 'الفواتير المخصمة')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        .invoice-modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease;
        }
        .invoice-modal-overlay.active {
            display: flex;
        }
        .invoice-modal {
            background: white;
            border-radius: 12px;
            width: 650px;
            max-width: 95vw;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
        }
        .invoice-modal-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 16px 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .invoice-modal-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }
        .invoice-modal-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .invoice-modal-close:hover {
            background: rgba(255,255,255,0.3);
        }
        .invoice-modal-body {
            padding: 20px;
        }
        .invoice-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .invoice-form-group {
            display: flex;
            flex-direction: column;
        }
        .invoice-form-group.full-width {
            grid-column: 1 / -1;
        }
        .invoice-form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .invoice-form-group input,
        .invoice-form-group textarea {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Cairo', sans-serif;
            transition: border-color 0.2s;
            background: #f8fafc;
        }
        .invoice-form-group input:focus,
        .invoice-form-group textarea:focus {
            border-color: #667eea;
            outline: none;
            background: white;
        }
        .invoice-form-group input:read-only {
            background: #f1f5f9;
            color: #64748b;
        }
        .invoice-modal-footer {
            padding: 14px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .invoice-modal-footer button {
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            border: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .invoice-modal-footer button:hover {
            opacity: 0.85;
        }
        .btn-invoice-save {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-invoice-cancel {
            background: #e2e8f0;
            color: #475569;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
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
                            <th>اجمالى المصروف (الكمية)</th>
                            <th>اجمالى المصروف (المبلغ)</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
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
                                <td style="font-weight: 600;">{{ $invoice->original_invoice_count }}</td>
                                <td style="font-weight: 600;">{{ number_format($invoice->original_amount, 2) }} ج.م</td>
                                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <button type="button" class="btn-view-invoice"
                                        data-id="{{ $invoice->id }}"
                                        data-claim_number="{{ $invoice->claim_number }}"
                                        data-original_invoice_count="{{ $invoice->original_invoice_count }}"
                                        data-discounted_invoice_count="{{ $invoice->discounted_invoice_count }}"
                                        data-original_amount="{{ $invoice->original_amount }}"
                                        data-discounted_amount="{{ $invoice->discounted_amount }}"
                                        data-deduction_amount="{{ $invoice->deduction_amount }}"
                                        data-taxes_amount="{{ $invoice->taxes_amount }}"
                                        data-unpaid_amount="{{ $invoice->unpaid_amount }}"
                                        data-notes="{{ $invoice->notes ?? '' }}"
                                        data-created_at="{{ $invoice->created_at->format('Y-m-d') }}"
                                        title="عرض"
                                        style="background:#3b82f6; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; font-size:12px; margin-left:4px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-edit-invoice"
                                        data-id="{{ $invoice->id }}"
                                        data-claim_number="{{ $invoice->claim_number }}"
                                        data-original_invoice_count="{{ $invoice->original_invoice_count }}"
                                        data-discounted_invoice_count="{{ $invoice->discounted_invoice_count }}"
                                        data-original_amount="{{ $invoice->original_amount }}"
                                        data-discounted_amount="{{ $invoice->discounted_amount }}"
                                        data-deduction_amount="{{ $invoice->deduction_amount }}"
                                        data-taxes_amount="{{ $invoice->taxes_amount }}"
                                        data-unpaid_amount="{{ $invoice->unpaid_amount }}"
                                        data-notes="{{ $invoice->notes ?? '' }}"
                                        title="تعديل"
                                        style="background:#f59e0b; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; font-size:12px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<!-- Invoice Modal -->
<div class="invoice-modal-overlay" id="invoiceModal">
    <div class="invoice-modal">
        <div class="invoice-modal-header">
            <h3 id="invoiceModalTitle">تفاصيل الفاتورة</h3>
            <button type="button" class="invoice-modal-close" id="invoiceModalClose">&times;</button>
        </div>
        <form id="invoiceEditForm" method="POST">
            @csrf
            @method('PUT')
            <div class="invoice-modal-body">
                <div class="invoice-form-grid">
                    <div class="invoice-form-group">
                        <label>#</label>
                        <input type="text" id="inv_id" readonly>
                    </div>
                    <div class="invoice-form-group">
                        <label>رقم المطالبة</label>
                        <input type="text" id="inv_claim_number" readonly>
                    </div>
                    <div class="invoice-form-group">
                        <label>عدد الفواتير الأصلي</label>
                        <input type="number" id="inv_original_invoice_count" name="original_invoice_count" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>عدد الفواتير المخصمة</label>
                        <input type="number" id="inv_discounted_invoice_count" name="discounted_invoice_count" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>المبلغ الأصلي</label>
                        <input type="number" step="0.01" id="inv_original_amount" name="original_amount" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>المبلغ بعد الخصم</label>
                        <input type="number" step="0.01" id="inv_discounted_amount" name="discounted_amount" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>خصم / اشعار دائن</label>
                        <input type="number" step="0.01" id="inv_deduction_amount" name="deduction_amount" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>ضرائب</label>
                        <input type="number" step="0.01" id="inv_taxes_amount" name="taxes_amount" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>المبلغ الغير مسدد</label>
                        <input type="number" step="0.01" id="inv_unpaid_amount" name="unpaid_amount" min="0">
                    </div>
                    <div class="invoice-form-group">
                        <label>تاريخ الإنشاء</label>
                        <input type="text" id="inv_created_at" readonly>
                    </div>
                    <div class="invoice-form-group full-width">
                        <label>ملاحظات</label>
                        <textarea id="inv_notes" name="notes" rows="2" style="resize:vertical;"></textarea>
                    </div>
                </div>
            </div>
            <div class="invoice-modal-footer" id="invoiceModalFooter">
                <button type="button" class="btn-invoice-cancel" id="invoiceModalCancel">إلغاء</button>
                <button type="submit" class="btn-invoice-save" id="invoiceSaveBtn">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <!-- JSZip for Excel export -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        function openInvoiceModal(mode) {
            var modal = document.getElementById('invoiceModal');
            var title = document.getElementById('invoiceModalTitle');
            var form = document.getElementById('invoiceEditForm');
            var saveBtn = document.getElementById('invoiceSaveBtn');
            var fields = form.querySelectorAll('input, textarea');

            if (mode === 'view') {
                title.textContent = 'عرض تفاصيل الفاتورة';
                saveBtn.style.display = 'none';
                fields.forEach(function(f) {
                    if (f.name) f.readOnly = true;
                });
            } else {
                title.textContent = 'تعديل الفاتورة';
                saveBtn.style.display = '';
                saveBtn.textContent = 'حفظ التغييرات';
                fields.forEach(function(f) {
                    if (f.name && f.name !== '') f.readOnly = false;
                });
                document.getElementById('inv_id').readOnly = true;
                document.getElementById('inv_claim_number').readOnly = true;
                document.getElementById('inv_created_at').readOnly = true;
            }
            modal.classList.add('active');
        }

        function closeInvoiceModal() {
            document.getElementById('invoiceModal').classList.remove('active');
        }

        $(document).ready(function () {
            // View button click
            $(document).on('click', '.btn-view-invoice', function() {
                var btn = $(this);
                $('#inv_id').val(btn.data('id'));
                $('#inv_claim_number').val(btn.data('claim_number'));
                $('#inv_original_invoice_count').val(btn.data('original_invoice_count'));
                $('#inv_discounted_invoice_count').val(btn.data('discounted_invoice_count'));
                $('#inv_original_amount').val(btn.data('original_amount'));
                $('#inv_discounted_amount').val(btn.data('discounted_amount'));
                $('#inv_deduction_amount').val(btn.data('deduction_amount'));
                $('#inv_taxes_amount').val(btn.data('taxes_amount'));
                $('#inv_unpaid_amount').val(btn.data('unpaid_amount'));
                $('#inv_notes').val(btn.data('notes'));
                $('#inv_created_at').val(btn.data('created_at'));
                openInvoiceModal('view');
            });

            // Edit button click
            $(document).on('click', '.btn-edit-invoice', function() {
                var btn = $(this);
                $('#inv_id').val(btn.data('id'));
                $('#inv_claim_number').val(btn.data('claim_number'));
                $('#inv_original_invoice_count').val(btn.data('original_invoice_count'));
                $('#inv_discounted_invoice_count').val(btn.data('discounted_invoice_count'));
                $('#inv_original_amount').val(btn.data('original_amount'));
                $('#inv_discounted_amount').val(btn.data('discounted_amount'));
                $('#inv_deduction_amount').val(btn.data('deduction_amount'));
                $('#inv_taxes_amount').val(btn.data('taxes_amount'));
                $('#inv_unpaid_amount').val(btn.data('unpaid_amount'));
                $('#inv_notes').val(btn.data('notes'));
                $('#inv_created_at').val(btn.data('created_at'));

                var actionUrl = '{{ route("prepaid-discounted-invoices.index") }}/' + btn.data('id');
                $('#invoiceEditForm').attr('action', actionUrl);
                openInvoiceModal('edit');
            });

            // Close modal
            $('#invoiceModalClose, #invoiceModalCancel').on('click', closeInvoiceModal);
            $(document).on('click', function(e) {
                if ($(e.target).hasClass('invoice-modal-overlay')) {
                    closeInvoiceModal();
                }
            });

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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        format: {
                            body: function (data, row, column, node) {
                                var text = data;
                                if (typeof text === 'string') {
                                    if (column === 4 || column === 5 || column === 6 || column === 7 || column === 8 || column === 10) {
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