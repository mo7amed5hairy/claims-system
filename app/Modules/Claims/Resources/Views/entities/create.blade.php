@extends('claims::layouts.app')

@section('title', 'إضافة جهة جديدة')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-plus"></i> إضافة جهة جديدة</h1>
</div>

<div class="table-container" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('entities.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">اسم الجهة</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">النوع</label>
            <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
            @error('type') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> حفظ الجهة
            </button>
            <a href="{{ route('entities.index') }}" class="btn" style="background: #6c757d;">إلغاء</a>
        </div>
    </form>
</div>
@endsection