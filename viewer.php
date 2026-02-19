<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
$path = $_GET['path'] ?? '';
$allowed = ['uploads/'];
$valid = false;
foreach ($allowed as $p) {
    if (strpos($path, $p) === 0 && !strpos($path, '..')) $valid = true;
}
if (!$valid || !file_exists(__DIR__ . '/' . $path)) {
    header('HTTP/1.0 404 Not Found');
    echo 'File not found';
    exit;
}
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if ($ext === 'pdf') {
    header('Content-Type: application/pdf');
    readfile(__DIR__ . '/' . $path);
} elseif (in_array($ext, ['doc', 'docx'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<p style="padding:2rem;color:#333">Word documents (.doc/.docx) are best viewed by downloading. <a href="' . htmlspecialchars($path) . '" download>Download this file</a></p>';
} else {
    header('HTTP/1.0 404 Not Found');
}
