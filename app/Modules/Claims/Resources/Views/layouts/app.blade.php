<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'نظام المطالبات')</title>
    <!-- Local Cairo Font -->
    <link rel="stylesheet" href="{{ asset('modules/claims/css/cairo.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('modules/claims/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Local FontAwesome -->
    <link rel="stylesheet" href="{{ asset('modules/claims/css/all.min.css') }}">

    <!-- DataTables & Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('modules/claims/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/claims/css/buttons.dataTables.min.css') }}">

    <style>
        /* Navbar Dropdown Styles */
        .navbar-user-dropdown {
            position: relative;
            display: inline-block;
            background: #ffffff;
            padding: .1rem;
            border-radius: 50px;
        }

        .navbar-user-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            font-family: inherit;
            padding: 5px 10px;
            border-radius: 25px;
            transition: background 0.2s;
        }

        .navbar-user-btn:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .user-avatar-placeholder {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            background-color: white;
            min-width: 220px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1px;
        }

        .dropdown-content a,
        .dropdown-content button.dropdown-item {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            text-align: right;
            border: none;
            background: none;
            font-family: inherit;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .dropdown-content a:hover,
        .dropdown-content button.dropdown-item:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .dropdown-divider {
            height: 1px;
            background-color: #eee;
            margin: 4px 0;
        }

        .navbar-user-dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>

<body>
    <div class="app-container">
        @if (auth()->check())
            <nav class="navbar">
                <a href="{{ route('dashboard') }}" style="color: #fff !important;" class="navbar-brand">
                    {{-- <i class="fa-solid fa-chart-line"></i> --}}
                    <i class="fa-solid fa-file-circle-check"></i>
                    <span>
                        نظام إدارة المطالبات المالية
                        <br>
                        Claims Management System
                    </span>
                </a>
                <div class="navbar-menu">
                    <div class="navbar-user-dropdown">
                        <button class="navbar-user-btn">
                            <span>مرحباً، {{ Auth::user()->name }}</span>
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="user-avatar">
                            @else
                                <div class="user-avatar-placeholder">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <i class="fa-solid fa-chevron-down" style="font-size: 12px; margin-right: 5px;"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="{{ route('dashboard') }}">
                                <i class="fa-solid fa-home"></i> {{ trans('messages.dashboard') }}
                            </a>
                            <a href="{{ route('profile.edit') }}">
                                <i class="fa-solid fa-user-circle"></i> الملف الشخصي
                            </a>
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('users.index') }}">
                                    <i class="fa-solid fa-users-cog"></i> إدارة المستخدمين
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>

                            <a href="{{ route('entities.index') }}">
                                <i class="fa-solid fa-file-contract"></i> جهات التعاقد
                            </a>
                            <a href="{{ route('claims.index') }}">
                                <i class="fa-solid fa-file-invoice-dollar"></i> المطالبات
                            </a>
                            <a href="{{ route('returns.index') }}">
                                <i class="fa-solid fa-file-invoice"></i> الفواتير العائدة
                            </a>
                            <a href="{{ route('payments.index') }}">
                                <i class="fa-solid fa-money-check-dollar"></i> أوامر الدفع
                            </a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fa-solid fa-sign-out-alt"></i> {{ trans('messages.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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

    <!-- DataTables & Buttons JS -->
    <script src="{{ asset('modules/claims/js/jquery.min.js') }}"></script>
    <script src="{{ asset('modules/claims/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('modules/claims/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('modules/claims/js/jszip.min.js') }}"></script>
    <script src="{{ asset('modules/claims/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('modules/claims/js/buttons.print.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Global Select2 Fix: Auto-close on selection
            $(document).on('select2:select', '.select2', function (e) {
                $(this).select2('close');
            });
        });
    </script>

    @yield('scripts')
</body>

</html>