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
    .timeline-thread { display: none !important; }
</style>
<style>
    /* Purchase Timeline Styles */
    .purchase-timeline {
        position: relative;
        padding-left: 2rem;
    }
    .timeline-thread {
        position: absolute;
        left: 31px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
        z-index: 1;
    }
    .timeline-date-group {
        position: relative;
        z-index: 2;
        margin-bottom: 2.5rem;
    }
    .timeline-date-header {
        position: relative;
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        color: #495057;
        margin-left: -2.3rem;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .timeline-date-header .material-symbols-outlined {
        font-size: 20px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-item-marker {
        position: absolute;
        left: -33px;
        top: 20px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0d6efd;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
        z-index: 2;
    }
    .timeline-item-marker.payment-marker {
        background: #198754;
        box-shadow: 0 0 0 2px #19875444;
    }
    .timeline-card {
        transition: transform 0.2s, shadow-sm 0.2s;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .timeline-card.payment-card {
        border-right: 4px solid #198754 !important;
    }
    .timeline-card:hover {
        transform: translateX(5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.8em;
    }
    @media (max-width: 576px) {
        .purchase-timeline { padding-left: 1.5rem; }
        .timeline-thread { left: 15px; }
        .timeline-date-header { margin-left: -1.8rem; font-size: 0.9rem; }
        .timeline-item-marker { left: -25px; }
    }
</style>
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Back and Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= $_SESSION['last_customers_url'] ?? BASE_URL . '/customers' ?>" class="btn btn-outline-secondary btn-sm">
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
                <?php if ($customer['total_debt'] > 0): ?>
                <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1"
                        data-bs-toggle="modal" 
                        data-bs-target="#bulkRepayModal">
                    <span class="material-symbols-outlined" style="font-size: 16px;">payments</span> Bulk Repayment
                </button>
                <?php endif; ?>
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

        <!-- Purchase History Timeline -->
        <div class="card shadow-sm border-0 mb-4 no-print">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Purchase History Timeline
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($groupedHistory)): ?>
                    <div class="text-center py-5">
                        <span class="material-symbols-outlined fs-1 text-muted mb-3">shopping_cart_off</span>
                        <p class="text-muted">No purchase history found for this customer.</p>
                    </div>
                <?php else: ?>
                    <div class="purchase-timeline">
                        <div class="timeline-thread"></div>
                        
                        <?php foreach ($groupedHistory as $date => $events): ?>
                            <div class="timeline-date-group">
                                <div class="timeline-date-header">
                                    <span class="material-symbols-outlined">calendar_today</span>
                                    <?= e($date) ?>
                                </div>
                                
                                <?php foreach ($events as $event): 
                                    if ($event['type'] === 'sale'):
                                        $sale = $event['data'];
                                        $statusClass = match($sale['payment_status']) {
                                            'paid' => 'bg-success',
                                            'partial' => 'bg-warning text-dark',
                                            'unpaid' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                ?>
                                    <div class="timeline-item">
                                        <div class="timeline-item-marker"></div>
                                        <div class="card timeline-card shadow-sm <?php echo $sale['voided'] ? 'opacity-50 grayscale' : ''; ?>">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <span class="fw-bold text-primary">Purchase #<?= $sale['id'] ?></span>
                                                            <span class="text-muted small d-flex align-items-center gap-1">
                                                                <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                                                <?= date('h:i A', strtotime($sale['created_at'])) ?>
                                                            </span>
                                                        </div>
                                                        <div class="small text-dark mb-2">
                                                            <?= !empty($sale['items_summary']) ? e($sale['items_summary']) : '<em class="text-muted">No item details</em>' ?>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <?php if ($sale['voided']): ?>
                                                            <span class="badge bg-dark status-badge rounded-pill">Voided</span>
                                                        <?php else: ?>
                                                            <span class="badge <?= $statusClass ?> status-badge rounded-pill"><?= ucfirst($sale['payment_status']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="row g-2 align-items-center border-top pt-2">
                                                    <div class="col-4">
                                                        <label class="text-muted small d-block">Total</label>
                                                        <span class="fw-bold">₵<?= number_format($sale['total_amount'], 2) ?></span>
                                                    </div>
                                                    <div class="col-4 border-start border-end">
                                                        <label class="text-muted small d-block">Paid</label>
                                                        <span class="text-success fw-bold">₵<?= number_format($sale['paid_amount'], 2) ?></span>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="text-muted small d-block">Balance</label>
                                                        <span class="<?= $sale['balance'] > 0 ? 'text-danger' : 'text-muted' ?> fw-bold">
                                                            <?= ($sale['balance'] > 0) ? '₵'.number_format($sale['balance'], 2) : '-' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-end gap-2 mt-3">
                                                    <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" class="btn btn-sm btn-outline-primary py-1 px-3">
                                                        Details
                                                    </a>
                                                    <?php if (!$sale['voided'] && $sale['balance'] > 0): ?>
                                                        <button class="btn btn-sm btn-success py-1 px-3"
                                                                onclick="openPayModal(<?= $sale['id'] ?>, <?= $sale['balance'] ?>)">
                                                            Pay Debt
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: // Payment Event
                                    $pay = $event['data'];
                                ?>
                                    <div class="timeline-item">
                                        <div class="timeline-item-marker payment-marker"></div>
                                        <div class="card timeline-card payment-card shadow-sm border-success bg-success bg-opacity-10">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <span class="fw-bold text-success d-flex align-items-center gap-1">
                                                                <span class="material-symbols-outlined" style="font-size: 18px;">payments</span>
                                                                Debt Repayment
                                                            </span>
                                                            <span class="text-muted small d-flex align-items-center gap-1">
                                                                <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                                                <?= date('h:i A', strtotime($pay['payment_date'])) ?>
                                                            </span>
                                                        </div>
                                                        <div class="small text-muted mb-1">
                                                            <strong>Recorded By:</strong> <?= e($pay['recorder_name']) ?>
                                                        </div>
                                                        <div class="small text-dark">
                                                            <strong>Note:</strong> <?= e($pay['notes'] ?: 'No details') ?>
                                                        </div>
                                                        <?php if (!empty($pay['affected_invoices'])): ?>
                                                            <div class="mt-2 small">
                                                                <span class="text-muted">Applied to Invoices:</span>
                                                                <span class="badge bg-white text-dark border shadow-sm">#<?= str_replace(', ', '</span> <span class="badge bg-white text-dark border shadow-sm">#', e($pay['affected_invoices'])) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="h5 mb-0 text-success fw-bold">+₵<?= number_format($pay['amount'], 2) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hidden Table for Printing (Maintains existing Print functionality) -->
        <div class="d-none d-print-block">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 print-title" id="printTitle">Purchase History</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0" id="purchaseHistoryTable">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $sale): ?>
                            <tr>
                                <td><?= date('M j, Y H:i', strtotime($sale['created_at'])) ?></td>
                                <td>#<?= $sale['id'] ?></td>
                                <td><small><?= e($sale['items_summary']) ?></small></td>
                                <td>₵<?= number_format($sale['total_amount'], 2) ?></td>
                                <td>₵<?= number_format($sale['paid_amount'], 2) ?></td>
                                <td>₵<?= number_format($sale['balance'], 2) ?></td>
                                <td><span class="badge"><?= ucfirst($sale['payment_status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
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
    const normalTitle = document.querySelector('.no-print .card-header h5');
    
    // Set title based on filter
    const titles = {
        'all': 'All Purchase Records',
        'paid': 'Paid Records Only',
        'partial': 'Partial Payment Records',
        'unpaid': 'Unpaid Records Only',
        'outstanding': 'Outstanding Debt Records (Unpaid & Partial)'
    };
    
    if (printTitle) {
        printTitle.textContent = titles[filter] || 'Purchase History';
        printTitle.style.display = 'block';
    }
    
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
    if (normalTitle) normalTitle.style.display = 'none';
    
    // Print
    window.print();
    
    // Restore after print
    setTimeout(() => {
        rows.forEach(row => row.style.display = '');
        if (printTitle) printTitle.style.display = 'none';
        if (normalTitle) normalTitle.style.display = 'block';
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

<!-- Bulk Repayment Modal -->
<div class="modal fade" id="bulkRepayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">payments</span>
                    Bulk Debt Repayment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/customers/repay-bulk" method="POST" onsubmit="return confirm('Are you sure you want to allocate this payment across all oldest invoices?')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold">Customer</label>
                        <div class="h5 mb-0 fw-bold"><?= e($customer['name']) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold">Total Amount Brought</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">₵</span>
                            <input type="number" name="amount" class="form-control fw-bold border-start-0" step="0.01" min="0.01" max="<?= $customer['total_debt'] + 1000000 ?>" value="<?= $customer['total_debt'] ?>" required autofocus>
                        </div>
                        <div class="form-text mt-2 d-flex justify-content-between">
                            <span>Total Debt: <span class="text-danger fw-bold">₵<?= number_format($customer['total_debt'], 2) ?></span></span>
                            <span class="text-muted italic small">FIFO Allocation (Oldest First)</span>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 shadow-sm small py-2 d-flex align-items-start gap-2">
                        <span class="material-symbols-outlined text-info" style="font-size: 18px;">info</span>
                        <div>This payment will be automatically applied to the oldest invoices first until the amount is exhausted.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">Process Repayment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
