# RHYTHM NOVA

Juego de ritmo para móvil y escritorio, con editor de beatmaps integrado. Desarrollado con HTML5 Canvas + Web Audio API + PHP.

---

## Estructura de carpetas

```
rhythm-nova/
├── index.php                  ← Menú de selección (carrusel dinámico)
├── game.php                   ← Motor del juego
├── editor.html                ← Editor de beatmaps
├── api/
│   ├── songs.php              ← API REST de canciones y charts
│   └── scores.php             ← API de puntuaciones
├── songs/
│   └── nombre-cancion/        ← Una carpeta por canción
│       ├── song.json          ← Metadatos de la canción
│       ├── easy.json          ← Chart dificultad easy (opcional)
│       ├── normal.json        ← Chart dificultad normal
│       ├── hard.json          ← Chart dificultad hard (opcional)
│       ├── audio.mp3          ← Audio (mp3, ogg, wav o m4a)
│       └── cover.jpg          ← Portada (jpg, png o webp — opcional)
├── assets/
│   └── sounds/                ← Efectos de sonido (todos opcionales)
│       ├── navigate.ogg/mp3
│       ├── diffsel.ogg/mp3
│       ├── start.ogg/mp3
│       ├── cd3.ogg/mp3
│       ├── cd2.ogg/mp3
│       ├── cd1.ogg/mp3
│       ├── go.ogg/mp3
│       ├── hit.ogg/mp3
│       ├── bad.ogg/mp3
│       ├── miss.ogg/mp3
│       └── results.ogg/mp3
└── data/
    └── scores.json            ← Puntuaciones (generado automáticamente)
```

---

## Instalación

1. Copia la carpeta en tu servidor con PHP 7.4+
2. Da permisos de escritura a la carpeta de datos:
   ```bash
   chmod 775 data/
   chmod 664 data/scores.json
   chown -R www-data:www-data data/
   ```
3. Accede a `http://tu-servidor/rhythm-nova/`

> El juego funciona sin audio externo ni portadas — todo tiene fallbacks integrados.

---

## Añadir una canción

1. Abre `editor.html` en el navegador
2. Sube tu MP3 o arrastra el archivo
3. Rellena los metadatos (título, artista, BPM, offset)
4. Selecciona el número de **carriles** (1, 2 o 3)
5. Coloca las notas (ver sección Editor más abajo)
6. Selecciona dificultad y nivel (1-10)
7. **Exportar → Descargar [diff].json** — repite para cada dificultad
8. **Exportar → song.json** — solo la primera vez por canción
9. Crea una carpeta en `songs/` con el nombre que quieras
10. Coloca dentro: `song.json`, los `.json` de dificultad, `audio.mp3` y `cover.jpg`
11. El carrusel la detecta automáticamente en la siguiente carga

> El nombre de la carpeta en `songs/` puede contener espacios y mayúsculas (ej: `songs/Kimetsu no Yaiba - Gurenge/`). No uses `..` ni `/` en el nombre.

---

## Editor de beatmaps

### Atajos de teclado

| Tecla | Acción |
|---|---|
| `Espacio` | Play / Pausa |
| `T` (pulso) | Añadir nota **normal** en el carril activo |
| `T` (mantener ≥300ms) | Añadir nota **hold** — la duración es el tiempo que mantienes pulsado |
| `R` | Añadir nota **flick izquierda** `◁` en el carril activo |
| `Y` | Añadir nota **flick derecha** `▷` en el carril activo |
| `1` / `2` / `3` | Seleccionar carril activo |
| `Supr` / `Backspace` | Borrar nota seleccionada |
| `←` / `→` | Ajustar tiempo de la nota seleccionada ±10ms |

### Interacción con la waveform

| Gesto | Acción |
|---|---|
| Clic izquierdo | Añadir nota normal (o seleccionar si hay una cerca) |
| Clic + arrastrar (≥300ms de duración) | Añadir nota hold |
| Clic derecho | Borrar nota bajo el cursor en ese carril |
| Doble clic | Saltar a esa posición del audio |
| Rueda del ratón | Scroll horizontal por la canción |

### Tipos de nota

