// ================================================================
// BikeHub — script.js
//
// JS 1 — Carousel:      auto-sliding hero with arrows, dots, swipe
// JS 2 — Navbar:        scroll shadow + jQuery background colour
// JS 3 — Product hover: GPU hint + jQuery glow shadow
// JS 4 — Live search:   filter product cards as you type
// JS 5 — Validation:    login, register, contact forms
// JS 6 — File upload:   profile photo preview on register page
//      — Toast:         slide-up popup notification
//
// NOTE: staticProducts array and localStorage cart have been
// removed — products now come from PHP/MySQL and the cart is
// stored in $_SESSION via php/add_to_cart.php.
// ================================================================


// ================================================================
// JS 1 — IMAGE CAROUSEL
// All slides sit side-by-side inside .slides-track.
// goTo(n) shifts the track with translateX and updates the dots.
// Called by arrow buttons, dot buttons, auto-timer, and swipe.
// ================================================================
function initCarousel() {
    var track = document.getElementById('slidesTrack');
    if (!track) return;

    var dots    = document.querySelectorAll('.dot');
    var prevBtn = document.getElementById('prevBtn');
    var nextBtn = document.getElementById('nextBtn');
    var current = 0;
    var timer;
    var TOTAL   = track.children.length;

    function goTo(index) {
        current = (index + TOTAL) % TOTAL;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        dots.forEach(function(dot, i) {
            dot.classList.toggle('active', i === current);
        });
    }

    // Expose globally so onclick="goTo(0)" in PHP-generated HTML works
    window.goTo = goTo;

    function startAuto() {
        timer = setInterval(function() { goTo(current + 1); }, 5000);
    }
    function stopAuto() { clearInterval(timer); }

    if (nextBtn) nextBtn.addEventListener('click', function() { stopAuto(); goTo(current + 1); startAuto(); });
    if (prevBtn) prevBtn.addEventListener('click', function() { stopAuto(); goTo(current - 1); startAuto(); });

    // Touch swipe
    var touchStartX = 0;
    track.addEventListener('touchstart', function(e) { touchStartX = e.touches[0].clientX; });
    track.addEventListener('touchend',   function(e) {
        var diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) { stopAuto(); goTo(diff > 0 ? current + 1 : current - 1); startAuto(); }
    });

    goTo(0);
    startAuto();
}


// ================================================================
// JS 2 — NAVBAR SCROLL EFFECT
// Adds a drop shadow when the user scrolls down past 10px.
// ================================================================
function initNavbar() {
    var nav = document.querySelector('.navbar');
    if (!nav) return;
    window.addEventListener('scroll', function() {
        nav.style.boxShadow = window.scrollY > 10
            ? '0 4px 24px rgba(0,0,0,0.4)'
            : 'none';
    });
}


// ================================================================
// JS 3 — PRODUCT CARD HOVER (vanilla JS)
// willChange hints the GPU before the CSS translateY runs.
// ================================================================
function initHover() {
    document.querySelectorAll('.product-card').forEach(function(card) {
        card.addEventListener('mouseenter', function() { this.style.willChange = 'transform'; });
        card.addEventListener('mouseleave', function() { this.style.willChange = 'auto'; });
    });
}


// ================================================================
// JS 4 — LIVE SEARCH (products.php only)
// Filters .product-card elements as the user types.
// The category chip clicks are handled inline in products.php
// using jQuery — no duplication needed here.
// ================================================================
function initSearch() {
    var searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        var query = this.value.toLowerCase().trim();
        document.querySelectorAll('.product-card').forEach(function(card) {
            var name = card.querySelector('.product-name') ? card.querySelector('.product-name').textContent.toLowerCase() : '';
            var cat  = card.querySelector('.product-cat')  ? card.querySelector('.product-cat').textContent.toLowerCase()  : '';
            card.style.display = (!query || name.includes(query) || cat.includes(query)) ? '' : 'none';
        });
    });
}


