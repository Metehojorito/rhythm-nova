<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>RHYTHM NOVA</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet"/>
<style>
:root {
  --purple: #bc13fe; --cyan: #00f2ff; --gold: #ffcc00;
  --red: #ff3131;    --green: #00ff88; --bg: #05010a;
  --safe-bottom: env(safe-area-inset-bottom, 0px);
  --safe-top: env(safe-area-inset-top, 0px);
  --real-height: 100dvh;
}
* { margin:0; padding:0; box-sizing:border-box; }
body {
  background:var(--bg); color:#fff; font-family:'Rajdhani',sans-serif;
  height:var(--real-height); overflow:hidden; user-select:none;
  padding: var(--safe-top) 0 0 0;
}
#bg-canvas { position:fixed; inset:0; width:100%; height:100%; z-index:0; }
#app {
  position:relative; z-index:10; height:var(--real-height);
  display:flex; flex-direction:column; align-items:center;
  justify-content:space-between; padding:20px 0 16px;
  padding-bottom: max(16px, var(--safe-bottom));
}

/* ── HEADER ── */
header { text-align:center; }
.logo {
  font-family:'Orbitron',monospace; font-size:26px; font-weight:900;
  letter-spacing:8px; color:#fff; text-shadow:0 0 20px var(--purple),0 0 40px rgba(188,19,254,.3);
}
.logo span { color:var(--purple); }
.logo-line { width:120px; height:1px; background:linear-gradient(90deg,transparent,var(--purple),transparent); margin:6px auto 0; }

/* ── CAROUSEL ── */
#carousel-wrap {
  flex:1; width:100%; display:flex; align-items:center;
  justify-content:center; position:relative; overflow:hidden;
  min-height: 380px;
}
.nav-btn {
  position:absolute; z-index:30; width:42px; height:42px; border-radius:50%;
  border:1px solid rgba(255,255,255,.1); background:rgba(255,255,255,.04);
  backdrop-filter:blur(8px); color:#fff; font-size:20px; cursor:pointer;
  display:flex; align-items:center; justify-content:center; transition:all .2s;
}
.nav-btn:hover { background:rgba(188,19,254,.2); border-color:var(--purple); }
.nav-btn:disabled { opacity:.2; cursor:not-allowed; }
#btn-prev { left:16px; } #btn-next { right:16px; }

#carousel { position:relative; width:260px; height:370px; perspective:1000px; }

.song-card {
  position:absolute; width:240px; height:340px; left:50%; top:50%;
  border-radius:16px; background:rgba(255,255,255,.04); backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,.08); transition:all .42s cubic-bezier(.4,0,.2,1);
  transform-style:preserve-3d; overflow:hidden;
  display:flex; flex-direction:column; cursor:pointer;
}
/* Card positions */
.song-card[data-pos="0"] {
  transform:translate(-50%,-50%) translate3d(0,0,80px) scale(1.06);
  opacity:1; border-color:var(--purple);
  box-shadow:0 0 28px rgba(188,19,254,.35),0 20px 50px rgba(0,0,0,.6);
  z-index:10;
}
.song-card[data-pos="1"] {
  transform:translate(-50%,-50%) translate3d(215px,20px,-80px) rotateY(-18deg) scale(.87);
  opacity:.5; z-index:5; pointer-events:none;
}
.song-card[data-pos="-1"] {
  transform:translate(-50%,-50%) translate3d(-215px,20px,-80px) rotateY(18deg) scale(.87);
  opacity:.5; z-index:5; pointer-events:none;
}
/* Everything beyond ±1 is hidden — no pileup behind */
.song-card[data-pos="far"],
.song-card:not([data-pos="0"]):not([data-pos="1"]):not([data-pos="-1"]) {
  transform:translate(-50%,-50%) translate3d(0,0,-400px) scale(.3);
  opacity:0; z-index:0; pointer-events:none;
}

