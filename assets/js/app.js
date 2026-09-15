/**
 * CurrencyX — Main Application JS
 * assets/js/app.js
 *
 * Modules:
 *  1. Nav (mobile menu)
 *  2. CurrencyPicker (custom searchable dropdown)
 *  3. Converter (AJAX conversion)
 *  4. LineChart (Canvas line chart)
 *  5. ChartController (period buttons + data loading)
 *  6. RatesPage (A-Z directory)
 */

'use strict';

// Dynamic base URL (set by PHP in layout.php with fallback to current URL path)
const BASE = (typeof window.CX_BASE === 'string' && window.CX_BASE !== '')
  ? window.CX_BASE
  : (window.location.pathname.startsWith('/currency_converter') ? '/currency_converter' : '');

// ══════════════════════════════════════════════════════════════
// 1. Navigation — Mobile menu toggle
// ══════════════════════════════════════════════════════════════
function initNav() {
  const toggle = document.getElementById('menu-toggle');
  const nav    = document.querySelector('.main-nav');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', isOpen);
  });

  // Close on nav link click (mobile)
  nav.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });

  // Close on outside click
  document.addEventListener('click', e => {
    if (!nav.contains(e.target) && !toggle.contains(e.target)) {
      nav.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
}

// ══════════════════════════════════════════════════════════════
// 2. CurrencyPicker — Custom searchable dropdown
// ══════════════════════════════════════════════════════════════
class CurrencyPicker {
  /**
   * @param {HTMLElement} container  .currency-picker wrapper
   * @param {string}      id         unique id prefix
   * @param {Function}    onChange   called with selected currency object
   */
  constructor(container, id, onChange) {
    this.container   = container;
    this.id          = id;
    this.onChange    = onChange;
    this.currencies  = [];
    this.selected    = null;
    this.highlighted = -1;
    this.isOpen      = false;

    this._build();
    this._bindEvents();
  }

  _build() {
    this.container.innerHTML = `
      <button class="picker-trigger" id="${this.id}-trigger"
              aria-haspopup="listbox" aria-expanded="false"
              type="button">
        <span class="picker-code" id="${this.id}-code">—</span>
        <span class="picker-name" id="${this.id}-name">Select currency</span>
        <span class="picker-arrow" aria-hidden="true">▼</span>
      </button>
      <div class="picker-dropdown" id="${this.id}-dropdown" role="listbox">
        <div class="picker-search-wrap">
          <input class="picker-search" id="${this.id}-search"
                 type="text" autocomplete="off" spellcheck="false"
                 placeholder="${t('search_currency')}" aria-label="Search currency">
        </div>
        <div class="picker-list" id="${this.id}-list" role="listbox"></div>
      </div>
    `;

    this.trigger  = this.container.querySelector('.picker-trigger');
    this.dropdown = this.container.querySelector('.picker-dropdown');
    this.search   = this.container.querySelector('.picker-search');
    this.list     = this.container.querySelector('.picker-list');
    this.codeEl   = this.container.querySelector('.picker-code');
    this.nameEl   = this.container.querySelector('.picker-name');
  }

  _bindEvents() {
    this.trigger.addEventListener('click', () => this.toggle());

    this.search.addEventListener('input', () => {
      this.highlighted = -1;
      this._renderList(this.search.value);
    });

    this.search.addEventListener('keydown', e => this._onSearchKey(e));

    // Close on outside click
    document.addEventListener('click', e => {
      if (!this.container.contains(e.target)) this.close();
    });

    // Re-apply placeholders on lang change
    document.addEventListener('langchange', () => {
      this.search.placeholder = t('search_currency');
      if (this.nameEl.dataset.name) {
        this._updateTriggerText();
      }
    });
  }

  _onSearchKey(e) {
    const items = [...this.list.querySelectorAll('.picker-item:not(.hidden-item)')];
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      this.highlighted = Math.min(this.highlighted + 1, items.length - 1);
      this._highlightItem(items);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      this.highlighted = Math.max(this.highlighted - 1, 0);
      this._highlightItem(items);
    } else if (e.key === 'Enter') {
      if (items[this.highlighted]) items[this.highlighted].click();
    } else if (e.key === 'Escape') {
      this.close();
    }
  }

  _highlightItem(items) {
    items.forEach((el, i) => el.classList.toggle('highlighted', i === this.highlighted));
    if (items[this.highlighted]) {
      items[this.highlighted].scrollIntoView({ block: 'nearest' });
    }
  }

  setCurrencies(currencies) {
    this.currencies = currencies;
    this._renderList('');
  }

  setSelected(code) {
    const found = this.currencies.find(c => c.code === code);
    if (found) this._select(found, false);
  }

  _select(currency, notify = true) {
    this.selected = currency;
    this._updateTriggerText();
    this.close();
    if (notify && this.onChange) this.onChange(currency);
  }

  _updateTriggerText() {
    const c = this.selected;
    if (!c) return;
    this.codeEl.textContent = c.code;
    const name = currentLang === 'zh' ? (c.currency_zh || c.currency_en) : c.currency_en;
    this.nameEl.textContent = name;
    this.nameEl.dataset.name = '1';
  }

  _renderList(query) {
    const q   = query.trim().toLowerCase();
    const matches = q
      ? this.currencies.filter(c =>
          c.code.toLowerCase().includes(q) ||
          c.currency_en.toLowerCase().includes(q) ||
          c.currency_zh.toLowerCase().includes(q) ||
          c.country_en.toLowerCase().includes(q) ||
          c.country_zh.toLowerCase().includes(q)
        )
      : this.currencies;

    if (matches.length === 0) {
      this.list.innerHTML = `<div class="picker-empty">${t('rates_no_results')}</div>`;
      return;
    }

    const frag = document.createDocumentFragment();
    matches.forEach(c => {
      const name = currentLang === 'zh' ? (c.currency_zh || c.currency_en) : c.currency_en;
      const country = currentLang === 'zh' ? (c.country_zh || c.country_en) : c.country_en;
      const el = document.createElement('div');
      el.className = 'picker-item' + (this.selected?.code === c.code ? ' selected' : '');
      el.setAttribute('role', 'option');
      el.setAttribute('data-code', c.code);
      el.innerHTML = `
        <span class="picker-item__code">${escHtml(c.code)}</span>
        <span class="picker-item__name">${escHtml(name)}</span>
        <span class="picker-item__country">${escHtml(country)}</span>
      `;
      el.addEventListener('click', () => this._select(c));
      frag.appendChild(el);
    });

    this.list.innerHTML = '';
    this.list.appendChild(frag);
  }

  toggle() {
    this.isOpen ? this.close() : this.open();
  }

  open() {
    this.isOpen = true;
    this.trigger.classList.add('open');
    this.trigger.setAttribute('aria-expanded', 'true');
    this.dropdown.classList.add('open');
    this._positionDropdown();
    this.search.value = '';
    this._renderList('');
    setTimeout(() => this.search.focus(), 50);

    // Highlight selected
    const sel = this.list.querySelector('.selected');
    if (sel) sel.scrollIntoView({ block: 'nearest' });

    // Reposition on scroll/resize
    this._scrollHandler = () => this._positionDropdown();
    window.addEventListener('scroll', this._scrollHandler, true);
    window.addEventListener('resize', this._scrollHandler);
  }

  _positionDropdown() {
    const rect = this.trigger.getBoundingClientRect();
    const dd   = this.dropdown;
    const ddH  = 320; // max-height
    const vH   = window.innerHeight;
    const vW   = window.innerWidth;

    // Width matches trigger
    dd.style.width = rect.width + 'px';
    dd.style.left  = Math.min(rect.left, vW - rect.width - 8) + 'px';

    // Open downward normally, flip up if near bottom
    if (rect.bottom + ddH > vH && rect.top > ddH) {
      dd.style.top    = 'auto';
      dd.style.bottom = (vH - rect.top + 4) + 'px';
    } else {
      dd.style.bottom = 'auto';
      dd.style.top    = (rect.bottom + 4) + 'px';
    }
  }

  close() {
    this.isOpen = false;
    this.trigger.classList.remove('open');
    this.trigger.setAttribute('aria-expanded', 'false');
    this.dropdown.classList.remove('open');
    this.highlighted = -1;

    // Unbind scroll/resize listeners
    if (this._scrollHandler) {
      window.removeEventListener('scroll', this._scrollHandler, true);
      window.removeEventListener('resize', this._scrollHandler);
      this._scrollHandler = null;
    }
  }

  getValue() { return this.selected?.code ?? null; }
}

// ══════════════════════════════════════════════════════════════
// 3. Converter
// ══════════════════════════════════════════════════════════════
class Converter {
  constructor() {
    this.fromPicker = null;
    this.toPicker   = null;
    this.currencies = [];

    this.amountInput  = document.getElementById('amount-input');
    this.convertBtn   = document.getElementById('convert-btn');
    this.swapBtn      = document.getElementById('swap-btn');
    this.resultCard   = document.getElementById('result-card');
    this.errorBanner  = document.getElementById('error-banner');

    if (!this.convertBtn) return; // Not on converter page

    this._initPickers();
    this._bindEvents();
    this._loadCurrencies();
  }

  _initPickers() {
    const fromWrap = document.getElementById('from-picker');
    const toWrap   = document.getElementById('to-picker');

    this.fromPicker = new CurrencyPicker(fromWrap, 'from', () => this._onCurrencyChange());
    this.toPicker   = new CurrencyPicker(toWrap,   'to',   () => this._onCurrencyChange());
  }

  _bindEvents() {
    this.convertBtn.addEventListener('click', () => this.convert());
    this.swapBtn.addEventListener('click',    () => this.swap());

    this.amountInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') this.convert();
    });

    // Auto-convert on currency change after first conversion
    document.addEventListener('langchange', () => {
      this.fromPicker._renderList('');
      this.toPicker._renderList('');
      this.fromPicker._updateTriggerText();
      this.toPicker._updateTriggerText();
    });
  }

  _onCurrencyChange() {
    // Auto-convert if a result is already showing
    if (this.resultCard.classList.contains('visible')) {
      this.convert();
    }
    // Update chart if controller exists
    if (window.chartController) {
      window.chartController.updatePair(
        this.fromPicker.getValue(),
        this.toPicker.getValue()
      );
    }
  }

  async _loadCurrencies() {
    try {
      const res  = await fetch(BASE + '/api/currencies.php');
      const data = await res.json();
      if (data.error || !data.currencies) throw new Error(data.message);
      this.currencies = data.currencies;
      this.fromPicker.setCurrencies(this.currencies);
      this.toPicker.setCurrencies(this.currencies);
      // Set defaults
      const savedFrom = localStorage.getItem('cx-from') || 'USD';
      const savedTo   = localStorage.getItem('cx-to')   || 'MYR';
      this.fromPicker.setSelected(savedFrom);
      this.toPicker.setSelected(savedTo);
    } catch (err) {
      console.error('Failed to load currencies:', err);
      this._showError(t('err_api_unavailable'));
    }
  }

  async convert() {
    const from   = this.fromPicker.getValue();
    const to     = this.toPicker.getValue();
    const amount = parseFloat(this.amountInput.value);

    if (!from || !to) { this._showError(t('err_invalid_code')); return; }
    if (!isFinite(amount) || amount <= 0) { this._showError(t('err_invalid_amount')); return; }

    this._setLoading(true);
    this._hideError();

    try {
      const res  = await fetch(BASE + '/api/convert.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ from, to, amount }),
      });
      const data = await res.json();

      if (data.error) throw new Error(data.message);
      this._showResult(data);

      // Save preferences
      localStorage.setItem('cx-from', from);
      localStorage.setItem('cx-to',   to);
    } catch (err) {
      this._showError(err.message || t('err_api_unavailable'));
    } finally {
      this._setLoading(false);
    }
  }

  swap() {
    const fromCode = this.fromPicker.getValue();
    const toCode   = this.toPicker.getValue();
    if (!fromCode || !toCode) return;

    // Animate
    this.swapBtn.classList.add('spinning');
    setTimeout(() => this.swapBtn.classList.remove('spinning'), 400);

    this.fromPicker.setSelected(toCode);
    this.toPicker.setSelected(fromCode);

    // Re-convert if result is showing
    if (this.resultCard.classList.contains('visible')) {
      setTimeout(() => this.convert(), 100);
    }
  }

  _showResult(data) {
    const resultFrom = document.getElementById('result-from');
    const resultTo   = document.getElementById('result-to');
    const resultRate = document.getElementById('result-rate');
    const resultBadge  = document.getElementById('result-badge');
    const resultUpdated = document.getElementById('result-updated');

    if (resultFrom) {
      resultFrom.textContent = `${formatNumber(data.amount)} ${data.from}`;
    }
    if (resultTo) {
      resultTo.textContent = `${formatNumber(data.result, 4)} ${data.to}`;
    }
    if (resultRate) {
      resultRate.innerHTML = `<strong>1 ${escHtml(data.from)} = ${formatNumber(data.rate, 6)} ${escHtml(data.to)}</strong>`;
    }
    if (resultBadge) {
      if (data.stale) {
        resultBadge.className  = 'result-badge result-badge--stale';
        resultBadge.textContent = t('badge_stale');
      } else if (data.cached) {
        resultBadge.className  = 'result-badge result-badge--cached';
        resultBadge.textContent = t('badge_cached');
      } else {
        resultBadge.className  = 'result-badge result-badge--live';
        resultBadge.textContent = t('badge_live');
      }
    }
    if (resultUpdated && data.last_updated) {
      const d = new Date(data.last_updated.replace(' ', 'T') + 'Z');
      resultUpdated.textContent = t('result_updated', {
        time: isNaN(d) ? data.last_updated : d.toLocaleString(),
      });
    }

    this.resultCard.classList.remove('visible');
    void this.resultCard.offsetWidth; // Reflow for re-animation
    this.resultCard.classList.add('visible');
  }

  _setLoading(on) {
    this.convertBtn.disabled = on;
    const label = this.convertBtn.querySelector('.btn-label');
    const spinner = this.convertBtn.querySelector('.btn-spinner');
    if (label)  label.textContent  = on ? t('btn_converting') : t('btn_convert');
    if (spinner) spinner.style.display = on ? 'inline-block' : 'none';
  }

  _showError(msg) {
    this.resultCard.classList.remove('visible');
    if (this.errorBanner) {
      this.errorBanner.querySelector('.error-msg').textContent = msg;
      this.errorBanner.classList.add('visible');
    }
  }

  _hideError() {
    if (this.errorBanner) this.errorBanner.classList.remove('visible');
  }
}

