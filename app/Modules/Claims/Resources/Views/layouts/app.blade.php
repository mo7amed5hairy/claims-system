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
        /* ===== Navbar Base ===== */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0 24px;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff !important;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.3;
            flex-shrink: 0;
        }

        .navbar-brand i {
            font-size: 28px;
        }

        /* ===== Toggler (Burger) - Always on far left ===== */
        .navbar-toggler {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            font-size: 20px;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            margin-left: auto;
            /* Push to far right in its flex container */
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* ===== Nav Links Container ===== */
        .nav-links-container {
            display: flex;
            justify-content: center;
            gap: 2px;
            align-items: center;
            flex-wrap: wrap;
            flex: 1;
            margin: 0 20px;
        }

        /* ===== Desktop Dropdowns ===== */
        .nav-item-dropdown {
            position: relative;
            display: inline-block;
        }

        .nav-item-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .nav-item-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-item-dropdown:hover .dropdown-content,
        .nav-item-dropdown.dropdown-open .dropdown-content {
            display: block;
        }

        /* ===== User Dropdown (Professional Style) ===== */
        .navbar-menu {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .navbar-user-dropdown {
            position: relative;
            display: inline-block;
        }

        .navbar-user-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            font-family: inherit;
            padding: 6px 10px 6px 6px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .navbar-user-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-user-btn span {
            font-size: 13px;
            font-weight: 600;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-avatar-placeholder {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .navbar-user-dropdown:hover .dropdown-content,
        .navbar-user-dropdown.dropdown-open .dropdown-content {
            display: block;
        }

        /* ===== Dropdown Content (Shared) ===== */
        .dropdown-content {
            display: none;
            position: absolute;
            left: 0;
            top: calc(100% + 6px);
            background-color: white;
            min-width: 220px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border-radius: 12px;
            overflow: hidden;
            padding: 6px 0;
        }

        .nav-item-dropdown .dropdown-content {
            left: auto;
            right: 0;
        }

        .dropdown-content a,
        .dropdown-content button.dropdown-item {
            color: #1e293b;
            padding: 10px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            text-align: right;
            border: none;
            background: none;
            font-family: inherit;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.15s;
        }

        .dropdown-content a:hover,
        .dropdown-content button.dropdown-item:hover {
            background-color: #f1f5f9;
            color: #6366f1;
        }

        .dropdown-content a i,
        .dropdown-content button.dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 15px;
            color: #94a3b8;
        }

        .dropdown-content a:hover i,
        .dropdown-content button.dropdown-item:hover i {
            color: #6366f1;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 4px 0;
        }

        .content-wrapper {
            padding: 0 !important;
        }

        /* ===== Mobile Responsive ===== */
        /* Medium screens: tighten spacing */
        @media (max-width: 1400px) and (min-width: 1201px) {
            .nav-links-container {
                gap: 1px;
                margin: 0 4px;
                flex-wrap: nowrap;
            }
            .nav-item-btn {
                font-size: 10px;
                padding: 6px 6px;
                gap: 3px;
            }
            .navbar-user-btn span {
                max-width: 70px;
                font-size: 11px;
            }
            .navbar-brand span {
                font-size: 11px !important;
            }
            .navbar-brand i {
                font-size: 22px !important;
            }
            .navbar {
                padding: 0 12px;
            }
        }

        @media (max-width: 1200px) {
            .navbar-toggler {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .nav-links-container {
                display: none !important;
                flex-direction: column;
                align-items: stretch !important;
                width: 100%;
                background: #1e293b;
                position: absolute;
                top: 64px;
                right: 0;
                left: 0;
                padding: 8px 0;
                z-index: 1000;
                gap: 2px !important;
                margin: 0;
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
                border-top: 1px solid rgba(255, 255, 255, 0.08);
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
                padding: 12px 24px;
                border-radius: 0;
                font-size: 13px;
            }

            .nav-item-dropdown .dropdown-content {
                position: static !important;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.06);
                border-radius: 0;
                margin: 0;
                padding: 0;
                left: auto !important;
                right: auto !important;
            }

            .nav-item-dropdown .dropdown-content a {
                color: rgba(255, 255, 255, 0.85);
                padding: 10px 40px;
                font-size: 13px;
            }

            .nav-item-dropdown .dropdown-content a:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
            }

            .nav-item-dropdown .dropdown-content a i {
                color: rgba(255, 255, 255, 0.5);
            }
        }

        /* Small phones (360px - 480px) */
        @media (max-width: 480px) {
            .navbar {
                padding: 0 10px !important;
                height: 56px !important;
            }
            .navbar-brand i {
                font-size: 22px !important;
            }
            .navbar-brand span {
                font-size: 11px !important;
                line-height: 1.3;
                max-height: 14.3px;
                overflow: hidden;
                display: block;
                white-space: normal;
            }
            .navbar-user-btn {
                padding: 4px 8px 4px 4px !important;
                gap: 5px !important;
            }
            .navbar-user-btn span {
                font-size: 11px !important;
                max-width: 75px !important;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .user-avatar,
            .user-avatar-placeholder {
                width: 30px !important;
                height: 30px !important;
            }
            .navbar-toggler {
                width: 34px !important;
                height: 34px !important;
                font-size: 15px !important;
            }
            .nav-links-container.show-mobile {
                top: 56px !important;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        @if (auth()->check())
            <nav class="navbar">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('dashboard') }}" style="color: #fff !important;" class="navbar-brand">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>
                            نظام إدارة المطالبات المالية
                            <br>
                            Claims Management System
                        </span>
                    </a>
                </div>

                <div class="nav-links-container">
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
                                <i class="fa-solid fa-hand-holding-dollar"></i> الاستلامات المالية <i
                                    class="fa-solid fa-chevron-down" style="font-size: 10px; margin-right: 4px;"></i>
                            </button>
                            <div class="dropdown-content">
                                <a href="{{ route('financial-receipts.index') }}"><i
                                        class="fa-solid fa-hand-holding-dollar"></i>
                                    سجل الاستلامات المالية</a>
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

                <div style="display: flex; align-items: center; gap: 4px;">
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
                    <button class="navbar-toggler" id="mobileNavToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
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
                if ($(window).width() <= 1200 && $(this).parent().hasClass('nav-item-dropdown')) {
                    var $content = $(this).siblings('.dropdown-content');
                    $('.dropdown-content').not($content).slideUp(200); // close others
                    $content.slideToggle(200);
                }
            });

            // Desktop dropdown hover with delay
            if ($(window).width() > 1200) {
                $('.nav-item-dropdown, .navbar-user-dropdown').on('mouseenter', function () {
                    clearTimeout($(this).data('dropdownTimer'));
                    $(this).addClass('dropdown-open');
                }).on('mouseleave', function () {
                    var $self = $(this);
                    var timer = setTimeout(function () {
                        $self.removeClass('dropdown-open');
                    }, 200);
                    $self.data('dropdownTimer', timer);
                });
                // Keep open when hovering on the dropdown content itself
                $(document).on('mouseenter', '.dropdown-content', function () {
                    var $parent = $(this).closest('.nav-item-dropdown, .navbar-user-dropdown');
                    clearTimeout($parent.data('dropdownTimer'));
                }).on('mouseleave', '.dropdown-content', function () {
                    var $parent = $(this).closest('.nav-item-dropdown, .navbar-user-dropdown');
                    if ($parent.length) {
                        var timer = setTimeout(function () {
                            $parent.removeClass('dropdown-open');
                        }, 200);
                        $parent.data('dropdownTimer', timer);
                    }
                });
                // Close on click outside
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.nav-item-dropdown, .navbar-user-dropdown').length) {
                        $('.nav-item-dropdown, .navbar-user-dropdown').removeClass('dropdown-open');
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>