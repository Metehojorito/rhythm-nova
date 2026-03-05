<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title>RHYTHM NOVA — Logros</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet"/>
<style>
  :root { --purple:#bc13fe; --cyan:#00f2ff; --gold:#ffcc00; --green:#00ff88; --red:#ef4444; }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body {
    background:#050510;
    background-image: radial-gradient(ellipse at 50% 0%, rgba(188,19,254,.08) 0%, transparent 60%);
    font-family:'Orbitron',monospace;
    color:#fff;
    min-height:100dvh;
    padding-bottom:40px;
  }

  /* ── Header ── */
  .header {
    position:sticky; top:0; z-index:10;
    background:rgba(5,5,16,.9); backdrop-filter:blur(10px);
    border-bottom:1px solid rgba(188,19,254,.2);
    padding:14px 20px;
    display:flex; align-items:center; gap:14px;
  }
  .back-btn {
    background:none; border:1px solid rgba(255,255,255,.15);
    color:rgba(255,255,255,.6); font-family:'Orbitron',monospace;
    font-size:11px; padding:7px 14px; border-radius:4px; cursor:pointer;
    transition:all .2s;
  }
  .back-btn:hover { border-color:var(--purple); color:#fff; }
  .header-title {
    font-size:14px; font-weight:900; letter-spacing:5px;
    color:#fff;
  }
  .header-count {
    margin-left:auto; font-size:10px; letter-spacing:2px;
    color:rgba(255,255,255,.3);
  }
  .header-count span { color:var(--cyan); }

  /* ── Stats bar ── */
  .stats-bar {
    display:flex; gap:24px; padding:16px 20px;
    border-bottom:1px solid rgba(255,255,255,.05);
    overflow-x:auto;
  }
  .stat-item { text-align:center; flex-shrink:0; }
  .stat-val { font-size:18px; font-weight:700; color:var(--cyan); }
  .stat-lbl { font-size:8px; letter-spacing:2px; color:rgba(255,255,255,.3); margin-top:2px; }

  /* ── Category sections ── */
  .category {
    padding:20px 16px 8px;
  }
  .category-label {
    font-size:9px; letter-spacing:3px; color:rgba(255,255,255,.25);
    text-transform:uppercase; margin-bottom:12px;
    padding-left:2px;
    border-left:2px solid rgba(188,19,254,.4);
    padding-left:8px;
  }
  .ach-grid {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(80px,1fr));
    gap:10px;
  }

  /* ── Achievement item ── */
  .ach-item {
    display:flex; flex-direction:column; align-items:center;
    gap:6px; cursor:pointer;
    padding:8px 4px; border-radius:8px;
    transition:background .2s;
  }
  .ach-item:active { background:rgba(255,255,255,.05); }
  .ach-img-wrap {
    width:60px; height:60px; position:relative;
    display:flex; align-items:center; justify-content:center;
  }
  .ach-img-wrap img {
    width:60px; height:60px; object-fit:contain;
    transition:filter .3s;
  }
  .ach-item.locked .ach-img-wrap img {
    filter:grayscale(1) brightness(0.2);
  }

  .ach-item.earned .ach-img-wrap {
    filter:drop-shadow(0 0 8px var(--cyan));
  }
  .ach-name {
    font-size:8px; letter-spacing:1px; text-align:center;
    color:rgba(255,255,255,.4); line-height:1.3;
    max-width:72px;
  }
  .ach-item.earned .ach-name { color:rgba(255,255,255,.8); }

  /* ── Tooltip/modal ── */
  #ach-modal {
    display:none; position:fixed; inset:0; z-index:100;
    background:rgba(0,0,0,.7); backdrop-filter:blur(4px);
    align-items:center; justify-content:center; padding:24px;
  }
  #ach-modal.show { display:flex; }
  .modal-box {
    background:rgba(10,10,25,.98);
    border:1px solid rgba(188,19,254,.3);
    border-radius:16px; padding:28px 24px;
    max-width:320px; width:100%;
    text-align:center;
    animation:pop-in .2s ease;
  }
  @keyframes pop-in {
    from { transform:scale(.9); opacity:0; }
    to   { transform:scale(1);  opacity:1; }
  }
  .modal-img { width:180px; height:180px; object-fit:contain; margin:0 auto 14px; display:block; }
  .modal-title { font-size:16px; font-weight:700; margin-bottom:8px; }
  .modal-desc { font-size:11px; color:rgba(255,255,255,.5); line-height:1.6; margin-bottom:16px; font-family:'Rajdhani',sans-serif; font-weight:500; letter-spacing:1px; }
  .modal-date { font-size:9px; color:var(--cyan); letter-spacing:2px; }
  .modal-locked { font-size:11px; color:rgba(255,255,255,.25); letter-spacing:1px; }
  .modal-close {
    margin-top:18px; background:none; border:1px solid rgba(255,255,255,.15);
    color:rgba(255,255,255,.5); font-family:'Orbitron',monospace;
    font-size:10px; padding:8px 20px; border-radius:4px; cursor:pointer;
    width:100%;
  }
  .modal-close:hover { border-color:var(--purple); color:#fff; }

  /* emoji fallback */
  .ach-emoji {
    font-size:32px; line-height:60px; text-align:center;
    width:60px; height:60px;
  }
</style>
</head>
<body>

<div class="header">
  <button class="back-btn" onclick="history.back()">← VOLVER</button>
  <div class="header-title">LOGROS</div>
  <div class="header-count"><span id="count-done">0</span> / 36</div>
</div>

<div class="stats-bar">
  <div class="stat-item"><div class="stat-val" id="st-songs">0</div><div class="stat-lbl">Canciones</div></div>
  <div class="stat-item"><div class="stat-val" id="st-flick">0</div><div class="stat-lbl">Flick perf.</div></div>
  <div class="stat-item"><div class="stat-val" id="st-hold">0</div><div class="stat-lbl">Hold perf.</div></div>
  <div class="stat-item"><div class="stat-val" id="st-dtap">0</div><div class="stat-lbl">Double perf.</div></div>
  <div class="stat-item"><div class="stat-val" id="st-combo">0</div><div class="stat-lbl">Max combo</div></div>
</div>

<div id="ach-list"></div>

<!-- Modal -->
<div id="ach-modal" onclick="closeModal()">
  <div class="modal-box" onclick="event.stopPropagation()">
    <img class="modal-img" id="modal-img" src="" alt="">
    <div class="modal-title" id="modal-title"></div>
    <div class="modal-desc"  id="modal-desc"></div>
    <div id="modal-status"></div>
    <button class="modal-close" onclick="closeModal()">CERRAR</button>
  </div>
</div>

<script>
const ACHIEVEMENTS = [
  { id:'first_song',       cat:'Primera vez',     title:'Primera canción',    desc:'Completa tu primera canción',                                    img:'ach_first_song.png'       },
  { id:'first_dual',       cat:'Primera vez',     title:'Dual Lane',          desc:'Completa tu primera canción con 2 carriles',                     img:'ach_first_dual.png'       },
  { id:'first_triple',     cat:'Primera vez',     title:'Triple Lane',        desc:'Completa tu primera canción con 3 carriles',                     img:'ach_first_triple.png'     },
  { id:'hard_1',           cat:'Difícil',         title:'Hard x1',            desc:'Completa 1 canción en difícil',                                  img:'ach_hard_1.png'           },
  { id:'hard_5',           cat:'Difícil',         title:'Hard x5',            desc:'Completa 5 canciones en difícil',                                img:'ach_hard_5.png'           },
  { id:'hard_10',          cat:'Difícil',         title:'Hard x10',           desc:'Completa 10 canciones en difícil',                               img:'ach_hard_10.png'          },
  { id:'hard_flawless',    cat:'Difícil',         title:'Hard Flawless',      desc:'Completa una canción en difícil sin ningún fallo',               img:'ach_hard_flawless.png'    },
  { id:'comeback',         cat:'Difícil',         title:'Comeback',           desc:'En difícil: llega al 100% de vida habiendo tenido menos del 10%', img:'ach_comeback.png'        },
  { id:'untouchable',      cat:'Habilidad',       title:'Untouchable',        desc:'Completa una canción sin perder vida',                            img:'ach_untouchable.png'      },
  { id:'last_breath',      cat:'Habilidad',       title:'Last Breath',        desc:'Gana una canción con la vida al mínimo (≤5%)',                   img:'ach_last_breath.png'      },
  { id:'all_perfect',      cat:'Habilidad',       title:'All Perfect',        desc:'Completa una canción con All Perfect',                           img:'ach_all_perfect.png'      },
  { id:'precision',        cat:'Habilidad',       title:'Precision',          desc:'En difícil: completa con más del 98% de accuracy',               img:'ach_precision.png'        },
  { id:'combo_50',         cat:'Combo',           title:'Combo 50',           desc:'Alcanza un combo de 50',                                         img:'ach_combo_50.png'         },
  { id:'combo_100',        cat:'Combo',           title:'Combo 100',          desc:'Alcanza un combo de 100',                                        img:'ach_combo_100.png'        },
  { id:'combo_150_dual',   cat:'Combo',           title:'Combo 150 ×2',      desc:'Combo de 150 en una canción de 2 carriles',                       img:'ach_combo_150_dual.png'   },
  { id:'combo_100_triple', cat:'Combo',           title:'Combo 100 ×3',      desc:'Combo de 100 en una canción de 3 carriles',                       img:'ach_combo_100_triple.png' },
  { id:'combo_200',        cat:'Combo',           title:'Combo 200',          desc:'Alcanza un combo de 200',                                        img:'ach_combo_200.png'        },
  { id:'combo_300',        cat:'Combo',           title:'Combo 300',          desc:'Alcanza un combo de 300',                                        img:'ach_combo_300.png'        },
  { id:'flick_500',        cat:'Flick',           title:'Flick 500',          desc:'500 Flick perfectos acumulados',                                 img:'ach_flick_500.png'        },
  { id:'flick_1000',       cat:'Flick',           title:'Flick 1000',         desc:'1000 Flick perfectos acumulados',                                img:'ach_flick_1000.png'       },
  { id:'flick_1500',       cat:'Flick',           title:'Flick 1500',         desc:'1500 Flick perfectos acumulados',                                img:'ach_flick_1500.png'       },
  { id:'flick_2000',       cat:'Flick',           title:'Flick 2000',         desc:'2000 Flick perfectos acumulados',                                img:'ach_flick_2000.png'       },
  { id:'hold_500',         cat:'Hold',            title:'Hold 500',           desc:'500 Hold perfectos acumulados',                                  img:'ach_hold_500.png'         },
  { id:'hold_1000',        cat:'Hold',            title:'Hold 1000',          desc:'1000 Hold perfectos acumulados',                                 img:'ach_hold_1000.png'        },
  { id:'hold_1500',        cat:'Hold',            title:'Hold 1500',          desc:'1500 Hold perfectos acumulados',                                 img:'ach_hold_1500.png'        },
  { id:'hold_2000',        cat:'Hold',            title:'Hold 2000',          desc:'2000 Hold perfectos acumulados',                                 img:'ach_hold_2000.png'        },
  { id:'dtap_500',         cat:'Double Tap',      title:'Double 500',         desc:'500 Double Tap perfectos acumulados',                            img:'ach_dtap_500.png'         },
  { id:'dtap_1000',        cat:'Double Tap',      title:'Double 1000',        desc:'1000 Double Tap perfectos acumulados',                           img:'ach_dtap_1000.png'        },
  { id:'dtap_1500',        cat:'Double Tap',      title:'Double 1500',        desc:'1500 Double Tap perfectos acumulados',                           img:'ach_dtap_1500.png'        },
  { id:'dtap_2000',        cat:'Double Tap',      title:'Double 2000',        desc:'2000 Double Tap perfectos acumulados',                           img:'ach_dtap_2000.png'        },
  { id:'songs_10',         cat:'Canciones',       title:'10 Songs',           desc:'Completa 10 canciones en cualquier dificultad',                  img:'ach_songs_10.png'         },
  { id:'songs_50',         cat:'Canciones',       title:'50 Songs',           desc:'Completa 50 canciones',                                          img:'ach_songs_50.png'         },
  { id:'songs_150',        cat:'Canciones',       title:'150 Songs',          desc:'Completa 150 canciones',                                         img:'ach_songs_150.png'        },
  { id:'songs_300',        cat:'Canciones',       title:'300 Songs',          desc:'Completa 300 canciones',                                         img:'ach_songs_300.png'        },
  { id:'songs_500',        cat:'Canciones',       title:'500 Songs',          desc:'Completa 500 canciones',                                         img:'ach_songs_500.png'        },
  { id:'songs_1000',       cat:'Canciones',       title:'1000 Songs',         desc:'Completa 1000 canciones',                                        img:'ach_songs_1000.png'       },
];

const IMG_PATH = 'assets/images/achievements/';

function loadUnlocked() {
  try { return JSON.parse(localStorage.getItem('rn_achievements') || '{}'); } catch(e) { return {}; }
}
function loadStats() {
  try { return JSON.parse(localStorage.getItem('rn_stats') || '{}'); } catch(e) { return {}; }
}

function render() {
  const unlocked = loadUnlocked();
  const stats    = loadStats();
  const list     = document.getElementById('ach-list');
  list.innerHTML = '';

  // Stats bar
  document.getElementById('st-songs').textContent  = stats.totalSongs   || 0;
  document.getElementById('st-flick').textContent  = stats.flickPerfect || 0;
  document.getElementById('st-hold').textContent   = stats.holdPerfect  || 0;
  document.getElementById('st-dtap').textContent   = stats.dtapPerfect  || 0;
  document.getElementById('st-combo').textContent  = stats.maxComboEver || 0;

  // Count
  const doneCount = Object.keys(unlocked).length;
  document.getElementById('count-done').textContent = doneCount;

  // Group by category
  const cats = {};
  ACHIEVEMENTS.forEach(a => {
    if(!cats[a.cat]) cats[a.cat] = [];
    cats[a.cat].push(a);
  });

  Object.entries(cats).forEach(([cat, achs]) => {
    const sec = document.createElement('div');
    sec.className = 'category';
    const lbl = document.createElement('div');
    lbl.className = 'category-label';
    lbl.textContent = cat;
    sec.appendChild(lbl);
    const grid = document.createElement('div');
    grid.className = 'ach-grid';
    achs.forEach(a => {
      const earned = !!unlocked[a.id];
      const item = document.createElement('div');
      item.className = 'ach-item ' + (earned ? 'earned' : 'locked');
      item.onclick = () => openModal(a, unlocked[a.id]);

      const wrap = document.createElement('div');
      wrap.className = 'ach-img-wrap';
      const img = document.createElement('img');
      img.src = IMG_PATH + a.img;
      img.alt = a.title;
      img.onerror = () => {
        wrap.innerHTML = `<div class="ach-emoji">🏅</div>`;
      };
      wrap.appendChild(img);

      const name = document.createElement('div');
      name.className = 'ach-name';
      name.textContent = a.title;

      item.appendChild(wrap);
      item.appendChild(name);
      grid.appendChild(item);
    });
    sec.appendChild(grid);
    list.appendChild(sec);
  });
}

function openModal(ach, unlockedAt) {
  const img = document.getElementById('modal-img');
  img.src = IMG_PATH + ach.img;
  img.onerror = () => { img.style.display='none'; };
  img.style.display = 'block';
  img.style.filter = unlockedAt ? 'none' : 'grayscale(1) brightness(0.2)';
  document.getElementById('modal-title').textContent = ach.title;
  document.getElementById('modal-desc').textContent  = ach.desc;
  const status = document.getElementById('modal-status');
  if(unlockedAt) {
    const d = new Date(unlockedAt);
    const fmt = d.toLocaleDateString('es-ES',{day:'2-digit',month:'2-digit',year:'numeric'});
    status.innerHTML = `<div class="modal-date">DESBLOQUEADO · ${fmt}</div>`;
  } else {
    status.innerHTML = `<div class="modal-locked">AÚN NO CONSEGUIDO</div>`;
  }
  document.getElementById('ach-modal').classList.add('show');
}

function closeModal() {
  document.getElementById('ach-modal').classList.remove('show');
}

render();
</script>
</body>
</html>
