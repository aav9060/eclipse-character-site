<?php
// ─── Initialize character data and header color ───────────────────────────
$_header_page = basename($_SERVER['SCRIPT_NAME']);
$_header_character_map = [
    'lancelot.php' => 'lancelot.json',
    'astra.php'    => 'astra.json',
];

$_header_json = null;
$_header_character_data = [];
$_header_avatar_src = '';
$_header_bullets = [];
$_header_title = '';
$_header_scroll_text = '';
$_header_color = '#2b2b2b'; // default dark gray

if (isset($_header_character_map[$_header_page])) {
    $_header_json = __DIR__ . '/../../assets/json/' . $_header_character_map[$_header_page];
}

if (!$_header_json) {
    $_header_json = __DIR__ . '/../../assets/json/org-data.json';
}

$json_path = $_header_json;

if (file_exists($_header_json)) {
    $_header_character_data = json_decode(file_get_contents($_header_json), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $_header_avatar_src = $_header_character_data['avatar_src'] ?? '';
        $_header_bullets = $_header_character_data['bullets'] ?? $_header_character_data['key_points'] ?? [];
        $_header_parts = $_header_character_data['character_info'] ?? $_header_character_data['player_info'] ?? [];
        $_header_fn = $_header_parts['first_name'] ?? $_header_parts['firstname'] ?? '';
        $_header_un = $_header_parts['nickname'] ?? $_header_parts['username'] ?? '';
        $_header_ln = $_header_parts['last_name'] ?? $_header_parts['lastname'] ?? '';
        $_header_emoji = $_header_character_data['flair']['emoji'] ?? '';
        $_header_color = $_header_character_data['flair']['color'] ?? '#87cefa';

        $_header_title_parts = array_filter([
            $_header_fn ?: null,
            $_header_ln ?: null,
            $_header_un ? '"' . $_header_un . '"' : null,
        ]);
        $_header_title = trim(implode(' ', $_header_title_parts));
        if ($_header_emoji) {
            $_header_title = trim($_header_title . ' ' . $_header_emoji);
        }

        $_header_title_image = '';
        $_header_full_name = trim(implode(' ', array_filter([$_header_fn, $_header_ln])));
        $_header_display_name = trim($_header_un ?: $_header_full_name);
        if ($_header_page === 'lancelot.php' || $_header_page === 'astra.php') {
            $_header_title_image = '../assets/images/lancelot-title.png';
        }

        $_header_scroll_items = [];
        foreach ($_header_parts as $_header_part_key => $_header_part_value) {
            if (is_string($_header_part_value) && trim($_header_part_value) !== '') {
                $_header_scroll_items[] = trim($_header_part_value);
            }
        }
        $_header_scroll_text = implode(' • ', $_header_scroll_items);
        if ($_header_scroll_text !== '') {
            $_header_scroll_text .= ' •';
        }
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

$_header_faction_groups = [];
$_header_faction_page_map = [
    'The Warden Circle' => '/factions/the-warden-circle.php',
    'The Lantern Covenant' => '/factions/the-lantern-covenant.php',
];
foreach ($_header_character_map as $_header_page_key => $_header_json_file) {
    $_header_faction_path = __DIR__ . '/../../assets/json/' . $_header_json_file;
    if (!file_exists($_header_faction_path)) {
        continue;
    }

    $_header_faction_data = json_decode(file_get_contents($_header_faction_path), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        continue;
    }

    $_header_faction_name = trim($_header_faction_data['faction'] ?? 'Unassigned');
    $_header_character_name = trim($_header_faction_data['character_info']['nickname'] ?? $_header_faction_data['character_info']['first_name'] ?? $_header_page_key);

    $_header_faction_groups[$_header_faction_name][] = [
        'name' => $_header_character_name,
        'page' => '/player-pages/' . $_header_page_key,
        'avatar' => $_header_faction_data['portrait_src'] ?? $_header_faction_data['avatar_src'] ?? '',
    ];
}
ksort($_header_faction_groups);

if (!empty($_header_init_only)) {
    return;
}

$_nav_links = [
    'index.php'        => 'Home',
    'characters.php'   => 'Characters',
];

// Pages with overflow:hidden / no window scroll — pill triggers on a timer
$_fixed_pages = ['lancelot.php', 'astra.php', 'characters.php', 'archeologists.php', 'echonet.php'];
$_use_timer   = in_array($_current, $_fixed_pages);
// ──────────────────────────────────────────────────────────────────────────────
?>
<nav class="site-nav" id="siteNav">
  <div class="nav-inner">
   <?php $_first = true; foreach ($_nav_links as $_file => $_label): ?>

    <?php if (!$_first): ?>
      <span class="nav-sep">·</span>
    <?php endif; ?>

    <?php if ($_file === 'characters.php'): ?>
      
      <div class="nav-dropdown <?= $_current === 'characters.php' ? 'nav-active' : '' ?>">
        <a class="dropdown-toggle" href="/characters.php">
          <?= htmlspecialchars($_label) ?>
        </a>

        <div class="dropdown-menu">
          <?php foreach ($_header_faction_groups as $_header_faction_name => $_header_faction_members): ?>
            <div class="faction-item">
              <a class="faction-link" href="<?= htmlspecialchars($_header_faction_page_map[$_header_faction_name] ?? '/characters.php') ?>">
                <?= htmlspecialchars($_header_faction_name) ?>
              </a>
              <div class="faction-submenu">
                <?php foreach ($_header_faction_members as $_header_member): ?>
                  <a class="character-link" href="<?= htmlspecialchars($_header_member['page']) ?>">
                    <?php if (!empty($_header_member['avatar'])): ?>
                      <img src="<?= htmlspecialchars($_header_member['avatar']) ?>" alt="<?= htmlspecialchars($_header_member['name']) ?>">
                    <?php endif; ?>
                    <span><?= htmlspecialchars($_header_member['name']) ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
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
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-top: 0.6rem;
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
  min-width: 220px;
}

.faction-item {
  position: relative;
}

.faction-link,
.character-link {
  font-family: 'DM Mono', monospace;
  font-size: 0.65rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: <?= $_text_muted ?>;
  text-decoration: none;
  transition: color 0.2s ease;
}

.faction-link:hover,
.character-link:hover {
  color: <?= $_text_muted_hover ?>;
}

.faction-submenu {
  position: absolute;
  left: 100%;
  top: -0.5rem;
  margin-left: 0.75rem;
  background: <?= htmlspecialchars($_header_color) ?>;
  padding: 0.85rem 1rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  min-width: 220px;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease;
  box-shadow: 0 10px 30px rgba(0,0,0,0.35);
}

.character-link {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.character-link img {
  width: 28px;
  height: 28px;
  object-fit: cover;
  border-radius: 999px;
}

.faction-item:hover .faction-submenu {
  opacity: 1;
  visibility: visible;
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