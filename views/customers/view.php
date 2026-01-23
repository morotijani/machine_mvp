<?php 
$title = "Customer Profile: " . e($customer['name']);
ob_start();
?>
<style media="print">
    /* Print Overrides */
    .no-print, .btn, .navbar, .sidebar { display: none !important; }
    .card { border: none !important; shadow: none !important; }
    .table-responsive { overflow: visible !important; }
    body { background-color: #fff !important; }
    /* Ensure table fits */
    table { width: 100% !important; }
    th:last-child, td:last-child { display: none !important; } /* Hide Actions column */
    /* Show print title, hide normal title */
    .print-title { display: block !important; }
    .card-header h5:not(.print-title) { display: none !important; }
</style>
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Back and Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASE_URL ?>/customers" class="btn btn-outline-secondary btn-sm">
                    <span class="material-symbols-outlined align-middle" style="font-size: 18px;">arrow_back</span>
                </a>
                <h1 class="h2 mb-0">Customer Profile</h1>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                <div class="btn-group">
                    <button onclick="printRecords('all')" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Print
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="printRecords('all'); return false;">All Records</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printRecords('paid'); return false;">Paid Only</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printRecords('partial'); return false;">Partial Only</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printRecords('unpaid'); return false;">Unpaid Only</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-bold text-danger" href="#" onclick="printRecords('outstanding'); return false;">Outstanding (Partial & Unpaid)</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                        data-bs-toggle="modal" 
                        data-bs-target="#editCustomerModal"
                        data-id="<?= $customer['id'] ?>"
                        data-name="<?= e($customer['name']) ?>"
                        data-phone="<?= e($customer['phone'] ?? '') ?>"
                        data-address="<?= e($customer['address'] ?? '') ?>">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span> Edit
                </button>
            </div>
        </div>

        <!-- Profile & Stats Card -->
        <div class="row mb-4">
            <!-- Profile Info -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                <span class="material-symbols-outlined fs-3">person</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?= e($customer['name']) ?></h5>
                                <small class="text-muted">Customer ID: #<?= $customer['id'] ?></small>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <span class="material-symbols-outlined text-muted" style="font-size: 20px;">phone</span>
                                <div><?php echo !empty($customer['phone']) ? e($customer['phone']) : '<em class="text-muted">No phone</em>'; ?></div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <span class="material-symbols-outlined text-muted" style="font-size: 20px;">location_on</span>
                                <div><?php echo !empty($customer['address']) ? nl2br(e($customer['address'])) : '<em class="text-muted">No address</em>'; ?></div>
                            </li>
                            <li class="d-flex align-items-start gap-2 mt-2">
                                <span class="material-symbols-outlined text-muted" style="font-size: 20px;">calendar_month</span>
                                <div>Since: <?php echo !empty($customer['created_at']) ? date('M j, Y', strtotime($customer['created_at'])) : 'Unknown'; ?></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Financial Stats -->
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-start border-4 border-success">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Paid</h6>
                                <h3 class="text-success mb-0">₵<?php echo number_format($customer['total_paid'], 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-start border-4 border-danger">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Debt</h6>
                                <h3 class="text-danger mb-0">₵<?php echo number_format($customer['total_debt'], 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-start border-4 border-primary">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-bold mb-2">Lifetime Sales</h6>
                                <h3 class="text-primary mb-0">₵<?php echo number_format($customer['total_sales_amount'], 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase History Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Purchase History</h5>
                <h5 class="mb-0 print-title" id="printTitle" style="display:none;"></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="purchaseHistoryTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Invoice #</th>
                                <th>Items Bought</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th class="text-end">Balance</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $sale): 
                                $statusClass = match($sale['payment_status']) {
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning text-dark',
                                    'unpaid' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <tr class="<?php echo $sale['voided'] ? 'table-light opacity-50' : ''; ?>">
                                <td class="ps-3"><?php echo date('M j, Y H:i', strtotime($sale['created_at'])); ?></td>
                                <td><a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="fw-bold text-decoration-none">#<?php echo $sale['id']; ?></a></td>
                                <td>
                                    <small class="text-secondary">
                                        <?php echo !empty($sale['items_summary']) ? e($sale['items_summary']) : '-'; ?>
                                    </small>
                                </td>
                                <td class="fw-bold">₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td class="text-success">₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                                <td class="text-end text-danger fw-bold">
                                    <?php echo ($sale['balance'] > 0) ? '₵'.number_format($sale['balance'], 2) : '-'; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($sale['voided']): ?>
                                        <span class="badge bg-dark">Voided</span>
                                    <?php else: ?>
                                        <span class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo ucfirst($sale['payment_status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <?php if (!$sale['voided'] && $sale['balance'] > 0): ?>
                                        <button class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 ms-auto"
                                                onclick="openPayModal(<?php echo $sale['id']; ?>, <?php echo $sale['balance']; ?>)">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">payments</span> Pay Debt
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No purchase history found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pay Debt Modal -->
<div class="modal fade" id="payDebtModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Settle Invoice Debt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/sales/pay" method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="modal-body">
                    <input type="hidden" name="sale_id" id="pay_sale_id">
                    <div class="mb-3">
                        <label class="form-label">Invoice #</label>
                        <input type="text" class="form-control" id="pay_invoice_display" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text">₵</span>
                            <input type="number" name="amount" id="pay_amount" class="form-control" step="0.01" max="" required>
                        </div>
                        <small class="text-muted">Remaining Balance: <span id="pay_max_display"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reusing Edit Customer Modal from Index (requires duplication or include) - Let's just include it or rewrite lightly -->
<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= BASE_URL ?>/customers/edit" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
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
// Print Records Function
function printRecords(filter) {
    const rows = document.querySelectorAll('#purchaseHistoryTable tbody tr');
    const printTitle = document.getElementById('printTitle');
    const normalTitle = document.querySelector('.card-header h5:not(.print-title)');
    
    // Set title based on filter
    const titles = {
        'all': 'All Purchase Records',
        'paid': 'Paid Records Only',
        'partial': 'Partial Payment Records',
        'unpaid': 'Unpaid Records Only',
        'outstanding': 'Outstanding Debt Records (Unpaid & Partial)'
    };
    
    printTitle.textContent = titles[filter] || 'Purchase History';
    
    // Filter rows
    rows.forEach(row => {
        if (filter === 'all') {
            row.style.display = '';
        } else {
            const statusBadge = row.querySelector('.badge');
            if (!statusBadge) {
                row.style.display = 'none';
                return;
            }
            
            const statusText = statusBadge.textContent.toLowerCase();
            const isVoided = row.classList.contains('opacity-50');
            
            if (isVoided) {
                row.style.display = 'none';
            } else if (filter === 'paid' && statusText === 'paid') {
                row.style.display = '';
            } else if (filter === 'partial' && statusText === 'partial') {
                row.style.display = '';
            } else if (filter === 'unpaid' && statusText === 'unpaid') {
                row.style.display = '';
            } else if (filter === 'outstanding' && (statusText === 'partial' || statusText === 'unpaid')) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
    
    // Show print title, hide normal title
    printTitle.style.display = 'block';
    normalTitle.style.display = 'none';
    
    // Print
    window.print();
    
    // Restore after print
    setTimeout(() => {
        rows.forEach(row => row.style.display = '');
        printTitle.style.display = 'none';
        normalTitle.style.display = 'block';
    }, 100);
}

function openPayModal(saleId, balance) {
    document.getElementById('pay_sale_id').value = saleId;
    document.getElementById('pay_invoice_display').value = '#' + saleId;
    document.getElementById('pay_amount').value = balance.toFixed(2);
    document.getElementById('pay_amount').max = balance.toFixed(2);
    document.getElementById('pay_max_display').textContent = '₵' + balance.toFixed(2);
    
    var modal = new bootstrap.Modal(document.getElementById('payDebtModal'));
    modal.show();
}

// Edit Modal Script
var editCustomerModal = document.getElementById('editCustomerModal');
if (editCustomerModal) {
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
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
