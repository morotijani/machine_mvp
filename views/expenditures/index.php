<?php
$title = "Expenditures";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Expenditures</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/expenditures/create" class="btn btn-primary d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">add</span> Add Expenditure
                </a>
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
                <form action="<?= BASE_URL ?>/expenditures" method="GET" class="row g-2 mb-4">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Search category or description..." value="<?= e($search ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <th>Recorded By</th>
                                <?php endif; ?>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenditures as $exp): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($exp['date'])) ?></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= e($exp['category']) ?></span></td>
                                <td><small class="text-muted"><?= e($exp['description']) ?></small></td>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <td><small class="text-muted"><?= e($exp['recorder_name'] ?? 'N/A') ?></small></td>
                                <?php endif; ?>
 <!-- spot -->
                                <td class="text-end fw-bold text-danger">- ₵<?= number_format($exp['amount'], 2) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= BASE_URL ?>/expenditures/edit?id=<?= $exp['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </a>
                                        <form action="<?= BASE_URL ?>/expenditures/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this expenditure?')">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= $exp['id'] ?>">
 <!-- spot -->
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($expenditures)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No expenditures found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/expenditures?page=<?= $i ?><?= $search ? '&search='.urlencode($search) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
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
