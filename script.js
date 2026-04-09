// ================================================================
// BikeHub — script.js
// Main JavaScript file for the BikeHub cycling store website
//
// What this file covers:
//   JS 1 — Carousel:     auto-sliding hero with arrows, dots, swipe
//   JS 2 — Navbar:       scroll shadow effect on sticky navbar
//   JS 3 — Product hover: GPU hint for smooth card hover animation
//   JS 4 — Live search:  filter product cards as you type + category chips
//   JS 5 — Validation:   check login, register, contact forms before submit
//   JS 6 — File upload:  preview profile photo before register submits
//        — Cart:         add/remove/update using localStorage
//        — Toast:        slide-up popup notification
//
// Data flow (frontend only, until PHP connects):
//   staticProducts array → renderProducts() → HTML product cards in DOM
//   Add to Cart button   → addToCart()      → cart array → localStorage
//   Cart page loads      → renderCart()     → reads localStorage → HTML
//
// When backend connects:
//   staticProducts → replaced by PHP fetch from products table
//   localStorage cart → replaced by PHP $_SESSION['cart']
// ================================================================


// ================================================================
// PRODUCTS DATA
// Array of product objects used as placeholder until PHP/MySQL
// is connected. Each object has these properties:
//   id       - unique number, used to identify item in cart
//   name     - product display name
//   cat      - category string matching filter chip data-cat values:
//              'bikes' | 'gears' | 'helmets' | 'accessories'
//   badge    - 'NEW' | 'SALE' | '' (empty = no badge)
//   price    - current selling price in USD
//   oldPrice - original price shown with strikethrough if on sale, null if not
//   img      - relative path to image inside Bikehub Image/ folder
//   desc     - short product description on card
// ================================================================


// ================================================================
// CART — localStorage keeps cart data between page visits
//
// localStorage.getItem() returns a JSON string or null
// JSON.parse() converts that string back into a JS array
// If nothing is stored yet, start with empty array []
//
// Cart items look like: { id, name, price, img, qty }
// ================================================================
let cart = JSON.parse(localStorage.getItem('bh_cart')) || [];

// Write the current cart array to localStorage as a JSON string
function saveCart() {
    localStorage.setItem('bh_cart', JSON.stringify(cart));
}

// Count all items in cart and show on the cart icon badge
// Uses Array.reduce() to sum all qty values
function updateCartBadge() {
    const totalItems = cart.reduce((total, item) => total + item.qty, 0);

    // Update every element with id="cart-count" on the page
    document.querySelectorAll('#cart-count').forEach(function(badge) {
        badge.textContent = totalItems;
        badge.style.display = totalItems > 0 ? 'flex' : 'none';
    });
}

// Add a product to cart by its id
// If already in cart → increase qty, else push a new object
function addToCart(id, name, price, img) {
    // Array.find() returns the matching object or undefined
    const existing = cart.find(function(item) { return item.id === id; });

    if (existing) {
        existing.qty++; // product already in cart, just add one more
    } else {
        cart.push({ id: id, name: name, price: price, img: img, qty: 1 });
    }

    saveCart();
    updateCartBadge();
    showToast(name + ' added to cart ✓');
}

// Completely remove a product from cart
// Array.filter() returns a new array without the matching item
function removeFromCart(id) {
    cart = cart.filter(function(item) { return item.id !== id; });
    saveCart();
    updateCartBadge();
    renderCart(); // refresh the cart page HTML
}

// Change quantity of a cart item by delta (+1 or -1)
// If qty reaches 0, remove the item completely
function updateQty(id, delta) {
    const item = cart.find(function(i) { return i.id === id; });
    if (!item) return;

    item.qty += delta;

    if (item.qty <= 0) {
        removeFromCart(id); // qty hit zero so remove it
    } else {
        saveCart();
        updateCartBadge();
        renderCart();
    }
}

