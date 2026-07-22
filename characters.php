<?php
$_page_title = 'Characters';
$characterFiles = [
  ['slug' => 'lancelot.php', 'json' => 'lancelot.json'],
  ['slug' => 'anesthesia.php', 'json' => 'anesthesia.json'],
  ['slug' => 'ciabatta.php', 'json' => 'ciabatta.json'],
  ['slug' => 'ram.php', 'json' => 'ram.json'],
  ['slug' => 'paradise.php', 'json' =>'paradise.json'],
  ['slug' => 'jackknife.php', 'json'=>'jackknife.json'],
  ['slug' => 'mina.php', 'json'=>'mina.json'],
  ['slug' => 'rogue.php', 'json'=>'rogue.json'],
  ['slug' => 'gemini.php', 'json'=>'gemini.json'],
  ['slug' => 'slyx.php', 'json'=>'slyx.json'],
  ['slug' => '4j.php', 'json'=>'4j.json'],
  ['slug' => 'clockwork.php', 'json'=>'clockwork.json'],
  ['slug' => 'bracken.php', 'json'=>'bracken.json'],
  ['slug' => 'mahere.php', 'json'=>'mahere.json'],
  ['slug' => 'nexus.php', 'json'=>'nexus.json'],
  ['slug' => 'chili.php', 'json'=>'chili.json'],
  ['slug' => 'etude.php', 'json'=>'etude.json'],
  ['slug' => 'shade.php', 'json'=>'shade.json'],
  ['slug' => 'umi.php', 'json'=>'umi.json'],
];

$characterEntries = [];
foreach ($characterFiles as $characterFile) {
  $jsonPath = __DIR__ . '/assets/json/' . $characterFile['json'];
  if (!file_exists($jsonPath)) {
    continue;
  }

  $data = json_decode(file_get_contents($jsonPath), true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    continue;
  }

  $characterInfo = $data['character_info'] ?? [];
  $characterEntries[] = [
    'slug' => $characterFile['slug'],
    'title' => $characterInfo['nickname'] ?? $characterInfo['first_name'] ?? ucfirst(pathinfo($characterFile['json'], PATHINFO_FILENAME)),
    'description' => $data['bio'] ?? 'A new character profile is being written.',
    'image' => $data['portrait_src'] ?? $data['avatar_src'] ?? '',
    'faction' => $data['faction'] ?? 'Unassigned',
  ];
}

$factionBackgrounds = [
  'Archeologist Faction' => './assets/images/faction-icon/archeologistFactionBackground.png',
  'Echo//Net' => './assets/images/faction-icon/echonetBackground.png',
  'Pillars of Sol' => "./assets/images/faction-icon/pillarsofsolBackground.png",
  'The Flock' => "./assets/images/faction-icon/flockBackground.png",
  'Neverwhere' => "./assets/images/faction-icon/neverwhereBackground.png"
];

$groupedCharacters = [];
foreach ($characterEntries as $entry) {
  $groupedCharacters[$entry['faction']][] = $entry;
}

foreach ($groupedCharacters as &$group) {
  usort($group, function ($a, $b) {
    return strcmp($a['title'], $b['title']);
  });
}
unset($group);

$selectedFaction = trim($_GET['faction'] ?? '');
if ($selectedFaction !== '' && isset($groupedCharacters[$selectedFaction])) {
  $displayGroups = [$selectedFaction => $groupedCharacters[$selectedFaction]];
} else {
  $displayGroups = $groupedCharacters;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($_page_title) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400&family=DM+Sans:wght@300;400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./assets/css/page.css"/>
</head>
<body>

<?php include './components/header-footer/header.php'; ?>

<main class="page-content">
  <h1>Characters</h1>
  <p>These figures shape the world of Eclipse through duty, relics, and old promises.</p>

  <div class="character-sections">
    <?php foreach ($displayGroups as $factionName => $characters): ?>
      <?php $background = $factionBackgrounds[$factionName] ?? ''; ?>
      <a href="<?= htmlspecialchars($_header_faction_page_map[$factionName] ?? '/characters.php') ?>" style="text-decoration: none;">
      <section class="faction-section" style="--bg-image: url('<?= htmlspecialchars($background) ?>');">
        <div class="faction-data">
          <h2><?= htmlspecialchars($factionName) ?></h2>
          <ul class="character-list">
            <?php foreach ($characters as $character): ?>
              <li class="character-list-item">
                <a href="./character-pages/<?= htmlspecialchars($character['slug']) ?>">
                  <?php if (!empty($character['image'])): ?>
                    <img src="<?= htmlspecialchars($character['image']) ?>" alt="<?= htmlspecialchars($character['title']) ?>" />
                  <?php endif; ?>
                  <span><?= htmlspecialchars($character['title']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
      </a>
    <?php endforeach; ?>
  </div>
</main>

<?php include './components/header-footer/footer.php'; ?>

<style>

  .character-sections {
    display: flex;
    flex-direction: column;
    gap: 0rem;
    margin-top: 1.5rem;
  }

 /*.faction-section {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    padding: 4rem;
    background-image:
        linear-gradient(
            rgba(10,10,10,0),
            rgba(10,10,10,1)
        ),
        var(--bg-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border: 1px solid rgba(255,255,255,.15);
    width: 100vw;
    margin-left: calc(50% - 50vw);
  }*/

  .faction-section {
    position: relative;
    overflow: hidden;
    background: #1d1d1d; /* Your page background */
    isolation: isolate;
    width: 100vw;
    margin-left: calc(50% - 50vw);
    padding: 4rem;
}

.faction-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background: var(--bg-image) center/cover no-repeat;
    opacity: 0.35;

    /* Fade the image itself */
    mask-image: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(0,0,0,0.75) 15%,
        black 35%,
        black 65%,
        rgba(0,0,0,0.75) 85%,
        transparent 100%
    );
    -webkit-mask-image: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(0,0,0,0.75) 15%,
        black 35%,
        black 65%,
        rgba(0,0,0,0.75) 85%,
        transparent 100%
    );

    z-index: -1;
}
  

  .faction-data{
    padding-left:20%;
  }

  .faction-section h2 {
    margin-bottom: 1rem;
    font-size: 1.1rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
  }

  .character-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 0.75rem;
  }

  .character-list-item a {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    color: inherit;
    text-decoration: none;
  }

  .character-list-item img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.15);
  }
</style>

</body>
</html>
