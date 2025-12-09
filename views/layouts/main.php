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
    <div class="container-fluid">
        <div class="row">
            <nav class="sidebar collapse d-md-block" id="sidebarMenu">
                <div class="sidebar-sticky">
                    <div class="px-3 mb-4 mt-2">
                         <!-- G-style Logo/Header -->
                         <div class="d-flex align-items-center">
                            <span class="fs-4 text-primary fw-bold">Machine MVP</span>
                            <!-- <span class="badge bg-primary text-white ms-2 rounded-pill">Admin</span> -->
                         </div>
                         <div class="text-muted small mt-1"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></div>
                    </div>

                    <ul class="nav flex-column nav-flex-column">
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
                        <div class="my-2 border-top"></div>
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

                        <li class="nav-item mt-5">
                             <a class="nav-link text-danger" href="<?= BASE_URL ?>/logout">
                                <span class="icon">🚪</span> Logout
                            </a>
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