// ══════════════════════════════════════════════════════════════
// 4. LineChart — Canvas-based line chart
// ══════════════════════════════════════════════════════════════
class LineChart {
  constructor(canvas, tooltipEl) {
    this.canvas  = canvas;
    this.ctx     = canvas.getContext('2d');
    this.tooltip = tooltipEl;
    this.data    = [];        // [{date, rate}]
    this.padding = { top: 24, right: 20, bottom: 48, left: 68 };
    this._mouseX = null;
    this._hoverIdx = -1;

    this._bindEvents();
    this._resize();
  }

  // ── Data ──────────────────────────────────────────────────────
  setData(data) {
    this.data = data;
    this._hoverIdx = -1;
    if (this.tooltip) this.tooltip.classList.remove('visible');
    this._resize();
  }

  // ── Render ────────────────────────────────────────────────────
  render() {
    const { canvas, ctx, data, padding: p } = this;
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    const W = rect.width || (canvas.parentElement ? canvas.parentElement.clientWidth : 800);
    const H = rect.height || (canvas.parentElement ? canvas.parentElement.clientHeight : 260);

    const targetW = Math.round(W * dpr);
    const targetH = Math.round(H * dpr);
    if (canvas.width !== targetW || canvas.height !== targetH) {
      canvas.width = targetW;
      canvas.height = targetH;
      canvas.style.width = W + 'px';
      canvas.style.height = H + 'px';
      ctx.setTransform(1, 0, 0, 1, 0, 0);
      ctx.scale(dpr, dpr);
    }

    ctx.clearRect(0, 0, W, H);
    if (!data || data.length === 0) return;

    const plotW = W - p.left - p.right;
    const plotH = H - p.top  - p.bottom;

    const rates  = data.map(d => d.rate);
    const minR   = Math.min(...rates);
    const maxR   = Math.max(...rates);
    const rangeR = maxR - minR || 1;
    const paddedMin = minR - rangeR * 0.08;
    const paddedMax = maxR + rangeR * 0.08;
    const paddedRange = paddedMax - paddedMin;

    // Helper: data → canvas coords
    const xOf = i => p.left + (i / Math.max(1, data.length - 1)) * plotW;
    const yOf = r => p.top  + plotH - ((r - paddedMin) / paddedRange) * plotH;

    // ── Grid ──────────────────────────────────────────────────
    const yTicks = 5;
    ctx.save();
    ctx.strokeStyle = 'rgba(48,54,61,0.6)';
    ctx.lineWidth   = 1;
    ctx.setLineDash([3, 4]);
    for (let i = 0; i <= yTicks; i++) {
      const y = p.top + (i / yTicks) * plotH;
      ctx.beginPath(); ctx.moveTo(p.left, y); ctx.lineTo(W - p.right, y); ctx.stroke();
    }
    ctx.restore();

    // ── Gradient fill ─────────────────────────────────────────
    const grad = ctx.createLinearGradient(0, p.top, 0, p.top + plotH);
    grad.addColorStop(0,   'rgba(88, 166, 255, 0.25)');
    grad.addColorStop(0.6, 'rgba(88, 166, 255, 0.05)');
    grad.addColorStop(1,   'rgba(88, 166, 255, 0)');

    if (data.length > 1) {
      ctx.beginPath();
      ctx.moveTo(xOf(0), yOf(data[0].rate));
      for (let i = 1; i < data.length; i++) {
        const xp = xOf(i - 1), yp = yOf(data[i - 1].rate);
        const xn = xOf(i),     yn = yOf(data[i].rate);
        const cpx = (xp + xn) / 2;
        ctx.bezierCurveTo(cpx, yp, cpx, yn, xn, yn);
      }
      ctx.lineTo(xOf(data.length - 1), p.top + plotH);
      ctx.lineTo(xOf(0), p.top + plotH);
      ctx.closePath();
      ctx.fillStyle = grad;
      ctx.fill();
    }

    // ── Line ──────────────────────────────────────────────────
    const lineGrad = ctx.createLinearGradient(p.left, 0, W - p.right, 0);
    lineGrad.addColorStop(0, '#58a6ff');
    lineGrad.addColorStop(1, '#a371f7');

    if (data.length === 1) {
      const singleY = yOf(data[0].rate);
      ctx.beginPath();
      ctx.moveTo(p.left, singleY);
      ctx.lineTo(W - p.right, singleY);
      ctx.strokeStyle = lineGrad;
      ctx.lineWidth   = 2.5;
      ctx.setLineDash([]);
      ctx.stroke();

      ctx.beginPath();
      ctx.arc(p.left + plotW / 2, singleY, 5, 0, Math.PI * 2);
      ctx.fillStyle = '#58a6ff';
      ctx.fill();
      ctx.strokeStyle = '#fff';
      ctx.lineWidth = 2;
      ctx.stroke();
    } else {
      ctx.beginPath();
      ctx.moveTo(xOf(0), yOf(data[0].rate));
      for (let i = 1; i < data.length; i++) {
        const xp = xOf(i - 1), yp = yOf(data[i - 1].rate);
        const xn = xOf(i),     yn = yOf(data[i].rate);
        const cpx = (xp + xn) / 2;
        ctx.bezierCurveTo(cpx, yp, cpx, yn, xn, yn);
      }
      ctx.strokeStyle = lineGrad;
      ctx.lineWidth   = 2.5;
      ctx.lineJoin    = 'round';
      ctx.setLineDash([]);
      ctx.stroke();
    }

    // ── Y-axis labels ─────────────────────────────────────────
    ctx.fillStyle  = 'rgba(125,133,144,0.9)';
    ctx.font       = '11px Inter, sans-serif';
    ctx.textAlign  = 'right';
    ctx.textBaseline = 'middle';
    for (let i = 0; i <= yTicks; i++) {
      const val = paddedMin + (1 - i / yTicks) * paddedRange;
      const y   = p.top + (i / yTicks) * plotH;
      ctx.fillText(formatNumber(val, 4), p.left - 8, y);
    }

    // ── X-axis labels ─────────────────────────────────────────
    const labelCount = Math.min(6, data.length);
    ctx.textAlign    = 'center';
    ctx.textBaseline = 'top';
    for (let k = 0; k < labelCount; k++) {
      const idx = data.length === 1 ? 0 : Math.round((k / Math.max(1, labelCount - 1)) * (data.length - 1));
      const x   = data.length === 1 ? (p.left + plotW / 2) : xOf(idx);
      const lbl = formatDateLabel(data[idx].date);
      ctx.fillText(lbl, x, p.top + plotH + 10);
    }

    // ── Hover point ───────────────────────────────────────────
    if (this._hoverIdx >= 0 && this._hoverIdx < data.length) {
      const hx = xOf(this._hoverIdx);
      const hy = yOf(data[this._hoverIdx].rate);

      // Vertical line
      ctx.save();
      ctx.strokeStyle = 'rgba(88,166,255,0.45)';
      ctx.lineWidth   = 1;
      ctx.setLineDash([4, 3]);
      ctx.beginPath(); ctx.moveTo(hx, p.top); ctx.lineTo(hx, p.top + plotH); ctx.stroke();
      ctx.restore();

      // Dot
      ctx.beginPath();
      ctx.arc(hx, hy, 5.5, 0, Math.PI * 2);
      ctx.fillStyle   = '#58a6ff';
      ctx.fill();
      ctx.strokeStyle = '#fff';
      ctx.lineWidth   = 2;
      ctx.stroke();
    }
  }

