<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>RHYTHM NOVA</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet"/>
<style>
:root { --purple:#bc13fe; --cyan:#00f2ff; --gold:#ffcc00; --red:#ff3131; --green:#00ff88; --bg:#050508; }
*{ margin:0; padding:0; box-sizing:border-box; }
body { background:var(--bg); overflow:hidden; height:100vh; width:100vw; font-family:'Rajdhani',sans-serif; color:#fff; user-select:none; touch-action:none; }

#bg-canvas { position:fixed; inset:0; width:100%; height:100%; z-index:0; }

/* ── LOADING ── */
#loading-screen {
  position:fixed; inset:0; z-index:80; background:var(--bg);
  display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px;
  transition: opacity .5s;
}
#loading-screen.fade-out { opacity:0; pointer-events:none; }
.spinner { width:40px; height:40px; border:2px solid rgba(188,19,254,.15); border-top-color:var(--purple); border-radius:50%; animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
#loading-song-name { font-family:'Orbitron',monospace; font-size:16px; letter-spacing:4px; color:var(--cyan); }
#loading-diff      { font-size:11px; letter-spacing:3px; color:rgba(255,255,255,.3); }
.load-bar-wrap { width:180px; height:2px; background:rgba(255,255,255,.06); border-radius:2px; overflow:hidden; }
.load-bar      { height:100%; width:0; background:linear-gradient(90deg,var(--purple),var(--cyan)); transition:width .3s; }

/* ── COUNTDOWN ── */
#countdown {
  position:fixed; inset:0; z-index:70;
  display:none; align-items:center; justify-content:center;
  background:rgba(5,5,8,.7); backdrop-filter:blur(6px);
}
#countdown.show { display:flex; }
#cd-number {
  font-family:'Orbitron',monospace; font-size:140px; font-weight:900; font-style:italic;
  color:#fff; text-shadow:0 0 40px var(--cyan),0 0 80px rgba(0,242,255,.4);
  animation:cd-pop .9s ease-out;
}
@keyframes cd-pop {
  0%  { transform:scale(1.8); opacity:0; }
  30% { transform:scale(.95); opacity:1; }
  80% { transform:scale(1); opacity:1; }
  100%{ transform:scale(.7); opacity:0; }
}
#cd-go {
  font-family:'Orbitron',monospace; font-size:80px; font-weight:900; letter-spacing:8px;
  color:var(--green); text-shadow:0 0 30px var(--green); display:none;
  animation:cd-pop .7s ease-out forwards;
}

/* ── GAME WRAP ── */
#game-wrap { position:relative; z-index:10; width:100%; height:100vh; display:flex; flex-direction:column; align-items:center; }

/* HUD */
#hud { width:100%; max-width:480px; display:flex; justify-content:space-between; align-items:flex-start; padding:14px 18px 0; pointer-events:none; }
.hud-block { display:flex; flex-direction:column; }
.hud-block.center { align-items:center; flex:1; }
.hud-block.right  { align-items:flex-end; }
.hud-label { font-size:9px; font-weight:700; letter-spacing:3px; color:var(--purple); text-transform:uppercase; margin-bottom:2px; }
#score-display { font-family:'Orbitron',monospace; font-size:26px; font-weight:900; letter-spacing:2px; text-shadow:0 0 10px rgba(255,255,255,.4); }
#song-title    { font-size:11px; font-family:'Orbitron',monospace; letter-spacing:3px; color:var(--cyan); opacity:.8; margin-bottom:4px; white-space:nowrap; overflow:hidden; max-width:180px; text-overflow:ellipsis; }
#diff-badge    { font-size:9px; letter-spacing:2px; color:rgba(255,255,255,.3); text-transform:uppercase; margin-bottom:4px; }
#progress-wrap { width:160px; height:2px; background:rgba(255,255,255,.08); border-radius:2px; overflow:hidden; }
#progress-bar  { height:100%; width:0%; background:linear-gradient(90deg,var(--purple),var(--cyan)); border-radius:2px; }
#combo-display { font-family:'Orbitron',monospace; font-size:36px; font-weight:900; font-style:italic; text-shadow:0 0 14px rgba(0,242,255,.5); line-height:1; }
#combo-display.pop { animation:combo-pop .15s ease-out; }
@keyframes combo-pop { 0%{transform:scale(1.3)} 100%{transform:scale(1)} }

/* LANE */
#lane-area { flex:1; position:relative; width:min(420px,100vw); max-width:480px; }
#lane-strip {
  position:absolute; inset:0;
  background:linear-gradient(to bottom,transparent,rgba(188,19,254,.04) 40%,rgba(0,242,255,.12) 85%,rgba(0,242,255,.2) 100%);
  border-left:1px solid rgba(255,255,255,.04); border-right:1px solid rgba(255,255,255,.04);
}
#lane-strip::after { content:''; position:absolute; inset:0; background:repeating-linear-gradient(to bottom,transparent 0,transparent 3px,rgba(0,0,0,.1) 3px,rgba(0,0,0,.1) 4px); }

/* Per-lane divider lines (injected by JS) */
.lane-divider { position:absolute; top:0; bottom:0; width:1px; background:rgba(255,255,255,.06); pointer-events:none; }

#note-canvas    { position:absolute; inset:0; width:100%; height:100%; }
#particle-canvas{ position:absolute; inset:0; width:100%; height:100%; pointer-events:none; }

/* Hit zone — contains N individual lane targets (injected by JS) */
#hit-zone { position:absolute; bottom:80px; left:0; right:0; height:4px; pointer-events:none; display:flex; }
.lane-hit-line {
  flex:1; height:2px; margin:0 6px; background:var(--cyan);
  box-shadow:0 0 10px var(--cyan),0 0 22px var(--cyan); border-radius:2px; position:relative;
}
.lane-target-ring {
  position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
  width:46px; height:46px; border-radius:50%; border:2px solid var(--cyan);
  box-shadow:0 0 12px var(--cyan),inset 0 0 12px rgba(0,242,255,.1);
  transition:transform .07s,box-shadow .07s;
}
.lane-target-ring.tapped { transform:translate(-50%,-50%) scale(1.3); box-shadow:0 0 28px var(--cyan),0 0 55px rgba(0,242,255,.35); }

/* HEALTH BAR - Actualizar los estilos existentes */
#health-wrap {
  width:100%; max-width:480px;
  padding:6px 18px 4px;
  pointer-events:none;
}
#health-track {
  width:100%; height:6px;
  background:rgba(255,255,255,.07);
  border-radius:3px; overflow:hidden;
  position:relative;
  transition: box-shadow 0.3s ease;
}
#health-track.full-glow {
  box-shadow: 0 0 20px var(--green), 0 0 40px rgba(0, 255, 136, 0.4);
  animation: pulse-glow-green 1.5s ease-in-out infinite;
}
#health-track.danger-glow {
  box-shadow: 0 0 15px var(--red), 0 0 30px rgba(255, 49, 49, 0.6);
  animation: pulse-glow-red 0.8s ease-in-out infinite;
}
#health-bar {
  height:100%; width:70%;
  border-radius:3px;
  transition:width .15s ease-out, background .4s;
  background:var(--green);
}
#health-bar.low    { background:#facc15; }
#health-bar.danger { background:var(--red); }
#health-bar.full   { 
  background: linear-gradient(90deg, var(--green), #7fff7f);
  box-shadow: 0 0 10px var(--green);
}
#health-bar.shaking {
  animation: shake-bar 0.12s ease-in-out infinite;
}

/* Animaciones nuevas */
@keyframes shake-bar {
  0% { transform: translateX(0) scaleX(1); }
  20% { transform: translateX(-3px) scaleX(1.03); }
  40% { transform: translateX(3px) scaleX(0.97); }
  60% { transform: translateX(-2px) scaleX(1.02); }
  80% { transform: translateX(2px) scaleX(0.98); }
  100% { transform: translateX(0) scaleX(1); }
}

@keyframes pulse-glow-green {
  0%, 100% { 
    box-shadow: 0 0 20px var(--green), 0 0 40px rgba(0, 255, 136, 0.4);
  }
  50% { 
    box-shadow: 0 0 30px var(--green), 0 0 60px rgba(0, 255, 136, 0.6);
  }
}

@keyframes pulse-glow-red {
  0%, 100% { 
    box-shadow: 0 0 15px var(--red), 0 0 30px rgba(255, 49, 49, 0.5);
  }
  50% { 
    box-shadow: 0 0 25px var(--red), 0 0 50px rgba(255, 49, 49, 0.8);
  }
}

