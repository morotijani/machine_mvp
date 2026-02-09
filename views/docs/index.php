<?php
$title = "System Documentation";
ob_start();
?>

<div class="row justify-content-center pt-4">
    <div class="col-lg-10 col-xl-9">
        <!-- Header Section -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5, #0ea5e9);">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold text-white mb-2">POS System Guide</h1>
                        <p class="lead text-white-50 mb-0">Learn how to master the tools and streamline your business operations.</p>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <span class="material-symbols-outlined text-white" style="font-size: 100px; opacity: 0.2;">menu_book</span>
                    </div>
                </div>
            </div>
        </div>

        <nav id="docs-navbar" class="navbar sticky-top bg-white border-bottom mb-5 px-3 rounded shadow-sm">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item"><a class="nav-link active py-1 px-3" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link py-1 px-3" href="#sales-role">Sales Guide</a></li>
                <li class="nav-item"><a class="nav-link py-1 px-3" href="#admin-role">Admin Guide</a></li>
                <li class="nav-item"><a class="nav-link py-1 px-3" href="#finance">Financials</a></li>
                <li class="nav-item"><a class="nav-link py-1 px-3" href="#advanced">Advanced</a></li>
            </ul>
        </nav>

        <section id="about" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-primary-subtle p-2 rounded-circle">
                    <span class="material-symbols-outlined text-primary">info</span>
                </div>
                <h2 class="fw-bold mb-0">About the System</h2>
            </div>
            <div class="card border-0 shadow-sm p-4">
                <p>Welcome to your integrated Point of Sale and Inventory Management system. This platform is designed to provide real-time tracking of sales, inventory, and staff performance with high precision.</p>
                <div class="row g-4 mt-2">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <span class="material-symbols-outlined text-primary mb-2">speed</span>
                            <h6 class="fw-bold">Efficiency</h6>
                            <p class="small text-muted mb-0">Optimized for fast checkout and quick inventory lookups.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <span class="material-symbols-outlined text-success mb-2">data_thresholding</span>
                            <h6 class="fw-bold">Data Integrity</h6>
                            <p class="small text-muted mb-0">Strict validation prevents duplicates and financial errors.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <span class="material-symbols-outlined text-info mb-2">monitoring</span>
                            <h6 class="fw-bold">Analytics</h6>
                            <p class="small text-muted mb-0">Deep insights into profitability and collection rates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-5 opacity-25">

        <section id="sales-role" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-success-subtle p-2 rounded-circle">
                    <span class="material-symbols-outlined text-success">shopping_cart</span>
                </div>
                <h2 class="fw-bold mb-0">Sales Role Guide</h2>
            </div>
            
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">1. Checkout Process (POS)</h5>
                    <ol class="text-muted lh-lg">
                        <li>Navigate to <span class="badge bg-light text-dark border">Point of Sale</span>.</li>
                        <li>Search and select a customer (use <span class="text-primary fw-bold">+</span> to add new ones instantly).</li>
                        <li>Add items to the cart. Stock levels are validated automatically.</li>
                        <li>Enter the <span class="fw-bold">Amount Paid</span>. The system tracks remaining balance as <strong>Debt</strong>.</li>
                        <li>Complete sale to generate an invoice.</li>
                    </ol>
                    <div class="alert alert-info mt-3 small d-flex gap-2 align-items-center">
                        <span class="material-symbols-outlined">lightbulb</span>
                        <span>Stock cannot be sold if quantity is insufficient. Contact your admin for replenishment.</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">2. Collections & Returns</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <h6 class="fw-bold"><span class="material-symbols-outlined align-middle fs-6">payments</span> Recording Payments</h6>
                            <p class="small text-muted">Found an invoice with pending debt? Click "Record Payment" to update the balance. This money is tracked as <strong>Debt Recovered</strong> on your dashboard.</p>
                        </li>
                        <li>
                            <h6 class="fw-bold"><span class="material-symbols-outlined align-middle fs-6">assignment_return</span> Processing Returns</h6>
                            <p class="small text-muted">Items returned by customers are restored to stock, and the invoice total is adjusted automatically. This prevents "phantom sales" in your records.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <hr class="my-5 opacity-25">

        <section id="admin-role" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-dark p-2 rounded-circle">
                    <span class="material-symbols-outlined text-white">shield_person</span>
                </div>
                <h2 class="fw-bold mb-0">Admin Role Guide</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Inventory Mastery</h6>
                            <p class="small text-muted">Admins have full control over items. Use <strong>Bundles</strong> to group items that are sold together (e.g., a "Combo Kit"). Bundle sales correctly deduct quantities from all sub-items.</p>
                            <span class="badge bg-warning-subtle text-warning border px-2 py-1">Low Stock Alerts</span>
                            <p class="mt-2 small text-muted">Dashboard monitors items below threshold (5 units) to help you restock proactively.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Staff & Performance</h6>
                            <p class="small text-muted">Navigate to <span class="text-primary fw-bold">Staff Performance</span> to see individual dealer stats. You can view <strong>Live Performance</strong> cards (Sales, Revenue, Profit, Expenses) and generate printable PDF-style reports.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-dark text-white p-4">
                        <h6 class="fw-bold mb-3 text-info">Financial Safety (Recycle Bin)</h6>
                        <p class="small text-white-50">Sales cannot be deleted instantly. Staff must "Request Delete". Admins review these in the <strong>Recycle Bin</strong> to Approve (restoring stock) or Reject the request.</p>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-5 opacity-25">

        <section id="finance" class="mb-5 pt-3 pb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-info-subtle p-2 rounded-circle">
                    <span class="material-symbols-outlined text-info">calculate</span>
                </div>
                <h2 class="fw-bold mb-0">Financial Formulae</h2>
            </div>
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Metric</th>
                                <th>Definition / Formula</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 fw-bold">Gross Profit</td>
                                <td><code>Total Sales - (Cost Price × Quantity)</code></td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Net Today</td>
                                <td><code>Gross Profit - Today's Expenses</code></td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Debt Recovered</td>
                                <td>Payments made today for invoices generated in the past.</td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Realized GP</td>
                                <td><code>(Cash Collected / Total Sales) × Potential Gross Profit</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="advanced" class="mb-5 pt-3 pb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-warning-subtle p-2 rounded-circle">
                    <span class="material-symbols-outlined text-warning">new_releases</span>
                </div>
                <h2 class="fw-bold mb-0">Advanced Features</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 d-flex align-items-center"><span class="material-symbols-outlined text-primary me-2">barcode_scanner</span> Barcode Support</h5>
                            <p class="text-muted small">The system now supports lightning-fast item entry using scanners.</p>
                            <ul class="small mb-0">
                                <li>Scan barcodes on <strong>POS</strong> to add items instantly.</li>
                                <li><strong>Scannable Receipts:</strong> Invoices now include a barcode of the Receipt ID. Scan this in the <strong>Sales History</strong> search box to find an order instantly.</li>
                                <li>Visual Code128 barcodes auto-generated for all SKUs.</li>
                                <li>Print barcodes directly from the <strong>Stock List</strong>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 d-flex align-items-center"><span class="material-symbols-outlined text-success me-2">analytics</span> Reporting & Exports</h5>
                            <p class="text-muted small">Deep data insights and external compatibility.</p>
                            <ul class="small mb-0">
                                <li><strong>Top Selling:</strong> View lists by volume and revenue.</li>
                                <li><strong>CSV Export:</strong> Download any report for Excel/Google Sheets.</li>
                                <li><strong>Customer Retention:</strong> The percentage of your customers who return to make a second purchase. A higher rate means better loyalty.</li>
                                <li><strong>Inventory Turnover:</strong> Shows how many times you sell through your entire stock value in a year. Higher turnover usually means high efficiency.</li>
                                <li><strong>Realized Profit:</strong> Accurate tracking of actual cash performance.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    body {
        scroll-behavior: smooth;
        background-color: #f8fafc;
    }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    #docs-navbar .nav-link {
        color: #64748b;
        font-weight: 500;
        transition: all 0.2s;
    }
    #docs-navbar .nav-link:hover {
        background-color: #f1f5f9;
        color: #4f46e5;
    }
    #docs-navbar .nav-link.active {
        background-color: #4f46e5 !important;
        color: white !important;
    }
    .badge {
        font-weight: 500;
    }
</style>

<script>
    // Smooth scrolling for docs navbar
    document.querySelectorAll('#docs-navbar a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            window.scrollTo({
                top: target.offsetTop - 100,
                behavior: 'smooth'
            });
            
            // Set active class
            document.querySelectorAll('#docs-navbar a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