// Build and display the cart on cart.html
// Reads cart array, creates HTML, calculates totals
function renderCart() {
    var list       = document.getElementById('cart-items-list');
    var countEl    = document.getElementById('cart-item-count');
    var subtotalEl = document.getElementById('summary-subtotal');
    var shippingEl = document.getElementById('summary-shipping');
    var taxEl      = document.getElementById('summary-tax');
    var totalEl    = document.getElementById('summary-total');

    if (!list) return; // not on cart page, stop here

    // Update the "(2 items)" label next to heading
    var totalQty = cart.reduce(function(t, i) { return t + i.qty; }, 0);
    if (countEl) countEl.textContent = '(' + totalQty + ' ' + (totalQty === 1 ? 'item' : 'items') + ')';

    // Show empty state if cart has nothing
    if (cart.length === 0) {
        list.innerHTML = '<div class="cart-empty">'
            + '<svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>'
            + '<h3>Your cart is empty</h3>'
            + '<p>Looks like you haven\'t added anything yet.</p>'
            + '<a href="products.php" class="btn-blue-solid">Browse Products</a>'
            + '</div>';
        if (subtotalEl) subtotalEl.textContent = '$0.00';
        if (totalEl)    totalEl.textContent    = '$0.00';
        return;
    }

    // Build item rows and calculate subtotal at the same time
    var subtotal = 0;
    var html = '';

    cart.forEach(function(item) {
        subtotal += item.price * item.qty;

        html += '<div class="cart-item">'
            + '<div class="cart-item-img">'
            // Real product image from Bikehub Image/ folder
            + '<img src="' + item.img + '" alt="' + item.name + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\'">'
            // SVG fallback if image path doesn't work
            + '<svg viewBox="0 0 40 30" style="display:none"><circle cx="30" cy="22" r="7" stroke="#8896b8" stroke-width="1.4" fill="none"/><circle cx="9" cy="22" r="7" stroke="#8896b8" stroke-width="1.4" fill="none"/><path d="M24 9H15L9 22" stroke="#8896b8" stroke-width="1.4" fill="none"/></svg>'
            + '</div>'
            + '<div class="cart-info">'
            + '<div class="cart-name">' + item.name + '</div>'
            + '<div class="cart-sub">$' + item.price.toFixed(2) + ' each</div>'
            // Quantity controls call updateQty() with +1 or -1
            + '<div class="qty-row">'
            + '<button class="qty-btn" onclick="updateQty(' + item.id + ', -1)">−</button>'
            + '<span class="qty-num">' + item.qty + '</span>'
            + '<button class="qty-btn" onclick="updateQty(' + item.id + ', +1)">+</button>'
            + '</div>'
            + '</div>'
            // Line total = price × quantity
            + '<div class="cart-price">$' + (item.price * item.qty).toFixed(2) + '</div>'
            // Remove button calls removeFromCart with this item's id
            + '<button class="cart-remove" onclick="removeFromCart(' + item.id + ')" title="Remove">×</button>'
            + '</div>';
    });

    list.innerHTML = html;

    // Calculate summary totals
    var shipping = subtotal >= 200 ? 0 : 25; // free shipping if order over $200
    var tax      = subtotal * 0.08;           // 8% tax
    var total    = subtotal + shipping + tax;

    if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2);
    if (shippingEl) {
        shippingEl.textContent = shipping === 0 ? 'Free' : '$' + shipping.toFixed(2);
        shippingEl.className   = shipping === 0 ? 'free-txt' : ''; // green colour for free
    }
    if (taxEl)   taxEl.textContent   = '$' + tax.toFixed(2);
    if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
}


