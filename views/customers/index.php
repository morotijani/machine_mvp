<?php
$title = "Customers";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Customers</h1>
            <div class="d-flex gap-3 align-items-center">
                <form action="" method="GET" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <span class="material-symbols-outlined text-muted" style="font-size: 20px;">search</span>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search customers..." value="<?= htmlspecialchars($search ?? '') ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>

                <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <span class="material-symbols-outlined" style="font-size: 20px;">person_add</span> New Customer
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th class="text-end">Outstanding Debt</th>
                                <th>Last Purchase</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td><?php echo htmlspecialchars($customer['address']); ?></td>
                                <td class="text-end">
                                    <?php if ($customer['total_debt'] > 0): ?>
                                        <span class="text-danger fw-bold">₵<?php echo number_format($customer['total_debt'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="text-success small">Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo $customer['last_purchase'] ? date('M j, Y', strtotime($customer['last_purchase'])) : '-'; ?></small></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCustomerModal"
                                            data-id="<?php echo $customer['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                            data-phone="<?php echo htmlspecialchars($customer['phone']); ?>"
                                            data-address="<?php echo htmlspecialchars($customer['address']); ?>">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">edit</span> Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No customers found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>/customers/create" method="POST">
                <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Edit Customer Modal -->
        <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>/customers/edit" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
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
        </script>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