/* Card internals */
.card-cover {
  width:100%; height:155px; background-size:cover; background-position:center;
  flex-shrink:0; position:relative;
}
.card-cover-overlay {
  position:absolute; inset:0;
  background:linear-gradient(to bottom,transparent 40%,rgba(5,1,10,.95) 100%);
}
.card-cover-placeholder {
  width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:44px;
}
.card-body { padding:12px 14px; flex:1; display:flex; flex-direction:column; }
.card-title {
  font-family:'Orbitron',monospace; font-size:13px; font-weight:700; letter-spacing:1px;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:3px;
}
.card-artist { font-size:12px; color:rgba(255,255,255,.4); font-style:italic; margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.card-bpm { font-size:10px; color:var(--cyan); letter-spacing:2px; margin-bottom:8px; }
.card-diffs { display:flex; gap:4px; flex-wrap:wrap; }
.diff-pill {
  font-size:9px; font-weight:700; letter-spacing:1px; padding:2px 8px;
  border-radius:20px; border:1px solid; color:inherit;
}
.card-footer {
  padding:8px 14px; border-top:1px solid rgba(255,255,255,.06);
  display:flex; justify-content:space-between; align-items:center;
}
.card-score-label { font-size:9px; color:rgba(255,255,255,.3); letter-spacing:2px; }
.card-score { font-family:'Orbitron',monospace; font-size:13px; color:var(--purple); }
.card-rank { font-family:'Orbitron',monospace; font-size:22px; font-weight:900; font-style:italic; }

/* ── FOOTER ── */
footer { 
  width:100%; max-width:480px; padding:0 16px; 
  display:flex; flex-direction:column; align-items:center; gap:10px;
  box-sizing:border-box;
}

#diff-selector {
  display:flex; gap:5px; background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.08); border-radius:30px; padding:4px;
  min-height:auto; align-items:center; justify-content:center;
  flex-wrap:wrap;
}
.diff-btn {
  padding:7px 22px; border-radius:24px; border:none;
  background:transparent; color:rgba(255,255,255,.4);
  font-family:'Rajdhani',sans-serif; font-size:12px; font-weight:700;
  letter-spacing:2px; cursor:pointer; transition:all .2s;
}
.diff-btn.active { background:var(--purple); color:#fff; box-shadow:0 0 12px rgba(188,19,254,.5); }
.diff-no-songs { font-size:11px; color:rgba(255,255,255,.2); letter-spacing:2px; }

#start-btn {
  width:100%; padding:15px; border:none; border-radius:12px;
  background:linear-gradient(135deg,var(--purple),#7c00cc);
  color:#fff; font-family:'Orbitron',monospace; font-size:16px; font-weight:700;
  letter-spacing:4px; cursor:pointer; transition:all .2s;
  box-shadow:0 0 24px rgba(188,19,254,.4); animation:pulse-btn 2.5s ease-in-out infinite;
  position:relative; overflow:hidden;
  white-space:nowrap;
}
#start-btn::after { content:''; position:absolute; inset:0; background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent); transform:translateX(-100%); transition:.5s; }
#start-btn:hover::after { transform:translateX(100%); }
#start-btn:hover { transform:scale(1.02); box-shadow:0 0 40px rgba(188,19,254,.7); }
#start-btn:disabled { opacity:.35; cursor:not-allowed; animation:none; transform:none; }
@keyframes pulse-btn { 0%,100%{box-shadow:0 0 24px rgba(188,19,254,.4)} 50%{box-shadow:0 0 50px rgba(188,19,254,.8)} }

#visualizer { width:100%; height:22px; display:flex; align-items:flex-end; gap:2px; opacity:.25; }
.vbar { flex:1; background:var(--purple); border-radius:1px; transform-origin:bottom; animation:vbar 1.4s ease-in-out infinite; }
@keyframes vbar { 0%,100%{transform:scaleY(.1)} 50%{transform:scaleY(1)} }

/* ── STATES ── */
#loading-state, #empty-state {
  position:absolute; inset:0; display:flex; flex-direction:column;
  align-items:center; justify-content:center; gap:12px;
  color:rgba(255,255,255,.35); font-size:12px; letter-spacing:2px;
}
.spinner { width:32px; height:32px; border:2px solid rgba(188,19,254,.2); border-top-color:var(--purple); border-radius:50%; animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }

