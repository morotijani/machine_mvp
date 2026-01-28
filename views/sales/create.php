<?php
$title = "New Sale (POS)";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">New Sale</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-outline-secondary">History</a>
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
                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center item-row" 
                                data-id="<?= $item['id'] ?>" 
                                data-name="<?= e($item['name']) ?>" 
                                data-price="<?= $item['price'] ?>"
                                data-stock="<?= $item['quantity'] ?>">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= BASE_URL ?>/<?php echo $item['image_path']; ?>" alt="Item" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded me-3 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-secondary small" style="width: 40px; height: 40px;">Img</div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?= e($item['name']) ?></div>
                                        <small class="text-muted"><?= e($item['sku']) ?> | Stock: <?= $item['quantity'] ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill">₵<?php echo number_format($item['price'], 2); ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Current Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <div class="input-group">
                                <select id="customerSelect" class="form-select">
                                    <option value="">-- Select Customer --</option>
                                    <?php foreach ($customers as $cx): ?>
                                    <option value="<?= $cx['id'] ?>"><?= e($cx['name'] . ' (' . $cx['phone'] . ')') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#quickAddCustomerModal" title="Quick Add Customer">+</button>
                            </div>
                        </div>

                        <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th style="width: 70px;">Qty</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <!-- JS will populate -->
                                </tbody>
                            </table>
                        </div>

                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold fs-5" id="cartTotal">₵0.00</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount Paid</label>
                                <div class="input-group">
                                    <input type="number" id="payAmount" class="form-control" step="0.01" value="0.00">
                                    <button class="btn btn-outline-warning" type="button" id="btnPayLater" title="Mark as Credit / Pay Later">Pay Later</button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button id="btnCompleteSale" class="btn btn-success btn-lg">Complete Sale & Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickAddCustomerForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" id="new_cx_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" id="new_cx_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea id="new_cx_address" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveNewCustomerBtn" class="btn btn-primary">Add & Select</button>
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
    globalSearch.addEventListener('input', function(e) {
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

document.getElementById('itemList').addEventListener('click', function(e) {
    const btn = e.target.closest('.item-row');
    if (!btn) return;

    const id = btn.dataset.id;
    const name = btn.dataset.name;
    const price = parseFloat(btn.dataset.price);
    
    // Parse stock carefully
    let stock = parseFloat(btn.dataset.stock);
    if (isNaN(stock)) stock = 0;

    console.log('Clicked item:', name, 'Stock:', stock); // Debug log

    if(stock <= 0) {
        alert('Item is Out of Stock!');
        return;
    }

    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.quantity >= stock) {
            alert('Max stock reached (' + stock + ')');
            return;
        }
        existing.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1, max: stock });
    }
    renderCart();
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
            <td><input type="number" class="form-control form-control-sm qty-input" min="1" max="${item.max}" value="${item.quantity}" data-index="${index}"></td>
            <td class="text-end">₵${lineTotal.toFixed(2)}</td>
            <td class="text-end"><button class="btn btn-sm btn-link text-danger remove-btn" data-index="${index}">&times;</button></td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('cartTotal').textContent = '₵' + total.toFixed(2);
    document.getElementById('payAmount').value = total.toFixed(2);
}

document.getElementById('cartTableBody').addEventListener('change', (e) => {
    if (e.target.classList.contains('qty-input')) {
        const idx = e.target.dataset.index;
        let val = parseInt(e.target.value);
        if (val < 1) val = 1;
        if (val > cart[idx].max) {
             val = cart[idx].max;
             alert('Max stock is ' + cart[idx].max);
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



document.getElementById('btnPayLater').addEventListener('click', () => {
    document.getElementById('payAmount').value = 0;
});

document.getElementById('btnCompleteSale').addEventListener('click', function() {
    const btn = this;
    const customerId = document.getElementById('customerSelect').value;
    const payAmount = parseFloat(document.getElementById('payAmount').value);

    // If Pay Later (credit sale), enforce Customer selection
     const total = parseFloat(document.getElementById('cartTotal').textContent.replace('₵', ''));
    if (payAmount < total && !customerId) {
        alert('For Credit/Partial payments, you MUST select a Customer to record the debt.');
        return;
    }

    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    
    if (!customerId) {
        // Confirmation for walk-in
        if(!confirm('No customer selected. Proceed as Walk-in?')) {
            return; 
        }
    }

    const payload = {
        customer_id: customerId || null,
        payment_amount: payAmount,
        items: cart.map(i => ({ id: i.id, quantity: i.quantity }))
    };

    // Disable button to prevent duplicate clicks
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
            window.location.href = '<?= BASE_URL ?>/sales/view?id=' + data.sale_id;
        } else {
            alert('Error: ' + data.message);
            // Re-enable button on error
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Communication error');
        // Re-enable button on error
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Quick Add Customer Logic
document.getElementById('saveNewCustomerBtn').addEventListener('click', function() {
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
