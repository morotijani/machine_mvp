<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Machine MVP'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Top Fixed Navbar -->
    <header class="navbar navbar-top fixed-top flex-md-nowrap p-0">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5 fw-bold d-flex align-items-center" href="<?= BASE_URL ?>/dashboard">
            <span class="text-primary me-2">Machine MVP</span>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation" style="right: 10px; top: 15px;">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Search Bar -->
        <div class="search-bar-container d-none d-md-block mx-auto">
            <input class="form-control form-control-search w-100" type="text" placeholder="Search items in POS..." aria-label="Search" id="globalSearch">
        </div>

        <!-- Right Side: User Profile -->
        <div class="navbar-nav flex-row align-items-center pe-3">
             <div class="nav-item text-nowrap d-flex align-items-center">
                 <span class="d-none d-md-inline-block me-3 text-secondary"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                 <a href="<?= BASE_URL ?>/profile" class="d-block link-dark text-decoration-none" title="My Profile">
                     <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                         <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                     </div>
                 </a>
             </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav class="sidebar collapse d-md-block" id="sidebarMenu">
                <div class="sidebar-sticky">
                    <!-- Removed Branding from here -->
                    <ul class="nav flex-column nav-flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == BASE_URL . '/dashboard') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/dashboard">
                                <span class="icon">📊</span> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/sales') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/sales/create">
                                <span class="icon">🛒</span> Sales / POS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/items') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/items">
                                <span class="icon">📦</span> Items / Stock
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/customers') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/customers">
                                <span class="icon">👥</span> Customers
                            </a>
                        </li>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <div class="my-2 border-top mx-3"></div>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/users') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/users">
                                <span class="icon">👤</span> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/reports') !== false) ? 'active' : ''; ?>" href="<?= BASE_URL ?>/reports">
                                <span class="icon">📈</span> Reports
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
                                        <span class="icon">⚙️</span>
                                        Settings
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2 text-danger" href="<?= BASE_URL ?>/logout">
                                        <span class="icon">🚪</span>
                                        Sign out
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php echo $content; ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
