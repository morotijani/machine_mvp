<?php
if (!isset($settings)) {
    $pdo = \App\Config\Database::getInstance();
    $settingModel = new \App\Models\Setting($pdo);
    $settings = $settingModel->get();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['company_name'] ?? ($title ?? 'Machine MVP')) ?></title>
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <!-- Theme Color for mobile status bar -->
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/icon.svg">

    <?php if (!empty($settings['company_logo'])): ?>
        <link rel="icon" type="image/png" href="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css?v=1.1" rel="stylesheet">
    <style>
        @media (min-width: 768px) {

            .nav-side-left,
            .nav-side-right {
                flex: 1 1 0% !important;
            }
        }
    </style>
</head>

<body>
    <style>
        @media (max-width: 767px) {
            body {
                padding-top: 110px !important;
            }

            .sidebar {
                top: 110px !important;
            }
        }
    </style>
    <style>
        /* Google Header */
        .google-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e3e3;
            min-height: 64px;
            /* Standard Google header height */
            padding: 8px 0;
        }

        /* Google Search Bar */
        .google-search-container {
            background-color: #f1f3f4;
            border-radius: 24px;
            height: 48px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            transition: background-color 0.2s, box-shadow 0.2s;
            max-width: 720px;
            width: 100%;
            margin: 0 auto;
        }

        .google-search-container:focus-within {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .google-search-input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            padding: 8px 16px;
            font-size: 16px;
            color: #1f1f1f;
        }

        .google-search-icon {
            color: #5f6368;
            font-size: 24px;
        }
    </style>
    <!-- Top Fixed Navbar -->
    <header class="navbar fixed-top p-0 google-header no-print flex-wrap flex-md-nowrap align-items-center">
        <!-- Top Row on Mobile: Toggler + Brand + Profile -->
        <div
            class="d-flex nav-side-left w-100 w-md-auto align-items-center justify-content-between px-2 px-md-0 order-1">
            <div class="d-flex align-items-center">
                <!-- Mobile Toggle -->
                <button class="navbar-toggler d-lg-none collapsed border-0 p-1 me-2 ms-2" type="button"
                    data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="material-symbols-outlined" style="font-size: 24px; color: #5f6368;">menu</span>
                </button>

                <a class="navbar-brand me-0 px-md-4 fs-5 fw-bold d-flex align-items-center"
                    href="<?= BASE_URL ?>/dashboard">
                    <span class="text-primary me-2 d-flex align-items-center">
                        <?php if (!empty($settings['company_logo'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>" alt="Company Logo"
                                class="img-fluid" style="max-height: 32px; border-radius: 4px;">
                        <?php else: ?>
                            <span class="material-symbols-outlined icon me-2" style="font-size: 28px;">storefront</span>
                        <?php endif; ?>
                    </span>
                    <span
                        style="color: #444746; font-size: 22px; font-weight: 400; letter-spacing: -0.5px;"><?= e($settings['company_name'] ?? 'POS') ?></span>
                </a>
            </div>

            <!-- Mobile Profile -->
            <div class="d-md-none d-flex align-items-center pe-3">
                <a href="<?= BASE_URL ?>/profile" class="d-block link-dark text-decoration-none" title="My Profile">
                    <?php if (!empty($_SESSION['profile_image'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($_SESSION['profile_image']) ?>"
                            class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px; font-weight: 500; background-color: #0b57d0; color: white;">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mx-3 mx-md-auto d-flex justify-content-center align-items-center w-100 mt-2 mt-md-0 mb-2 mb-md-0 order-3 order-md-2"
            style="max-width: 720px;">
            <div class="google-search-container w-100 me-2">
                <span class="material-symbols-outlined google-search-icon">search</span>
                <input class="google-search-input" type="text" placeholder="Search items in POS..." aria-label="Search"
                    id="globalSearch">
            </div>

            <div id="statusIndicators" class="d-flex gap-2 ms-2">
                <span id="internetStatus" class="badge rounded-pill bg-success d-flex align-items-center"
                    title="Internet Status" style="padding: 6px 10px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">wifi</span>
                </span>
                <span id="dbStatus" class="badge rounded-pill bg-success d-flex align-items-center"
                    title="Database Status" style="padding: 6px 10px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">database</span>
                </span>
            </div>
        </div>

        <!-- Right Side: User Profile (Desktop) -->
        <div
            class="navbar-nav nav-side-right flex-row align-items-center justify-content-end pe-4 d-none d-md-flex order-2 order-md-3">
            <div class="nav-item text-nowrap d-flex align-items-center">
                <a href="<?= BASE_URL ?>/profile" class="d-block link-dark text-decoration-none" title="My Profile">
                    <?php if (!empty($_SESSION['profile_image'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($_SESSION['profile_image']) ?>"
                            class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-weight: 500; font-size: 18px; background-color: #0b57d0; color: white;">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <div class="d-flex align-items-stretch w-100">
        <nav class="sidebar collapse d-lg-block no-print" id="sidebarMenu">
            <div class="sidebar-sticky">
                <!-- Removed Branding from here -->
                <ul class="nav flex-column nav-flex-column mt-3">
                    <?php if (in_array($_SESSION['role'], ['admin', 'sales_cashier'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == BASE_URL . '/dashboard') ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/dashboard">
                                <span class="material-symbols-outlined icon">dashboard</span> Dashboard
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['role'], ['admin', 'sales', 'sales_cashier'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/sales') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/sales/create">
                                <span class="material-symbols-outlined icon">point_of_sale</span> Sales / POS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/proformas') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/proformas/create">
                                <span class="material-symbols-outlined icon">receipt_long</span> Pro Forma Invoices
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/items') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/items">
                                <span class="material-symbols-outlined icon">inventory_2</span> Items / Stock
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/customers') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/customers">
                                <span class="material-symbols-outlined icon">group</span> Customers
                            </a>
                        </li>
                        <?php if (!isset($settings['enable_debt_module']) || $settings['enable_debt_module'] == 1): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/debtors') !== false) ? 'active' : ''; ?>"
                                    href="<?= BASE_URL ?>/debtors">
                                    <span class="material-symbols-outlined icon">person_search</span> Debt System
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['role'], ['admin', 'cashier', 'sales_cashier'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/cashier') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/cashier"
                                style="background-color: <?= (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/cashier') !== false) ? '' : '#f8f9fa' ?>; border-left: 3px solid #0d6efd;">
                                <span class="material-symbols-outlined icon text-primary">point_of_sale</span> Live Cashier
                                Desk
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['role'], ['admin', 'cashier', 'sales_cashier'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/expenditures') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/expenditures">
                                <span class="material-symbols-outlined icon">payments</span> Expenditures
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="my-2 border-top mx-3"></div>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/admin/finance') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/admin/finance">
                                <span class="material-symbols-outlined icon">account_balance</span> Finance & Coffers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/users') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/users">
                                <span class="material-symbols-outlined icon">manage_accounts</span> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/reports') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/reports">
                                <span class="material-symbols-outlined icon">bar_chart</span> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/admin/staff') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/admin/staff">
                                <span class="material-symbols-outlined icon">monitoring</span> Staff Performance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/admin/trash') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/admin/trash">
                                <span class="material-symbols-outlined icon">delete</span> Recycle Bin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/settings') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/settings">
                                <span class="material-symbols-outlined icon">admin_panel_settings</span> Company Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/sync') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/sync">
                                <span class="material-symbols-outlined icon">cloud_sync</span> Cloud Sync & Backup
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/docs') !== false) ? 'active' : ''; ?>"
                            href="<?= BASE_URL ?>/docs">
                            <span class="material-symbols-outlined icon">help</span> System Guide
                        </a>
                    </li>

                    <li class="nav-item mt-3">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-4 mt-4 mb-2 text-muted text-uppercase"
                            style="font-size: 0.75rem;">
                            <span>Account</span>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 <?php echo (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) ? 'active' : ''; ?>"
                                    href="<?= BASE_URL ?>/profile">
                                    <span class="material-symbols-outlined icon">settings</span>
                                    Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 text-danger"
                                    href="<?= BASE_URL ?>/logout">
                                    <span class="material-symbols-outlined icon">logout</span>
                                    Sign out
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="flex-grow-1 px-md-4">
            <?php echo $content; ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // Initialize all Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
    <script>
        // System Status Polling
        function updateStatus() {
            // Internet Status
            const internetStatus = document.getElementById('internetStatus');
            if (internetStatus) {
                if (navigator.onLine) {
                    internetStatus.classList.replace('bg-danger', 'bg-success');
                    const stText = internetStatus.querySelector('.status-text');
                    if (stText) stText.innerText = 'Online';
                    internetStatus.querySelector('.material-symbols-outlined').innerText = 'wifi';
                } else {
                    internetStatus.classList.replace('bg-success', 'bg-danger');
                    const stText = internetStatus.querySelector('.status-text');
                    if (stText) stText.innerText = 'Offline';
                    internetStatus.querySelector('.material-symbols-outlined').innerText = 'wifi_off';
                }
            }

            // Database Status
            fetch('<?= BASE_URL ?>/api/status')
                .then(res => res.json())
                .then(data => {
                    const dbStatus = document.getElementById('dbStatus');
                    if (dbStatus) {
                        if (data.database) {
                            dbStatus.classList.replace('bg-danger', 'bg-success');
                            const dbText = dbStatus.querySelector('.status-text');
                            if (dbText) dbText.innerText = 'DB Connected';
                        } else {
                            dbStatus.classList.replace('bg-success', 'bg-danger');
                            const dbText = dbStatus.querySelector('.status-text');
                            if (dbText) dbText.innerText = 'DB Disconnected';
                        }
                    }
                })
                .catch(err => {
                    const dbStatus = document.getElementById('dbStatus');
                    if (dbStatus) {
                        dbStatus.classList.replace('bg-success', 'bg-danger');
                        const dbText = dbStatus.querySelector('.status-text');
                        if (dbText) dbText.innerText = 'Server Error';
                    }
                });
        }

        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        setInterval(updateStatus, 30000); // Check every 30 seconds
        updateStatus(); // Initial check

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= BASE_URL ?>/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
</body>

</html>