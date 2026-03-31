@extends('claims::layouts.app')

@section('title', 'قوائم الانتظار')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">قوائم الانتظار</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-list-ol"></i> قوائم الانتظار</h1>
        <p class="page-subtitle">اختر نوع قائمة الانتظار للمتابعة</p>
    </div>

    <div class="cards-grid two-cards">
        <a href="{{ route('flow.waiting-lists-insurance') }}" class="card">
            <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fa-solid fa-shield"></i>
            </div>
            <h3 class="card-title">قوائم انتظار التأمين الصحي</h3>
            <p class="card-text">إدارة قوائم انتظار مستشفيات التأمين الصحي</p>
        </a>

        <a href="{{ route('flow.waiting-lists-ministry') }}" class="card">
            <div class="card-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fa-solid fa-hospital"></i>
            </div>
            <h3 class="card-title">قوائم انتظار مديرية الشئون الصحية</h3>
            <p class="card-text">إدارة قوائم انتظار مستشفيات المديرية</p>
        </a>
    </div>
@endsection
