<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeHub — About Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar"><div class="container">
    <a href="index.php" class="nav-logo">Bike<span>Hub</span></a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li class="nav-dropdown"><a href="products.php">Products <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></a>
            <div class="dropdown-menu">
                <a href="products.php?cat=bikes" class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><circle cx="18.5" cy="17.5" r="3.5"/></svg></div>Road Bikes</a>
                <a href="products.php?cat=gears" class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/></svg></div>Gear Sets</a>
                <a href="products.php" class="dropdown-item"><div class="dropdown-icon"><svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/></svg></div>All Products</a>
            </div>
        </li>
        <li><a href="about.php" class="active">About</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="nav-right">
        <a href="cart.php" class="cart-link"><svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg><span class="cart-badge" id="cart-count">0</span></a>
        <a href="account.php" class="btn-nav-blue">Account</a>
    </div>
</div></nav>

<!-- ── ABOUT HERO ── -->
<section class="about-hero-sec">
    <div class="container">
        <div class="about-hero-inner">
            <div>
                <div class="about-eyebrow">
                    <div class="about-eyebrow-dot"></div>
                    <span>Our Story</span>
                </div>
                <h1 class="about-h">Built for Riders,<br>By <span class="blue">Riders.</span></h1>
                <p class="about-sub">BikeHub was founded in 2018 by two cycling obsessives who couldn't find a single destination for premium bikes and gear. We built the shop we always wished existed.</p>
                <div class="about-stats">
                    <div class="astat"><div class="astat-n">8yr</div><div class="astat-l">In Business</div></div>
                    <div class="astat"><div class="astat-n">200+</div><div class="astat-l">Products</div></div>
                    <div class="astat"><div class="astat-n">12k+</div><div class="astat-l">Happy Riders</div></div>
                </div>
            </div>
            <div class="about-img-grid">
                <div class="about-img-box tall">
                    <img src="Bikehub Image/Bikes/canyon.jpg" alt="Canyon Bike"
                         onerror="this.parentElement.innerHTML='<div class=about-img-placeholder style=height:200px><svg width=60 height=60 viewBox=&quot;0 0 80 60&quot;><circle cx=60 cy=45 r=12 stroke=&quot;rgba(255,255,255,0.3)&quot; stroke-width=1.4 fill=none/><circle cx=18 cy=45 r=12 stroke=&quot;rgba(255,255,255,0.3)&quot; stroke-width=1.4 fill=none/></svg></div>'">
                </div>
                <div class="about-img-box tall">
                    <img src="Bikehub Image/Bikes/sworks.jpeg" alt="S-Works Bike"
                         onerror="this.parentElement.innerHTML='<div class=about-img-placeholder style=height:200px></div>'">
                </div>
                <div class="about-img-box wide">
                    <img src="Bikehub Image/Rider.jpg" alt="Rider"
                         onerror="this.parentElement.innerHTML='<div class=about-img-placeholder style=height:160px></div>'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── MISSION ── -->
<section class="mission-sec">
    <div class="container">
        <div class="mission-inner">
            <div>
                <div class="mission-label">Our Mission</div>
                <h2 class="mission-title">Cycling gear that<br>doesn't <span class="blue">compromise.</span></h2>
                <p class="mission-p">We believe every rider deserves access to world-class equipment — whether you're chasing podiums or just chasing the sunrise. BikeHub curates only the best, tested by riders for riders.</p>
                <p class="mission-p">From carbon road bikes to precision gear sets, every product we carry has earned its place through rigorous selection.</p>
                <a href="products.php" class="btn-blue-outline">
                    Shop our Range
                    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            <div class="mission-photo">
                <img src="Bikehub Image/Rider.jpg" alt="Rider on road bike"
                     onerror="this.parentElement.innerHTML='<div class=mission-photo-placeholder><svg width=80 height=80 viewBox=&quot;0 0 24 24&quot; fill=none stroke=&quot;#8896b8&quot; stroke-width=1><path d=&quot;M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2&quot;/><circle cx=12 cy=7 r=4/></svg></div>'">
            </div>
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
        <div><div class="footer-col-title">Company</div><ul class="footer-links"><li><a href="about.php">About Us</a></li><li><a href="about.php">Our Team</a></li><li><a href="contact.php">Contact</a></li><li><a href="#">Careers</a></li></ul></div>
        <div><div class="footer-col-title">Help</div><ul class="footer-links"><li><a href="#">FAQ</a></li><li><a href="#">Returns</a></li><li><a href="#">Shipping Info</a></li><li><a href="#">Track Order</a></li></ul></div>
    </div>
    <div class="footer-bottom">
        <span class="footer-copy">© 2026 BikeHub. All rights reserved.</span>
    </div>
</div></footer>
<script src="script.js"></script>
</body>
</html>