/* ── SPLASH (autoplay unlock) ── */
#splash {
  position:fixed; inset:0; z-index:100;
  display:flex; flex-direction:column; align-items:center; justify-content:center; gap:28px;
  background:var(--bg); cursor:pointer;
  transition: opacity .6s ease;
  height: var(--real-height);
  padding: var(--safe-top) 0 var(--safe-bottom) 0;
}
#splash.hide { opacity:0; pointer-events:none; }
.splash-logo {
  font-family:'Orbitron',monospace; font-size:38px; font-weight:900; letter-spacing:10px;
  color:#fff; text-shadow:0 0 30px var(--purple), 0 0 60px rgba(188,19,254,.3);
  text-align:center;
}
.splash-logo span { color:var(--purple); }
.splash-tap {
  font-family:'Orbitron',monospace; font-size:13px; letter-spacing:5px;
  color:rgba(255,255,255,.45); text-transform:uppercase;
  animation: tap-pulse 1.8s ease-in-out infinite;
}
@keyframes tap-pulse {
  0%,100% { opacity:.3; transform:scale(1); }
  50%      { opacity:1;  transform:scale(1.04); }
}
.splash-line {
  width:80px; height:1px;
  background:linear-gradient(90deg,transparent,var(--purple),transparent);
}

/* ── RESPONSIVE MEDIA QUERIES ── */
@media (max-width: 400px) {
  .nav-btn {
    width: 36px;
    height: 36px;
    font-size: 18px;
  }
  #btn-prev { left: 8px; }
  #btn-next { right: 8px; }
  .logo {
    font-size: 22px;
    letter-spacing: 6px;
  }
}

@media (max-width: 360px) {
  .song-card {
    width: 210px;
    height: 310px;
  }
  .card-cover {
    height: 130px;
  }
  .card-title {
    font-size: 12px;
  }
  .card-artist {
    font-size: 11px;
  }
  .card-bpm {
    font-size: 9px;
  }
  .diff-btn {
    padding: 6px 16px;
    font-size: 11px;
    letter-spacing: 1.5px;
  }
  #start-btn {
    padding: 13px;
    font-size: 14px;
    letter-spacing: 3px;
  }
}

@media (max-width: 340px) {
  .diff-btn {
    padding: 5px 12px;
    font-size: 10px;
    letter-spacing: 1px;
  }
}

@media (max-height: 600px) {
  .song-card {
    transform: scale(0.85);
  }
  footer {
    transform: scale(0.9);
  }
  #carousel-wrap {
    min-height: 320px;
  }
  #visualizer {
    height: 18px;
  }
  .logo {
    font-size: 20px;
  }
}

@media (max-height: 500px) {
  #carousel-wrap {
    min-height: 280px;
  }
  .song-card {
    transform: scale(0.75);
  }
  .splash-logo {
    font-size: 28px;
    letter-spacing: 8px;
  }
}

/* Soporte para orientación horizontal */
@media (orientation: landscape) and (max-height: 450px) {
  #app {
    flex-direction: row;
    padding: 10px;
  }
  header {
    width: 30%;
  }
  #carousel-wrap {
    width: 40%;
    min-height: auto;
  }
  footer {
    width: 30%;
    transform: scale(0.85);
  }
  .song-card {
    transform: scale(0.7);
  }
  #visualizer {
    display: none;
  }
}
</style>
</head>
<body>
<canvas id="bg-canvas"></canvas>

<!-- SPLASH — mandatory first gesture to unlock AudioContext -->
<div id="splash">
  <div class="splash-logo">RHYTHM <span>NOVA</span></div>
  <div class="splash-line"></div>
  <div class="splash-tap">Toca para comenzar</div>
</div>

<div id="app">
  <header>
    <div class="logo">RHYTHM <span>NOVA</span></div>
    <div class="logo-line"></div>
  </header>

  <div id="carousel-wrap">
    <button class="nav-btn" id="btn-prev" disabled>‹</button>
    <div id="carousel">
      <div id="loading-state"><div class="spinner"></div>Cargando canciones...</div>
      <div id="empty-state" style="display:none">🎵<br>No hay canciones disponibles<br><small style="font-size:10px;margin-top:8px;opacity:.5">Añade carpetas en /songs/</small></div>
    </div>
    <button class="nav-btn" id="btn-next" disabled>›</button>
  </div>

  <footer>
    <div id="diff-selector"><span class="diff-no-songs">—</span></div>
    <button id="start-btn" disabled>▶ &nbsp;START SESSION</button>
    <div id="visualizer"></div>
  </footer>
</div>

<script>
(function fixViewportHeight() {
  const setVH = () => {
    // Primero intentamos con dvh si es soportado
    if (CSS.supports('height', '100dvh')) {
      document.documentElement.style.setProperty('--real-height', '100dvh');
    } else {
      // Fallback para navegadores antiguos
      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
      document.documentElement.style.setProperty('--real-height', 'calc(var(--vh, 1vh) * 100)');
    }
  };
  
  setVH();
  window.addEventListener('resize', setVH);
  window.addEventListener('orientationchange', () => setTimeout(setVH, 100));
})();