  // ── Events ───────────────────────────────────────────────────
  _bindEvents() {
    this.canvas.addEventListener('mousemove', e => this._onMouseMove(e));
    this.canvas.addEventListener('mouseleave', () => {
      this._hoverIdx = -1;
      this.tooltip.classList.remove('visible');
      this.render();
    });

    const handleTouch = e => {
      if (!e.touches || !e.touches.length) return;
      e.preventDefault();
      this._onMouseMove(e.touches[0]);
    };
    this.canvas.addEventListener('touchstart', handleTouch, { passive: false });
    this.canvas.addEventListener('touchmove', handleTouch, { passive: false });
    this.canvas.addEventListener('touchend', () => {
      this._hoverIdx = -1;
      this.tooltip.classList.remove('visible');
      this.render();
    });

    if (window.ResizeObserver && this.canvas.parentElement) {
      this._ro = new ResizeObserver(() => this._resize());
      this._ro.observe(this.canvas.parentElement);
    }
  }

  _onMouseMove(e) {
    if (!this.data || !this.data.length) return;
    const rect  = this.canvas.getBoundingClientRect();
    const W     = rect.width;
    const H     = rect.height;
    const mx    = e.clientX - rect.left;
    const my    = e.clientY - rect.top;
    const { padding: p } = this;
    const plotW = W - p.left - p.right;
    if (plotW <= 0) return;

    // Check if within bounds
    if (mx < p.left - 12 || mx > W - p.right + 12 || my < 0 || my > H) {
      if (this._hoverIdx !== -1) {
        this._hoverIdx = -1;
        this.tooltip.classList.remove('visible');
        this.render();
      }
      return;
    }

    const ratio = Math.max(0, Math.min(1, (mx - p.left) / plotW));
    const idx   = Math.round(ratio * (this.data.length - 1));

    if (idx !== this._hoverIdx) {
      this._hoverIdx = idx;
      this.render();
    }
    this._updateTooltip(idx, rect);
  }

