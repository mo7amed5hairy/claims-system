<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول - نظام المطالبات</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fa-solid fa-shield-halved"></i>
                <div>نظام المطالبات</div>
            </div>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus tabindex="1">
                    @error('username')
                        <div style="color:red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input id="password" type="password" class="form-control" name="password" required tabindex="2">
                    @error('password')
                        <div style="color:red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input id="remember_me" type="checkbox" name="remember" tabindex="3">
                    <label for="remember_me" style="font-size: 0.9rem; cursor: pointer;">تذكرني</label>
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn" style="width: 100%;" tabindex="4">
                        تسجيل الدخول
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