/* Opcional: Efecto de pulso para el texto FULL HEALTH si quieres que combine */
#full-health {
  position:absolute; top:60px; left:50%; transform:translateX(-50%);
  font-family:'Orbitron',monospace; font-size:13px; letter-spacing:3px;
  color:var(--green); text-shadow:0 0 14px var(--green);
  opacity:0; pointer-events:none; transition:opacity .4s;
  white-space:nowrap;
}
#full-health.show { 
  opacity:1;
  animation: text-pulse-green 1.5s ease-in-out infinite;
}

@keyframes text-pulse-green {
  0%, 100% { text-shadow: 0 0 14px var(--green); }
  50% { text-shadow: 0 0 25px var(--green), 0 0 40px var(--green); }
}

/* FAILED screen */
#failed-screen {
  position:fixed; inset:0; z-index:75;
  display:none; flex-direction:column; align-items:center; justify-content:center; gap:18px;
  background:rgba(5,5,8,.85); backdrop-filter:blur(8px);
}
#failed-screen.show { display:flex; }
#failed-screen h2 {
  font-family:'Orbitron',monospace; font-size:52px; font-weight:900; letter-spacing:6px;
  color:var(--red); text-shadow:0 0 30px var(--red), 0 0 60px rgba(255,49,49,.4);
  animation:fail-pop .4s ease-out;
}
@keyframes fail-pop {
  0%  { transform:scale(1.6); opacity:0; }
  60% { transform:scale(.95); opacity:1; }
  100%{ transform:scale(1);   opacity:1; }
}
#failed-screen p { font-size:13px; color:rgba(255,255,255,.4); letter-spacing:2px; }
#full-health {
  position:absolute; top:60px; left:50%; transform:translateX(-50%);
  font-family:'Orbitron',monospace; font-size:13px; letter-spacing:3px;
  color:var(--green); text-shadow:0 0 14px var(--green);
  opacity:0; pointer-events:none; transition:opacity .4s;
  white-space:nowrap;
}
#full-health.show { opacity:1; }

/* Feedback */
#feedback {
  position:absolute; bottom:158px; left:50%; transform:translateX(-50%);
  font-family:'Orbitron',monospace; font-size:20px; font-weight:900; font-style:italic; letter-spacing:2px;
  pointer-events:none; opacity:0; white-space:nowrap; transition:opacity .12s;
}
#feedback.show { opacity:1; }
#feedback.perfect { color:var(--gold);  text-shadow:0 0 16px var(--gold); }
#feedback.good    { color:var(--cyan);  text-shadow:0 0 16px var(--cyan); }
#feedback.bad     { color:var(--red);   text-shadow:0 0 16px var(--red); }
#feedback.miss    { color:#555; }

/* Tap zone */
#tap-zone { position:fixed; inset:0; z-index:20; cursor:pointer; }

/* Back btn */
#back-btn {
  position:fixed; top:12px; left:12px; z-index:40; width:34px; height:34px;
  border-radius:50%; border:1px solid rgba(255,255,255,.1); background:rgba(0,0,0,.5);
  backdrop-filter:blur(6px); color:rgba(255,255,255,.5); font-size:14px;
  cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .2s;
}
#back-btn:hover { border-color:var(--red); color:var(--red); }

/* ── RESULTS ── */
#results {
  position:fixed; inset:0; z-index:60;
  background:rgba(5,5,8,.94); backdrop-filter:blur(18px);
  display:none; flex-direction:column; align-items:center; justify-content:center;
  padding:32px; text-align:center;
}
#results.show { display:flex; animation:fade-in .5s ease; }
@keyframes fade-in { from{opacity:0;transform:scale(.97)} to{opacity:1;transform:scale(1)} }
#results h2 { font-family:'Orbitron',monospace; font-size:32px; font-weight:900; color:var(--cyan); text-shadow:0 0 18px var(--cyan); letter-spacing:4px; margin-bottom:3px; }
.res-sub  { font-size:10px; letter-spacing:5px; color:rgba(255,255,255,.35); text-transform:uppercase; margin-bottom:3px; }
.res-diff { font-size:10px; letter-spacing:3px; color:rgba(255,255,255,.2); margin-bottom:22px; }
#new-best { font-size:10px; letter-spacing:3px; color:var(--gold); text-transform:uppercase; margin-bottom:8px; display:none; animation:pulse-gold 1s ease-in-out infinite; }
@keyframes pulse-gold { 0%,100%{opacity:.6} 50%{opacity:1} }
.result-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px 32px; margin-bottom:20px; width:260px; }
.result-item .rl { font-size:9px; letter-spacing:2px; color:rgba(255,255,255,.25); text-transform:uppercase; margin-bottom:3px; }
.result-item .rv { font-family:'Orbitron',monospace; font-size:20px; font-weight:700; }
#rank { font-family:'Orbitron',monospace; font-size:88px; font-weight:900; font-style:italic; line-height:1; width:120px; text-align:center; background:linear-gradient(135deg,#fff 30%,rgba(255,255,255,.15)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; filter:drop-shadow(0 0 24px rgba(255,255,255,.25)); margin-bottom:4px; }
#rank-label { font-size:10px; letter-spacing:4px; color:var(--gold); text-transform:uppercase; margin-bottom:28px; }
.res-btns { display:flex; gap:10px; }
.res-btn { padding:13px 28px; border-radius:50px; font-family:'Orbitron',monospace; font-size:11px; font-weight:700; letter-spacing:2px; cursor:pointer; transition:all .2s; }
#retry-btn { border:1px solid var(--purple); background:rgba(188,19,254,.1); color:#fff; }
#retry-btn:hover { background:rgba(188,19,254,.22); }
#menu-btn { border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04); color:rgba(255,255,255,.55); }
#menu-btn:hover { color:#fff; background:rgba(255,255,255,.1); }