// ================================================================
// PRODUCT CARD RENDERER
// Builds HTML product card elements and injects them into a grid
// Usage: renderProducts('featured-grid', arrayOfProducts)
//
// Card structure:
//   - Product image (from Bikehub Image/ folder) + onerror fallback SVG
//   - NEW or SALE badge (only if product.badge is not empty)
//   - Category label (formatted nicely), name, description
//   - Price with strikethrough old price if on sale
//   - Add to Cart button → calls addToCart()
// ================================================================
function renderProducts(containerId, products) {
    var container = document.getElementById(containerId);
    if (!container) return;

    // Show "no results" message if array is empty
    if (products.length === 0) {
        container.innerHTML = '<div style="grid-column:span 3; text-align:center; padding:60px; color:var(--grey)"><p style="font-size:16px">No products found. Try a different search.</p></div>';
        return;
    }

    var html = '';

    products.forEach(function(p) {
        // Format the category name for display
        var catName = p.cat
            .replace('bikes',       'Road Bike')
            .replace('gears',       'Gear Set')
            .replace('helmets',     'Helmet')
            .replace('accessories', 'Accessory');

        // Only show badge span if badge string is not empty
        var badgeHtml = '';
        if (p.badge) {
            var badgeClass = p.badge === 'NEW' ? 'badge-new' : 'badge-sale';
            badgeHtml = '<span class="product-badge ' + badgeClass + '">' + p.badge + '</span>';
        }

        // Show old price with strikethrough only if product has oldPrice
        var priceHtml = '';
        if (p.oldPrice) {
            priceHtml = '<span class="product-old-price">$' + p.oldPrice.toFixed(2) + '</span>';
        }
        priceHtml += ' $' + p.price.toFixed(2);

        html += '<div class="product-card" data-cat="' + p.cat + '">'
            + '<div class="product-img">'
            + badgeHtml
            // Real image from Bikehub Image/ — onerror shows SVG fallback
            + '<img src="' + p.img + '" alt="' + p.name + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">'
            + '<div class="product-img-placeholder" style="display:none"><svg viewBox="0 0 80 60"><circle cx="60" cy="45" r="12"/><circle cx="18" cy="45" r="12"/><path d="M48 18H30L18 45M48 18l8 18-12 9"/></svg></div>'
            + '</div>'
            + '<div class="product-body">'
            + '<div class="product-cat">' + catName + '</div>'
            + '<div class="product-name">' + p.name + '</div>'
            + '<div class="product-desc">' + p.desc + '</div>'
            + '<div class="product-footer">'
            + '<div class="product-price">' + priceHtml + '</div>'
            // onclick passes id, name, price and img path to addToCart
            + '<button class="add-btn" onclick="addToCart(' + p.id + ', \'' + p.name + '\', ' + p.price + ', \'' + p.img + '\')">'
            + '<svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
            + '</button>'
            + '</div>'
            + '</div>'
            + '</div>';
    });

    container.innerHTML = html;
}


// ================================================================
// JS 1 — IMAGE CAROUSEL
// How it works:
//   All slides sit side-by-side inside .slides-track
//   Moving the track left by (index × 100%) shows the right slide
//   CSS transition: transform makes the movement smooth
//
// goTo(n) is the core function — it moves the track and updates dots
// Called by: arrow buttons, dot buttons, auto-timer, touch swipe
// ================================================================
function initCarousel() {
    var track = document.getElementById('slidesTrack');
    if (!track) return; // carousel only exists on index.html

    var dots    = document.querySelectorAll('.dot');
    var prevBtn = document.getElementById('prevBtn');
    var nextBtn = document.getElementById('nextBtn');
    var current = 0;   // which slide is currently showing
    var timer;         // reference to setInterval so we can stop it

    var TOTAL = track.children.length; // how many slides there are (3)

    // Move to a specific slide index
    function goTo(index) {
        // Wrap around using modulo — so after slide 2 comes slide 0 again
        current = (index + TOTAL) % TOTAL;

        // Shift the track: showing slide 0 = 0%, slide 1 = -100%, slide 2 = -200%
        track.style.transform = 'translateX(-' + (current * 100) + '%)';

        // Update dots — active dot becomes a wider pill shape via CSS
        dots.forEach(function(dot, i) {
            dot.classList.toggle('active', i === current);
        });
    }

    // Expose goTo globally so onclick="goTo(0)" in HTML buttons works
    window.goTo = goTo;

    // Auto-advance: move to next slide every 5 seconds
    function startAuto() {
        timer = setInterval(function() {
            goTo(current + 1);
        }, 5000);
    }

    // Stop the timer so user interactions don't conflict with auto-advance
    function stopAuto() {
        clearInterval(timer);
    }

    // Next arrow button
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            stopAuto();
            goTo(current + 1);
            startAuto(); // restart so timer resets after manual click
        });
    }

    // Previous arrow button
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            stopAuto();
            goTo(current - 1);
            startAuto();
        });
    }

    // Touch swipe support (mobile devices)
    var touchStartX = 0;

    track.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX; // record where finger started
    });

    track.addEventListener('touchend', function(e) {
        var diff = touchStartX - e.changedTouches[0].clientX; // distance moved
        if (Math.abs(diff) > 50) { // only register as swipe if > 50px
            stopAuto();
            goTo(diff > 0 ? current + 1 : current - 1); // left swipe = next
            startAuto();
        }
    });

    goTo(0);     // initialise at slide 0
    startAuto(); // begin auto-advance
}


// ================================================================
// JS 2 — NAVBAR SCROLL EFFECT
// When the user scrolls down, we add a drop shadow to the sticky
// navbar so it visually separates from the page content
// Uses the scroll event listener on the window object
// ================================================================
function initNavbar() {
    var nav = document.querySelector('.navbar');
    if (!nav) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            nav.style.boxShadow = '0 4px 24px rgba(0,0,0,0.4)';
        } else {
            nav.style.boxShadow = 'none';
        }
    });
}