const DIFF_COLORS = { easy:'#00ff88', normal:'#00f2ff', hard:'#ff3131' };
const DIFF_ORDER  = ['easy','normal','hard'];
const RANK_COLORS = { S:'#ffcc00', A:'#00f2ff', B:'#00ff88', C:'#bc13fe', D:'#ff3131' };

let songs = [], currentIdx = 0, selectedDiff = 'normal';

// ── Background ────────────────────────────────────────────────────────────────
const bgCanvas = document.getElementById('bg-canvas');
const bgCtx = bgCanvas.getContext('2d');
let bgOff = 0;
function drawBg() {
  // Usar window.innerHeight pero en realidad queremos usar dvh
  // Para el canvas de fondo, podemos mantener innerHeight ya que es solo fondo
  bgCanvas.width = window.innerWidth;
  bgCanvas.height = window.innerHeight;
  const W = bgCanvas.width, H = bgCanvas.height;
  const g = bgCtx.createRadialGradient(W/2,H*.4,0,W/2,H*.4,H);
  g.addColorStop(0,'#1a0535'); g.addColorStop(1,'#05010a');
  bgCtx.fillStyle=g; bgCtx.fillRect(0,0,W,H);
  bgOff=(bgOff+.3)%60;
  bgCtx.strokeStyle='rgba(188,19,254,0.06)'; bgCtx.lineWidth=1;
  for(let y=bgOff-60;y<H+60;y+=60){ bgCtx.beginPath(); bgCtx.moveTo(0,y); bgCtx.lineTo(W,y); bgCtx.stroke(); }
  for(let x=0;x<W;x+=80){ bgCtx.beginPath(); bgCtx.moveTo(x,0); bgCtx.lineTo(x,H); bgCtx.stroke(); }
}
(function loop(){ drawBg(); requestAnimationFrame(loop); })();

// Añadir listener para cuando cambie el viewport (ej. cuando se oculta la barra)
window.addEventListener('resize', () => {
  // Forzar recálculo de altura si es necesario
  document.documentElement.style.setProperty('--real-height', window.innerHeight + 'px');
});

// También escuchar el evento orientationchange
window.addEventListener('orientationchange', () => {
  setTimeout(() => {
    document.documentElement.style.setProperty('--real-height', window.innerHeight + 'px');
  }, 100);
});

// ── Load songs ────────────────────────────────────────────────────────────────
async function loadSongs() {
  try {
    const res  = await fetch('api/songs.php');
    const data = await res.json();
    songs = data.songs || [];
    document.getElementById('loading-state').style.display = 'none';

    if (!songs.length) {
      document.getElementById('empty-state').style.display = 'flex';
      return;
    }
    renderCarousel();
    buildDiffSelector();
    document.getElementById('btn-prev').disabled = songs.length < 2;
    document.getElementById('btn-next').disabled = songs.length < 2;
    document.getElementById('start-btn').disabled = false;
    // If audio is already unlocked (returning from game), start preview now
    if (audioUnlocked) schedulePreview();
  } catch(e) {
    document.getElementById('loading-state').innerHTML =
      '<span style="color:#ff3131">⚠ Error al conectar con la API</span>';
  }
}

// ── Carousel ──────────────────────────────────────────────────────────────────
function renderCarousel() {
  const el = document.getElementById('carousel');
  el.querySelectorAll('.song-card').forEach(c => c.remove());
  songs.forEach((song, i) => el.appendChild(makeCard(song, i)));
  updatePositions();
}

