<?php
/**
 * User Manual
 * manual.php
 */
define('APP_ROOT', __DIR__);
require_once 'includes/config.php';
require_once 'includes/layout.php';

pageHeader(
    'User Manual',
    'Learn how to use CurrencyX — currency conversion, live rates, historical charts, and more.',
    'manual'
);
?>

<!-- Hero -->
<section class="page-hero">
  <div class="page-hero__badge">
    <span>📖</span>
    <span data-i18n="manual_badge">Documentation</span>
  </div>
  <h1 data-i18n="manual_title">User Manual</h1>
  <p data-i18n="manual_subtitle">Everything you need to know about CurrencyX.</p>
</section>

<div class="manual-wrap">

  <!-- ============================================================ -->
  <!-- ENGLISH VERSION -->
  <!-- ============================================================ -->
  <div class="manual-en">
    <!-- Table of Contents -->
    <nav class="manual-toc" aria-label="Table of contents">
      <h2>Contents</h2>
      <ol>
        <li><a href="#en-getting-started">Getting Started</a></li>
        <li><a href="#en-converter">Currency Converter</a></li>
        <li><a href="#en-rates">Live Exchange Rates</a></li>
        <li><a href="#en-charts">Historical Charts</a></li>
        <li><a href="#en-search">Currency Search</a></li>
        <li><a href="#en-language">Language Settings</a></li>
        <li><a href="#en-data-sources">Data Sources</a></li>
        <li><a href="#en-faq">FAQ</a></li>
      </ol>
    </nav>

    <!-- Section 1: Getting Started -->
    <section class="manual-section" id="en-getting-started">
      <h2>🚀 Getting Started</h2>
      <p>
        Welcome to <strong>CurrencyX</strong> — a real-time currency converter with live exchange rates
        for 170+ world currencies. No sign-up required. Just open the site and start converting.
      </p>
      <div class="tip-box">
        <span class="tip-box__icon">💡</span>
        <span>
          CurrencyX remembers your last selected currency pair and preferred language across sessions.
        </span>
      </div>
      <p>The website has three pages:</p>
      <ul>
        <li><strong>Converter</strong> — Convert any currency pair instantly with live rates.</li>
        <li><strong>Rates</strong> — Browse the A–Z directory of all exchange rates.</li>
        <li><strong>Manual</strong> — This user guide.</li>
      </ul>
    </section>

    <!-- Section 2: Converter -->
    <section class="manual-section" id="en-converter">
      <h2>💱 Currency Converter</h2>
      <p>
        The main page features a currency converter that performs conversions using live exchange rates
        without reloading the page.
      </p>

      <h3>How to Convert</h3>
      <ul>
        <li>Enter the <strong>amount</strong> you want to convert in the input field.</li>
        <li>Click the <strong>From Currency</strong> selector and search for your source currency.</li>
        <li>Click the <strong>To Currency</strong> selector and search for your target currency.</li>
        <li>Click <strong>Convert</strong> (or press Enter in the amount field).</li>
      </ul>

      <h3>Swap Button</h3>
      <p>
        Click the <strong>⇄</strong> button between the two currency selectors to instantly swap
        the source and target currencies. If you already have a result showing, the conversion
        will automatically re-run with the swapped pair.
      </p>

      <h3>Reading the Result</h3>
      <p>The result shows:</p>
      <ul>
        <li>The converted amount (e.g. <code>100 USD = 407.60 MYR</code>)</li>
        <li>The unit exchange rate (e.g. <code>1 USD = 4.076 MYR</code>)</li>
        <li>A badge indicating whether the rate is <em>Live</em>, <em>Cached</em>, or <em>Stale</em></li>
        <li>The last time the rate was updated</li>
      </ul>

      <div class="tip-box">
        <span class="tip-box__icon">⚡</span>
        <span>
          <strong>Live</strong> — fetched fresh from the API right now.<br>
          <strong>Cached</strong> — within the 1-hour cache window, very accurate.<br>
          <strong>Stale</strong> — older than 1 hour, APIs may be temporarily unavailable.
        </span>
      </div>

      <h3>Currency Search</h3>
      <p>
        When you open a currency selector, a searchable dropdown appears. You can type:
      </p>
      <ul>
        <li>A currency <strong>code</strong> (e.g. <code>MYR</code>)</li>
        <li>A currency <strong>name</strong> (e.g. <code>Ringgit</code>)</li>
        <li>A <strong>country name</strong> (e.g. <code>Malaysia</code>)</li>
      </ul>
      <p>Use Arrow keys to navigate the list, and Enter to select.</p>
    </section>

    <!-- Section 3: Live Rates -->
    <section class="manual-section" id="en-rates">
      <h2>📊 Live Exchange Rates</h2>
      <p>
        The <strong>Rates</strong> page shows all supported currencies with their current exchange rates
        relative to a selected base currency.
      </p>

      <h3>Alphabetical Filter</h3>
      <p>
        Click any letter from <strong>A</strong> to <strong>Z</strong> to filter currencies whose
        code starts with that letter. Click <strong>ALL</strong> to show all currencies.
      </p>

      <h3>Search</h3>
      <p>
        Type in the search box at the top to filter currencies by code, name, or country.
        The table updates in real-time as you type.
      </p>

      <h3>Base Currency</h3>
      <p>
        Use the <strong>Base</strong> dropdown to change the reference currency.
        All rates in the table will be recalculated relative to your chosen base.
      </p>
    </section>

    <!-- Section 4: Historical Charts -->
    <section class="manual-section" id="en-charts">
      <h2>📈 Historical Charts</h2>
      <p>
        Below the converter on the main page, you'll find an interactive line chart showing
        the historical exchange rate for the selected currency pair.
      </p>

      <h3>Period Buttons</h3>
      <ul>
        <li><code>7D</code> — Last 7 days</li>
        <li><code>30D</code> — Last 30 days</li>
        <li><code>90D</code> — Last 90 days</li>
        <li><code>1Y</code>  — Last 12 months</li>
        <li><code>5Y</code>  — Last 5 years (weekly samples)</li>
        <li><code>MAX</code> — All available data from 1999 (monthly samples)</li>
      </ul>

      <h3>Hover Tooltip</h3>
      <p>
        Hover your mouse over the chart to see the exact rate and date at any point.
        On touch devices, use a single finger to drag over the chart.
      </p>

      <div class="tip-box">
        <span class="tip-box__icon">📌</span>
        <span>
          Historical data is sourced from <strong>frankfurter.app</strong> and cached locally.
          Very exotic currency pairs may have limited historical data.
        </span>
      </div>
    </section>

    <!-- Section 5: Search -->
    <section class="manual-section" id="en-search">
      <h2>🔍 Currency Search</h2>
      <p>
        CurrencyX supports searching in multiple languages. You can search currencies using:
      </p>
      <ul>
        <li>English currency codes (e.g. <code>USD</code>, <code>EUR</code>)</li>
        <li>English names (e.g. <code>Dollar</code>, <code>Euro</code>)</li>
        <li>English country names (e.g. <code>United States</code>)</li>
        <li>Chinese names (e.g. <code>美元</code>, <code>欧元</code>)</li>
        <li>Chinese country names (e.g. <code>美国</code>)</li>
      </ul>
    </section>

    <!-- Section 6: Language -->
    <section class="manual-section" id="en-language">
      <h2>🌐 Language Settings</h2>
      <p>
        CurrencyX supports <strong>English</strong> and <strong>Chinese (Simplified)</strong>.
      </p>
      <p>
        Click the <strong>EN | 中文</strong> toggle button in the top-right corner of the navigation bar
        to switch between languages. Your preference is saved automatically and will be remembered
        the next time you visit.
      </p>
      <p>
        In Chinese mode, currency names, country names, and all UI text switch to Chinese.
      </p>
    </section>

    <!-- Section 7: Data Sources -->
    <section class="manual-section" id="en-data-sources">
      <h2>🔗 Data Sources</h2>
      <p>CurrencyX uses two reliable, free exchange-rate APIs:</p>

      <h3>Primary: open.er-api.com</h3>
      <ul>
        <li>170+ currencies</li>
        <li>Updates every 24 hours</li>
        <li>Free tier, no API key required</li>
      </ul>

      <h3>Fallback: frankfurter.app</h3>
      <ul>
        <li>33 major currencies</li>
        <li>Historical data from January 1999</li>
        <li>Free, open source, European Central Bank data</li>
      </ul>

      <h3>Caching Policy</h3>
      <p>
        Rates are cached in a local MySQL database for <strong>1 hour</strong>.
        If neither API is reachable, the most recently cached rates are used automatically.
        This ensures the converter keeps working even during temporary API outages.
      </p>
    </section>

    <!-- Section 8: FAQ -->
    <section class="manual-section" id="en-faq">
      <h2>❓ FAQ</h2>

      <h3>Why does the rate show "Stale"?</h3>
      <p>
        This means the cached rate is older than 1 hour and the live API could not be reached.
        The rate shown is the most recent one we have. Refresh and try again shortly.
      </p>

      <h3>Is this rate suitable for financial transactions?</h3>
      <p>
        No. CurrencyX is for <strong>informational purposes only</strong>. Bank and money-transfer
        rates will differ due to fees, spreads, and timing. Always confirm rates with your
        financial institution.
      </p>

      <h3>Why doesn't the chart load for some currency pairs?</h3>
      <p>
        Historical data is sourced from frankfurter.app, which supports 33 major currencies.
        For exotic pairs, we compute cross rates using USD as a pivot. If no historical data
        exists in our database yet, the chart may show an error — it will populate over time
        as you use the converter.
      </p>

      <h3>How do I report a bug or suggest a feature?</h3>
      <p>
        Please open an issue on the project's GitHub repository linked in the README.
      </p>
    </section>
  </div><!-- .manual-en -->


  <!-- ============================================================ -->
  <!-- CHINESE VERSION -->
  <!-- ============================================================ -->
  <div class="manual-zh">
    <!-- Table of Contents -->
    <nav class="manual-toc" aria-label="目录导航">
      <h2>目录</h2>
      <ol>
        <li><a href="#zh-getting-started">快速入门</a></li>
        <li><a href="#zh-converter">货币换算器</a></li>
        <li><a href="#zh-rates">实时汇率表</a></li>
        <li><a href="#zh-charts">历史走势图表</a></li>
        <li><a href="#zh-search">货币搜索指南</a></li>
        <li><a href="#zh-language">多语言设置</a></li>
        <li><a href="#zh-data-sources">数据来源与缓存</a></li>
        <li><a href="#zh-faq">常见问题 (FAQ)</a></li>
      </ol>
    </nav>

    <!-- Section 1: Getting Started -->
    <section class="manual-section" id="zh-getting-started">
      <h2>🚀 快速入门</h2>
      <p>
        欢迎使用 <strong>CurrencyX</strong> —— 一款支持全球 170 多种货币的实时汇率换算工具。免注册免登录，打开网页即可立即使用。
      </p>
      <div class="tip-box">
        <span class="tip-box__icon">💡</span>
        <span>
          CurrencyX 会在本地浏览器中自动保存您上次选择的货币对与语言偏好，下次访问无需重复设置。
        </span>
      </div>
      <p>本站包含三个主要页面：</p>
      <ul>
        <li><strong>汇率换算</strong> —— 利用实时汇率，即时换算任意货币对。</li>
        <li><strong>汇率表</strong> —— 浏览全球所有货币按 A–Z 字母排序的汇率目录。</li>
        <li><strong>使用说明</strong> —— 本操作手册与使用帮助。</li>
      </ul>
    </section>

    <!-- Section 2: Converter -->
    <section class="manual-section" id="zh-converter">
      <h2>💱 货币换算器</h2>
      <p>
        首页提供现代化货币转换器，采用 AJAX 异步获取最新汇率，无需刷新网页即可即时完成换算。
      </p>

      <h3>如何进行换算</h3>
      <ul>
        <li>在金额输入框中输入您要换算的<strong>金额</strong>（例如 100）。</li>
        <li>点击<strong>源货币</strong>下拉框，搜索并选择您持有的货币（例如 USD）。</li>
        <li>点击<strong>目标货币</strong>下拉框，搜索并选择您要兑换的目标货币（例如 MYR）。</li>
        <li>点击<strong>换算</strong>按钮（或在金额框中直接按 Enter 回车键）。</li>
      </ul>

      <h3>一键对调按钮 (⇄)</h3>
      <p>
        点击两个货币选择器中间的 <strong>⇄</strong> 按钮，即可瞬间调换源货币与目标货币的位置。如果当前已显示计算结果，系统将自动使用调换后的货币重新计算，省时便捷。
      </p>

      <h3>换算结果解析</h3>
      <p>结果卡片包含以下信息：</p>
      <ul>
        <li>兑换总额（例如 <code>100 USD = 407.62 MYR</code>）</li>
        <li>基准单价汇率（例如 <code>1 USD = 4.0762 MYR</code>）</li>
        <li>数据时效状态徽章（<em>实时 Live</em>、<em>缓存 Cached</em> 或 <em>过期 Stale</em>）</li>
        <li>数据最后更新时间</li>
      </ul>

      <div class="tip-box">
        <span class="tip-box__icon">⚡</span>
        <span>
          <strong>实时 (Live)</strong> —— 刚刚从官方接口获取的最新鲜汇率。<br>
          <strong>缓存 (Cached)</strong> —— 处于 1 小时缓存有效期内，精度高响应快。<br>
          <strong>过期 (Stale)</strong> —— 数据超过 1 小时，通常因外部 API 临时网络故障引起，仍可作参考。
        </span>
      </div>

      <h3>货币智能搜索</h3>
      <p>
        打开货币选择器后会展开搜索列表，支持多种检索方式：
      </p>
      <ul>
        <li>货币<strong>代码</strong>（例如 <code>MYR</code>、<code>CNY</code>、<code>USD</code>）</li>
        <li>货币<strong>名称</strong>（例如 <code>林吉特</code>、<code>人民币</code>、<code>美元</code>）</li>
        <li><strong>国家/地区名称</strong>（例如 <code>马来西亚</code>、<code>中国</code>、<code>美国</code>）</li>
      </ul>
      <p>支持使用键盘上下方向键 ↑ ↓ 浏览选项，按 Enter 回车键快速选择。</p>
    </section>

    <!-- Section 3: Live Rates -->
    <section class="manual-section" id="zh-rates">
      <h2>📊 实时汇率表</h2>
      <p>
        <strong>汇率表</strong>页面以清晰直观的表格形式，展示所有支持货币相对于指定基准货币的最新汇率。
      </p>

      <h3>首字母索引 (A–Z)</h3>
      <p>
        点击顶部 <strong>A</strong> 到 <strong>Z</strong> 字母按钮，可快速过滤出货币代码以该字母开头的货币；点击 <strong>全部 (ALL)</strong> 恢复完整列表。
      </p>

      <h3>实时关键词检索</h3>
      <p>
        在表格上方的搜索框中输入代码、货币中文名或国家名称，表格将随着输入实时筛选出匹配行。
      </p>

      <h3>切换基准货币</h3>
      <p>
        通过<strong>基准货币</strong>下拉菜单（如 USD、EUR、GBP、CNY、MYR 等），可更改参考基准，全表汇率将自动基于选定基准货币全部重新换算。
      </p>
    </section>

    <!-- Section 4: Historical Charts -->
    <section class="manual-section" id="zh-charts">
      <h2>📈 历史走势图表</h2>
      <p>
        在首页换算器下方配有动态历史汇率走势折线图，直观展示所选货币对在不同时期的汇率涨跌趋势。
      </p>

      <h3>时间周期选择</h3>
      <ul>
        <li><code>7天 (7D)</code> —— 最近 7 天的每日走势</li>
        <li><code>30天 (30D)</code> —— 最近 30 天的每日走势（默认）</li>
        <li><code>90天 (90D)</code> —— 最近 90 天走势</li>
        <li><code>1年 (1Y)</code>  —— 最近 12 个月走势</li>
        <li><code>5年 (5Y)</code>  —— 最近 5 年走势（每周抽样）</li>
        <li><code>全部 (MAX)</code> —— 自 1999 年以来的全部可用历史走势（每月抽样）</li>
      </ul>

      <h3>精准鼠标悬停追踪</h3>
      <p>
        将鼠标指针移动到图表曲线上，辅助虚线十字光标与高亮光斑会自动定位到最近日期，并在上方精准显示当天的日期与精确汇率。在移动设备触屏上，支持单指拖动查看。
      </p>

      <div class="tip-box">
        <span class="tip-box__icon">📌</span>
        <span>
          历史数据由欧洲央行官方汇率接口 <strong>frankfurter.app</strong> 提供并在本地自动缓存。部分极小众冷门货币对可能历史数据有限。
        </span>
      </div>
    </section>

    <!-- Section 5: Search -->
    <section class="manual-section" id="zh-search">
      <h2>🔍 货币搜索指南</h2>
      <p>
        CurrencyX 内置强大的双语搜索引擎，无论当前处于中文还是英文模式，您均可使用：
      </p>
      <ul>
        <li>标准 3 位国际货币代码（例如 <code>USD</code>、<code>EUR</code>、<code>JPY</code>）</li>
        <li>中文货币名称（例如 <code>美元</code>、<code>欧元</code>、<code>日元</code>、<code>林吉特</code>）</li>
        <li>中文国家名称（例如 <code>中国</code>、<code>美国</code>、<code>马来西亚</code>、<code>新加坡</code>）</li>
        <li>英文货币及国家名称（例如 <code>Dollar</code>、<code>Ringgit</code>、<code>Japan</code>）</li>
      </ul>
    </section>

    <!-- Section 6: Language -->
    <section class="manual-section" id="zh-language">
      <h2>🌐 多语言设置</h2>
      <p>
        CurrencyX 原生支持<strong>简体中文</strong>与<strong>英文 (English)</strong>双语界面。
      </p>
      <p>
        点击顶部导航栏右上角的 <strong>EN | 中文</strong> 切换按钮即可无缝切换语言。您的语言设置会自动保存在本地存储中，下次打开网站时依然保持生效。
      </p>
      <p>
        在中文模式下，所有货币名称、所属国家名称、操作按钮、提示标签以及本说明手册均会以完整中文呈现。
      </p>
    </section>

    <!-- Section 7: Data Sources -->
    <section class="manual-section" id="zh-data-sources">
      <h2>🔗 数据来源与高可用缓存</h2>
      <p>CurrencyX 采用双重权威、免费的汇率数据服务商保障系统可靠性：</p>

      <h3>主数据源：open.er-api.com</h3>
      <ul>
        <li>覆盖全球 170+ 种法定货币</li>
        <li>每 24 小时由官方权威金融机构同步</li>
        <li>高可用节点，保障日常换算极速响应</li>
      </ul>

      <h3>备用数据源：frankfurter.app</h3>
      <ul>
        <li>欧洲中央银行 (ECB) 官方参考汇率</li>
        <li>覆盖 33 种世界主要流通货币</li>
        <li>提供自 1999 年以来的权威历史跨期数据</li>
      </ul>

      <h3>智能降级与缓存策略</h3>
      <p>
        所有成功拉取的汇率均在本地 MySQL 数据库中自动缓存 <strong>1 小时</strong>。当两个外部接口由于网络波动均暂时不可达时，系统会自动启用最近一次有效的缓存数据（标有 <code>⚠ 过期/Stale</code> 徽章），确保您的业务换算永不中断。
      </p>
    </section>

    <!-- Section 8: FAQ -->
    <section class="manual-section" id="zh-faq">
      <h2>❓ 常见问题解答 (FAQ)</h2>

      <h3>为什么汇率状态会显示为“过期 (Stale)”？</h3>
      <p>
        这表示本地缓存的数据已超过 1 小时，且当前网络环境下外部 API 接口暂时未能成功刷新。此时显示的汇率依然是近期有效的数据，您可以稍后刷新重试。
      </p>

      <h3>此汇率能否直接用于银行商业交易结算？</h3>
      <p>
        不能。CurrencyX 提供的汇率为银行间中间牌价，<strong>仅供参考与信息查询之用</strong>。各大商业银行、柜台与跨境汇款服务商会根据自身点差、手续费及实时汇率产生差异，实际结算金额请务必以金融机构最终凭单为准。
      </p>

      <h3>为什么某些货币对的历史图表无法展示？</h3>
      <p>
        历史图表数据主要来源于欧洲中央银行公开数据（涵盖主流的 33 种货币）。对于其他冷门小众货币，系统会在您日常换算时逐步沉淀本地历史数据，随着使用时间累积，图表会越来越丰富完整。
      </p>

      <h3>发现问题或有建议如何反馈？</h3>
      <p>
        欢迎在项目关联的代码仓库（README 中提供）提交 Issue 或 Pull Request，感谢您的支持！
      </p>
    </section>
  </div><!-- .manual-zh -->

</div><!-- .manual-wrap -->

<?php pageFooter(); ?>
