<?php
/**
 * Shared HTML Layout
 * Currency Converter — includes/layout.php
 */

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

/**
 * Render <head> + opening nav.
 * @param string $title    Page <title>
 * @param string $desc     Meta description
 * @param string $page     Active nav link: 'index' | 'rates' | 'manual'
 */
function pageHeader(string $title, string $desc, string $page = 'index'): void
{
    $appName = APP_NAME;
    $fullTitle = $title . ' — ' . $appName;
    $base = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($desc) ?>">
  <meta name="theme-color" content="#0d1117">
  <title><?= e($fullTitle) ?></title>
  <!-- Preconnect for Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
  <!-- Favicon inline SVG -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💱</text></svg>">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="<?= $base ?>/" class="logo" id="logo-link">
      <span class="logo-icon">💱</span>
      <span class="logo-text"><?= e($appName) ?></span>
    </a>

    <nav class="main-nav" role="navigation" aria-label="Main navigation">
      <a href="<?= $base ?>/"
         class="nav-link <?= $page === 'index'  ? 'active' : '' ?>"
         id="nav-converter"
         data-i18n="nav_converter">Converter</a>
      <a href="<?= $base ?>/rates.php"
         class="nav-link <?= $page === 'rates'  ? 'active' : '' ?>"
         id="nav-rates"
         data-i18n="nav_rates">Rates</a>
      <a href="<?= $base ?>/manual.php"
         class="nav-link <?= $page === 'manual' ? 'active' : '' ?>"
         id="nav-manual"
         data-i18n="nav_manual">Manual</a>
    </nav>

    <div class="header-actions">
      <button class="lang-toggle" id="lang-toggle" aria-label="Switch language" title="Switch language">
        <span class="lang-en">EN</span>
        <span class="lang-sep">|</span>
        <span class="lang-zh">中文</span>
      </button>
      <button class="menu-toggle" id="menu-toggle" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main class="site-main">
<?php
}

/**
 * Render closing tags, footer, and script tags.
 * @param string[] $extraScripts  Additional JS files to load
 */
function pageFooter(array $extraScripts = []): void
{
    $year = date('Y');
    $appName = APP_NAME;
    $base = getBaseUrl();
?>
</main>

<footer class="site-footer">
  <div class="container">
    <p class="footer-copy">
      &copy; <?= $year ?> <?= e($appName) ?> &mdash;
      <span data-i18n="footer_tagline">Real-time exchange rates powered by open.er-api.com &amp; frankfurter.app</span>
    </p>
    <p class="footer-disclaimer" data-i18n="footer_disclaimer">
      Rates are for informational purposes only. Not financial advice.
    </p>
  </div>
</footer>

<script>window.CX_BASE = '<?= $base ?>';</script>
<script src="<?= $base ?>/assets/js/i18n.js"></script>
<script src="<?= $base ?>/assets/js/app.js"></script>
<?php foreach ($extraScripts as $src): ?>
<script src="<?= e($src) ?>"></script>
<?php endforeach; ?>
</body>
</html>
<?php
}
