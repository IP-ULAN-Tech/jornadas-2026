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
$registo = $_SESSION['submeter_rate'] ?? [];
$registo = array_values(array_filter($registo, fn($t) => $t > $agora - 3600));

if (count($registo) >= 10) {
    http_response_code(429);
    echo json_encode(['erro' => 'Demasiadas submissões. Tente novamente mais tarde.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$registo[] = $agora;
$_SESSION['submeter_rate'] = $registo;

$cfg = cfg();

$tipo        = post('tipo');
$eixo        = post('eixo');
$titulo      = post('titulo');
$autores     = post('autores');
$instituicao = post('instituicao');
$email       = strtolower(post('email'));
$telefone    = post('telefone');
$resumo      = post('resumo');
$palavras    = post('palavras_chave');
$areaInova   = post('area_inova');
$equipa      = post('elementos_equipa');

if (!in_array($tipo, ['comunicacao', 'poster', 'projeto_inova'], true)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Tipo de submissão inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($titulo === '' || mb_strlen($titulo) > 255) {
    http_response_code(400);
    echo json_encode(['erro' => 'Título obrigatório (máx. 255 caracteres).'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($autores === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Indique pelo menos um autor.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Email inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (mb_strlen($resumo) < 50 || mb_strlen($resumo) > 2000) {
    http_response_code(400);
    echo json_encode(['erro' => 'O resumo deve ter entre 50 e 2000 caracteres.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($eixo !== '' && !in_array($eixo, ['01', '02', '03', '04'], true)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Eixo temático inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($tipo === 'projeto_inova' && $areaInova === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Indique a área de inovação do projecto.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$fOrig = $fGuard = null;
$fTamanho = null;

if (!empty($_FILES['ficheiro']['name'])) {
    $f = $_FILES['ficheiro'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['erro' => 'Falha no upload.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($f['size'] > $cfg['uploads_max']) {
        http_response_code(400);
        echo json_encode(['erro' => 'Ficheiro maior que 10 MB.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $mime = mime_content_type($f['tmp_name']) ?: '';
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

    if ($mime !== 'application/pdf' || $ext !== 'pdf') {
        http_response_code(400);
        echo json_encode(['erro' => 'Apenas ficheiros PDF são aceites.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $fh = fopen($f['tmp_name'], 'rb');
    $assinatura = fread($fh, 4);
    fclose($fh);

    if ($assinatura !== '%PDF') {
        http_response_code(400);
        echo json_encode(['erro' => 'O ficheiro não é um PDF válido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $destinoDir = rtrim($cfg['uploads_dir'], '/') . '/submissoes';
    if (!is_dir($destinoDir)) mkdir($destinoDir, 0755, true);

    $fGuard = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.pdf';
    $destino = $destinoDir . '/' . $fGuard;

    if (!move_uploaded_file($f['tmp_name'], $destino)) {
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível guardar o ficheiro.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $fOrig = mb_substr(basename($f['name']), 0, 255);
    $fTamanho = (int) $f['size'];
}

try {
    $stmt = db()->prepare('
        INSERT INTO submissoes (
            tipo, eixo, titulo, autores, instituicao, email, telefone,
            resumo, palavras_chave, area_inova, elementos_equipa,
            ficheiro_original, ficheiro_guardado, ficheiro_tamanho
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ');
    $stmt->execute([
        $tipo, $eixo ?: null, $titulo, $autores, $instituicao ?: null, $email, $telefone ?: null,
        $resumo, $palavras ?: null, $areaInova ?: null, $equipa ?: null,
        $fOrig, $fGuard, $fTamanho
    ]);

    $id = (int) db()->lastInsertId();

    registar_log(null, 'nova_submissao', "id=$id tipo=$tipo");

    echo json_encode([
        'ok' => true,
        'id' => $id,
        'mensagem' => 'Submissão recebida com sucesso.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    if ($fGuard) {
        $caminho = rtrim($cfg['uploads_dir'], '/') . '/submissoes/' . $fGuard;
        if (is_file($caminho)) @unlink($caminho);
    }
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao processar a submissão.'], JSON_UNESCAPED_UNICODE);
}