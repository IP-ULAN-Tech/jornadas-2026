<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/mailer.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

iniciar_sessao();
$agora = time();
$reg = array_values(array_filter($_SESSION['submeter_rate'] ?? [], fn($t) => $t > $agora - 3600));
if (count($reg) >= 10) {
    http_response_code(429);
    echo json_encode(['erro' => 'Demasiadas submissões. Tente novamente mais tarde.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$reg[] = $agora;
$_SESSION['submeter_rate'] = $reg;

$cfg = cfg();
$formulario = post('formulario', 'jornadas');

/* ============================================================
   Validação comum
   ============================================================ */
$titulo   = post('titulo');
$email    = strtolower(post('email'));
$telefone = post('telefone');
$resumo   = post('resumo');

if ($titulo === '' || mb_strlen($titulo) > 255) {
    http_response_code(400);
    echo json_encode(['erro' => 'Título obrigatório (máx. 255 caracteres).'], JSON_UNESCAPED_UNICODE);
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

/* ============================================================
   Upload de PDF (comum aos dois formulários)
   ============================================================ */
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
    if (!move_uploaded_file($f['tmp_name'], $destinoDir . '/' . $fGuard)) {
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível guardar o ficheiro.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $fOrig = mb_substr(basename($f['name']), 0, 255);
    $fTamanho = (int) $f['size'];
}

/* ============================================================
   Inserção na base de dados
   ============================================================ */
try {
    if ($formulario === 'inova') {
        $cursos     = post('cursos');
        $membros    = post('membros');
        $supervisor = post('supervisor');
        $areaInova  = post('area_inova');

        if ($cursos === '' || $membros === '' || $supervisor === '' || $areaInova === '') {
            throw new RuntimeException('Preencha todos os campos obrigatórios do projecto INOVA.');
        }

        $tipo = 'projeto_inova';
        $autores = $membros;

        $stmt = db()->prepare('
            INSERT INTO submissoes (
                tipo, eixo, titulo, autores, instituicao, email, telefone,
                resumo, palavras_chave, area_inova, elementos_equipa,
                ficheiro_original, ficheiro_guardado, ficheiro_tamanho
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ');
        $stmt->execute([
            $tipo, null, $titulo, $autores, null, $email, $telefone ?: null,
            $resumo, null, $areaInova, $cursos . ' — Supervisor: ' . $supervisor,
            $fOrig, $fGuard, $fTamanho
        ]);

    } else {
        $tipo         = post('tipo');
        $eixo         = post('eixo');
        $autores      = post('autores');
        $instituicao  = post('instituicao');
        $palavras     = post('palavras_chave');

        if (!in_array($tipo, ['comunicacao', 'poster'], true)) {
            throw new RuntimeException('Modalidade inválida.');
        }
        if ($autores === '' || $eixo === '') {
            throw new RuntimeException('Preencha todos os campos obrigatórios.');
        }

        $stmt = db()->prepare('
            INSERT INTO submissoes (
                tipo, eixo, titulo, autores, instituicao, email, telefone,
                resumo, palavras_chave,
                ficheiro_original, ficheiro_guardado, ficheiro_tamanho
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
        ');
        $stmt->execute([
            $tipo, $eixo, $titulo, $autores, $instituicao ?: null, $email, $telefone ?: null,
            $resumo, $palavras ?: null,
            $fOrig, $fGuard, $fTamanho
        ]);
    }

    $id = (int) db()->lastInsertId();
    registar_log(null, 'nova_submissao', "id=$id formulario=$formulario");

    /* ------------------------------------------------------------
       Email para o submissor
       ------------------------------------------------------------ */
    $assuntoAutor = $formulario === 'inova'
        ? 'Projecto INOVA IPS 2026 recebido — Registo #' . $id
        : 'Submissão recebida — Jornadas Científicas #' . $id;

    $corpoAutor = '
        <div style="font-family:Arial,sans-serif;color:#0A1F44;max-width:600px;margin:0 auto">
            <div style="background:#0A1F44;color:#fff;padding:32px 40px">
                <p style="font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:#B8E600;margin:0 0 8px">
                    Instituto Politécnico de Saurimo
                </p>
                <h1 style="font-size:24px;margin:0;line-height:1.2">
                    ' . ($formulario === 'inova' ? 'Projecto INOVA IPS 2026 registado' : 'Submissão recebida com sucesso') . '
                </h1>
            </div>
            <div style="padding:32px 40px;background:#fff">
                <p style="font-size:16px;line-height:1.6">Caro(a) ' . htmlspecialchars($autores) . ',</p>
                <p style="font-size:16px;line-height:1.6">A sua submissão foi registada no sistema das Jornadas Técnico-Científicas do IPS — Edição 2026.</p>
                <table style="border-collapse:collapse;margin:24px 0;width:100%">
                    <tr><td style="padding:12px;background:#F6F7F9;font-weight:bold">Número de registo</td><td style="padding:12px">#' . $id . '</td></tr>
                    <tr><td style="padding:12px;background:#F6F7F9;font-weight:bold">Título</td><td style="padding:12px">' . htmlspecialchars($titulo) . '</td></tr>
                    <tr><td style="padding:12px;background:#F6F7F9;font-weight:bold">Data</td><td style="padding:12px">' . date('d/m/Y H:i') . '</td></tr>
                </table>
                <p style="font-size:16px;line-height:1.6"><strong>Guarde o número de registo.</strong> Pode consultar o estado a qualquer momento em <a href="https://webmail.ip-ulan.ao" style="color:#1E4E8C">consultar.php</a>.</p>
                <p style="font-size:14px;color:#5A6B85;line-height:1.6;margin-top:32px;border-top:1px solid #E1E5EB;padding-top:20px">
                    Instituto Politécnico de Saurimo<br>
                    Universidade Lueji A\'Nkonde<br>
                    <a href="https://webmail.ip-ulan.ao" style="color:#5A6B85">jornadascientificas@ip-ulan.ao</a>
                </p>
            </div>
        </div>
    ';

    @enviar_email($email, $autores, $assuntoAutor, $corpoAutor);

    /* ------------------------------------------------------------
       Email para a comissão
       ------------------------------------------------------------ */
    $corpoComissao = '
        <h2>Nova submissão #' . $id . ' — ' . ($formulario === 'inova' ? 'INOVA IPS 2026' : 'Jornadas Científicas') . '</h2>
        <p><strong>Título:</strong> ' . htmlspecialchars($titulo) . '</p>
        <p><strong>Autores / Equipa:</strong> ' . htmlspecialchars($autores) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
        <p><strong>Telefone:</strong> ' . htmlspecialchars($telefone ?: '—') . '</p>
        <p><strong>Data:</strong> ' . date('d/m/Y H:i') . '</p>
        <p><a href="https://webmail.ip-ulan.ao">Abrir webmail institucional</a></p>
    ';

    @enviar_notificacao_comissao(
        'Nova submissão #' . $id . ' — ' . ($formulario === 'inova' ? 'INOVA IPS 2026' : 'Jornadas Científicas'),
        $corpoComissao
    );

    http_response_code(201);
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
    echo json_encode(['erro' => $e->getMessage() ?: 'Erro ao processar a submissão.'], JSON_UNESCAPED_UNICODE);
}