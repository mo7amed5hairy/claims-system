@extends('claims::layouts.app')

@section('title', 'تقارير المطالبات مسبقة الدفع')

@section('content')
    <style>
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
                            <div class="th-inner">جهة التعاقد <i class="fa-solid fa-filter th-filter-icon" data-col="1"></i>
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">بيان <i class="fa-solid fa-filter th-filter-icon" data-col="2"></i></div>
                        </th>
                        <th>
                            <div class="th-inner">رقم المطالبة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="3"></i></div>
                        </th>
                        <th>
                            <div class="th-inner">الشهر <i class="fa-solid fa-filter th-filter-icon" data-col="4"></i></div>
                        </th>
                        <th style="background:#dbeafe;">
                            <div class="th-inner">عدد الحالات <i class="fa-solid fa-filter th-filter-icon" data-col="5"></i>
                            </div>
                        </th>
                        <th style="background:#dbeafe;">
                            <div class="th-inner">مبلغ العملية <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="6"></i></div>
                        </th>
                        <th style="background:#fef08a;color:#713f12;">
                            <div class="th-inner">عدد الحالات بعد المراجعة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="7"></i></div>
                        </th>
                        <th style="background:#fef08a;color:#713f12;">
                            <div class="th-inner">المبلغ بعد المراجعة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="8"></i></div>
                        </th>
                        <th style="background:#bfdbfe;">
                            <div class="th-inner">عدد الحالات المحصلة <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="9"></i></div>
                        </th>
                        <th style="background:#bfdbfe;">
                            <div class="th-inner">ماتم تحصيله فى امر الدفع <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="10"></i></div>
                        </th>
                        <th style="background:#fed7aa;color:#7c2d12;">
                            <div class="th-inner">الفرق <i class="fa-solid fa-filter th-filter-icon" data-col="11"></i>
                            </div>
                        </th>
                        <th style="background:#f1f5f9;">
                            <div class="th-inner">رقم امر الدفع <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="12"></i></div>
                        </th>
                        <th style="background:#f1f5f9;">
                            <div class="th-inner">تاريخ الاستحقاق <i class="fa-solid fa-filter th-filter-icon"
                                    data-col="13"></i></div>
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
                            $year = $order->rendered_year;
                            $fullMonth = $order->rendered_month;
                            $hospName = $order->payeeHospital->name ?? '-';
                            $deptName = $order->department->name ?? '-';
                        @endphp
                        <tr data-year="{{ $year }}" data-month="{{ $fullMonth }}" data-hosp="{{ $hospName }}"
                            data-entity="{{ $order->entity->name ?? '-' }}" data-dept="{{ $deptName }}"
                            data-location="{{ $order->claim->location ?? ($order->location ?? '') }}">
                            <td><span class="badge-hosp">{{ $hospName }}</span></td>
                            <td><span class="badge-entity"
                                    style="background:#ecfeff; color:#0891b2; padding:2px 6px; border-radius:4px; font-size:11px; font-weight:700;">{{ $order->entity->name ?? '-' }}</span>
                            </td>
                            <td>{{ $deptName }}</td>
                            <td style="font-family:monospace">{{ $order->claim_number ?? '-' }}</td>
                            <td>{{ $fullMonth }}</td>
                            <td class="val-cases">{{ $order->claim->invoice_count ?? 0 }}</td>
                            <td class="td-money val-amt" data-val="{{ $rawAmt }}"
                                data-search="{{ (int) $rawAmt }} {{ number_format($rawAmt, 2) }}">
                                {{ number_format($rawAmt, 2) }}
                            </td>
                            <td class="val-rev-cases">{{ $order->invoice_count_after_review }}</td>
                            <td class="td-money val-rev-amt" data-val="{{ $rawRevAmt }}"
                                data-search="{{ (int) $rawRevAmt }} {{ number_format($rawRevAmt, 2) }}">
                                {{ number_format($rawRevAmt, 2) }}
                            </td>
                            <td class="val-coll-cases">{{ $order->is_payment ? $order->invoice_count_after_review : 0 }}</td>
                            <td class="td-money val-pay-amt money-green" data-val="{{ $rawPayAmt }}"
                                data-search="{{ (int) $rawPayAmt }} {{ number_format($rawPayAmt, 2) }}">
                                {{ number_format($rawPayAmt, 2) }}
                            </td>
                            <td class="td-money val-diff {{ $rawDiff > 0 ? 'text-negative' : ($rawDiff < 0 ? 'text-positive' : '') }}"
                                data-val="{{ $rawDiff }}" data-search="{{ (int) $rawDiff }} {{ number_format($rawDiff, 2) }}">
                                {{ number_format($rawDiff, 2) }}
                            </td>
                            <td style="font-family:monospace">{{ $order->electronic_invoice_no ?? '-' }}</td>
                            <td>{{ $order->due_date ? (\Carbon\Carbon::parse($order->due_date)->format('Y-m-d')) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc; font-weight:bold; border-top:2px solid #cbd5e1;">
                        <th colspan="5" style="text-align: right; font-weight: 800; font-size: 13px; color: #1e293b;">
                            الإجمالي</th>
                        <th id="foot_cases" style="font-weight: 900; font-size: 13px; text-align: center; color: #0f172a;">0
                        </th>
                        <th id="foot_amt" style="font-weight: 900; font-size: 13px; text-align: left; color: #0f172a;">0.00
                        </th>
                        <th id="foot_rev_cases"
                            style="font-weight: 900; font-size: 13px; text-align: center; color: #713f12;">0</th>
                        <th id="foot_rev_amt" style="font-weight: 900; font-size: 13px; text-align: left; color: #713f12;">
                            0.00</th>
                        <th id="foot_coll_cases"
                            style="font-weight: 900; font-size: 13px; text-align: center; color: #1e40af;">0</th>
                        <th id="foot_pay_amt" style="font-weight: 900; font-size: 13px; text-align: left; color: #166534;">
                            0.00</th>
                        <th id="foot_diff" style="font-weight: 900; font-size: 13px; text-align: left; color: #9a3412;">0.00
                        </th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        function normalizeArabic(text) {
            if (typeof text !== 'string') return text;
            return text
                .replace(/[أإآ]/g, 'ا')
                .replace(/ى/g, 'ي')
                .replace(/ة/g, 'ه');
        }

        $(function () {

            var allRows = [
                @foreach($paymentOrders as $order)
                    { year: "{{ $order->rendered_year }}", month: "{{ $order->rendered_month }}", hosp: "{{ addslashes($order->payeeHospital->name ?? '-') }}", entity: "{{ addslashes($order->entity->name ?? '-') }}" },
                @endforeach
                        ];

            var allHospitals = @json($allHospitals);
            var monthNames = @json($allMonths);
            var othersDepartments = @json($othersDepartments);
            var dbEntities = @json($allEntities);
            var entitySubFilters = @json($entitySubFilters);

            var allYears = [];
            $.each(allRows, function (_, r) { if (allYears.indexOf(r.year) === -1) allYears.push(r.year); });
            allYears.sort();

            var dt = $('#reportsTable').DataTable({
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'تقرير المطالبات مسبقة الدفع - ' + new Date().toLocaleDateString('ar-EG'),
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data) { if (typeof data === 'string') return data.replace(/<[^>]*>/g, '').replace(/\s*ج\.م\s*/g, '').trim(); return data; }
                        }
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $(sheet).find('sheetViews sheetView').attr('rightToLeft', '1');

                        var sheetData = sheet.getElementsByTagName('sheetData')[0];
                        if (!sheetData) return;
                        var rows = sheet.getElementsByTagName('row');
                        var rNum = rows.length + 1;

                        function addCell(row, ref, val) {
                            var cell = sheet.createElement('c');
                            cell.setAttribute('r', ref);
                            if (val !== undefined) {
                                cell.setAttribute('t', 'inlineStr');
                                var is = sheet.createElement('is');
                                var t = sheet.createElement('t');
                                t.appendChild(sheet.createTextNode(String(val)));
                                is.appendChild(t);
                                cell.appendChild(is);
                            }
                            row.appendChild(cell);
                        }

                        var newRow = sheet.createElement('row');
                        newRow.setAttribute('r', rNum);
                        addCell(newRow, 'A' + rNum);
                        addCell(newRow, 'B' + rNum);
                        addCell(newRow, 'C' + rNum);
                        addCell(newRow, 'D' + rNum);
                        addCell(newRow, 'E' + rNum, 'الإجمالي');

                        var footIds = ['foot_cases','foot_amt','foot_rev_cases','foot_rev_amt','foot_coll_cases','foot_pay_amt','foot_diff'];
                        var colRefs = ['F','G','H','I','J','K','L'];
                        for (var i = 0; i < footIds.length; i++) {
                            var el = document.getElementById(footIds[i]);
                            addCell(newRow, colRefs[i] + rNum, el ? el.textContent : '0');
                        }
                        addCell(newRow, 'M' + rNum);
                        addCell(newRow, 'N' + rNum);
                        sheetData.appendChild(newRow);
                    }
                }],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' },
                pageLength: 200,
                scrollX: false,
                drawCallback: updateSummary
            });
            $('.dt-buttons').hide();

            /* Column Filters */
            var colFilterState = {};
            var activeColIdx = null;

            var $colPanel = $(
                '<div class="col-filter-panel" id="colFilterPanel">' +
                '<div class="rp-dd-search"><input type="text" id="colFilterSearch" placeholder="بحث..."></div>' +
                '<div class="rp-dd-list">' +
                '<div class="rp-dd-item"><input type="checkbox" id="col-all" checked><label for="col-all">الكل</label></div>' +
                '<div class="rp-dd-sep"></div>' +
                '<div id="colFilterOpts"></div>' +
                '</div></div>'
            ).appendTo('body');

            $(document).on('click', '.th-filter-icon', function (e) {
                e.stopPropagation();
                var colIdx = parseInt($(this).data('col'));
                if (activeColIdx === colIdx && $colPanel.hasClass('open')) { closeColPanel(); return; }
                activeColIdx = colIdx;
                var vals = [];
                if (colIdx === 1) {
                    vals = dbEntities.slice();
                    dt.column(colIdx).data().each(function (d) {
                        var clean = $('<div>').html(d).text().trim();
                        if (clean && vals.indexOf(clean) === -1) vals.push(clean);
                    });
                } else {
                    dt.column(colIdx).data().each(function (d) {
                        var clean = $('<div>').html(d).text().trim();
                        if (clean && vals.indexOf(clean) === -1) vals.push(clean);
                    });
                }
                vals.sort();
                var currentFilter = colFilterState[colIdx] || null;
                var $opts = $('#colFilterOpts').empty();
                if (colIdx === 0 && othersDepartments.length) {
                    $.each(vals, function (_, v) {
                        var chk = !currentFilter || currentFilter.indexOf(v) > -1 ? 'checked' : '';
                        if (v.indexOf('باق') > -1) {
                            $opts.append(
                                '<div class="rp-dd-item">' +
                                '<span class="col-dept-toggle" style="cursor:pointer;font-size:10px;margin-left:5px;user-select:none;">&#9654;</span>' +
                                '<input type="checkbox" class="col-cb" value="' + v + '" ' + chk + '><label>' + v + '</label></div>'
                            );
                            var $dc = $('<div class="col-dept-container" style="display:none;"></div>');
                            $.each(othersDepartments, function (_, dept) {
                                var dchk = !currentFilter || currentFilter.indexOf(dept) > -1 ? 'checked' : '';
                                $dc.append(
                                    '<div class="rp-dd-item" style="margin-right:20px;border-right:2px solid #cbd5e1;padding-right:8px;">' +
                                    '<input type="checkbox" class="col-cb" value="' + dept + '" ' + dchk + '>' +
                                    '<label style="font-size:11px;color:#475569">' + dept + '</label></div>'
                                );
                            });
                            $opts.append($dc);
                        } else {
                            $opts.append('<div class="rp-dd-item"><input type="checkbox" class="col-cb" value="' + v + '" ' + chk + '><label>' + v + '</label></div>');
                        }
                    });
                    $opts.find('.col-dept-toggle').on('click', function (e) {
                        e.stopPropagation();
                        var $dc = $(this).closest('.rp-dd-item').next('.col-dept-container');
                        $dc.slideToggle(200);
                        $(this).html($dc.is(':visible') ? '&#9660;' : '&#9654;');
                    });
                } else if (colIdx === 1 && entitySubFilters) {
                    $.each(vals, function (_, v) {
                        var chk = !currentFilter || currentFilter.indexOf(v) > -1 ? 'checked' : '';
                        if (entitySubFilters[v]) {
                            $opts.append(
                                '<div class="rp-dd-item">' +
                                '<span class="col-entity-toggle" style="cursor:pointer;font-size:10px;margin-left:5px;user-select:none;">&#9654;</span>' +
                                '<input type="checkbox" class="col-cb" value="' + v + '" ' + chk + '><label>' + v + '</label></div>'
                            );
                            var $ec = $('<div class="col-entity-container" style="display:none;"></div>');
                            $.each(entitySubFilters[v], function (_, sub) {
                                var schk = !currentFilter || currentFilter.indexOf(sub) > -1 ? 'checked' : '';
                                $ec.append(
                                    '<div class="rp-dd-item" style="margin-right:20px;border-right:2px solid #cbd5e1;padding-right:8px;">' +
                                    '<input type="checkbox" class="col-cb" value="' + sub + '" ' + schk + '>' +
                                    '<label style="font-size:11px;color:#475569">' + sub + '</label></div>'
                                );
                            });
                            $opts.append($ec);
                        } else {
                            $opts.append('<div class="rp-dd-item"><input type="checkbox" class="col-cb" value="' + v + '" ' + chk + '><label>' + v + '</label></div>');
                        }
                    });
                    $opts.find('.col-entity-toggle').on('click', function (e) {
                        e.stopPropagation();
                        var $ec = $(this).closest('.rp-dd-item').next('.col-entity-container');
                        $ec.slideToggle(200);
                        $(this).html($ec.is(':visible') ? '&#9660;' : '&#9654;');
                    });
                } else {
                    $.each(vals, function (_, v) {
                        var chk = !currentFilter || currentFilter.indexOf(v) > -1 ? 'checked' : '';
                        $opts.append('<div class="rp-dd-item"><input type="checkbox" class="col-cb" value="' + v + '" ' + chk + '><label>' + v + '</label></div>');
                    });
                }
                syncColAll();
                $('.th-filter-icon').removeClass('active');
                $(this).addClass('active');
                var rect = this.getBoundingClientRect();
                var rightSpace = window.innerWidth - rect.right;
                if (rect.right < 230) {
                    $colPanel.css({ top: rect.bottom + window.scrollY + 4, left: 10, right: 'auto' });
                } else {
                    $colPanel.css({ top: rect.bottom + window.scrollY + 4, right: Math.max(4, rightSpace), left: 'auto' });
                }
                $colPanel.addClass('open');
                $('#colFilterSearch').val('');
                $('#colFilterOpts .rp-dd-item').show();
            });

            $('#colFilterSearch').on('keyup', function () {
                var v = normalizeArabic($(this).val().toLowerCase());
                $('#colFilterOpts').children('.rp-dd-item, .col-dept-container, .col-entity-container').each(function () {
                    if ($(this).hasClass('col-dept-container') || $(this).hasClass('col-entity-container')) {
                        var hasMatch = false;
                        $(this).find('.rp-dd-item').each(function () {
                            var t = normalizeArabic($(this).text().toLowerCase());
                            var m = t.indexOf(v) > -1;
                            $(this).toggle(m);
                            if (m) hasMatch = true;
                        });
                        if (v && hasMatch) { $(this).show(); $(this).prev('.rp-dd-item').find('.col-dept-toggle, .col-entity-toggle').html('&#9660;'); }
                        else if (!v) { $(this).hide(); $(this).prev('.rp-dd-item').find('.col-dept-toggle, .col-entity-toggle').html('&#9654;'); }
                    } else {
                        $(this).toggle(normalizeArabic($(this).text().toLowerCase()).indexOf(v) > -1);
                    }
                });
            });

            $('#col-all').on('change', function () { $('.col-cb').prop('checked', this.checked); applyColFilter(); });
            $(document).on('change', '.col-cb', function () { syncColAll(); applyColFilter(); });

            function syncColAll() {
                var t = $('.col-cb').length, c = $('.col-cb:checked').length;
                $('#col-all').prop('checked', t === c);
            }

            function applyColFilter() {
                if (activeColIdx === null) return;
                var checked = $('.col-cb:checked').map(function () { return $(this).val(); }).get();
                if (checked.length === $('.col-cb').length) {
                    colFilterState[activeColIdx] = null;
                    $('.th-filter-icon[data-col="' + activeColIdx + '"]').removeClass('active');
                } else {
                    colFilterState[activeColIdx] = checked;
                    $('.th-filter-icon[data-col="' + activeColIdx + '"]').addClass('active');
                }
                dt.draw();
            }

            function closeColPanel() { $colPanel.removeClass('open'); activeColIdx = null; }

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#colFilterPanel').length && !$(e.target).hasClass('th-filter-icon')) closeColPanel();
            });

            /* Toolbar Filters */
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
                var n = $(this).data('filter'), v = normalizeArabic($(this).val().toLowerCase());
                $('#' + n + '-opts').children('.rp-dd-item, .dept-container').each(function () {
                    if ($(this).hasClass('dept-container')) {
                        var hasMatch = false;
                        $(this).find('.rp-dd-item').each(function () {
                            var t = normalizeArabic($(this).text().toLowerCase());
                            var match = t.indexOf(v) > -1;
                            $(this).toggle(match);
                            if (match) hasMatch = true;
                        });
                        if (v && hasMatch) { $(this).show(); $(this).prev('.rp-dd-item').find('.dept-toggle').html('&#9660;'); }
                        else if (!v) { $(this).hide(); $(this).prev('.rp-dd-item').find('.dept-toggle').html('&#9654;'); }
                    } else {
                        $(this).toggle(normalizeArabic($(this).text().toLowerCase()).indexOf(v) > -1);
                    }
                });
            });

            function syncAll(n) { var t = $('.cb-' + n).length, c = $('.cb-' + n + ':checked').length; $('#' + n + '-all').prop('checked', t === c); updateLabel(n); }
            function updateLabel(n) { var t = $('.cb-' + n).length, c = $('.cb-' + n + ':checked').length; $('#' + n + '-val').text(c === t ? 'الكل' : c + ' مختار'); }

            $('#year-all').on('change', function () { $('.cb-year').prop('checked', this.checked); rebuildMonths(); applyFilters(); });
            $('#month-all').on('change', function () { $('.cb-month').prop('checked', this.checked); applyFilters(); });
            $('#hosp-all').on('change', function () { $('.cb-hosp, .cb-dept').prop('checked', this.checked); syncAll('hosp'); applyFilters(); });

            $(document).on('change', '.cb-year', function () { syncAll('year'); rebuildMonths(); applyFilters(); });
            $(document).on('change', '.cb-month', function () { syncAll('month'); applyFilters(); });
            $(document).on('change', '.cb-hosp', function () {
                if ($(this).val().indexOf('باق') > -1) $('.cb-dept').prop('checked', this.checked);
                syncAll('hosp'); applyFilters();
            });
            $(document).on('change', '.cb-dept', function () {
                var total = $('.cb-dept').length, checked = $('.cb-dept:checked').length;
                $('.cb-hosp').each(function () { if ($(this).val().indexOf('باق') > -1) $(this).prop('checked', total === checked); });
                syncAll('hosp'); applyFilters();
            });

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
                    if (h.indexOf('باق') > -1 && othersDepartments.length) {
                        $c.append(
                            '<div class="rp-dd-item">' +
                            '<span class="dept-toggle" style="cursor:pointer;font-size:10px;margin-left:5px;user-select:none;">&#9654;</span>' +
                            '<input type="checkbox" class="cb-hosp" value="' + h + '" checked><label>' + h + '</label>' +
                            '</div>'
                        );
                        var $dc = $('<div class="dept-container" style="display:none;"></div>');
                        $.each(othersDepartments, function (_, dept) {
                            $dc.append(
                                '<div class="rp-dd-item" style="margin-right: 20px; border-right: 2px solid #cbd5e1; padding-right: 8px;">' +
                                '<input type="checkbox" class="cb-dept" value="' + dept + '" checked>' +
                                '<label style="font-size: 11px; color:#475569">' + dept + '</label>' +
                                '</div>'
                            );
                        });
                        $c.append($dc);
                    } else {
                        $c.append('<div class="rp-dd-item"><input type="checkbox" class="cb-hosp" value="' + h + '" checked><label>' + h + '</label></div>');
                    }
                });
                syncAll('hosp');
            }

            $(document).on('click', '.dept-toggle', function (e) {
                e.stopPropagation();
                var $dc = $(this).closest('.rp-dd-item').next('.dept-container');
                $dc.slideToggle(200);
                $(this).html($dc.is(':visible') ? '&#9660;' : '&#9654;');
            });

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

            /* DataTables Filter */
            var globalSearchQuery = '';
            $('.dataTables_filter input').off().on('input keyup', function () { globalSearchQuery = $(this).val(); dt.draw(); });

            $.fn.dataTable.ext.search.push(function (settings, searchData, index) {
                if (settings.sTableId !== 'reportsTable') return true;

                var rowNode = settings.aoData[index] ? settings.aoData[index].nTr : null;
                var rowYear = rowNode ? rowNode.getAttribute('data-year') : '';
                var rowMonth = rowNode ? rowNode.getAttribute('data-month') : '';
                var rowHosp = rowNode ? rowNode.getAttribute('data-hosp') : '';
                var rowDept = rowNode ? rowNode.getAttribute('data-dept') : '';
                var rowLocation = rowNode ? rowNode.getAttribute('data-location') : '';

                var selY = $('.cb-year:checked').map(function () { return $(this).val(); }).get();
                if (selY.length && selY.indexOf(String(rowYear)) === -1) return false;

                var selM = $('.cb-month:checked').map(function () { return $(this).val(); }).get();
                if (selM.length && selM.indexOf(String(rowMonth)) === -1) return false;

                var hospRow = String(rowHosp);
                var deptRow = String(rowDept);
                var selH = $('.cb-hosp:checked').map(function () { return $(this).val(); }).get();

                if (selH.length) {
                    if (hospRow.indexOf('باق') > -1) {
                        var selDept = $('.cb-dept:checked').map(function () { return $(this).val(); }).get();
                        if (selDept.length && selDept.indexOf(deptRow) === -1) return false;
                    } else {
                        if (selH.indexOf(hospRow) === -1) return false;
                    }
                }

                for (var ci in colFilterState) {
                    if (colFilterState[ci]) {
                        var ciNum = parseInt(ci);
                        var cellText = normalizeArabic($('<div>').html(searchData[ciNum]).text().trim()).toLowerCase();
                        var matched = false;
                        for (var k = 0; k < colFilterState[ci].length; k++) {
                            if (cellText === normalizeArabic(colFilterState[ci][k]).toLowerCase()) { matched = true; break; }
                        }
                        if (!matched && ciNum === 0 && cellText.indexOf('باق') > -1) {
                            for (var k = 0; k < colFilterState[ci].length; k++) {
                                if (rowDept && normalizeArabic(rowDept).toLowerCase() === normalizeArabic(colFilterState[ci][k]).toLowerCase()) { matched = true; break; }
                            }
                        }
                        if (!matched && ciNum === 1 && entitySubFilters) {
                            for (var k = 0; k < colFilterState[ci].length; k++) {
                                var filterVal = normalizeArabic(colFilterState[ci][k]).toLowerCase();
                                var isSubFilter = false;
                                for (var entityName in entitySubFilters) {
                                    if (entitySubFilters[entityName].indexOf(colFilterState[ci][k]) > -1) {
                                        isSubFilter = true;
                                        break;
                                    }
                                }
                                if (isSubFilter && rowLocation && normalizeArabic(rowLocation).toLowerCase() === filterVal) {
                                    matched = true;
                                    break;
                                }
                            }
                        }
                        if (!matched) return false;
                    }
                }

                if (globalSearchQuery) {
                    var terms = normalizeArabic(globalSearchQuery).toLowerCase().split(/\s+/);
                    terms = $.grep(terms, function (t) { return t.trim() !== ''; });
                    var rowText = searchData.map(function (val) {
                        return normalizeArabic(val.replace(/<[^>]*>/g, '')).toLowerCase();
                    }).join(' ');
                    for (var i = 0; i < terms.length; i++) {
                        if (rowText.indexOf(terms[i]) === -1) return false;
                    }
                }

                return true;
            });

            function applyFilters() { updateLabel('year'); updateLabel('month'); updateLabel('hosp'); dt.draw(); }

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

                $('#foot_cases').text(c); $('#foot_amt').text(fmt(a));
                $('#foot_rev_cases').text(rc); $('#foot_rev_amt').text(fmt(ra));
                $('#foot_coll_cases').text(cc); $('#foot_pay_amt').text(fmt(pa));
                $('#foot_diff').text(fmt(d));
            }

            $('#btnReset').on('click', function () {
                colFilterState = {}; $('.th-filter-icon').removeClass('active');
                buildYears(); buildHospitals(); rebuildMonths(); applyFilters();
            });
            $('#btnExcel').on('click', function () { dt.button(0).trigger(); });

            buildYears();
            buildHospitals();
            rebuildMonths();
            updateSummary();
        });
    </script>
@endsection