function makeCard(song, idx) {
  const card = document.createElement('div');
  card.className = 'song-card';
  card.dataset.idx = idx;

  // Cover
  const cover = document.createElement('div');
  cover.className = 'card-cover';
  if (song.cover) {
    cover.style.backgroundImage = `url(${song.cover})`;
  } else {
    cover.style.background = deterministicGradient(song.title);
    const ph = document.createElement('div');
    ph.className = 'card-cover-placeholder'; ph.textContent = '🎵';
    cover.appendChild(ph);
  }
  const overlay = document.createElement('div'); overlay.className='card-cover-overlay';
  cover.appendChild(overlay);

  // Diff pills
  const diffs = Object.entries(song.difficulties || {});
  const sorted = diffs.sort((a,b) => DIFF_ORDER.indexOf(a[0]) - DIFF_ORDER.indexOf(b[0]));
  const pillsHtml = sorted.map(([key,d]) =>
    `<span class="diff-pill" style="border-color:${DIFF_COLORS[key]||'#bc13fe'};color:${DIFF_COLORS[key]||'#bc13fe'}">${d.label} ${d.level}</span>`
  ).join('');

  // Best score overall
  let bestScore=null, bestRank=null;
  diffs.forEach(([,d]) => { if(d.bestScore!==null&&(bestScore===null||d.bestScore>bestScore)){ bestScore=d.bestScore; bestRank=d.bestRank; }});

  const body = document.createElement('div'); body.className='card-body';
  body.innerHTML = `
    <div class="card-title">${esc(song.title)}</div>
    <div class="card-artist">${esc(song.artist)}</div>
    <div class="card-bpm">${song.bpm} BPM · ${fmtDur(song.duration)}</div>
    <div class="card-diffs">${pillsHtml}</div>`;

  const footer = document.createElement('div'); footer.className='card-footer';
  footer.innerHTML = `
    <div>
      <div class="card-score-label">Mejor puntuación</div>
      <div class="card-score">${bestScore!==null ? bestScore.toString().padStart(6,'0') : '------'}</div>
    </div>
    <div class="card-rank" style="color:${RANK_COLORS[bestRank]||'rgba(255,255,255,.2)'}">${bestRank||'—'}</div>`;

  card.appendChild(cover); card.appendChild(body); card.appendChild(footer);
  card.addEventListener('click', () => { if(idx!==currentIdx){ currentIdx=idx; updatePositions(); buildDiffSelector(); }});
  return card;
}

function updatePositions() {
  const total = songs.length;
  document.querySelectorAll('.song-card').forEach(card => {
    const idx = parseInt(card.dataset.idx);
    let pos = idx - currentIdx;
    // Wrap around for circular navigation
    if (pos >  total / 2) pos -= total;
    if (pos < -total / 2) pos += total;
    // Only -1, 0, 1 are visible — everything else is 'far'
    card.dataset.pos = (Math.abs(pos) > 1) ? 'far' : pos.toString();
  });
}

// ── Difficulty selector ───────────────────────────────────────────────────────
function buildDiffSelector() {
  const wrap = document.getElementById('diff-selector');
  wrap.innerHTML = '';
  if (!songs.length) { wrap.innerHTML='<span class="diff-no-songs">—</span>'; return; }

  const song  = songs[currentIdx];
  const diffs = Object.keys(song.difficulties||{});
  const sorted= [...diffs].sort((a,b)=>DIFF_ORDER.indexOf(a)-DIFF_ORDER.indexOf(b));

  if (!sorted.includes(selectedDiff)) selectedDiff = sorted[0]||'normal';

  sorted.forEach(key => {
    const btn = document.createElement('button');
    btn.className = 'diff-btn' + (key===selectedDiff?' active':'');
    btn.textContent = song.difficulties[key].label;
    btn.dataset.diff = key;
    btn.addEventListener('click', () => {
      sfxDiffsel();
      selectedDiff = key;
      wrap.querySelectorAll('.diff-btn').forEach(b=>b.classList.toggle('active',b.dataset.diff===key));
    });
    wrap.appendChild(btn);
  });
}

// ══════════════════════════════════════════════════════════════════
//  SOUND SYSTEM
//  Un solo AudioContext para todo (UI + preview).
//  Pon archivos en assets/sounds/ para reemplazar los sonidos synth.
//  Nombres de archivo → keys en SOUND_FILES.
// ══════════════════════════════════════════════════════════════════
let AC = null;
function getAC() {
  if (!AC) AC = new (window.AudioContext || window.webkitAudioContext)();
  return AC;
}

const SFX = { navigate:null, diffsel:null, start:null };

// Intenta cargar un sonido probando ogg y mp3
async function tryLoadSound(baseName) {
  const ac = getAC();
  for (const ext of ['ogg', 'mp3']) {
    try {
      const res = await fetch(`assets/sounds/${baseName}.${ext}`);
      if (!res.ok) continue;
      const buf = await ac.decodeAudioData(await res.arrayBuffer());
      console.log(`✓ ${baseName}.${ext}`);
      return buf;
    } catch(e) {}
  }
  return null;
}

