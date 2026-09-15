/**
 * Internationalisation (i18n) — EN / ZH
 * assets/js/i18n.js
 * Usage: t('key') → translated string
 *        applyTranslations() → scan [data-i18n] attrs and replace text
 */

const TRANSLATIONS = {
  en: {
    // Nav
    nav_converter:  'Converter',
    nav_rates:      'Rates',
    nav_manual:     'Manual',

    // Hero
    hero_badge:       '🌐 Live Exchange Rates',
    hero_title:       'Currency Converter',
    hero_subtitle:    'Real-time rates for 170+ currencies, powered by live APIs.',

    // Converter
    label_amount:   'Amount',
    label_from:     'From Currency',
    label_to:       'To Currency',
    btn_convert:    'Convert',
    btn_converting: 'Converting…',
    placeholder_amount: 'Enter amount',
    search_currency: 'Search currency…',

    // Result
    result_rate:     '1 {from} = {rate} {to}',
    result_updated:  'Updated {time}',
    badge_live:      '● Live',
    badge_cached:    '⊙ Cached',
    badge_stale:     '⚠ Stale',

    // Chart
    chart_title:     '{from} / {to} Exchange Rate',
    chart_loading:   'Loading chart data…',
    chart_no_data:   'No historical data available for this pair.',
    chart_error:     'Failed to load chart data.',

    // Periods
    period_7d:  '7D',
    period_30d: '30D',
    period_90d: '90D',
    period_1y:  '1Y',
    period_5y:  '5Y',
    period_max: 'MAX',

    // Rates page
    rates_title:        'Live Exchange Rates',
    rates_subtitle:     'Browse rates for all currencies, sorted A–Z.',
    rates_search_ph:    'Search by code, name, or country…',
    rates_base_label:   'Base:',
    rates_col_code:     'Code',
    rates_col_currency: 'Currency',
    rates_col_country:  'Country',
    rates_col_symbol:   'Symbol',
    rates_col_rate:     'Rate',
    rates_col_updated:  'Updated',
    rates_no_results:   'No currencies found.',
    rates_loading:      'Loading rates…',
    rates_all:          'ALL',
    rates_badge:        '🔤 A–Z Currency Directory',
    time_just_now:      'Just now',
    time_mins_ago:      '{m}m ago',
    time_hours_ago:     '{h}h ago',

    // Stats
    stat_currencies:  'Currencies',
    stat_sources:     'Data Sources',
    stat_cache:       'Cache TTL',
    stat_cache_val:   '1 Hour',

    // Manual
    manual_badge:     'Documentation',
    manual_title:     'User Manual',
    manual_subtitle:  'Everything you need to know about CurrencyX.',

    // Footer
    footer_tagline:    'Real-time exchange rates powered by open.er-api.com & frankfurter.app',
    footer_disclaimer: 'Rates are for informational purposes only. Not financial advice.',

    // Errors
    err_api_unavailable: 'Live exchange rate is temporarily unavailable. Please try again later.',
    err_no_history:      'Historical data is not available for this currency pair.',
    err_invalid_amount:  'Please enter a valid positive amount.',
    err_invalid_code:    'Please select a valid currency.',
  },

  zh: {
    // Nav
    nav_converter:  '汇率换算',
    nav_rates:      '汇率表',
    nav_manual:     '使用说明',

    // Hero
    hero_badge:       '🌐 实时汇率',
    hero_title:       '货币转换器',
    hero_subtitle:    '实时获取170多种货币汇率，数据由实时API提供。',

    // Converter
    label_amount:   '金额',
    label_from:     '源货币',
    label_to:       '目标货币',
    btn_convert:    '立即换算',
    btn_converting: '换算中…',
    placeholder_amount: '输入金额',
    search_currency: '搜索货币…',

    // Result
    result_rate:     '1 {from} = {rate} {to}',
    result_updated:  '更新时间：{time}',
    badge_live:      '● 实时',
    badge_cached:    '⊙ 已缓存',
    badge_stale:     '⚠ 过期数据',

    // Chart
    chart_title:     '{from} / {to} 历史汇率',
    chart_loading:   '正在加载图表数据…',
    chart_no_data:   '此货币对暂无历史数据。',
    chart_error:     '加载图表数据失败。',

    // Periods
    period_7d:  '7天',
    period_30d: '30天',
    period_90d: '90天',
    period_1y:  '1年',
    period_5y:  '5年',
    period_max: '全部',

    // Rates page
    rates_title:        '实时汇率表',
    rates_subtitle:     '按A至Z顺序浏览所有货币汇率。',
    rates_search_ph:    '按货币代码、名称或国家搜索…',
    rates_base_label:   '基础货币：',
    rates_col_code:     '代码',
    rates_col_currency: '货币名称',
    rates_col_country:  '国家/地区',
    rates_col_symbol:   '符号',
    rates_col_rate:     '汇率',
    rates_col_updated:  '更新时间',
    rates_no_results:   '未找到相关货币。',
    rates_loading:      '正在加载汇率…',
    rates_all:          '全部',
    rates_badge:        '🔤 A–Z 货币目录',
    time_just_now:      '刚刚',
    time_mins_ago:      '{m}分钟前',
    time_hours_ago:     '{h}小时前',

    // Stats
    stat_currencies:  '支持货币',
    stat_sources:     '数据来源',
    stat_cache:       '缓存时效',
    stat_cache_val:   '1小时',

    // Manual
    manual_badge:     '使用文档',
    manual_title:     '使用说明',
    manual_subtitle:  '了解如何使用 CurrencyX 的一切功能。',

    // Footer
    footer_tagline:    '实时汇率由 open.er-api.com 和 frankfurter.app 提供',
    footer_disclaimer: '汇率仅供参考，不构成财务建议。',

    // Errors
    err_api_unavailable: '实时汇率暂时无法获取，请稍后重试。',
    err_no_history:      '此货币对暂无历史数据。',
    err_invalid_amount:  '请输入有效的正数金额。',
    err_invalid_code:    '请选择有效的货币。',
  },
};

// ── Language state ───────────────────────────────────────────────
let currentLang = localStorage.getItem('cx-lang') || 'en';
if (!TRANSLATIONS[currentLang]) currentLang = 'en';

/** Get a translated string for the current language. */
function t(key, vars = {}) {
  const str = (TRANSLATIONS[currentLang][key] || TRANSLATIONS.en[key] || key);
  return str.replace(/\{(\w+)\}/g, (_, k) => vars[k] ?? '');
}

/** Apply [data-i18n] translations throughout the DOM. */
function applyTranslations() {
  document.documentElement.setAttribute('data-lang', currentLang);
  document.documentElement.setAttribute('lang', currentLang === 'zh' ? 'zh-Hans' : 'en');

  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.dataset.i18n;
    if (key) el.textContent = t(key);
  });
  document.querySelectorAll('[data-i18n-ph]').forEach(el => {
    const key = el.dataset.i18nPh;
    if (key) el.placeholder = t(key);
  });

  // Dispatch so page scripts can react
  document.dispatchEvent(new CustomEvent('langchange', { detail: { lang: currentLang } }));
}

/** Toggle between EN and ZH. */
function toggleLanguage() {
  currentLang = currentLang === 'en' ? 'zh' : 'en';
  localStorage.setItem('cx-lang', currentLang);
  applyTranslations();
}

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  applyTranslations();
  const btn = document.getElementById('lang-toggle');
  if (btn) btn.addEventListener('click', toggleLanguage);
});
