<?php
// ─── Load player data from JSON ────────────────────────────────────────────────
// Detect current file (player1.php)
$current = basename(__FILE__);

// Map player file → json file
$map = [
    'joeschmoe8.php' => 'player-data-joeschmoe.json',
    'watrre.php' => 'player-data-watrre.json',
    'okdragon.php' => 'player-data-okdragon.json',
    'melon.php' => 'player-data-melon.json',
    'shadowhunter.php' => 'player-data-shadowhunter.json',
];

$json_file = $map[$current] ?? null;

if (!$json_file) {
    die('Error: No JSON mapped for this player page.');
}

$json_path = __DIR__ . '/../assets/json/' . $json_file;

if (!file_exists($json_path)) {
    die('Error: player-data-joeschmoe.json not found.');
}
$data = json_decode(file_get_contents($json_path), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error: Failed to parse player-data-joeschmoe.json — ' . json_last_error_msg());
}

$avatar_src = $data['avatar_src'] ?? '';
$bullets    = $data['bullets']    ?? [];

// ─── Build title and scroll text from player_info ─────────────────────────────
$parts       = $data['player_info'] ?? [];
$fn          = $parts['firstname'] ?? '';
$un          = $parts['username']  ?? '';
$ln          = $parts['lastname']  ?? '';
$emoji       = $data['flair']['emoji'] ?? '';
$color       = $data['flair']['color'] ?? '';
$title       = trim("$fn \"$un\" $ln $emoji");
$scroll_text = implode(' • ', array_filter(array_values($parts))) . ' •';
// ──────────────────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400&family=DM+Sans:wght@300;400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/css/background.css"/>
  <link rel="stylesheet" href="../assets/css/player.css"/>
</head>
<body>

<?php include '../components/header-footer/header.php'; ?>

<div class="scene">
  <div class="light-side"></div>
  <div class="text-layer">
    <div class="conveyor"></div>
  </div>
  <div class="profile-pin" id="profilePin">
    <img class="avatar" src="<?= htmlspecialchars($avatar_src) ?>" alt="Profile photo"/>
  </div>
  <div class="dark-content">
    <h1 class="main-title"><?= htmlspecialchars($title) ?></h1>
    <ul class="bullets">
      <?php foreach ($bullets as $bullet): ?>
      <li><?= htmlspecialchars($bullet) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Pass scroll text to JS, then run -->
<script>window.SCROLL_TEXT = <?= json_encode($scroll_text) ?>; window.PRIMARY_COLOR = "<?php echo $color; ?>";</script>
<script src="../assets/js/background.js"></script>

<?php include '../components/detail.php'; ?>

<?php include '../components/header-footer/footer.php'; ?>

</body>
</html>