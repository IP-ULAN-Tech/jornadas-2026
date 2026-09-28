<?php
require_once __DIR__ . '/../inc/auth.php';
exigir_login();

$id = (int) get('id');
$stmt = db()->prepare('SELECT ficheiro_guardado, ficheiro_original FROM submissoes WHERE id = ?');
$stmt->execute([$id]);
$l = $stmt->fetch();

if (!$l || !$l['ficheiro_guardado']) {
    http_response_code(404); exit('Ficheiro não encontrado.');
}

$caminho = rtrim(cfg()['uploads_dir'], '/') . '/submissoes/' . basename($l['ficheiro_guardado']);
if (!is_file($caminho)) { http_response_code(404); exit('Inexistente.'); }

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($caminho));
header('Content-Disposition: inline; filename="' . basename($l['ficheiro_original']) . '"');
readfile($caminho);
