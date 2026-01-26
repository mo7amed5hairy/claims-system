@extends('layouts.app')

@section('title', 'الرئيسية - نظام المطالبات')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="color: #2563eb; background: #eff6ff;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h4>إجمالي المطالبات (الشهر)</h4>
                <div class="value">0 ج.م</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="color: #22c55e; background: #dcfce7;">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>المطالبات المقبولة</h4>
                <div class="value">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="color: #ef4444; background: #fee2e2;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div class="stat-info">
                <h4>الفواتير العائدة</h4>
                <div class="value">0 ج.م</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="color: #f59e0b; background: #fef3c7;">
                <i class="fa-solid fa-building"></i>
            </div>
            <div class="stat-info">
                <h4>عدد الجهات</h4>
                <div class="value">{{ \Illuminate\Support\Facades\DB::table('claim_entities')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            العمليات السريعة
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="#" class="btn" style="justify-content: center; gap: 8px;">
                <i class="fa-solid fa-plus"></i> تسجيل مطالبة
            </a>
            <a href="#" class="btn" style="background-color: var(--secondary-color); justify-content: center; gap: 8px;">
                <i class="fa-solid fa-building-circle-check"></i> إضافة جهة
            </a>
            <a href="#" class="btn" style="background-color: var(--danger-color); justify-content: center; gap: 8px;">
                <i class="fa-solid fa-arrow-rotate-left"></i> تسجيل عائد
            </a>
            <a href="#" class="btn" style="background-color: var(--success-color); justify-content: center; gap: 8px;">
                <i class="fa-solid fa-money-bill-wave"></i> أمر دفع
            </a>
        </div>
    </div>
@endsection
