<?php
require_once __DIR__ . '/bio.php';

// ─── Load character data from JSON ───────────────────────────────────────────
$jsonPath = $json_path ?? $_header_json ?? null;

if (!$jsonPath || !file_exists($jsonPath)) {
    die("Character JSON not found: " . $jsonPath);
}

$data = json_decode(file_get_contents($jsonPath), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error: Failed to parse character data — ' . json_last_error_msg());
}

$bio     = $data['bio'] ?? '';
$gallery = $data['gallery'] ?? [];
$characterInfo = $data['character_info'] ?? $data['player_info'] ?? [];
$profession = $characterInfo['profession'] ?? $characterInfo['role'] ?? $data['profession'] ?? '';
$faction = $data['faction'] ?? $characterInfo['faction'] ?? '';
$background = $data['background'] ?? $characterInfo['background'] ?? '';
$power = $data['power'] ?? $characterInfo['power'] ?? '';
$associatedGod = $data['associated_god'] ?? $data['god'] ?? $characterInfo['associated_god'] ?? '';
$weapon = $data['weapon'] ?? $data['choice_of_weapon'] ?? $characterInfo['weapon'] ?? '';
$keyPoints = $data['key_points'] ?? [];
$relationships = $data['relationships'] ?? [];

function is_image_asset(string $url): bool {
    $url = trim($url);

    if ($url === '') {
        return false;
    }

    if (preg_match('/^data:image\//i', $url)) {
        return true;
    }

    return preg_match('/\.(jpg|jpeg|png|gif|webp|svg|avif|bmp|tif|tiff)$/i', $url) === 1;
}

$galleryImages = [];
foreach ($gallery as $asset) {
    if (is_image_asset((string) $asset)) {
        $galleryImages[] = (string) $asset;
    }
}

$socials = $data['socials'] ?? [];
?>

<div class="detail-page">

  <section class="reveal">
    <p class="section-label">About</p>
    <p class="bio-text"><?= format_bio_text($bio, 'A new character profile is being written for this legend.') ?></p>
  </section>

  <section class="reveal">
    <p class="section-label">Character Profile</p>
    <div class="socials reveal-stagger">
      <?php if ($profession): ?>
      <div class="social-link">
        <span class="social-platform">Profession</span>
        <span class="social-handle"><?= htmlspecialchars($profession) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($faction): ?>
      <div class="social-link">
        <span class="social-platform">Faction</span>
        <span class="social-handle"><?= htmlspecialchars($faction) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($background): ?>
      <div class="social-link">
        <span class="social-platform">Background</span>
        <span class="social-handle"><?= htmlspecialchars($background) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($power): ?>
      <div class="social-link">
        <span class="social-platform">Power</span>
        <span class="social-handle"><?= htmlspecialchars($power) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($associatedGod): ?>
      <div class="social-link">
        <span class="social-platform">Associated God</span>
        <span class="social-handle"><?= htmlspecialchars($associatedGod) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($weapon): ?>
      <div class="social-link">
        <span class="social-platform">Weapon</span>
        <span class="social-handle"><?= htmlspecialchars($weapon) ?></span>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="reveal">
    <p class="section-label">Relationships</p>
    <?php if (!empty($relationships)): ?>
    <div class="socials reveal-stagger">
      <?php foreach ($relationships as $relationship): ?>
      <?php $name = is_array($relationship) ? ($relationship['name'] ?? '') : (string)$relationship; ?>
      <?php $page = is_array($relationship) ? ($relationship['page'] ?? '') : ''; ?>
      <?php if ($name): ?>
      <a class="social-link" href="<?= htmlspecialchars($page ?: '#') ?>" target="_self">
        <span class="social-platform">Linked Character</span>
        <span class="social-handle"><?= htmlspecialchars($name) ?></span>
      </a>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="bio-text">No recorded relationships yet.</p>
    <?php endif; ?>
  </section>

  <section class="reveal">
    <p class="section-label">Key Points</p>
    <ul class="bullets">
      <?php foreach ($keyPoints as $point): ?>
      <li><?= htmlspecialchars($point) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <?php if (!empty($galleryImages)): ?>
  <section class="reveal">
    <p class="section-label">Gallery</p>
    <div class="carousel" id="carousel">
      <div class="carousel-track" id="carouselTrack">
        <?php foreach ($galleryImages as $i => $photo): ?>
        <div class="carousel-slide" data-index="<?= $i ?>">
          <img
            src="<?= htmlspecialchars($photo) ?>"
            alt="Gallery image <?= $i + 1 ?>"
            loading="lazy"
            class="gallery-image"
            data-image="<?= htmlspecialchars($photo) ?>"
          />
        </div>
        <?php endforeach; ?>
      </div>

      <div class="carousel-controls">
        <button class="carousel-btn" id="carouselPrev" aria-label="Previous">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M12.5 4L7 10L12.5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <span class="carousel-counter">
          <span id="carouselCurrent">1</span>
          <span class="carousel-counter-sep">/</span>
          <span id="carouselTotal"><?= count($galleryImages) ?></span>
        </span>

        <button class="carousel-btn" id="carouselNext" aria-label="Next">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M7.5 4L13 10L7.5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <div class="carousel-pips" id="carouselPips">
        <?php foreach ($galleryImages as $i => $photo): ?>
        <button class="carousel-pip <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" aria-label="Go to image <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <div class="image-lightbox" id="imageLightbox" aria-hidden="true">
    <button class="image-lightbox-close" id="imageLightboxClose" aria-label="Close image">×</button>
    <img id="imageLightboxImage" src="" alt="Expanded gallery image" />
  </div>

  <?php if (!empty($socials)): ?>
  <section class="reveal">
    <div class="socials reveal-stagger">
      <?php foreach ($socials as $s): ?>
      <a class="social-link" href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener">
        <span class="social-platform"><?= htmlspecialchars($s['platform']) ?></span>
        <span class="social-handle"><?= htmlspecialchars($s['handle']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</div>

<script src="../assets/js/player.js"></script>