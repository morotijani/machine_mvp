<?php
$title = "Bundle Print Preview";
ob_start();
?>
<div class="container mt-4 mb-5">
    <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
        <a href="<?= BASE_URL ?>/items" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span> Back to Items
        </a>
        <button onclick="window.print()" class="btn btn-primary d-flex align-items-center gap-2">
            <span class="material-symbols-outlined">print</span> Print Preview
        </button>
    </div>

    <div class="card shadow-sm border-0 print-content">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-0"><?= htmlspecialchars($item['name']) ?></h1>
                <p class="text-muted fs-5">Bundle Overview & Components</p>
                <div class="badge bg-primary px-3 py-2">SKU: <?= htmlspecialchars($item['sku']) ?></div>
            </div>

            <div class="row mb-5">
                <div class="col-6">
                    <h5 class="text-secondary fw-bold text-uppercase small mb-3">Bundle Specification</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted py-1" style="width: 150px;">Category</td>
                            <td class="fw-bold py-1"><?= htmlspecialchars($item['category']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Current Stock</td>
                            <td class="fw-bold py-1"><?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>s</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Location</td>
                            <td class="fw-bold py-1"><?= htmlspecialchars($item['location'] ?: 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-6 text-end">
                    <h5 class="text-secondary fw-bold text-uppercase small mb-3">Pricing</h5>
                    <div class="display-5 fw-bold text-primary">₵<?= number_format($item['price'], 2) ?></div>
                    <p class="text-muted small">Total Bundle Selling Price</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Bundle Composition
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">Component Name</th>
                                <th class="py-3 text-center">Part Number (SKU)</th>
                                <th class="py-3 text-center">Qty / Bundle</th>
                                <th class="py-3 text-end">Selling Price (₵)</th>
                                <th class="py-3 text-end">Subtotal (₵)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($components as $comp): ?>
                            <tr>
                                <td class="py-3 fw-bold"><?= htmlspecialchars($comp['name']) ?></td>
                                <td class="py-3 text-center text-muted"><?= htmlspecialchars($comp['sku']) ?></td>
                                <td class="py-3 text-center"><?= $comp['quantity'] ?></td>
                                <td class="py-3 text-end">₵<?= number_format($comp['selling_price'], 2) ?></td>
                                <td class="py-3 text-end fw-bold">₵<?= number_format($comp['selling_price'] * $comp['quantity'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-3">Total Calculated Value</td>
                                <td class="text-end fw-bold py-3 text-primary">₵<?= number_format($item['price'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top">
                <div class="row text-center text-muted small">
                    <div class="col-12">
                        <p>Generated on <?= date('d M Y, H:i') ?> | <strong>Mijma Inc. +233553477150</strong></p>
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
    header, .sidebar, .d-print-none {
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
        width: 210mm; /* A4 width */
        min-height: 297mm; /* A4 height */
        padding: 15mm !important;
        margin: 0 auto;
    }
    .card-body {
        padding: 0 !important;
    }
    .btn-primary, .badge {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
.display-5 { font-size: 2.5rem; }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
