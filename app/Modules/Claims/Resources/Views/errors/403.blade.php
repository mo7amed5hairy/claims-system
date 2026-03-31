@extends('claims::layouts.app')

@section('content')
    <div class="card" style="text-align: center; padding: 60px 20px;">
        <div style="margin-bottom: 30px;">
            <i class="fa-solid fa-lock" style="font-size: 80px; color: #dc3545;"></i>
        </div>
        <h1 style="font-size: 32px; color: #dc3545; margin-bottom: 20px;">غير مسموح بالدخول</h1>
        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
            عذراً، لا يوجد لديك صلاحية للوصول إلى هذه الصفحة.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-home"></i> العودة للصفحة الرئيسية
        </a>
    </div>
@endsection
