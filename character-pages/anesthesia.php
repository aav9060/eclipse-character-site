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
   <div class="colored-side" style="background:<?= htmlspecialchars($_header_color) ?>;"></div>
  <div class="dark-content">
    <?php if (!empty($_header_title_image)): ?>
      <img class="character-title-image" src="<?= htmlspecialchars($_header_title_image) ?>" alt="Anesthesia title" />
    <?php else: ?>
      <h1 class="main-title"><?= htmlspecialchars($_header_title) ?></h1>
    <?php endif; ?>
    <div class="character-name-meta">
      <div class="character-full-name"><?= htmlspecialchars(trim(implode(' ', array_filter([$_header_fn ?? '', $_header_ln ?? ''])))) ?></div>
      <div class="character-nickname"><?= htmlspecialchars($_header_un ?: '') ?></div>
      <div class="character-voiceActor">EN VA: <?= htmlspecialchars($_header_va ?: '')?></div>
    </div>
    <div class="feature-frames">
      <div class="feature-frame"> 
        <div class="frame-image" style="border:3px solid <?= htmlspecialchars($_header_color) ?>;"> 
          <img src="../assets/images/role-icon/support.png" alt="Feature One"> 
        </div> 
        <div class="frame-title">Role</div> 
        <div class="frame-subtitle"><?= htmlspecialchars($_header_role) ?></div> 
      </div> 
      <div class="feature-frame"> 
        <div class="frame-image" style="border:3px solid <?= htmlspecialchars($_header_color) ?>;"> 
          <img src="../assets/images/faction-icon/archeology.png" alt="Feature Two"> 
        </div> 
        <div class="frame-title">Faction</div> 
        <div class="frame-subtitle"><?= htmlspecialchars($_header_faction_title) ?></div> 
      </div> <div class="feature-frame"> 
        <div class="frame-image" style="border:3px solid <?= htmlspecialchars($_header_color) ?>;"> 
          <img src="../assets/images/weapon-type/fist.png" alt="Feature Three"> 
        </div> 
        <div class="frame-title">Weapon Type</div> 
        <div class="frame-subtitle"><?= htmlspecialchars($_header_weapon_type) ?></div> 
      </div>
    </div>
   <ul class="bullets">
      <?php foreach ($bullets as $bullet): ?>
      <li><?= htmlspecialchars($bullet) ?></li>
      <?php endforeach; ?>
      </div>
    </ul>
  </div>
</div>

<script>window.SCROLL_TEXT = <?= json_encode($_header_scroll_text) ?>; window.PRIMARY_COLOR = <?= json_encode($_header_color) ?>;</script>
<script src="../assets/js/background.js"></script>

<?php include '../components/detail.php'; ?>

<?php include '../components/header-footer/footer.php'; ?>

</body>
</html>
