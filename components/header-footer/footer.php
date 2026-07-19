<?php
// ─── Load org data from JSON ───────────────────────────────────────────────────
$_footer_json = __DIR__ . '/../../assets/json/org-data.json';
$_footer_socials = [];
$_footer_nav     = [];
$_footer_color   = '#2b2b2b';
$_footer_name    = '';

if (file_exists($_footer_json)) {
    $_footer_data = json_decode(file_get_contents($_footer_json), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $_footer_socials = $_footer_data['socials'] ?? [];
        $_footer_nav     = $_footer_data['nav']     ?? [];
        $_footer_color   = $_footer_data['color']   ?? '#2b2b2b';
        $_footer_name    = $_footer_data['name']    ?? '';
    }
}

// ─── Luminance check ──────────────────────────────────────────────────────────
$_fhex = ltrim($_footer_color, '#');
if (strlen($_fhex) === 3) {
    $_fhex = $_fhex[0].$_fhex[0].$_fhex[1].$_fhex[1].$_fhex[2].$_fhex[2];
}
$_fr = hexdec(substr($_fhex, 0, 2)) / 255;
$_fg = hexdec(substr($_fhex, 2, 2)) / 255;
$_fb = hexdec(substr($_fhex, 4, 2)) / 255;
$_fToLinear = function($c) {
    return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
};
$_fLum = 0.2126 * $_fToLinear($_fr)
       + 0.7152 * $_fToLinear($_fg)
       + 0.0722 * $_fToLinear($_fb);
$_fTextColor = $_fLum > 0.179 ? '#111111'           : '#ffffff';
$_fMuted     = $_fLum > 0.179 ? 'rgba(0,0,0,0.45)' : 'rgba(255,255,255,0.45)';
$_fBorder    = $_fLum > 0.179 ? 'rgba(0,0,0,0.1)'  : 'rgba(255,255,255,0.1)';
$_fHover     = $_fLum > 0.179 ? '#111111'           : '#ffffff';

// ─── Page detection ───────────────────────────────────────────────────────────
$_footer_page   = basename($_SERVER['SCRIPT_NAME']);
$_footer_simple = ($_footer_page === 'joeschmoe8.php' 
                        || $_footer_page === 'melon.php' || $_footer_page === 'shadowhunter.php'
                        || $_footer_page === 'okdragon.php' || $_footer_page === 'watrre.php'); // Simple footer for index + character pages
$_footer_year   = date('Y');
$_footer_copy   = '© ' . $_footer_year . ($_footer_name ? ' ' . $_footer_name : '');
// ──────────────────────────────────────────────────────────────────────────────
?>

<footer class="site-footer <?= $_footer_simple ? 'site-footer--simple' : 'site-footer--full' ?>">

  <?php if ($_footer_simple): ?>

    <!-- Simple footer: copyright only -->
    <p class="footer-copy"><?= htmlspecialchars($_footer_copy) ?></p>

  <?php else: ?>

    <!-- Full footer: nav + socials + copyright -->

      <?php if (!empty($_footer_nav)): ?>
      <nav class="footer-nav">
        <p class="footer-label">Pages</p>
        <?php foreach ($_footer_nav as $_item): ?>
          <a href="/<?= htmlspecialchars($_item['file']) ?>"
             class="footer-nav-link <?= $_footer_page === $_item['file'] ? 'footer-nav-link--active' : '' ?>">
            <?= htmlspecialchars($_item['label']) ?>
          </a>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>

      <?php if (!empty($_footer_socials)): ?>
      <nav class="footer-socials">
        <p class="footer-label">Find Us</p>
        <?php foreach ($_footer_socials as $_s): ?>
          <a href="<?= htmlspecialchars($_s['url']) ?>"
             class="footer-social-link"
             target="_blank" rel="noopener">
            <span class="footer-social-platform"><?= htmlspecialchars($_s['platform']) ?></span>
            <span class="footer-social-handle"><?= htmlspecialchars($_s['handle']) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>

    </div>

    <div class="footer-bottom">
      <p class="footer-copy"><?= htmlspecialchars($_footer_copy) ?></p>
    </div>

  <?php endif; ?>

</footer>

<style>
  .site-footer {
    background: <?= htmlspecialchars($_footer_color) ?>;
    color: <?= $_fTextColor ?>;
    font-family: 'DM Sans', sans-serif;
    margin-top: auto;
  }

  /* ── Simple (index.php) ── */
  .site-footer--simple {
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    border-top: 1px solid <?= $_fBorder ?>;
  }

  /* ── Full footer ── */
  .site-footer--full {
    border-top: 1px solid <?= $_fBorder ?>;
  }

  .footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem clamp(1.5rem, 5vw, 4rem);
    display: flex;
    flex-wrap: wrap;
    gap: 3rem;
  }

  .footer-bottom {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.25rem clamp(1.5rem, 5vw, 4rem);
    border-top: 1px solid <?= $_fBorder ?>;
  }

  /* ── Section label ── */
  .footer-label {
    font-family: 'DM Mono', monospace;
    font-size: 0.65rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: <?= $_fMuted ?>;
    margin-bottom: 1rem;
  }

  /* ── Nav links ── */
  .footer-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 120px;
  }

  .footer-nav-link {
    font-family: 'DM Mono', monospace;
    font-size: 0.75rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: <?= $_fMuted ?>;
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .footer-nav-link:hover,
  .footer-nav-link--active {
    color: <?= $_fHover ?>;
  }

  .footer-nav-link--active {
    text-decoration: underline;
    text-underline-offset: 3px;
  }

  /* ── Socials ── */
  .footer-socials {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .footer-social-link {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    transition: opacity 0.2s ease;
    opacity: 0.75;
  }

  .footer-social-link:hover {
    opacity: 1;
  }

  .footer-social-platform {
    font-family: 'DM Mono', monospace;
    font-size: 0.65rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: <?= $_fMuted ?>;
  }

  .footer-social-handle {
    font-size: 0.85rem;
    color: <?= $_fTextColor ?>;
  }

  /* ── Copyright ── */
  .footer-copy {
    font-family: 'DM Mono', monospace;
    font-size: 0.68rem;
    letter-spacing: 0.1em;
    color: <?= $_fMuted ?>;
  }
</style>