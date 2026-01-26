@extends('claims::layouts.app')
@section('title', 'الخيارات الفرعية')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
    <span class="breadcrumb-item active">الخيارات الفرعية</span>
</div>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-sliders"></i> استكمال البيانات</h1>
    <p class="page-subtitle">{{ $type === 'contracts' ? 'اختيار خيارات التعاقدات' : 'اختيار الخيارات الفرعية' }}</p>
</div>

<div class="form-container">
    <div class="form-card">
        <form method="POST" action="{{ route('flow.store-options') }}">
            @csrf

            @if($type === 'insurance')
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-map-location-dot"></i> الفرع / الموقع
                    </label>
                    <select name="location" class="form-control" required>
                        <option value="">اختر الفرع...</option>
                        <option value="Cairo">القاهرة</option>
                        <option value="Giza">الجيزة</option>
                        <option value="DistrictHead">رئاسة الحى</option>
                        <option value="Qalyubia">القليوبية</option>
                    </select>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>لا يتطلب خيارات إضافية - يمكنك المتابعة مباشرة</span>
                </div>
                <input type="hidden" name="type_confirmed" value="1">
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-arrow-left"></i> التالي
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> العودة
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