| Tipo | Visual en waveform | Cómo crearla |
|---|---|---|
| Normal | Rombo del color del carril | `T` o clic simple |
| Hold | Rombo + barra horizontal hasta el final | `T` mantenido ≥300ms / clic+arrastrar |
| Flick ← | Rombo **naranja** con extensión a la izquierda | `R` |
| Flick → | Rombo **verde** con extensión a la derecha | `Y` |

### Sidebar derecho

- **Estadísticas** (arriba): número de notas, duración, notas/min y BPM detectado
- **Lista de notas** (abajo): scroll completo, muestra tiempo, carril y tipo. Clic en una nota para seleccionarla y saltar a su posición

### Importar un chart existente

Botón **Importar** en la barra superior. Acepta tanto `normal.json` (chart de dificultad) como `song.json` (metadatos). Puedes pegar el JSON directamente o cargar el archivo. El importador detecta automáticamente si los tiempos están en ms o segundos.

---

## Tipos de nota (juego)

### Normal
Toca en el momento en que la nota llega a la línea de hit.

### Hold
Pulsa cuando la cabeza llega y **mantén** hasta que la barra termine. Mientras aguantas, partículas fluyen continuamente desde la línea de hit como feedback visual.

| Cuándo sueltas | Juicio |
|---|---|
| Al completar la barra | PERFECT (+150 pts) |
| Últimos 150ms | GOOD (+75 pts) |
| Con >150ms restantes | BAD (+15 pts) |
| Muy pronto | MISS (0 pts) |

### Flick
Desliza el dedo en la dirección indicada por el color de la nota:

- **Naranja** (extensión a la izquierda) → swipe izquierda
- **Verde** (extensión a la derecha) → swipe derecha

Umbral: ≥40px de desplazamiento horizontal en ≤300ms. Se detecta en `touchmove` (respuesta inmediata al superar el umbral) y en `touchend`. En escritorio usa `←` / `→`.

| Acción | Resultado |
|---|---|
| Swipe correcto en tiempo | PERFECT / GOOD / BAD según timing |
| Swipe dirección incorrecta | BAD automático |
| Tap sin deslizar | Ignorado — la nota sigue activa |

---

## Ventanas de timing

| Juicio | Ventana | Puntos base |
|---|---|---|
| PERFECT | < 55 ms | 100 pts (normal/flick) · 150 pts (hold) |
| GOOD | < 120 ms | 50 pts (normal/flick) · 75 pts (hold) |
| BAD | < 220 ms | 10 pts |
| MISS | > 220 ms | 0 pts |

**Multiplicadores de combo:** ×2 desde combo 10 · ×4 desde combo 20 · ×8 desde combo 40

**Ranking final:** S ≥95% · A ≥85% · B ≥70% · C ≥50% · D <50%
(porcentaje de perfects + goods sobre el total de notas)

---

## Formatos JSON

### `song.json`
```json
{
  "id": "fc5542b1-df84-4237-b248-5304ce0cded8",
  "title": "Neon Horizon",
  "artist": "Synthwave Pro",
  "bpm": 128,
  "lanes": 2,
  "duration": 180000,
  "audio": "audio.mp3",
  "cover": "cover.jpg",
  "difficulties": ["easy", "normal", "hard"]
}
```

- `id` — UUID generado por el editor. **No lo cambies**: identifica la canción en el sistema de scores. Renombrar la carpeta no rompe las puntuaciones.
- `lanes` — número de carriles (1, 2 o 3). Se propaga al juego.
- `duration` — en milisegundos.

### `normal.json` (chart de dificultad)
```json
{
  "difficulty": "normal",
  "label": "NORMAL",
  "level": 5,
  "lanes": 2,
  "bpm": 128,
  "offset": 0,
  "duration": 180000,
  "notes": [
    { "time": 1000, "lane": 0, "type": "normal" },
    { "time": 1500, "lane": 1, "type": "hold", "duration": 800 },
    { "time": 2200, "lane": 0, "type": "flick", "direction": "left" },
    { "time": 2800, "lane": 1, "type": "flick", "direction": "right" }
  ]
}
```

