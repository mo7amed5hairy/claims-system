<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'نظام المطالبات')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        @if (auth()->check())
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fa-solid fa-chart-line"></i>
                <span>نظام المطالبات</span>
            </div>
            <ul class="navbar-menu">
                <li><a href="{{ route('dashboard') }}">{{ trans('messages.dashboard') }}</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout">{{ trans('messages.logout') }}</button>
                    </form>
                </li>
            </ul>
        </nav>
        <div class="dashboard-container">
            @endif

            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i> {{ $message }}
            </div>
            @endif

            @if ($message = Session::get('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-circle"></i> {{ $message }}
            </div>
            @endif

            @yield('content')

            @if (auth()->check())
        </div>
        @endif
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>