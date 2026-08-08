<?php 
$title = "Cashier Dashboard | Live Queue";
ob_start();
?>

<style>
    /* Google Aesthetics */
    .google-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .google-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px 0 rgba(60, 64, 67, 0.15) !important;
    }

    .queue-board {
        min-height: 50vh;
        background: transparent;
        padding: 10px 0;
    }

    .ticket-card {
        animation: slideIn 0.3s ease-out;
        margin-bottom: 24px;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal Styling */
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
    .material-modal .btn-ok:hover {
        background-color: #0842a0;
        color: #fff;
    }

    /* Input inside Process Modal */
    .google-input {
        border: 1px solid #dadce0;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 16px;
        color: #202124;
        width: 100%;
        outline: none;
        transition: border 0.2s;
    }
    .google-input:focus {
        border-color: #1a73e8;
        border-width: 2px;
        padding: 13px 15px; /* offset border */
    }

    .ticket-type-badge {
        background-color: #e8f0fe;
        color: #1967d2;
        border-radius: 12px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .ticket-type-badge.debt {
        background-color: #e6f4ea;
        color: #137333;
    }

    .pill-btn {
        border-radius: 24px;
        font-weight: 500;
        padding: 8px 24px;
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
    .google-alert.alert-danger {
        background-color: #fce8e6;
        color: #c5221f;
        border-color: #fad2cf;
    }
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-4">
    <h1 class="h3 mb-0 d-flex align-items-center gap-2" style="color: #1f1f1f; font-weight: 400;">
        <span class="material-symbols-outlined text-primary fs-3">point_of_sale</span>
        Cashier Dashboard
    </h1>
    <div class="d-flex align-items-center gap-3">
        <!-- Sound Toggle -->
        <button id="soundToggleBtn" class="btn btn-light rounded-pill border shadow-sm d-flex align-items-center gap-2 px-3 py-2" onclick="toggleSound()">
            <span class="material-symbols-outlined text-muted" id="soundIcon">notifications_off</span>
            <span class="small fw-bold text-muted" id="soundText">Sound Off</span>
        </button>

        <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white border" style="border-radius: 20px;">
            <div class="spinner-grow text-success spinner-grow-sm" style="width: 1rem; height: 1rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="text-success fw-bold small">Live Queue</span>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="google-alert shadow-sm">
        <span class="material-symbols-outlined text-success me-2">check_circle</span>
        <span><?= $_SESSION['success'] ?></span>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="google-alert alert-danger shadow-sm">
        <span class="material-symbols-outlined text-danger me-2">error</span>
        <span><?= $_SESSION['error'] ?></span>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="queue-board" id="queue-container">
            <!-- Tickets injected here -->
            <div id="empty-state" class="text-center text-muted py-5 <?= count($pendingRequests) > 0 ? 'd-none' : '' ?>">
                <span class="material-symbols-outlined fs-1 opacity-50 mb-2" style="font-size: 64px !important;">inbox</span>
                <h5 class="fw-bold mt-3">Queue is clear!</h5>
                <p>No pending payment requests right now.</p>
            </div>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div class="modal fade material-modal" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="modal-title-custom mb-0 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">payments</span>
                        Process Payment
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="processForm" action="<?= BASE_URL ?>/cashier/process" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="request_id" id="modal_request_id">
                    
                    <div class="p-3 bg-light rounded-4 mb-4 d-flex justify-content-between align-items-center border">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 13px;">Reference</span>
                        <span class="badge bg-primary fs-6 rounded-pill px-3 py-2" id="modal_reference"></span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase mb-2">Amount Due / Requested (₵)</label>
                        <input type="number" name="amount" id="modal_amount" class="google-input fw-bold text-success fs-4" step="0.01" min="0" required>
                        <div id="walkin_help_text" class="form-text text-danger mt-2 d-none">
                            <span class="material-symbols-outlined align-middle" style="font-size: 16px;">info</span> Walk-in customers must pay the exact requested amount.
                        </div>
                    </div>

                    <!-- Action input for submit buttons -->
                    <input type="hidden" name="action" id="modal_action" value="approve">

                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-ok fs-6 py-2 d-flex justify-content-center align-items-center gap-2" onclick="submitProcessForm('approve')">
                            <span class="material-symbols-outlined">task_alt</span> Endorse & Receive Cash
                        </button>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <button type="button" id="btn_pay_later" class="btn btn-outline-warning pill-btn w-100 d-none d-flex align-items-center justify-content-center gap-1"
                                    onclick="document.getElementById('modal_amount').value = '0'; submitProcessForm('approve');">
                                    <span class="material-symbols-outlined fs-5">schedule</span> Pay Later
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-danger pill-btn w-100 d-flex align-items-center justify-content-center gap-1" onclick="showRejectConfirm()">
                                    <span class="material-symbols-outlined fs-5">cancel</span> Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirm Modal -->
<div class="modal fade material-modal" id="rejectConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="material-symbols-outlined text-danger me-2" style="font-size: 28px;">cancel</span>
                    <h4 class="modal-title-custom mb-0 text-danger">Reject Request</h4>
                </div>
                <p class="modal-text">Are you sure you want to reject this request? The salesperson will have to create a new one.</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-ok bg-danger text-white" onclick="submitProcessForm('reject')">Confirm Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>


<template id="ticket-template">
    <div class="google-card ticket-card mb-4">
        <div class="p-4">
            <div class="row align-items-center">
                <div class="col-md-3 mb-3 mb-md-0">
                    <span class="ticket-type-badge t-type mb-2">Type</span>
                    <h4 class="fw-bold text-dark mb-0 t-ref" style="font-size: 20px;">#000</h4>
                </div>
                <div class="col-md-3 mb-3 mb-md-0 border-start ps-md-4">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Customer</div>
                    <div class="fw-medium text-dark t-customer text-truncate fs-6">Walk-in</div>
                    <div class="small text-muted mt-1 text-truncate d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 14px;">person</span> 
                        <span class="t-creator">Salesperson</span>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0 border-start ps-md-4">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                    <div class="fw-medium text-dark t-date-time text-truncate fs-6">Date Time</div>
                    <div class="small text-danger mt-1 fw-bold t-time-ago d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span> <span class="t-time-val">0 mins ago</span>
                    </div>
                </div>
                <div class="col-md-3 d-flex flex-column align-items-md-end justify-content-center border-start ps-md-4">
                    <div class="text-muted small text-uppercase fw-bold mb-1 text-md-end w-100">Amount Due</div>
                    <h3 class="text-success fw-bold mb-3 t-amount text-md-end w-100">₵0.00</h3>
                    <button class="btn btn-primary pill-btn btn-process w-100">Process Payment</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
let currentRequests = <?= json_encode($pendingRequests) ?>;
let soundEnabled = false;

// Notification Sound functionality using Web Audio API
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
function playNotificationSound() {
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    const oscillator = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    
    oscillator.type = 'sine';
    oscillator.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
    oscillator.frequency.exponentialRampToValueAtTime(1046.50, audioCtx.currentTime + 0.1); // C6
    
    gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
    gainNode.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.05);
    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.5);
    
    oscillator.start(audioCtx.currentTime);
    oscillator.stop(audioCtx.currentTime + 0.5);
}

function toggleSound() {
    soundEnabled = !soundEnabled;
    const btn = document.getElementById('soundToggleBtn');
    const icon = document.getElementById('soundIcon');
    const text = document.getElementById('soundText');
    
    if (soundEnabled) {
        icon.textContent = 'notifications_active';
        icon.classList.replace('text-muted', 'text-primary');
        text.textContent = 'Sound On';
        text.classList.replace('text-muted', 'text-primary');
        
        // Play a test sound to initialize audio context
        playNotificationSound();
    } else {
        icon.textContent = 'notifications_off';
        icon.classList.replace('text-primary', 'text-muted');
        text.textContent = 'Sound Off';
        text.classList.replace('text-primary', 'text-muted');
    }
}

function parseDbDate(dateString) {
    if (!dateString) return new Date();
    const t = dateString.split(/[- :]/);
    return new Date(t[0], t[1]-1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
}

function timeAgo(dateString) {
    const d = parseDbDate(dateString);
    const now = new Date();
    const seconds = Math.max(0, Math.floor((now - d) / 1000));
    
    if (seconds < 60) {
        if (seconds <= 5) return "just now";
        return seconds + " secs ago";
    }
    
    if (seconds >= 31536000) {
        const years = Math.floor(seconds / 31536000);
        return years + " year" + (years > 1 ? "s" : "") + " ago";
    }
    
    if (seconds >= 2592000) {
        const months = Math.floor(seconds / 2592000);
        return months + " month" + (months > 1 ? "s" : "") + " ago";
    }
    
    if (seconds >= 86400) {
        const days = Math.floor(seconds / 86400);
        const hrs = Math.floor((seconds % 86400) / 3600);
        let res = days + " day" + (days > 1 ? "s" : "");
        if (hrs > 0) res += " " + hrs + " hr" + (hrs > 1 ? "s" : "");
        return res + " ago";
    }
    
    if (seconds >= 3600) {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        let res = hrs + " hr" + (hrs > 1 ? "s" : "");
        if (mins > 0) res += " " + mins + " min" + (mins > 1 ? "s" : "");
        return res + " ago";
    }
    
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    let res = mins + " min" + (mins > 1 ? "s" : "");
    if (secs > 0) res += " " + secs + " sec" + (secs > 1 ? "s" : "");
    return res + " ago";
}

function formatDateTime(dateString) {
    const d = parseDbDate(dateString);
    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

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
        
        card.setAttribute('data-created-at', req.created_at || '');
        
        let typeStr = 'Sale Invoice';
        if (req.type === 'debt_single') typeStr = 'Settle Invoice Debt';
        if (req.type === 'debt_bulk') typeStr = 'Bulk Debt Repayment';
        
        if (req.type.includes('debt')) {
            clone.querySelector('.t-type').classList.add('debt');
            clone.querySelector('.t-type').textContent = typeStr;
        } else {
            clone.querySelector('.t-type').textContent = typeStr;
        }

        clone.querySelector('.t-ref').textContent = req.type === 'debt_bulk' ? 'Customer #' + req.reference_id : 'Invoice #' + req.reference_id;
        clone.querySelector('.t-customer').textContent = req.customer_name || 'Walk-in Customer';
        clone.querySelector('.t-creator').textContent = req.creator_name;
        clone.querySelector('.t-amount').textContent = '₵' + parseFloat(req.amount_due).toFixed(2);
        
        if (req.created_at) {
            clone.querySelector('.t-date-time').textContent = formatDateTime(req.created_at);
            clone.querySelector('.t-time-val').textContent = timeAgo(req.created_at);
        }

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
        amountInput.style.backgroundColor = '#f1f3f4';
        if (helpTextInfo) helpTextInfo.classList.remove('d-none');
        document.getElementById('btn_pay_later').classList.add('d-none');
    } else {
        amountInput.readOnly = false;
        amountInput.style.backgroundColor = 'transparent';
        if (helpTextInfo) helpTextInfo.classList.add('d-none');
        document.getElementById('btn_pay_later').classList.remove('d-none');
    }
    
    var modal = new bootstrap.Modal(document.getElementById('processModal'));
    modal.show();
}

function submitProcessForm(actionType) {
    document.getElementById('modal_action').value = actionType;
    document.getElementById('processForm').submit();
}

function showRejectConfirm() {
    var rejectModal = new bootstrap.Modal(document.getElementById('rejectConfirmModal'));
    rejectModal.show();
}

// Initial render
renderQueue(currentRequests);

// Live Time Updater
setInterval(() => {
    document.querySelectorAll('.ticket-card').forEach(card => {
        const dateStr = card.getAttribute('data-created-at');
        if (dateStr) {
            const timeAgoEl = card.querySelector('.t-time-val');
            if (timeAgoEl) timeAgoEl.textContent = timeAgo(dateStr);
        }
    });
}, 60000);

// Live Polling
setInterval(() => {
    fetch('<?= BASE_URL ?>/cashier/pending')
        .then(res => res.json())
        .then(data => {
            if (JSON.stringify(data.requests) !== JSON.stringify(currentRequests)) {
                // Check if there are new requests that were not in the queue previously
                const currentIds = currentRequests.map(r => r.id);
                const hasNew = data.requests.some(r => !currentIds.includes(r.id));
                
                currentRequests = data.requests;
                renderQueue(currentRequests);
                
                if (hasNew && soundEnabled) {
                    playNotificationSound();
                }
            }
        })
        .catch(err => console.error('Queue polling error:', err));
}, 5000);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
