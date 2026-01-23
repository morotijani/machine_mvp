<?php
$title = "Sales History";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Sales History</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/sales/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                    <span class="fs-5">+</span> New Sale
                </a>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="card shadow-sm mb-3" style="overflow-x: auto;">
            <div class="card-body py-3">
                <form action="<?= BASE_URL ?>/sales" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search Invoice # or Customer" value="<?= e($filters['search']) ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" placeholder="Start Date" value="<?= e($filters['start_date']) ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" placeholder="End Date" value="<?= e($filters['end_date']) ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="partial" <?php echo $filters['status'] === 'partial' ? 'selected' : ''; ?>>Partial</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="show_voided" class="form-select">
                            <option value="no" <?php echo $filters['show_voided'] === 'no' ? 'selected' : ''; ?>>Active Sales</option>
                            <option value="yes" <?php echo $filters['show_voided'] === 'yes' ? 'selected' : ''; ?>>Voided Sales</option>
                            <option value="all" <?php echo $filters['show_voided'] === 'all' ? 'selected' : ''; ?>>All Records</option>
                        </select>
                    </div>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div class="col-md-2">
                         <select name="delete_request" class="form-select">
                            <option value="" <?php echo $filters['delete_request'] === '' ? 'selected' : ''; ?>>All Requests</option>
                            <option value="pending" <?php echo $filters['delete_request'] === 'pending' ? 'selected' : ''; ?>>Pending Deletes</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 18px;">filter_list</span> Filter
                        </button>
                        <a href="<?= BASE_URL ?>/sales" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 18px;">restart_alt</span> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
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
                                $statusClass = match($sale['payment_status']) {
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning text-dark',
                                    'unpaid' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <tr class="<?php echo $sale['voided'] ? 'table-light opacity-75' : ''; ?>">
                                <td><?php echo date('M j, Y H:i', strtotime($sale['created_at'])); ?></td>
                                <td><a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" class="fw-bold text-decoration-none">#<?= $sale['id'] ?></a></td>
                                <td><?= e($sale['customer_name'] ?? '') ?></td>
                                <td><span class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo ucfirst($sale['payment_status']); ?></span></td>
                                <td class="text-end fw-bold">₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td class="text-end text-success">₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                                <td class="text-end text-danger"><?php echo ($balance > 0) ? '₵'.number_format($balance, 2) : '-'; ?></td>
                                <td><small><?= e($sale['seller_name']) ?></small></td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span> View
                                    </a>
                                    
                                    <?php if ($sale['voided']): ?>
                                        <span class="badge bg-dark">Voided</span>
                                    <?php elseif ($sale['delete_request_status'] === 'pending'): ?>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <form action="<?= BASE_URL ?>/sales/process-delete" method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-outline-danger" title="Approve Delete">
                                                    <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                                                </button>
                                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-success" title="Reject">
                                                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Deletion Pending</span>
                                        <?php endif; ?>
                                    <?php elseif ($sale['user_id'] == $_SESSION['user_id'] && $sale['delete_request_status'] == 'none'): ?>
                                        <form action="<?= BASE_URL ?>/sales/request-delete" method="POST" style="display:inline;" onsubmit="return confirm('Request to delete this sale?');">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Request Delete">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No sales found matching your criteria.</td>
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
                            <?php $queryParams['page'] = $page - 1; ?>
                            <a class="page-link" href="<?= BASE_URL ?>/sales?<?php echo http_build_query($queryParams); ?>">Previous</a>
                        </li>

                        <!-- Page Numbers (Smart) -->
                        <?php 
                        $range = 2; // Number of pages around current page
                        // Always show first page
                        if ($page > 1 + $range) {
                            $queryParams['page'] = 1;
                            echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . '/sales?' . http_build_query($queryParams) . '">1</a></li>';
                            if ($page > 2 + $range) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }

                        // Range around current
                        for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++) {
                            $queryParams['page'] = $i;
                            $active = ($page == $i) ? 'active' : '';
                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . BASE_URL . '/sales?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                        }

                        // Always show last page
                        if ($page < $totalPages - $range) {
                            if ($page < $totalPages - $range - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            $queryParams['page'] = $totalPages;
                            echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . '/sales?' . http_build_query($queryParams) . '">' . $totalPages . '</a></li>';
                        }
                        ?>

                        <!-- Next -->
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <?php $queryParams['page'] = $page + 1; ?>
                            <a class="page-link" href="<?= BASE_URL ?>/sales?<?php echo http_build_query($queryParams); ?>">Next</a>
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
