<?php
$_page_title = 'Characters';
$characterFiles = [
  ['slug' => 'lancelot.php', 'json' => 'lancelot.json'],
  ['slug' => 'astra.php', 'json' => 'astra.json'],
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
      <section class="faction-section">
        <h2><?= htmlspecialchars($factionName) ?></h2>
        <ul class="character-list">
          <?php foreach ($characters as $character): ?>
            <li class="character-list-item">
              <a href="./player-pages/<?= htmlspecialchars($character['slug']) ?>">
                <?php if (!empty($character['image'])): ?>
                  <img src="<?= htmlspecialchars($character['image']) ?>" alt="<?= htmlspecialchars($character['title']) ?>" />
                <?php endif; ?>
                <span><?= htmlspecialchars($character['title']) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endforeach; ?>
  </div>
</main>

<?php include './components/header-footer/footer.php'; ?>

<style>
  .character-sections {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-top: 1.5rem;
  }

  .faction-section {
    border-top: 1px solid rgba(255,255,255,0.15);
    padding-top: 1.25rem;
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
