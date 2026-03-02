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
│   ├── sounds/                ← Efectos de sonido (todos opcionales)
│   │   ├── navigate.ogg/mp3
│   │   ├── diffsel.ogg/mp3
│   │   ├── start.ogg/mp3
│   │   ├── cd3.ogg/mp3
│   │   ├── cd2.ogg/mp3
│   │   ├── cd1.ogg/mp3
│   │   ├── go.ogg/mp3
│   │   ├── hit.ogg/mp3
│   │   ├── bad.ogg/mp3
│   │   ├── miss.ogg/mp3
│   │   └── results.ogg/mp3
│   └── images/                ← Imágenes de sellos de mérito (opcionales)
│       ├── full_health.png
│       ├── full_combo.png
│       ├── all_perfect.png
│       ├── combo_50.png
│       ├── combo_100.png
│       └── combo_200.png
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

Para desarrollo en LAN con móvil se recomienda HTTPS (ver sección más abajo).

> El juego funciona sin audio externo, portadas ni imágenes de sellos — todo tiene fallbacks integrados.

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

Umbral: ≥40px de desplazamiento horizontal en ≤300ms. Se detecta en `touchmove` (respuesta inmediata) y en `touchend`. En escritorio usa `←` / `→`.

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

## Health bar

La barra de salud aparece entre la cabecera y el área de juego. Empieza al 70%.

| Juicio | Efecto en salud |
|---|---|
| PERFECT | +3 |
| GOOD | +1 |
| BAD | −8 |
| MISS | −15 |

**Estados visuales:** verde (>50%) · amarillo (26–50%) · rojo pulsante + temblor (1–25%) · glow verde cuando llega al 100%.

Si la salud llega a 0 aparece la pantalla **FAILED** con opciones de Retry y Menú.

---

## Pantalla de resultados

Muestra score, max combo, perfects, misses, accuracy con barra segmentada (perfect/good/bad/miss) y ranking.

### Sellos de mérito

Cuatro sellos siempre visibles (apagados si no se consiguieron). Los conseguidos se estampan en secuencia con animación al finalizar la canción:

| Sello | Condición |
|---|---|
| 💚 Full Health | Terminar con 100% de vida |
| ◆ Full Combo | Sin misses ni bads (perfects + goods) |
| ★ All Perfect | Solo perfects |
| 🔥 Combo | Máximo combo alcanzado: 🥉 ×50 · 🥈 ×100 · 🥇 ×200 |

Los sellos pueden personalizarse con imágenes PNG en `assets/images/` (ver estructura de carpetas). Si no existen, se muestran los emojis como fallback.

### Feedback táctil

En Android, las notas PERFECT generan una vibración corta (35ms) y GOOD una más suave (15ms). Requiere que la **respuesta táctil** esté activada en Ajustes del sistema y que el sitio sirva por **HTTPS**.

---

## Menú principal

El carrusel 3D muestra las canciones disponibles con portada, BPM, duración, dificultades disponibles y mejor puntuación guardada.

### Giroscopio

En móviles Android la card central reacciona al movimiento del dispositivo con un efecto de inclinación 3D. En iOS 13+ se solicita permiso al tocar la pantalla inicial.

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

## HTTPS en desarrollo local (LAN)

Algunas APIs del navegador (vibración, giroscopio en iOS) requieren HTTPS. Para desarrollo en LAN con EasyPHP:

1. Instala **mkcert** y genera certificados para tu IP:
   ```bash
   mkcert -install
   mkcert 192.168.1.XX localhost 127.0.0.1
   ```
2. Copia los `.pem` a `conf/ssl/` dentro de Apache
3. Añade al `httpd.conf`:
   ```apache
   LoadModule ssl_module modules/mod_ssl.so
   LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
   Listen 443
   <VirtualHost 192.168.1.XX:443>
     DocumentRoot "ruta/eds-www"
     SSLEngine on
     SSLCertificateFile    "conf/ssl/192.168.1.XX+2.pem"
     SSLCertificateKeyFile "conf/ssl/192.168.1.XX+2-key.pem"
   </VirtualHost>
   ```
4. Instala el certificado raíz de mkcert en Android: `%LOCALAPPDATA%/mkcert/rootCA.pem` → Ajustes → Seguridad → Instalar certificado CA

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
