<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

$acao = $_GET['acao'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($acao) {

    /* 
       LOGIN — público, com rate limit
        */
    case 'login':
        if ($metodo !== 'POST') responder_erro('Método inválido.', 405);
        if (!limitar('login', 8, 900)) {
            responder_erro('Demasiadas tentativas. Aguarde 15 minutos.', 429);
        }

        $dados = corpo_json();
        $email = strtolower(campo('email', $dados));
        $pass  = campo('password', $dados);

        if ($email === '' || $pass === '') {
            responder_erro('Email e password obrigatórios.');
        }

        $stmt = db()->prepare('SELECT * FROM admins WHERE email = ? AND ativo = 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($pass, $admin['password_hash'])) {
            responder_erro('Credenciais inválidas.', 401);
        }

        iniciar_sessao();
        session_regenerate_id(true);
        $_SESSION['admin_id']   = (int) $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        $_SESSION['csrf']       = bin2hex(random_bytes(32));

        registar_log((int) $admin['id'], 'login');

        responder_json([
            'ok'   => true,
            'nome' => $admin['nome'],
            'csrf' => $_SESSION['csrf'],
        ]);
        break;

    /* ============================================================
       LOGOUT
       ============================================================ */
    case 'logout':
        iniciar_sessao();
        if (!empty($_SESSION['admin_id'])) {
            registar_log((int) $_SESSION['admin_id'], 'logout');
        }
        $_SESSION = [];
        session_destroy();
        responder_json(['ok' => true]);
        break;

    /* ============================================================
       VERIFICAR SESSÃO
       ============================================================ */
    case 'sessao':
        iniciar_sessao();
        if (empty($_SESSION['admin_id'])) {
            responder_json(['autenticado' => false]);
        }
        responder_json([
            'autenticado' => true,
            'nome'        => $_SESSION['admin_nome'],
            'csrf'        => $_SESSION['csrf'] ?? '',
        ]);
        break;

    /* ============================================================
       LISTAR SUBMISSÕES
       ============================================================ */
    case 'listar':
        exigir_admin();

        $status = $_GET['status'] ?? '';
        $tipo   = $_GET['tipo']   ?? '';
        $q      = $_GET['q']      ?? '';

        $sql = 'SELECT id, tipo, eixo, titulo, autores, email, status, criado_em
                FROM submissoes WHERE 1=1';
        $params = [];

        if ($status !== '') {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        if ($tipo !== '') {
            $sql .= ' AND tipo = ?';
            $params[] = $tipo;
        }
        if ($q !== '') {
            $sql .= ' AND (titulo LIKE ? OR autores LIKE ? OR email LIKE ?)';
            $like = '%' . $q . '%';
            array_push($params, $like, $like, $like);
        }

        $sql .= ' ORDER BY criado_em DESC LIMIT 500';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $linhas = $stmt->fetchAll();

        responder_json([
            'total'       => count($linhas),
            'submissoes'  => $linhas,
        ]);
        break;

    /* 
       DETALHE
        */
    case 'detalhe':
        exigir_admin();
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) responder_erro('ID inválido.');

        $stmt = db()->prepare('SELECT * FROM submissoes WHERE id = ?');
        $stmt->execute([$id]);
        $linha = $stmt->fetch();

        if (!$linha) responder_erro('Submissão não encontrada.', 404);
        responder_json($linha);
        break;

    /* 
       ATUALIZAR (PATCH)
        */
    case 'atualizar':
        $adminId = exigir_admin();
        if ($metodo !== 'POST') responder_erro('Método inválido.', 405);

        $dados = corpo_json();

        if (!csrf_valido($dados['csrf'] ?? null)) {
            responder_erro('Token CSRF inválido.', 403);
        }

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) responder_erro('ID inválido.');

        $status = campo('status', $dados);
        $obs    = campo('observacoes', $dados);

        $estados = ['pendente', 'em_analise', 'aceite', 'rejeitado'];
        if ($status !== '' && !in_array($status, $estados, true)) {
            responder_erro('Estado inválido.');
        }

        $stmt = db()->prepare('
            UPDATE submissoes
            SET status        = COALESCE(NULLIF(?, ""), status),
                observacoes   = ?,
                avaliado_por  = ?,
                avaliado_em   = NOW()
            WHERE id = ?
        ');
        $stmt->execute([
            $status,
            $obs !== '' ? $obs : null,
            $adminId,
            $id,
        ]);

        registar_log($adminId, 'atualizar_submissao', "id=$id status=$status");

        responder_json(['ok' => true]);
        break;

    /* 
       ESTATÍSTICAS
        */
    case 'estatisticas':
        exigir_admin();

        $total = (int) db()->query('SELECT COUNT(*) FROM submissoes')->fetchColumn();

        $porEstado = db()->query('
            SELECT status, COUNT(*) AS total FROM submissoes GROUP BY status
        ')->fetchAll();

        $porTipo = db()->query('
            SELECT tipo, COUNT(*) AS total FROM submissoes GROUP BY tipo
        ')->fetchAll();

        responder_json([
            'total'     => $total,
            'porEstado' => $porEstado,
            'porTipo'   => $porTipo,
        ]);
        break;

    /* 
       EXPORTAR CSV
        */
    case 'exportar':
        exigir_admin();

        $stmt = db()->query('
            SELECT id, tipo, eixo, titulo, autores, email, telefone, status, criado_em
            FROM submissoes
            ORDER BY criado_em DESC
        ');
        $linhas = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="submissoes-jornadas-2026.csv"');

        echo "\xEF\xBB\xBF"; // BOM UTF-8 para Excel
        $out = fopen('php://output', 'w');

        fputcsv($out, ['ID', 'Tipo', 'Eixo', 'Título', 'Autores', 'Email', 'Telefone', 'Estado', 'Submetido em'], ';');

        foreach ($linhas as $l) {
            fputcsv($out, [
                $l['id'], $l['tipo'], $l['eixo'], $l['titulo'],
                $l['autores'], $l['email'], $l['telefone'],
                $l['status'], $l['criado_em'],
            ], ';');
        }

        fclose($out);
        exit;

    default:
        responder_erro('Ação desconhecida.', 404);
}