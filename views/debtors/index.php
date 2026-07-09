<?php
$title = "Debt System";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 gap-2">
            <h1 class="h2 mb-0">Standalone Debt System</h1>
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-start justify-content-md-end flex-grow-1">
                <form action="<?= BASE_URL ?>/debtors" method="GET" class="d-flex flex-grow-1 flex-md-grow-0 gap-2" style="min-width: 280px;">
                    <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border flex-grow-1">
                        <span class="input-group-text bg-white border-0 pe-1">
                            <span class="material-symbols-outlined text-muted" style="font-size: 18px;">search</span>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-1" placeholder="Search debtor name or phone..." value="<?= e($_GET['search'] ?? '') ?>" style="box-shadow: none;">
                        <button type="submit" class="btn btn-primary px-3 fw-medium">Search</button>
                    </div>
                    
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="<?= BASE_URL ?>/debtors" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="d-flex flex-wrap gap-2 page-header-actions">
                    <a href="<?= BASE_URL ?>/debtors/create" class="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 px-3 fw-medium shadow-sm">
                        <span class="material-symbols-outlined" style="font-size: 18px;">person_add</span> Add New Debtor
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Debtor Name</th>
                                <th>Contact</th>
                                <th class="text-end">Total Debt</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($debtors as $d): ?>
                            <?php $balance = $d['total_amount'] - $d['paid_amount']; ?>
                            <tr>
                                <td>
                                    <strong><?= e($d['name']) ?></strong>
                                    <br><small class="text-muted"><?= e($d['description']) ?></small>
                                </td>
                                <td><?= e($d['phone'] ?: 'N/A') ?></td>
                                <td class="text-end">₵<?= number_format($d['total_amount'], 2) ?></td>
                                <td class="text-end text-success">₵<?= number_format($d['paid_amount'], 2) ?></td>
                                <td class="text-end fw-bold text-danger">₵<?= number_format($balance, 2) ?></td>
                                <td>
                                    <?php if ($d['status'] === 'cleared'): ?>
                                        <span class="badge bg-success">Cleared</span>
                                    <?php elseif ($d['status'] === 'partially_paid'): ?>
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <?php if ($balance > 0): ?>
                                            <a href="<?= BASE_URL ?>/debtors/payment?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-success" title="Record Payment">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">payments</span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <a href="<?= BASE_URL ?>/debtors/increase?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-warning" title="Increase Debt">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">add_circle</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= BASE_URL ?>/debtors/history?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-info" title="Payment History">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">history</span>
                                        </a>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <form action="<?= BASE_URL ?>/debtors/delete" method="POST" onsubmit="return confirm('Remove this debtor?')" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($debtors)): ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">No debtors found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