// ================================================================
// JS 3 — PRODUCT CARD HOVER ANIMATION
// willChange is a CSS hint telling the browser to prepare the GPU
// for an upcoming transform (the hover translateY animation)
// This makes the card lift animation smoother and avoids jank
// Reset to 'auto' on mouseleave so we don't waste GPU resources
// ================================================================
function initHover() {
    document.querySelectorAll('.product-card').forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.willChange = 'transform';
        });
        card.addEventListener('mouseleave', function() {
            this.style.willChange = 'auto';
        });
    });
}


// ================================================================
// JS 4 — LIVE SEARCH + CATEGORY FILTER (products page only)
//
// Part A — Search box:
//   Listens for 'input' event on the search field
//   On each keypress, loops through all .product-card elements
//   Shows/hides each card depending on whether its name or
//   category text includes the search query
//
// Part B — Category filter chips:
//   Each chip has data-cat attribute ('all', 'bikes', 'gears' etc.)
//   Clicking a chip filters the visible cards to that category
//   Also reads ?cat= URL parameter on page load so links like
//   products.php?cat=bikes pre-select the Bikes chip
// ================================================================
function initSearch() {
    var searchInput = document.getElementById('search-input');

    // Part A: live search as user types
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();

            document.querySelectorAll('.product-card').forEach(function(card) {
                var name = card.querySelector('.product-name') ? card.querySelector('.product-name').textContent.toLowerCase() : '';
                var cat  = card.querySelector('.product-cat')  ? card.querySelector('.product-cat').textContent.toLowerCase()  : '';

                // Show card if it matches query, hide if not
                card.style.display = (name.includes(query) || cat.includes(query)) ? '' : 'none';
            });
        });
    }

    // Part B: category chip clicks
    var chips = document.querySelectorAll('.chip[data-cat]');

    // Read ?cat= from URL to highlight correct chip on load
    var urlParams   = new URLSearchParams(window.location.search);
    var activeCat   = urlParams.get('cat') || 'all';

    chips.forEach(function(chip) {
        // Mark this chip active if it matches URL param
        if (chip.dataset.cat === activeCat) chip.classList.add('active');

        chip.addEventListener('click', function(e) {
            e.preventDefault(); // prevent <a> from reloading page

            // Switch active chip styling
            chips.forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');

            var selected = this.dataset.cat;

            // Show only matching category cards (or all if 'all')
            document.querySelectorAll('.product-card').forEach(function(card) {
                card.style.display = (selected === 'all' || card.dataset.cat === selected) ? '' : 'none';
            });
        });
    });

    // Apply URL category filter on page load
    if (activeCat !== 'all') {
        document.querySelectorAll('.product-card').forEach(function(card) {
            card.style.display = card.dataset.cat === activeCat ? '' : 'none';
        });
    }
}


// ================================================================
// JS 5 — FORM VALIDATION
// Runs when a form is submitted — checks required fields
// If any field is invalid: shows red error message, stops submit
// If all valid: form submits normally to its PHP action file
//
// Forms validated:
//   #login-form    → email format + password length
//   #register-form → all fields + password length
//   #contact-form  → email format + message not empty
// ================================================================
function initForms() {

    // Add a red error message below a form input
    function showError(input, message) {
        clearError(input);
        input.style.borderColor = 'var(--red)';

        var err = document.createElement('span');
        err.className  = 'field-error';
        err.style.cssText = 'color:var(--red); font-size:11px; margin-top:3px; display:block';
        err.textContent = message;
        input.parentNode.appendChild(err);
    }

    // Remove error styling from a field
    function clearError(input) {
        input.style.borderColor = '';
        var existing = input.parentNode.querySelector('.field-error');
        if (existing) existing.remove();
    }

    // Simple email format check using regex
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // ── Login form ──
    var loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            var ok    = true;
            var email = this.querySelector('#email');
            var pass  = this.querySelector('#password');

            if (!isValidEmail(email.value)) {
                showError(email, 'Please enter a valid email address.'); ok = false;
            } else { clearError(email); }

            if (pass.value.length < 6) {
                showError(pass, 'Password must be at least 6 characters.'); ok = false;
            } else { clearError(pass); }

            if (!ok) e.preventDefault(); // block submit if invalid
        });
    }

    // ── Register form ──
    var registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            var ok    = true;
            var fname = this.querySelector('#firstname');
            var lname = this.querySelector('#lastname');
            var email = this.querySelector('#email');
            var pass  = this.querySelector('#password');

            if (!fname.value.trim()) { showError(fname, 'First name is required.');               ok = false; } else { clearError(fname); }
            if (!lname.value.trim()) { showError(lname, 'Last name is required.');                ok = false; } else { clearError(lname); }
            if (!isValidEmail(email.value)) { showError(email, 'Enter a valid email address.'); ok = false; } else { clearError(email); }
            if (pass.value.length < 6) { showError(pass, 'Password must be at least 6 characters.'); ok = false; } else { clearError(pass); }

            if (!ok) e.preventDefault();
        });
    }

    // ── Contact form ──
    var contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            var ok      = true;
            var email   = this.querySelector('[name="email"]');
            var message = this.querySelector('[name="message"]');

            if (!isValidEmail(email.value)) { showError(email, 'Enter a valid email address.'); ok = false; } else { clearError(email); }
            if (!message.value.trim())       { showError(message, 'Message cannot be empty.');  ok = false; } else { clearError(message); }

            if (!ok) e.preventDefault();
        });
    }
}


