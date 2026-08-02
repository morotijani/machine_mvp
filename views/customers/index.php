<?php
$title = "Customers";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <?php if (isset($_GET['error'])): ?>
            <div class="google-alert google-alert-danger alert-dismissible fade show">
                <span class="material-symbols-outlined">error</span>
                <div class="flex-grow-1">
                    <?= $_GET['error'] === 'phone_exists' ? 'A customer with that phone number already exists.' : e($_GET['error']) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 12px; padding: 1.25rem;"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="google-alert google-alert-success alert-dismissible fade show">
                <span class="material-symbols-outlined">check_circle</span>
                <div class="flex-grow-1">
                    <?= e($_GET['success']) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 12px; padding: 1.25rem;"></button>
            </div>
        <?php endif; ?>
        <style>
            .google-search-wrapper {
                background: #fff;
                border-radius: 24px;
                padding: 4px 8px;
                display: flex;
                align-items: center;
                box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
                max-width: 600px;
                width: 100%;
            }

            .google-search-input {
                border: none;
                outline: none;
                padding: 8px 12px;
                flex-grow: 1;
                font-size: 15px;
            }

            .google-search-select {
                border: none;
                outline: none;
                padding: 8px 12px;
                color: #5f6368;
                background: transparent;
                font-size: 14px;
                cursor: pointer;
            }

            .google-divider {
                width: 1px;
                height: 24px;
                background-color: #dadce0;
                margin: 0 8px;
            }

            .google-btn-primary {
                background-color: #0b57d0;
                color: #fff;
                border-radius: 20px;
                padding: 8px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
                text-decoration: none;
            }

            .google-btn-primary:hover {
                background-color: #0842a0;
                color: #fff;
            }

            .google-btn-secondary {
                background-color: transparent;
                color: #444746;
                border-radius: 20px;
                padding: 8px 16px;
                font-weight: 500;
                border: 1px solid #747775;
                transition: background-color 0.2s;
                text-decoration: none;
            }

            .google-btn-secondary:hover {
                background-color: #f1f3f4;
                color: #1f1f1f;
            }

            .google-table-card {
                background: #fff;
                border-radius: 24px;
                overflow: hidden;
                border: none;
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
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                border: none;
                background: transparent;
                transition: background-color 0.2s;
                text-decoration: none;
            }

            .action-btn:hover {
                background-color: #f1f3f4;
                color: #202124;
            }

            .google-badge {
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 12px;
                letter-spacing: 0.5px;
                font-weight: 600;
                display: inline-block;
            }

            .badge-subtle-success {
                background-color: #e6f4ea;
                color: #137333;
                border: 1px solid #ceead6;
            }
            .google-alert {
                border-radius: 16px;
                padding: 12px 24px;
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
                font-size: 14px;
                font-weight: 500;
            }
            .google-alert-danger {
                background-color: #fce8e6;
                color: #c5221f;
                border: 1px solid #fad2cf;
            }
            .google-alert-success {
                background-color: #e6f4ea;
                color: #137333;
                border: 1px solid #ceead6;
            }

            .google-row {
                display: flex;
                align-items: center;
                padding: 24px 32px;
                border-bottom: 1px solid #e3e3e3;
                transition: background-color 0.2s;
            }

            .google-row:last-child {
                border-bottom: none;
            }

            .google-row:focus-within {
                background-color: #f8f9fa;
            }

            .google-icon {
                color: #444746;
                margin-right: 24px;
                font-size: 24px;
            }

            .google-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .google-label {
                font-size: 16px;
                color: #1f1f1f;
                margin-bottom: 4px;
                font-weight: 500;
            }

            .google-input {
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #5f6368;
                padding: 0;
                width: 100%;
            }

            .google-input:focus {
                color: #202124;
            }
        </style>

        <div class="d-flex justify-content-between flex-wrap align-items-center pt-4 pb-3 mb-3 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Customers</h1>
            <div
                class="d-flex flex-wrap gap-2 align-items-center justify-content-start justify-content-md-end flex-grow-1">
                <form action="" method="GET" class="d-flex flex-grow-1 flex-md-grow-0" style="min-width: 280px;">
                    <div class="google-search-wrapper">
                        <span class="material-symbols-outlined text-muted" style="margin-left: 8px;">search</span>
                        <input type="text" name="search" class="google-search-input" placeholder="Search customers..."
                            value="<?= e($search ?? '') ?>">

                        <div class="google-divider"></div>
                        <select name="sort" class="google-search-select" onchange="this.form.submit()">
                            <option value="name" <?= ($sort == 'name') ? 'selected' : '' ?>>Name</option>
                            <option value="total_debt" <?= ($sort == 'total_debt') ? 'selected' : '' ?>>Debt Amount
                            </option>
                            <option value="last_purchase" <?= ($sort == 'last_purchase') ? 'selected' : '' ?>>Last Purchase
                            </option>
                        </select>

                        <div class="google-divider"></div>
                        <select name="order" class="google-search-select" onchange="this.form.submit()">
                            <option value="DESC" <?= ($order == 'DESC') ? 'selected' : '' ?>>DESC</option>
                            <option value="ASC" <?= ($order == 'ASC') ? 'selected' : '' ?>>ASC</option>
                        </select>
                    </div>
                </form>
                <?php if (!empty($search) || $sort !== 'total_debt' || $order !== 'DESC'): ?>
                    <a href="<?= BASE_URL ?>/customers" class="google-btn-secondary" style="padding: 8px 16px;">Clear</a>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/admin/trash" class="google-btn-secondary d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span> Trash
                    </a>
                <?php endif; ?>
                <button type="button" class="google-btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#addCustomerModal">
                    <span class="material-symbols-outlined" style="font-size: 18px;">person_add</span> New Customer
                </button>
            </div>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Added By</th>
                            <th class="text-end">Outstanding Debt</th>
                            <th>Last Purchase</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($customers as $customer): ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $i++ ?></td>
                                <td>
                                    <strong><a href="<?= BASE_URL ?>/customers/view?id=<?= $customer['id'] ?>"
                                            class="text-decoration-none" style="color: #1f1f1f;">
                                            <?= e($customer['name']) ?>
                                        </a></strong>
                                </td>
                                <td><?= e($customer['phone']) ?></td>
                                <td><?= e($customer['address']) ?></td>
                                <td><span class="google-badge" style="background-color: #f1f3f4; color: #444746;"><i
                                            class="material-symbols-outlined align-middle"
                                            style="font-size: 14px;">person</i>
                                        <?= e($customer['created_by_name'] ?? 'System') ?></span></td>
                                <td class="text-end">
                                    <?php if ($customer['total_debt'] > 0): ?>
                                        <span
                                            class="text-danger fw-bold">₵<?php echo number_format($customer['total_debt'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="google-badge badge-subtle-success">Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td><span
                                        style="color: #5f6368; font-size: 14px;"><?php echo $customer['last_purchase'] ? date('M j, Y', strtotime($customer['last_purchase'])) : '-'; ?></span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="action-btn text-primary" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#editCustomerModal"
                                        data-id="<?= $customer['id'] ?>" data-name="<?= e($customer['name']) ?>"
                                        data-phone="<?= e($customer['phone'] ?? '') ?>"
                                        data-address="<?= e($customer['address'] ?? '') ?>">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                    </button>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <button type="button" class="action-btn text-danger" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#deleteCustomerModal"
                                            data-id="<?= $customer['id'] ?>" data-name="<?= e($customer['name']) ?>">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No customers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php
                        $queryStr = "?search=" . urlencode($search ?? '') . "&sort=" . urlencode($sort) . "&order=" . urlencode($order);
                        ?>
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $queryStr ?>&page=<?= $page - 1 ?>">Previous</a>
                        </li>

                        <?php
                        $range = 2;
                        for ($i = 1; $i <= $totalPages; $i++):
                            if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                                ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $queryStr ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                                <?php
                            elseif ($i == $page - $range - 1 || $i == $page + $range + 1):
                                ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php
                            endif;
                        endfor;
                        ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $queryStr ?>&page=<?= $page + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title" style="color: #1f1f1f; font-weight: 500;">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>/customers/create" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="modal-body pt-3 pb-4 px-0">
                        <div class="google-row" style="padding: 12px 32px;">
                            <span class="material-symbols-outlined google-icon">person</span>
                            <div class="google-content">
                                <label class="google-label">Name</label>
                                <input type="text" name="name" class="google-input" placeholder="Enter customer name"
                                    required>
                            </div>
                        </div>
                        <div class="google-row" style="padding: 12px 32px;">
                            <span class="material-symbols-outlined google-icon">phone</span>
                            <div class="google-content">
                                <label class="google-label">Phone</label>
                                <input type="text" name="phone" class="google-input" placeholder="Enter customer phone">
                            </div>
                        </div>
                        <div class="google-row"
                            style="padding: 12px 32px; border-bottom: none; align-items: flex-start;">
                            <span class="material-symbols-outlined google-icon"
                                style="margin-top: 2px;">location_on</span>
                            <div class="google-content">
                                <label class="google-label">Address</label>
                                <textarea name="address" class="google-input" rows="2"
                                    style="resize: none; margin-top: 2px;"
                                    placeholder="Enter customer address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="google-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="google-btn-primary">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title" style="color: #1f1f1f; font-weight: 500;">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>/customers/edit" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body pt-3 pb-4 px-0">
                        <div class="google-row" style="padding: 12px 32px;">
                            <span class="material-symbols-outlined google-icon">person</span>
                            <div class="google-content">
                                <label class="google-label">Name</label>
                                <input type="text" name="name" id="edit_name" class="google-input" required>
                            </div>
                        </div>
                        <div class="google-row" style="padding: 12px 32px;">
                            <span class="material-symbols-outlined google-icon">phone</span>
                            <div class="google-content">
                                <label class="google-label">Phone</label>
                                <input type="text" name="phone" id="edit_phone" class="google-input">
                            </div>
                        </div>
                        <div class="google-row"
                            style="padding: 12px 32px; border-bottom: none; align-items: flex-start;">
                            <span class="material-symbols-outlined google-icon"
                                style="margin-top: 2px;">location_on</span>
                            <div class="google-content">
                                <label class="google-label">Address</label>
                                <textarea name="address" id="edit_address" class="google-input" rows="2"
                                    style="resize: none; margin-top: 2px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="google-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="google-btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Customer Modal -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title" style="color: #1f1f1f; font-weight: 500;">Delete Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>/customers/delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-body pt-3 pb-4 px-4 text-center">
                        <span class="material-symbols-outlined text-danger mb-3" style="font-size: 48px;">warning</span>
                        <h5 class="mb-2" id="delete_name_display" style="color: #1f1f1f; font-weight: 500;"></h5>
                        <p style="color: #5f6368; font-size: 15px; margin-bottom: 0;">
                            Are you sure you want to soft-delete this customer?<br>
                            They can be restored from Trash later.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-center">
                        <button type="button" class="google-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="google-btn-primary" style="background-color: #dc3545;">Delete Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Populate Edit Modal
        var editCustomerModal = document.getElementById('editCustomerModal');
        editCustomerModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var phone = button.getAttribute('data-phone');
            var address = button.getAttribute('data-address');

            var modal = this;
            modal.querySelector('#edit_id').value = id;
            modal.querySelector('#edit_name').value = name;
            modal.querySelector('#edit_phone').value = phone;
            modal.querySelector('#edit_address').value = address;
        });

        // Populate Delete Modal
        var deleteCustomerModal = document.getElementById('deleteCustomerModal');
        if (deleteCustomerModal) {
            deleteCustomerModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');

                var modal = this;
                modal.querySelector('#delete_id').value = id;
                modal.querySelector('#delete_name_display').textContent = name;
            });
        }
    </script>
</div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>