async function loadUISounds() {
  [SFX.navigate, SFX.diffsel, SFX.start] = await Promise.all([
    tryLoadSound('navigate'),
    tryLoadSound('diffsel'),
    tryLoadSound('start'),
  ]);
}

function playBuf(buf, vol=1) {
  const ac=getAC(), src=ac.createBufferSource(), g=ac.createGain();
  src.buffer=buf; g.gain.value=vol;
  src.connect(g); g.connect(ac.destination); src.start();
}

// ── Synth fallbacks ───────────────────────────────────────────────
function synthNavigate() {
  const ac=getAC(), t=ac.currentTime;
  const o=ac.createOscillator(), g=ac.createGain();
  o.type='sine';
  o.frequency.setValueAtTime(300,t); o.frequency.exponentialRampToValueAtTime(500,t+.07);
  g.gain.setValueAtTime(.22,t); g.gain.exponentialRampToValueAtTime(.001,t+.1);
  o.connect(g); g.connect(ac.destination); o.start(t); o.stop(t+.1);
}

function synthDiffsel() {
  const ac=getAC(), t=ac.currentTime;
  [440,660].forEach(freq => {
    const o=ac.createOscillator(), g=ac.createGain();
    o.type='triangle'; o.frequency.value=freq;
    g.gain.setValueAtTime(.18,t); g.gain.exponentialRampToValueAtTime(.001,t+.18);
    o.connect(g); g.connect(ac.destination); o.start(t); o.stop(t+.18);
  });
}

function synthStart() {
  const ac=getAC(), t=ac.currentTime;
  [220,330,440,660].forEach((freq,i) => {
    const o=ac.createOscillator(), g=ac.createGain();
    o.type='sawtooth';
    o.frequency.setValueAtTime(freq, t+i*.04);
    o.frequency.exponentialRampToValueAtTime(freq*1.5, t+i*.04+.15);
    g.gain.setValueAtTime(.16, t+i*.04);
    g.gain.exponentialRampToValueAtTime(.001, t+i*.04+.25);
    o.connect(g); g.connect(ac.destination);
    o.start(t+i*.04); o.stop(t+i*.04+.25);
  });
}

function sfxNavigate() { SFX.navigate ? playBuf(SFX.navigate, .7) : synthNavigate(); }
function sfxDiffsel()  { SFX.diffsel  ? playBuf(SFX.diffsel,  .7) : synthDiffsel();  }
function sfxStart()    { SFX.start    ? playBuf(SFX.start,    .9) : synthStart();    }

// ── Audio Preview ─────────────────────────────────────────────────
let previewSrc=null, previewGain=null;
let previewTimer=null, previewFadeTimer=null;
let previewLoadingFor=null, previewDebounce=null;
let audioUnlocked = false;

const PREVIEW_OFFSET=30, PREVIEW_SECONDS=15, FADE_SECONDS=2;

function getPreviewCtx() { return getAC(); }

function stopPreview() {
  clearTimeout(previewTimer); clearTimeout(previewFadeTimer);
  if (previewSrc) { try { previewSrc.stop(); } catch(e) {} previewSrc=null; }
  previewLoadingFor = null;
}

async function startPreview(song) {
  if (!song.audio || !audioUnlocked) return;
  stopPreview();
  const songId = song.id;
  previewLoadingFor = songId;
  try {
    const ac = getAC();
    if (ac.state==='suspended') await ac.resume();
    const res = await fetch(song.audio);
    if (!res.ok || previewLoadingFor!==songId) return;
    const arr = await res.arrayBuffer();
    if (previewLoadingFor!==songId) return;
    const buffer = await ac.decodeAudioData(arr);
    if (previewLoadingFor!==songId) return;

    previewGain = ac.createGain();
    previewGain.gain.setValueAtTime(0, ac.currentTime);
    previewGain.gain.linearRampToValueAtTime(0.75, ac.currentTime+.8);
    previewGain.connect(ac.destination);

    previewSrc = ac.createBufferSource();
    previewSrc.buffer = buffer;
    previewSrc.connect(previewGain);

    const offset = Math.min(PREVIEW_OFFSET, Math.max(0, buffer.duration-PREVIEW_SECONDS-2));
    previewSrc.start(0, offset);

    previewTimer = setTimeout(() => {
      if (!previewGain) return;
      const t = getAC().currentTime;
      previewGain.gain.setValueAtTime(previewGain.gain.value, t);
      previewGain.gain.linearRampToValueAtTime(0, t+FADE_SECONDS);
      previewFadeTimer = setTimeout(stopPreview, FADE_SECONDS*1000);
    }, (PREVIEW_SECONDS-FADE_SECONDS)*1000);

  } catch(e) { console.warn('Preview error:', e.message); }
}

