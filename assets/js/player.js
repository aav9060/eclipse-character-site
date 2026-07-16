// ── Scroll reveal ─────────────────────────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal, .reveal-stagger').forEach(el => observer.observe(el));

// ── Carousel ──────────────────────────────────────────────────────────────────
(function () {
  const track     = document.getElementById('carouselTrack');
  if (!track) return;

  const slides    = Array.from(track.querySelectorAll('.carousel-slide'));
  const pips      = Array.from(document.querySelectorAll('.carousel-pip'));
  const btnPrev   = document.getElementById('carouselPrev');
  const btnNext   = document.getElementById('carouselNext');
  const elCurrent = document.getElementById('carouselCurrent');
  const total     = slides.length;
  let current     = 0;

  // ── YouTube IFrame API ────────────────────────────────────────────────────
  // Map of iframe element → YT.Player instance
  const ytPlayers = new Map();
  let ytApiReady  = false;

  // Called by YouTube API once script loads
  window.onYouTubeIframeAPIReady = function () {
    ytApiReady = true;
    document.querySelectorAll('[data-yt-iframe]').forEach((iframe, idx) => {
      // Give each iframe a unique ID the API needs
      if (!iframe.id) iframe.id = 'yt-player-' + idx;
      const player = new YT.Player(iframe.id, {
        events: {
          onStateChange: (e) => {
            // YT.PlayerState.PLAYING = 1
            if (e.data === 1) stopAuto();
            // PAUSED = 2, ENDED = 0, CUED = 5
            if (e.data === 2 || e.data === 0 || e.data === 5) startAuto();
          }
        }
      });
      ytPlayers.set(iframe, player);
    });
  };

  // Inject the YouTube IFrame API script if there are any YT iframes on the page
  if (document.querySelector('[data-yt-iframe]')) {
    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(tag);
  }

  function isYouTubePlaying() {
    for (const player of ytPlayers.values()) {
      try {
        if (player.getPlayerState && player.getPlayerState() === 1) return true;
      } catch (e) {}
    }
    return false;
  }

  // ── Auto-rotation ─────────────────────────────────────────────────────────
  const AUTO_DELAY = 5000;
  let autoTimer    = null;

  function isAnyVideoPlaying() {
    // Check native <video> elements
    const video = slides[current].querySelector('video');
    if (video && !video.paused && !video.ended) return true;
    // Check YouTube players
    if (isYouTubePlaying()) return true;
    return false;
  }

  function startAuto() {
    stopAuto();
    autoTimer = setInterval(() => {
      if (!isAnyVideoPlaying()) goTo(current + 1, 'next');
    }, AUTO_DELAY);
  }

  function stopAuto() {
    if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
  }

  function resetAuto() {
    stopAuto();
    startAuto();
  }

  // ── Core navigation ───────────────────────────────────────────────────────
  function goTo(index, direction = 'next') {
    const prev = current;
    current = (index + total) % total;

    slides[prev].classList.remove('active');
    slides[prev].classList.add('prev');
    setTimeout(() => slides[prev].classList.remove('prev'), 500);

    slides[current].style.transform = direction === 'next' ? 'translateX(60px)' : 'translateX(-60px)';
    slides[current].classList.add('active');
    void slides[current].offsetWidth;
    slides[current].style.transform = '';

    pips.forEach((p, i) => p.classList.toggle('active', i === current));
    elCurrent.textContent = current + 1;
  }

  // ── Init ──────────────────────────────────────────────────────────────────
  slides[0].classList.add('active');
  startAuto();

  // ── Controls ──────────────────────────────────────────────────────────────
  btnNext.addEventListener('click', () => { goTo(current + 1, 'next'); resetAuto(); });
  btnPrev.addEventListener('click', () => { goTo(current - 1, 'prev'); resetAuto(); });

  pips.forEach((pip, i) => {
    pip.addEventListener('click', () => { goTo(i, i > current ? 'next' : 'prev'); resetAuto(); });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') { goTo(current + 1, 'next'); resetAuto(); }
    if (e.key === 'ArrowLeft')  { goTo(current - 1, 'prev'); resetAuto(); }
  });

  // ── Touch / swipe ─────────────────────────────────────────────────────────
  let touchStartX = 0;
  track.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });

  track.addEventListener('touchend', (e) => {
    const delta = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(delta) > 40) {
      delta > 0 ? goTo(current + 1, 'next') : goTo(current - 1, 'prev');
      resetAuto();
    }
  }, { passive: true });

  // ── Mouse drag (skipped on video slides) ──────────────────────────────────
  let dragStartX = 0;
  let isDragging = false;

  track.addEventListener('mousedown', (e) => {
    if (slides[current].querySelector('video')) return;
    dragStartX = e.clientX;
    isDragging = true;
  });

  track.addEventListener('mouseup', (e) => {
    if (!isDragging) return;
    isDragging = false;
    const delta = dragStartX - e.clientX;
    if (Math.abs(delta) > 40) {
      delta > 0 ? goTo(current + 1, 'next') : goTo(current - 1, 'prev');
      resetAuto();
    }
  });

  track.addEventListener('mouseleave', () => { isDragging = false; });

  // Native video play/pause events
  track.addEventListener('play',  () => stopAuto(), true);
  track.addEventListener('pause', () => startAuto(), true);
  track.addEventListener('ended', () => startAuto(), true);

})();