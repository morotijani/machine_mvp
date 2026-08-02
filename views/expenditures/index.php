<?php
$title = "Expenditures";
ob_start();
?>

<style>
    .google-search-wrapper {
        height: 44px;
        border-radius: 22px;
        border: 1px solid #dadce0;
        background-color: #fff;
        display: flex;
        overflow: hidden;
        flex-grow: 1;
        min-width: 280px;
    }
    .google-search-icon {
        display: flex;
        align-items: center;
        padding-left: 16px;
        color: #5f6368;
    }
    .google-search-input {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        background: transparent;
        padding-left: 8px;
        font-size: 15px;
        color: #202124;
        flex-grow: 1;
    }
    .google-search-input::placeholder {
        color: #5f6368;
    }
    .google-search-btn {
        background-color: #3b71ca;
        color: #fff;
        border: none;
        font-weight: 500;
        padding: 0 24px;
        font-size: 15px;
        transition: background-color 0.2s;
    }
    .google-search-btn:hover {
        background-color: #2b5db0;
    }
    .google-add-btn {
        background-color: #3b71ca;
        color: #fff;
        border: none;
        border-radius: 22px;
        height: 44px;
        padding: 0 24px;
        font-weight: 500;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .google-add-btn:hover {
        background-color: #2b5db0;
        color: #fff;
    }

    .google-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: none;
        margin-bottom: 30px;
    }
    .google-table-card thead th {
        border-bottom: 1px solid #e3e3e3;
        background-color: #fff;
        color: #5f6368;
        font-weight: 500;
        padding: 16px 24px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .google-table-card tbody td {
        padding: 16px 24px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .google-table-card tbody tr:last-child td {
        border-bottom: none;
    }
    .google-table-card tbody tr {
        transition: background-color 0.2s;
    }
    .google-table-card tbody tr:hover {
        background-color: #f8f9fa;
    }
    .action-btn {
        border: none;
        background: transparent;
        color: #5f6368;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s, color 0.2s;
        text-decoration: none !important;
    }
    .action-btn:hover {
        background-color: #f1f3f4;
        color: #202124;
    }

    .google-alert {
        display: flex;
        align-items: center;
        background-color: #e6f4ea;
        color: #137333;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        border: 1px solid #ceead6;
        margin-bottom: 24px;
    }

    /* Modal styling */
    .material-modal .modal-content {
        border-radius: 28px;
        border: none;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    }
    .material-modal .modal-title-custom {
        font-size: 24px;
        color: #1f1f1f;
        font-weight: 400;
        margin-bottom: 16px;
    }
    .material-modal .btn-cancel {
        color: #0b57d0;
        font-weight: 500;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 20px;
    }
    .material-modal .btn-cancel:hover {
        background-color: #f6f8fb;
    }
    .material-modal .btn-ok {
        background-color: #0b57d0;
        color: #fff;
        font-weight: 500;
        border-radius: 20px;
        padding: 10px 24px;
        border: none;
        transition: background-color 0.2s;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-4 pb-3 mb-3 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Expenditures</h1>
            
            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-start justify-content-md-end flex-grow-1">
                <form action="<?= BASE_URL ?>/expenditures" method="GET" class="d-flex flex-grow-1 flex-md-grow-0 gap-2">
                    <div class="google-search-wrapper">
                        <div class="google-search-icon">
                            <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                        </div>
                        <input type="text" name="search" class="form-control google-search-input" placeholder="Search expenditures..." value="<?= e($search ?? '') ?>">
                        <button type="submit" class="google-search-btn">Search</button>
                    </div>
                </form>
                
                <?php if (!empty($search)): ?>
                    <a href="<?= BASE_URL ?>/expenditures" class="btn btn-light rounded-pill px-3 d-flex align-items-center" style="border: 1px solid #dadce0;">Clear</a>
                <?php endif; ?>

                <div class="d-flex flex-wrap page-header-actions">
                    <a href="<?= BASE_URL ?>/expenditures/create" class="google-add-btn">
                        <span class="material-symbols-outlined" style="font-size: 20px;">add</span> Add Expenditure
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="google-alert shadow-sm">
                <span class="material-symbols-outlined text-success me-2">check_circle</span>
                <span><?= e($_GET['success']) ?></span>
            </div>
        <?php endif; ?>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
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
                        <?php $i = 1; foreach ($expenditures as $exp): ?>
                        <tr>
                            <td class="text-muted fw-bold"><?= $i++ ?></td>
                            <td class="fw-bold text-dark" style="font-size: 13px;">
                                <?= date('M d, Y', strtotime($exp['date'])) ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1 fw-medium">
                                    <?= e($exp['category']) ?>
                                </span>
                            </td>
                            <td class="text-muted text-uppercase" style="font-size: 13px;">
                                <?= e($exp['description']) ?>
                            </td>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-muted">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                                        <small><?= e($exp['recorder_name'] ?? 'N/A') ?></small>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td class="text-end fw-bold text-danger" style="font-size: 16px;">
                                - ₵<?= number_format($exp['amount'], 2) ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="<?= BASE_URL ?>/expenditures/edit?id=<?= $exp['id'] ?>" class="action-btn text-secondary" title="Edit">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                </a>
                                <button type="button" class="action-btn text-danger" title="Delete" onclick="confirmDelete(<?= $exp['id'] ?>)">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($expenditures)): ?>
                            <tr>
                                <td colspan="<?= ($_SESSION['role'] === 'admin') ? 7 : 6 ?>" class="text-center py-5">
                                    <div class="text-muted">No expenditures found.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <style>
                .custom-pagination {
                    --bs-pagination-color: #3b71ca;
                    --bs-pagination-active-bg: #3b71ca;
                    --bs-pagination-active-border-color: #3b71ca;
                    --bs-pagination-hover-color: #2b5db0;
                    --bs-pagination-focus-color: #2b5db0;
                    box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.1);
                    border-radius: 4px;
                }
                .custom-pagination .page-link {
                    padding: 8px 16px;
                    font-weight: 500;
                }
            </style>
            <div class="d-flex justify-content-center mt-4 pt-4 border-top">
                <nav>
                    <ul class="pagination custom-pagination mb-0">
                        <?php 
                        $pages = [];
                        if ($totalPages <= 7) {
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $pages[] = $i;
                            }
                        } else {
                            if ($page <= 4) {
                                $pages = [1, 2, 3, 4, 5, '...', $totalPages];
                            } elseif ($page >= $totalPages - 3) {
                                $pages = [1, '...', $totalPages - 4, $totalPages - 3, $totalPages - 2, $totalPages - 1, $totalPages];
                            } else {
                                $pages = [1, '...', $page - 1, $page, $page + 1, '...', $totalPages];
                            }
                        }

                        foreach ($pages as $p): 
                            if ($p === '...'):
                        ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php else: ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/expenditures?page=<?= $p ?><?= $search ? '&search='.urlencode($search) : '' ?>"><?= $p ?></a>
                            </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade material-modal" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom text-danger d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">warning</span> Delete Expenditure?
                </h4>
                <p class="modal-text text-muted mb-4">This action cannot be undone. Are you sure you want to permanently delete this expenditure record?</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="<?= BASE_URL ?>/expenditures/delete" method="POST" class="m-0">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" id="delete_id">
                        <button type="submit" class="btn btn-ok bg-danger text-white">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('delete_id').value = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