  _updateTooltip(idx, rect) {
    const d = this.data[idx];
    if (!d) return;
    const dateEl = this.tooltip.querySelector('.chart-tooltip__date');
    const rateEl = this.tooltip.querySelector('.chart-tooltip__rate');
    if (dateEl) dateEl.textContent = formatDateFull(d.date);
    if (rateEl) rateEl.textContent = formatNumber(d.rate, 6);

    const { padding: p } = this;
    const plotW = rect.width - p.left - p.right;
    const plotH = rect.height - p.top - p.bottom;

    const rates  = this.data.map(item => item.rate);
    const minR   = Math.min(...rates);
    const maxR   = Math.max(...rates);
    const rangeR = maxR - minR || 1;
    const paddedMin = minR - rangeR * 0.08;
    const paddedMax = maxR + rangeR * 0.08;
    const paddedRange = paddedMax - paddedMin;

    const hx = p.left + (idx / Math.max(1, this.data.length - 1)) * plotW;
    const hy = p.top  + plotH - ((d.rate - paddedMin) / paddedRange) * plotH;

    // Center tooltip on hover crosshair / dot
    const tipW = this.tooltip.offsetWidth || 130;
    const tipH = this.tooltip.offsetHeight || 52;

    let left = hx - tipW / 2;
    if (left < 8) left = 8;
    if (left + tipW > rect.width - 8) left = rect.width - tipW - 8;

    // Position above dot; flip below dot if near top edge
    let top = hy - tipH - 12;
    if (top < 8) top = hy + 14;

    this.tooltip.style.left = Math.round(left) + 'px';
    this.tooltip.style.top  = Math.round(top) + 'px';
    this.tooltip.classList.add('visible');
  }

