@extends('claims::layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-home"></i> لوحة التحكم</h1>
        <p class="page-subtitle">اختر الجهة والمستشفى والقسم للبدء</p>
    </div>

    <form action="{{ route('flow.store-type') }}" method="POST">
        @csrf
        <div class="cards-grid">
            <button type="submit" name="entity_type" value="contracts" class="card">
                <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <h3 class="card-title">التعاقدات</h3>
                <p class="card-text">إدارة التعاقدات والاتفاقيات</p>
            </button>

            <button type="submit" name="entity_type" value="insurance" class="card">
                <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fa-solid fa-shield"></i>
                </div>
                <h3 class="card-title">التأمين الصحي</h3>
                <p class="card-text">إدارة المطالبات التأمينية</p>
            </button>

            <button type="submit" name="entity_type" value="ministry" class="card">
                <div class="card-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fa-solid fa-hospital"></i>
                </div>
                <h3 class="card-title">مديرية الشئون الصحية</h3>
                <p class="card-text">إدارة مطالبات مديرية الشئون الصحية</p>
            </button>

            <button type="submit" name="entity_type" value="comprehensive" class="card">
                <div class="card-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="card-title">التأمين الصحي الشامل</h3>
                <p class="card-text">إدارة التأمين الصحي الشامل</p>
            </button>

            <button type="submit" name="entity_type" value="waiting_lists" class="card">
                <div class="card-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fa-solid fa-list-ol"></i>
                </div>
                <h3 class="card-title">قوائم الانتظار</h3>
                <p class="card-text">إدارة قوائم انتظار المستشفيات</p>
            </button>
        </div>
    </form>
@endsection