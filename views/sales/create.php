<?php
$title = "New Sale (POS)";
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
    .google-btn-success {
        background: #137333;
        border: none;
        color: #fff;
        padding: 12px 24px;
        border-radius: 24px;
        font-weight: 500;
        font-size: 16px;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    .google-btn-success:hover {
        background: #0d5023;
        color: #fff;
    }
    .google-input {
        background: #f1f3f4;
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 10px 16px;
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
    .google-input-pill {
        border-radius: 24px;
    }
    .item-row {
        border-left: none;
        border-right: none;
        border-radius: 0 !important;
        border-bottom: 1px solid #f1f3f4;
        padding: 12px 20px;
        transition: background 0.2s;
    }
    .item-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .item-row:last-child {
        border-bottom: none;
    }
    .cart-table th {
        font-size: 12px;
        text-transform: uppercase;
        color: #5f6368;
        font-weight: 600;
        border-bottom: 1px solid #e3e3e3;
        padding: 12px 16px;
        background: #fff;
    }
    .cart-table td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #dadce0;
        padding: 4px;
        font-size: 14px;
    }
    .qty-input:focus {
        border-color: #0b57d0;
        outline: none;
    }
    .remove-btn {
        color: #5f6368;
        text-decoration: none;
        padding: 4px;
        border-radius: 50%;
    }
    .remove-btn:hover {
        background: #fce8e6;
        color: #c5221f;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-4 gap-2">
            <h1 class="h3 mb-0 fw-normal" style="color: #1f1f1f;">New Sale</h1>
            <div>
                <a href="<?= BASE_URL ?>/sales" class="google-btn-secondary gap-1 border-0 shadow-sm bg-white">
                    Sales History <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Item Selection -->
            <div class="col-md-7">
                <div class="google-table-card">
                    <!-- Global Search handles filtering, so we just display the list cleanly -->
                    <div class="p-3" style="border-bottom: 1px solid #e3e3e3; background: #f8f9fa;">
                        <h6 class="mb-0 fw-medium text-muted d-flex align-items-center gap-2" style="font-size: 13px; text-transform: uppercase;">
                            <span class="material-symbols-outlined fs-6">inventory_2</span> Select Items from Inventory
                        </h6>
                    </div>
                    <div style="max-height: 600px; overflow-y: auto;">
                        <div class="list-group list-group-flush" id="itemList">
                            <?php foreach ($items as $item): ?>
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center item-row"
                                    data-id="<?= $item['id'] ?>" data-name="<?= e($item['name']) ?>"
                                    data-price="<?= $item['price'] ?>" data-stock="<?= $item['quantity'] ?>">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?= BASE_URL ?>/<?php echo $item['image_path']; ?>" alt="Item"
                                                class="me-3" style="width: 48px; height: 48px; object-fit: cover; border-radius: 12px; border: 1px solid #e3e3e3;">
                                        <?php else: ?>
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center text-muted"
                                                style="width: 48px; height: 48px; border-radius: 12px; border: 1px solid #e3e3e3;">
                                                <span class="material-symbols-outlined">image</span>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-medium text-dark" style="font-size: 15px;"><?= e($item['name']) ?></div>
                                            <div class="text-muted d-flex gap-2 mt-1" style="font-size: 12px;">
                                                <span><?= e($item['sku']) ?></span>
                                                <span>•</span>
                                                <span class="<?= $item['quantity'] <= 5 ? 'text-danger fw-medium' : '' ?>">Stock: <?= $item['quantity'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge" style="background: #e8f0fe; color: #1967d2; font-size: 14px; padding: 6px 12px; border-radius: 12px; border: 1px solid #d2e3fc;">
                                        ₵<?php echo number_format($item['price'], 2); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart -->
            <div class="col-md-5">
                <div class="google-table-card h-100 d-flex flex-column">
                    <div class="p-4 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 d-flex align-items-center gap-2" style="font-size: 20px; color: #1f1f1f; font-weight: 400;">
                                <span class="material-symbols-outlined text-primary">shopping_cart</span> Current Order
                            </h4>
                            <button type="button" id="btnClearCart" class="btn btn-link text-danger p-0 text-decoration-none d-flex align-items-center gap-1" style="font-size: 14px; font-weight: 500;">
                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span> Clear Cart
                            </button>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted fw-bold small text-uppercase mb-2 d-flex justify-content-between align-items-center">
                                Customer
                                <span style="font-size: 0.7rem; font-weight: normal;">(Optional for walk-in)</span>
                            </label>
                            <div class="d-flex gap-2">
                                <select id="customerSelect" class="google-input flex-grow-1" style="padding-top: 12px; padding-bottom: 12px;">
                                    <option value="">-- Walk-in Customer --</option>
                                    <?php foreach ($customers as $cx): ?>
                                        <option value="<?= $cx['id'] ?>"><?= e($cx['name'] . ' (' . $cx['phone'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="google-btn-secondary" style="border-radius: 8px; padding: 0 16px;" type="button"
                                    data-bs-toggle="modal" data-bs-target="#quickAddCustomerModal"
                                    title="Quick Add Customer">
                                    <span class="material-symbols-outlined align-middle"
                                        style="font-size: 20px; color: #0b57d0;">person_add</span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="position-relative">
                                <span class="material-symbols-outlined position-absolute" style="left: 16px; top: 50%; transform: translateY(-50%); color: #0b57d0;">barcode_scanner</span>
                                <input type="text" id="barcodeInput" class="google-input google-input-pill w-100"
                                    placeholder="Scan barcode here..." autofocus style="padding-left: 48px; border: 2px solid #e8f0fe;">
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted" style="font-size: 0.75rem;">Scanner will auto-add item to cart.</small>
                            </div>
                        </div>

                        <div class="mb-4 rounded-3 border" style="max-height: 250px; overflow-y: auto; background: #fff;">
                            <table class="table table-borderless cart-table mb-0 w-100">
                                <thead class="sticky-top">
                                    <tr>
                                        <th>Item</th>
                                        <th style="width: 80px;" class="text-center">Qty</th>
                                        <th class="text-end">Total</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <!-- JS will populate -->
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4" style="font-size: 14px;">
                                            Cart is empty. Select items to begin.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment & Checkout Section (Sticks to bottom of card) -->
                    <div class="p-4" style="background: #f8f9fa; border-top: 1px solid #e3e3e3;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 13px;">Total Order</span>
                            <span class="fw-bold fs-3 text-primary" id="cartTotal">₵0.00</span>
                        </div>
                        
                        <div class="<?php echo ($_SESSION['role'] === 'sales') ? 'd-none' : ''; ?>">
                            <hr class="my-3 border-secondary border-opacity-25">
                            <label class="form-label text-muted fw-bold small text-uppercase">Amount Paid</label>
                            <div class="d-flex gap-2">
                                <div class="position-relative flex-grow-1">
                                    <span class="position-absolute fw-bold" style="left: 16px; top: 50%; transform: translateY(-50%); color: #137333;">₵</span>
                                    <input type="number" id="payAmount"
                                        class="google-input w-100 fw-bold" style="padding-left: 32px; font-size: 18px; color: #137333; border: 1px solid #ceead6; background: #e6f4ea;" step="0.01"
                                        value="0.00" min="0">
                                </div>
                                <button class="google-btn-secondary" style="border-radius: 8px; border: 1px solid #fad2cf; background: #fce8e6; color: #c5221f;" type="button"
                                    id="btnPayLater" title="Mark as Credit / Pay Later">Pay Later</button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button id="btnCompleteSale" class="google-btn-success d-flex gap-2">
                                <span class="material-symbols-outlined">task_alt</span>
                                <?php echo ($_SESSION['role'] === 'sales') ? 'Authorize & Send to Cashier' : 'Complete Sale & Print'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade material-modal" id="quickAddCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <span class="material-symbols-outlined text-primary me-2" style="font-size: 28px;">person_add</span>
                    <h4 class="modal-title-custom mb-0">Quick Add Customer</h4>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="google-card border border-light-subtle mb-4">
                    <form id="quickAddCustomerForm">
                        <div class="google-row p-3 border-bottom">
                            <span class="material-symbols-outlined google-icon text-muted me-3">badge</span>
                            <div class="google-content flex-grow-1">
                                <label class="google-label d-block text-dark fw-medium mb-1" style="font-size: 14px;">Full Name</label>
                                <input type="text" id="new_cx_name" class="google-input w-100 p-0 border-0" placeholder="e.g. Jane Doe" required style="outline: none; font-size: 15px; color: #5f6368; background: transparent;">
                            </div>
                        </div>
                        <div class="google-row p-3 border-bottom">
                            <span class="material-symbols-outlined google-icon text-muted me-3">call</span>
                            <div class="google-content flex-grow-1">
                                <label class="google-label d-block text-dark fw-medium mb-1" style="font-size: 14px;">Phone Number</label>
                                <input type="text" id="new_cx_phone" class="google-input w-100 p-0 border-0" placeholder="e.g. 055 123 4567" style="outline: none; font-size: 15px; color: #5f6368; background: transparent;">
                            </div>
                        </div>
                        <div class="google-row p-3 d-flex align-items-start">
                            <span class="material-symbols-outlined google-icon text-muted me-3 mt-1">location_on</span>
                            <div class="google-content flex-grow-1">
                                <label class="google-label d-block text-dark fw-medium mb-1" style="font-size: 14px;">Address</label>
                                <textarea id="new_cx_address" class="google-textarea w-100 p-0 border-0" rows="2" placeholder="Optional details..." style="outline: none; font-size: 15px; color: #5f6368; background: transparent; resize: none;"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveNewCustomerBtn" class="btn btn-ok">Add & Select</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Material Design Modal Styles -->
<style>
    .material-modal .modal-content {
        border-radius: 28px;
    }

    .material-modal .modal-title-custom {
        font-size: 24px;
        color: #1f1f1f;
        font-weight: 400;
        margin-bottom: 16px;
    }

    .material-modal .modal-text {
        font-size: 14px;
        color: #444746;
        line-height: 1.5;
        margin-bottom: 32px;
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

    .google-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
    }
    .google-row {
        display: flex;
        align-items: center;
        transition: background-color 0.2s;
    }
    .google-row:focus-within {
        background-color: #f8f9fa;
    }
    .google-input:focus, .google-textarea:focus {
        color: #202124 !important;
    }
</style>

<!-- Out of Stock Modal -->
<div class="modal fade material-modal" id="outOfStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom">Out of stock</h4>
                <p class="modal-text">This item currently has no available stock. Please choose another item.</p>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-ok" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Max Stock Modal -->
<div class="modal fade material-modal" id="maxStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom">Max stock reached</h4>
                <p class="modal-text" id="maxStockModalText">You cannot add more of this item to the cart.</p>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-ok" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Walk-in Confirm Modal -->
<div class="modal fade material-modal" id="walkInConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom">Proceed as Walk-in?</h4>
                <p class="modal-text">No customer is selected. Are you sure you want to proceed with this sale as a Walk-in customer?</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmWalkInBtn" class="btn btn-ok">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Cart Confirm Modal -->
<div class="modal fade material-modal" id="clearCartConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="material-symbols-outlined text-danger me-2" style="font-size: 28px;">delete</span>
                    <h4 class="modal-title-custom mb-0 text-danger">Clear Cart</h4>
                </div>
                <p class="modal-text">Are you sure you want to clear the entire cart? This action cannot be undone.</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmClearCartBtn" class="btn btn-ok bg-danger text-white">Clear</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generic Error Modal -->
<div class="modal fade material-modal" id="posErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="material-symbols-outlined text-danger me-2" style="font-size: 28px;">error</span>
                    <h4 class="modal-title-custom mb-0 text-danger" id="posErrorModalTitle">Action Required</h4>
                </div>
                <p class="modal-text" id="posErrorModalText">An error occurred.</p>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-ok" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const CSRF_TOKEN = '<?= csrf_token() ?>';
    const items = <?php echo json_encode($items); ?>;
    const cart = [];

    // Utility function for showing errors beautifully
    function showPosError(message, title = 'Action Required') {
        document.getElementById('posErrorModalTitle').textContent = title;
        document.getElementById('posErrorModalText').textContent = message;
        const errModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('posErrorModal'));
        errModal.show();
    }

    // GLOBAL SEARCH INTEGRATION
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.focus(); // Focus on load
        globalSearch.addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(term)) {
                    row.classList.remove('d-none');
                    row.classList.add('d-flex');
                } else {
                    row.classList.remove('d-flex');
                    row.classList.add('d-none');
                }
            });
        });
    }

    // Barcode Scanner Logic
    const barcodeInput = document.getElementById('barcodeInput');
    if (barcodeInput) {
        barcodeInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const sku = e.target.value.trim();
                if (sku) {
                    findAndAddItemBySku(sku);
                }
                e.target.value = ''; // Clear for next scan
            }
        });

        // Auto-focus barcode input when clicking anywhere outside of other inputs
        document.addEventListener('click', function (e) {
            if (!['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON', 'A'].includes(e.target.tagName)) {
                barcodeInput.focus();
            }
        });
    }

    function findAndAddItemBySku(sku) {
        fetch('<?= BASE_URL ?>/api/items/find-by-sku?sku=' + encodeURIComponent(sku))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = data.item;
                    addItemToCart(item.id, item.name, parseFloat(item.price), parseFloat(item.quantity));
                } else {
                    showPosError('Item not found for SKU: ' + sku, 'Barcode Error');
                }
            })
            .catch(err => {
                console.error('Barcode error:', err);
                showPosError('Error scanning barcode', 'Barcode Error');
            });
    }

    function addItemToCart(id, name, price, stock) {
        if (stock <= 0) {
            const oosModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('outOfStockModal'));
            oosModal.show();
            return;
        }

        const existing = cart.find(i => i.id == id);
        if (existing) {
            if (existing.quantity >= stock) {
                document.getElementById('maxStockModalText').textContent = 'You have reached the maximum available stock (' + stock + ') for this item.';
                const msModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('maxStockModal'));
                msModal.show();
                return;
            }
            existing.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1, max: stock });
        }
        renderCart();
    }

    document.getElementById('itemList').addEventListener('click', function (e) {
        const btn = e.target.closest('.item-row');
        if (!btn) return;

        addItemToCart(
            btn.dataset.id,
            btn.dataset.name,
            parseFloat(btn.dataset.price),
            parseFloat(btn.dataset.stock)
        );
    });

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        tbody.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4" style="font-size: 14px;">Cart is empty. Select items to begin.</td></tr>';
            document.getElementById('cartTotal').textContent = '₵0.00';
            
            const payInput = document.getElementById('payAmount');
            if (payInput && !payInput.parentElement.parentElement.classList.contains('d-none')) {
                payInput.value = '0.00';
            }
            return;
        }

        cart.forEach((item, index) => {
            const lineTotal = item.price * item.quantity;
            total += lineTotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>
                <div class="fw-medium text-dark" style="font-size: 14px;">${item.name}</div>
                <div class="text-muted" style="font-size: 12px;">₵${item.price.toFixed(2)}</div>
            </td>
            <td class="text-center"><input type="number" class="qty-input" min="1" max="${item.max}" value="${item.quantity}" data-index="${index}"></td>
            <td class="text-end fw-medium text-primary">₵${lineTotal.toFixed(2)}</td>
            <td class="text-end"><button class="btn btn-link text-danger remove-btn p-1" data-index="${index}"><span class="material-symbols-outlined" style="font-size: 18px;">close</span></button></td>
        `;
            tbody.appendChild(tr);
        });

        document.getElementById('cartTotal').textContent = '₵' + total.toFixed(2);

        const payInput = document.getElementById('payAmount');
        if (payInput && !payInput.parentElement.parentElement.classList.contains('d-none')) {
            payInput.value = total.toFixed(2);
        } else {
            payInput.value = total.toFixed(2);
        }
    }

    document.getElementById('cartTableBody').addEventListener('change', (e) => {
        if (e.target.classList.contains('qty-input')) {
            const idx = e.target.dataset.index;
            let val = parseInt(e.target.value);
            if (val < 1) val = 1;
            if (val > cart[idx].max) {
                val = cart[idx].max;
                document.getElementById('maxStockModalText').textContent = 'You have reached the maximum available stock (' + cart[idx].max + ') for this item.';
                const msModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('maxStockModal'));
                msModal.show();
            }
            cart[idx].quantity = val;
            renderCart();
        }
    });

    document.getElementById('cartTableBody').addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-btn');
        if (btn) {
            const idx = btn.dataset.index;
            cart.splice(idx, 1);
            renderCart();
        }
    });

    document.getElementById('btnPayLater').addEventListener('click', () => {
        document.getElementById('payAmount').value = 0;
    });

    document.getElementById('btnClearCart').addEventListener('click', () => {
        if (cart.length === 0) return;
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('clearCartConfirmModal'));
        modal.show();
    });

    document.getElementById('confirmClearCartBtn').addEventListener('click', () => {
        cart.length = 0;
        document.getElementById('customerSelect').value = '';
        renderCart();
        const modal = bootstrap.Modal.getInstance(document.getElementById('clearCartConfirmModal'));
        if (modal) modal.hide();
    });

    function submitSalePayload() {
        const btn = document.getElementById('btnCompleteSale');
        const customerId = document.getElementById('customerSelect').value;
        const payAmount = parseFloat(document.getElementById('payAmount').value);

        const payload = {
            customer_id: customerId || null,
            payment_amount: payAmount,
            items: cart.map(i => ({ id: i.id, quantity: i.quantity }))
        };

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        fetch('<?= BASE_URL ?>/sales/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '<?= BASE_URL ?>/sales/view?id=' + data.sale_id + '&success=' + encodeURIComponent('Sale successfully completed');
                } else {
                    showPosError(data.message, 'Transaction Error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                showPosError('Communication error with the server. Please try again.', 'Network Error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    }

    document.getElementById('btnCompleteSale').addEventListener('click', function () {
        const customerId = document.getElementById('customerSelect').value;
        const payAmount = parseFloat(document.getElementById('payAmount').value);
        const total = parseFloat(document.getElementById('cartTotal').textContent.replace('₵', ''));

        if (payAmount < 0) {
            showPosError('Amount Paid cannot be less than zero.', 'Invalid Payment Amount');
            return;
        }

        if (payAmount > total + 0.01) {
            showPosError('Amount Paid (₵' + payAmount.toFixed(2) + ') cannot exceed the Total Order Amount (₵' + total.toFixed(2) + ').', 'Overpayment Detected');
            return;
        }

        if (payAmount < total && !customerId) {
            showPosError('For Credit/Partial payments, you MUST select a Customer to record the debt.', 'Customer Required');
            return;
        }

        if (cart.length === 0) {
            showPosError('Your cart is empty. Please add items before checking out.', 'Empty Cart');
            return;
        }

        if (!customerId) {
            const walkInModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('walkInConfirmModal'));
            walkInModal.show();
            return;
        }

        submitSalePayload();
    });

    document.getElementById('confirmWalkInBtn').addEventListener('click', function () {
        const walkInModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('walkInConfirmModal'));
        walkInModal.hide();
        submitSalePayload();
    });

    document.getElementById('saveNewCustomerBtn').addEventListener('click', function () {
        const name = document.getElementById('new_cx_name').value;
        const phone = document.getElementById('new_cx_phone').value;
        const address = document.getElementById('new_cx_address').value;

        if (!name.trim()) {
            showPosError('Customer Name is required', 'Validation Error');
            return;
        }

        fetch('<?= BASE_URL ?>/customers/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ name, phone, address })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('customerSelect');
                    const option = document.createElement('option');
                    option.value = data.customer.id;
                    option.text = data.customer.name + ' (' + data.customer.phone + ')';
                    select.add(option);
                    select.value = data.customer.id;

                    document.getElementById('quickAddCustomerForm').reset();
                    const modalEl = document.getElementById('quickAddCustomerModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                } else {
                    showPosError(data.message || 'Error adding customer', 'Action Failed');
                }
            })
            .catch(err => {
                console.error(err);
                showPosError('Error communicating with server', 'Network Error');
            });
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>