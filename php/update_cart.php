<?php
// php/update_cart.php — AJAX cart update handler
session_start();
header('Content-Type: application/json');

$key    = $_POST['key']    ?? '';
$action = $_POST['action'] ?? '';

if (!isset($_SESSION['cart'][$key])) {
    echo json_encode(['success' => false, 'message' => 'Item not in cart.']);
    exit;
}

if ($action === 'increase') {
    $_SESSION['cart'][$key]['qty']++;
} elseif ($action === 'decrease') {
    $_SESSION['cart'][$key]['qty']--;
    if ($_SESSION['cart'][$key]['qty'] <= 0) {
        unset($_SESSION['cart'][$key]);
    }
} elseif ($action === 'remove') {
    unset($_SESSION['cart'][$key]);
} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// Recalculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$shipping  = ($subtotal > 0 && $subtotal < 5000) ? 250 : 0;
$tax       = round($subtotal * 0.08, 2);
$total     = $subtotal + $shipping + $tax;
$total_qty = array_sum(array_column($_SESSION['cart'], 'qty'));
$line      = isset($_SESSION['cart'][$key])
    ? $_SESSION['cart'][$key]['price'] * $_SESSION['cart'][$key]['qty']
    : 0;

echo json_encode([
    'success'    => true,
    'cart_count' => $total_qty,
    'line_total' => number_format($line, 2),
    'subtotal'   => number_format($subtotal, 2),
    'shipping'   => $shipping === 0 ? 'Free' : 'Rs ' . number_format($shipping, 2),
    'tax'        => number_format($tax, 2),
    'total'      => number_format($total, 2),
    'removed'    => !isset($_SESSION['cart'][$key]),
]);