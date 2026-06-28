@extends('claims::layouts.app')
@section('title', 'لوحة عمليات الدفع المسبق')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.payment-type') }}" class="breadcrumb-item">
            نوع الدفع
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">لوحة الدفع المسبق</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-wallet"></i> لوحة عمليات الدفع المسبق</h1>
        <p class="page-subtitle">
            المستشفى:
            <strong>{{ \App\Modules\Claims\Models\Hospital::find(session('flow_hospital_id'))->name ?? 'غير محدد' }}</strong>
        </p>
    </div>

    <div class="operations-container">
        <div class="operations-grid" style="display: flex; justify-content: center; gap: 25px; flex-wrap: wrap;">
            @if(auth()->user()->canAccessNonPayments())
                @can('viewAny', App\Modules\Claims\Models\Claim::class)
                    <a href="{{ route('prepaid-claims.create') }}" class="btn-action" style="display:none;"></a> {{-- Hidden helper
                    --}}
                    <a href="{{ route('prepaid-claims.create') }}" class="operation-card"
                        style="width: 350px; max-width: 100%; margin: 0;">
                        <div class="operation-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <h3 class="operation-title">مطالبات دفع مسبق</h3>
                        <p class="operation-text">إضافة مطالبات دفع مسبق جديدة</p>
                        <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
                    </a>
                @endcan
            @endif

            @if(auth()->user()->canAccessPayments())
                @can('viewAny', App\Modules\Claims\Models\PaymentOrder::class)
                    <a href="{{ route('prepaid-payments.index') }}" class="operation-card"
                        style="width: 350px; max-width: 100%; margin: 0;">
                        <div class="operation-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </div>
                        <h3 class="operation-title">أوامر دفع مسبق</h3>
                        <p class="operation-text">إدارة أوامر الدفع المسبق</p>
                        <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
                    </a>

                    <a href="{{ route('prepaid-discounted-invoices.index') }}" class="operation-card"
                        style="width: 350px; max-width: 100%; margin: 0;">
                        <div class="operation-icon" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <h3 class="operation-title">الفواتير المخصمة مسبقاً</h3>
                        <p class="operation-text">عرض الفواتير المخصمة للمطالبات المسبقة</p>
                        <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
                    </a>

                    <a href="{{ route('financial-receipts.index') }}" class="operation-card"
                        style="width: 350px; max-width: 100%; margin: 0;">
                        <div class="operation-icon" style="background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%);">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <h3 class="operation-title">استلام دفعة مالية</h3>
                        <p class="operation-text">تسجيل وإدارة الدفعات المالية المستلمة</p>
                        <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
                    </a>
                @endcan
            @endif
        </div>
    </div>
@endsection