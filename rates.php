<?php
/**
 * A-Z Currency Rates Directory
 * rates.php
 */
define('APP_ROOT', __DIR__);
require_once 'includes/config.php';
require_once 'includes/layout.php';

pageHeader(
    'Live Exchange Rates',
    'Browse live exchange rates for all currencies from A to Z. Updated every hour.',
    'rates'
);

// Build A-Z letters array
$letters = range('A', 'Z');
?>

<!-- Hero -->
<section class="page-hero">
  <div class="page-hero__badge">
    <span class="dot"></span>
    <span data-i18n="rates_badge">🔤 A–Z Currency Directory</span>
  </div>
  <h1 data-i18n="rates_title">Live Exchange Rates</h1>
  <p data-i18n="rates_subtitle">Browse rates for all currencies, sorted A–Z.</p>
</section>

<!-- Toolbar -->
<div class="rates-wrap">
  <div class="rates-toolbar">
    <!-- Search -->
    <div class="rates-search-wrap">
      <span class="rates-search-icon">🔍</span>
      <input
        type="search"
        id="rates-search"
        class="rates-search"
        data-i18n-ph="rates_search_ph"
        placeholder="Search by code, name, or country…"
        autocomplete="off"
        aria-label="Search currencies"
      >
    </div>

    <!-- Base currency selector -->
    <div class="base-selector">
      <label for="base-select" class="field-label" data-i18n="rates_base_label">Base:</label>
      <select id="base-select" class="base-select" aria-label="Base currency">
        <?php
        $popularBases = ['USD','EUR','GBP','JPY','CNY','MYR','SGD','AUD','CAD','CHF','HKD','INR'];
        foreach ($popularBases as $b) {
            $sel = ($b === 'USD') ? 'selected' : '';
            echo "<option value=\"{$b}\" {$sel}>{$b}</option>\n";
        }
        ?>
      </select>
    </div>
  </div>

  <!-- A-Z Filter -->
  <div class="az-filter" role="group" aria-label="Filter by first letter">
    <button class="az-btn all active" data-letter="all" data-i18n="rates_all">ALL</button>
    <?php foreach ($letters as $letter): ?>
    <button class="az-btn" data-letter="<?= $letter ?>"><?= $letter ?></button>
    <?php endforeach; ?>
  </div>

  <!-- Loading indicator -->
  <div class="rates-loading" id="rates-loading">
    <div class="chart-spinner"></div>
    <span data-i18n="rates_loading">Loading rates…</span>
  </div>

  <!-- Rates Table -->
  <div class="rates-table-wrap">
    <table class="rates-table" id="rates-table" aria-label="Currency exchange rates">
      <thead>
        <tr>
          <th data-i18n="rates_col_code">Code</th>
          <th data-i18n="rates_col_currency">Currency</th>
          <th data-i18n="rates_col_country">Country</th>
          <th data-i18n="rates_col_symbol">Symbol</th>
          <th data-i18n="rates_col_rate">Rate</th>
          <th data-i18n="rates_col_updated">Updated</th>
        </tr>
      </thead>
      <tbody id="rates-tbody">
        <!-- Populated by JavaScript -->
      </tbody>
    </table>

    <!-- Empty state (shown by JS when no results) -->
    <div id="rates-empty" style="display:none;" class="table-empty">
      <div class="table-empty__icon">🔍</div>
      <div class="table-empty__text" data-i18n="rates_no_results">No currencies found.</div>
    </div>
  </div><!-- .rates-table-wrap -->

</div><!-- .rates-wrap -->

<?php pageFooter(); ?>
