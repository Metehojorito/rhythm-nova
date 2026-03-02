<?php
/**
 * api/upload.php
 * 
 * POST multipart/form-data:
 *   - audio    : MP3/OGG/WAV file (required)
 *   - chart    : chart.json file  (required)
 *   - cover    : image file       (optional)
 *   - songId   : custom slug      (optional — auto-generated from title if missing)
 * 
 * Returns: { success, songId, message }
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('SONGS_DIR',   __DIR__ . '/../songs/');
define('MAX_AUDIO_MB', 30);
define('MAX_COVER_MB', 5);

// ── Auth: simple token check ─────────────────────────────────────────────────
// Set ADMIN_TOKEN in config.php or as env var. 
// Leave empty to disable auth (not recommended for production).
$configFile = __DIR__ . '/../config.php';
if (file_exists($configFile)) require_once $configFile;

$adminToken = defined('ADMIN_TOKEN') ? ADMIN_TOKEN : ($_ENV['ADMIN_TOKEN'] ?? '');
if ($adminToken !== '') {
    $sentToken = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $_POST['token'] ?? '';
    if ($sentToken !== $adminToken) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function slugify(string $text): string {
    $text = mb_strtolower($text);
    $text = preg_replace('/[áàäâ]/u', 'a', $text);
    $text = preg_replace('/[éèëê]/u', 'e', $text);
    $text = preg_replace('/[íìïî]/u', 'i', $text);
    $text = preg_replace('/[óòöô]/u', 'o', $text);
    $text = preg_replace('/[úùüû]/u', 'u', $text);
    $text = preg_replace('/ñ/u', 'n', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function validateAudio(array $file): ?string {
    $allowed = ['audio/mpeg','audio/mp3','audio/ogg','audio/wav','audio/wave','audio/x-wav','audio/mp4','audio/m4a'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extOk   = in_array($ext, ['mp3','ogg','wav','m4a']);
    $mimeOk  = in_array($file['type'], $allowed);
    if (!$extOk && !$mimeOk) return 'Formato de audio no permitido (mp3, ogg, wav, m4a)';
    if ($file['size'] > MAX_AUDIO_MB * 1024 * 1024) return 'El audio supera ' . MAX_AUDIO_MB . 'MB';
    return null;
}

function validateChart(array $file): ?string {
    if ($file['size'] > 2 * 1024 * 1024) return 'El chart.json supera 2MB';
    $content = file_get_contents($file['tmp_name']);
    $data    = json_decode($content, true);
    if (!$data) return 'chart.json inválido (no es JSON válido)';
    if (empty($data['difficulties'])) return 'chart.json debe contener al menos una dificultad';
    return null;
}

function validateCover(array $file): ?string {
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array($file['type'], $allowed)) return 'Imagen no permitida (jpg, png, webp)';
    if ($file['size'] > MAX_COVER_MB * 1024 * 1024) return 'La portada supera ' . MAX_COVER_MB . 'MB';
    return null;
}

// ── Validate inputs ───────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$errors = [];

if (empty($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Falta el archivo de audio';
} else {
    $err = validateAudio($_FILES['audio']);
    if ($err) $errors[] = $err;
}

if (empty($_FILES['chart']) || $_FILES['chart']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Falta el archivo chart.json';
} else {
    $err = validateChart($_FILES['chart']);
    if ($err) $errors[] = $err;
}

if (!empty($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $err = validateCover($_FILES['cover']);
    if ($err) $errors[] = $err;
}

if ($errors) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// ── Determine song ID ─────────────────────────────────────────────────────────

$chartData = json_decode(file_get_contents($_FILES['chart']['tmp_name']), true);
$baseId    = !empty($_POST['songId'])
    ? slugify($_POST['songId'])
    : slugify($chartData['title'] ?? 'song-' . time());

// Avoid collisions
$songId = $baseId;
$suffix = 1;
while (is_dir(SONGS_DIR . $songId)) {
    $songId = $baseId . '-' . $suffix++;
}

$songDir = SONGS_DIR . $songId;
if (!mkdir($songDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo crear el directorio']);
    exit;
}

// ── Move files ────────────────────────────────────────────────────────────────

// Audio
$audioExt = strtolower(pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION));
move_uploaded_file($_FILES['audio']['tmp_name'], "$songDir/audio.$audioExt");

// Chart
move_uploaded_file($_FILES['chart']['tmp_name'], "$songDir/chart.json");

// Cover (optional)
if (!empty($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $coverExt = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
    move_uploaded_file($_FILES['cover']['tmp_name'], "$songDir/cover.$coverExt");
}

echo json_encode([
    'success' => true,
    'songId'  => $songId,
    'message' => "Canción '$songId' subida correctamente",
]);
