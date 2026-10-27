<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

iniciar_sessao();

$agora = time();
$registo = $_SESSION['consultar_rate'] ?? [];
$registo = array_values(array_filter($registo, fn($t) => $t > $agora - 300));

if (count($registo) >= 15) {
    http_response_code(429);
    echo json_encode(['erro' => 'Demasiadas consultas. Aguarde alguns minutos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$registo[] = $agora;
$_SESSION['consultar_rate'] = $registo;

$corpo = file_get_contents('php://input');
$dados = json_decode($corpo ?: '[]', true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$numero = (int) ($dados['numero'] ?? 0);
$email  = strtolower(trim((string) ($dados['email'] ?? '')));

if ($numero <= 0 || $email === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Indique o número de registo e o email.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Email inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = db()->prepare('
    SELECT id, tipo, eixo, titulo, autores, email, status,
           ficheiro_original, ficheiro_guardado, criado_em
    FROM submissoes
    WHERE id = ? AND LOWER(email) = ?
    LIMIT 1
');
$stmt->execute([$numero, $email]);
$sub = $stmt->fetch();

if (!$sub) {
    usleep(400000);

    http_response_code(404);
    echo json_encode([
        'erro' => 'Não encontrámos nenhuma submissão com esses dados. Verifique o número de registo e o email.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$tipos = [
    'comunicacao'   => 'Comunicação Oral',
    'poster'        => 'Poster Científico',
    'projeto_inova' => 'Projecto INOVA IPS 2026',
];

$autores = explode(';', $sub['autores']);
$autorPrincipal = trim($autores[0] ?? '');

echo json_encode([
    'ok' => true,
    'submissao' => [
        'id'              => (int) $sub['id'],
        'titulo'          => $sub['titulo'],
        'tipo'            => $sub['tipo'],
        'tipo_nome'       => $tipos[$sub['tipo']] ?? $sub['tipo'],
        'autor_principal' => $autorPrincipal,
        'status'          => $sub['status'],
        'criado_em'       => date('d/m/Y H:i', strtotime($sub['criado_em'])),
        'tem_ficheiro'    => !empty($sub['ficheiro_guardado']),
        'ficheiro_nome'   => $sub['ficheiro_original'] ?: null,
    ]
], JSON_UNESCAPED_UNICODE);