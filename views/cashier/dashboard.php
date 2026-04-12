<?php 
$title = "Cashier Dashboard | Live Queue";
ob_start();
?>

<style>
    .queue-board {
        min-height: 50vh;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        border: 2px dashed #dee2e6;
    }
    .ticket-card {
        border-left: 5px solid #0d6efd;
        transition: transform 0.2s, box-shadow 0.2s;
        animation: slideIn 0.3s ease-out;
    }
    .ticket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .ticket-type-sale { border-left-color: #0d6efd; }
    .ticket-type-debt { border-left-color: #198754; }
</style>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 d-flex align-items-center gap-2">
        <span class="material-symbols-outlined text-primary fs-1">point_of_sale</span>
        Cashier Dashboard
    </h1>
    <div class="d-flex align-items-center gap-2">
        <div class="spinner-grow text-success spinner-grow-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="text-success fw-bold small">Live Queue Active</span>
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

<div class="row">
    <div class="col-md-12">
        <h5 class="fw-bold mb-3">Pending Payment Requests</h5>
        <div class="queue-board" id="queue-container">
            <!-- Tickets injected here -->
            <div id="empty-state" class="text-center text-muted py-5 <?= count($pendingRequests) > 0 ? 'd-none' : '' ?>">
                <span class="material-symbols-outlined fs-1 opacity-50 mb-2">inbox</span>
                <p>No pending payment requests right now.</p>
            </div>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div class="modal fade" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">payments</span>
                    Process Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/cashier/process" method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="request_id" id="modal_request_id">
                <div class="modal-body py-4">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Reference</span>
                        <span class="badge bg-primary fs-5" id="modal_reference"></span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase fw-bold">Amount Due / Requested</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">₵</span>
                            <input type="number" name="amount" id="modal_amount" class="form-control fw-bold border-start-0" step="0.01" min="0" required>
                        </div>
                        <div id="walkin_help_text" class="form-text text-danger mt-2 d-none">
                            <span class="material-symbols-outlined align-middle" style="font-size: 14px;">info</span> Walk-in customers do not have a ledger and must pay the exact requested amount.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-lg fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">task_alt</span> Endorse & Receive Cash
                        </button>
                        <button type="button" id="btn_pay_later" class="btn btn-outline-warning d-none d-flex align-items-center justify-content-center gap-2"
                            onclick="document.getElementById('modal_amount').value = '0'; this.form.querySelector('[name=action][value=approve]').click();">
                            <span class="material-symbols-outlined">schedule</span> Pay Later (Endorse as Unpaid)
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 mt-2" onclick="return confirm('Are you sure you want to reject this request? The salesperson will have to create a new one.')">
                            <span class="material-symbols-outlined">cancel</span> Reject Request
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="ticket-template">
    <div class="card ticket-card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <span class="badge bg-light text-dark border t-type mb-1 d-inline-block">Type</span>
                    <h4 class="fw-bold text-primary mb-0 t-ref">#000</h4>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Customer</div>
                    <div class="fw-bold t-customer">Walk-in</div>
                    <div class="small text-muted mt-1"><span class="material-symbols-outlined align-text-bottom" style="font-size: 14px;">person</span> <span class="t-creator">Salesperson</span></div>
                </div>
                <div class="col-md-3 text-end">
                    <div class="text-muted small">Amount Due</div>
                    <h3 class="text-success fw-bold mb-0 t-amount">₵0.00</h3>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-dark fw-bold px-4 btn-process" onclick="">Process</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
let currentRequests = <?= json_encode($pendingRequests) ?>;

function renderQueue(requests) {
    const container = document.getElementById('queue-container');
    const emptyState = document.getElementById('empty-state');
    const template = document.getElementById('ticket-template');

    // Remove existing cards
    container.querySelectorAll('.ticket-card').forEach(el => el.remove());

    if (requests.length === 0) {
        emptyState.classList.remove('d-none');
        return;
    }

    emptyState.classList.add('d-none');

    requests.forEach(req => {
        const clone = template.content.cloneNode(true);
        const card = clone.querySelector('.ticket-card');
        
        let typeStr = 'Sale Invoice';
        if (req.type === 'debt_single') typeStr = 'Settle Invoice Dept';
        if (req.type === 'debt_bulk') typeStr = 'Bulk Debt Repayment';
        
        if (req.type.includes('debt')) {
            card.classList.add('ticket-type-debt');
            clone.querySelector('.t-type').textContent = typeStr;
        } else {
            clone.querySelector('.t-type').textContent = typeStr;
        }

        clone.querySelector('.t-ref').textContent = req.type === 'debt_bulk' ? 'Customer #' + req.reference_id : 'Invoice #' + req.reference_id;
        clone.querySelector('.t-customer').textContent = req.customer_name || 'Walk-in Customer';
        clone.querySelector('.t-creator').textContent = req.creator_name;
        clone.querySelector('.t-amount').textContent = '₵' + parseFloat(req.amount_due).toFixed(2);
        
        const btn = clone.querySelector('.btn-process');
        btn.onclick = () => openProcessModal(req);

        container.appendChild(clone);
    });
}

function openProcessModal(req) {
    document.getElementById('modal_request_id').value = req.id;
    document.getElementById('modal_reference').textContent = req.type === 'debt_bulk' ? 'Customer #' + req.reference_id : 'Invoice #' + req.reference_id;
    
    const amountInput = document.getElementById('modal_amount');
    const helpTextInfo = document.getElementById('walkin_help_text');
    
    amountInput.value = parseFloat(req.amount_due).toFixed(2);
    amountInput.max = parseFloat(req.amount_due).toFixed(2);
    
    // JS Validation
    amountInput.oninput = function() {
        let maxVal = parseFloat(this.max);
        if (parseFloat(this.value) > maxVal) {
            this.value = maxVal;
        }
    };
    
    if (!req.customer_id) {
        amountInput.readOnly = true;
        amountInput.classList.add('bg-light');
        if (helpTextInfo) helpTextInfo.classList.remove('d-none');
        // Walk-in: must pay full — hide Pay Later
        document.getElementById('btn_pay_later').classList.add('d-none');
    } else {
        amountInput.readOnly = false;
        amountInput.classList.remove('bg-light');
        if (helpTextInfo) helpTextInfo.classList.add('d-none');
        // Named customer: show Pay Later
        document.getElementById('btn_pay_later').classList.remove('d-none');
    }
    
    var modal = new bootstrap.Modal(document.getElementById('processModal'));
    modal.show();
}

// Initial render
renderQueue(currentRequests);

// Live Polling
setInterval(() => {
    fetch('<?= BASE_URL ?>/cashier/pending')
        .then(res => res.json())
        .then(data => {
            if (JSON.stringify(data.requests) !== JSON.stringify(currentRequests)) {
                currentRequests = data.requests;
                renderQueue(currentRequests);
            }
        })
        .catch(err => console.error('Queue polling error:', err));
}, 5000);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
