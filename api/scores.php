<?php
/**
 * api/scores.php
 * POST → guardar puntuación
 * GET  → consultar puntuaciones
 *
 * El songId es el UUID del campo "id" en song.json.
 * Canciones sin UUID usan el nombre de carpeta como fallback (legacy).
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

define('DATA_DIR',    __DIR__ . '/../data/');
define('SCORES_FILE', DATA_DIR . 'scores.json');

if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);

// Igual que en songs.php: solo bloquea path traversal, preserva espacios/mayúsculas
function sanitizeId(string $v): string {
    return trim(str_replace(['..', '/', '\\', "\0"], '', $v));
}

// Dificultad solo tiene caracteres seguros (easy/normal/hard)
function sanitizeDiff(string $v): string {
    return preg_replace('/[^a-z0-9\-_]/', '', strtolower($v));
}

function readScores(): array {
    if (!file_exists(SCORES_FILE)) return [];
    return json_decode(file_get_contents(SCORES_FILE), true) ?? [];
}

function writeScores(array $s): void {
    file_put_contents(SCORES_FILE, json_encode($s, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ── POST: guardar ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $b = json_decode(file_get_contents('php://input'), true) ?? [];

    foreach (['songId','difficulty','score','combo','rank'] as $f) {
        if (!isset($b[$f])) {
            http_response_code(400);
            echo json_encode(['error' => "Falta campo: $f"]);
            exit;
        }
    }

    $songId = sanitizeId((string)$b['songId']);
    $diff   = sanitizeDiff((string)$b['difficulty']);
    $score  = (int)$b['score'];
    $combo  = (int)$b['combo'];
    $rank   = substr(preg_replace('/[^A-Z]/', '', strtoupper($b['rank'])), 0, 1);

    if (!$songId || !$diff) {
        http_response_code(400);
        echo json_encode(['error' => 'songId o difficulty inválidos']);
        exit;
    }

    $scores  = readScores();
    $current = $scores[$songId][$diff] ?? null;
    $isBest  = !$current || $score > ($current['score'] ?? 0);

    if ($isBest) {
        $scores[$songId][$diff] = [
            'score'    => $score,
            'combo'    => $combo,
            'rank'     => $rank,
            'perfects' => (int)($b['perfects'] ?? 0),
            'goods'    => (int)($b['goods']    ?? 0),
            'bads'     => (int)($b['bads']     ?? 0),
            'misses'   => (int)($b['misses']   ?? 0),
            'date'     => date('Y-m-d H:i:s'),
        ];
        writeScores($scores);
    }

    echo json_encode(['saved' => $isBest, 'best' => $scores[$songId][$diff]]);
    exit;
}

// ── GET ───────────────────────────────────────────────────────────────────────
$scores = readScores();
$songId = isset($_GET['songId']) ? sanitizeId($_GET['songId'])         : null;
$diff   = isset($_GET['diff'])   ? sanitizeDiff($_GET['diff'])         : null;

if ($songId && $diff) { echo json_encode($scores[$songId][$diff] ?? null); exit; }
if ($songId)          { echo json_encode($scores[$songId] ?? []); exit; }
echo json_encode($scores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
