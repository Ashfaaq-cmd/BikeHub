<?php

session_start();
header('Content-Type: application/json');

require_once 'db.php';

$product_id = (int)($_POST['product_id'] ?? 0);
$table_name = $_POST['table_name']        ?? '';
$qty        = max(1, (int)($_POST['qty'] ?? 1));

// Only allow our two tables — never use user input directly in a query
if (!in_array($table_name, ['bikes', 'products'], true) || $product_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

//  Fetch product from the correct table 
$stmt = mysqli_prepare($conn,
    "SELECT id, name, price, sale_price, image FROM `$table_name` WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

// Use sale_price if set, otherwise regular price
$price = ($product['sale_price'] > 0) ? (float)$product['sale_price'] : (float)$product['price'];

// Add to session cart 
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$key = $table_name . '_' . $product_id;

if (isset($_SESSION['cart'][$key])) {
    $_SESSION['cart'][$key]['qty'] += $qty;
} else {
    $_SESSION['cart'][$key] = [
        'id'    => $product_id,
        'type'  => $table_name,   // 'bikes' or 'products'
        'name'  => $product['name'],
        'price' => $price,
        'img'   => $product['image'],
        'qty'   => $qty,
    ];
}

$total_qty = array_sum(array_column($_SESSION['cart'], 'qty'));

echo json_encode([
    'success'    => true,
    'message'    => $product['name'] . ' added to cart',
    'cart_count' => $total_qty,
]);