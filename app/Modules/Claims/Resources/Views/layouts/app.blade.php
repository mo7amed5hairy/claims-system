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
            /* background: var(--primary-color); */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
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

        .content-wrapper {
            padding: 0 !important;
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

        /* Navbar Header Links Styles */
        .nav-item-dropdown {
            position: relative;
            display: inline-block;
        }

        .nav-item-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-item-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-item-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Mobile Responsive Navbar styles */
        .navbar-toggler {
            display: none;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: white;
            font-size: 18px;
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 15px;
            /* Spacer from brand */
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 1024px) {
            .navbar {
                flex-wrap: wrap;
                position: relative;
            }

            .navbar-toggler {
                display: block;
                /* Show burger icon */
            }

            .nav-links-container {
                display: none !important;
                flex-direction: column;
                align-items: flex-start !important;
                width: 100%;
                background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
                position: absolute;
                top: 100%;
                right: 0;
                padding: 10px 0;
                z-index: 1000;
                gap: 5px !important;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .nav-links-container.show-mobile {
                display: flex !important;
            }

            .nav-item-dropdown {
                width: 100%;
            }

            .nav-item-btn {
                width: 100%;
                justify-content: flex-start;
                padding: 12px 20px;
                border-radius: 0;
            }

            .dropdown-content {
                position: static;
                box-shadow: none;
                background-color: rgba(0, 0, 0, 0.2);
                border-radius: 0;
            }

            .dropdown-content a {
                color: #fff;
                padding-right: 40px;
            }

            .dropdown-content a:hover {
                background-color: rgba(255, 255, 255, 0.1);
                color: #fff;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        @if (auth()->check())
            <nav class="navbar" style="position: relative;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('dashboard') }}" style="color: #fff !important;" class="navbar-brand">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>
                            نظام إدارة المطالبات المالية
                            <br>
                            Claims Management System
                        </span>
                    </a>
                    <button class="navbar-toggler" id="mobileNavToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>

                <div class="nav-links-container"
                    style="flex-grow: 1; display: flex; justify-content: center; gap: 15px; margin-right: 30px; align-items: center; flex-wrap: wrap;">
                    @if(Auth::user()->canAccessNonPayments())
                        <a href="{{ route('hospitals.index') }}" class="nav-item-btn" style="text-decoration: none;">
                            <i class="fa-solid fa-hospital"></i> المستشفيات والأقسام
                        </a>
                        <a href="{{ route('entities.index') }}" class="nav-item-btn" style="text-decoration: none;">
                            <i class="fa-solid fa-file-contract"></i> جهات التعاقد
                        </a>
                        <div class="nav-item-dropdown">
                            <button class="nav-item-btn">
                                <i class="fa-solid fa-file-invoice-dollar"></i> المطالبات <i class="fa-solid fa-chevron-down"
                                    style="font-size: 10px; margin-right: 4px;"></i>
                            </button>
                            <div class="dropdown-content">
                                <a href="{{ route('claims.index') }}"><i class="fa-solid fa-file-invoice-dollar"></i> المطالبات
                                    العادية</a>
                                <a href="{{ route('prepaid-claims.index') }}"><i class="fa-solid fa-file-invoice-dollar"></i>
                                    مطالبات مسبقة الدفع</a>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->canAccessPayments())
                        <div class="nav-item-dropdown">
                            <button class="nav-item-btn">
                                <i class="fa-solid fa-money-check-dollar"></i> أوامر الدفع <i class="fa-solid fa-chevron-down"
                                    style="font-size: 10px; margin-right: 4px;"></i>
                            </button>
                            <div class="dropdown-content">
                                <a href="{{ route('payments.index') }}"><i class="fa-solid fa-money-check-dollar"></i> أوامر
                                    الدفع العادية</a>
                                <a href="{{ route('prepaid-payments.index') }}"><i class="fa-solid fa-money-check-dollar"></i>
                                    أوامر دفع مسبقة الدفع</a>
                            </div>
                        </div>

                        <div class="nav-item-dropdown">
                            <button class="nav-item-btn">
                                <i class="fa-solid fa-receipt"></i> الفواتير المخصمة <i class="fa-solid fa-chevron-down"
                                    style="font-size: 10px; margin-right: 4px;"></i>
                            </button>
                            <div class="dropdown-content">
                                <a href="{{ route('discounted-invoices.index') }}"><i class="fa-solid fa-receipt"></i> الفواتير
                                    المخصمة العادية</a>
                                <a href="{{ route('prepaid-discounted-invoices.index') }}"><i class="fa-solid fa-receipt"></i>
                                    الفواتير المخصمة مسبقاً</a>
                            </div>
                        </div>

                        <div class="nav-item-dropdown">
                            <button class="nav-item-btn">
                                <i class="fa-solid fa-chart-pie"></i> التقارير <i class="fa-solid fa-chevron-down"
                                    style="font-size: 10px; margin-right: 4px;"></i>
                            </button>
                            <div class="dropdown-content">
                                <a href="{{ route('reports.index') }}"><i class="fa-solid fa-chart-pie"></i> التقارير
                                    العادية</a>
                                <a href="{{ route('prepaid-reports.index') }}"><i class="fa-solid fa-chart-pie"></i> تقارير
                                    مسبقة الدفع</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="navbar-menu">
                    <div class="navbar-user-dropdown">
                        <button class="navbar-user-btn">
                            <span>مرحباً، {{ Auth::user()->name }}</span>
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="user-avatar">
                            @else
                                <div class="user-avatar-placeholder">
                                    <i class="fa-solid fa-user"></i>
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

            // Mobile Navbar Toggle Script
            $('#mobileNavToggle').on('click', function (e) {
                e.preventDefault();
                $('.nav-links-container').toggleClass('show-mobile');
            });

            // Toggle dropdowns on click in mobile view
            $('.nav-item-btn').on('click', function (e) {
                if ($(window).width() <= 1024 && $(this).parent().hasClass('nav-item-dropdown')) {
                    var $content = $(this).siblings('.dropdown-content');
                    $('.dropdown-content').not($content).slideUp(200); // close others
                    $content.slideToggle(200);
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>