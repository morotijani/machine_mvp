<style>
    /* Thermal Receipt Styles */
    @media print {
        .thermal-receipt {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            color: #000;
            background: #fff;
        }
        .thermal-receipt table {
            width: 100%;
            border-collapse: collapse;
        }
        .thermal-receipt th, .thermal-receipt td {
            padding: 2px 0;
            border-bottom: 1px dashed #000;
        }
        .thermal-receipt .text-center { text-align: center; }
        .thermal-receipt .text-right { text-align: right; }
        .thermal-receipt .text-left { text-align: left; }
        .thermal-receipt .font-bold { font-weight: bold; }
        .thermal-receipt .mb-2 { margin-bottom: 10px; }
        .thermal-receipt .mt-2 { margin-top: 10px; }
        .thermal-receipt .border-top { border-top: 1px dashed #000; }
        .thermal-receipt .border-bottom { border-bottom: 1px dashed #000; }
        .thermal-receipt h2 { font-size: 16px; margin: 0; padding: 0; }
    }
</style>

<div class="print-only thermal-receipt">
    <div class="text-center mb-2">
        <h2><?= e($settings['company_name']) ?></h2>
        <?php if (!empty($settings['company_address'])): ?>
            <div><?= nl2br(e($settings['company_address'])) ?></div>
        <?php endif; ?>
        <?php if (!empty($settings['company_phone'])): ?>
            <div>PH: <?= e($settings['company_phone']) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-2 border-top mt-2" style="padding-top:5px;">
        <div><span class="font-bold">Receipt #:</span> <?= htmlspecialchars($sale['invoice_number'] ?? str_pad($sale['id'], 6, '0', STR_PAD_LEFT)) ?></div>
        <div><span class="font-bold">Date:</span> <?= date('M j, Y H:i', strtotime($sale['created_at'])) ?></div>
        <div><span class="font-bold">Customer:</span> <?= $sale['customer_name'] ? e($sale['customer_name']) : 'Walk-in' ?></div>
    </div>

    <table class="mb-2 mt-2">
        <thead>
            <tr>
                <th class="text-left">Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale['items'] as $item): ?>
                <tr>
                    <td class="text-left"><?= e($item['item_name']) ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-right">₵<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="mb-2" style="border: none;">
        <tr>
            <td class="text-right font-bold" style="border:none;">Total Amount:</td>
            <td class="text-right font-bold" style="border:none;">₵<?= number_format($sale['total_amount'], 2) ?></td>
        </tr>
        <tr>
            <td class="text-right" style="border:none;">Amount Paid:</td>
            <td class="text-right" style="border:none;">₵<?= number_format($sale['paid_amount'], 2) ?></td>
        </tr>
        <?php if ($sale['total_amount'] - $sale['paid_amount'] > 0): ?>
            <tr>
                <td class="text-right font-bold" style="border:none;">Balance Due:</td>
                <td class="text-right font-bold" style="border:none;">₵<?= number_format($sale['total_amount'] - $sale['paid_amount'], 2) ?></td>
            </tr>
        <?php endif; ?>
    </table>

    <div class="text-center mt-2 mb-2 font-bold border-top border-bottom" style="padding: 5px 0;">
        <?php if ($sale['payment_status'] === 'paid'): ?>
            *** FULLY PAID ***
        <?php elseif ($sale['payment_status'] === 'partial'): ?>
            *** PARTIAL PAYMENT ***
        <?php else: ?>
            *** UNPAID ***
        <?php endif; ?>
    </div>

    <div class="text-center mt-2" style="font-size: 10px;">
        Thank you for your business!<br>
        Mijma Inc. | POS System
    </div>
</div>
