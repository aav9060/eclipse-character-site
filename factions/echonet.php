<?php
require_once __DIR__ . '/../components/bio.php';

$_page_title = 'The Lantern Covenant';
$characterFiles = [
  ['slug' => 'astra.php', 'json' => 'astra.json'],
];

$characters = [];
foreach ($characterFiles as $characterFile) {
  $jsonPath = __DIR__ . '/../assets/json/' . $characterFile['json'];
  if (!file_exists($jsonPath)) {
    continue;
  }

  $data = json_decode(file_get_contents($jsonPath), true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    continue;
  }

  $characterInfo = $data['character_info'] ?? [];
  $characters[] = [
    'slug' => $characterFile['slug'],
    'title' => $characterInfo['nickname'] ?? $characterInfo['first_name'] ?? 'Character',
    'description' => $data['bio'] ?? 'A new character profile is being written.',
    'image' => $data['portrait_src'] ?? $data['avatar_src'] ?? '',
  ];
}

usort($characters, function ($a, $b) {
  return strcmp($a['title'], $b['title']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($_page_title) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400&family=DM+Sans:wght@300;400;600&display=swap" rel="stylesheet"/>
</head>
<body>
<?php include '../components/header-footer/header.php'; ?>
<main class="page-content">
  <h1>The Lantern Covenant</h1>
  <div class="character-list-view">
    <?php foreach ($characters as $character): ?>
      <a class="character-card" href="../player-pages/<?= htmlspecialchars($character['slug']) ?>">
        <?php if (!empty($character['image'])): ?>
          <img src="<?= htmlspecialchars($character['image']) ?>" alt="<?= htmlspecialchars($character['title']) ?>" />
        <?php endif; ?>
        <div>
          <strong><?= htmlspecialchars($character['title']) ?></strong>
          <p><?= format_bio_text($character['description']) ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</main>
<?php include '../components/header-footer/footer.php'; ?>
</body>
</html>
