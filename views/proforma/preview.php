<?php
$title = "Pro Forma Invoice #" . str_pad($proforma['id'], 6, '0', STR_PAD_LEFT);
ob_start();
?>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        body {
            background-color: #fff !important;
            margin: 0;
            padding: 0;
        }

        .invoice-card {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            display: block !important;
        }

        .modal-backdrop,
        .modal,
        .navbar,
        .sidebar {
            display: none !important;
        }

        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .bundle-components-hide .bundle-component-row {
            display: none !important;
        }

        .bundle-prices-hide .bundle-component-price {
            display: none !important;
        }
    }

    .print-only {
        display: none;
    }

    .invoice-card {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 0 auto;
        overflow: hidden;
    }

    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 6rem;
        font-weight: 900;
        color: rgba(0, 0, 0, 0.04);
        white-space: nowrap;
        pointer-events: none;
        z-index: 0;
        user-select: none;
        text-align: center;
        width: 100%;
    }

    @media print {
        .watermark {
            color: rgba(0, 0, 0, 0.05) !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    @media (max-width: 768px) {
        .watermark {
            font-size: 3.5rem;
        }
    }

    .header-title {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
    }

    .bundle-components-hide .bundle-component-row {
        display: none !important;
    }

    .bundle-prices-hide .bundle-component-price {
        display: none !important;
    }
</style>

<div class="row justify-content-center" id="proformaContainer">
    <div class="col-md-10">

        <!-- Toolbar -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 no-print">
            <h1 class="h2">Pro Forma Invoice #<?= $proforma['id'] ?></h1>
            <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                <a href="<?= BASE_URL ?>/proformas/create"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined align-text-bottom" style="font-size: 18px;">arrow_back</span>
                    Back
                </a>

                <?php
                $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $whatsappText = urlencode("Hello! Here is your Pro Forma Invoice #{$proforma['id']}. Total Amount: ₵" . number_format($proforma['total_amount'], 2) . ". See details here: " . $currentUrl);
                ?>
                <!-- <a href="https://wa.me/?text=<?= $whatsappText ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                    </svg>
                    WhatsApp
                </a> -->
                <button onclick="window.print()"
                    class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                    title="Save as PDF using Print dialog">
                    <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span> Save as PDF
                </button>
                <button onclick="window.print()"
                    class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">print</span>
                    Print Invoice
                </button>
            </div>
        </div>

        <div class="card mb-3 no-print">
            <div
                class="card-body bg-light rounded d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <span class="fw-bold text-muted">Print View Options:</span>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggleBundleComponents" checked>
                        <label class="form-check-label" for="toggleBundleComponents">Show Bundle Components</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggleBundlePrices" checked>
                        <label class="form-check-label" for="toggleBundlePrices">Show Component Prices</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-card position-relative">
            <div class="watermark">PRO FORMA INVOICE</div>
            <!-- Header -->
            <div class="row mb-4 border-bottom pb-4 align-items-md-center">
                <div class="col-12 col-md-8 mb-4 mb-md-0">
                    <div class="d-flex align-items-center mb-2">
                        <?php if (!empty($settings['company_logo'])): ?>
                            <img src="<?= BASE_URL ?>/<?= e($settings['company_logo']) ?>" alt="Logo"
                                style="height: 50px; margin-right: 15px;">
                        <?php endif; ?>
                        <h1 class="header-title m-0"><?= e($settings['company_name']) ?></h1>
                    </div>
                    <div class="text-muted">
                        <?php if (!empty($settings['company_address'])): ?>
                            <?= nl2br(e($settings['company_address'])) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_phone'])): ?>
                            PH: <?= e($settings['company_phone']) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_email'])): ?>
                            Email: <?= e($settings['company_email']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-start text-md-end">
                    <h4 class="fw-bold text-primary">PRO FORMA INVOICE</h4>
                    <div class="fs-5"><?= e($proforma['reference_no']) ?></div>
                    <div class="text-muted small mb-2">Date: <?= date('M j, Y', strtotime($proforma['created_at'])) ?>
                    </div>
                    <div class="text-muted small">Valid for 30 days</div>
                </div>
            </div>

            <!-- Bill To -->
            <div class="row mb-5">
                <div class="col-12 col-md-6">
                    <p class="mb-1 text-uppercase text-muted small fw-bold">Quote For:</p>
                    <?php if ($proforma['customer_name']): ?>
                        <h5 class="fw-bold">
                            <?= e($proforma['customer_name']) ?>
                        </h5>
                        <p>
                            <?= e($proforma['customer_address'] ?? '') ?><br>
                            <?= e($proforma['customer_phone'] ?? '') ?>
                        </p>
                    <?php else: ?>
                        <h5 class="fw-bold text-muted">Walk-in Customer</h5>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="table-responsive mb-4">
                <table class="table table-striped">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 text-center" style="width: 50px;">#</th>
                            <th class="py-3">Item</th>
                            <th class="py-3 text-center">SKU</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="py-3 text-end">Unit Price</th>
                            <th class="py-3 text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $index + 1 ?></td>
                                <td class="fw-bold">
                                    <?= e($item['name']) ?>

                                    <?php if ($item['item_type'] === 'bundle' && isset($bundleComponents[$item['item_id']])): ?>
                                        <div
                                            class="small text-muted fw-normal mt-1 ps-3 border-start border-3 bundle-component-row">
                                            <?php foreach ($bundleComponents[$item['item_id']] as $comp): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span><?= $comp['quantity'] ?>x <?= e($comp['name']) ?></span>
                                                    <span
                                                        class="bundle-component-price">₵<?= number_format($comp['selling_price'], 2) ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center small text-muted"><?= e($item['sku']) ?></td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end">₵<?php echo number_format($item['price_at_time'], 2); ?></td>
                                <td class="text-end">
                                    ₵<?php echo number_format($item['price_at_time'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total Quote Amount</td>
                            <td class="text-end fw-bold fs-5 text-primary">
                                ₵<?php echo number_format($proforma['total_amount'], 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if (!empty($proforma['notes'])): ?>
                <div class="mb-4 mt-2 p-3 bg-light rounded-3">
                    <p class="mb-1 text-uppercase text-muted small fw-bold">Notes / Conditions</p>
                    <p class="mb-0 text-dark"><?= nl2br(e($proforma['notes'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="text-center mt-3 text-muted print-only">
                <p>This is a quotation, not an invoice for payment.</p>
                <span style="font-size: 12px;">Generated on: <?php echo date('M j, Y H:i'); ?></span>
                <br>
                <span style="font-size: 10px;">Mijma Inc. | POS System (0553477150)</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggleBundleComponents').addEventListener('change', function () {
        const container = document.getElementById('proformaContainer');
        if (this.checked) {
            container.classList.remove('bundle-components-hide');
        } else {
            container.classList.add('bundle-components-hide');
        }
    });

    document.getElementById('toggleBundlePrices').addEventListener('change', function () {
        const container = document.getElementById('proformaContainer');
        if (this.checked) {
            container.classList.remove('bundle-prices-hide');
        } else {
            container.classList.add('bundle-prices-hide');
        }
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts/main.php';
?>