function schedulePreview() {
  clearTimeout(previewDebounce); stopPreview();
  previewDebounce = setTimeout(() => {
    if (songs[currentIdx]) startPreview(songs[currentIdx]);
  }, 400);
}

// ── Navigation ────────────────────────────────────────────────────────────────
function navigate(dir) {
  if (!songs.length) return;
  currentIdx = (currentIdx + dir + songs.length) % songs.length;
  updatePositions();
  buildDiffSelector();
  schedulePreview();
}

document.getElementById('btn-prev').addEventListener('click', () => { audioUnlocked=true; sfxNavigate(); navigate(-1); });
document.getElementById('btn-next').addEventListener('click', () => { audioUnlocked=true; sfxNavigate(); navigate(1);  });
document.addEventListener('keydown', e => {
  if (e.code==='ArrowLeft')  { sfxNavigate(); navigate(-1); }
  if (e.code==='ArrowRight') { sfxNavigate(); navigate(1);  }
  if (e.code==='Enter' || e.code==='Space') { e.preventDefault(); goToGame(); }
});
let swipeX = 0;
document.getElementById('carousel-wrap').addEventListener('touchstart', e => { swipeX = e.touches[0].clientX; }, {passive:true});
document.getElementById('carousel-wrap').addEventListener('touchend', e => {
  const dx = e.changedTouches[0].clientX - swipeX;
  if (Math.abs(dx) > 40) { sfxNavigate(); navigate(dx < 0 ? 1 : -1); }
});

// ── Start game ────────────────────────────────────────────────────────────────
function goToGame() {
  if (!songs.length) return;
  sfxStart();
  stopPreview();
  const song = songs[currentIdx];
  // 1.5s delay so the start sound plays fully before navigating
  setTimeout(() => {
    window.location.href = `game.php?song=${encodeURIComponent(song.id)}&diff=${encodeURIComponent(selectedDiff)}`;
  }, 1500);
}
document.getElementById('start-btn').addEventListener('click', goToGame);

// ── Visualizer bars ───────────────────────────────────────────────────────────
const viz = document.getElementById('visualizer');
for(let i=0;i<48;i++){
  const b=document.createElement('div'); b.className='vbar';
  b.style.animationDelay=`${i*.04}s`;
  b.style.animationDuration=`${1.1+Math.random()*.9}s`;
  viz.appendChild(b);
}

// ── Utils ─────────────────────────────────────────────────────────────────────
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function fmtDur(ms){ const s=Math.floor(ms/1000); return `${Math.floor(s/60)}:${(s%60).toString().padStart(2,'0')}`; }
function deterministicGradient(title){
  let h=0; for(let i=0;i<title.length;i++) h=title.charCodeAt(i)+((h<<5)-h);
  const h1=Math.abs(h)%360, h2=(h1+45)%360;
  return `linear-gradient(135deg,hsl(${h1},75%,22%),hsl(${h2},85%,12%))`;
}

// ── Splash dismiss ────────────────────────────────────────────────────────────
// The browser requires an explicit user gesture before AudioContext can play.
// If coming back from the game page, the gesture already happened — skip splash.
const splash = document.getElementById('splash');
const fromGame = new URLSearchParams(window.location.search).get('from') === 'game';

function dismissSplash() {
  splash.removeEventListener('pointerdown', dismissSplash);
  splash.removeEventListener('keydown',     dismissSplash);
  getAC().resume();
  audioUnlocked = true;
  loadUISounds();   // load navigate/diffsel/start sounds
  splash.classList.add('hide');
  setTimeout(() => splash.style.display = 'none', 650);
  schedulePreview();
}

if (fromGame) {
  splash.style.display = 'none';
  audioUnlocked = true;
  getAC().resume();
  loadUISounds();
} else {
  splash.addEventListener('pointerdown', dismissSplash);
  splash.addEventListener('keydown',     dismissSplash);
}

loadSongs();
</script>
</body>
</html>
