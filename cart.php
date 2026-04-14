<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/php/db.php'; 

// Image path helper 
function img_path(string $table, string $category, string $filename): string {
    if ($table === 'bikes') {
        $sub = 'Bikes';
    } else {
        $map = [
            'gears'       => 'Gears',
            'helmet'      => 'Helmet',
            'accessories' => 'Accessories',
            'clothing'    => 'Clothing',
        ];
        $sub = $map[strtolower($category)] ?? 'Gears';
    }
    return 'Bikehub Image/' . $sub . '/' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
}

function safe($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Rebuild cart with fresh DB prices
$cart_items = [];
$subtotal   = 0.0;
$total_qty  = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        $table = in_array($item['type'], ['bikes', 'products']) ? $item['type'] : 'products';
        $col   = ($table === 'bikes') ? 'brand' : 'category';

        $stmt = mysqli_prepare($conn,
            "SELECT id, name, $col AS subtitle, price, sale_price, image FROM `$table` WHERE id = ? LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 'i', $item['id']);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($row) {
            $unit = ($row['sale_price'] > 0) ? (float)$row['sale_price'] : (float)$row['price'];
            $_SESSION['cart'][$key]['price'] = $unit;
            $qty  = (int)$item['qty'];
            $line = $unit * $qty;
            $subtotal  += $line;
            $total_qty += $qty;
            $cart_items[] = [
                'key'      => $key,
                'id'       => $item['id'],
                'type'     => $table,
                'name'     => $row['name'],
                'subtitle' => $row['subtitle'],
                'price'    => $unit,
                'image'    => $row['image'],
                'qty'      => $qty,
                'line'     => $line,
            ];
        }
    }
}

$shipping = ($subtotal > 0 && $subtotal < 5000) ? 250 : 0;
$tax      = round($subtotal * 0.08, 2);
$total    = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeHub — Your Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<nav class="navbar"><div class="container">
    <a href="index.php" class="nav-logo">Bike<span>Hub</span></a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li class="nav-dropdown">
            <a href="products.php">Products <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></a>
            <div class="dropdown-menu">
                <a href="products.php?cat=bikes"       class="dropdown-item">Road Bikes</a>
                <a href="products.php?cat=gears"       class="dropdown-item">Gear Sets</a>
                <a href="products.php"                 class="dropdown-item">All Products</a>
            </div>
        </li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="nav-right">
        <span style="color:var(--grey-l);font-size:14px;margin-right:4px">
            Hi, <?= safe(explode(' ', $_SESSION['user_name'])[0]) ?>
        </span>
        <a href="php/logout.php" class="btn-nav-blue">Log Out</a>
    </div>
</div></nav>

<div class="cart-page">
    <div class="container">

        <?php if (empty($cart_items)): ?>
        <div class="cart-empty">
            <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a href="products.php" class="btn-blue-solid">Browse Products</a>
        </div>

        <?php else: ?>
        <div class="cart-layout">
            <div>
                <div class="cart-heading">
                    Your Cart
                    <span class="cart-count-lbl" id="cart-item-count">
                        (<?= $total_qty ?> <?= $total_qty === 1 ? 'item' : 'items' ?>)
                    </span>
                </div>

                <div class="cart-items-list" id="cart-items-list">
                    <?php foreach ($cart_items as $item):
                        $src = img_path($item['type'], $item['subtitle'], $item['image']);
                    ?>
                    <div class="cart-item" id="row-<?= safe($item['key']) ?>">
                        <div class="cart-item-img">
                            <img src="<?= $src ?>" alt="<?= safe($item['name']) ?>"
                                 onerror="this.style.display='none'">
                        </div>
                        <div class="cart-info">
                            <div class="cart-name"><?= safe($item['name']) ?></div>
                            <div class="cart-sub">
                                <?= safe(ucfirst($item['subtitle'])) ?> &middot;
                                Rs <?= number_format($item['price'], 2) ?> each
                            </div>
                            <div class="qty-row">
                                <button class="qty-btn" onclick="cartUpdate('<?= safe($item['key']) ?>', 'decrease')">−</button>
                                <span class="qty-num" id="qty-<?= safe($item['key']) ?>"><?= $item['qty'] ?></span>
                                <button class="qty-btn" onclick="cartUpdate('<?= safe($item['key']) ?>', 'increase')">+</button>
                            </div>
                        </div>
                        <div class="cart-price" id="line-<?= safe($item['key']) ?>">
                            Rs <?= number_format($item['line'], 2) ?>
                        </div>
                        <button class="cart-remove" onclick="cartUpdate('<?= safe($item['key']) ?>', 'remove')" title="Remove">×</button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <a href="products.php" class="continue-link">← Continue Shopping</a>
            </div>

            <div class="order-summary">
                <div class="summary-title">Order Summary</div>
                <div class="summary-row"><span>Subtotal</span><span id="summary-subtotal">Rs <?= number_format($subtotal, 2) ?></span></div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="summary-shipping" <?= $shipping === 0 ? 'class="free-txt"' : '' ?>>
                        <?= $shipping === 0 ? 'Free' : 'Rs ' . number_format($shipping, 2) ?>
                    </span>
                </div>
                <div class="summary-row"><span>Tax (8%)</span><span id="summary-tax">Rs <?= number_format($tax, 2) ?></span></div>
                <div class="summary-div"></div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="summary-total">Rs <?= number_format($total, 2) ?></span>
                </div>

                <div class="promo-row">
                    <input class="promo-input" type="text" id="promo-input" placeholder="Promo code...">
                    <button class="promo-apply" onclick="applyPromo()">Apply</button>
                </div>
                <p id="promo-msg" style="font-size:12px;margin-top:6px;display:none;"></p>

                <a href="php/checkout.php" class="checkout-btn" id="checkout-btn">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Secure Checkout
                </a>
                <div class="ssl-note">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    SSL encrypted · Safe checkout
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="script.js"></script>
<script>
function cartUpdate(key, action) {
    $.post('php/update_cart.php', { key: key, action: action })
     .done(function (res) {
        if (!res.success) return;
        if (res.removed) {
            $('#row-' + key).fadeOut(250, function () { $(this).remove(); });
        } else {
            $('#line-' + key).text('Rs ' + res.line_total);
        }
        $('#summary-subtotal').text('Rs ' + res.subtotal);
        $('#summary-shipping').text(res.shipping).toggleClass('free-txt', res.shipping === 'Free');
        $('#summary-tax').text('Rs ' + res.tax);
        $('#summary-total').text('Rs ' + res.total);
        $('#cart-count').text(res.cart_count);
        var qty = res.cart_count;
        $('#cart-item-count').text('(' + qty + ' ' + (qty === 1 ? 'item' : 'items') + ')');
        if (res.cart_count === 0) setTimeout(function () { location.reload(); }, 400);
     });
}
function applyPromo() {
    var code = $('#promo-input').val().trim().toUpperCase();
    var $msg = $('#promo-msg').show();
    if (code === 'BIKEHUB')  $msg.css('color','#22c55e').text('✓ Free shipping applied!');
    else if (!code)          $msg.css('color','red').text('Please enter a promo code.');
    else                     $msg.css('color','red').text('Invalid promo code.');
}
</script>
</body>
</html>