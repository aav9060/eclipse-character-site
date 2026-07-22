<?php
// WHEN ADDING A NEW CHARACTER TO THE WEBSITE: DO THE FOLLOWING: 

// 1. CREATE THEIR JSON FILE. -> easy to read file :)
// 2. ADD ALL IMAGES TO THEIR SPECIFIC FOLDER WITHIN IMAGES -> organized storage
// 3. CREATE THEIR PAGE IN THE CHARACTER-PAGES FOLDER  -> creates their page
// 4. ADD THEM TO THE MAP ON LINE 23 of HEADER.PHP -> adds the actual clickable links to the website
// 5. ADD THEM TO THE MAP $_fixed_pages IN HEADER.PHP -> makes the nav bar animation work
// 6. ADD THEM TO THEIR RESPECTIVE FACTION PAGE -> seperate map
// 7. ADD THEM TO LINE 3 OF CHARACTERS.PHP -> populates the character page



// TO ADD A NEW FACTION, DO THE FOLLOWING:

// 1. CREATE THEIR PHP PAGE IN ./FACTIONS/
// 2. ADD THEIR LINK TO LINES 113 AND TO THE MAP $_fixed_pages IN HEADER.PHP 
// 3. ADD IT TO LINE 32 OF CHARACTERS.PHP


// ─── Initialize character data and header color ───────────────────────────
$_header_page = basename($_SERVER['SCRIPT_NAME']);
$_header_character_map = [
    'lancelot.php' => 'lancelot.json',
    'anesthesia.php'    => 'anesthesia.json',
    'ciabatta.php' => 'ciabatta.json',
    'ram.php' => 'ram.json',
    'paradise.php' => 'paradise.json',
    'jackknife.php' => 'jackknife.json',
    'mina.php' => 'mina.json',
    'rogue.php' => 'rogue.json',
    'gemini.php' => 'gemini.json',
    'slyx.php' => 'slyx.json',
    '4j.php' => '4j.json',
    'clockwork.php' => 'clockwork.json',
    'bracken.php' => 'bracken.json',
    'mahere.php' => 'mahere.json',
    'nexus.php' => 'nexus.json',
    'chili.php' => 'chili.json',
    'etude.php' => 'etude.json',
    'shade.php' => 'shade.json',
    'umi.php' => 'umi.json'
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
        $_header_role = $_header_parts['role'] ?? '';
        $_header_weapon_type = $_header_parts['weaponType'] ?? '';
        $_header_faction_title = $_header_character_data['faction'] ?? 'Unassigned';
        $_header_va = $_header_parts['voiceActor'];

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
        $_header_title_image = $_header_character_data['character_title'];

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
    'Echo//Net' => '/factions/echonet.php',
    'Archeologist Faction' => '/factions/archeologists.php',
    'Pillars of Sol' => '/factions/pillarsofsol.php',
    'The Flock' => '/factions/flock.php',
    'Neverwhere' => '/factions/neverwhere.php',
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
        'page' => '/character-pages/' . $_header_page_key,
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
$_fixed_pages = ['lancelot.php', 'anesthesia.php', 'ciabatta.php', 'ram.php', 'paradise.php', 'jackknife.php', 'mina.php', 'rogue.php', 'gemini.php', 'slyx.php', '4j.php', 'clockwork.php', 'bracken.php', 'mahere.php', 'nexus.php', 'chili.php', 'etude.php', 'shade.php','umi.php', 'characters.php', 'archeologists.php', 'echonet.php', 'pillarsofsol.php', 'neverwhere.php'];
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

<link rel="stylesheet" href="/assets/css/header.css">

<style>
  :root {
    --header-color: <?= htmlspecialchars($_header_color) ?>;
    --header-muted: <?= htmlspecialchars($_text_muted) ?>;
    --header-muted-hover: <?= htmlspecialchars($_text_muted_hover) ?>;
    --header-separator: <?= htmlspecialchars($_separator_color) ?>;
  }
</style>

<script>
  (function () {
    const nav = document.getElementById('siteNav');
    if (!nav) return;

    nav.querySelectorAll('.nav-dropdown').forEach(function (dropdown) {
      let closeTimer;
      let subCloseTimer;

      const openMenu = function () {
        clearTimeout(closeTimer);
        dropdown.classList.add('dropdown-open');
      };

      const closeMenu = function () {
        clearTimeout(closeTimer);
        closeTimer = window.setTimeout(function () {
          dropdown.classList.remove('dropdown-open');
          dropdown.querySelectorAll('.faction-item').forEach(function (item) {
            item.classList.remove('dropdown-open');
          });
        }, 180);
      };

      dropdown.addEventListener('mouseenter', openMenu);
      dropdown.addEventListener('mouseleave', closeMenu);
      dropdown.addEventListener('focusin', openMenu);
      dropdown.addEventListener('focusout', function (event) {
        if (!dropdown.contains(event.relatedTarget)) {
          closeMenu();
        }
      });

      dropdown.querySelectorAll('.faction-item').forEach(function (item) {
        const openSubmenu = function () {
          clearTimeout(subCloseTimer);
          item.classList.add('dropdown-open');
        };

        const closeSubmenu = function () {
          clearTimeout(subCloseTimer);
          subCloseTimer = window.setTimeout(function () {
            item.classList.remove('dropdown-open');
          }, 140);
        };

        item.addEventListener('mouseenter', openSubmenu);
        item.addEventListener('mouseleave', closeSubmenu);
        item.addEventListener('focusin', openSubmenu);
        item.addEventListener('focusout', function (event) {
          if (!item.contains(event.relatedTarget)) {
            closeSubmenu();
          }
        });
      });
    });

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