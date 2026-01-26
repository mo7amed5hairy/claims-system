<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fa-solid fa-file-invoice-dollar me-2" style="margin-left: 10px;"></i>
                نظام المطالبات
            </div>

            <ul class="nav-links">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-home"></i>
                        الرئيسية
                    </a>
                </li>
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('hospitals.index') }}" class="{{ request()->routeIs('hospitals.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-hospital"></i>
                        المستشفيات
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('entities.index') }}" class="{{ request()->routeIs('entities.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        جهات المطالبة
                    </a>
                </li>
                <li>
                    <a href="{{ route('claims.index') }}" class="{{ request()->routeIs('claims.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-signature"></i>
                        المطالبات
                    </a>
                </li>
                <li>
                    <a href="{{ route('entities.index') }}" class="{{ request()->routeIs('entities.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-building"></i>
                        جهات المطالبة
                    </a>
                </li>
                <li>
                    <a href="{{ route('returns.index') }}" class="{{ request()->routeIs('returns.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-rotate-left"></i>
                        الفواتير العائدة
                    </a>
                </li>
                <li>
                    <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        أوامر الدفع
                    </a>
                </li>

                <li style="margin-top: auto; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fa-solid fa-sign-out-alt"></i>
                            تسجيل خروج
                        </button>
                    </form>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="user-info">
                    <span style="font-weight: 500;">مرحباً، {{ auth()->user()->name }}</span>
                </div>
            </header>

            <div class="content-wrapper">
                @if(session('success'))
                <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    {{ session('success') }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>