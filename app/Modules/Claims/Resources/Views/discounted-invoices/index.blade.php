@extends('claims::layouts.app')

@section('title', 'الفواتير المخصمة')

@section('content')
<div class="page-header" style="padding: 10px 15px; margin-bottom: 6px; background: white; border-bottom: 1px solid #e2e8f0;">
    <h1 class="page-title" style="font-size: 18px; margin: 0;">
        <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary-color);"></i> الفواتير المخصمة
    </h1>
    <span style="font-size: 12px; color: #64748b;">
        <i class="fa-solid fa-info-circle"></i> يتم إنشاؤها تلقائياً عند إضافة أوامر الدفع
    </span>
</div>

<div class="content-wrapper" style="padding: 0 15px;">
    @if(session('success'))
        <div class="alert alert-success" style="margin: 10px 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="form-card" style="padding: 15px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="table-container" style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">#</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">رقم المطالبة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">عدد الفواتير الأصلي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">عدد الفواتير المخصمة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">المبلغ الأصلي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">المبلغ بعد الخصم</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">المبلغ الغير مسدد</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0; font-size: 13px;">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discountedInvoices as $invoice)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; font-size: 12px;">{{ $invoice->id }}</td>
                            <td style="padding: 10px; font-size: 12px;">{{ $invoice->claim_number }}</td>
                            <td style="padding: 10px; font-size: 12px;">{{ $invoice->original_invoice_count }}</td>
                            <td style="padding: 10px; font-size: 12px; color: #dc2626;">{{ $invoice->discounted_invoice_count }}</td>
                            <td style="padding: 10px; font-size: 12px;">{{ number_format($invoice->original_amount, 2) }} ج.م</td>
                            <td style="padding: 10px; font-size: 12px;">{{ number_format($invoice->discounted_amount, 2) }} ج.م</td>
                            <td style="padding: 10px; font-size: 12px; color: #dc2626; font-weight: bold;">{{ number_format($invoice->unpaid_amount, 2) }} ج.م</td>
                            <td style="padding: 10px; font-size: 12px;">{{ $invoice->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 20px; text-align: center; color: #64748b;">
                                <i class="fa-solid fa-inbox" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                لا توجد فواتير مخصمة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
