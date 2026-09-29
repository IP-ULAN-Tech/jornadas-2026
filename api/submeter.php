<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_erro('Método não permitido.', 405);
}

if (!limitar('submeter', 10, 3600)) {
    responder_erro('Demasiadas submissões. Tente novamente mais tarde.', 429);
}

$cfg = require __DIR__ . '/config.php';

$tipo         = campo('tipo', $_POST);
$eixo         = campo('eixo', $_POST);
$titulo       = campo('titulo', $_POST);
$autores      = campo('autores', $_POST);
$instituicao  = campo('instituicao', $_POST);
$email        = campo('email', $_POST);
$telefone     = campo('telefone', $_POST);
$resumo       = campo('resumo', $_POST);
$palavras     = campo('palavras_chave', $_POST);
$areaInova    = campo('area_inova', $_POST);
$equipa       = campo('elementos_equipa', $_POST);

/* ---------- Validação ---------- */

if (!in_array($tipo, ['comunicacao', 'poster', 'projeto_inova'], true)) {
    responder_erro('Tipo de submissão inválido.');
}
if ($titulo === '' || mb_strlen($titulo) > 255) {
    responder_erro('Título obrigatório (máx. 255 caracteres).');
}
if ($autores === '') {
    responder_erro('Indique pelo menos um autor.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_erro('Email inválido.');
}
if (mb_strlen($resumo) < 50 || mb_strlen($resumo) > 2000) {
    responder_erro('O resumo deve ter entre 50 e 2000 caracteres.');
}
if ($eixo !== '' && !in_array($eixo, ['01', '02', '03', '04'], true)) {
    responder_erro('Eixo temático inválido.');
}

/* ---------- Upload do PDF ---------- */

$ficheiroOriginal = null;
$ficheiroGuardado = null;
$ficheiroTamanho  = null;

if (!empty($_FILES['ficheiro']['name'])) {
    $f = $_FILES['ficheiro'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        responder_erro('Falha no upload do ficheiro (código ' . $f['error'] . ').');
    }
    if ($f['size'] > $cfg['upload_max']) {
        responder_erro('Ficheiro demasiado grande (máx. 10 MB).');
    }

    $mime = mime_content_type($f['tmp_name']) ?: '';
    $ext  = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

    if ($mime !== 'application/pdf' || $ext !== 'pdf') {
        responder_erro('Apenas ficheiros PDF são aceites.');
    }

    // Nome no disco — aleatório, sem relação com o nome original
    $ficheiroGuardado = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.pdf';
    $destino = rtrim($cfg['upload_dir'], '/') . '/' . $ficheiroGuardado;

    if (!move_uploaded_file($f['tmp_name'], $destino)) {
        responder_erro('Não foi possível guardar o ficheiro no servidor.', 500);
    }

    $ficheiroOriginal = mb_substr(basename($f['name']), 0, 255);
    $ficheiroTamanho  = (int) $f['size'];
}

/* ---------- Inserir na BD ---------- */

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
        ':forig'       => $ficheiroOriginal,
        ':fguard'      => $ficheiroGuardado,
        ':ftam'        => $ficheiroTamanho,
    ]);

    $id = (int) db()->lastInsertId();

    responder_json([
        'ok'       => true,
        'id'       => $id,
        'mensagem' => 'Submissão recebida com sucesso. Receberá notificação após avaliação.'
    ], 201);

} catch (Throwable $e) {
    responder_erro('Erro ao processar a submissão.', 500);
}