<?php
// ─── Read flair color from JSON (fallback to site default) ────────────────────
// ─── Determine which JSON file to use ─────────────────────────────
$_header_json = $json_path ?? null;

// Fallback to default if none provided
if (!$_header_json) {
    $_header_json = __DIR__ . '/../../assets/json/org-data.json';
}
$_header_color = '#2b2b2b'; // default dark gray

if (file_exists($_header_json)) {
    $_header_data = json_decode(file_get_contents($_header_json), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $_header_color = $_header_data['flair']['color'] ?? '#87cefa';
    }
}

// ─── Determine text color via relative luminance ──────────────────────────────
$_hex = ltrim($_header_color, '#');
if (strlen($_hex) === 3) {
    $_hex = $_hex[0].$_hex[0].$_hex[1].$_hex[1].$_hex[2].$_hex[2];
}
$_r = hexdec(substr($_hex, 0, 2)) / 255;
$_g = hexdec(substr($_hex, 2, 2)) / 255;
$_b = hexdec(substr($_hex, 4, 2)) / 255;

$_toLinear = function($c) {
    return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
};
$_luminance = 0.2126 * $_toLinear($_r)
            + 0.7152 * $_toLinear($_g)
            + 0.0722 * $_toLinear($_b);

$_text_muted       = $_luminance > 0.179 ? 'rgba(0,0,0,0.5)'  : 'rgba(255,255,255,0.75)';
$_text_muted_hover = $_luminance > 0.179 ? '#111111'           : '#ffffff';
$_separator_color  = $_luminance > 0.179 ? 'rgba(0,0,0,0.2)'  : 'rgba(255,255,255,0.25)';

// ─── Detect active page ───────────────────────────────────────────────────────
$_current = basename($_SERVER['SCRIPT_NAME']);

$_nav_links = [
    'index.php'  => 'Home',
    'player.php' => 'Players',
];

// Pages with overflow:hidden / no window scroll — pill triggers on a timer
$_fixed_pages = ['joeschmoe8.php', 'watrre.php', 'okdragon.php', 'melon.php', 'shadowhunter.php'];
$_use_timer   = in_array($_current, $_fixed_pages);
// ──────────────────────────────────────────────────────────────────────────────
?>
<nav class="site-nav" id="siteNav">
  <div class="nav-inner">
   <?php $_first = true; foreach ($_nav_links as $_file => $_label): ?>

    <?php if (!$_first): ?>
      <span class="nav-sep">·</span>
    <?php endif; ?>

    <?php if ($_file === 'player.php'): ?>
      
      <div class="nav-dropdown <?= $_current === 'player.php' ? 'nav-active' : '' ?>">
        <button class="dropdown-toggle">
          <?= htmlspecialchars($_label) ?>
        </button>

        <div class="dropdown-menu">
          <?php $base = '/player-pages/'; ?>
          <a href="<?= $base ?>joeschmoe8.php">JoeSchmoe8</a>
          <a href="<?= $base ?>watrre.php">Watrre</a>
          <a href="<?= $base ?>okdragon.php">OkDragon</a>
          <a href="<?= $base ?>melon.php">Melon</a>
          <a href="<?= $base ?>shadowhunter.php">ShadowHunter</a>
        </div>
      </div>

    <?php else: ?>

      <a href="/<?= htmlspecialchars($_file) ?>"
        class="<?= $_current === $_file ? 'nav-active' : '' ?>">
        <?= htmlspecialchars($_label) ?>
      </a>

    <?php endif; ?>

  <?php $_first = false; endforeach; ?>
  </div>
</nav>