// ================================================================
// JS 6 — FILE UPLOAD IMAGE PREVIEW (register page)
// When user selects a profile photo file:
//   FileReader reads it as a data URL (base64 encoded string)
//   That URL becomes the src of the <img id="img-preview"> element
//   The image becomes visible so user can see their chosen photo
//
// Also handles drag-and-drop onto the upload zone box
// ================================================================
function initFileUpload() {
    var fileInput  = document.getElementById('profile_img');
    var previewImg = document.getElementById('img-preview');

    if (!fileInput || !previewImg) return; // not on register page

    // Standard file input change event
    fileInput.addEventListener('change', function() {
        var file = this.files[0];

        if (file && file.type.startsWith('image/')) {
            var reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result; // set base64 string as src
                previewImg.style.display = 'block'; // make visible
            };

            reader.readAsDataURL(file); // triggers onload when done
        }
    });

    // Drag-and-drop support
    var zone = document.querySelector('.upload-zone');
    if (zone) {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();                    // allow drop to happen
            this.style.borderColor = 'var(--blue)'; // highlight border
        });

        zone.addEventListener('dragleave', function() {
            this.style.borderColor = ''; // remove highlight
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '';
            fileInput.files = e.dataTransfer.files;           // pass dropped file to input
            fileInput.dispatchEvent(new Event('change')); // trigger preview
        });
    }
}


// ================================================================
// TOAST NOTIFICATION
// Slide-up popup from bottom-right corner of screen
// Used to confirm actions like adding an item to cart
// Auto-hides after 3 seconds
// ================================================================
function showToast(message) {
    var toast = document.getElementById('bh-toast');

    // Create the element if it doesn't already exist in the DOM
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'bh-toast';
        toast.style.cssText = 'position:fixed; bottom:28px; right:28px; background:var(--black); color:#fff; border:1px solid rgba(255,255,255,0.1); border-radius:14px; padding:13px 20px; font-size:14px; font-family:\'DM Sans\',sans-serif; z-index:9999; display:flex; align-items:center; gap:10px; transform:translateY(80px); opacity:0; transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1)';
        document.body.appendChild(toast);
    }

    // Green dot + message text
    toast.innerHTML = '<span style="width:8px;height:8px;background:var(--green);border-radius:50%;flex-shrink:0"></span>' + message;

    // Animate in
    requestAnimationFrame(function() {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity   = '1';
    });

    // Animate out after 3 seconds
    setTimeout(function() {
        toast.style.transform = 'translateY(80px)';
        toast.style.opacity   = '0';
    }, 3000);
}