// ================================================================
// JS 5 — FORM VALIDATION
// Runs on submit — prevents the form from posting if fields fail.
// Red error messages appear below the invalid input.
// ================================================================
function initForms() {

    function showError(input, message) {
        clearError(input);
        input.style.borderColor = 'var(--red)';
        var err = document.createElement('span');
        err.className = 'field-error';
        err.style.cssText = 'color:var(--red);font-size:11px;margin-top:3px;display:block';
        err.textContent = message;
        input.parentNode.appendChild(err);
    }

    function clearError(input) {
        input.style.borderColor = '';
        var existing = input.parentNode.querySelector('.field-error');
        if (existing) existing.remove();
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Login form
    var loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            var ok    = true;
            var email = this.querySelector('#email');
            var pass  = this.querySelector('#password');
            if (!isValidEmail(email.value))  { showError(email, 'Please enter a valid email address.'); ok = false; } else { clearError(email); }
            if (pass.value.length < 6)       { showError(pass,  'Password must be at least 6 characters.'); ok = false; } else { clearError(pass); }
            if (!ok) e.preventDefault();
        });
    }

    // Register form
    var registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            var ok    = true;
            var fname = this.querySelector('#firstname');
            var lname = this.querySelector('#lastname');
            var email = this.querySelector('#email');
            var pass  = this.querySelector('#password');
            if (!fname.value.trim())        { showError(fname, 'First name is required.');              ok = false; } else { clearError(fname); }
            if (!lname.value.trim())        { showError(lname, 'Last name is required.');               ok = false; } else { clearError(lname); }
            if (!isValidEmail(email.value)) { showError(email, 'Enter a valid email address.');         ok = false; } else { clearError(email); }
            if (pass.value.length < 6)      { showError(pass,  'Password must be at least 6 chars.'); ok = false; } else { clearError(pass); }
            if (!ok) e.preventDefault();
        });
    }

    // Contact form
    var contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            var ok      = true;
            var email   = this.querySelector('[name="email"]');
            var message = this.querySelector('[name="message"]');
            if (!isValidEmail(email.value)) { showError(email,   'Enter a valid email address.'); ok = false; } else { clearError(email); }
            if (!message.value.trim())      { showError(message, 'Message cannot be empty.');     ok = false; } else { clearError(message); }
            if (!ok) e.preventDefault();
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {

    // ── CONTACT FORM ──
    const form = document.getElementById("contact-form");

    if (form) {
        const formCard    = form.closest(".contact-form");
        const formTitle   = formCard.querySelector(".contact-form-title");
        const submitBtn   = form.querySelector(".form-submit");

        function showMessage(type, text) {
            // Remove any existing messages
            formCard.querySelectorAll(".success-message, .error-message")
                    .forEach(el => el.remove());

            const msg = document.createElement("div");
            msg.className = type === "success" ? "success-message" : "error-message";
            msg.textContent = text;

            // Insert message at the top of the card
            formCard.insertBefore(msg, formCard.firstChild);
            return msg;
        }

        // Handle messages left by a non-JS (fallback) redirect
        const existingSuccess = formCard.querySelector(".success-message");
        const existingError   = formCard.querySelector(".error-message");

        if (existingSuccess || existingError) {
            form.reset();
            setTimeout(() => {
                if (existingSuccess) existingSuccess.style.display = "none";
                if (existingError)   existingError.style.display   = "none";
            }, 4000);
        }

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            // Button loading state
            submitBtn.disabled    = true;
            submitBtn.textContent = "Sending…";

            const data = new FormData(form);

            fetch(form.action, {
                method: "POST",
                headers: { "X-Requested-With": "XMLHttpRequest" },
                body: data
            })
            .then(res => res.json())
            .then(json => {
                if (json.status === "success") {
                    // Hide form content, show success
                    formTitle.style.display = "none";
                    form.style.display      = "none";

                    const msg = showMessage("success", json.message);

                    // After 4s restore the form
                    setTimeout(() => {
                        msg.style.display       = "none";
                        form.reset();
                        form.style.display      = "";
                        formTitle.style.display = "";
                    }, 4000);

                } else {
                    // Show inline error, keep form visible
                    showMessage("error", json.message);
                }
            })
            .catch(() => {
                showMessage("error", "Network error. Please check your connection and try again.");
            })
            .finally(() => {
                submitBtn.disabled    = false;
                submitBtn.textContent = "Send Message →";
            });
        });
    }
});