  // ── Resize ───────────────────────────────────────────────────
  _resize() {
    const rect = this.canvas.getBoundingClientRect();
    const w = rect.width || (this.canvas.parentElement ? this.canvas.parentElement.clientWidth : 800);
    const h = rect.height || (this.canvas.parentElement ? this.canvas.parentElement.clientHeight : 260);
    const dpr = window.devicePixelRatio || 1;

    this.canvas.width  = Math.round(w * dpr);
    this.canvas.height = Math.round(h * dpr);
    this.canvas.style.width  = w + 'px';
    this.canvas.style.height = h + 'px';
    this.ctx.setTransform(1, 0, 0, 1, 0, 0);
    this.ctx.scale(dpr, dpr);
    this.render();
  }

  resize() { this._resize(); }
}

// ══════════════════════════════════════════════════════════════
// 5. ChartController — manages period buttons + data fetching
// ══════════════════════════════════════════════════════════════
class ChartController {
  constructor() {
    this.canvasEl   = document.getElementById('history-canvas');
    this.tooltipEl  = document.getElementById('chart-tooltip');
    this.loadingEl  = document.getElementById('chart-loading');
    this.errorEl    = document.getElementById('chart-error');

    if (!this.canvasEl) return;

    this.chart  = new LineChart(this.canvasEl, this.tooltipEl);
    this.from   = 'USD';
    this.to     = 'MYR';
    this.period = '30d';
    this.cache  = {};     // key: `${from}_${to}_${period}`

    this._bindPeriodBtns();
    this._bindResize();
    this._bindLangChange();

    // Initial load
    this.load();
  }

