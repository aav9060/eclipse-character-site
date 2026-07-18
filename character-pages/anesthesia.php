<?php
$_header_init_only = true;
include '../components/header-footer/header.php';
$_header_init_only = false;
$bullets = $_header_bullets ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($_header_title ?: 'Eclipse Character Site') ?></title>
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
    <img class="avatar" src="<?= htmlspecialchars($_header_avatar_src ?: '../assets/images/placeholder.png') ?>" alt="Profile photo"/>
  </div>
  <div class="dark-content">
    <?php if (!empty($_header_title_image)): ?>
      <img class="character-title-image" src="<?= htmlspecialchars($_header_title_image) ?>" alt="Astra title" />
    <?php else: ?>
      <h1 class="main-title"><?= htmlspecialchars($_header_title) ?></h1>
    <?php endif; ?>
    <div class="character-name-meta">
      <div class="character-full-name"><?= htmlspecialchars(trim(implode(' ', array_filter([$_header_fn ?? '', $_header_ln ?? ''])))) ?></div>
      <div class="character-nickname"><?= htmlspecialchars($_header_un ?: '') ?></div>
    </div>
    <ul class="bullets">
      <?php foreach ($bullets as $bullet): ?>
      <li><?= htmlspecialchars($bullet) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<script>window.SCROLL_TEXT = <?= json_encode($_header_scroll_text) ?>; window.PRIMARY_COLOR = <?= json_encode($_header_color) ?>;</script>
<script src="../assets/js/background.js"></script>

<?php include '../components/detail.php'; ?>

<?php include '../components/header-footer/footer.php'; ?>

</body>
</html>
