<?php
$title = "Sales History";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 gap-2">
            <h1 class="h2 mb-0">Sales History</h1>
            <div class="d-flex flex-wrap gap-2 page-header-actions">
                <a href="<?= BASE_URL ?>/sales/create"
                    class="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 px-3 fw-medium shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span> New Sale
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <span class="material-symbols-outlined align-middle me-2">check_circle</span>
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <span class="material-symbols-outlined align-middle me-2">error</span>
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Form -->
        <div class="bg-white p-3 rounded-4 shadow-sm mb-4">
            <form action="<?= BASE_URL ?>/sales" method="GET" class="row g-2 align-items-center">
                <!-- Group 1: Search & Dates -->
                <div class="col-12 col-xl-5">
                    <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 pe-1">
                            <span class="material-symbols-outlined text-muted" style="font-size: 18px;">search</span>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-1"
                            placeholder="Invoice # or Customer" value="<?= e($filters['search']) ?>"
                            style="box-shadow: none;">
                        <span class="input-group-text bg-light border-0 border-start border-end px-2"
                            style="font-size: 12px;">From</span>
                        <input type="date" name="start_date" class="form-control border-0 px-1"
                            value="<?= e($filters['start_date']) ?>" style="box-shadow: none; max-width: 110px;">
                        <span class="input-group-text bg-light border-0 border-start border-end px-2"
                            style="font-size: 12px;">To</span>
                        <input type="date" name="end_date" class="form-control border-0 px-1"
                            value="<?= e($filters['end_date']) ?>" style="box-shadow: none; max-width: 110px;">
                    </div>
                </div>

                <!-- Group 2: Status & Display Filters -->
                <div class="col-12 col-xl-7 d-flex flex-wrap flex-md-nowrap gap-2">
                    <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border flex-grow-1">
                        <span class="input-group-text bg-white border-0 fw-bold text-muted px-2 px-md-3">Status:</span>
                        <select name="status" class="form-select border-0 ps-0" onchange="this.form.submit()"
                            style="box-shadow: none;">
                            <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid
                            </option>
                            <option value="partial" <?php echo $filters['status'] === 'partial' ? 'selected' : ''; ?>>
                                Partial</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid
                            </option>
                        </select>

                        <span
                            class="input-group-text bg-white border-0 border-start fw-bold text-muted px-2 px-md-3">Display:</span>
                        <select name="show_voided" class="form-select border-0 ps-0" onchange="this.form.submit()"
                            style="box-shadow: none;">
                            <option value="no" <?php echo $filters['show_voided'] === 'no' ? 'selected' : ''; ?>>Active
                            </option>
                            <option value="yes" <?php echo $filters['show_voided'] === 'yes' ? 'selected' : ''; ?>>Voided
                            </option>
                            <option value="all" <?php echo $filters['show_voided'] === 'all' ? 'selected' : ''; ?>>All
                            </option>
                        </select>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <span
                                class="input-group-text bg-white border-0 border-start fw-bold text-muted px-2 px-md-3">Req:</span>
                            <select name="delete_request" class="form-select border-0 ps-0" onchange="this.form.submit()"
                                style="box-shadow: none;">
                                <option value="" <?php echo $filters['delete_request'] === '' ? 'selected' : ''; ?>>All
                                </option>
                                <option value="pending" <?php echo $filters['delete_request'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 flex-shrink-0">
                        <button type="submit"
                            class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1 fw-medium">
                            <span class="material-symbols-outlined" style="font-size: 16px;">filter_list</span> Filter
                        </button>
                        <a href="<?= BASE_URL ?>/sales"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 16px;">restart_alt</span> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Salesperson</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                                <?php
                                $balance = $sale['total_amount'] - $sale['paid_amount'];
                                $statusClass = match ($sale['payment_status']) {
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning text-dark',
                                    'unpaid' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <tr class="<?php echo $sale['voided'] ? 'table-light opacity-75' : ''; ?>">
                                    <td><?php echo date('M j, Y H:i', strtotime($sale['created_at'])); ?></td>
                                    <td><a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>"
                                            class="fw-bold text-decoration-none">#<?= $sale['id'] ?></a></td>
                                    <td><?= e($sale['customer_name'] ?? '') ?></td>
                                    <td><span
                                            class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo ucfirst($sale['payment_status']); ?></span>
                                    </td>
                                    <td class="text-end fw-bold">₵<?php echo number_format($sale['total_amount'], 2); ?>
                                    </td>
                                    <td class="text-end text-success">₵<?php echo number_format($sale['paid_amount'], 2); ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        <?php echo ($balance > 0) ? '₵' . number_format($balance, 2) : '-'; ?>
                                    </td>
                                    <td><small><?= e($sale['seller_name']) ?></small></td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>"
                                            class="btn btn-sm btn-outline-secondary">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 16px;">visibility</span> View
                                        </a>

                                        <?php if ($sale['voided']): ?>
                                            <span class="badge bg-dark">Voided</span>
                                        <?php elseif ($sale['delete_request_status'] === 'pending'): ?>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                                <form action="<?= BASE_URL ?>/sales/process-delete" method="POST"
                                                    style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                                    <button type="submit" name="action" value="approve"
                                                        class="btn btn-sm btn-outline-danger" title="Approve Delete">
                                                        <span class="material-symbols-outlined"
                                                            style="font-size: 16px;">check</span>
                                                    </button>
                                                    <button type="submit" name="action" value="reject"
                                                        class="btn btn-sm btn-outline-success" title="Reject">
                                                        <span class="material-symbols-outlined"
                                                            style="font-size: 16px;">close</span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Deletion Pending</span>
                                            <?php endif; ?>
                                        <?php elseif ($sale['user_id'] == $_SESSION['user_id'] && $sale['delete_request_status'] == 'none'): ?>
                                            <form action="<?= BASE_URL ?>/sales/request-delete" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Request to delete this sale?');">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Request Delete">
                                                    <span class="material-symbols-outlined"
                                                        style="font-size: 16px;">delete</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No sales found matching your
                                        criteria.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php
                            // Helper to build query string
                            $queryParams = $_GET;
                            ?>

                            <!-- Previous -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <?php
                                $prevParams = $queryParams;
                                $prevParams['page'] = $page - 1;
                                ?>
                                <a class="page-link"
                                    href="<?= BASE_URL ?>/sales?<?php echo http_build_query($prevParams); ?>">Previous</a>
                            </li>

                            <!-- Page Numbers (Smart) -->
                            <?php
                            $range = 2; // Number of pages around current page
                            // Always show first page
                            if ($page > 1 + $range) {
                                $firstParams = $queryParams;
                                $firstParams['page'] = 1;
                                echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . '/sales?' . http_build_query($firstParams) . '">1</a></li>';
                                if ($page > 2 + $range) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            // Range around current
                            for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++) {
                                $pageParams = $queryParams;
                                $pageParams['page'] = $i;
                                $active = ($page == $i) ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . BASE_URL . '/sales?' . http_build_query($pageParams) . '">' . $i . '</a></li>';
                            }

                            // Always show last page
                            if ($page < $totalPages - $range) {
                                if ($page < $totalPages - $range - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                $lastParams = $queryParams;
                                $lastParams['page'] = $totalPages;
                                echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . '/sales?' . http_build_query($lastParams) . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <!-- Next -->
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <?php
                                $nextParams = $queryParams;
                                $nextParams['page'] = $page + 1;
                                ?>
                                <a class="page-link"
                                    href="<?= BASE_URL ?>/sales?<?php echo http_build_query($nextParams); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>