<style>
  .site-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    display: flex;
    justify-content: center;
    padding: 0;
    transition: padding 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ── Expanded state (top of page) ── */
  .nav-inner {
    max-width: 100vw;
    width: 100%;
    background: <?= htmlspecialchars($_header_color) ?>;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2.5rem;
    padding: 1.1rem 2.5rem;
    border-radius: 0;
    transition:
      max-width   0.7s cubic-bezier(0.4, 0, 0.2, 1),
      padding     0.6s cubic-bezier(0.4, 0, 0.2, 1),
      border-radius 0.6s cubic-bezier(0.4, 0, 0.2, 1),
      box-shadow  0.6s ease;
  }

  /* ── Scrolled / pill state ── */
  .site-nav.scrolled {
    padding: 0.75rem 1rem;
    pointer-events: none;
  }

  .site-nav.scrolled .nav-inner {
    max-width: 320px;
    padding: 0.6rem 2rem;
    border-radius: 999px;
    box-shadow: 0 4px 32px rgba(0, 0, 0, 0.35);
    pointer-events: all;
  }

  /* ── Hover on pill — re-extends to full bar (player.php) ── */
  .site-nav.scrolled:hover {
    padding: 0;
    pointer-events: all;
  }

  .site-nav.scrolled:hover .nav-inner {
    max-width: 100vw;
    padding: 1.1rem 2.5rem;
    border-radius: 0;
    box-shadow: none;
  }

  /* ── Nav links ── */
  .nav-inner a {
    font-family: 'DM Mono', monospace;
    font-size: 0.7rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: <?= $_text_muted ?>;
    text-decoration: none;
    transition: color 0.2s ease;
    white-space: nowrap;
  }

  .nav-inner a:hover {
    color: <?= $_text_muted_hover ?>;
  }

  /* ── Active page link ── */
  .nav-inner a.nav-active {
    color: <?= $_text_muted_hover ?>;
    text-decoration: underline;
    text-underline-offset: 4px;
  }

  /* ── Dot separator between links ── */
  .nav-sep {
    font-family: 'DM Mono', monospace;
    font-size: 0.7rem;
    color: <?= $_separator_color ?>;
    pointer-events: none;
    line-height: 1;
  }
  /* ── Dropdown Container ── */
.nav-dropdown {
  position: relative;
  display: flex;
  align-items: center;
}

/* Remove button styling */
.dropdown-toggle {
  font-family: 'DM Mono', monospace;
  font-size: 0.7rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  background: none;
  border: none;
  cursor: pointer;
  color: <?= $_text_muted ?>;
  padding: 0;
}

/* Hover color */
.dropdown-toggle:hover {
  color: <?= $_text_muted_hover ?>;
}

/* ── Dropdown Menu ── */
.dropdown-menu {
  position: absolute;
  top: 100%;              /* was 150% */
  left: 50%;
  transform: translateX(-50%);
  margin-top: 0.6rem;     /* creates visual spacing WITHOUT hover gap */
  background: <?= htmlspecialchars($_header_color) ?>;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.25s ease;
  box-shadow: 0 10px 30px rgba(0,0,0,0.35);
  white-space: nowrap;
  z-index: 2000;
}

/* Dropdown links */
.dropdown-menu a {
  font-family: 'DM Mono', monospace;
  font-size: 0.65rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: <?= $_text_muted ?>;
  text-decoration: none;
  transition: color 0.2s ease;
}

.nav-dropdown::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  height: 12px;
}

.dropdown-menu a:hover {
  color: <?= $_text_muted_hover ?>;
}

/* Show on hover */
.nav-dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
}

/* Active state underline */
.nav-dropdown.nav-active .dropdown-toggle {
  color: <?= $_text_muted_hover ?>;
  text-decoration: underline;
  text-underline-offset: 4px;
}
</style>

<script>
  (function () {
    const nav = document.getElementById('siteNav');

    <?php if ($_use_timer): ?>
    // index.php — fixed viewport, no scroll: show bar briefly then collapse to pill
    setTimeout(function () {
      nav.classList.add('scrolled');
    }, 1800);
    <?php else: ?>
    // Scrollable pages — stays as full bar, no auto-collapse
    let ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(function () {
          nav.classList.toggle('scrolled', window.scrollY > 40);
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
    <?php endif; ?>
  })();
</script>