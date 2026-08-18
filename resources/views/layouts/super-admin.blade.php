@php($surface = 'super-admin')
@php($surfaceLabel = 'Super Admin')
<!doctype html>
<html lang='id' data-theme='light'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <meta name='csrf-token' content='{{ csrf_token() }}'>
    <title>@yield('title', 'Super Admin Dashboard') · Jelajah Tegal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Scoped Super Admin Theme Styles */
        .superadmin-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (max-width: 992px) {
            .superadmin-grid {
                grid-template-columns: 1fr;
            }
        }
        .superadmin-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 130px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .superadmin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
        }
        .superadmin-icon-box {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            line-height: 1 !important;
            flex-shrink: 0;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .superadmin-icon-box i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1 !important;
            font-size: 18px !important;
        }
        .superadmin-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }
        .superadmin-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 13px 16px;
            white-space: nowrap;
        }
        .superadmin-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .superadmin-table tr:hover td {
            background-color: #f8fafc;
        }
        .superadmin-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
        }
    </style>
</head>
<body class='dashboard-body' data-surface='{{ $surface }}'>
    <div class='mobile-backdrop' data-sidebar-close></div>
    <div class='dashboard-shell' data-dashboard-shell>
        <x-sidebar :surface='$surface' />
        <div class='dashboard-main'>
            <x-topbar :surface='$surface' />
            <main class='dashboard-content' id='main-content'>
                @php($breadcrumbItems = $breadcrumbs ?? ['Dashboard' => null])
                <x-breadcrumb :items='$breadcrumbItems' />
                <div class='page-heading'>
                    <div>
                        <p class='page-eyebrow'>{{ $surfaceLabel ?? str($surface)->headline() }}</p>
                        <h1>@yield('page-title', 'Dashboard')</h1>
                        <p class='page-description'>@yield('page-description', 'Ringkasan aktivitas dan status terbaru Lokantara.')</p>
                    </div>
                    <div class='page-actions'>@yield('page-actions')</div>
                </div>
                @yield('content')
            </main>
        </div>
    </div>
    <x-toast />
    <x-confirm-modal />
    @stack('modals')@stack('scripts')@livewireScripts
</body>
</html>