  _bindPeriodBtns() {
    document.querySelectorAll('.period-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.period = btn.dataset.period;
        this.load();
      });
    });
  }

  _bindResize() {
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => this.chart.resize(), 150);
    });
  }

  _bindLangChange() {
    document.addEventListener('langchange', () => this._updateChartTitle());
  }

  _updateChartTitle() {
    const titleEl = document.getElementById('chart-title');
    if (titleEl) {
      titleEl.textContent = t('chart_title', { from: this.from, to: this.to });
    }
  }

  updatePair(from, to) {
    if (!from || !to) return;
    this.from = from;
    this.to   = to;
    this._updateChartTitle();
    this.load();
  }

  _updateSubtitle(data) {
    const subtitleEl = document.querySelector('.chart-subtitle');
    if (!subtitleEl) return;
    if (data && data.length > 0) {
      const latest = data[data.length - 1];
      subtitleEl.textContent = `1 ${this.from} = ${formatNumber(latest.rate, 4)} ${this.to} · ${this.period.toUpperCase()}`;
    } else {
      subtitleEl.textContent = '';
    }
  }

  async load() {
    const key = `${this.from}_${this.to}_${this.period}`;

    // Cache hit
    if (this.cache[key]) {
      this.chart.setData(this.cache[key]);
      this._updateSubtitle(this.cache[key]);
      return;
    }

    this._setLoading(true);

    try {
      const url = `${BASE}/api/history.php?from=${this.from}&to=${this.to}&period=${this.period}`;
      const res  = await fetch(url);
      const data = await res.json();

      if (data.error || !data.data || data.data.length === 0) {
        throw new Error(data.message || t('chart_no_data'));
      }

      this.cache[key] = data.data;
      this.chart.setData(data.data);
      this._updateSubtitle(data.data);
      this._setLoading(false);
    } catch (err) {
      this._setLoading(false, err.message || t('chart_error'));
    }
  }

  _setLoading(on, errorMsg = null) {
    const subtitleEl = document.querySelector('.chart-subtitle');
    if (on && subtitleEl) subtitleEl.textContent = t('chart_loading');
    this.canvasEl.style.opacity  = on ? '0' : '1';
    this.loadingEl.classList.toggle('visible', on && !errorMsg);
    this.errorEl.classList.toggle('visible',   !!errorMsg);
    if (errorMsg) {
      const msgEl = this.errorEl.querySelector('.chart-error-msg');
      if (msgEl) msgEl.textContent = errorMsg;
    }
  }
}

