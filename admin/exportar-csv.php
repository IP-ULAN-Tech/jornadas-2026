<?php
require_once __DIR__ . '/../inc/auth.php';
exigir_login();

$status = get('status');
$tipo   = get('tipo');
$q      = get('q');

$sql = 'SELECT id, tipo, eixo, titulo, autores, email, telefone, status, criado_em FROM submissoes WHERE 1=1';
$p = [];
if ($status) { $sql .= ' AND status = ?'; $p[] = $status; }
if ($tipo)   { $sql .= ' AND tipo = ?';   $p[] = $tipo; }
if ($q)      { $sql .= ' AND (titulo LIKE ? OR autores LIKE ? OR email LIKE ?)'; $l = "%$q%"; array_push($p, $l, $l, $l); }
$sql .= ' ORDER BY criado_em DESC';

$stmt = db()->prepare($sql);
$stmt->execute($p);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="submissoes-jornadas-2026.csv"');

echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Tipo', 'Eixo', 'Título', 'Autores', 'Email', 'Telefone', 'Estado', 'Submetido em'], ';');

while ($l = $stmt->fetch()) {
    fputcsv($out, array_values($l), ';');
}
fclose($out);
