# 💱 CurrencyX — Real-Time Currency Converter

A complete, production-ready currency converter web application built with **PHP 8+, MySQL, HTML5, CSS3, and Vanilla JavaScript**. No frameworks. Real exchange-rate API data.

---

## ✨ Features

| Feature | Details |
|---|---|
| **Live Conversion** | AJAX-powered, no page reload. 170+ currencies. |
| **Searchable Picker** | Filter currencies by code, name, or country (EN & ZH) |
| **Historical Charts** | Canvas line chart · 7D / 30D / 90D / 1Y / 5Y / MAX |
| **A-Z Rate Directory** | Browse all rates with letter filter & search |
| **Bilingual** | English & Chinese (Simplified). Saved in localStorage |
| **Smart Caching** | DB cache · 1-hour TTL · stale fallback |
| **API Fallback** | open.er-api.com → frankfurter.app → stale cache |
| **Security** | PDO prepared statements · XSS escaping · rate limiting |
| **Performance** | MySQL indexes · debounced resize · browser no-cache |
| **Responsive** | Mobile-first, works on all screen sizes |

---

## 📂 Project Structure

```
currency_converter/
├── api/
│   ├── convert.php       → POST  Conversion endpoint
│   ├── currencies.php    → GET   Currency list + search
│   ├── rates.php         → GET   All rates for a base currency
│   └── history.php       → GET   Historical rates for chart
│
├── assets/
│   ├── css/
│   │   └── style.css     → Dark theme, glassmorphism, animations
│   └── js/
│       ├── app.js        → Converter, Picker, Chart, Rates page
│       └── i18n.js       → EN/ZH translation system
│
├── includes/
│   ├── config.php        → DB credentials, API URLs, settings
│   ├── db.php            → PDO singleton
│   ├── helpers.php       → Utilities: JSON, validation, rate limiting
│   ├── layout.php        → Shared header/footer
│   ├── rates.php         → Rate fetching, caching, history
│   └── schema.sql        → Database schema + seed data
│
├── index.php             → Currency converter page
├── rates.php             → A-Z currency directory
├── manual.php            → User manual
└── README.md             → This file
```

---

## 🛠 Installation (XAMPP / Local)

### 1. Clone / Download

Place the project in your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\currency_converter\
```

### 2. Create the Database

Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or use the MySQL CLI:

```sql
-- In phpMyAdmin: click "Import" and upload includes/schema.sql
-- Or in MySQL CLI:
mysql -u root -p < includes/schema.sql
```

This creates the `currency_converter` database, all 4 tables, and seeds 150+ currencies.

### 3. Configure Database Credentials

Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'currency_converter');
define('DB_USER', 'root');
define('DB_PASS', '');          // ← Change for production
```

### 4. Start XAMPP

Ensure **Apache** and **MySQL** are running in the XAMPP Control Panel.

### 5. Open in Browser

```
http://localhost/currency_converter/
```

---

## 🌐 API Endpoints

### `POST /api/convert.php`

Convert an amount between two currencies.

**Request body (JSON):**
```json
{ "from": "USD", "to": "MYR", "amount": 100 }
```

**Response:**
```json
{
  "error": false,
  "from": "USD",
  "to": "MYR",
  "amount": 100,
  "result": 407.62,
  "rate": 4.0762,
  "last_updated": "2026-09-15 00:02:31",
  "source": "open.er-api.com",
  "stale": false
}
```

### `GET /api/currencies.php?q=ringgit`

Search currencies by code, name, or country.

### `GET /api/rates.php?base=USD`

Get all exchange rates relative to a base currency.

### `GET /api/history.php?from=USD&to=MYR&period=30d`

Get historical rates. Periods: `7d`, `30d`, `90d`, `1y`, `5y`, `max`

---

## 🌍 Data Sources

| Source | Currencies | Historical | Usage |
|---|---|---|---|
| [open.er-api.com](https://open.er-api.com) | 170+ | No | Primary live rates |
| [frankfurter.app](https://api.frankfurter.app) | 33 | Yes (1999–) | Fallback + history |

---

## 🔒 Security

- All database queries use **PDO prepared statements**
- User input is validated (regex for codes, `is_numeric` for amounts)
- All HTML output is escaped with `htmlspecialchars()`
- API rate limiting: **60 requests / 60 seconds per IP**
- PHP errors are logged but **never shown to users**
- Security headers: `X-Content-Type-Options`, `X-Frame-Options`

---

## 🚀 iFastNet Shared Hosting Deployment

1. **Upload** all files via FTP/cPanel File Manager to your `public_html` or a subdirectory.
2. **Create a MySQL database** via cPanel → MySQL Databases.
3. **Import** `includes/schema.sql` via phpMyAdmin.
4. **Edit** `includes/config.php` with your cPanel DB credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'youruser_currency_converter');
   define('DB_USER', 'youruser_dbuser');
   define('DB_PASS', 'your_password_here');
   ```
5. **Update URL paths** in `includes/layout.php` — replace `/currency_converter/` with your path (e.g. `/` if deployed to root, or `/myapp/` for a subdirectory).
6. **Verify PHP 8+** is enabled in cPanel → Select PHP Version.
7. **Enable PHP extensions**: `pdo`, `pdo_mysql`, `curl` or `openssl` (for file_get_contents HTTPS).

### `.htaccess` (optional, for clean URLs)

Create `/currency_converter/.htaccess`:

```apache
Options -Indexes
ServerSignature Off

# Security headers
<IfModule mod_headers.c>
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "DENY"
  Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# PHP errors off
php_flag display_errors Off
php_flag log_errors On

# Cache static assets 1 month
<FilesMatch "\.(css|js|woff2?)$">
  Header set Cache-Control "max-age=2592000, public"
</FilesMatch>
```

---

## 🐛 Troubleshooting

| Problem | Solution |
|---|---|
| **Blank page** | Enable PHP error log. Check `includes/config.php` DB credentials. |
| **"Service temporarily unavailable"** | Can't connect to MySQL. Check DB host/name/user/pass. |
| **No rates appear** | The API may be slow. Wait 10s and refresh. Check server has internet access (file_get_contents). |
| **Chart shows error** | The currency pair may not be supported by Frankfurter. Try USD base or a major pair. |
| **Rate limiting 429** | You're making > 60 API requests/min. Wait 60s. |
| **Chinese characters garbled** | Ensure MySQL uses `utf8mb4`. Re-import `schema.sql`. |

---

## ⚡ Performance Tips

- **PHP OPcache**: Enable in `php.ini` for 2–5× PHP speed boost.
- **MySQL indexes**: Already created on `(base_currency, target_currency)` and `fetched_at`.
- **API caching**: 1-hour DB cache means only 1 external request per hour per base currency.
- **Browser caching**: Static assets (CSS/JS) should be cached by your web server.

---

## 📄 License

MIT — Free to use and modify.