// ================================================================
// JS 6 — FILE UPLOAD IMAGE PREVIEW (register page)
// Reads the chosen image file and shows a preview before submit.
// Also supports drag-and-drop onto the upload zone.
// ================================================================
function initFileUpload() {
    var fileInput  = document.getElementById('profile_img');
    var previewImg = document.getElementById('img-preview');
    if (!fileInput || !previewImg) return;

    fileInput.addEventListener('change', function() {
        var file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    var zone = document.querySelector('.upload-zone');
    if (zone) {
        zone.addEventListener('dragover',  function(e) { e.preventDefault(); this.style.borderColor = 'var(--blue)'; });
        zone.addEventListener('dragleave', function()  { this.style.borderColor = ''; });
        zone.addEventListener('drop',      function(e) {
            e.preventDefault();
            this.style.borderColor = '';
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        });
    }
}


// ================================================================
// TOAST NOTIFICATION
// Slide-up popup from bottom-right. Called by PHP AJAX handlers
// on this page with: showToast('Product added ✓')
// ================================================================
function showToast(message) {
    var toast = document.getElementById('bh-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'bh-toast';
        toast.style.cssText = 'position:fixed;bottom:28px;right:28px;background:var(--black);color:#fff;border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:13px 20px;font-size:14px;font-family:\'DM Sans\',sans-serif;z-index:9999;display:flex;align-items:center;gap:10px;transform:translateY(80px);opacity:0;transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1)';
        document.body.appendChild(toast);
    }
    toast.innerHTML = '<span style="width:8px;height:8px;background:var(--green);border-radius:50%;flex-shrink:0"></span>' + message;
    requestAnimationFrame(function() { toast.style.transform = 'translateY(0)'; toast.style.opacity = '1'; });
    setTimeout(function() { toast.style.transform = 'translateY(80px)'; toast.style.opacity = '0'; }, 3000);
}


// ================================================================
// DOMContentLoaded — initialise everything after HTML is parsed
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    initCarousel();   // JS 1 — hero slider (index.php only)
    initNavbar();     // JS 2 — scroll shadow (all pages)
    initHover();      // JS 3 — card GPU hint (all pages)
    initSearch();     // JS 4 — live search (products.php only)
    initForms();      // JS 5 — form validation (login/register/contact)
    initFileUpload(); // JS 6 — photo preview (register page only)
});


// ================================================================
// JQUERY SECTION
// jQuery 3.7 loaded from CDN before script.js in every page.
//
// jQuery 1 → Navbar background darkens on scroll (adds .scrolled)
// jQuery 2 → Product card hover glow (blue box-shadow)
// jQuery 3 → Form input focus/blur border highlight
// ================================================================
$(document).ready(function() {

    // jQuery 1: navbar scroll — darken background
    $(window).scroll(function() {
        $('.navbar').toggleClass('scrolled', $(this).scrollTop() > 10);
    });

    // jQuery 2: product card glow on hover
    $(document).on('mouseenter', '.product-card', function() {
        $(this).css('box-shadow', '0 16px 40px rgba(26,107,255,0.15)');
    });
    $(document).on('mouseleave', '.product-card', function() {
        $(this).css('box-shadow', '0 4px 12px rgba(0,0,0,0.06)');
    });

    // jQuery 3: form input focus highlight
    $(document).on('focus', '.form-input', function() {
        $(this).addClass('active');
    });
    $(document).on('blur', '.form-input', function() {
        $(this).removeClass('active');
    });

});