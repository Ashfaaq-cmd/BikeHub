<?php
//  DB tables: bikes | products 

session_start();
require_once 'php/db.php';

//  Carousel: 2 bikes + 1 product 
$bikes_carousel = mysqli_fetch_all(
    mysqli_query($conn,
        "SELECT id, name, price, sale_price, image, 'bikes' AS table_name
         FROM bikes ORDER BY id DESC LIMIT 2"),
    MYSQLI_ASSOC
);
$products_carousel = mysqli_fetch_all(
    mysqli_query($conn,
        "SELECT id, name, price, sale_price, image, 'products' AS table_name
         FROM products ORDER BY id LIMIT 1"),
    MYSQLI_ASSOC
);
$carousel_items = array_merge($bikes_carousel, $products_carousel);

// Featured products: 3 bikes + 2 products 
$featured_bikes = mysqli_fetch_all(
    mysqli_query($conn,
        "SELECT id, name, brand AS subtitle, price, sale_price, image,
                'bikes' AS table_name, 'NEW' AS badge
         FROM bikes ORDER BY id DESC LIMIT 3"),
    MYSQLI_ASSOC
);
$featured_products = mysqli_fetch_all(
    mysqli_query($conn,
        "SELECT id, name, category AS subtitle, price, sale_price, image,
                'products' AS table_name, 'SALE' AS badge
         FROM products ORDER BY id LIMIT 2"),
    MYSQLI_ASSOC
);
$featured = array_merge($featured_bikes, $featured_products);

//  Category counts 
$bike_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM bikes"))[0];
$gear_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE category='gears'"))[0];
$hel_count  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE category='helmet'"))[0];
$acc_count  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE category='accessories'"))[0];

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
    <title>BikeHub — Ride Faster. Gear Smarter.</title>
    <!-- ../ goes up from php/ to the root where style.css lives -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<!--  NAVBAR  -->
<nav class="navbar">
    <div class="container">
        <a href="index.php" class="nav-logo">Bike<span>Hub</span></a>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li class="nav-dropdown">
                <a href="products.php">Products <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></a>
                <div class="dropdown-menu">
                    <a href="products.php?cat=bikes"       class="dropdown-item">Road Bikes</a>
                    <a href="products.php?cat=gears"       class="dropdown-item">Gear Sets</a>
                    <a href="products.php?cat=helmet"      class="dropdown-item">Helmets</a>
                    <a href="products.php?cat=accessories" class="dropdown-item">Accessories</a>
                    <div class="dropdown-sep"></div>
                    <a href="products.php"                 class="dropdown-item">All Products</a>
                </div>
            </li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact</a></li>
        </ul>
        <div class="nav-right">
            <a href="cart.php" class="cart-link">
                <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
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
    </div>
</nav>

<!-- HERO CAROUSEL  -->
<section class="carousel-section">
    <div class="carousel-grid-bg"></div>
    <div class="slides-track" id="slidesTrack">

        <?php
        $colors      = ['#1a6bff',      '#22c55e',      '#a855f7'];
        $rgb         = ['26,107,255',   '34,197,94',    '168,85,247'];
        $slide_class = ['slide-blue',   'slide-green',  'slide-purple'];
        $themes      = ['2026 Season Drop', 'New Arrivals', 'Safety First'];
        $tag_labels  = ['Featured Bike',    'Top Seller',   'Best Rated'];

        foreach ($carousel_items as $idx => $item):
            $c    = $colors[$idx % 3];
            $r    = $rgb[$idx % 3];
            $sc   = $slide_class[$idx % 3];
            $th   = $themes[$idx % 3];
            $tag  = $tag_labels[$idx % 3];
            $disp = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
        ?>
        <div class="slide <?= $sc ?>">
            <div class="slide-left">
                <div class="slide-badge"
                     style="background:rgba(<?= $r ?>,.1);border:1px solid rgba(<?= $r ?>,.22)">
                    <div class="badge-dot" style="background:<?= $c ?>"></div>
                    <span class="badge-text" style="color:<?= $c ?>"><?= $th ?></span>
                </div>
                <h1 class="slide-title">
                    Premium <span style="color:<?= $c ?>"><?= safe($item['name']) ?></span>
                </h1>
                <p class="slide-sub">
                    Discover <?= safe($item['name']) ?> from our collection. Engineered for performance.
                </p>
                <div class="slide-ctas">
                    <a href="products.php" class="btn-slide-primary"
                       style="background:<?= $c ?>;box-shadow:0 0 28px rgba(<?= $r ?>,.35)">
                        Shop Now →
                    </a>
                    <a href="products.php" class="btn-slide-ghost">View All</a>
                </div>
                <div class="slide-stats">
                    <div><div class="stat-num">200+</div><div class="stat-lbl">Products</div></div>
                    <div><div class="stat-num">50+</div><div class="stat-lbl">Brands</div></div>
                    <div><div class="stat-num">24/7</div><div class="stat-lbl">Support</div></div>
                </div>
            </div>
            <div class="slide-right">
                <div class="slide-glow"></div>
                <div class="ring ring-lg"></div><div class="ring ring-md"></div><div class="ring ring-sm"></div>
                <div class="feat-card">
                    <!-- ../ goes up from php/ to root, then into Bikehub Image/ -->
                    <img src="Bikehub Image/<?= safe($item['image']) ?>"
                         alt="<?= safe($item['name']) ?>"
                         style="width:100%;height:120px;object-fit:cover;border-radius:12px"
                         onerror="this.style.display='none'">
                    <div class="feat-card-tag" style="color:<?= $c ?>"><?= $tag ?></div>
                    <div class="feat-card-name"><?= safe($item['name']) ?></div>
                    <div class="feat-card-price">Rs <?= number_format($disp, 2) ?></div>
                    <a href="products.php" class="feat-card-btn" style="background:<?= $c ?>">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <div class="carousel-dots">
        <?php foreach ($carousel_items as $idx => $item): ?>
            <button class="dot <?= $idx === 0 ? 'active' : '' ?>"
                    onclick="goTo(<?= $idx ?>)" aria-label="Slide <?= $idx+1 ?>"></button>
        <?php endforeach; ?>
    </div>
    <div class="carousel-arrows">
        <button class="arrow-btn" id="prevBtn"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="arrow-btn" id="nextBtn"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
</section>

<!--  BROWSE CATEGORIES  -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <div class="section-title">Browse Categories</div>
                <div class="section-sub">Find exactly what you need</div>
            </div>
            <a href="products.php" class="section-link">All Products →</a>
        </div>
        <div class="cat-grid">
            <a href="products.php?cat=bikes"       class="cat-card dark">
                <img src="icons/racing-bike.png" alt="Road Bikes" class="cat-icon-img" onerror="this.style.display='none'">
                <div class="cat-name">Road Bikes</div>
                <div class="cat-count"><?= $bike_count ?> products</div>
            </a>
            <a href="products.php?cat=gears"       class="cat-card">
                <img src="icons/crankset.png" alt="Gear Sets" class="cat-icon-img" onerror="this.style.display='none'">
                <div class="cat-name">Gear Sets</div>
                <div class="cat-count"><?= $gear_count ?> products</div>
            </a>
            <a href="products.php?cat=helmet"      class="cat-card">
                <img src="icons/helmet.png" alt="Helmets" class="cat-icon-img" onerror="this.style.display='none'">
                <div class="cat-name">Helmets</div>
                <div class="cat-count"><?= $hel_count ?> products</div>
            </a>
            <a href="products.php?cat=accessories" class="cat-card">
                <img src="icons/tool-box.png" alt="Accessories" class="cat-icon-img" onerror="this.style.display='none'">
                <div class="cat-name">Accessories</div>
                <div class="cat-count"><?= $acc_count ?> products</div>
            </a>
        </div>
    </div>
</section>

<!--  FEATURED PRODUCTS  -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <div>
                <div class="section-title">Featured Products</div>
                <div class="section-sub">Handpicked for performance and style</div>
            </div>
            <a href="products.php" class="section-link">View All →</a>
        </div>

        <div class="product-grid">
            <?php foreach ($featured as $p):
                $badge_class  = $p['badge'] === 'NEW' ? 'badge-new' : 'badge-sale';
                $display_price = ($p['sale_price'] > 0) ? $p['sale_price'] : $p['price'];
            ?>
            <div class="product-card">
                <div class="product-img">
                    <?php if ($p['badge']): ?>
                        <span class="product-badge <?= $badge_class ?>"><?= $p['badge'] ?></span>
                    <?php endif; ?>
                    <img src="Bikehub Image/<?= safe($p['image']) ?>"
                         alt="<?= safe($p['name']) ?>"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="product-img-placeholder" style="display:none">
                        <svg viewBox="0 0 80 60"><circle cx="60" cy="45" r="12" stroke="#8896b8" stroke-width="1.4" fill="none"/><circle cx="18" cy="45" r="12" stroke="#8896b8" stroke-width="1.4" fill="none"/></svg>
                    </div>
                </div>
                <div class="product-body">
                    <div class="product-cat"><?= safe(ucfirst($p['subtitle'])) ?></div>
                    <div class="product-name"><?= safe($p['name']) ?></div>
                    <div class="product-footer">
                        <div class="product-price">
                            <?php if ($p['sale_price'] > 0 && $p['sale_price'] != $p['price']): ?>
                                <span class="product-old-price">Rs <?= number_format($p['price'], 2) ?></span>
                            <?php endif; ?>
                            Rs <?= number_format($display_price, 2) ?>
                        </div>
                        <button class="add-btn php-add-btn"
                                data-id="<?= (int)$p['id'] ?>"
                                data-table="<?= safe($p['table_name']) ?>"
                                data-name="<?= safe($p['name']) ?>">
                            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!--  PROMO BANNER  -->
<section class="section">
    <div class="container">
        <div class="promo-banner">
            <div class="promo-text">
                <div class="promo-title">Free Shipping on Orders Over Rs 5,000</div>
                <div class="promo-sub">Use code BIKEHUB at checkout. Valid on all road bikes and gear sets.</div>
            </div>
            <a href="products.php" class="btn-white">Shop the Deal →</a>
        </div>
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
</script>
</body>
</html>