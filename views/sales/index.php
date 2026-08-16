<?php
$title = "Sales History";
ob_start();
?>

<style>
    .google-table-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .google-btn-secondary {
        background: transparent;
        border: 1px solid #dadce0;
        color: #1f1f1f;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .google-btn-secondary:hover {
        background: #f8f9fa;
        color: #1f1f1f;
    }
    .google-btn-primary {
        background: #0b57d0;
        border: none;
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .google-btn-primary:hover {
        background: #0842a0;
        color: #fff;
    }
    .google-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        letter-spacing: 0.5px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-subtle-success { background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
    .badge-subtle-danger { background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; }
    .badge-subtle-primary { background-color: #e8f0fe; color: #1967d2; border: 1px solid #d2e3fc; }
    .badge-subtle-warning { background-color: #fef7e0; color: #b06000; border: 1px solid #fce8b2; }
    
    .table-custom tbody td {
        padding: 16px 24px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }
    .table-custom th {
        padding: 12px 24px;
        font-size: 12px;
        text-transform: uppercase;
        color: #5f6368;
        font-weight: 600;
        border-bottom: 1px solid #e3e3e3;
    }
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }
    .google-input {
        background: #f1f3f4;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 8px 12px;
        color: #1f1f1f;
        font-size: 14px;
        transition: all 0.2s ease;
        box-shadow: none;
    }
    .google-input:focus {
        background: #fff;
        border-color: #0b57d0;
        box-shadow: inset 0 0 0 1px #0b57d0;
        outline: none;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-4 gap-2">
            <h1 class="h3 mb-0 fw-normal" style="color: #1f1f1f;">Sales History</h1>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= BASE_URL ?>/sales/create" class="google-btn-primary gap-1">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span> New Sale
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #e6f4ea; color: #137333;">
                <span class="material-symbols-outlined align-middle me-2">check_circle</span>
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #fce8e6; color: #c5221f;">
                <span class="material-symbols-outlined align-middle me-2">error</span>
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Form -->
        <div class="google-table-card p-3 mb-4" style="border-radius: 16px;">
            <form action="<?= BASE_URL ?>/sales" method="GET" class="row g-3 align-items-center">
                <!-- Group 1: Search & Dates -->
                <div class="col-12 col-xl-5">
                    <div class="d-flex align-items-center bg-light rounded-pill overflow-hidden border px-3">
                        <span class="material-symbols-outlined text-muted" style="font-size: 18px;">search</span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none"
                            placeholder="Invoice # or Customer" value="<?= e($filters['search']) ?>">
                        
                        <div class="d-flex align-items-center gap-1 border-start ps-3 ms-2">
                            <span class="text-muted" style="font-size: 12px;">From</span>
                            <input type="date" name="start_date" class="form-control border-0 bg-transparent shadow-none px-1"
                                value="<?= e($filters['start_date']) ?>" style="width: 105px; font-size: 13px;">
                            <span class="text-muted" style="font-size: 12px;">To</span>
                            <input type="date" name="end_date" class="form-control border-0 bg-transparent shadow-none px-1"
                                value="<?= e($filters['end_date']) ?>" style="width: 105px; font-size: 13px;">
                        </div>
                    </div>
                </div>

                <!-- Group 2: Status & Display Filters -->
                <div class="col-12 col-xl-7 d-flex flex-wrap flex-md-nowrap gap-3">
                    <div class="d-flex align-items-center flex-grow-1 bg-light rounded-pill border px-3">
                        <span class="fw-medium text-muted me-2" style="font-size: 13px;">Status:</span>
                        <select name="status" class="form-select border-0 bg-transparent shadow-none pe-4" onchange="this.form.submit()" style="font-size: 14px;">
                            <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="partial" <?php echo $filters['status'] === 'partial' ? 'selected' : ''; ?>>Partial</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>

                        <div class="border-start ms-2 ps-3 d-flex align-items-center">
                            <span class="fw-medium text-muted me-2" style="font-size: 13px;">Show:</span>
                            <select name="show_voided" class="form-select border-0 bg-transparent shadow-none pe-4" onchange="this.form.submit()" style="font-size: 14px;">
                                <option value="no" <?php echo $filters['show_voided'] === 'no' ? 'selected' : ''; ?>>Active</option>
                                <option value="yes" <?php echo $filters['show_voided'] === 'yes' ? 'selected' : ''; ?>>Voided</option>
                                <option value="all" <?php echo $filters['show_voided'] === 'all' ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <div class="border-start ms-2 ps-3 d-flex align-items-center">
                                <span class="fw-medium text-muted me-2" style="font-size: 13px;">Req:</span>
                                <select name="delete_request" class="form-select border-0 bg-transparent shadow-none pe-4" onchange="this.form.submit()" style="font-size: 14px;">
                                    <option value="" <?php echo $filters['delete_request'] === '' ? 'selected' : ''; ?>>All</option>
                                    <option value="pending" <?php echo $filters['delete_request'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 flex-shrink-0">
                        <button type="submit" class="google-btn-primary gap-1">
                            <span class="material-symbols-outlined" style="font-size: 16px;">filter_list</span> Filter
                        </button>
                        <a href="<?= BASE_URL ?>/sales" class="google-btn-secondary gap-1">
                            <span class="material-symbols-outlined" style="font-size: 16px;">restart_alt</span> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless table-custom mb-0">
                    <thead>
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
                                'paid' => 'badge-subtle-success',
                                'partial' => 'badge-subtle-warning',
                                'unpaid' => 'badge-subtle-danger',
                                default => 'badge-subtle-primary'
                            };
                            ?>
                            <tr class="<?php echo $sale['voided'] ? 'opacity-50 bg-light' : ''; ?>">
                                <td>
                                    <div class="fw-medium text-dark"><?php echo date('M j, Y', strtotime($sale['created_at'])); ?></div>
                                    <div class="text-muted" style="font-size: 12px;"><?php echo date('H:i', strtotime($sale['created_at'])); ?></div>
                                </td>
                                <td><a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" class="fw-medium text-decoration-none" style="color: #0b57d0;">#<?= htmlspecialchars($sale['invoice_number'] ?? $sale['id']) ?></a></td>
                                <td class="fw-medium text-dark"><?= e($sale['customer_name'] ?? 'Walk-in') ?></td>
                                <td><span class="google-badge <?php echo $statusClass; ?>"><?php echo ucfirst($sale['payment_status']); ?></span></td>
                                <td class="text-end fw-bold text-primary">₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td class="text-end" style="color: #137333;">₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                                <td class="text-end <?php echo ($balance > 0) ? 'text-danger fw-medium' : 'text-muted'; ?>">
                                    <?php echo ($balance > 0) ? '₵' . number_format($balance, 2) : '-'; ?>
                                </td>
                                <td><span class="google-badge" style="background: #f1f3f4; color: #444746;"><?= e($sale['seller_name']) ?></span></td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="google-btn-secondary" style="padding: 4px 12px; font-size: 12px; gap: 4px;">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">visibility</span> View
                                    </a>

                                    <?php if ($sale['voided']): ?>
                                        <span class="google-badge ms-1" style="background: #444746; color: #fff;">Voided</span>
                                    <?php elseif ($sale['delete_request_status'] === 'pending'): ?>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <form action="<?= BASE_URL ?>/sales/process-delete" method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                                <button type="submit" name="action" value="approve" class="google-btn-secondary ms-1" style="padding: 4px; border-color: #fad2cf; color: #c5221f;" title="Approve Delete">
                                                    <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                                                </button>
                                                <button type="submit" name="action" value="reject" class="google-btn-secondary" style="padding: 4px; border-color: #ceead6; color: #137333;" title="Reject">
                                                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="google-badge badge-subtle-warning ms-1">Deletion Pending</span>
                                        <?php endif; ?>
                                    <?php elseif ($sale['user_id'] == $_SESSION['user_id'] && $sale['delete_request_status'] == 'none'): ?>
                                        <form action="<?= BASE_URL ?>/sales/request-delete" method="POST" style="display:inline;" onsubmit="return confirm('Request to delete this sale?');">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                            <button type="submit" class="google-btn-secondary ms-1" style="padding: 4px; border-color: transparent; color: #c5221f;" title="Request Delete">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <span class="material-symbols-outlined text-muted mb-2" style="font-size: 32px;">receipt_long</span>
                                    <p class="text-muted mb-0" style="font-size: 14px;">No sales found matching your criteria.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-3" style="border-top: 1px solid #e3e3e3; background: #fafafa;">
                    <nav>
                        <ul class="pagination justify-content-center mb-0 gap-2">
                            <?php $queryParams = $_GET; ?>
                            
                            <?php if ($page > 1): ?>
                                <?php $prevParams = $queryParams; $prevParams['page'] = $page - 1; ?>
                                <li class="page-item">
                                    <a class="google-btn-secondary" href="<?= BASE_URL ?>/sales?<?php echo http_build_query($prevParams); ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <li class="page-item disabled d-flex align-items-center">
                                <span class="text-muted px-3" style="font-size: 14px;">Page <?= $page ?> of <?= $totalPages ?></span>
                            </li>

                            <?php if ($page < $totalPages): ?>
                                <?php $nextParams = $queryParams; $nextParams['page'] = $page + 1; ?>
                                <li class="page-item">
                                    <a class="google-btn-secondary" href="<?= BASE_URL ?>/sales?<?php echo http_build_query($nextParams); ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>