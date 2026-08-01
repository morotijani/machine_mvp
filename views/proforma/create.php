<?php
$title = "New Pro Forma Invoice";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">New Pro Forma Invoice</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/proformas" class="btn btn-sm btn-outline-secondary rounded-pill">View Previous Pro Formas
                    >></a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Item Selection -->
            <div class="col-md-7">
                <div class="card shadow-sm mb-3">
                    <!-- Removed Local Search Header -->
                    <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                        <div class="list-group list-group-flush" id="itemList">
                            <?php foreach ($items as $item): ?>
                                <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center item-row"
                                    data-id="<?= $item['id'] ?>" data-name="<?= e($item['name']) ?>"
                                    data-price="<?= $item['price'] ?>" data-stock="<?= $item['quantity'] ?>">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?= BASE_URL ?>/<?php echo $item['image_path']; ?>" alt="Item"
                                                class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded me-3 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-secondary small"
                                                style="width: 40px; height: 40px;">Img</div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= e($item['name']) ?></div>
                                            <small class="text-muted"><?= e($item['sku']) ?> | Stock:
                                                <?= $item['quantity'] ?></small>
                                        </div>
                                    </div>
                                    <span
                                        class="badge bg-primary rounded-pill">₵<?php echo number_format($item['price'], 2); ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm" style="border-radius: 28px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="font-size: 24px; color: #1f1f1f; font-weight: 400;">Current Order</h4>
                        <div class="mb-4">
                            <label
                                class="form-label d-flex justify-content-between text-muted fw-bold small text-uppercase">
                                Select Customer
                                <span class="text-primary" style="font-size: 0.7rem;">(Optional for walk-in)</span>
                            </label>
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <select id="customerSelect" class="form-select border-0 bg-light py-2 px-3"
                                    style="box-shadow: none;">
                                    <option value="">-- Walk-in Customer --</option>
                                    <?php foreach ($customers as $cx): ?>
                                        <option value="<?= $cx['id'] ?>"><?= e($cx['name'] . ' (' . $cx['phone'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-primary px-3 fw-medium border-0" type="button"
                                    data-bs-toggle="modal" data-bs-target="#quickAddCustomerModal"
                                    title="Quick Add Customer">
                                    <span class="material-symbols-outlined align-middle"
                                        style="font-size: 18px;">person_add</span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div
                                class="input-group shadow-sm rounded-pill overflow-hidden border border-primary border-2">
                                <span class="input-group-text bg-primary text-white border-0 px-3">
                                    <span class="material-symbols-outlined fs-5 align-middle">barcode_scanner</span>
                                </span>
                                <input type="text" id="barcodeInput" class="form-control py-2 border-0 fw-medium"
                                    placeholder="SCAN BARCODE HERE..." autofocus style="box-shadow: none;">
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted" style="font-size: 0.75rem;">Scanner will auto-add item to
                                    cart.</small>
                            </div>
                        </div>

                        <div class="table-responsive mb-4 rounded-3 border"
                            style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3 border-bottom-0">Item</th>
                                        <th style="width: 80px;" class="border-bottom-0">Qty</th>
                                        <th class="text-end border-bottom-0">Total</th>
                                        <th class="border-bottom-0"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody" class="border-top-0">
                                    <!-- JS will populate -->
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 bg-light rounded-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted fw-bold">Total Order:</span>
                                <span class="fw-bold fs-3 text-primary" id="cartTotal">₵0.00</span>
                            </div>
                            <div class="d-none">
                                <!-- Removed amount paid for proformas -->
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted fw-bold small text-uppercase">Notes (Optional)</label>
                            <textarea id="proformaNotes" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="2" style="box-shadow: none;" placeholder="Add any special conditions, delivery notes, etc..."></textarea>
                        </div>
                        <div class="d-grid mt-2">
                            <button id="btnCompleteSale" class="btn btn-primary btn-lg rounded-pill fw-medium"
                                style="padding: 12px 24px;">
                                Generate Pro Forma Invoice
                            </button>
                        </div>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="modal-title-custom mb-0">Quick Add Customer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddCustomerForm">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Name</label>
                        <input type="text" id="new_cx_name" class="form-control rounded-pill bg-light border-0 px-3 py-2" style="box-shadow: none;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Phone</label>
                        <input type="text" id="new_cx_phone" class="form-control rounded-pill bg-light border-0 px-3 py-2" style="box-shadow: none;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase">Address</label>
                        <textarea id="new_cx_address" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="2" style="box-shadow: none;"></textarea>
                    </div>
                </form>
                <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
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
</style>

<!-- Out of Stock Modal -->
<div class="modal fade material-modal" id="outOfStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom">Out of stock</h4>
                <p class="modal-text" id="outOfStockModalText">This item currently has no available stock. Please choose another item.</p>
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

<!-- Empty Cart Modal -->
<div class="modal fade material-modal" id="emptyCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom">Cart is empty</h4>
                <p class="modal-text">Cannot generate an empty pro forma invoice. Please add items to the cart.</p>
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
                <p class="modal-text">No customer is selected. Are you sure you want to proceed with this sale as a
                    Walk-in customer?</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmWalkInBtn" class="btn btn-ok">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN = '<?= csrf_token() ?>';
    const items = <?php echo json_encode($items); ?>;
    const cart = [];

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
    // End Global Search Integration

    /* Old event listener was here */
    /*
    document.getElementById('itemSearch').addEventListener('input', function(e) {
        ...
    });
    */

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
            if (!['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON'].includes(e.target.tagName)) {
                barcodeInput.focus();
            }
        });
    }

    function findAndAddItemBySku(sku) {
        // Show a small loader or feedback if needed
        fetch('<?= BASE_URL ?>/api/items/find-by-sku?sku=' + encodeURIComponent(sku))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = data.item;
                    addItemToCart(item.id, item.name, parseFloat(item.price), parseFloat(item.quantity));

                    // Optional: Play a "beep" sound or show success toast
                    console.log('Added via barcode:', item.name);
                } else {
                    alert('Item not found for SKU: ' + sku);
                }
            })
            .catch(err => {
                console.error('Barcode error:', err);
                alert('Error scanning barcode');
            });
    }

    function addItemToCart(id, name, price, stock) {
        if (stock <= 0) {
            document.getElementById('outOfStockModalText').textContent = 'Warning: ' + name + ' is currently out of stock. It will still be added to the quote.';
            const oosModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('outOfStockModal'));
            oosModal.show();
        }

        const existing = cart.find(i => i.id == id); // Loose equal for ID types
        if (existing) {
            if (existing.quantity >= stock && stock > 0) {
                document.getElementById('maxStockModalText').textContent = 'Warning: You are quoting more than the available stock (' + stock + ') for ' + name + '.';
                const msModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('maxStockModal'));
                msModal.show();
            }
            existing.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1, max: stock });
        }
        renderCart();
    }

    // Refactor list click to use the same addItemToCart function
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

        cart.forEach((item, index) => {
            const lineTotal = item.price * item.quantity;
            total += lineTotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td><small>${item.name}</small></td>
            <td><input type="number" class="form-control form-control-sm qty-input" min="1" value="${item.quantity}" data-index="${index}"></td>
            <td class="text-end">₵${lineTotal.toFixed(2)}</td>
            <td class="text-end"><button class="btn btn-sm btn-link text-danger remove-btn" data-index="${index}">&times;</button></td>
        `;
            tbody.appendChild(tr);
        });

        document.getElementById('cartTotal').textContent = '₵' + total.toFixed(2);
    }

    document.getElementById('cartTableBody').addEventListener('change', (e) => {
        if (e.target.classList.contains('qty-input')) {
            const idx = e.target.dataset.index;
            let val = parseInt(e.target.value);
            if (val < 1) val = 1;
            if (cart[idx].max > 0 && val > cart[idx].max) {
                document.getElementById('maxStockModalText').textContent = 'Warning: You are quoting more than the available stock (' + cart[idx].max + ') for ' + cart[idx].name + '.';
                const msModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('maxStockModal'));
                msModal.show();
            }
            cart[idx].quantity = val;
            renderCart();
        }
    });

    document.getElementById('cartTableBody').addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-btn')) {
            const idx = e.target.dataset.index;
            cart.splice(idx, 1);
            renderCart();
        }
    });

    function submitSalePayload() {
        const btn = document.getElementById('btnCompleteSale');
        const customerId = document.getElementById('customerSelect').value;
        const total = parseFloat(document.getElementById('cartTotal').textContent.replace('₵', ''));
        const notes = document.getElementById('proformaNotes').value;
        const originalText = btn.innerHTML;

        // Disable button to prevent duplicate clicks
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        const data = {
            customer_id: customerId || null,
            items: cart.map(i => ({ item_id: i.id, quantity: i.quantity, price: i.price })),
            total: total,
            notes: notes
        };

        fetch('<?= BASE_URL ?>/proformas/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                window.location.href = result.redirect;
            } else {
                alert('Error: ' + result.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    document.getElementById('btnCompleteSale').addEventListener('click', function () {
        if (cart.length === 0) {
            const emptyModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('emptyCartModal'));
            emptyModal.show();
            return;
        }

        const customerId = document.getElementById('customerSelect').value;
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

    // Quick Add Customer Logic
    document.getElementById('saveNewCustomerBtn').addEventListener('click', function () {
        const name = document.getElementById('new_cx_name').value;
        const phone = document.getElementById('new_cx_phone').value;
        const address = document.getElementById('new_cx_address').value;

        if (!name.trim()) {
            alert('Customer Name is required');
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
                    // Add to dropdown
                    const select = document.getElementById('customerSelect');
                    const option = document.createElement('option');
                    option.value = data.customer.id;
                    option.text = data.customer.name + ' (' + data.customer.phone + ')';
                    select.add(option);

                    // Select it
                    select.value = data.customer.id;

                    // Close modal & Reset form
                    document.getElementById('quickAddCustomerForm').reset();
                    const modalEl = document.getElementById('quickAddCustomerModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    // Notify
                    // alert('Customer added and selected!');
                    // Notify
                    // alert('Customer added and selected!');
                } else {
                    alert(data.message || 'Error adding customer');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error communicating with server');
            });
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>