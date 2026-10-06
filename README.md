# BikeHub 🚴

> Mauritius's premier online destination for premium road bikes and cycling gear.

A full-stack e-commerce web application built for the **LLC2020Y Web Application & Technologies** module at the **University of Mauritius**.

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3 (custom design system) |
| Scripting | JavaScript ES6 + jQuery 3.7.1 |
| Backend | PHP 8 (procedural + MySQLi) |
| Database | MySQL 8 via XAMPP |
| Server | Apache (XAMPP on Windows) |
| Fonts | Syne + DM Sans (Google Fonts) |
| Version Control | Git + GitHub |

---

## 📁 Project Structure

```
Bikehub1.1/
│
├── index.php                  # Home — hero carousel, categories, featured products
├── products.php               # All products with live search + category filter
├── cart.php                   # Session-based shopping cart with AJAX qty controls
├── login.php                  # Login page (split-screen layout)
├── register.php               # Registration page with profile photo upload
├── contact.php                # Contact form page
├── about.php                  # About Us page
├── checkout.php               # Order confirmation — inserts order + order_items
│
├── php/                       # All PHP handlers (backend)
│   ├── db.php                 # mysqli_connect() — single DB connection
│   ├── login.php              # Login handler — SELECT + password_verify()
│   ├── register_process.php   # Register handler — INSERT + file upload
│   ├── logout.php             # session_destroy() + redirect
│   ├── contact_process.php    # Contact form — INSERT to contact_messages
│   ├── add_to_cart.php        # AJAX — add item to $_SESSION['cart']
│   └── update_cart.php        # AJAX — increase / decrease / remove cart item
│
├── assets/ (or root)
│   ├── style.css              # Full CSS design system
│   └── script.js              # All JS/jQuery features (6 total)
│
├── Bikehub Image/             # Product images organised by category
│   ├── Bikes/
│   ├── Gears/
│   ├── Helmet/
│   ├── Accesories/
│   └── Clothing/
│
├── icons/                     # Category icon images (navbar + home grid)
├── uploads/                   # User-uploaded profile pictures (gitignored)
└── bikehub_db.sql             # Full DB schema + seed data
```

---

## ⚙️ Setup — XAMPP (Windows)

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/bikehub.git
```
Place the folder inside:
```
C:\xampp\htdocs\Bikehub1.1\
```

### 2. Start XAMPP
Open **XAMPP Control Panel** and start both:
- ✅ Apache
- ✅ MySQL

### 3. Create the database
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **SQL** tab
3. Paste the contents of `bikehub_db.sql` and click **Go**

### 4. Create the uploads folder
```
C:\xampp\htdocs\Bikehub1.1\uploads\
```
Make sure it is writable (XAMPP handles this by default on Windows).

### 5. Open the site
```
http://localhost/Bikehub1.1/index.php
```

---

## 🗄 Database — `bikehub_db`

| Table | Description |
|---|---|
| `users` | Registered accounts — fname, lname, email, bcrypt password, profile_img |
| `bikes` | Road bikes catalogue — name, brand, price, sale_price, image, stock |
| `products` | Gears / helmets / accessories / clothing — name, category, price, sale_price |
| `contact_messages` | Enquiries submitted via contact form |
| `orders` | Checkout orders — user_id, total, status, created_at |
| `order_items` | Individual items per order — order_id, product name, qty, price |

---

## ✨ Features

### Frontend
- Dark-themed UI with CSS custom properties (`#080C12`, `#1A6BFF`, `#DDE2EC`)
- Responsive layout — CSS Grid (3-col products, 4-col footer) + Flexbox navbar
- Auto-advancing hero image carousel with dot/arrow/swipe navigation

### JavaScript / jQuery (6 features)
1. **Hero carousel** — `setInterval` auto-advance, touch swipe, dot + arrow controls
2. **Navbar scroll** — jQuery toggles `.scrolled` class, vanilla JS adds drop shadow
3. **Product card hover** — GPU `willChange` hint + jQuery blue glow shadow
4. **Live product search** — real-time filter as user types, no page reload
5. **Form validation** — inline red errors on submit, `e.preventDefault()`, jQuery focus
6. **File upload preview** — `FileReader` API shows circular photo preview before submit

### Backend / PHP
- Secure login with `password_verify()` against bcrypt hash
- Registration with server-side validation, duplicate email check, file MIME validation
- `$_SESSION` cart — stored server-side, rebuilt with fresh DB prices on every cart load
- AJAX add-to-cart and qty update — returns JSON, badge updates without reload
- Checkout inserts into `orders` and `order_items` tables, then clears the session cart
- Flash messages via `$_SESSION` survive the POST-redirect-GET cycle
- All DB queries use `mysqli_prepare()` + `bind_param()` — no SQL injection possible

### Forms (5 total)
| Form | Handler | Action |
|---|---|---|
| Login | `php/login.php` | SELECT + session |
| Register | `php/register_process.php` | INSERT + file upload |
| Contact | `php/contact_process.php` | INSERT to contact_messages |
| Add to cart | `php/add_to_cart.php` | Session INSERT (AJAX) |
| Cart qty update | `php/update_cart.php` | Session UPDATE (AJAX) |

---

## 👥 Team

| Member | Role | Responsibilities |
|---|---|---|
| Member 1 | Frontend Lead | index.php, products.php, about.php, style.css, script.js |
| Member 2 | Backend Lead | Auth, cart, checkout, contact handlers, DB design |

---

## 📋 Assignment Coverage

| Requirement | Implementation | Marks |
|---|---|---|
| HTML5 + CSS3 | 7 pages, semantic markup, Grid/Flex layout | /15 |
| JS / jQuery × 5 | Carousel, search, hover, validation, file preview, scroll | /5 |
| Forms × 5 | Login, register, contact, add-to-cart, qty update | /5 |
| PHP | Auth, session, isset(), INSERT/UPDATE/SELECT, DB connection | /10 |
| MySQL (3+ tables) | users, bikes, products, contact_messages, orders, order_items | /5 |
| File Upload | Profile photo on register — MIME check + move_uploaded_file() | /5 |
| **Total** | | **/50** |

---

## 🔒 Security Notes

- Passwords hashed with `PASSWORD_BCRYPT` via `password_hash()`
- All DB queries use prepared statements — SQL injection prevented
- File uploads validated by real MIME type (`finfo`), not just extension
- Session variables checked with `isset()` before every use
- Cart page protected — redirects to login if `$_SESSION['user_id']` not set

---
