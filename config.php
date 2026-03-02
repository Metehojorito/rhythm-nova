<?php
/**
 * config.php — Configuración central de Rhythm Nova
 * 
 * IMPORTANTE: Añade este archivo a .gitignore si usas control de versiones.
 */

// ── Admin Token ────────────────────────────────────────────────────────────
// Cambia esto por un valor secreto. Se usa para proteger la API de subida.
// Déjalo vacío ('') para desactivar la autenticación (solo para desarrollo local).
define('ADMIN_TOKEN', '');

// ── Base URL ───────────────────────────────────────────────────────────────
// URL raíz del proyecto (sin barra final). Útil para generar rutas absolutas.
define('BASE_URL', 'http://localhost/beats');

// ── Límites ────────────────────────────────────────────────────────────────
define('MAX_SONGS',     100);   // Máx. canciones permitidas
define('MAX_AUDIO_MB',   30);   // Tamaño máximo de audio en MB
define('MAX_COVER_MB',    5);   // Tamaño máximo de portada en MB

// ── Directorios ────────────────────────────────────────────────────────────
define('SONGS_DIR',  __DIR__ . '/songs/');
define('DATA_DIR',   __DIR__ . '/data/');