| Campo | Descripción |
|---|---|
| `time` | ms desde el inicio del audio |
| `lane` | Índice 0-based (0 = L1, 1 = L2, 2 = L3) |
| `type` | `"normal"` · `"hold"` · `"flick"` |
| `duration` | Solo en holds — duración en ms |
| `direction` | Solo en flicks — `"left"` o `"right"` |

---

## Multi-carril

La pantalla se divide en N zonas táctiles verticales. Tocar en la zona equivocada mientras pasa una nota de otro carril no genera ningún juicio — la nota permanece activa hasta que su ventana de timing expira y se cuenta como MISS.

Las notas se colorean según su carril en juego y editor:

| Carril | Color |
|---|---|
| L1 | Cyan `#00f2ff` |
| L2 | Morado `#a855f7` |
| L3 | Verde `#10b981` |

---

## Efectos de sonido

Todos tienen fallback sintético integrado — son completamente opcionales. El sistema prueba `.ogg` primero y luego `.mp3`. Coloca los archivos en `assets/sounds/`.

| Archivo | Cuándo suena | Duración rec. |
|---|---|---|
| `navigate` | Cambiar canción en carrusel | ~0.3s |
| `diffsel` | Cambiar dificultad | ~0.2s |
| `start` | Pulsar START SESSION | ~0.5s |
| `cd3` / `cd2` / `cd1` | Cuenta atrás (3, 2, 1) | ~0.4s |
| `go` | "GO!" al iniciar | ~0.5s |
| `hit` | Nota acertada (perfect/good) | ~0.15s |
| `bad` | BAD timing | ~0.15s |
| `miss` | Nota perdida | ~0.2s |
| `results` | Pantalla de resultados | ~2s |

### Prompts para generar con IA

```
navigate  → "Short futuristic UI swoosh, soft electronic blip, subtle ascending
             pitch, clean cyberpunk aesthetic, 0.3 seconds"

diffsel   → "Quick two-tone electronic ping, triangle wave, bright and crisp,
             sci-fi interface option select, 0.2 seconds"

start     → "Powerful energetic electronic impact with upward sweep, neon cyberpunk
             style, punchy, game start confirmation sound, 0.5 seconds"

cd3       → "Deep low electronic tick, subdued, tension-building, 0.4 seconds"
cd2       → "Medium electronic tick, slightly brighter than cd3, 0.4 seconds"
cd1       → "High bright electronic tick, sharp, maximum tension, 0.4 seconds"
go        → "Energetic upward electronic fanfare, bright and punchy, GO signal, 0.5 seconds"

hit       → "Short crisp electronic tap, clean percussive click with high-frequency
             sparkle, rhythm game note hit, 0.15 seconds"

bad       → "Short downward buzz, electric sawtooth, negative feedback, 0.15 seconds"

miss      → "Low soft electronic thud, slightly muffled, descending tone,
             subtle disappointment, rhythm game miss, 0.2 seconds"

results   → "Short 4-note victory jingle, electronic synth, upbeat and satisfying,
             cyberpunk neon aesthetic, no vocals, 2 seconds"
```

> **Herramientas recomendadas:**
> - Efectos cortos: **ElevenLabs Sound Effects** — elevenlabs.io/sound-effects
> - Jingle de resultados: **Udio** — udio.com
> - Biblioteca gratuita: **Freesound.org** (Creative Commons)

---

## API

### `GET /api/songs.php`
Lista todas las canciones disponibles (sin arrays de notas).

### `GET /api/songs.php?id=<uuid>`
Metadatos de una canción sin notas.

### `GET /api/songs.php?id=<uuid>&diff=<normal|easy|hard>`
Chart completo con array de notas — esto es lo que carga `game.php`.

### `POST /api/scores.php`
Guarda una puntuación. Solo persiste si supera la anterior.
```json
{
  "songId": "fc5542b1-df84-4237-b248-5304ce0cded8",
  "difficulty": "normal",
  "score": 42000,
  "combo": 64,
  "rank": "A",
  "perfects": 50,
  "goods": 10,
  "bads": 2,
  "misses": 2
}
```
Responde `{ "saved": true, "best": { "score": 42000, "combo": 64, "rank": "A" } }`.
