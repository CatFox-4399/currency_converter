<?php
/**
 * Currency Converter — Home Page
 * index.php
 */
define('APP_ROOT', __DIR__);
require_once 'includes/config.php';
require_once 'includes/layout.php';

pageHeader(
    'Currency Converter',
    'Convert currencies instantly with live exchange rates. Supports 170+ currencies with real-time data.',
    'index'
);
?>

<!-- Hero -->
<section class="page-hero">
  <div class="page-hero__badge">
    <span class="dot"></span>
    <span data-i18n="hero_badge">🌐 Live Exchange Rates</span>
  </div>
  <h1 data-i18n="hero_title">Currency Converter</h1>
  <p data-i18n="hero_subtitle">Real-time rates for 170+ currencies, powered by live APIs.</p>
</section>

<!-- Stats Bar -->
<div class="stats-bar">
  <div class="stat-card">
    <div class="stat-value" id="stat-currencies">170+</div>
    <div class="stat-label" data-i18n="stat_currencies">Currencies</div>
  </div>
  <div class="stat-card">
    <div class="stat-value">2</div>
    <div class="stat-label" data-i18n="stat_sources">Data Sources</div>
  </div>
  <div class="stat-card">
    <div class="stat-value" data-i18n="stat_cache_val">1 Hour</div>
    <div class="stat-label" data-i18n="stat_cache">Cache TTL</div>
  </div>
</div>

<!-- Converter -->
<section class="converter-wrap">
  <div class="converter-grid">

    <!-- Main Converter Card -->
    <div class="card converter-card card--accent" id="converter-card">
      <div class="converter-row">
        <!-- Amount -->
        <div class="field-group">
          <label class="field-label" for="amount-input" data-i18n="label_amount">Amount</label>
          <input
            type="number"
            id="amount-input"
            class="amount-input"
            value="100"
            min="0.000001"
            max="1000000000"
            step="any"
            placeholder="100"
            autocomplete="off"
            aria-label="Amount to convert"
          >
        </div>

        <!-- Swap Button -->
        <div class="field-group" style="align-items:center;padding-bottom:2px;">
          <button class="swap-btn" id="swap-btn" title="Swap currencies" aria-label="Swap currencies">⇄</button>
        </div>

        <!-- From Picker -->
        <div class="field-group">
          <label class="field-label" data-i18n="label_from">From Currency</label>
          <div class="currency-picker" id="from-picker"></div>
        </div>
      </div>

      <!-- To Picker (below on mobile, side by side on desktop shown via grid) -->
      <div class="field-group" style="margin-bottom:var(--space-lg);">
        <label class="field-label" data-i18n="label_to">To Currency</label>
        <div class="currency-picker" id="to-picker"></div>
      </div>

      <button class="convert-btn" id="convert-btn" type="button">
        <span class="btn-spinner" style="display:none"></span>
        <span class="btn-label" data-i18n="btn_convert">Convert</span>
      </button>

      <!-- Error Banner -->
      <div class="error-banner" id="error-banner" role="alert">
        <span>⚠️</span>
        <span class="error-msg"></span>
      </div>
    </div>

    <!-- Result Card -->
    <div class="card result-card" id="result-card">
      <div class="result-main">
        <div class="result-amount-row">
          <span class="result-from" id="result-from">100 USD</span>
          <span class="result-equals">=</span>
          <span class="result-to" id="result-to">391.20 MYR</span>
        </div>
        <div class="result-rate" id="result-rate">
          <strong>1 USD = 3.9120 MYR</strong>
        </div>
      </div>
      <div class="result-meta">
        <span class="result-badge result-badge--live" id="result-badge">● Live</span>
        <span class="result-updated" id="result-updated"></span>
      </div>
    </div>

  </div><!-- .converter-grid -->
</section>

<!-- Historical Chart -->
<section class="chart-section">
  <div class="card">
    <div class="chart-header">
      <div>
        <div class="chart-title" id="chart-title">USD / MYR Exchange Rate</div>
        <div class="chart-subtitle" data-i18n="chart_loading">Loading chart data…</div>
      </div>
      <div class="period-tabs" role="group" aria-label="Chart period">
        <button class="period-btn" data-period="7d"  data-i18n="period_7d">7D</button>
        <button class="period-btn active" data-period="30d" data-i18n="period_30d">30D</button>
        <button class="period-btn" data-period="90d" data-i18n="period_90d">90D</button>
        <button class="period-btn" data-period="1y"  data-i18n="period_1y">1Y</button>
        <button class="period-btn" data-period="5y"  data-i18n="period_5y">5Y</button>
        <button class="period-btn" data-period="max" data-i18n="period_max">MAX</button>
      </div>
    </div>

    <div class="chart-canvas-wrap" id="chart-wrap">
      <canvas id="history-canvas" aria-label="Exchange rate history chart"></canvas>

      <div class="chart-loading" id="chart-loading">
        <div class="chart-spinner"></div>
        <span data-i18n="chart_loading">Loading chart data…</span>
      </div>

      <div class="chart-error" id="chart-error">
        <span>📉</span>
        <span class="chart-error-msg" data-i18n="chart_error">Failed to load chart data.</span>
      </div>

      <!-- Tooltip -->
      <div class="chart-tooltip" id="chart-tooltip" aria-hidden="true">
        <div class="chart-tooltip__date"></div>
        <div class="chart-tooltip__rate"></div>
      </div>
    </div>
  </div>
</section>

<?php pageFooter(); ?>
