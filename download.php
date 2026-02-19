<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
$path = $_GET['path'] ?? '';
$allowed = ['uploads/'];
$valid = false;
foreach ($allowed as $p) {
    if (strpos($path, $p) === 0 && strpos($path, '..') === false) $valid = true;
}
if (!$valid || !file_exists(__DIR__ . '/' . $path)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
$pdo = getDB();
$stmt = $pdo->prepare("SELECT id FROM notes WHERE file_path = ? AND user_id = ?");
$stmt->execute([$path, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
$name = basename($path);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $name . '"');
readfile(__DIR__ . '/' . $path);
