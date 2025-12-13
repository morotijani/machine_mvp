<?php
$title = "Sales History";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Sales History</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/sales/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                    <span class="fs-5">+</span> New Sale
                </a>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="card shadow-sm mb-3">
            <div class="card-body py-3">
                <form action="<?= BASE_URL ?>/sales" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search Invoice # or Customer" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" placeholder="Start Date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" placeholder="End Date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="partial" <?php echo $filters['status'] === 'partial' ? 'selected' : ''; ?>>Partial</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
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
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($sale['created_at'])); ?></td>
                                <td><a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="fw-bold text-decoration-none">#<?php echo $sale['id']; ?></a></td>
                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? ''); ?></td>
                                <td><span class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo ucfirst($sale['payment_status']); ?></span></td>
                                <td class="text-end fw-bold">₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td class="text-end text-success">₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                                <td class="text-end text-danger"><?php echo ($balance > 0) ? '₵'.number_format($balance, 2) : '-'; ?></td>
                                <td><small><?php echo htmlspecialchars($sale['seller_name']); ?></small></td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span> View
                                    </a>
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

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <?php $queryParams['page'] = $i; ?>
                                <a class="page-link" href="<?= BASE_URL ?>/sales?<?php echo http_build_query($queryParams); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

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
