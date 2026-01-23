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
    <?php if (!empty($settings['company_logo'])): ?>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css?v=1.1" rel="stylesheet">
</head>
<body>
    <!-- Top Fixed Navbar -->
    <header class="navbar navbar-top fixed-top flex-md-nowrap p-0 shadow-sm bg-white">
        <!-- Mobile Toggle (Left aligned) -->
        <button class="navbar-toggler d-lg-none collapsed border-0" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation" style="margin-left: 10px;">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5 fw-bold d-flex align-items-center" href="<?= BASE_URL ?>/dashboard">
            <span class="text-primary me-2">
                <?php if (!empty($settings['company_logo'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>" alt="Company Logo" class="img-fluid" style="max-height: 32px;">
                <?php else: ?>
                    <span class="material-symbols-outlined icon">dashboard</span>
                    <?= e($settings['company_name'] ?? 'POS') ?>
                <?php endif; ?>
            </span>
        </a>
        
        <!-- Search Bar -->
        <div class="search-bar-container mx-2 flex-grow-1 d-flex align-items-center">
            <input class="form-control form-control-search w-100 me-2" type="text" placeholder="Search items in POS..." aria-label="Search" id="globalSearch">
            <div id="statusIndicators" class="d-flex gap-2">
                <span id="internetStatus" class="badge rounded-pill bg-success d-flex align-items-center" title="Internet Status">
                    <span class="material-symbols-outlined" style="font-size: 14px; margin-right: 4px;">wifi</span>
                    <span class="status-text d-none d-lg-inline">Online</span>
                </span>
                <span id="dbStatus" class="badge rounded-pill bg-success d-flex align-items-center" title="Database Status">
                    <span class="material-symbols-outlined" style="font-size: 14px; margin-right: 4px;">database</span>
                    <span class="status-text d-none d-lg-inline">DB Connected</span>
                </span>
            </div>
        </div>

        <!-- Right Side: User Profile -->
        <div class="navbar-nav flex-row align-items-center pe-3">
             <div class="nav-item text-nowrap d-flex align-items-center">
                 <span class="d-none d-md-inline-block me-3 text-secondary"><?= e($_SESSION['username'] ?? 'User') ?></span>
                 <a href="<?= BASE_URL ?>/profile" class="d-block link-dark text-decoration-none" title="My Profile">
                     <?php if (!empty($_SESSION['profile_image'])): ?>
                         <img src="<?= BASE_URL ?>/<?= htmlspecialchars($_SESSION['profile_image']) ?>" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                     <?php else: ?>
                         <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                             <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                         </div>
                     <?php endif; ?>
                 </a>
             </div>
        </div>
    </header>

    <div class="d-flex align-items-stretch w-100">
        <nav class="sidebar collapse d-lg-block" id="sidebarMenu">
            <div class="sidebar-sticky">
                    <!-- Removed Branding from here -->
                    <ul class="nav flex-column nav-flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == BASE_URL . '/dashboard') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/dashboard">
                                <span class="material-symbols-outlined icon">dashboard</span> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/sales') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/sales/create">
                                <span class="material-symbols-outlined icon">point_of_sale</span> Sales / POS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/items') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/items">
                                <span class="material-symbols-outlined icon">inventory_2</span> Items / Stock
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/customers') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/customers">
                                <span class="material-symbols-outlined icon">group</span> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/debtors') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/debtors">
                                <span class="material-symbols-outlined icon">person_search</span> Debt System
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/expenditures') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/expenditures">
                                <span class="material-symbols-outlined icon">payments</span> Expenditures
                            </a>
                        </li>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <div class="my-2 border-top mx-3"></div>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/users') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/users">
                                <span class="material-symbols-outlined icon">manage_accounts</span> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/reports') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/reports">
                                <span class="material-symbols-outlined icon">bar_chart</span> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/admin/trash') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/trash">
                                <span class="material-symbols-outlined icon">delete</span> Recycle Bin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/settings') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/settings">
                                <span class="material-symbols-outlined icon">admin_panel_settings</span> Company Settings
                            </a>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-4 mt-4 mb-2 text-muted text-uppercase" style="font-size: 0.75rem;">
                                <span>Account</span>
                            </h6>
                            <ul class="nav flex-column mb-2">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2 <?php echo (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/profile">
                                        <span class="material-symbols-outlined icon">settings</span>
                                        Settings
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2 text-danger" href="<?= BASE_URL ?>/logout">
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
    <script>
        // System Status Polling
        function updateStatus() {
            // Internet Status
            const internetStatus = document.getElementById('internetStatus');
            if (navigator.onLine) {
                internetStatus.classList.replace('bg-danger', 'bg-success');
                internetStatus.querySelector('.status-text').innerText = 'Online';
                internetStatus.querySelector('.material-symbols-outlined').innerText = 'wifi';
            } else {
                internetStatus.classList.replace('bg-success', 'bg-danger');
                internetStatus.querySelector('.status-text').innerText = 'Offline';
                internetStatus.querySelector('.material-symbols-outlined').innerText = 'wifi_off';
            }

            // Database Status
            fetch('<?= BASE_URL ?>/api/status')
                .then(res => res.json())
                .then(data => {
                    const dbStatus = document.getElementById('dbStatus');
                    if (data.database) {
                        dbStatus.classList.replace('bg-danger', 'bg-success');
                        dbStatus.querySelector('.status-text').innerText = 'DB Connected';
                    } else {
                        dbStatus.classList.replace('bg-success', 'bg-danger');
                        dbStatus.querySelector('.status-text').innerText = 'DB Disconnected';
                    }
                })
                .catch(err => {
                    const dbStatus = document.getElementById('dbStatus');
                    dbStatus.classList.replace('bg-success', 'bg-danger');
                    dbStatus.querySelector('.status-text').innerText = 'Server Error';
                });
        }

        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        setInterval(updateStatus, 30000); // Check every 30 seconds
        updateStatus(); // Initial check
    </script>
</body>
</html>
