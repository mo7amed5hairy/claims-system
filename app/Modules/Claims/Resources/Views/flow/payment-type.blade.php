@extends('claims::layouts.app')
@section('title', 'اختيار نوع الدفع')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">نوع الدفع</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-credit-card"></i> اختيار نوع الدفع</h1>
        <p class="page-subtitle">
            يرجى تحديد نوع الدفع للمتابعة
        </p>
    </div>

    <div class="operations-container">
        <div class="operations-grid" style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">
            <a href="{{ route('flow.operations') }}" class="operation-card"
                style="width: 450px; max-width: 100%; margin: 0;">
                <div class="operation-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <h3 class="operation-title">دفع عادي</h3>
                <p class="operation-text">متابعة لسير العمل الطبيعي للمطالبات</p>
                <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
            </a>

            <a href="{{ route('flow.prepaid_operations') }}" class="operation-card"
                style="width: 450px; max-width: 100%; margin: 0;">
                <div class="operation-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h3 class="operation-title">دفع مسبق</h3>
                <p class="operation-text">متابعة لسير عمل لمطالبات ذات الدفع المسبق</p>
                <span class="operation-arrow"><i class="fa-solid fa-arrow-left"></i></span>
            </a>
        </div>
    </div>
@endsection