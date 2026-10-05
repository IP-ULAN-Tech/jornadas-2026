<?php
require_once __DIR__ . '/../../inc/auth.php';
exigir_editor_ou_super();

$crud = $crud ?? null;
if (!$crud) die('CRUD mal configurado.');

$tabela      = $crud['tabela'];
$titulo      = $crud['titulo'];
$singular    = $crud['singular'] ?? 'Registo';
$subpasta    = $crud['subpasta'] ?? $tabela;
$ordem       = $crud['ordem'] ?? 'id DESC';
$colunas     = $crud['colunas'] ?? [];
$campos      = $crud['campos'] ?? [];
$campoImagem = $crud['campo_imagem'] ?? null;

$acao     = get('acao', 'listar');
$id       = (int) get('id', '0');
$mensagem = flash('mensagem');
$erro     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_valido($_POST['csrf'] ?? null)) {
        $erro = 'Sessão expirada. Tente novamente.';

    } else {
        try {
            $dados = [];
            foreach ($campos as $campo) {
                if ($campo['tipo'] === 'imagem') {
                    $actual = $id ? get_campo($tabela, $id, $campo['nome']) : null;
                    $dados[$campo['nome']] = guardar_imagem($campo['nome'], $subpasta, $actual);
                } elseif ($campo['tipo'] === 'booleano') {
                    $dados[$campo['nome']] = isset($_POST[$campo['nome']]) ? 1 : 0;
                } else {
                    $dados[$campo['nome']] = trim((string) ($_POST[$campo['nome']] ?? ''));
                }
            }

            if ($id > 0) {
                $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($dados)));
                $dados['__id'] = $id;
                $stmt = db()->prepare("UPDATE $tabela SET $sets WHERE id = :__id");
                $stmt->execute($dados);
                registar_log((int) $_SESSION['admin_id'], 'editar', "$tabela #$id");
                flash('mensagem', $singular . ' actualizado com sucesso.');

            } else {
                $cols = implode(', ', array_keys($dados));
                $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($dados)));
                $stmt = db()->prepare("INSERT INTO $tabela ($cols) VALUES ($vals)");
                $stmt->execute($dados);
                registar_log((int) $_SESSION['admin_id'], 'criar', "$tabela #" . db()->lastInsertId());
                flash('mensagem', $singular . ' criado com sucesso.');
            }

            redirecionar(basename($_SERVER['PHP_SELF']));

        } catch (Throwable $e) {
            $erro = 'Erro: ' . $e->getMessage();
        }
    }
}

if ($acao === 'eliminar' && $id > 0 && csrf_valido($_GET['csrf'] ?? null)) {

    if (!admin_pode_apagar()) {
        flash('erro', 'Não tem permissão para eliminar registos.');
        redirecionar(basename($_SERVER['PHP_SELF']));
    }

    if ($campoImagem) {
        apagar_imagem(get_campo($tabela, $id, $campoImagem));
    }

    db()->prepare("DELETE FROM $tabela WHERE id = ?")->execute([$id]);
    registar_log((int) $_SESSION['admin_id'], 'eliminar', "$tabela #$id");
    flash('mensagem', $singular . ' eliminado com sucesso.');
    redirecionar(basename($_SERVER['PHP_SELF']));
}

$registo = [];
if ($acao === 'editar' && $id > 0) {
    $stmt = db()->prepare("SELECT * FROM $tabela WHERE id = ?");
    $stmt->execute([$id]);
    $registo = $stmt->fetch() ?: [];
    if (!$registo) $acao = 'listar';
}

function get_campo(string $tabela, int $id, string $campo): ?string
{
    $stmt = db()->prepare("SELECT `$campo` FROM `$tabela` WHERE id = ?");
    $stmt->execute([$id]);
    $v = $stmt->fetchColumn();
    return $v === false ? null : (string) $v;
}

$tituloPagina = $titulo;
require __DIR__ . '/header.php';
?>

<?php if ($mensagem): ?>
  <div class="aviso aviso-sucesso"><?= e($mensagem) ?></div>
<?php endif; ?>

<?php if ($erro): ?>
  <div class="aviso aviso-erro"><?= e($erro) ?></div>
<?php endif; ?>

