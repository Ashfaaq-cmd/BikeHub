<?php
//  Tables: bikes (category filter = 'bikes')
//          products (categories: gears, helmet, accessories, clothing)
//  ?cat= values: all | bikes | gears | helmet | accessories

session_start();
require_once 'php/db.php';

//  Sanitise ?cat= parameter 
$allowed_cats = ['all', 'bikes', 'gears', 'helmet', 'accessories', 'clothing'];
$active_cat   = $_GET['cat'] ?? 'all';
if (!in_array($active_cat, $allowed_cats, true)) {
    $active_cat = 'all';
}

//  Fetch bikes 
$bikes = mysqli_fetch_all(
    mysqli_query($conn,
        "SELECT id, name, brand AS subtitle, price, sale_price, image,
                'bikes' AS table_name, 'bikes' AS cat_key
         FROM bikes ORDER BY id"),
    MYSQLI_ASSOC
);

// ── Fetch products (gears, helmets, accessories, clothing) ────
// cat_key maps the DB category value to the chip data-cat value
$products_raw = mysqli_fetch_all(
    mysqli_query($conn,
        "SELECT id, name, category AS subtitle, price, sale_price, image,
                'products' AS table_name,
                category AS cat_key
         FROM products ORDER BY id"),
    MYSQLI_ASSOC
);

$all_products = array_merge($bikes, $products_raw);

// Cart badge count 
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
}

function safe($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeHub — Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<!-- NAVBAR  -->
<nav class="navbar"><div class="container">
    <a href="index.php" class="nav-logo">Bike<span>Hub</span></a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li class="nav-dropdown">
            <a href="products.php" class="active">Products
                <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </a>
            <div class="dropdown-menu">
                <a href="products.php?cat=bikes"       class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/></svg></div>Road Bikes</a>
                <a href="products.php?cat=gears"       class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/></svg></div>Gear Sets</a>
                <a href="products.php?cat=helmet"      class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 017 7c0 5-7 13-7 13S5 14 5 9a7 7 0 017-7z"/></svg></div>Helmets</a>
                <a href="products.php?cat=accessories" class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/></svg></div>Accessories</a>
                <div class="dropdown-sep"></div>
                <a href="products.php"                 class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/></svg></div>All Products</a>
            </div>
        </li>
        <li><a href="about.html">About</a></li>
        <li><a href="contact.html">Contact</a></li>
    </ul>
    <div class="nav-right">
        <a href="cart.php" class="cart-link">
            <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
            <span class="cart-badge" id="cart-count"><?= $cart_count ?></span>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span style="color:var(--grey-l);font-size:14px;margin-right:4px">
                Hi, <?= safe(explode(' ', $_SESSION['user_name'])[0]) ?>
            </span>
            <a href="php/logout.php" class="btn-nav-outline">Log Out</a>
        <?php else: ?>
            <a href="login.php"    class="btn-nav-outline">Log In</a>
            <a href="register.php" class="btn-nav-blue">Sign Up</a>
        <?php endif; ?>
    </div>
</div></nav>

<!--  SEARCH + FILTER BAR  -->
<section class="section" style="padding-top:48px">
    <div class="container">
        <div class="search-bar-wrap">
            <div class="search-field">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-input"
                       placeholder="Search bikes, gears, helmets, accessories...">
            </div>
            <div class="filter-chips">
                <?php
                $chips = [
                    'all'         => 'All',
                    'bikes'       => 'Bikes',
                    'gears'       => 'Gear Sets',
                    'helmet'      => 'Helmets',
                    'accessories' => 'Accessories',
                ];
                foreach ($chips as $val => $label):
                    $cls = ($active_cat === $val) ? 'chip active' : 'chip';
                ?>
                <a href="products.php?cat=<?= $val ?>"
                   class="<?= $cls ?>"
                   data-cat="<?= $val ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PRODUCT GRID — PHP loop from DB  -->
        <div class="product-grid" id="all-products-grid">

            <?php foreach ($all_products as $p):
                // Decide badge
                $badge       = ($p['table_name'] === 'bikes') ? 'NEW' : '';
                // Check for sale
                $on_sale     = ($p['sale_price'] > 0 && $p['sale_price'] != $p['price']);
                if ($on_sale) $badge = 'SALE';
                $badge_class = ($badge === 'NEW') ? 'badge-new' : 'badge-sale';
                $display_price = $on_sale ? $p['sale_price'] : $p['price'];

                // Visibility: hide if doesn't match active_cat filter
                $visible = ($active_cat === 'all' || $p['cat_key'] === $active_cat)
                           ? '' : ' style="display:none"';
            ?>
            <div class="product-card"
                 data-cat="<?= safe($p['cat_key']) ?>"
                 data-name="<?= safe(strtolower($p['name'])) ?>"<?= $visible ?>>

                <div class="product-img">
                    <?php if ($badge): ?>
                        <span class="product-badge <?= $badge_class ?>"><?= $badge ?></span>
                    <?php endif; ?>
                    <img src="Bikehub Image/<?= safe($p['image']) ?>"
                         alt="<?= safe($p['name']) ?>"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="product-img-placeholder" style="display:none">
                        <svg viewBox="0 0 80 60">
                            <circle cx="60" cy="45" r="12" stroke="#8896b8" stroke-width="1.4" fill="none"/>
                            <circle cx="18" cy="45" r="12" stroke="#8896b8" stroke-width="1.4" fill="none"/>
                        </svg>
                    </div>
                </div>

                <div class="product-body">
                    <div class="product-cat"><?= safe(ucfirst($p['subtitle'])) ?></div>
                    <div class="product-name"><?= safe($p['name']) ?></div>
                    <div class="product-footer">
                        <div class="product-price">
                            <?php if ($on_sale): ?>
                                <span class="product-old-price">Rs <?= number_format($p['price'], 2) ?></span>
                            <?php endif; ?>
                            Rs <?= number_format($display_price, 2) ?>
                        </div>
                        <button class="add-btn php-add-btn"
                                data-id="<?= (int)$p['id'] ?>"
                                data-table="<?= safe($p['table_name']) ?>"
                                data-name="<?= safe($p['name']) ?>">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>

        </div><!-- /#all-products-grid -->
    </div>
</section>

<!-- FOOTER  -->
<footer><div class="container">
    <div class="footer-top">
        <div>
            <div class="footer-logo">Bike<span>Hub</span></div>
            <p class="footer-desc">Your one-stop destination for premium cycling gear, bikes and accessories for every type of rider.</p>
            <div class="footer-socials">
                <a href="#" class="social-btn"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
                <a href="#" class="social-btn"><svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/></svg></a>
                <a href="#" class="social-btn"><svg viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5z"/></svg></a>
            </div>
        </div>
        <div><div class="footer-col-title">Shop</div><ul class="footer-links"><li><a href="products.php?cat=bikes">Road Bikes</a></li><li><a href="products.php?cat=gears">Gear Sets</a></li><li><a href="products.php?cat=helmet">Helmets</a></li><li><a href="products.php?cat=accessories">Accessories</a></li></ul></div>
        <div><div class="footer-col-title">Company</div><ul class="footer-links"><li><a href="about.html">About Us</a></li><li><a href="about.html">Our Team</a></li><li><a href="contact.html">Contact</a></li><li><a href="#">Careers</a></li></ul></div>
        <div><div class="footer-col-title">Help</div><ul class="footer-links"><li><a href="#">FAQ</a></li><li><a href="#">Returns</a></li><li><a href="#">Shipping Info</a></li><li><a href="#">Track Order</a></li></ul></div>
    </div>
    <div class="footer-bottom">
        <span class="footer-copy">© 2026 BikeHub. All rights reserved.</span>
    </div>
</div></footer>

<script src="script.js"></script>
<script>
$(document).ready(function () {

    //Live search (JS 4) 
    $('#search-input').on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        $('.product-card').each(function () {
            var name = $(this).find('.product-name').text().toLowerCase();
            var cat  = $(this).find('.product-cat').text().toLowerCase();
            $(this).toggle(!q || name.includes(q) || cat.includes(q));
        });
    });

    //Chip filter — client-side on PHP-rendered cards
    $(document).on('click', '.chip', function (e) {
        e.preventDefault();
        $('.chip').removeClass('active');
        $(this).addClass('active');
        var sel = $(this).data('cat');
        $('.product-card').each(function () {
            var match = sel === 'all' || $(this).data('cat') === sel;
            $(this).toggle(match);
        });
        history.replaceState(null, '', 'products.php?cat=' + sel);
    });

    // Add to cart via AJAX
    $(document).on('click', '.php-add-btn', function () {
        var btn   = $(this);
        var id    = btn.data('id');
        var table = btn.data('table');
        var name  = btn.data('name');

        $.post('php/add_to_cart.php', { product_id: id, table_name: table, qty: 1 })
         .done(function (res) {
            if (res.success) {
                $('#cart-count').text(res.cart_count).show();
                showToast(name + ' added to cart ✓');
            }
         });
    });

    //  Hover GPU hint (JS 3)
    $(document).on('mouseenter', '.product-card', function () {
        $(this).css('box-shadow', '0 16px 40px rgba(26,107,255,0.15)');
    }).on('mouseleave', '.product-card', function () {
        $(this).css('box-shadow', '');
    });
});
</script>
</body>
</html>