// ================================================================
// DOMContentLoaded — runs all initialisations after HTML is parsed
//
// Checks which page we're on by looking for specific elements
// and only runs the relevant code for that page
// ================================================================
document.addEventListener('DOMContentLoaded', function() {

    // These run on every page
    updateCartBadge(); // show cart item count on nav icon
    renderCart();      // build cart HTML if on cart.html
    initCarousel();    // set up carousel if on index.html
    initNavbar();      // scroll shadow on all pages
    initForms();       // validate any forms on the page
    initFileUpload();  // profile photo preview on register.html

    // Featured products grid on homepage (index.html)
    var featuredGrid = document.getElementById('featured-grid');
    if (featuredGrid) {
        // Show 3 products that have a NEW or SALE badge
        var featured = staticProducts.filter(function(p) { return p.badge !== ''; }).slice(0, 3);
        renderProducts('featured-grid', featured);
    }

    // All products grid on products.php
    var allGrid = document.getElementById('all-products-grid');
    if (allGrid) {
        // Check ?cat= URL parameter to filter on load
        var params  = new URLSearchParams(window.location.search);
        var catUrl  = params.get('cat') || 'all';

        var toShow = catUrl === 'all'
            ? staticProducts
            : staticProducts.filter(function(p) { return p.cat === catUrl; });

        renderProducts('all-products-grid', toShow);

        // Initialise search and hover after cards are rendered
        setTimeout(function() {
            initSearch();
            initHover();
        }, 50);
    }

    // Always init hover on any product cards on the page
    initHover();
});


// ================================================================
// JQUERY SECTION
// jQuery is loaded via CDN in each HTML file's <head>:
// <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
//
// We use jQuery here for 3 specific interactions.
// The $ symbol is jQuery's shorthand — $('selector') selects elements
// just like document.querySelector() but shorter and cross-browser.
//
// jQuery 1 → Navbar background colour change on scroll
// jQuery 2 → Product card animated hover using .hover()
// jQuery 3 → Form input border highlight using .focus() and .blur()
// ================================================================

$(document).ready(function() {
    // $(document).ready() is jQuery's version of DOMContentLoaded
    // It waits for the full HTML to load before running anything inside


    // ──────────────────────────────────────────────────────────────
    // JQUERY 1: NAVBAR SCROLL — background colour change
    //
    // When the user scrolls down more than 10px, we add the class
    // 'scrolled' to the navbar, which changes its background to
    // a more opaque black via CSS.
    //
    // $(window).scroll()  → fires every time the user scrolls
    // $(this).scrollTop() → how many px the page has scrolled
    // $('.navbar')        → selects the nav element
    // .addClass()         → adds a CSS class to the element
    // .removeClass()      → removes a CSS class from the element
    // ──────────────────────────────────────────────────────────────
    $(window).scroll(function() {
        if ($(this).scrollTop() > 10) {
            // User has scrolled down — darken the navbar background
            $('.navbar').addClass('scrolled');
        } else {
            // Back at top — remove the darker background
            $('.navbar').removeClass('scrolled');
        }
    });


    // ──────────────────────────────────────────────────────────────
    // JQUERY 2: PRODUCT CARD HOVER EFFECT
    //
    // .hover() takes two functions:
    //   First  function → runs when mouse enters the element
    //   Second function → runs when mouse leaves the element
    //
    // $(this) inside .hover() refers to the specific card being hovered
    // .css() sets an inline style on the element
    //
    // Note: the lift animation (translateY) is already in CSS —
    // we add an extra blue shadow here for an extra visual touch
    // ──────────────────────────────────────────────────────────────
    $(document).on('mouseenter', '.product-card', function() {
        // Mouse entered card — add a glowing blue shadow
        $(this).css('box-shadow', '0 16px 40px rgba(26,107,255,0.15)');
    });

    $(document).on('mouseleave', '.product-card', function() {
        // Mouse left card — remove the glow, go back to subtle shadow
        $(this).css('box-shadow', '0 4px 12px rgba(0,0,0,0.06)');
    });


    // ──────────────────────────────────────────────────────────────
    // JQUERY 3: FORM INPUT FOCUS HIGHLIGHT
    //
    // When a user clicks into an input field (.focus), we add a class
    // 'active' which makes the border turn blue and background white.
    // When they click out (.blur), we remove that class.
    //
    // .focus()  → fires when the element receives keyboard/mouse focus
    // .blur()   → fires when the element loses focus
    // $(this)   → the specific input that was focused/blurred
    // .addClass() / .removeClass() → toggle the 'active' CSS class
    //
    // This works on all pages with .form-input elements
    // (login, register, contact forms)
    // ──────────────────────────────────────────────────────────────
    $(document).on('focus', '.form-input', function() {
        // Input is focused — highlight it with blue border
        $(this).addClass('active');
    });

    $(document).on('blur', '.form-input', function() {
        // Input lost focus — remove the highlight
        $(this).removeClass('active');
    });

});