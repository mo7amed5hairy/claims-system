@extends('layouts.app')

@section('title', 'الخيارات الفرعية')

@section('content')
<div class="card" style="max-width: 600px; margin: 2rem auto;">
    <div class="card-header">
        استكمال البيانات
    </div>

    <form method="POST" action="{{ route('flow.store-options') }}">
        @csrf
        
        @if($type === 'health_insurance')
            <div class="form-group">
                <label class="form-label">الفرع / الموقع</label>
                <select name="location" class="form-control" required>
                    <option value="">اختر...</option>
                    <option value="Cairo">القاهرة</option>
                    <option value="Giza">الجيزة</option>
                    <option value="DistrictHead">رئاسة الحي</option>
                    <option value="Qalyubia">القليوبية</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">القوائم</label>
                <select name="list_type" class="form-control" required>
                    <option value="">اختر...</option>
                    <option value="branches">فروع</option>
                    <option value="waiting_list">قوائم انتظار</option>
                </select>
            </div>

        @elseif($type === 'ministry_health')
            <div class="form-group">
                <label class="form-label">المديرية</label>
                <select name="directorate" class="form-control" required>
                    <option value="">اختر...</option>
                    <option value="Cairo">القاهرة</option>
                    <option value="Giza">الجيزة</option>
                    <option value="Qalyubia">القليوبية</option>
                </select>
            </div>

        @elseif($type === 'comprehensive_insurance')
            <div class="form-group">
                <label class="form-label">المحافظة</label>
                <select name="governorate" class="form-control" required>
                    <option value="">اختر...</option>
                    <option value="Luxor">الأقصر</option>
                    <option value="Aswan">أسوان</option>
                    <option value="SouthSinai">جنوب سيناء</option>
                    <option value="Nuba">النوبة</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">القانون العلاجي</label>
                <select name="law" class="form-control" required>
                    <option value="">اختر...</option>
                    <option value="students">طلبة</option>
                    <option value="born">مواليد</option>
                    <option value="beneficiaries">منتفعين</option>
                    <option value="breadwinner">امرأة معيلة</option>
                </select>
            </div>

        @else
            <!-- Contracts 'contracts' -->
            <div class="alert alert-info" style="margin-bottom: 1rem;">
                هذا الاختيار لا يتطلب خيارات فرعية إضافية. اضغط التالي للمتابعة.
            </div>
            <input type="hidden" name="type_confirmed" value="1">
        @endif

        <div style="margin-top: 1.5rem; text-align: left;">
            <button type="submit" class="btn">
                التالي
                <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i>
            </button>
        </div>
    </form>
</div>
@endsection
