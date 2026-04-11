<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeHub — Create Account</title>
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
                <a href="products.php"                 class="dropdown-item">All Products</a>
            </div>
        </li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="nav-right"><a href="login.php" class="btn-nav-outline">Log In</a></div>
</div></nav>

<div class="auth-layout">
    <div class="auth-left">
        <div class="auth-brand">Bike<span>Hub</span></div>
        <h2 class="auth-heading">Join the<br>BikeHub<br>Community.</h2>
        <p class="auth-tagline">Create your account and start shopping, tracking orders, and unlocking member perks.</p>
        <div class="auth-features">
            <div class="auth-feat"><div class="feat-dot"></div><span>Free shipping on first order</span></div>
            <div class="auth-feat"><div class="feat-dot"></div><span>Exclusive member prices</span></div>
            <div class="auth-feat"><div class="feat-dot"></div><span>Early access to new arrivals</span></div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-title">Create Account</div>
        <div class="auth-subtitle">Takes less than a minute</div>

        <?php
        session_start();
        $reg_error = $_SESSION['reg_error'] ?? '';
        $reg_old   = $_SESSION['reg_old']   ?? [];
        unset($_SESSION['reg_error'], $_SESSION['reg_old']);

        if ($reg_error): ?>
            <div class="auth-server-error">
                <?= htmlspecialchars($reg_error) ?>
            </div>
        <?php endif; ?>

        <form id="register-form" action="php/register_process.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="firstname">First Name</label>
                    <input class="form-input" type="text" id="firstname" name="firstname"
                           placeholder="Alex" required
                           value="<?= htmlspecialchars($reg_old['firstname'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="lastname">Last Name</label>
                    <input class="form-input" type="text" id="lastname" name="lastname"
                           placeholder="Rider" required
                           value="<?= htmlspecialchars($reg_old['lastname'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="margin-top:16px">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email" name="email"
                       placeholder="you@email.com" required
                       value="<?= htmlspecialchars($reg_old['email'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-top:16px">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password"
                       placeholder="Min. 6 characters" required>
            </div>

            <!-- File Upload — handled by move_uploaded_file() in register.php -->
            <div class="form-group" style="margin-top:16px">
                <label class="form-label">
                    Profile Photo
                    <span style="color:var(--grey);font-weight:400;font-size:11px;text-transform:none">
                        (optional)
                    </span>
                </label>
                <label class="upload-zone" for="profile_img">
                    <svg viewBox="0 0 24 24"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/></svg>
                    <p>Drag &amp; drop or <strong>browse image</strong></p>
                    <p style="font-size:11px;color:var(--grey-l);margin-top:4px">JPG, PNG, WEBP — max 5MB</p>
                    <img id="img-preview" src="" alt=""
                         style="display:none;width:72px;height:72px;border-radius:50%;object-fit:cover;margin:12px auto 0">
                </label>
                <input type="file" id="profile_img" name="profile_img"
                       accept="image/*" style="display:none">
            </div>

            <div style="margin-top:20px">
                <button type="submit" name="register" class="form-submit">Create Account →</button>
            </div>
        </form>

        <div class="form-switch" style="margin-top:4px">
            Already have an account? <a href="login.php">Log In →</a>
        </div>
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