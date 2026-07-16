<?php
// ─── Load player data from JSON ────────────────────────────────────────────────
$jsonPath = $json_path ?? null;

if (file_exists($jsonPath)) {
    $data = json_decode(file_get_contents($jsonPath), true);
} else {
    die("Player JSON not found: " . $jsonPath);
}

$data = json_decode(file_get_contents($json_path), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error: Failed to parse player-data-joeschmoe.json — ' . json_last_error_msg());
}

$bio     = $data['bio']     ?? '';
$gallery = $data['gallery'] ?? [];

// ─── Detect video platform and return type + embed URL ───────────────────────
// Returns ['type' => 'iframe'|'video', 'src' => '...']
function video_embed(string $url): array {
    // YouTube: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
    if (preg_match('/(?:youtube\.com|youtu\.be)/', $url)) {
        if (preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return ['type' => 'iframe', 'src' =>
                'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1&playsinline=1&enablejsapi=1'];
        }
    }

    // Local file: anything without http/https is treated as a local path
    // Also catches explicit local extensions just in case
    if (!preg_match('/^https?:\/\//', $url) || preg_match('/\.(mp4|webm|ogg|mov)$/i', $url)) {
        return ['type' => 'video', 'src' => $url];
    }

    // Fallback — try as iframe
    return ['type' => 'iframe', 'src' => $url];
}
// ─────────────────────────────────────────────────────────────────────────────
$socials = $data['socials'] ?? [];
// ──────────────────────────────────────────────────────────────────────────────
?>

<div class="detail-page">

  <!-- Bio -->
  <section class="reveal">
    <p class="section-label">About</p>
    <p class="bio-text"><?= htmlspecialchars($bio) ?></p>
  </section>

  <!-- Gallery Carousel -->
  <section class="reveal">
    <p class="section-label">Gallery</p>
    <div class="carousel" id="carousel">

      <!-- Track -->
      <div class="carousel-track" id="carouselTrack">
        <?php foreach ($gallery as $i => $photo): ?>
        <div class="carousel-slide" data-index="<?= $i ?>">
          <?php $embed = video_embed($photo); ?>
          <?php if ($embed['type'] === 'video'): ?>
          <video
            src="<?= htmlspecialchars($embed['src']) ?>"
            loop playsinline preload="metadata"
            controls
          ></video>
          <?php else: ?>
          <iframe
            src="<?= htmlspecialchars($embed['src']) ?>"
            title="Gallery video <?= $i + 1 ?>"
            frameborder="0"
            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            <?php if (strpos($embed['src'], 'youtube.com') !== false): ?>data-yt-iframe<?php endif; ?>
          ></iframe>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Controls -->
      <div class="carousel-controls">
        <button class="carousel-btn" id="carouselPrev" aria-label="Previous">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M12.5 4L7 10L12.5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <span class="carousel-counter">
          <span id="carouselCurrent">1</span>
          <span class="carousel-counter-sep">/</span>
          <span id="carouselTotal"><?= count($gallery) ?></span>
        </span>

        <button class="carousel-btn" id="carouselNext" aria-label="Next">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M7.5 4L13 10L7.5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <!-- Pip indicators -->
      <div class="carousel-pips" id="carouselPips">
        <?php foreach ($gallery as $i => $photo): ?>
        <button class="carousel-pip <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" aria-label="Go to video <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- Socials -->
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

</div>

<script src="../assets/js/player.js"></script>