// ══════════════════════════════════════════════════════════════
// 6. RatesPage — A-Z currency directory
// ══════════════════════════════════════════════════════════════
class RatesPage {
  constructor() {
    this.tableBody = document.getElementById('rates-tbody');
    if (!this.tableBody) return;

    this.searchInput = document.getElementById('rates-search');
    this.azBtns      = document.querySelectorAll('.az-btn');
    this.baseSelect  = document.getElementById('base-select');
    this.allRows     = [];
    this.activeLetter = null;
    this.baseRates   = {};

    this._bindEvents();
    this._load();
  }

  _bindEvents() {
    this.searchInput?.addEventListener('input', () => {
      this.activeLetter = null;
      this.azBtns.forEach(b => b.classList.remove('active'));
      this.azBtns[0]?.classList.add('active'); // ALL
      this._filter(this.searchInput.value.trim().toLowerCase());
    });

    this.azBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        this.azBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const letter = btn.dataset.letter;
        this.activeLetter = letter === 'all' ? null : letter;
        if (this.searchInput) this.searchInput.value = '';
        this._filter('');
      });
    });

    this.baseSelect?.addEventListener('change', () => {
      this._loadRates(this.baseSelect.value);
    });

    document.addEventListener('langchange', () => {
      this._updateColumnNames();
      this._renderRows();
    });
  }

  async _load() {
    this._showLoading(true);
    try {
      // Load currencies and rates in parallel
      const [currRes, rateRes] = await Promise.all([
        fetch(BASE + '/api/currencies.php'),
        fetch(BASE + '/api/rates.php?base=' + (this.baseSelect?.value || 'USD')),
      ]);
      const currData = await currRes.json();
      const rateData = await rateRes.json();

      if (currData.error) throw new Error(currData.message);
      this.currencies = currData.currencies;

      if (!rateData.error && rateData.rates) {
        rateData.rates.forEach(r => { this.baseRates[r.code] = r; });
      }

      this._renderRows();
    } catch (err) {
      console.error('RatesPage load error:', err);
      this.tableBody.innerHTML = `<tr><td colspan="6" class="table-empty">
        <div class="table-empty__icon">⚠️</div>
        <div class="table-empty__text">${escHtml(t('err_api_unavailable'))}</div>
      </td></tr>`;
    } finally {
      this._showLoading(false);
    }
  }

  async _loadRates(base) {
    this.baseRates = {};
    try {
      const res  = await fetch(`${BASE}/api/rates.php?base=${encodeURIComponent(base)}`);
      const data = await res.json();
      if (!data.error && data.rates) {
        data.rates.forEach(r => { this.baseRates[r.code] = r; });
        this._renderRows();
      }
    } catch (e) { console.error(e); }
  }

  _renderRows() {
    if (!this.currencies) return;
    const base = this.baseSelect?.value || 'USD';
    const frag = document.createDocumentFragment();

    this.currencies.forEach(c => {
      const rateInfo = this.baseRates[c.code];
      const rate     = rateInfo ? formatNumber(rateInfo.rate, 6) : '—';
      const name     = currentLang === 'zh' ? (c.currency_zh || c.currency_en) : c.currency_en;
      const country  = currentLang === 'zh' ? (c.country_zh  || c.country_en)  : c.country_en;
      const updated  = rateInfo?.fetched_at ? timeAgo(rateInfo.fetched_at) : '—';

      const tr = document.createElement('tr');
      tr.dataset.code    = c.code;
      tr.dataset.name    = `${c.currency_en} ${c.currency_zh}`.toLowerCase();
      tr.dataset.country = `${c.country_en} ${c.country_zh}`.toLowerCase();
      tr.innerHTML = `
        <td><span class="rate-code">${escHtml(c.code)}</span></td>
        <td>${escHtml(name)}</td>
        <td>${escHtml(country)}</td>
        <td><span class="rate-symbol">${escHtml(c.symbol)}</span></td>
        <td><span class="rate-value">${escHtml(rate)}</span></td>
        <td><span class="rate-updated">${escHtml(updated)}</span></td>
      `;
      frag.appendChild(tr);
    });

    this.tableBody.innerHTML = '';
    this.tableBody.appendChild(frag);
    this.allRows = [...this.tableBody.querySelectorAll('tr')];
    this._filter(this.searchInput?.value.trim().toLowerCase() || '');
  }

  _updateColumnNames() {
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const k = el.dataset.i18n;
      if (k) el.textContent = t(k);
    });
  }

  _filter(q) {
    let shown = 0;
    this.allRows.forEach(tr => {
      const code    = (tr.dataset.code    || '').toLowerCase();
      const name    = (tr.dataset.name    || '').toLowerCase();
      const country = (tr.dataset.country || '').toLowerCase();

      const matchSearch  = !q || code.includes(q) || name.includes(q) || country.includes(q);
      const matchLetter  = !this.activeLetter || code.startsWith(this.activeLetter.toLowerCase());

      const show = matchSearch && matchLetter;
      tr.style.display = show ? '' : 'none';
      if (show) shown++;
    });

    // Empty state
    const emptyEl = document.getElementById('rates-empty');
    if (emptyEl) emptyEl.style.display = shown === 0 ? '' : 'none';
  }

  _showLoading(on) {
    const loadEl = document.getElementById('rates-loading');
    if (loadEl) loadEl.style.display = on ? '' : 'none';
    if (this.tableBody) this.tableBody.style.opacity = on ? '0' : '1';
  }
}

