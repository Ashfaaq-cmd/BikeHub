<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeHub — Contact</title>
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
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php" class="active">Contact</a></li>
    </ul>
    <div class="nav-right">
        <a href="cart.php" class="cart-link"><svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg><span class="cart-badge" id="cart-count">0</span></a>
        <a href="account.php" class="btn-nav-blue">Account</a>
    </div>
</div></nav>

<section class="contact-page">
    <div class="container">
        <div class="contact-grid">

            <!-- LEFT: Contact Info -->
            <div>
                <h2 class="contact-title">Get in Touch<br>With Us.</h2>
                <p class="contact-sub">Have a question about a product, an order, or just want to chat bikes? We're here — always.</p>
                <div class="contact-items">
                    <div class="contact-item">
                        <div class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
                        <div><div class="contact-item-title">Email</div><div class="contact-item-val">hello@bikehub.com</div></div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 2 2 0 012 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6z"/></svg></div>
                        <div><div class="contact-item-title">Phone</div><div class="contact-item-val">+1 (555) 200-BIKE</div></div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                        <div><div class="contact-item-title">Location</div><div class="contact-item-val">12 Rider St, Cycle City</div></div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Form  action="php/contact.php" method="POST", INSERT to DB -->
            <div class="contact-form">
                <div class="contact-form-title">Send a message</div>
                <form id="contact-form" action="php/contact_process.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input class="form-input" type="text" name="firstname" placeholder="Your name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input class="form-input" type="text" name="lastname" placeholder="Last name">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:14px">
                        <label class="form-label">Email Address</label>
                        <input class="form-input" type="email" name="email" placeholder="you@email.com" required>
                    </div>
                    <div class="form-group" style="margin-top:14px">
                        <label class="form-label">Phone</label>
                        <input class="form-input" type="tel" name="phone" placeholder="+1 (555) 000-0000">
                    </div>
                    <div class="form-group" style="margin-top:14px">
                        <label class="form-label">Subject</label>
                        <input class="form-input" type="text" name="subject" placeholder="Product enquiry..." required>
                    </div>
                    <div class="form-group" style="margin-top:14px">
                        <label class="form-label">Message</label>
                        <textarea class="form-textarea" name="message" placeholder="Tell us how we can help..." required></textarea>
                    </div>
                    <div style="margin-top:16px">
                        <button type="submit" name="submit" class="form-submit">Send Message →</button>
                    </div>
                </form>
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
</body></html>