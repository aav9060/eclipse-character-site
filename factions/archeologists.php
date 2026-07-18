<?php
require_once __DIR__ . '/../components/bio.php';

$_page_title = 'The Archeologist Faction';
$characterFiles = [
  ['slug' => 'lancelot.php', 'json' => 'lancelot.json'],
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
    'description' => $characterInfo['background'] ?? 'A new character profile is being written.',
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
  <link rel="stylesheet" href="../assets/css/page.css"/>
</head>
<body>
<?php include '../components/header-footer/header.php'; ?>
<main class="page-content">
  <h1>The Archeologist Faction</h1>
  <div class="character-list-view">
    <?php foreach ($characters as $character): ?>
      <a class="character-card" href="../character-pages/<?= htmlspecialchars($character['slug']) ?>">
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
<style>
  .character-list-view {
    display: grid;
    gap: 1rem;
  }

  .character-card {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 18px;
    background: rgba(255,255,255,0.04);
    color: inherit;
    text-decoration: none;
    transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
  }

  .character-card:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.22);
  }

  .character-card img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.18);
  }

  .character-card strong {
    display: block;
    font-family: 'DM Mono', monospace;
    font-size: 0.75rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    margin-bottom: 0.35rem;
  }

  .character-card p {
    margin: 0;
    color: rgba(255,255,255,0.78);
    font-size: 0.95rem;
  }
</style>
</body>
</html>
