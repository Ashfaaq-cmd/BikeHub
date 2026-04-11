<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeHub — Log In</title>
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
                <a href="products.php?cat=bikes"       class="dropdown-item">Road Bikes</a>
                <a href="products.php?cat=gears"       class="dropdown-item">Gear Sets</a>
                <a href="products.php?cat=helmet"      class="dropdown-item">Helmets</a>
                <a href="products.php?cat=accessories" class="dropdown-item">Accessories</a>
                <div class="dropdown-sep"></div>
                <a href="products.php" class="dropdown-item">All Products</a>
            </div>
        </li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="nav-right">
        <a href="register.php" class="btn-nav-blue">Sign Up</a>
    </div>
</div></nav>

<div class="auth-layout">
    <div class="auth-left">
        <div class="auth-brand">Bike<span>Hub</span></div>
        <h2 class="auth-heading">Welcome Back,<br>Rider.</h2>
        <p class="auth-tagline">Log in to track your orders, manage your cart, and access exclusive member deals.</p>
        <div class="auth-features">
            <div class="auth-feat"><div class="feat-dot"></div><span>Full order history &amp; tracking</span></div>
            <div class="auth-feat"><div class="feat-dot"></div><span>Cart synced across devices</span></div>
            <div class="auth-feat"><div class="feat-dot"></div><span>Member-only discounts</span></div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-title">Log In</div>
        <div class="auth-subtitle">Enter your credentials to continue</div>

        <?php
        session_start();
        if (!empty($_SESSION['login_error'])): 
        ?>
            <div class="auth-server-error">
                <?= htmlspecialchars($_SESSION['login_error']) ?>
            </div>
        <?php
            unset($_SESSION['login_error']);
        endif; ?>

        <form id="login-form" action="php/login_process.php" method="POST">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email" name="email"
                       placeholder="you@email.com" required
                       value="<?= htmlspecialchars($_SESSION['login_old_email'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin-top:16px">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password"
                       placeholder="••••••••••" required>
            </div>
            <div style="margin-top:20px">
                <button type="submit" name="login" class="form-submit">Log In →</button>
            </div>
        </form>

        <div class="form-divider">or</div>
        <div class="form-switch">Don't have an account? <a href="register.php">Create one →</a></div>
    </div>
</div>

<style>
.auth-server-error {
    background: rgba(239,68,68,.1);
    border: 1px solid rgba(239,68,68,.35);
    color: #f87171;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 14px;
    margin-bottom: 20px;
}
</style>

<script src="script.js"></script>
</body>
</html>