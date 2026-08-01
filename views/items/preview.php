<?php
$title = "Bundle Print Preview";
ob_start();
?>
<div class="container mt-4 mb-5">
    <div class="d-print-none d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= BASE_URL ?>/items"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">arrow_back</span> Back to
                Items
            </a>
            <h1 class="h4 mb-0 ms-2 text-muted">Print Preview</h1>
        </div>
        <div class="d-flex flex-wrap gap-2 page-header-actions">
            <?php
            // Construct WhatsApp Share Text
            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $whatsappText = urlencode("Check out this bundle: {$item['name']} (SKU: {$item['sku']}). Total Price: ₵" . number_format($item['price'], 2) . ". See details here: " . $currentUrl);
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
                class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 print-content">
        <div class="card-body p-4 p-md-5">

            <!-- Company Header -->
            <div class="text-center mb-4 border-bottom pb-4">
                <?php if (!empty($settings['company_logo'])): ?>
                    <img src="<?= BASE_URL ?>/<?= e($settings['company_logo']) ?>" alt="Company Logo" class="mb-3"
                        style="max-height: 80px; object-fit: contain;">
                <?php endif; ?>
                <h3 class="fw-bold mb-1"><?= e($settings['company_name'] ?: 'My Company') ?></h3>
                <?php if (!empty($settings['company_address'])): ?>
                    <p class="text-muted mb-1 small"><?= nl2br(e($settings['company_address'])) ?></p>
                <?php endif; ?>
                <div class="text-muted small">
                    <?php if (!empty($settings['company_phone'])): ?>
                        <span>📞 <?= e($settings['company_phone']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($settings['company_phone']) && !empty($settings['company_email'])): ?>
                        <span class="mx-2">|</span>
                    <?php endif; ?>
                    <?php if (!empty($settings['company_email'])): ?>
                        <span>✉️ <?= e($settings['company_email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center mb-4 mb-md-5">
                <h2 class="fw-bold mb-2"><?= htmlspecialchars($item['name']) ?></h2>
                <p class="text-muted mb-3">Bundle Overview & Components</p>
                <div class="badge bg-primary px-3 py-2">SKU: <?= htmlspecialchars($item['sku']) ?></div>
            </div>

            <div class="row mb-4 mb-md-5">
                <div class="col-12 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-secondary fw-bold text-uppercase small mb-3">Bundle Specification</h5>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted py-1" style="width: 120px;">Category</td>
                            <td class="fw-bold py-1"><?= htmlspecialchars($item['category']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Current Stock</td>
                            <td class="fw-bold py-1"><?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>s
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Location</td>
                            <td class="fw-bold py-1"><?= htmlspecialchars($item['location'] ?: 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 col-md-6 text-start text-md-end border-top border-md-0 pt-3 pt-md-0">
                    <h5 class="text-secondary fw-bold text-uppercase small mb-3">Pricing</h5>
                    <div class="h2 fw-bold text-primary mb-1">₵<?= number_format($item['price'], 2) ?></div>
                    <p class="text-muted small">Total Bundle Selling Price</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Bundle Composition
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2">Component Name</th>
                                <th class="py-2 text-center">Part # (SKU)</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-end">Price (₵)</th>
                                <th class="py-2 text-end">Subtotal (₵)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($components as $comp): ?>
                                <tr>
                                    <td class="py-2 fw-bold"><?= htmlspecialchars($comp['name']) ?></td>
                                    <td class="py-2 text-center text-muted"><?= htmlspecialchars($comp['sku']) ?></td>
                                    <td class="py-2 text-center"><?= $comp['quantity'] ?></td>
                                    <td class="py-2 text-end">₵<?= number_format($comp['selling_price'], 2) ?></td>
                                    <td class="py-2 text-end fw-bold">
                                        ₵<?= number_format($comp['selling_price'] * $comp['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-2">Total Calculated Value</td>
                                <td class="text-end fw-bold py-2 text-primary">₵<?= number_format($item['price'], 2) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top">
                <div class="row text-center text-muted small">
                    <div class="col-12">
                        <p style="font-size: 8px;">Generated on <?= date('d M Y, H:i') ?> | <strong>Mijma Inc.
                                +233553477150</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 0;
        }

        header,
        .sidebar,
        .d-print-none {
            display: none !important;
        }

        body {
            background-color: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .print-content {
            box-shadow: none !important;
            border: none !important;
            width: 210mm;
            /* A4 width */
            min-height: 297mm;
            /* A4 height */
            padding: 15mm !important;
            margin: 0 auto;
        }

        .card-body {
            padding: 0 !important;
        }

        .btn-primary,
        .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>