// ══════════════════════════════════════════════════════════════
// Utilities
// ══════════════════════════════════════════════════════════════
function formatNumber(n, decimals = 2) {
  if (n == null || !isFinite(n)) return '—';
  if (n >= 1000) return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  if (n >= 1)    return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 });
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: Math.max(decimals, 6) });
}

function formatDateLabel(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  if (isNaN(d)) return dateStr;
  return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

function formatDateFull(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  if (isNaN(d)) return dateStr;
  return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

function timeAgo(dateStr) {
  const d    = new Date(dateStr.replace(' ', 'T') + (dateStr.includes('+') ? '' : 'Z'));
  const secs = Math.max(0, Math.floor((Date.now() - d) / 1000));
  if (secs < 60)   return t('time_just_now');
  if (secs < 3600) return t('time_mins_ago', { m: Math.floor(secs / 60) });
  if (secs < 86400) return t('time_hours_ago', { h: Math.floor(secs / 3600) });
  return formatDateFull(dateStr.split('T')[0] || dateStr.split(' ')[0]);
}

function escHtml(str) {
  if (typeof str !== 'string') return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// ══════════════════════════════════════════════════════════════
// Bootstrap
// ══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  initNav();

  // Converter page
  if (document.getElementById('convert-btn')) {
    const conv = new Converter();
    window.chartController = new ChartController();
    window.converter = conv;
  }

  // Rates page
  if (document.getElementById('rates-tbody')) {
    new RatesPage();
  }
});
