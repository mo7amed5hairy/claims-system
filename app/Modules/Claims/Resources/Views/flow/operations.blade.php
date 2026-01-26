@extends('claims::layouts.app')
@section('title', 'لوحة العمليات')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <span class="breadcrumb-item active">لوحة العمليات</span>
</div>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-sliders"></i> لوحة العمليات</h1>
    <p class="page-subtitle">
        المستشفى: <strong>{{ \App\Modules\Claims\Models\Hospital::find(session('flow_hospital_id'))->name ?? 'غير محدد' }}</strong>
    </p>
</div>

<div class="operations-container">
    <div class="operations-grid">
        <a href="{{ route('claims.create') }}" class="operation-card">
            <div class="operation-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h3 class="operation-title">تسجيل مطالبات</h3>
            <p class="operation-text">إضافة مطالبات جديدة</p>
            <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
        </a>

        <a href="{{ route('entities.index') }}" class="operation-card">
            <div class="operation-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fa-solid fa-building"></i>
            </div>
            <h3 class="operation-title">تسجيل جهات</h3>
            <p class="operation-text">إدارة الجهات والشركاء</p>
            <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
        </a>

        <a href="{{ route('returns.index') }}" class="operation-card">
            <div class="operation-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <h3 class="operation-title">فواتير عائدة</h3>
            <p class="operation-text">إدارة الفواتير المرجعة</p>
            <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
        </a>

        <a href="{{ route('payments.index') }}" class="operation-card">
            <div class="operation-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <h3 class="operation-title">أوامر دفع</h3>
            <p class="operation-text">إدارة أوامر الدفع</p>
            <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
        </a>
    </div>
</div>
@endsection
