@extends('claims::layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-user-circle"></i> الملف الشخصي</h1>
</div>

<div class="card" style="display:grid !important; max-width: 800px; margin: 0 auto;">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="profile-header" style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
            <div class="avatar-upload" style="position: relative;">
                <div class="current-avatar">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color);">
                    @else
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--bg-color); display: flex; align-items: center; justify-content: center; font-size: 40px; color: var(--primary-color); border: 3px solid var(--primary-color);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <label for="avatar" class="avatar-edit-btn" style="position: absolute; bottom: 0; right: 0; background: var(--primary-color); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white;">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" id="avatar" name="avatar" style="display: none;" accept="image/*" onchange="previewImage(this)">
            </div>
            
            <div class="user-info">
                <h2 style="margin: 0; font-size: 24px;">{{ $user->name }}</h2>
                <p style="margin: 5px 0 0; color: #666;">{{ $user->role ?? 'User' }}</p>
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">الاسم</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
           <div class="alert alert-info">
            <h3 style="font-size: 18px; margin-bottom: 20px;">تغيير كلمة المرور</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">اترك الحقول فارغة إذا كنت لا تريد تغيير كلمة المرور</p>
           </div>
            <div class="form-group">
                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                <input type="password" placeholder="XXXXXXXX" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                <input type="password" placeholder="XXXXXXXX" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
        </div>

        <div class="form-actions" style="margin-top: 30px; text-align: left;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> حفظ التغييرات
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.querySelector('.current-avatar img');
            if (!img) {
                // Create img if it doesn't exist (case where user had no avatar)
                var container = document.querySelector('.current-avatar');
                container.innerHTML = '<img src="" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color);">';
                img = container.querySelector('img');
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
@endsection
