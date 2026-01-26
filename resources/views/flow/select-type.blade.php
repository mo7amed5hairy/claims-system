@extends('layouts.app')

@section('title', 'اختيار نوع الجهة')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto; min-height: 400px; display: flex; flex-direction: column; justify-content: center;">
    <div class="card-header" style="text-align: center; font-size: 1.5rem; border: none; padding-bottom: 2rem;">
        الرجاء اختيار نوع الجهة
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 0 1rem;">
        
        <form action="{{ route('flow.store-type') }}" method="POST" style="display: contents;">
            @csrf
            <button type="submit" name="entity_type" value="contracts" class="btn-card">
                <i class="fa-solid fa-file-contract"></i>
                <span>تعاقدات</span>
            </button>
        </form>

        <form action="{{ route('flow.store-type') }}" method="POST" style="display: contents;">
            @csrf
            <button type="submit" name="entity_type" value="health_insurance" class="btn-card">
                <i class="fa-solid fa-notes-medical"></i>
                <span>تأمين صحي</span>
            </button>
        </form>

        <form action="{{ route('flow.store-type') }}" method="POST" style="display: contents;">
            @csrf
            <button type="submit" name="entity_type" value="ministry_health" class="btn-card">
                <i class="fa-solid fa-hospital-user"></i>
                <span>وزارة الصحة</span>
            </button>
        </form>

        <form action="{{ route('flow.store-type') }}" method="POST" style="display: contents;">
            @csrf
            <button type="submit" name="entity_type" value="comprehensive_insurance" class="btn-card">
                <i class="fa-solid fa-heart-pulse"></i>
                <span>تأمين صحي شامل</span>
            </button>
        </form>
    </div>
</div>

<style>
    .btn-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-color);
        font-family: inherit;
    }
    
    .btn-card i {
        font-size: 2.5rem;
        color: var(--primary-color);
    }
    
    .btn-card span {
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .btn-card:hover {
        border-color: var(--primary-color);
        background-color: #eff6ff;
        transform: translateY(-5px);
        box-shadow: 0 4px 6px -1px rgb(37 99 235 / 0.1);
    }
</style>
@endsection
