<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - <?= e($settings['company_name'] ?? 'Machine MVP') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <?php if (!empty($settings['company_logo'])): ?>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/<?= e($settings['company_logo']) ?>">
    <?php endif; ?>
</head>
<body class="bg-light" style="padding-top: 0px;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="text-center mb-4">
                <?php if (!empty($settings['company_logo'])): ?>
                    <img src="<?= BASE_URL ?>/<?= e($settings['company_logo']) ?>" alt="Logo" style="max-height: 80px; margin-bottom: 15px;">
                <?php else: ?>
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="48px" height="48px" viewBox="0 0 48 48" class="mb-2">
                        <g>
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                            <path fill="none" d="M0 0h48v48H0z"></path>
                        </g>
                    </svg>
                <?php endif; ?>
                <h2 class="auth-title">Sign in</h2>
                <p class="auth-subtitle mb-4">to continue to Machine MVP</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size: 14px; border-radius: 4px;"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="usernameInput" name="username" placeholder="Username" required autofocus autocomplete="off">
                    <label for="usernameInput">Username</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password" required autocomplete="new-password">
                    <label for="passwordInput">Password</label>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="#" class="text-decoration-none text-primary fw-medium" style="font-size: 14px; opacity: 0; pointer-events: none;">Forgot password?</a> 
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-medium">Next</button>
                </div>
            </form>
        </div>
        
        <!-- <div class="auth-footer mt-3 d-flex justify-content-between" style="max-width: 450px; font-size: 12px;">
             <div class="text-muted">English (United States)</div>
             <div class="text-muted">
                 <span class="ms-3 cursor-pointer">Help</span>
                 <span class="ms-3 cursor-pointer">Privacy</span>
                 <span class="ms-3 cursor-pointer">Terms</span>
             </div>
        </div> -->
    </div>
</body>
</html>
