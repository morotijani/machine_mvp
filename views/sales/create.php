<?php
$title = "New Sale (POS)";
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Sale</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-outline-secondary">History</a>
    </div>
</div>

<div class="row">
    <!-- Left Column: Item Selection -->
    <div class="col-md-7">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <input type="text" id="itemSearch" class="form-control" placeholder="Search items by name or SKU...">
            </div>
            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                <div class="list-group list-group-flush" id="itemList">
                    <?php foreach ($items as $item): ?>
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center item-row" 
                        data-id="<?php echo $item['id']; ?>" 
                        data-name="<?php echo htmlspecialchars($item['name']); ?>" 
                        data-price="<?php echo $item['price']; ?>"
                        data-stock="<?php echo $item['quantity']; ?>">
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                            <small class="text-muted"><?php echo htmlspecialchars($item['sku']); ?> | Stock: <?php echo $item['quantity']; ?></small>
                        </div>
                        <span class="badge bg-primary rounded-pill">$<?php echo number_format($item['price'], 2); ?></span>
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
                            <option value="<?php echo $cx['id']; ?>"><?php echo htmlspecialchars($cx['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-primary" type="button" onclick="location.href='<?= BASE_URL ?>/customers'">+</button>
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
                        <span class="fw-bold fs-5" id="cartTotal">$0.00</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" id="payAmount" class="form-control" step="0.01" value="0.00">
                    </div>
                    <div class="d-grid">
                        <button id="btnCompleteSale" class="btn btn-success btn-lg">Complete Sale & Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const items = <?php echo json_encode($items); ?>;
const cart = [];

document.getElementById('itemSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});

document.querySelectorAll('.item-row').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const price = parseFloat(btn.dataset.price);
        const stock = parseInt(btn.dataset.stock);

        if(stock <= 0) {
            alert('Out of stock!');
            return;
        }

        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.quantity >= stock) {
                alert('Max stock reached');
                return;
            }
            existing.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1, max: stock });
        }
        renderCart();
    });
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
            <td class="text-end">$${lineTotal.toFixed(2)}</td>
            <td class="text-end"><button class="btn btn-sm btn-link text-danger remove-btn" data-index="${index}">&times;</button></td>
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

document.getElementById('btnCompleteSale').addEventListener('click', () => {
    const customerId = document.getElementById('customerSelect').value;
    const payAmount = parseFloat(document.getElementById('payAmount').value);

    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    if (!customerId) {
        if(!confirm('No customer selected. Proceed as Walk-in? (Sales are not tracked to profiles without selection)')) {
            return; 
        }
        // Actually, schema allows null customer_id
    }

    const payload = {
        customer_id: customerId || null,
        payment_amount: payAmount,
        items: cart.map(i => ({ id: i.id, quantity: i.quantity }))
    };

    fetch('<?= BASE_URL ?>/sales/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '<?= BASE_URL ?>/sales/view?id=' + data.sale_id;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Communication error');
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
