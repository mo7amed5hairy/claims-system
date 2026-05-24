@extends('claims::layouts.app')

@section('title', 'تقارير المطالبات مسبقة الدفع')@section('content')<style>
        * {
            box-sizing: border-box;
        }

        .rp-page {
            padding: 20px;
            background: #f1f5f9;
            min-height: calc(100vh - 100px);
            direction: rtl;
            font-family: 'Cairo', sans-serif;
        }

        .page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .page-icon {
            background: #0891b2;
            /* Cyan/Teal for differentiation */
            color: white;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .page-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .toolbar {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-label {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .filters-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex: 1;
        }

        /* ── Dropdown ── */
        .rp-dd {
            position: relative;
        }

        .rp-dd-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            padding: 7px 11px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
            white-space: nowrap;
            user-select: none;
            font-family: 'Cairo', sans-serif;
            transition: border-color .15s, background .15s;
        }

        .rp-dd-btn:hover {
            border-color: #0891b2;
            background: #f8fafc;
            color: #1e293b;
        }

        .rp-dd-btn.open {
            border-color: #0891b2;
            background: #ecfeff;
            color: #0891b2;
        }

        .rp-dd-prefix {
            color: #94a3b8;
            font-size: 12px;
        }

        .rp-dd-val {
            font-weight: 700;
            color: #1e293b;
        }

        .rp-dd-btn.open .rp-dd-val {
            color: #0891b2;
        }

        .rp-chev {
            font-size: 10px !important;
            opacity: .5;
            transition: transform .2s;
            margin-right: 2px;
        }

        .rp-dd-btn.open .rp-chev {
            transform: rotate(180deg);
        }

        .rp-dd-panel {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            z-index: 9999;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            min-width: 230px;
            max-height: 300px;
            overflow: hidden;
            flex-direction: column;
        }

        .rp-dd-panel.open {
            display: flex;
        }

        .rp-dd-search {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            background: white;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .rp-dd-search input {
            width: 100%;
            padding: 5px 9px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Cairo', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
        }

        .rp-dd-search input:focus {
            border-color: #0891b2;
        }

        .rp-dd-list {
            overflow-y: auto;
            flex: 1;
            padding: 4px 0;
        }

        .rp-dd-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 14px;
            cursor: pointer;
            font-size: 13px;
            color: #334155;
        }

        .rp-dd-item:hover {
            background: #f1f5f9;
        }

        .rp-dd-item input[type=checkbox] {
            width: 15px;
            height: 15px;
            accent-color: #0891b2;
            cursor: pointer;
            flex-shrink: 0;
        }

        .rp-dd-item label {
            cursor: pointer;
            margin: 0;
        }

        .rp-dd-sep {
            height: 1px;
            background: #f1f5f9;
            margin: 3px 0;
        }

        /* ── Buttons ── */
        .btn-reset {
            background: #ef4444;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-reset:hover {
            background: #dc2626;
        }

        .btn-excel {
            background: #16a34a;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-right: auto;
        }

        .btn-excel:hover {
            background: #15803d;
        }

        /* ── Summary Grid ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            background: #0891b2;
            border: 2px solid #0891b2;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(8, 145, 178, .15);
        }

        .s-item {
            background: white;
            display: flex;
            flex-direction: column;
            min-width: 120px;
        }

        .s-item+.s-item {
            border-right: 1px solid #0891b2;
        }

        .s-lbl {
            background: #0891b2;
            color: white;
            padding: 8px 4px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .s-val {
            background: #fef9c3;
            color: #0f172a;
            padding: 10px 4px;
            font-size: 14px;
            font-weight: 900;
            text-align: center;
        }

        .s-val.is-neg {
            color: #dc2626;
        }

        .s-val.is-pos {
            color: #16a34a;
        }

        /* ── Table ── */
        .table-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        #reportsTable thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-align: center !important;
            padding: 10px 6px !important;
            white-space: nowrap;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        #reportsTable td {
            font-size: 12.5px;
            padding: 8px 6px !important;
            vertical-align: middle !important;
            text-align: center !important;
        }

        .badge-hosp {
            background: #ecfeff;
            color: #0891b2;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .td-money {
            text-align: left !important;
            font-variant-numeric: tabular-nums;
        }

        .money-green {
            color: #15803d;
            font-weight: 700;
        }

        .text-positive {
            color: #16a34a !important;
            font-weight: 700;
        }

        .text-negative {
            color: #dc2626 !important;
            font-weight: 700;
        }

        .dt-buttons {
            display: none !important;
        }


        /* Column filter */
        .th-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        .th-filter-icon {
            font-size: 11px;
            opacity: .4;
            cursor: pointer;
            transition: opacity .2s, color .2s;
            flex-shrink: 0;
        }

        .th-filter-icon:hover {
            opacity: 1;
            color: #0891b2;
        }

        .th-filter-icon.active {
            opacity: 1;
            color: #0891b2;
        }

        /* Column filter dropdown */
        .col-filter-panel {
            display: none;
            position: absolute;
            z-index: 99999;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
            min-width: 220px;
            max-height: 280px;
            overflow: hidden;
            flex-direction: column;
        }

        .col-filter-panel.open {
            display: flex;
        }

        .col-filter-panel .rp-dd-search {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .col-filter-panel .rp-dd-search input {
            width: 100%;
            padding: 5px 9px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Cairo', sans-serif;
            background: #f8fafc;
            outline: none;
        }

        .col-filter-panel .rp-dd-search input:focus {
            border-color: #0891b2;
        }

        .col-filter-panel .rp-dd-list {
            overflow-y: auto;
            flex: 1;
            padding: 4px 0;
        }
    </style>

    <div class="rp-page">

        {{-- Header --}}
        <div class="page-header">
            <div class="page-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div>
                <div class="page-title">تقارير المطالبات مسبقة الدفع</div>
                <div class="page-sub">تصفية وتحليل البيانات المالية للمطالبات المسبقة</div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="toolbar">
            <span class="toolbar-label"><i class="fa-solid fa-sliders"></i> تصفية:</span>
            <div class="filters-row">

                {{-- Year --}}
                <div class="rp-dd" id="dd-year">
                    <div class="rp-dd-btn" id="btn-year">
                        <i class="fa-solid fa-calendar"></i>
                        <span class="rp-dd-prefix">السنة</span>
                        <span class="rp-dd-val" id="year-val">الكل</span>
                        <i class="fa-solid fa-chevron-down rp-chev"></i>
                    </div>
                    <div class="rp-dd-panel" id="panel-year">
                        <div class="rp-dd-search"><input type="text" placeholder="بحث..." data-filter="year"></div>
                        <div class="rp-dd-list">
                            <div class="rp-dd-item">
                                <input type="checkbox" id="year-all" checked>
                                <label for="year-all">الكل</label>
                            </div>
                            <div class="rp-dd-sep"></div>
                            <div id="year-opts">
                                @php
                                    $years = collect($paymentOrders)
                                        ->map(fn($o) => $o->due_date?->year ?? $o->created_at?->year ?? date('Y'))
                                        ->filter()->unique()->sort();
                                @endphp
                                @foreach($years as $y)
                                    <div class="rp-dd-item">
                                        <input type="checkbox" class="cb-year" value="{{ $y }}" checked>
                                        <label>{{ $y }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Month --}}
                <div class="rp-dd" id="dd-month">
                    <div class="rp-dd-btn" id="btn-month">
                        <i class="fa-solid fa-clock"></i>
                        <span class="rp-dd-prefix">الشهر</span>
                        <span class="rp-dd-val" id="month-val">الكل</span>
                        <i class="fa-solid fa-chevron-down rp-chev"></i>
                    </div>
                    <div class="rp-dd-panel" id="panel-month">
                        <div class="rp-dd-search"><input type="text" placeholder="بحث..." data-filter="month"></div>
                        <div class="rp-dd-list">
                            <div class="rp-dd-item">
                                <input type="checkbox" id="month-all" checked>
                                <label for="month-all">الكل</label>
                            </div>
                            <div class="rp-dd-sep"></div>
                            <div id="month-opts"></div>
                        </div>
                    </div>
                </div>

                {{-- Hospital --}}
                <div class="rp-dd" id="dd-hosp">
                    <div class="rp-dd-btn" id="btn-hosp">
                        <i class="fa-solid fa-hospital"></i>
                        <span class="rp-dd-prefix">المستشفى</span>
                        <span class="rp-dd-val" id="hosp-val">الكل</span>
                        <i class="fa-solid fa-chevron-down rp-chev"></i>
                    </div>
                    <div class="rp-dd-panel" id="panel-hosp">
                        <div class="rp-dd-search"><input type="text" placeholder="بحث..." data-filter="hosp"></div>
                        <div class="rp-dd-list">
                            <div class="rp-dd-item">
                                <input type="checkbox" id="hosp-all" checked>
                                <label for="hosp-all">الكل</label>
                            </div>
                            <div class="rp-dd-sep"></div>
                            <div id="hosp-opts">
                                @php
                                    $hospitals = collect($paymentOrders)
                                        ->map(fn($o) => $o->payeeHospital->name ?? '-')
                                        ->unique()->sort();
                                @endphp
                                @foreach($hospitals as $h)
                                    <div class="rp-dd-item">
                                        <input type="checkbox" class="cb-hosp" value="{{ $h }}" checked>
                                        <label>{{ $h }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn-reset" id="btnReset">
                    <i class="fa-solid fa-sync"></i> إعادة تعيين
                </button>
                <button class="btn-excel" id="btnExcel">
                    <i class="fa-solid fa-file-excel"></i> تصدير إكسل
                </button>
            </div>
        </div>

        {{-- Summary Grid --}}
        <div class="summary-grid">
            <div class="s-item">
                <div class="s-lbl">عدد الحالات</div>
                <div class="s-val" id="tot_cases">0</div>
            </div>
            <div class="s-item">
                <div class="s-lbl">مبلغ العملية</div>
                <div class="s-val" id="tot_amt">0.00</div>
            </div>
            <div class="s-item">
                <div class="s-lbl">حالات بعد المراجعة</div>
                <div class="s-val" id="tot_rev_cases">0</div>
            </div>
            <div class="s-item">
                <div class="s-lbl">المبلغ بعد المراجعة</div>
                <div class="s-val" id="tot_rev_amt">0.00</div>
            </div>
            <div class="s-item">
                <div class="s-lbl">الحالات المحصلة</div>
                <div class="s-val" id="tot_coll_cases">0</div>
            </div>
            <div class="s-item">
                <div class="s-lbl">ما تم تحصيله</div>
                <div class="s-val" id="tot_pay_amt">0.00</div>
            </div>
            <div class="s-item">
                <div class="s-lbl">الفرق</div>
                <div class="s-val" id="tot_diff">0.00</div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-card" style="overflow-x: auto;">
            <table id="reportsTable" class="table table-bordered table-hover" style="width:100%; min-width: 1400px;">
                <thead>
                    <tr>
                        <th>
                            <div class="th-inner">اسم المستشفى <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="0"></i></div>
                        </th>
                        <th>
                            <div class="th-inner">بيان <i class="fa-solid fa-filter th-filter-icon" data-col="1"></i></div>
                        </th>
                        <th>
                            <div class="th-inner">رقم المطالبة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="2"></i></div>
                        </th>
                        <th>
                            <div class="th-inner">الشهر <i class="fa-solid fa-filter th-filter-icon" data-col="3"></i></div>
                        </th>
                        <th style="background:#dbeafe;">
                            <div class="th-inner">عدد الحالات <i class="fa-solid fa-filter th-filter-icon" data-col="4"></i>
                            </div>
                        </th>
                        <th style="background:#dbeafe;">
                            <div class="th-inner">مبلغ العملية <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="5"></i></div>
                        </th>
                        <th style="background:#fef08a;color:#713f12;">
                            <div class="th-inner">عدد الحالات بعد المراجعة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="6"></i></div>
                        </th>
                        <th style="background:#fef08a;color:#713f12;">
                            <div class="th-inner">المبلغ بعد المراجعة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="7"></i></div>
                        </th>
                        <th style="background:#bfdbfe;">
                            <div class="th-inner">عدد الحالات المحصلة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="8"></i>
                            </div>
                        </th>
                        <th style="background:#bfdbfe;">
                            <div class="th-inner">ماتم تحصيله فى امر الدفع <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="9"></i></div>
                        </th>
                        <th style="background:#fed7aa;color:#7c2d12;">
                            <div class="th-inner">الفرق <i class="fa-solid fa-filter th-filter-icon" data-col="10"></i>
                            </div>
                        </th>
                        <th style="background:#f1f5f9;">
                            <div class="th-inner">رقم امر الدفع <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="11"></i></div>
                        </th>
                        <th style="background:#f1f5f9;">
                            <div class="th-inner">تاريخ الاستحقاق <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="12"></i></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentOrders as $order)
                        @php
                            $rawAmt = $order->claim->claim_value ?? 0;
                            $rawRevAmt = $order->amount_after_review ?? 0;
                            $rawPayAmt = $order->amount ?? 0;
                            $rawDiff = $rawRevAmt - $rawPayAmt;

                            $year = $order->due_date?->year ?? $order->created_at?->year ?? date('Y');
                            $monthNum = $order->due_date?->month ?? $order->created_at?->month ?? 0;

                            $monthsAr = [
                                '',
                                'يناير',
                                'فبراير',
                                'مارس',
                                'أبريل',
                                'مايو',
                                'يونيو',
                                'يوليو',
                                'أغسطس',
                                'سبتمبر',
                                'أكتوبر',
                                'نوفمبر',
                                'ديسمبر'
                            ];
                            $mText = $monthsAr[$monthNum] ?? '-';
                            $fullMonth = $mText . ' ' . $year;
                        @endphp
                        <tr data-year="{{ $year }}" data-month="{{ $fullMonth }}"
                            data-hosp="{{ $order->payeeHospital->name ?? '-' }}">
                            <td><span class="badge-hosp">{{ $order->payeeHospital->name ?? '-' }}</span></td>
                            <td>{{ $order->department->name ?? '-' }}</td>
                            <td style="font-family:monospace">{{ $order->claim_number ?? '-' }}</td>
                            <td>{{ $fullMonth }}</td>
                            <td class="val-cases">{{ $order->claim->invoice_count ?? 0 }}</td>
                            <td class="td-money val-amt" data-val="{{ $rawAmt }}">{{ number_format($rawAmt, 2) }}</td>
                            <td class="val-rev-cases">{{ $order->invoice_count_after_review ?? 0 }}</td>
                            <td class="td-money val-rev-amt" data-val="{{ $rawRevAmt }}">{{ number_format($rawRevAmt, 2) }}</td>
                            <td class="val-coll-cases">{{ $order->invoice_count_after_review ?? 0 }}</td>
                            <td class="td-money val-pay-amt money-green" data-val="{{ $rawPayAmt }}">
                                {{ number_format($rawPayAmt, 2) }}
                            </td>
                            <td class="td-money val-diff {{ $rawDiff > 0 ? 'text-negative' : ($rawDiff < 0 ? 'text-positive' : '') }}"
                                data-val="{{ $rawDiff }}">{{ number_format($rawDiff, 2) }}</td>
                            <td style="font-family:monospace">
                                {{ $order->electronic_invoice_no ?? $order->gp_number ?? $order->invoice_no ?? '-' }}
                            </td>
                            <td>{{ $order->due_date?->format('Y-m-d') ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(function () {

            /* ══ Raw data من PHP ══ */
            var allRows = [
                @foreach($paymentOrders as $order)
                    @php
                        $y = $order->due_date?->year ?? $order->created_at?->year ?? date('Y');
                        $mn = $order->due_date?->month ?? $order->created_at?->month ?? 0;
                        $ar = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                        $fm = ($ar[$mn] ?? '-') . ' ' . $y;
                    @endphp
                    { year: "{{ $y }}", month: "{{ $fm }}", hosp: "{{ addslashes($order->payeeHospital->name ?? '-') }}" },
                @endforeach
                                        ];

            var allHospitals = @json($allHospitals);
            var monthNames = @json($allMonths);

            var allYears = [];
            $.each(allRows, function (_, r) { if (allYears.indexOf(r.year) === -1) allYears.push(r.year); });
            allYears.sort();

            /* ══ DataTable ══ */
            var dt = $('#reportsTable').DataTable({
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'تقرير المطالبات مسبقة الدفع - ' + new Date().toLocaleDateString('ar-EG'),
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $(sheet).find('sheetViews sheetView').attr('rightToLeft', '1');
                    }
                }],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' },
                pageLength: 200,
                scrollX: false,
                drawCallback: updateSummary
            });
            $('.dt-buttons').hide();

            /* ══════════════════════════════════════════
               فلاتر رؤوس الجدول (Column Filters)
            ══════════════════════════════════════════ */
            var colFilterState = {};
            var activeColIdx = null;

            // panel واحد مشترك لكل الـ columns
            var $colPanel = $(
                '<div class="col-filter-panel" id="colFilterPanel">' +
                '<div class="rp-dd-search">' +
                '<input type="text" id="colFilterSearch" placeholder="بحث...">' +
                '</div>' +
                '<div class="rp-dd-list">' +
                '<div class="rp-dd-item">' +
                '<input type="checkbox" id="col-all" checked>' +
                '<label for="col-all">الكل</label>' +
                '</div>' +
                '<div class="rp-dd-sep"></div>' +
                '<div id="colFilterOpts"></div>' +
                '</div>' +
                '</div>'
            ).appendTo('body');

            // فتح الـ panel عند الضغط على أيقونة الفلتر
            $(document).on('click', '.th-filter-icon', function (e) {
                e.stopPropagation();
                var colIdx = parseInt($(this).data('col'));

                // نفس الـ column مرة تانية = إغلاق
                if (activeColIdx === colIdx && $colPanel.hasClass('open')) {
                    closeColPanel(); return;
                }

                activeColIdx = colIdx;

                // جمع كل القيم الفريدة من الـ column من كل الـ rows
                var vals = [];
                dt.column(colIdx).data().each(function (d) {
                    var clean = $('<div>').html(d).text().trim();
                    if (clean && vals.indexOf(clean) === -1) vals.push(clean);
                });
                vals.sort();

                // بناء الـ checkboxes
                var currentFilter = colFilterState[colIdx] || null;
                var $opts = $('#colFilterOpts').empty();
                $.each(vals, function (_, v) {
                    var chk = !currentFilter || currentFilter.indexOf(v) > -1 ? 'checked' : '';
                    $opts.append(
                        '<div class="rp-dd-item">' +
                        '<input type="checkbox" class="col-cb" value="' + v + '" ' + chk + '>' +
                        '<label>' + v + '</label>' +
                        '</div>'
                    );
                });

                syncColAll();

                // تلوين الأيقونة النشطة
                $('.th-filter-icon').removeClass('active');
                $(this).addClass('active');

                // تحديد موضع الـ panel
                var rect = this.getBoundingClientRect();
                var panelWidth = 230;
                var rightSpace = window.innerWidth - rect.right;

                // لو المسافة ناحية الشمال مش كافية (في الـ RTL الصفحة بتبدأ من اليمين)
                // rect.right هي المسافة من يسار الشاشة لحد يمين العنصر
                // لو rect.right أقل من عرض الـ panel يبقى هيخبط في طرف الشاشة الشمال
                if (rect.right < panelWidth) {
                    $colPanel.css({
                        top: rect.bottom + window.scrollY + 4,
                        left: 10,
                        right: 'auto'
                    });
                } else {
                    $colPanel.css({
                        top: rect.bottom + window.scrollY + 4,
                        right: Math.max(4, rightSpace),
                        left: 'auto'
                    });
                }
                $colPanel.addClass('open');

                $('#colFilterSearch').val('');
                $('#colFilterOpts .rp-dd-item').show();
            });

            // بحث داخل الـ panel
            $('#colFilterSearch').on('keyup', function () {
                var v = $(this).val().toLowerCase();
                $('#colFilterOpts .rp-dd-item').each(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
                });
            });

            // checkbox "الكل" للـ column
            $('#col-all').on('change', function () {
                $('.col-cb').prop('checked', this.checked);
                applyColFilter();
            });

            // أي checkbox في الـ panel
            $(document).on('change', '.col-cb', function () {
                syncColAll();
                applyColFilter();
            });

            function syncColAll() {
                var t = $('.col-cb').length, c = $('.col-cb:checked').length;
                $('#col-all').prop('checked', t === c);
            }

            function applyColFilter() {
                if (activeColIdx === null) return;
                var checked = $('.col-cb:checked').map(function () { return $(this).val(); }).get();
                var total = $('.col-cb').length;
                if (checked.length === total) {
                    colFilterState[activeColIdx] = null;
                    $('.th-filter-icon[data-col="' + activeColIdx + '"]').removeClass('active');
                } else {
                    colFilterState[activeColIdx] = checked;
                    $('.th-filter-icon[data-col="' + activeColIdx + '"]').addClass('active');
                }
                dt.draw();
            }

            function closeColPanel() {
                $colPanel.removeClass('open');
                activeColIdx = null;
            }

            // إغلاق لما تضغط برا الـ panel
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#colFilterPanel').length &&
                    !$(e.target).hasClass('th-filter-icon')) {
                    closeColPanel();
                }
            });

            /* ══════════════════════════════════════════
               فلاتر الـ Toolbar (Year / Month / Hosp)
            ══════════════════════════════════════════ */
            var openDD = null;
            function openPanel(n) { if (openDD && openDD !== n) closePanel(openDD); $('#btn-' + n).addClass('open'); $('#panel-' + n).addClass('open'); openDD = n; }
            function closePanel(n) { $('#btn-' + n).removeClass('open'); $('#panel-' + n).removeClass('open'); if (openDD === n) openDD = null; }
            function togglePanel(n) { $('#panel-' + n).hasClass('open') ? closePanel(n) : openPanel(n); }

            $('#btn-year').on('click', function (e) { e.stopPropagation(); togglePanel('year'); });
            $('#btn-month').on('click', function (e) { e.stopPropagation(); togglePanel('month'); });
            $('#btn-hosp').on('click', function (e) { e.stopPropagation(); togglePanel('hosp'); });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.rp-dd').length) ['year', 'month', 'hosp'].forEach(closePanel);
            });

            $(document).on('keyup', '[data-filter]', function () {
                var n = $(this).data('filter'), v = $(this).val().toLowerCase();
                $('#' + n + '-opts .rp-dd-item').each(function () { $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1); });
            });

            function syncAll(n) { var t = $('.cb-' + n).length, c = $('.cb-' + n + ':checked').length; $('#' + n + '-all').prop('checked', t === c); updateLabel(n); }
            function updateLabel(n) { var t = $('.cb-' + n).length, c = $('.cb-' + n + ':checked').length; $('#' + n + '-val').text(c === t ? 'الكل' : c + ' مختار'); }

            $('#year-all').on('change', function () { $('.cb-year').prop('checked', this.checked); rebuildMonths(); applyFilters(); });
            $('#month-all').on('change', function () { $('.cb-month').prop('checked', this.checked); applyFilters(); });
            $('#hosp-all').on('change', function () { $('.cb-hosp').prop('checked', this.checked); applyFilters(); });

            $(document).on('change', '.cb-year', function () { syncAll('year'); rebuildMonths(); applyFilters(); });
            $(document).on('change', '.cb-month', function () { syncAll('month'); applyFilters(); });
            $(document).on('change', '.cb-hosp', function () { syncAll('hosp'); applyFilters(); });

            function buildYears() {
                var $c = $('#year-opts').empty();
                $.each(allYears, function (_, y) {
                    $c.append('<div class="rp-dd-item"><input type="checkbox" class="cb-year" value="' + y + '" checked><label>' + y + '</label></div>');
                });
                syncAll('year');
            }

            function buildHospitals() {
                var $c = $('#hosp-opts').empty();
                $.each(allHospitals, function (_, h) {
                    $c.append('<div class="rp-dd-item"><input type="checkbox" class="cb-hosp" value="' + h + '" checked><label>' + h + '</label></div>');
                });
                syncAll('hosp');
            }

            function rebuildMonths() {
                var selY = $('.cb-year:checked').map(function () { return $(this).val(); }).get();
                var prevSel = $('.cb-month:checked').map(function () { return $(this).val(); }).get();
                var months = [];
                $.each(selY, function (_, y) { $.each(monthNames, function (_, m) { var f = m + ' ' + y; if (months.indexOf(f) === -1) months.push(f); }); });
                var $c = $('#month-opts').empty();
                $.each(months, function (_, m) {
                    var chk = prevSel.length === 0 || prevSel.indexOf(m) > -1 ? 'checked' : '';
                    $c.append('<div class="rp-dd-item"><input type="checkbox" class="cb-month" value="' + m + '" ' + chk + '><label>' + m + '</label></div>');
                });
                syncAll('month');
            }

            /* ══════════════════════════════════════════
               DataTables Filter — الكل في function واحدة
            ══════════════════════════════════════════ */
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var row = $(dt.row(dataIndex).node());

                // فلتر السنة
                var selY = $('.cb-year:checked').map(function () { return $(this).val(); }).get();
                if (selY.length && selY.indexOf(String(row.data('year'))) === -1) return false;

                // فلتر الشهر
                var selM = $('.cb-month:checked').map(function () { return $(this).val(); }).get();
                if (selM.length && selM.indexOf(row.data('month')) === -1) return false;

                // فلتر المستشفى
                var selH = $('.cb-hosp:checked').map(function () { return $(this).val(); }).get();
                if (selH.length && selH.indexOf(row.data('hosp')) === -1) return false;

                // فلاتر رؤوس الجدول
                for (var ci in colFilterState) {
                    if (colFilterState[ci]) {
                        var cellText = $('<div>').html(data[parseInt(ci)]).text().trim();
                        if (colFilterState[ci].indexOf(cellText) === -1) return false;
                    }
                }

                return true;
            });

            function applyFilters() { updateLabel('year'); updateLabel('month'); updateLabel('hosp'); dt.draw(); }

            /* ══ Summary ══ */
            function updateSummary() {
                var c = 0, a = 0, rc = 0, ra = 0, cc = 0, pa = 0, d = 0;
                dt.rows({ search: 'applied' }).every(function () {
                    var $n = $(this.node());
                    c += parseInt($n.find('.val-cases').text()) || 0;
                    a += parseFloat($n.find('.val-amt').data('val')) || 0;
                    rc += parseInt($n.find('.val-rev-cases').text()) || 0;
                    ra += parseFloat($n.find('.val-rev-amt').data('val')) || 0;
                    cc += parseInt($n.find('.val-coll-cases').text()) || 0;
                    pa += parseFloat($n.find('.val-pay-amt').data('val')) || 0;
                    d += parseFloat($n.find('.val-diff').data('val')) || 0;
                });
                var fmt = function (v) { return v.toLocaleString('en-US', { minimumFractionDigits: 2 }); };
                $('#tot_cases').text(c); $('#tot_amt').text(fmt(a));
                $('#tot_rev_cases').text(rc); $('#tot_rev_amt').text(fmt(ra));
                $('#tot_coll_cases').text(cc); $('#tot_pay_amt').text(fmt(pa));
                $('#tot_diff').text(fmt(d)).removeClass('is-neg is-pos').addClass(d > 0 ? 'is-neg' : (d < 0 ? 'is-pos' : ''));
            }

            /* ══ Buttons ══ */
            $('#btnReset').on('click', function () {
                colFilterState = {};
                $('.th-filter-icon').removeClass('active');
                buildYears(); buildHospitals(); rebuildMonths(); applyFilters();
            });
            $('#btnExcel').on('click', function () { dt.button(0).trigger(); });

            /* ══ Init ══ */
            buildYears();
            buildHospitals();
            rebuildMonths();
            updateSummary();
        });
    </script>
@endsection