<?php if ($acao === 'novo' || $acao === 'editar'): ?>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo"><?= $acao === 'editar' ? 'Editar ' : 'Novo ' ?><?= e($singular) ?></h2>
      <a href="<?= e(basename($_SERVER['PHP_SELF'])) ?>" class="link-acao">Voltar</a>
    </div>

    <div class="cartao-corpo">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <?php foreach ($campos as $campo): ?>
          <?php
            $nome = $campo['nome'];
            $valor = $registo[$nome] ?? ($campo['padrao'] ?? '');
            $classe = ($campo['largura'] ?? 'full') === 'meio' ? 'campo meio' : 'campo';
          ?>

          <?php if ($campo['tipo'] === 'texto'): ?>
            <div class="<?= $classe ?>">
              <label for="<?= e($nome) ?>"><?= e($campo['label']) ?></label>
              <input type="text" id="<?= e($nome) ?>" name="<?= e($nome) ?>"
                     value="<?= e((string) $valor) ?>"
                     <?= !empty($campo['obrigatorio']) ? 'required' : '' ?>
                     <?= !empty($campo['max']) ? 'maxlength="' . (int) $campo['max'] . '"' : '' ?>>
            </div>

          <?php elseif ($campo['tipo'] === 'textarea'): ?>
            <div class="campo">
              <label for="<?= e($nome) ?>"><?= e($campo['label']) ?></label>
              <textarea id="<?= e($nome) ?>" name="<?= e($nome) ?>" rows="<?= (int) ($campo['linhas'] ?? 4) ?>"
                        <?= !empty($campo['obrigatorio']) ? 'required' : '' ?>><?= e((string) $valor) ?></textarea>
            </div>

          <?php elseif ($campo['tipo'] === 'numero'): ?>
            <div class="<?= $classe ?>">
              <label for="<?= e($nome) ?>"><?= e($campo['label']) ?></label>
              <input type="number" id="<?= e($nome) ?>" name="<?= e($nome) ?>"
                     value="<?= e((string) $valor) ?>"
                     <?= isset($campo['min']) ? 'min="' . (int) $campo['min'] . '"' : '' ?>
                     <?= isset($campo['max']) ? 'max="' . (int) $campo['max'] . '"' : '' ?>>
            </div>

          <?php elseif ($campo['tipo'] === 'booleano'): ?>
            <div class="campo">
              <label>
                <input type="checkbox" name="<?= e($nome) ?>" value="1" <?= !empty($valor) ? 'checked' : '' ?>>
                <?= e($campo['label']) ?>
              </label>
            </div>

          <?php elseif ($campo['tipo'] === 'select'): ?>
            <div class="<?= $classe ?>">
              <label for="<?= e($nome) ?>"><?= e($campo['label']) ?></label>
              <select id="<?= e($nome) ?>" name="<?= e($nome) ?>">
                <?php foreach ($campo['opcoes'] as $val => $rot): ?>
                  <option value="<?= e((string) $val) ?>" <?= (string) $valor === (string) $val ? 'selected' : '' ?>>
                    <?= e($rot) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

          <?php elseif ($campo['tipo'] === 'imagem'): ?>
            <div class="campo">
              <label for="<?= e($nome) ?>"><?= e($campo['label']) ?></label>
              <?php if (!empty($valor)): ?>
                <div style="margin-bottom:12px">
                  <img src="<?= e(imagem_url((string) $valor, 'Imagem')) ?>" alt=""
                       style="max-width:240px;border:1px solid var(--linha)">
                </div>
              <?php endif; ?>
              <input type="file" id="<?= e($nome) ?>" name="<?= e($nome) ?>"
                     accept="image/jpeg,image/png,image/webp"
                     data-preview="#preview-<?= e($nome) ?>">
              <div id="preview-<?= e($nome) ?>" style="margin-top:12px"></div>
              <small>Formatos: JPG, PNG, WebP · Máx. 5 MB</small>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>

        <div class="form-acoes">
          <button type="submit" class="botao botao-verde" style="width:auto">
            <?= $acao === 'editar' ? 'Guardar alterações' : 'Criar' ?>
          </button>
          <a href="<?= e(basename($_SERVER['PHP_SELF'])) ?>" class="botao botao-sec" style="width:auto">Cancelar</a>
        </div>
      </form>
    </div>
  </section>

<?php else: ?>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo"><?= e($titulo) ?></h2>
      <a href="?acao=novo" class="botao botao-verde botao-mini">Adicionar <?= e($singular) ?></a>
    </div>

    <?php $linhas = db()->query("SELECT * FROM $tabela ORDER BY $ordem")->fetchAll(); ?>

    <?php if (!$linhas): ?>
      <p class="vazio">Ainda não existem registos.</p>
    <?php else: ?>
      <table class="tabela">
        <thead>
          <tr>
            <?php foreach ($colunas as $col): ?>
              <th><?= e($col['label']) ?></th>
            <?php endforeach; ?>
            <th class="col-acao"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($linhas as $linha): ?>
            <tr>
              <?php foreach ($colunas as $col): ?>
                <?php $campo = $col['campo']; $tipo = $col['tipo'] ?? 'texto'; ?>
                <td class="<?= !empty($col['classe']) ? e($col['classe']) : '' ?>">
                  <?php if ($tipo === 'imagem'): ?>
                    <?php if (!empty($linha[$campo])): ?>
                      <img src="<?= e(imagem_url((string) $linha[$campo], 'Imagem')) ?>" alt="">
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  <?php elseif ($tipo === 'booleano'): ?>
                    <span class="etiqueta etiqueta-<?= !empty($linha[$campo]) ? 'ativo' : 'inativo' ?>">
                      <?= !empty($linha[$campo]) ? 'Activo' : 'Inactivo' ?>
                    </span>
                  <?php else: ?>
                    <?= e((string) $linha[$campo]) ?>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
              <td class="col-acao">
                <a href="?acao=editar&id=<?= (int) $linha['id'] ?>" class="link-acao">Editar</a>
                <?php if (admin_pode_apagar()): ?>
                  <a href="?acao=eliminar&id=<?= (int) $linha['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                     class="link-acao link-acao-perigo"
                     data-confirmar="Eliminar este registo?">Eliminar</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<?php endif; ?>

<?php require __DIR__ . '/footer.php'; ?>