/* Error screen */
#error-screen {
  position:fixed; inset:0; z-index:90; background:var(--bg);
  display:none; flex-direction:column; align-items:center; justify-content:center; gap:16px; text-align:center; padding:40px;
}
#error-screen.show { display:flex; }
#error-screen h2 { font-family:'Orbitron',monospace; font-size:18px; color:var(--red); letter-spacing:3px; }
#error-screen p  { font-size:13px; color:rgba(255,255,255,.4); max-width:300px; line-height:1.6; }
.err-back { padding:12px 32px; border:1px solid var(--purple); background:rgba(188,19,254,.1); color:#fff; font-family:'Orbitron',monospace; font-size:11px; letter-spacing:2px; border-radius:50px; cursor:pointer; text-decoration:none; }
</style>
</head>
<body>

<canvas id="bg-canvas"></canvas>

<!-- LOADING -->
<div id="loading-screen">
  <div id="loading-song-name">CARGANDO</div>
  <div id="loading-diff">—</div>
  <div class="load-bar-wrap"><div class="load-bar" id="load-bar"></div></div>
  <div class="spinner"></div>
</div>

<!-- COUNTDOWN -->
<div id="countdown">
  <div id="cd-number">3</div>
  <div id="cd-go">GO!</div>
</div>

<!-- FAILED -->
<div id="failed-screen">
  <h2>FAILED</h2>
  <p>La salud llegó a cero</p>
  <div style="display:flex;gap:10px;margin-top:8px;">
    <button onclick="retryGame()" class="res-btn" style="border:1px solid var(--purple);background:rgba(188,19,254,.1);color:#fff;">↺ &nbsp;RETRY</button>
    <a href="javascript:returnToMenu()" class="res-btn" style="border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;text-decoration:none;">☰ &nbsp;MENÚ</a>
  </div>
</div>

<!-- ERROR -->
<div id="error-screen">
  <h2>ERROR</h2>
  <p id="error-msg">No se pudo cargar la canción.</p>
  <a href="index.php" class="err-back">← Volver al menú</a>
</div>

<!-- BACK BTN -->
<button id="back-btn" onclick="returnToMenu()" title="Volver">←</button>

<!-- GAME -->
<div id="game-wrap">
  <div id="hud">
    <div class="hud-block">
      <span class="hud-label">Score</span>
      <div id="score-display">000000</div>
    </div>
    <div class="hud-block center">
      <div id="song-title">—</div>
      <div id="progress-wrap"><div id="progress-bar"></div></div>
      <div id="diff-badge">—</div>
    </div>
    <div class="hud-block right">
      <span class="hud-label">Combo</span>
      <div id="combo-display">0</div>
    </div>
  </div>

  <div id="health-wrap">
    <div id="health-track"><div id="health-bar"></div></div>
  </div>

  <div id="lane-area">
    <div id="lane-strip"></div>
    <canvas id="note-canvas"></canvas>
    <canvas id="particle-canvas"></canvas>
    <div id="hit-zone"><!-- lane-hit-lines injected by JS --></div>
    <div id="feedback"></div>
  </div>
</div>

<!-- TAP ZONE — divided into N lane zones by JS -->
<div id="tap-zone"></div>

<!-- RESULTS -->
<div id="results">
  <h2>COMPLETE</h2>
  <div class="res-sub"  id="res-title">—</div>
  <div class="res-diff" id="res-diff">—</div>
  <div id="new-best">★ NUEVO RÉCORD ★</div>
  <div id="full-health">💚 FULL HEALTH</div>
  <div class="result-grid">
    <div class="result-item"><div class="rl">Score</div><div class="rv" id="res-score">0</div></div>
    <div class="result-item"><div class="rl">Max Combo</div><div class="rv" id="res-combo">0</div></div>
    <div class="result-item"><div class="rl">Perfect</div><div class="rv" id="res-perfect" style="color:var(--gold)">0</div></div>
    <div class="result-item"><div class="rl">Miss</div><div class="rv" id="res-miss" style="color:var(--red)">0</div></div>
  </div>
  <div id="rank">—</div>
  <div id="rank-label">—</div>
  <div class="res-btns">
    <button class="res-btn" id="retry-btn">↺ &nbsp;RETRY</button>
    <button class="res-btn" id="menu-btn" onclick="returnToMenu()">☰ &nbsp;MENÚ</button>
  </div>
</div>

<script>
// ══════════════════════════════════════════
//  PARAMS
// ══════════════════════════════════════════
const params   = new URLSearchParams(window.location.search);
const SONG_ID  = params.get('song') || '';
const DIFF     = params.get('diff') || 'normal';

// ══════════════════════════════════════════
//  STATE
// ══════════════════════════════════════════
let CHART = null;
let score=0, combo=0, maxCombo=0, perfects=0, goods=0, bads=0, misses=0;
let notes=[], particles=[];
let isRunning=false, gameStartTime=null;

const NOTE_TRAVEL_MS = 1600;
const TIMING = { perfect:55, good:120, bad:220 };

// Number of lanes — read from CHART.lanes (1, 2 or 3). Default 1.
let LANE_COUNT = 1;

// Health bar
const HEALTH_START  = 70;   // starting %
const HEALTH_MAX    = 100;
const HEALTH_DELTA  = { perfect: 3, good: 1, bad: -8, miss: -15 };
let health = HEALTH_START;

// Track which lanes are currently held down (for hold notes)
const lanePressed = [false, false, false];

// Touch tracking per lane for flick detection
// touchOrigin[lane] = {x, time} at touchstart
const touchOrigin = [null, null, null];
const FLICK_THRESHOLD_PX  = 40;   // min horizontal travel
const FLICK_THRESHOLD_MS  = 300;  // max time to complete swipe

// Per-lane colors matching editor
const LANE_COLORS = ['#00f2ff', '#a855f7', '#10b981'];

// hitY is calculated dynamically from the actual DOM position of #hit-zone
let hitY = 0;

function recalcHitY() {
  const laneRect = document.getElementById('lane-area').getBoundingClientRect();
  const zoneRect = document.getElementById('hit-zone').getBoundingClientRect();
  hitY = (zoneRect.top - laneRect.top) + zoneRect.height / 2;
}

// Returns the X center of lane i (0-based) within the canvas width W
function laneX(i, W) {
  const laneW = W / LANE_COUNT;
  return laneW * i + laneW / 2;
}

// Builds the per-lane hit-lines inside #hit-zone
function buildHitZone() {
  const hz = document.getElementById('hit-zone');
  hz.innerHTML = '';
  for (let i = 0; i < LANE_COUNT; i++) {
    const div = document.createElement('div');
    div.className = 'lane-hit-line';
    div.style.borderColor = LANE_COLORS[i];
    div.style.boxShadow = `0 0 10px ${LANE_COLORS[i]}, 0 0 22px ${LANE_COLORS[i]}`;
    div.style.background = LANE_COLORS[i];
    const ring = document.createElement('div');
    ring.className = 'lane-target-ring';
    ring.id = `ring-${i}`;
    ring.style.borderColor = LANE_COLORS[i];
    ring.style.boxShadow = `0 0 12px ${LANE_COLORS[i]}, inset 0 0 12px ${LANE_COLORS[i]}40`;
    div.appendChild(ring);
    hz.appendChild(div);
  }
  // Lane dividers on the strip
  const strip = document.getElementById('lane-strip');
  strip.querySelectorAll('.lane-divider').forEach(d => d.remove());
  for (let i = 1; i < LANE_COUNT; i++) {
    const d = document.createElement('div');
    d.className = 'lane-divider';
    d.style.left = (100 / LANE_COUNT * i) + '%';
    strip.appendChild(d);
  }
  // Rebuild tap zone
  buildTapZone();
  recalcHitY();
}

// Divides the tap-zone into LANE_COUNT vertical sections
function buildTapZone() {
  const tz = document.getElementById('tap-zone');
  tz.innerHTML = '';
  for (let i = 0; i < LANE_COUNT; i++) {
    const zone = document.createElement('div');
    zone.style.cssText = `position:absolute;top:0;bottom:0;left:${(100/LANE_COUNT*i).toFixed(2)}%;width:${(100/LANE_COUNT).toFixed(2)}%;`;
    const lane = i;

    // ── MOUSE ──
    zone.addEventListener('mousedown', e => {
      touchOrigin[lane] = { x: e.clientX, time: performance.now() };
      handleTap(lane);
    });
    zone.addEventListener('mouseup', e => {
      const origin = touchOrigin[lane];
      if(origin){
        const dx = e.clientX - origin.x;
        const dt = performance.now() - origin.time;
        if(Math.abs(dx) >= FLICK_THRESHOLD_PX && dt <= FLICK_THRESHOLD_MS)
          handleFlick(lane, dx > 0 ? 'right' : 'left');
      }
      handleRelease(lane);
    });
    zone.addEventListener('mouseleave', () => handleRelease(lane));

    // ── TOUCH ──
    zone.addEventListener('touchstart', e => {
      e.preventDefault();
      const t = e.changedTouches[0];
      touchOrigin[lane] = { x: t.clientX, time: performance.now() };
      handleTap(lane);
    }, { passive: false });

    // touchmove: fire flick early if threshold crossed (responsive feel)
    zone.addEventListener('touchmove', e => {
      e.preventDefault();
      const origin = touchOrigin[lane];
      if(!origin) return;
      const t = e.changedTouches[0];
      const dx = t.clientX - origin.x;
      const dt = performance.now() - origin.time;
      if(Math.abs(dx) >= FLICK_THRESHOLD_PX && dt <= FLICK_THRESHOLD_MS) {
        handleFlick(lane, dx > 0 ? 'right' : 'left');
        touchOrigin[lane] = null; // consume — don't fire again on touchend
      }
    }, { passive: false });

    zone.addEventListener('touchend', e => {
      e.preventDefault();
      const origin = touchOrigin[lane];
      if(origin) {
        const t = e.changedTouches[0];
        const dx = t.clientX - origin.x;
        const dt = performance.now() - origin.time;
        if(Math.abs(dx) >= FLICK_THRESHOLD_PX && dt <= FLICK_THRESHOLD_MS)
          handleFlick(lane, dx > 0 ? 'right' : 'left');
      }
      handleRelease(lane);
    }, { passive: false });

    zone.addEventListener('touchcancel', e => { e.preventDefault(); handleRelease(lane); }, { passive: false });
    tz.appendChild(zone);
  }
}

// ══════════════════════════════════════════
//  AUDIO
// ══════════════════════════════════════════
let audioCtx=null, songBuf=null, songSrc=null;
// Hit sounds
let hitBuf=null, missBuf=null, badBuf=null;
// UI sounds — each countdown number has its own slot
let cd3Buf=null, cd2Buf=null, cd1Buf=null, goBuf=null;
// Results — needs a stoppable source
let resultsBuf=null, resultsSrc=null, resultsGain=null;

function getCtx() {
  if (!audioCtx) audioCtx = new (window.AudioContext||window.webkitAudioContext)();
  return audioCtx;
}

// Try ogg then mp3
async function tryLoadSound(base) {
  const ctx = getCtx();
  for (const ext of ['ogg','mp3']) {
    try {
      const res = await fetch(`${base}.${ext}`);
      if (!res.ok) continue;
      const buf = await ctx.decodeAudioData(await res.arrayBuffer());
      console.log(`✓ ${base}.${ext}`);
      return buf;
    } catch(e) {}
  }
  return null;
}

async function loadHitSounds() {
  [hitBuf, missBuf, badBuf] = await Promise.all([
    tryLoadSound('assets/sounds/hit'),
    tryLoadSound('assets/sounds/miss'),
    tryLoadSound('assets/sounds/bad'),
  ]);
}

async function loadGameUISounds() {
  [cd3Buf, cd2Buf, cd1Buf, goBuf, resultsBuf] = await Promise.all([
    tryLoadSound('assets/sounds/cd3'),
    tryLoadSound('assets/sounds/cd2'),
    tryLoadSound('assets/sounds/cd1'),
    tryLoadSound('assets/sounds/go'),
    tryLoadSound('assets/sounds/results'),
  ]);
}

function playBuffer(buf, volume=1) {
  const ctx=getCtx(), src=ctx.createBufferSource(), g=ctx.createGain();
  src.buffer=buf; g.gain.value=volume;
  src.connect(g); g.connect(ctx.destination); src.start();
}

// Stop the results jingle if playing (called on retry)
function stopResults() {
  if (resultsSrc) { try { resultsSrc.stop(); } catch(e) {} resultsSrc=null; }
  if (resultsGain) { resultsGain.disconnect(); resultsGain=null; }
}

function playSynth(type) {
  const ctx=getCtx(), now=ctx.currentTime;
  if (type==='perfect'||type==='good') {
    const len=ctx.sampleRate*.12, buf=ctx.createBuffer(1,len,ctx.sampleRate);
    const d=buf.getChannelData(0);
    for(let i=0;i<len;i++) d[i]=(Math.random()*2-1)*Math.pow(1-i/len,4);
    const src=ctx.createBufferSource(); src.buffer=buf;
    const filt=ctx.createBiquadFilter(); filt.type='bandpass'; filt.frequency.value=type==='perfect'?2200:1600; filt.Q.value=.8;
    const g=ctx.createGain(); g.gain.setValueAtTime(type==='perfect'?.9:.6,now); g.gain.exponentialRampToValueAtTime(.001,now+.12);
    src.connect(filt); filt.connect(g); g.connect(ctx.destination); src.start(now);
    if(type==='perfect'){
      const o=ctx.createOscillator(),g2=ctx.createGain();
      o.frequency.setValueAtTime(1400,now); o.frequency.exponentialRampToValueAtTime(2800,now+.08);
      g2.gain.setValueAtTime(.3,now); g2.gain.exponentialRampToValueAtTime(.001,now+.1);
      o.connect(g2); g2.connect(ctx.destination); o.start(now); o.stop(now+.1);
    }
  } else if (type==='bad') {
    // BAD: short downward buzz, distinct from miss
    const o=ctx.createOscillator(), g=ctx.createGain();
    o.type='sawtooth';
    o.frequency.setValueAtTime(320, now);
    o.frequency.exponentialRampToValueAtTime(160, now+.12);
    g.gain.setValueAtTime(.3, now); g.gain.exponentialRampToValueAtTime(.001, now+.13);
    o.connect(g); g.connect(ctx.destination); o.start(now); o.stop(now+.13);
  } else {
    // MISS: low sine drop
    const o=ctx.createOscillator(),g=ctx.createGain();
    o.type='sine';
    o.frequency.setValueAtTime(220,now);
    o.frequency.exponentialRampToValueAtTime(110,now+.15);
    g.gain.setValueAtTime(.2,now); g.gain.exponentialRampToValueAtTime(.001,now+.15);
    o.connect(g); g.connect(ctx.destination); o.start(now); o.stop(now+.15);
  }
}

function vibrate(ms) {
  if(navigator.vibrate) navigator.vibrate(ms);
}

// Actualizar la función updateHealth para manejar los nuevos estados
function updateHealth(delta) {
  health = Math.max(0, Math.min(HEALTH_MAX, health + delta));
  const bar = document.getElementById('health-bar');
  const track = document.getElementById('health-track');
  bar.style.width = health + '%';
  
  // Remover clases existentes
  bar.classList.remove('low', 'danger', 'full', 'shaking');
  track.classList.remove('full-glow', 'danger-glow');
  
  // Aplicar nuevas clases según el nivel de salud
  if (health >= HEALTH_MAX) {
    // Efecto GLOW cuando está al 100%
    bar.classList.add('full');
    track.classList.add('full-glow');
  } else if (health <= 25 && health > 0) {
    // Efecto TEMBLOR + GLOW ROJO cuando está por debajo del 25%
    bar.classList.add('danger', 'shaking');
    track.classList.add('danger-glow');
  } else if (health <= 50 && health > 25) {
    bar.classList.add('low');
  }
  
  if(health <= 0) triggerFailed();
}

function triggerFailed() {
  isRunning = false;
  stopSong();
  document.getElementById('failed-screen').classList.add('show');
}

function playHit(type) {
  if (type==='miss')    { missBuf ? playBuffer(missBuf,.7) : playSynth('miss'); return; }
  if (type==='bad')     { badBuf  ? playBuffer(badBuf, .7) : playSynth('bad');  return; }
  if (type==='perfect') { hitBuf  ? playBuffer(hitBuf, 1)  : playSynth('perfect'); vibrate(35); return; }
  if (type==='good')    { hitBuf  ? playBuffer(hitBuf, .7) : playSynth('good');    vibrate(15); return; }
}

// ── UI sounds ─────────────────────────────────────────────────────
// Synth countdown: pitch rises each number
function sfxCountdown(number) {
  const buf = number===3?cd3Buf : number===2?cd2Buf : cd1Buf;
  if (buf) { playBuffer(buf, 0.8); return; }
  const ctx=getCtx(), t=ctx.currentTime;
  const freq = number===3?220 : number===2?330 : 440;
  const vol  = number===3?.35 : number===2?.5  : .75;
  const dur  = 0.18;
  const o=ctx.createOscillator(), g=ctx.createGain();
  o.type='square'; o.frequency.value=freq;
  g.gain.setValueAtTime(vol,t); g.gain.exponentialRampToValueAtTime(.001,t+dur);
  o.connect(g); g.connect(ctx.destination); o.start(t); o.stop(t+dur);
  const o2=ctx.createOscillator(), g2=ctx.createGain();
  o2.type='sine'; o2.frequency.value=freq*2;
  g2.gain.setValueAtTime(vol*.4,t); g2.gain.exponentialRampToValueAtTime(.001,t+dur*.7);
  o2.connect(g2); g2.connect(ctx.destination); o2.start(t); o2.stop(t+dur);
}

function sfxGo() {
  if (goBuf) { playBuffer(goBuf, 0.9); return; }
  const ctx=getCtx(), t=ctx.currentTime;
  [440,550,660,880].forEach((freq,i) => {
    const o=ctx.createOscillator(), g=ctx.createGain();
    o.type='triangle';
    o.frequency.setValueAtTime(freq,t+i*.04);
    o.frequency.exponentialRampToValueAtTime(freq*1.2,t+i*.04+.15);
    g.gain.setValueAtTime(.5,t+i*.04); g.gain.exponentialRampToValueAtTime(.001,t+i*.04+.3);
    o.connect(g); g.connect(ctx.destination); o.start(t+i*.04); o.stop(t+i*.04+.3);
  });
}

function sfxResults(rank) {
  stopResults();   // clear any previous
  if (resultsBuf) {
    const ctx=getCtx();
    resultsGain = ctx.createGain(); resultsGain.gain.value=0.8;
    resultsSrc  = ctx.createBufferSource(); resultsSrc.buffer=resultsBuf;
    resultsSrc.connect(resultsGain); resultsGain.connect(ctx.destination);
    resultsSrc.start();
    resultsSrc.onended = () => { resultsSrc=null; resultsGain=null; };
    return;
  }
  // Synth fallback: melody per rank
  const ctx=getCtx(), t=ctx.currentTime;
  const melodies = {
    S:[[523,0],[659,.12],[784,.24],[1047,.36],[784,.52],[1047,.64]],
    A:[[440,0],[554,.14],[659,.28],[880,.42]],
    B:[[392,0],[494,.15],[587,.3]],
    C:[[330,0],[392,.18],[330,.36]],
    D:[[294,0],[262,.2]],
  };
  (melodies[rank]||melodies['C']).forEach(([freq,delay]) => {
    const o=ctx.createOscillator(), g=ctx.createGain();
    o.type='triangle'; o.frequency.setValueAtTime(freq,t+delay);
    g.gain.setValueAtTime(.45,t+delay); g.gain.exponentialRampToValueAtTime(.001,t+delay+.18);
    o.connect(g); g.connect(ctx.destination); o.start(t+delay); o.stop(t+delay+.2);
  });
}

async function loadAudio(url) {
  try {
    const ctx=getCtx();
    const res=await fetch(url);
    if(!res.ok) throw new Error('Audio not found');
    const arr=await res.arrayBuffer();
    songBuf=await ctx.decodeAudioData(arr);
  } catch(e) { console.warn('Audio no disponible:', e.message); songBuf=null; }
}

function playSong(offset=0) {
  if(!songBuf) return;
  const ctx=getCtx();
  if(ctx.state==='suspended') ctx.resume();
  if(songSrc){try{songSrc.stop();}catch(e){}}
  songSrc=ctx.createBufferSource();
  songSrc.buffer=songBuf;
  songSrc.connect(ctx.destination);
  songSrc.start(0, offset);
}

function stopSong() {
  if(songSrc){try{songSrc.stop();}catch(e){} songSrc=null;}
}

// ══════════════════════════════════════════
//  CANVAS
// ══════════════════════════════════════════
const bgCanvas   = document.getElementById('bg-canvas');
const noteCanvas = document.getElementById('note-canvas');
const partCanvas = document.getElementById('particle-canvas');
const bgCtx=bgCanvas.getContext('2d'), nCtx=noteCanvas.getContext('2d'), pCtx=partCanvas.getContext('2d');

function resize() {
  bgCanvas.width=window.innerWidth; bgCanvas.height=window.innerHeight;
  const r=document.getElementById('lane-area').getBoundingClientRect();
  noteCanvas.width=partCanvas.width=r.width;
  noteCanvas.height=partCanvas.height=r.height;
  recalcHitY();
}
window.addEventListener('resize', resize);
resize();

// ══════════════════════════════════════════
//  BACKGROUND
// ══════════════════════════════════════════
let bgOff=0;
function drawBg() {
  const W=bgCanvas.width,H=bgCanvas.height;
  bgCtx.clearRect(0,0,W,H);
  const g=bgCtx.createRadialGradient(W/2,H*.4,0,W/2,H*.4,H*.9);
  g.addColorStop(0,'#1a0535'); g.addColorStop(1,'#050508');
  bgCtx.fillStyle=g; bgCtx.fillRect(0,0,W,H);
  bgOff=(bgOff+.4)%60;
  bgCtx.strokeStyle='rgba(188,19,254,0.07)'; bgCtx.lineWidth=1;
  for(let y=bgOff-60;y<H+60;y+=60){bgCtx.beginPath();bgCtx.moveTo(0,y);bgCtx.lineTo(W,y);bgCtx.stroke();}
  for(let x=0;x<W;x+=80){bgCtx.beginPath();bgCtx.moveTo(x,0);bgCtx.lineTo(x,H);bgCtx.stroke();}
}
(function bgLoop(){drawBg();requestAnimationFrame(bgLoop);})();

// ══════════════════════════════════════════
//  NOTE DRAWING
// ══════════════════════════════════════════
function drawNote(x, y, alpha, missed, lane=0) {
  const r=18;
  const col = LANE_COLORS[Math.min(lane, LANE_COLORS.length-1)];
  // Parse hex color to rgba for gradients
  function hexRgb(hex, a) {
    const h = hex.replace('#','');
    const r=parseInt(h.slice(0,2),16), g=parseInt(h.slice(2,4),16), b=parseInt(h.slice(4,6),16);
    return `rgba(${r},${g},${b},${a})`;
  }
  nCtx.save(); nCtx.globalAlpha=missed?alpha*.25:alpha; nCtx.translate(x,y);
  if(!missed){
    const g=nCtx.createRadialGradient(0,0,r*.5,0,0,r*2.2);
    g.addColorStop(0, hexRgb(col,.3)); g.addColorStop(1, hexRgb(col,0));
    nCtx.beginPath(); nCtx.arc(0,0,r*2.2,0,Math.PI*2); nCtx.fillStyle=g; nCtx.fill();
  }
  nCtx.beginPath(); nCtx.moveTo(0,-r); nCtx.lineTo(r*.65,0); nCtx.lineTo(0,r); nCtx.lineTo(-r*.65,0); nCtx.closePath();
  if(missed){ nCtx.fillStyle='#1a1a1a'; nCtx.strokeStyle='#2a2a2a'; }
  else {
    const g=nCtx.createLinearGradient(0,-r,0,r);
    g.addColorStop(0,'#fff'); g.addColorStop(.4,col); g.addColorStop(1,'#bc13fe');
    nCtx.fillStyle=g; nCtx.shadowBlur=18; nCtx.shadowColor=col; nCtx.strokeStyle='rgba(255,255,255,.55)';
  }
  nCtx.lineWidth=1.5; nCtx.fill(); nCtx.stroke();
  if(!missed){
    nCtx.shadowBlur=0;
    nCtx.beginPath(); nCtx.moveTo(0,-r*.44); nCtx.lineTo(r*.27,0); nCtx.lineTo(0,r*.44); nCtx.lineTo(-r*.27,0); nCtx.closePath();
    nCtx.fillStyle='rgba(255,255,255,.65)'; nCtx.fill();
    nCtx.beginPath(); nCtx.arc(-r*.17,-r*.5,2.2,0,Math.PI*2); nCtx.fillStyle='#fff'; nCtx.fill();
  }
  nCtx.restore();
}

// ══════════════════════════════════════════
//  FLICK NOTE DRAWING
// ══════════════════════════════════════════
function drawFlickNote(x, y, alpha, missed, direction) {
  const isLeft = direction !== 'right';
  const col1 = isLeft ? '#f97316' : '#10b981';
  const col2 = isLeft ? '#ec4899' : '#06b6d4';
  const r = 18;
  const ext = r * 0.4;
  const dir = isLeft ? -1 : 1;
  nCtx.save();
  nCtx.globalAlpha = missed ? alpha * 0.25 : alpha;
  nCtx.translate(x, y);
  if(!missed){
    const g = nCtx.createRadialGradient(0,0,r*.5,0,0,r*2.4);
    g.addColorStop(0, isLeft ? 'rgba(249,115,22,0.35)' : 'rgba(16,185,129,0.35)');
    g.addColorStop(1, 'rgba(0,0,0,0)');
    nCtx.beginPath(); nCtx.arc(0,0,r*2.4,0,Math.PI*2); nCtx.fillStyle=g; nCtx.fill();
    const eg = nCtx.createLinearGradient(0,-r,0,r);
    eg.addColorStop(0, col1); eg.addColorStop(1, col2);
    const offsetX = dir * ext;
    const halfWidth = r * 0.65;
    nCtx.lineCap = 'round';
    nCtx.lineJoin = 'round';
    nCtx.shadowBlur = 12; nCtx.shadowColor = col2;
    nCtx.strokeStyle = 'rgba(255,255,255,0.9)';
    nCtx.lineWidth = 5;
    nCtx.beginPath();
    nCtx.moveTo(offsetX, -r);
    nCtx.lineTo(offsetX + (dir * halfWidth), 0);
    nCtx.lineTo(offsetX, r);
    nCtx.stroke();
    nCtx.strokeStyle = eg;
    nCtx.lineWidth = 3;
    nCtx.beginPath();
    nCtx.moveTo(offsetX, -r);
    nCtx.lineTo(offsetX + (dir * halfWidth), 0);
    nCtx.lineTo(offsetX, r);
    nCtx.stroke();
    nCtx.shadowBlur = 0;
  }
  const dg = nCtx.createLinearGradient(0,-r,0,r);
  if(missed){
    nCtx.fillStyle = '#1a1a1a'; nCtx.strokeStyle = '#2a2a2a';
  } else {
    dg.addColorStop(0,'#fff'); dg.addColorStop(.4, col1); dg.addColorStop(1, col2);
    nCtx.fillStyle = dg;
    nCtx.strokeStyle = 'rgba(255,255,255,.55)';
    nCtx.shadowBlur = 16; nCtx.shadowColor = col1;
  }
  nCtx.lineWidth = 1.5;
  nCtx.beginPath(); nCtx.moveTo(0,-r); nCtx.lineTo(r*.65,0); nCtx.lineTo(0,r); nCtx.lineTo(-r*.65,0); nCtx.closePath();
  nCtx.fill(); nCtx.stroke();
  nCtx.shadowBlur = 0;
  if(!missed){
    nCtx.fillStyle = 'rgba(255,255,255,.6)';
    nCtx.beginPath(); nCtx.moveTo(0,-r*.44); nCtx.lineTo(r*.27,0); nCtx.lineTo(0,r*.44); nCtx.lineTo(-r*.27,0); nCtx.closePath(); nCtx.fill();
  }
  nCtx.restore();
}

// ══════════════════════════════════════════
//  HOLD NOTE DRAWING
// ══════════════════════════════════════════
function drawHoldNote(x, yHead, yTail, alpha, lane, holdProgress, isActive) {
  const col = LANE_COLORS[Math.min(lane, LANE_COLORS.length-1)];
  function hexRgb(hex, a) {
    const h=hex.replace('#','');
    return `rgba(${parseInt(h.slice(0,2),16)},${parseInt(h.slice(2,4),16)},${parseInt(h.slice(4,6),16)},${a})`;
  }
  const bw = 22; // hold bar width
  nCtx.save();
  nCtx.globalAlpha = alpha;

  // Tail cap (end of hold, top)
  const yTop = Math.min(yHead, yTail);
  const yBot = Math.max(yHead, yTail);
  const bodyH = yBot - yTop;

  // Body background
  nCtx.fillStyle = hexRgb(col, 0.18);
  nCtx.beginPath(); nCtx.roundRect(x - bw/2, yTop, bw, bodyH, 4); nCtx.fill();

  // Progress fill (consumed portion from head downward)
  if(isActive && holdProgress > 0) {
    const fillH = bodyH * holdProgress;
    const grad = nCtx.createLinearGradient(0, yTop, 0, yTop + fillH);
    grad.addColorStop(0, hexRgb(col, 0.7));
    grad.addColorStop(1, hexRgb(col, 0.3));
    nCtx.fillStyle = grad;
    nCtx.beginPath(); nCtx.roundRect(x - bw/2, yTop, bw, fillH, 4); nCtx.fill();
  }

  // Border
  nCtx.strokeStyle = hexRgb(col, isActive ? 0.9 : 0.45);
  nCtx.lineWidth = isActive ? 2 : 1;
  nCtx.beginPath(); nCtx.roundRect(x - bw/2, yTop, bw, bodyH, 4); nCtx.stroke();

  // Tail end cap line
  nCtx.strokeStyle = hexRgb(col, 0.6);
  nCtx.lineWidth = 2;
  nCtx.beginPath(); nCtx.moveTo(x - bw/2 - 3, yTop); nCtx.lineTo(x + bw/2 + 3, yTop); nCtx.stroke();

  // Head diamond
  nCtx.globalAlpha = alpha;
  const r = 18;
  if(!isActive) {
    // Glow halo
    const g = nCtx.createRadialGradient(x, yHead, r*.5, x, yHead, r*2.2);
    g.addColorStop(0, hexRgb(col,.3)); g.addColorStop(1, hexRgb(col,0));
    nCtx.beginPath(); nCtx.arc(x, yHead, r*2.2, 0, Math.PI*2); nCtx.fillStyle=g; nCtx.fill();
  }
  nCtx.translate(x, yHead);
  const dg = nCtx.createLinearGradient(0,-r,0,r);
  dg.addColorStop(0,'#fff'); dg.addColorStop(.4,col); dg.addColorStop(1,'#bc13fe');
  nCtx.fillStyle = isActive ? col : dg;
  nCtx.shadowBlur = isActive ? 24 : 18;
  nCtx.shadowColor = col;
  nCtx.strokeStyle = 'rgba(255,255,255,.55)';
  nCtx.lineWidth = 1.5;
  nCtx.beginPath(); nCtx.moveTo(0,-r); nCtx.lineTo(r*.65,0); nCtx.lineTo(0,r); nCtx.lineTo(-r*.65,0); nCtx.closePath();
  nCtx.fill(); nCtx.stroke();
  nCtx.shadowBlur = 0;
  // Inner shine
  nCtx.fillStyle = 'rgba(255,255,255,.65)';
  nCtx.beginPath(); nCtx.moveTo(0,-r*.44); nCtx.lineTo(r*.27,0); nCtx.lineTo(0,r*.44); nCtx.lineTo(-r*.27,0); nCtx.closePath(); nCtx.fill();
  nCtx.restore();
}

// ══════════════════════════════════════════
//  PARTICLES
// ══════════════════════════════════════════
function spawnParticles(x, y, type, lane=0) {
  const laneCol = LANE_COLORS[Math.min(lane, LANE_COLORS.length-1)];
  const count=type==='perfect'?22:type==='good'?13:5;
  const colors=type==='perfect'?['#ffcc00','#fff','#ffee77',laneCol,'#bc13fe']:type==='good'?[laneCol,'#fff','#9af']:['#ff3131','#ff7755'];
  for(let i=0;i<count;i++){
    const a=(Math.PI*2/count)*i+Math.random()*.4, s=1.5+Math.random()*3.5;
    particles.push({x,y,vx:Math.cos(a)*s,vy:Math.sin(a)*s-(type==='perfect'?2:1),alpha:1,size:3+Math.random()*5,color:colors[Math.floor(Math.random()*colors.length)],shape:['diamond','circle','cross'][~~(Math.random()*3)],rot:Math.random()*Math.PI*2,rotV:(Math.random()-.5)*.2,decay:.025+Math.random()*.02});
  }
  if(type==='perfect'){
    particles.push({type:'ring',x,y,r:0,alpha:1,color:laneCol});
    particles.push({type:'ring',x,y,r:0,alpha:1,color:'#ffcc00'});
  }
}

// Trickle: 2-3 small particles per frame while hold is active
function spawnHoldParticle(x, y, lane) {
  const col = LANE_COLORS[Math.min(lane, LANE_COLORS.length-1)];
  const count = 2 + Math.floor(Math.random()*2);
  for(let i=0;i<count;i++){
    const a = -Math.PI/2 + (Math.random()-.5)*1.2; // mostly upward
    const s = 0.5 + Math.random()*1.8;
    particles.push({
      x: x + (Math.random()-.5)*14,
      y,
      vx: Math.cos(a)*s, vy: Math.sin(a)*s,
      alpha: 0.7 + Math.random()*0.3,
      size: 2 + Math.random()*3,
      color: Math.random()<0.5 ? col : '#fff',
      shape: Math.random()<0.6 ? 'circle' : 'diamond',
      rot: Math.random()*Math.PI*2,
      rotV: (Math.random()-.5)*.15,
      decay: 0.04 + Math.random()*0.03
    });
  }
}

function updateParticles() {
  pCtx.clearRect(0,0,partCanvas.width,partCanvas.height);
  particles=particles.filter(p=>p.alpha>.01);
  particles.forEach(p=>{
    pCtx.save(); pCtx.globalAlpha=p.alpha;
    if(p.type==='ring'){
      pCtx.beginPath(); pCtx.arc(p.x,p.y,p.r,0,Math.PI*2); pCtx.strokeStyle=p.color; pCtx.lineWidth=2; pCtx.stroke();
      p.r+=3; p.alpha-=.055;
    } else {
      pCtx.translate(p.x,p.y); pCtx.rotate(p.rot);
      if(p.shape==='diamond'){pCtx.beginPath();pCtx.moveTo(0,-p.size);pCtx.lineTo(p.size*.6,0);pCtx.lineTo(0,p.size);pCtx.lineTo(-p.size*.6,0);pCtx.closePath();pCtx.fillStyle=p.color;pCtx.fill();}
      else if(p.shape==='circle'){pCtx.beginPath();pCtx.arc(0,0,p.size*.5,0,Math.PI*2);pCtx.fillStyle=p.color;pCtx.fill();}
      else{pCtx.strokeStyle=p.color;pCtx.lineWidth=2;pCtx.beginPath();pCtx.moveTo(-p.size,0);pCtx.lineTo(p.size,0);pCtx.moveTo(0,-p.size);pCtx.lineTo(0,p.size);pCtx.stroke();}
      p.x+=p.vx; p.y+=p.vy; p.vy+=.12; p.rot+=p.rotV; p.alpha-=p.decay;
    }
    pCtx.restore();
  });
}

// ══════════════════════════════════════════
//  GAME LOOP
// ══════════════════════════════════════════
function gameLoop() {
  if (!isRunning) return;
  const elapsed = performance.now() - gameStartTime;
  document.getElementById('progress-bar').style.width = Math.min(elapsed/CHART.duration*100,100)+'%';
  nCtx.clearRect(0,0,noteCanvas.width,noteCanvas.height);
  updateParticles();

  const lH = noteCanvas.height;
  const lW = noteCanvas.width;

  notes.forEach(note=>{
    if(note.hit) return;
    const ttH  = note.time - elapsed;          // ms until head hits line
    const yHead = hitY - (ttH/NOTE_TRAVEL_MS)*lH;
    const x     = laneX(note.lane, lW);
    const alpha = ttH>NOTE_TRAVEL_MS*.9 ? 1-(ttH-NOTE_TRAVEL_MS*.9)/(NOTE_TRAVEL_MS*.1) : 1;

    // ── HOLD NOTE ──
    if(note.type === 'hold') {
      const endTime  = note.time + note.duration;
      const ttEnd    = endTime - elapsed;
      const yTail    = hitY - (ttEnd/NOTE_TRAVEL_MS)*lH;  // end of hold body
      const isActive = note.holdActive || false;
      const progress = note.holdProgress || 0;

      // Miss: head passed too late without press
      if(!note.missed && !note.holdStarted && ttH < -TIMING.bad) {
        note.missed = true; misses++; combo=0;
        updateHealth(HEALTH_DELTA.miss);
        updateCombo(); showFeedback('MISS','miss'); playHit('miss');
      }

      // Active hold: advance progress, spawn particles, check release
      if(note.holdActive) {
        const holdElapsed = elapsed - note.holdStartTime;
        note.holdProgress = Math.min(1, holdElapsed / note.duration);
        // Continuous particles at head position (clamped to hit zone)
        spawnHoldParticle(x, hitY, note.lane);

        // Check if hold completed naturally
        if(elapsed >= endTime) {
          note.hit = true; note.holdActive = false;
          resolveHold(note, 'completed');
          return;
        }
        // Check if finger lifted before end
        if(!lanePressed[note.lane]) {
          note.hit = true; note.holdActive = false;
          resolveHold(note, 'early');
          return;
        }
      }

      // Draw hold (head not yet hit line — show full body falling)
      if(!note.missed && !note.hit) {
        const visYHead = isActive ? hitY : yHead;
        if(visYHead > -40 && yTail < lH + 40)
          drawHoldNote(x, visYHead, yTail, Math.max(0,alpha), note.lane, progress, isActive);
      }
      return;
    }

    // ── FLICK NOTE ──
    if(note.type === 'flick') {
      if(!note.missed && ttH < -TIMING.bad){
        note.missed=true; misses++; combo=0;
        updateHealth(HEALTH_DELTA.miss);
        updateCombo(); showFeedback('MISS','miss'); playHit('miss');
      }
      if(yHead>-40 && yHead<lH+40) drawFlickNote(x, yHead, Math.max(0,alpha), note.missed, note.direction||'left');
      return;
    }

    // ── NORMAL NOTE ──
    if(!note.missed && ttH < -TIMING.bad){
      note.missed=true; misses++; combo=0;
      updateHealth(HEALTH_DELTA.miss);
      updateCombo(); showFeedback('MISS','miss'); playHit('miss');
    }
    if(yHead>-40 && yHead<lH+40) drawNote(x, yHead, Math.max(0,alpha), note.missed, note.lane);
  });

  if(elapsed > CHART.duration+1800){ endGame(); return; }
  requestAnimationFrame(gameLoop);
}

// Evaluate hold release
const HOLD_TIMING = { perfect: 150, good: 350 }; // ms early/late tolerance
function resolveHold(note, reason) {
  combo++; if(combo>maxCombo) maxCombo=combo;
  let type, label, cls, pts;
  if(reason === 'completed') {
    type='perfect'; label='PERFECT'; cls='perfect'; pts=150; perfects++;
  } else {
    // Early release: how much was left?
    const remaining = note.duration - (note.holdProgress * note.duration);
    if(remaining < HOLD_TIMING.perfect)      { type='good';  label='GOOD';  cls='good';  pts=75;  goods++; }
    else if(remaining < HOLD_TIMING.good)    { type='bad';   label='BAD';   cls='bad';   pts=15;  bads++;  combo=0; }
    else                                     { type='miss';  label='MISS';  cls='miss';  pts=0;   misses++;combo=0; }
  }
  const mult = combo>=40?8:combo>=20?4:combo>=10?2:1;
  score += pts * mult;
  updateHealth(HEALTH_DELTA[type==='miss'?'miss':type]);
  updateScore(); updateCombo(); showFeedback(label, cls); playHit(type==='miss'?'miss':type);
  spawnParticles(laneX(note.lane, noteCanvas.width), hitY, type==='miss'?'bad':type, note.lane);
}

// ══════════════════════════════════════════
//  INPUT
// ══════════════════════════════════════════
// handleTap: fires on press (touchstart/mousedown)
// Only handles normal and hold notes. Flick notes are SKIPPED here —
// they're evaluated in handleFlick() after the swipe is confirmed.
function handleTap(lane=0) {
  if(!isRunning) return;
  getCtx();
  lanePressed[lane] = true;

  const ring = document.getElementById(`ring-${lane}`);
  if(ring){ ring.classList.add('tapped'); setTimeout(()=>ring.classList.remove('tapped'),120); }

  const elapsed = performance.now() - gameStartTime;

  let closest=null, minDiff=Infinity;
  notes.forEach(n=>{
    if(n.hit || n.missed || n.holdActive) return;
    if(n.type === 'flick') return;          // flick notes ignored on press
    if(LANE_COUNT > 1 && n.lane !== lane) return;
    const d = Math.abs(elapsed - n.time);
    if(d < minDiff){ minDiff=d; closest=n; }
  });

  if(!closest || minDiff >= TIMING.bad) return;

  if(closest.type === 'hold') {
    closest.holdStarted  = true;
    closest.holdActive   = true;
    closest.holdStartTime = elapsed;
    closest.holdProgress  = 0;
    spawnParticles(laneX(closest.lane, noteCanvas.width), hitY, 'good', closest.lane);
    playHit('good');
  } else {
    processHit(closest, minDiff);
  }
}

// handleFlick: fires when a confirmed swipe gesture is detected
function handleFlick(lane, direction) {
  if(!isRunning) return;
  getCtx();
  const elapsed = performance.now() - gameStartTime;

  let closest=null, minDiff=Infinity;
  notes.forEach(n=>{
    if(n.hit || n.missed) return;
    if(n.type !== 'flick') return;
    if(LANE_COUNT > 1 && n.lane !== lane) return;
    const d = Math.abs(elapsed - n.time);
    if(d < minDiff){ minDiff=d; closest=n; }
  });

  if(!closest || minDiff >= TIMING.bad) return;

  if(direction === closest.direction) {
    // Correct swipe direction → normal scoring
    processHit(closest, minDiff);
  } else {
    // Wrong direction → BAD
    closest.hit = true; bads++; combo = 0;
    score += 10;
    updateHealth(HEALTH_DELTA.bad);
    updateScore(); updateCombo(); showFeedback('BAD','bad'); playHit('bad');
    spawnParticles(laneX(closest.lane, noteCanvas.width), hitY, 'bad', closest.lane);
  }
}

function handleRelease(lane=0) {
  lanePressed[lane] = false;
  touchOrigin[lane] = null;
}

function processHit(note, diff) {
  note.hit=true; combo++; if(combo>maxCombo) maxCombo=combo;
  let type,label,cls,pts;
  if(diff<TIMING.perfect)     { type='perfect'; label='PERFECT'; cls='perfect'; pts=100; perfects++; }
  else if(diff<TIMING.good)   { type='good';    label='GOOD';    cls='good';    pts=50;  goods++; }
  else                        { type='bad';      label='BAD';     cls='bad';     pts=10;  bads++; combo=0; }

  const mult = combo>=40?8:combo>=20?4:combo>=10?2:1;
  score += pts*mult;
  updateHealth(HEALTH_DELTA[type]);
  updateScore(); updateCombo(); showFeedback(label,cls); playHit(type);
  spawnParticles(laneX(note.lane, noteCanvas.width), hitY, type, note.lane);
}

// ══════════════════════════════════════════
//  UI HELPERS
// ══════════════════════════════════════════
function updateScore(){ document.getElementById('score-display').textContent=score.toString().padStart(6,'0'); }
function updateCombo(){
  const el=document.getElementById('combo-display');
  el.textContent=combo; el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
}
let fbTimer=null;
function showFeedback(text,cls){
  const el=document.getElementById('feedback');
  el.textContent=text; el.className=cls+' show';
  clearTimeout(fbTimer); fbTimer=setTimeout(()=>el.classList.remove('show'),320);
}
function setLoad(pct){ document.getElementById('load-bar').style.width=pct+'%'; }
function showError(msg){ document.getElementById('error-msg').textContent=msg; document.getElementById('error-screen').classList.add('show'); document.getElementById('loading-screen').style.display='none'; }

// ══════════════════════════════════════════
//  COUNTDOWN → START
// ══════════════════════════════════════════
function runCountdown() {
  return new Promise(resolve => {
    const cdEl = document.getElementById('countdown');
    const numEl = document.getElementById('cd-number');
    const goEl  = document.getElementById('cd-go');

    cdEl.classList.add('show');
    numEl.style.display = 'block';
    goEl.style.display  = 'none';

    const COUNTS = [3, 2, 1];
    let i = 0;

    function showCount() {
      numEl.style.animation = 'none';
      void numEl.offsetWidth;
      numEl.textContent   = COUNTS[i];
      numEl.style.animation = 'cd-pop .9s ease-out';

      // Sound: pitch rises with each number (3=low, 2=mid, 1=high)
      sfxCountdown(COUNTS[i]);

      i++;
      if (i < COUNTS.length) {
        setTimeout(showCount, 900);
      } else {
        setTimeout(() => {
          numEl.style.display = 'none';
          goEl.style.display  = 'block';
          goEl.style.animation = 'none';
          void goEl.offsetWidth;
          goEl.style.animation = 'cd-pop .7s ease-out forwards';
          sfxGo();
          setTimeout(() => {
            cdEl.classList.remove('show');
            resolve();
          }, 650);
        }, 900);
      }
    }

    showCount();
  });
}

// ══════════════════════════════════════════
//  GAME FLOW
// ══════════════════════════════════════════
function resetState() {
  score=0; combo=0; maxCombo=0; perfects=0; goods=0; bads=0; misses=0; particles=[]; health=HEALTH_START;
  const hb=document.getElementById('health-bar');
  if(hb){hb.style.width=HEALTH_START+'%';hb.classList.remove('low','danger');}
  const fs=document.getElementById('failed-screen');
  if(fs) fs.classList.remove('show');
  notes = CHART.notes.map(n=>({time:n.time,lane:Math.min(n.lane||0,LANE_COUNT-1),type:n.type||'normal',duration:n.duration||0,direction:n.direction||'left',hit:false,missed:false,holdStarted:false,holdActive:false,holdStartTime:0,holdProgress:0}));
  updateScore(); updateCombo();
  document.getElementById('progress-bar').style.width='0%';
  document.getElementById('results').classList.remove('show');
  document.getElementById('new-best').style.display='none';
}

async function startRound() {
  stopSong();
  stopResults();   // stop results jingle if replaying
  resetState();
  await runCountdown();
  isRunning     = true;
  gameStartTime = performance.now();   // single clock, consistent everywhere
  playSong(0);
  requestAnimationFrame(gameLoop);
}

async function endGame() {
  isRunning = false;
  stopSong();

  const total=perfects+goods+bads+misses;
  const pct  =total>0?((perfects*100+goods*50)/(total*100))*100:0;
  const rank =pct>=95?'S':pct>=85?'A':pct>=70?'B':pct>=50?'C':'D';
  const rlbl ={S:'Rhythm Master',A:'Excellent',B:'Great',C:'Good',D:'Keep Going'};

  document.getElementById('res-score').textContent   = score.toString().padStart(6,'0');
  document.getElementById('res-combo').textContent   = maxCombo;
  document.getElementById('res-perfect').textContent = perfects;
  document.getElementById('res-miss').textContent    = misses;
  document.getElementById('rank').textContent        = rank;
  document.getElementById('rank-label').textContent  = rlbl[rank];

  // Client-side new-best detection (instant, before server responds)
  const prevBest = CHART.bestScore ?? null;
  const isNewBest = prevBest === null || score > prevBest;
  if (isNewBest) document.getElementById('new-best').style.display='block';

  setTimeout(() => sfxResults(rank), 300);
  if(health >= HEALTH_MAX){
    const fh=document.getElementById('full-health');
    if(fh) fh.classList.add('show');
  }
  document.getElementById('results').classList.add('show');

  // Save to server
  try {
    const res = await fetch('api/scores.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        songId: SONG_ID, difficulty: DIFF,
        score, combo: maxCombo, rank,
        perfects, goods, bads, misses
      })
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    // Server is authoritative — update display if it disagrees
    if (data.saved && !isNewBest) document.getElementById('new-best').style.display='block';
    if (!data.saved && isNewBest) document.getElementById('new-best').style.display='none';
    console.log('Score guardado:', data);
  } catch(e) {
    console.warn('Score no guardado:', e);
  }
}

