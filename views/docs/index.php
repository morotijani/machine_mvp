<?php
$title = "System Documentation";
ob_start();
?>

<div class="row justify-content-center pt-4">
    <div class="col-lg-10 col-xl-9">
        
        <!-- Header Section -->
        <div class="google-card mb-4" style="background-color: #f1f3f4; padding: 48px; position: relative; overflow: hidden;">
            <div class="position-relative" style="z-index: 1;">
                <h1 class="display-5" style="color: #1f1f1f; font-weight: 400; letter-spacing: -0.5px;">POS System Guide</h1>
                <p class="lead mb-0" style="color: #444746; font-size: 18px;">Learn how to master the tools and streamline your business operations.</p>
            </div>
            <span class="material-symbols-outlined position-absolute" style="font-size: 160px; right: 40px; top: -10px; color: #fff; opacity: 0.8; user-select: none;">menu_book</span>
        </div>

        <nav id="docs-navbar" class="google-card mb-5 px-3 py-2 sticky-top" style="z-index: 1020;">
            <ul class="nav nav-pills google-nav-pills gap-2 flex-nowrap overflow-auto" style="white-space: nowrap;">
                <li class="nav-item"><a class="nav-link active" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#roles">Roles</a></li>
                <li class="nav-item"><a class="nav-link" href="#sales-role">POS Guide</a></li>
                <li class="nav-item"><a class="nav-link" href="#cashier-role">Cashier Desk</a></li>
                <li class="nav-item"><a class="nav-link" href="#admin-role">Admin Guide</a></li>
                <li class="nav-item"><a class="nav-link" href="#finance">Financials</a></li>
                <li class="nav-item"><a class="nav-link" href="#advanced">Advanced</a></li>
            </ul>
        </nav>

        <section id="about" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #0b57d0; font-size: 28px;">info</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">About the System</h2>
            </div>
            <div class="google-card p-5">
                <p style="color: #444746; font-size: 16px; line-height: 1.6;">Welcome to your integrated Point of Sale and Inventory Management system. This platform is designed to provide real-time tracking of sales, inventory, and staff performance with high precision.</p>
                <div class="row g-4 mt-2">
                    <div class="col-md-4">
                        <div style="background-color: #f8f9fa; border-radius: 24px; padding: 24px; height: 100%;">
                            <span class="material-symbols-outlined mb-2" style="color: #0b57d0; font-size: 32px;">speed</span>
                            <h6 style="color: #1f1f1f; font-weight: 500; font-size: 16px;">Efficiency</h6>
                            <p class="mb-0" style="color: #5f6368; font-size: 14px;">Optimized for fast checkout and quick inventory lookups.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background-color: #f8f9fa; border-radius: 24px; padding: 24px; height: 100%;">
                            <span class="material-symbols-outlined mb-2" style="color: #188038; font-size: 32px;">data_thresholding</span>
                            <h6 style="color: #1f1f1f; font-weight: 500; font-size: 16px;">Data Integrity</h6>
                            <p class="mb-0" style="color: #5f6368; font-size: 14px;">Strict validation prevents duplicates and financial errors.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background-color: #f8f9fa; border-radius: 24px; padding: 24px; height: 100%;">
                            <span class="material-symbols-outlined mb-2" style="color: #d93025; font-size: 32px;">monitoring</span>
                            <h6 style="color: #1f1f1f; font-weight: 500; font-size: 16px;">Analytics</h6>
                            <p class="mb-0" style="color: #5f6368; font-size: 14px;">Deep insights into profitability and collection rates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="roles" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #ea4335; font-size: 28px;">group</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">Role-Based Access</h2>
            </div>
            <div class="google-table-card">
                <div class="card-body" style="padding: 32px 32px 16px 32px;">
                    <p style="color: #444746; font-size: 16px; margin: 0;">The system features a robust 4-role architecture designed to separate responsibilities and land users where they are most productive.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Landing Page</th>
                                <th>Primary Capability</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="google-pill" style="background-color: #fce8e6; color: #d93025;">Admin</span></td>
                                <td><span style="color: #0b57d0; font-weight: 500;">Dashboard</span></td>
                                <td style="color: #444746;">Full access to all settings, users, and global financials.</td>
                            </tr>
                            <tr>
                                <td><span class="google-pill" style="background-color: #e8f0fe; color: #0b57d0;">Sales</span></td>
                                <td><span style="color: #0b57d0; font-weight: 500;">New Sale (POS)</span></td>
                                <td style="color: #444746;">Focus solely on inventory search and creating sales requests.</td>
                            </tr>
                            <tr>
                                <td><span class="google-pill" style="background-color: #e6f4ea; color: #137333;">Cashier</span></td>
                                <td><span style="color: #0b57d0; font-weight: 500;">Cashier Desk</span></td>
                                <td style="color: #444746;">Endorse payments, manage own expenditures, and print receipts.</td>
                            </tr>
                            <tr>
                                <td><span class="google-pill" style="background-color: #f1f3f4; color: #1f1f1f;">Sales & Cashier</span></td>
                                <td><span style="color: #0b57d0; font-weight: 500;">Personal Dashboard</span></td>
                                <td style="color: #444746;">A hybrid role that can both create sales and process payments.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="sales-role" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #188038; font-size: 28px;">shopping_cart</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">Sales Role Guide</h2>
            </div>
            
            <div class="google-card mb-4" style="padding: 32px;">
                <h5 style="color: #1f1f1f; font-weight: 500; font-size: 18px; margin-bottom: 24px;">1. Checkout Process (POS)</h5>
                <ol style="color: #444746; line-height: 1.8; margin-bottom: 0;">
                    <li>Navigate to <span class="google-pill" style="background: #f1f3f4; color: #1f1f1f;">Point of Sale</span>.</li>
                    <li>Search and select a customer (use <span style="color: #0b57d0; font-weight: 500;">+</span> to add new ones instantly, or proceed as <strong>Walk-in</strong>).</li>
                    <li>Add items to the cart. Stock levels are validated automatically.</li>
                    <li>Enter the <span style="font-weight: 500; color: #1f1f1f;">Amount Paid</span>. 
                        <ul style="margin-top: 8px; margin-bottom: 8px;">
                            <li><span style="color: #d93025; font-weight: 500;">Warning:</span> Negative amounts are blocked.</li>
                            <li><span style="color: #d93025; font-weight: 500;">Warning:</span> Overpayments (exceeding total) are not allowed.</li>
                            <li><strong>Credit Sales:</strong> If the customer is paying less than the total, you <strong>must</strong> select a Customer profile to record the debt.</li>
                        </ul>
                    </li>
                    <li>Complete sale. Pure Sales users send a request to the Cashier; hybrid roles complete the sale immediately.</li>
                </ol>
                <div class="google-alert" style="background-color: #e8f0fe; color: #0b57d0; margin-top: 24px; margin-bottom: 0;">
                    <span class="material-symbols-outlined">lightbulb</span>
                    <span>Stock cannot be sold if quantity is insufficient. Contact your admin for replenishment.</span>
                </div>
            </div>

            <div class="google-card" style="padding: 32px;">
                <h5 style="color: #1f1f1f; font-weight: 500; font-size: 18px; margin-bottom: 24px;">2. Collections, Returns & Expenses</h5>
                <ul class="list-unstyled mb-0">
                    <li style="margin-bottom: 24px;">
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #0b57d0; font-size: 20px;">payments</span> Recording Payments
                        </h6>
                        <p style="color: #444746; font-size: 14px; margin: 0; line-height: 1.6;">Found an invoice with pending debt? Click "Record Payment" to update the balance. This money is tracked as <strong>Debt Recovered</strong> on your dashboard.</p>
                    </li>
                    <li style="margin-bottom: 24px;">
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #ea4335; font-size: 20px;">assignment_return</span> Processing Returns
                        </h6>
                        <p style="color: #444746; font-size: 14px; margin: 0; line-height: 1.6;">Items returned by customers are restored to stock, and the invoice total is adjusted automatically. You can track all returns processed today in the <strong>Today's Returned Items</strong> list at the bottom of the dashboard.</p>
                    </li>
                    <li style="margin-bottom: 24px;">
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #188038; font-size: 20px;">speed</span> Bulk Debt Repayment (Smart Allocation)
                        </h6>
                        <p style="color: #444746; font-size: 14px; margin: 0; line-height: 1.6;">A customer has 5 different invoices with debt? No problem. Use the <strong style="color: #188038;">Bulk Repayment</strong> button on their profile. Enter the total cash they brought, and the system will automatically settle the <strong>oldest invoices first</strong> (FIFO) until the money runs out. This saves you from recording payments for each invoice manually.</p>
                    </li>
                    <li style="margin-bottom: 24px;">
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #0b57d0; font-size: 20px;">history_edu</span> Purchase Timeline & Debt History
                        </h6>
                        <p style="color: #444746; font-size: 14px; margin: 0; line-height: 1.6;">The customer profile now feature a <strong>Chronological Timeline</strong>. Every purchase and every debt repayment is recorded as a standalone event on the "thread". This gives you and the customer a transparent record of all financial activity over time.</p>
                    </li>
                    <li>
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #f9ab00; font-size: 20px;">payments</span> Personal Expenditures
                        </h6>
                        <p style="color: #444746; font-size: 14px; margin: 0; line-height: 1.6;"><strong>Cashiers</strong> and <strong>Sales/Cashiers</strong> can now record their own business-related expenses. These are subtracted from their net performance totals.</p>
                    </li>
                </ul>
            </div>
        </section>

        <section id="cashier-role" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #f9ab00; font-size: 28px;">point_of_sale</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">Cashier Desk Guide</h2>
            </div>
            
            <div class="google-card" style="padding: 32px;">
                <h5 style="color: #1f1f1f; font-weight: 500; font-size: 18px; margin-bottom: 8px;">Handling Payment Requests</h5>
                <p style="color: #5f6368; font-size: 14px; margin-bottom: 24px;">The Cashier Desk is the central hub for processing money coming from pure Sales staff.</p>
                <ul style="color: #444746; line-height: 1.8; margin-bottom: 0;">
                    <li><strong>Pending Queue:</strong> All sales initialized by sales staff appear here as "Pending Payments".</li>
                    <li><strong>Processing:</strong> Click <span class="google-pill" style="background-color: #e8f0fe; color: #0b57d0;">Endorse Payment</span> once you have received the cash from the customer.</li>
                    <li><strong>Pay Later:</strong> For trusted customers, you can use the "Pay Later" button to endorse the order as a credit sale (0 payment) and move it to history.</li>
                    <li><strong>Rejection:</strong> If a sale was created in error, you can <span style="color: #d93025; font-weight: 500;">Reject</span> it, which restores the stock to the items list immediately.</li>
                </ul>
                <div class="google-alert" style="background-color: #fef7e0; color: #b06000; margin-top: 24px; margin-bottom: 0;">
                    <span class="material-symbols-outlined">warning</span>
                    <span>Always verify the physical cash matches the "Amount Customer is Paying" shown on the screen before clicking Endorse.</span>
                </div>
            </div>
        </section>

        <section id="admin-role" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #1f1f1f; font-size: 28px;">shield_person</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">Admin Role Guide</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="google-card h-100" style="padding: 32px; margin-bottom: 0;">
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #f9ab00; font-size: 20px;">inventory_2</span>
                            Inventory Mastery & Auditing
                        </h6>
                        <p style="color: #444746; font-size: 14px; margin-bottom: 16px;">Admins have full control over items. Beyond basic CRUD, you can now use:</p>
                        <ul style="color: #5f6368; font-size: 14px; padding-left: 20px; margin-bottom: 0; line-height: 1.6;">
                            <li class="mb-2"><strong>Bundles & Membership:</strong> Group items sold together. The item detail page now shows all bundles an item belongs to.</li>
                            <li class="mb-2"><strong>Quick Role Management:</strong> You can now update any user's role directly from the Manage Users list using the new quick-change dropdown.</li>
                            <li class="mb-2"><strong>Item Activity Logs:</strong> Click "Activity Log" on any item to see a chronological history of changes, including price updates and stock adjustments.</li>
                            <li><strong>Low Stock Alerts:</strong> Threshold is set to 5 units. Check the dashboard for proactive restocking.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="google-card h-100" style="padding: 32px; margin-bottom: 0;">
                        <h6 style="color: #1f1f1f; font-weight: 500; font-size: 16px; margin-bottom: 16px;">Staff & Performance</h6>
                        <p style="color: #444746; font-size: 14px; line-height: 1.6;">Navigate to <span style="color: #0b57d0; font-weight: 500;">Staff Performance</span> to see individual dealer stats. You can view <strong>Live Performance</strong> cards (Sales, Revenue, Profit, Expenses) and generate printable PDF-style reports.</p>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="google-card" style="padding: 32px; margin-bottom: 0; background-color: #f1f3f4;">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="material-symbols-outlined" style="font-size: 32px; color: #188038;">savings</span>
                            <h6 style="color: #1f1f1f; font-weight: 500; font-size: 16px; margin: 0;">Coffer & Cash Management</h6>
                        </div>
                        <p style="color: #444746; font-size: 14px; margin: 0; line-height: 1.6;">Admins can record <strong>Deposits</strong> and <strong>Withdrawals</strong> from the business coffers. The Dashboard tracks the real-time balance based on (Total Cash Invoiced + Deposits - Withdrawals - Recorded Expenses).</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="google-card h-100" style="padding: 32px; margin-bottom: 0; background-color: #1f1f1f; color: #fff;">
                        <h6 style="color: #fff; font-weight: 500; font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #78d9ec; font-size: 20px;">event_note</span>
                            Daily Activity Report
                        </h6>
                        <p style="color: #9aa0a6; font-size: 14px; line-height: 1.6; margin-bottom: 24px;">The ultimate auditing tool. Select any date to see a full ledger of that day's activity: Sales, Returns, Debt Recovery, Expenditures, and a complete system log of who did what and when.</p>
                        <a href="<?= BASE_URL ?>/reports/daily" style="color: #78d9ec; text-decoration: none; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; gap: 4px;">Open Daily Report <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="google-card h-100" style="padding: 32px; margin-bottom: 0; background-color: #1f1f1f; color: #fff;">
                        <h6 style="color: #fff; font-weight: 500; font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #fdd663; font-size: 20px;">delete_sweep</span>
                            Financial Safety (Recycle Bin)
                        </h6>
                        <p style="color: #9aa0a6; font-size: 14px; line-height: 1.6;">Sales cannot be deleted instantly. Staff must "Request Delete". Admins review these in the <strong>Recycle Bin</strong> to Approve (restoring stock) or Reject the request.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="finance" class="mb-5 pt-3">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #0b57d0; font-size: 28px;">calculate</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">Financial Formulae</h2>
            </div>
            <div class="google-table-card">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Definition / Formula</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Gross Profit</td>
                                <td><code style="background-color: #f1f3f4; color: #1f1f1f; padding: 4px 8px; border-radius: 4px;">Total Sales - (Cost Price × Quantity)</code></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Net Today</td>
                                <td><code style="background-color: #f1f3f4; color: #1f1f1f; padding: 4px 8px; border-radius: 4px;">Gross Profit - Today's Expenses</code></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Cash Collected Today (Net)</td>
                                <td>
                                    <code style="background-color: #f1f3f4; color: #1f1f1f; padding: 4px 8px; border-radius: 4px;">Payments for Today's Sales - Returns of Today's Sales</code><br>
                                    <span style="font-size: 12px; color: #5f6368; display: inline-block; margin-top: 4px;">Strictly includes new business from today.</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Total Net Collections</td>
                                <td>
                                    <code style="background-color: #f1f3f4; color: #1f1f1f; padding: 4px 8px; border-radius: 4px;">(All Payments Today) - (All Returns Today)</code><br>
                                    <span style="font-size: 12px; color: #5f6368; display: inline-block; margin-top: 4px;">Your final drawer total including debt recovery.</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Debt Recovered</td>
                                <td><span style="color: #444746;">Gross payments made today for invoices generated in the past.</span></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Realized GP</td>
                                <td>
                                    <code style="background-color: #f1f3f4; color: #1f1f1f; padding: 4px 8px; border-radius: 4px;">(Cash Collected / Total Sales) × Potential Gross Profit</code><br>
                                    <span style="font-size: 12px; color: #5f6368; display: inline-block; margin-top: 4px;">Actual earned profit from money in hand.</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #1f1f1f;">Realized Net Profit</td>
                                <td>
                                    <code style="background-color: #f1f3f4; color: #1f1f1f; padding: 4px 8px; border-radius: 4px;">Realized Gross Profit - Today's Expenses</code><br>
                                    <span style="font-size: 12px; color: #5f6368; display: inline-block; margin-top: 4px;">Your final earnings after costs and overhead.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="advanced" class="mb-5 pt-3 pb-5">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <span class="material-symbols-outlined" style="color: #ea4335; font-size: 28px;">new_releases</span>
                <h2 style="color: #1f1f1f; font-weight: 400; margin: 0; font-size: 24px;">Advanced Features</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="google-card h-100" style="padding: 32px; margin-bottom: 0;">
                        <h5 style="color: #1f1f1f; font-weight: 500; font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #0b57d0; font-size: 20px;">barcode_scanner</span> Barcode Support
                        </h5>
                        <p style="color: #5f6368; font-size: 14px; margin-bottom: 16px;">The system supports lightning-fast item entry using scanners.</p>
                        <ul style="color: #444746; font-size: 14px; padding-left: 20px; margin-bottom: 0; line-height: 1.6;">
                            <li class="mb-2">Scan barcodes on <strong>POS</strong> to add items instantly.</li>
                            <li class="mb-2"><strong>Scannable Receipts:</strong> Invoices include a barcode of the Receipt ID. Scan this in the <strong>Sales History</strong> search box to find an order instantly.</li>
                            <li class="mb-2">Visual Code128 barcodes auto-generated for all SKUs.</li>
                            <li>Print barcodes directly from the <strong>Stock List</strong>.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="google-card h-100" style="padding: 32px; margin-bottom: 0;">
                        <h5 style="color: #1f1f1f; font-weight: 500; font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: #188038; font-size: 20px;">analytics</span> Reporting & Exports
                        </h5>
                        <p style="color: #5f6368; font-size: 14px; margin-bottom: 16px;">Deep data insights and external compatibility.</p>
                        <ul style="color: #444746; font-size: 14px; padding-left: 20px; margin-bottom: 0; line-height: 1.6;">
                            <li class="mb-2"><strong>Top Selling:</strong> View lists by volume and revenue.</li>
                            <li class="mb-2"><strong>CSV Export:</strong> Download any report for Excel/Google Sheets.</li>
                            <li class="mb-2"><strong>Product Sales History:</strong> Click on any item name in the stock list to see a full chronological timeline of its sales and transactions.</li>
                            <li class="mb-2"><strong>Customer Retention:</strong> The percentage of your customers who return to make a second purchase.</li>
                            <li class="mb-2"><strong>Inventory Turnover:</strong> Shows how many times you sell through your entire stock value in a year.</li>
                            <li><strong>Realized Profit:</strong> Accurate tracking of actual cash performance.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    body {
        scroll-behavior: smooth;
    }
    .google-card {
        background: #fff;
        border-radius: 24px;
        border: none;
        box-shadow: none;
    }
    .google-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: none;
        margin-bottom: 24px;
    }
    .google-table-card table {
        margin-bottom: 0;
        width: 100%;
    }
    .google-table-card thead th {
        border-bottom: 1px solid #e3e3e3;
        background-color: #fff;
        color: #5f6368;
        font-weight: 500;
        padding: 16px 32px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .google-table-card tbody td {
        padding: 16px 32px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .google-table-card tbody tr:last-child td {
        border-bottom: none;
    }
    .google-table-card tbody tr {
        transition: background-color 0.2s;
    }
    .google-table-card tbody tr:hover {
        background-color: #f8f9fa;
    }
    .google-pill {
        border-radius: 16px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    .google-nav-pills {
        gap: 8px;
    }
    .google-nav-pills .nav-link {
        color: #444746;
        border-radius: 24px;
        font-weight: 500;
        padding: 8px 20px;
        font-size: 14px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .google-nav-pills .nav-link:hover:not(.active) {
        background-color: #f1f3f4;
    }
    .google-nav-pills .nav-link.active {
        background-color: #e8f0fe;
        color: #0b57d0;
    }
    .google-alert {
        border-radius: 16px;
        border: none;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        font-weight: 500;
        font-size: 14px;
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
