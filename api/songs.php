<?php
/**
 * api/songs.php
 *
 * El identificador de canción es el UUID en song.json["id"].
 * El nombre de carpeta se usa solo para localizar archivos en disco.
 * Canciones sin UUID en song.json usan basename() como fallback.
 *
 * GET /api/songs.php                     → lista todas las canciones
 * GET /api/songs.php?id=<uuid>           → detalle sin notas
 * GET /api/songs.php?id=<uuid>&diff=X   → chart completo con notas
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('SONGS_DIR', __DIR__ . '/../songs/');
define('DATA_DIR',  __DIR__ . '/../data/');

function readJson(string $path): ?array {
    if (!file_exists($path)) return null;
    $d = json_decode(file_get_contents($path), true);
    return is_array($d) ? $d : null;
}

function getBestScore(string $uuid, string $diff): ?array {
    $s = readJson(DATA_DIR . 'scores.json');
    return $s[$uuid][$diff] ?? null;
}

// UUID solo puede contener hex y guiones — muy estricto
function sanitizeUUID(string $v): string {
    $v = preg_replace('/[^a-f0-9\-]/', '', strtolower(trim($v)));
    return strlen($v) >= 8 ? $v : '';   // mínimo razonable
}

function sanitizeDiff(string $v): string {
    return preg_replace('/[^a-z0-9\-_]/', '', strtolower($v));
}

// URL-encode solo el segmento de directorio para que espacios no rompan fetch()
function findAudio(string $dir, string $folderName): ?string {
    foreach (['mp3','ogg','wav','m4a'] as $ext)
        if (file_exists("$dir/audio.$ext"))
            return 'songs/' . rawurlencode($folderName) . "/audio.$ext";
    return null;
}

function findCover(string $dir, string $folderName): ?string {
    foreach (['jpg','jpeg','png','webp'] as $ext)
        if (file_exists("$dir/cover.$ext"))
            return 'songs/' . rawurlencode($folderName) . "/cover.$ext";
    return null;
}

function buildSong(string $folderName): ?array {
    $dir  = SONGS_DIR . $folderName;
    $meta = readJson("$dir/song.json");
    if (!$meta) return null;

    // UUID from song.json, fallback to folder name for legacy songs
    $uuid = trim($meta['id'] ?? '');
    if (!$uuid) $uuid = $folderName;

    $diffs = [];
    foreach ($meta['difficulties'] ?? [] as $diff) {
        $chartPath = "$dir/$diff.json";
        if (!file_exists($chartPath)) continue;
        $chart = readJson($chartPath);
        if (!$chart) continue;
        $best = getBestScore($uuid, $diff);
        $diffs[$diff] = [
            'label'     => $chart['label']  ?? strtoupper($diff),
            'level'     => $chart['level']  ?? 1,
            'noteCount' => count($chart['notes'] ?? []),
            'bestScore' => $best['score'] ?? null,
            'bestCombo' => $best['combo'] ?? null,
            'bestRank'  => $best['rank']  ?? null,
        ];
    }
    if (empty($diffs)) return null;

    return [
        'id'         => $uuid,            // ← UUID, usado para scores
        'folder'     => $folderName,      // ← nombre de carpeta (interno, para debug)
        'title'      => $meta['title']  ?? $folderName,
        'artist'     => $meta['artist'] ?? 'Desconocido',
        'bpm'        => $meta['bpm']    ?? 120,
        'duration'   => $meta['duration'] ?? 0,
        'cover'      => findCover($dir, $folderName),
        'audio'      => findAudio($dir, $folderName),
        'difficulties' => $diffs,
    ];
}

// Busca la carpeta cuyo song.json tiene el UUID dado
function findFolderByUUID(string $uuid): ?string {
    if (!is_dir(SONGS_DIR)) return null;
    foreach (glob(SONGS_DIR . '*', GLOB_ONLYDIR) as $dir) {
        $meta = readJson("$dir/song.json");
        if (!$meta) continue;
        $id = trim($meta['id'] ?? basename($dir));
        if ($id === $uuid) return basename($dir);
    }
    return null;
}

// ── Router ────────────────────────────────────────────────────────────────────
$uuid = isset($_GET['id'])   ? sanitizeUUID($_GET['id'])   : null;
$diff = isset($_GET['diff']) ? sanitizeDiff($_GET['diff']) : null;

// GET chart con notas: ?id=uuid&diff=normal
if ($uuid && $diff) {
    $folder = findFolderByUUID($uuid);
    if (!$folder) {
        http_response_code(404);
        echo json_encode(['error' => "Canción no encontrada: $uuid"]);
        exit;
    }
    $dir       = SONGS_DIR . $folder;
    $chartPath = "$dir/$diff.json";
    if (!file_exists($chartPath)) {
        http_response_code(404);
        echo json_encode(['error' => "Dificultad '$diff' no encontrada"]);
        exit;
    }
    $chart = readJson($chartPath);
    $meta  = readJson("$dir/song.json") ?? [];
    $best  = getBestScore($uuid, $diff);

    $response = array_merge($chart, [
        'songId'    => $uuid,           // ← UUID para que game.php lo use en scores
        'title'     => $meta['title']  ?? $chart['title']  ?? $folder,
        'artist'    => $meta['artist'] ?? $chart['artist'] ?? '',
        'lanes'     => (int)($chart['lanes'] ?? $meta['lanes'] ?? 1), // lanes from chart, fallback song meta
        'audio'     => findAudio($dir, $folder),
        'cover'     => findCover($dir, $folder),
        'bestScore' => $best['score'] ?? null,
        'bestCombo' => $best['combo'] ?? null,
        'bestRank'  => $best['rank']  ?? null,
    ]);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// GET detalle sin notas: ?id=uuid
if ($uuid) {
    $folder = findFolderByUUID($uuid);
    if (!$folder) {
        http_response_code(404);
        echo json_encode(['error' => "No encontrada: $uuid"]);
        exit;
    }
    $song = buildSong($folder);
    if (!$song) { http_response_code(404); echo json_encode(['error' => "Sin dificultades válidas"]); exit; }
    echo json_encode($song, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// GET lista de todas las canciones
$songs = [];
if (is_dir(SONGS_DIR))
    foreach (glob(SONGS_DIR . '*', GLOB_ONLYDIR) as $dir) {
        $s = buildSong(basename($dir));
        if ($s) $songs[] = $s;
    }
usort($songs, fn($a,$b) => strcmp($a['title'], $b['title']));
echo json_encode(['total'=>count($songs),'songs'=>$songs], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
