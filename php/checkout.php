<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart    = $_SESSION['cart'];

mysqli_begin_transaction($conn);

try {

    $subtotal = 0;

    //Calculate total
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }

    $shipping = ($subtotal > 0 && $subtotal < 5000) ? 250 : 0;
    $tax      = round($subtotal * 0.08, 2);
    $total    = $subtotal + $shipping + $tax;

    //Insert order
    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (user_id, total, shipping, tax) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'iddd', $user_id, $total, $shipping, $tax);
    mysqli_stmt_execute($stmt);

    $order_id = mysqli_insert_id($conn);

    //Insert order items
    foreach ($cart as $item) {
        $product_id = $item['id'];
        $table      = $item['type'];
        $name       = $item['name'];
        $price      = $item['price'];
        $qty        = $item['qty'];
        $line       = $price * $qty;

        $stmt = mysqli_prepare($conn,
            "INSERT INTO order_items 
            (order_id, product_id, table_name, name, price, qty, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            'iissdii',
            $order_id,
            $product_id,
            $table,
            $name,
            $price,
            $qty,
            $line
        );

        mysqli_stmt_execute($stmt);
    }

    //Clear cart
    unset($_SESSION['cart']);

    mysqli_commit($conn);

    echo json_encode([
        'success' => true,
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}