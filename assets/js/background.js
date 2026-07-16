const TRIANGLE_X = 0.55; // hypotenuse lands at 55% of viewport width

const durations = [22, 14, 28, 18, 11, 25, 16];
const opacities = [0.28, 0.13, 0.24, 0.11, 0.30, 0.15, 0.22];

// Words are injected by PHP into window.SCROLL_TEXT before this script runs
const words = (window.SCROLL_TEXT || 'lorem ipsum dolor sit amet').split(' ');

function hexToRgb(hex) {
  hex = hex.replace('#', '');
  const bigint = parseInt(hex, 16);
  return {
    r: (bigint >> 16) & 255,
    g: (bigint >> 8) & 255,
    b: bigint & 255
  };
}

const baseColor = window.PRIMARY_COLOR || '#000000';
const { r, g, b } = hexToRgb(baseColor);

function buildRow(index) {
  const row = document.createElement('div');
  row.className = 'text-row';
  row.style.animationDuration = durations[index % durations.length] + 's';

  const wordsPerHalf = 16;
  const offset = (index * 4) % words.length;
  const half = Array.from({length: wordsPerHalf}, (_, w) => words[(offset + w) % words.length]);
  const doubled = [...half, ...half];

  doubled.forEach(word => {
    const span = document.createElement('span');
    span.textContent = word;
    const alpha = opacities[index % opacities.length];
    span.style.color = `rgba(${r},${g},${b},${alpha})`;
    row.appendChild(span);
  });

  return row;
}

function rebuild() {
  const vw = window.innerWidth;
  const vh = window.innerHeight;
  const conveyor = document.querySelector('.conveyor');

  // Rotate conveyor to match hypotenuse angle
  const angle = Math.atan2(vh, TRIANGLE_X * vw) * (180 / Math.PI);
  conveyor.style.transform = `rotate(${angle}deg)`;

  // Rebuild rows to fill conveyor height
  conveyor.innerHTML = '';
  const probe = buildRow(0);
  probe.style.visibility = 'hidden';
  conveyor.appendChild(probe);
  const rowH = probe.offsetHeight || 60;
  conveyor.removeChild(probe);

  const count = Math.ceil(conveyor.offsetHeight / rowH) + 4;
  for (let i = 0; i < count; i++) conveyor.appendChild(buildRow(i));

  // Position profile pin at hypotenuse midpoint (27.5vw, 20vh)
  const pin = document.getElementById('profilePin');
  pin.style.left = (TRIANGLE_X / 2 * vw) + 'px';
  pin.style.top  = (0.20 * vh) + 'px';
}

rebuild();
window.addEventListener('resize', rebuild);