<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';

header('Content-Type: application/json; charset=utf-8');

/* ------------------------------------------------------------
   Apenas aceita POST
   ------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ------------------------------------------------------------
   Rate limit — 10 submissões por hora, por sessão
   ------------------------------------------------------------ */
iniciar_sessao();
$agora = time();
$registo = $_SESSION['submeter_rate'] ?? [];
$registo = array_values(array_filter($registo, fn($t) => $t > $agora - 3600));

if (count($registo) >= 10) {
    http_response_code(429);
    echo json_encode(
        ['erro' => 'Demasiadas submissões. Tente novamente mais tarde.'],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

$registo[] = $agora;
$_SESSION['submeter_rate'] = $registo;

$cfg = cfg();

/* ------------------------------------------------------------
   Recolha e limpeza de dados
   ------------------------------------------------------------ */
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

/* ------------------------------------------------------------
   Validação
   ------------------------------------------------------------ */
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

if ($tipo === 'projeto_inova') {
    if ($areaInova === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Indique a área de inovação do projecto.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($equipa === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Indique os elementos da equipa do projecto.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* ------------------------------------------------------------
   Upload do PDF
   ------------------------------------------------------------ */
$fOrig    = null;
$fGuard   = null;
$fTamanho = null;

if (!empty($_FILES['ficheiro']['name'])) {

    $f = $_FILES['ficheiro'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['erro' => 'Falha no upload do ficheiro.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($f['size'] > $cfg['uploads_max']) {
        http_response_code(400);
        echo json_encode(['erro' => 'Ficheiro maior que 10 MB.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $mime = mime_content_type($f['tmp_name']) ?: '';
    $ext  = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

    if ($mime !== 'application/pdf' || $ext !== 'pdf') {
        http_response_code(400);
        echo json_encode(['erro' => 'Apenas ficheiros PDF são aceites.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $destinoDir = rtrim($cfg['uploads_dir'], '/') . '/submissoes';
    if (!is_dir($destinoDir)) {
        mkdir($destinoDir, 0755, true);
    }

    $fGuard = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.pdf';
    $destino = $destinoDir . '/' . $fGuard;

    if (!move_uploaded_file($f['tmp_name'], $destino)) {
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível guardar o ficheiro no servidor.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $fOrig    = mb_substr(basename($f['name']), 0, 255);
    $fTamanho = (int) $f['size'];
}

/* ------------------------------------------------------------
   Inserir na base de dados
   ------------------------------------------------------------ */
try {
    $stmt = db()->prepare('
        INSERT INTO submissoes (
            tipo, eixo, titulo, autores, instituicao, email, telefone,
            resumo, palavras_chave, area_inova, elementos_equipa,
            ficheiro_original, ficheiro_guardado, ficheiro_tamanho
        ) VALUES (
            :tipo, :eixo, :titulo, :autores, :instituicao, :email, :telefone,
            :resumo, :palavras, :area, :equipa,
            :forig, :fguard, :ftam
        )
    ');

    $stmt->execute([
        ':tipo'        => $tipo,
        ':eixo'        => $eixo !== '' ? $eixo : null,
        ':titulo'      => $titulo,
        ':autores'     => $autores,
        ':instituicao' => $instituicao !== '' ? $instituicao : null,
        ':email'       => $email,
        ':telefone'    => $telefone !== '' ? $telefone : null,
        ':resumo'      => $resumo,
        ':palavras'    => $palavras !== '' ? $palavras : null,
        ':area'        => $areaInova !== '' ? $areaInova : null,
        ':equipa'      => $equipa !== '' ? $equipa : null,
        ':forig'       => $fOrig,
        ':fguard'      => $fGuard,
        ':ftam'        => $fTamanho,
    ]);

    $id = (int) db()->lastInsertId();

    /* ------------------------------------------------------------
       Notificar a comissão organizadora por email (opcional)
       ------------------------------------------------------------ */
    $emailComissao = setting('email_comissao', '');
    if ($emailComissao !== '' && filter_var($emailComissao, FILTER_VALIDATE_EMAIL)) {
        $assunto = 'Nova submissão #' . $id . ' — Jornadas Técnico-Científicas 2026';
        $corpo   = "Nova submissão recebida.\n\n"
                 . "Número: #{$id}\n"
                 . "Tipo: {$tipo}\n"
                 . "Título: {$titulo}\n"
                 . "Autores: {$autores}\n"
                 . "Email: {$email}\n"
                 . "Telefone: " . ($telefone ?: '—') . "\n"
                 . "Instituição: " . ($instituicao ?: '—') . "\n"
                 . "Eixo: " . ($eixo ?: '—') . "\n\n"
                 . "Aceder ao painel: " . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '') . '/jornadas-ips-2026/admin/submissoes.php';

        @mail(
            $emailComissao,
            $assunto,
            $corpo,
            "From: Jornadas IPS 2026 <nao-responder@" . ($_SERVER['HTTP_HOST'] ?? 'ips.ao') . ">\r\n" .
            "Reply-To: {$email}\r\n" .
            "Content-Type: text/plain; charset=UTF-8"
        );
    }

    /* ------------------------------------------------------------
       Notificar o submissor por email (opcional)
       ------------------------------------------------------------ */
    $assuntoSubmissor = 'Submissão recebida — Jornadas Técnico-Científicas do IPS 2026';
    $corpoSubmissor   = "Caro(a) {$autores},\n\n"
                      . "A sua submissão foi recebida com sucesso nas Jornadas Técnico-Científicas do Instituto Politécnico de Saurimo — Edição 2026.\n\n"
                      . "Número de registo: #{$id}\n"
                      . "Título: {$titulo}\n"
                      . "Tipo: {$tipo}\n"
                      . "Data: " . date('d/m/Y H:i') . "\n\n"
                      . "Guarde o número de registo para qualquer consulta posterior.\n\n"
                      . "A comissão organizadora irá analisar o trabalho e comunicará a decisão através deste email.\n\n"
                      . "Instituto Politécnico de Saurimo\n"
                      . "Universidade Lueji A'Nkonde\n";

    @mail(
        $email,
        $assuntoSubmissor,
        $corpoSubmissor,
        "From: Jornadas IPS 2026 <nao-responder@" . ($_SERVER['HTTP_HOST'] ?? 'ips.ao') . ">\r\n" .
        "Content-Type: text/plain; charset=UTF-8"
    );

    /* ------------------------------------------------------------
       Registo de log
       ------------------------------------------------------------ */
    registar_log(null, 'nova_submissao', "id={$id} tipo={$tipo}");

    /* ------------------------------------------------------------
       Resposta de sucesso
       ------------------------------------------------------------ */
    http_response_code(201);
    echo json_encode([
        'ok'       => true,
        'id'       => $id,
        'evento'   => 'Jornadas Técnico-Científicas do IPS 2026',
        'mensagem' => 'Submissão recebida com sucesso. Receberá notificação após avaliação.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    /* Se a inserção falhar, remover o PDF já guardado */
    if ($fGuard) {
        $caminho = rtrim($cfg['uploads_dir'], '/') . '/submissoes/' . $fGuard;
        if (is_file($caminho)) @unlink($caminho);
    }

    http_response_code(500);
    echo json_encode(
        ['erro' => 'Erro ao processar a submissão. Tente novamente.'],
        JSON_UNESCAPED_UNICODE
    );
}