function retryGame() {
  document.getElementById('failed-screen').classList.remove('show');
  startRound();
}

function returnToMenu() {
  stopSong();
  isRunning=false;
  window.location.href='index.php?from=game';
}

// ══════════════════════════════════════════
//  LOAD CHART & INIT
// ══════════════════════════════════════════
async function init() {
  if(!SONG_ID){ showError('No se especificó ninguna canción.'); return; }
  document.getElementById('loading-song-name').textContent = 'CARGANDO';
  document.getElementById('loading-diff').textContent = DIFF.toUpperCase();

  setLoad(15);
  let data;
  try {
    const res = await fetch(`api/songs.php?id=${encodeURIComponent(SONG_ID)}&diff=${encodeURIComponent(DIFF)}`);
    if(!res.ok) throw new Error(`HTTP ${res.status}`);
    data = await res.json();
    if(data.error) throw new Error(data.error);
    if(!data.notes?.length) throw new Error('El chart no tiene notas.');
  } catch(e) {
    showError(`No se pudo cargar "${SONG_ID}" [${DIFF}]: ${e.message}`);
    return;
  }

  setLoad(50);
  CHART = data;

  // Apply lane count from chart (default 1 for old charts without lanes field)
  LANE_COUNT = Math.min(3, Math.max(1, CHART.lanes || 1));
  buildHitZone();   // builds hit-lines, target rings, tap zones

  // Apply to HUD
  document.getElementById('song-title').textContent = CHART.title || SONG_ID;
  document.getElementById('diff-badge').textContent = (CHART.label||DIFF) + ' · ' + (CHART.artist||'');
  document.getElementById('res-title').textContent  = CHART.title;
  document.getElementById('res-diff').textContent   = (CHART.label||DIFF) + ' · ' + (CHART.artist||'');
  document.title = `${CHART.title} — RHYTHM NOVA`;

  // Load audio if available + all sounds in parallel
  await Promise.all([
    CHART.audio ? loadAudio(CHART.audio) : Promise.resolve(),
    loadHitSounds(),
    loadGameUISounds(),
  ]);
  setLoad(100);

  // Fade loading screen
  const ls = document.getElementById('loading-screen');
  ls.classList.add('fade-out');
  setTimeout(()=>{ ls.style.display='none'; startRound(); }, 500);
}

// ══════════════════════════════════════════
//  EVENTS
// ══════════════════════════════════════════
// Per-lane tap zones are built dynamically by buildTapZone()
// Space bar always fires on lane 0 (or only lane for 1-lane songs)
document.addEventListener('keydown', e=>{
  if(e.code==='Space'){e.preventDefault(); handleTap(0);}
  if(e.code==='ArrowLeft') { e.preventDefault(); handleFlick(0,'left'); }
  if(e.code==='ArrowRight'){ e.preventDefault(); handleFlick(0,'right'); }
  if(e.code==='Escape') returnToMenu();
});
document.addEventListener('keyup', e=>{
  if(e.code==='Space') handleRelease(0);
});
document.getElementById('retry-btn').addEventListener('click', startRound);

resize();
recalcHitY();
init();
</script>
</body>
</html>
