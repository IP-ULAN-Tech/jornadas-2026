<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
$nivel = admin_nivel();

$menu = [];

if (admin_pode_editar_conteudo()) {
    $menu['Conteúdo do Site'] = [
        'dashboard.php'  => 'Painel',
        'slides.php'     => 'Slides do Hero',
        'sobre.php'      => 'Sobre as Jornadas',
        'eixos.php'      => 'Eixos Temáticos',
        'programa.php'   => 'Programa',
        'inova.php'      => 'INOVA IPS',
        'premiacao.php'  => 'Premiação',
        'galeria.php'    => 'Galeria',
    ];

    $menu['Estrutura do Evento'] = [
        'modalidades.php'  => 'Modalidades',
        'prazos.php'       => 'Prazos de Submissão',
        'cronograma.php'   => 'Cronograma',
        'local.php'        => 'Local do Evento',
    ];
}

$menu['Submissões'] = [
    'submissoes.php' => 'Submissões Recebidas',
];

if (admin_pode_gerir_utilizadores() || admin_pode_ver_logs()) {
    $grupoSistema = [];
    if (admin_pode_editar_conteudo()) {
        $grupoSistema['configuracoes.php'] = 'Configurações';
    }
    if (admin_pode_gerir_utilizadores()) {
        $grupoSistema['administradores.php'] = 'Administradores';
        $grupoSistema['logs.php']            = 'Registo de Actividade';
    }
    if ($grupoSistema) {
        $menu['Sistema'] = $grupoSistema;
    }
}

$admin = admin_actual();
?>
<aside class="barra-lateral">
  <div class="barra-lateral-marca">
    <p class="barra-lateral-inst">Instituto Politécnico de Saurimo</p>
    <p class="barra-lateral-titulo">Jornadas Técnico-Científicas<br>Edição 2026</p>
  </div>

  <div class="barra-lateral-perfil">
    <p class="barra-lateral-perfil-nome"><?= e($admin['nome'] ?? '') ?></p>
    <p class="barra-lateral-perfil-nivel"><?= e(admin_nome_nivel($nivel)) ?></p>
  </div>

  <?php foreach ($menu as $grupo => $itens): ?>
    <div class="barra-lateral-grupo">
      <p class="barra-lateral-grupo-titulo"><?= e($grupo) ?></p>
      <ul class="barra-lateral-menu">
        <?php foreach ($itens as $href => $nome): ?>
          <li>
            <a href="<?= e($href) ?>" class="<?= $paginaActual === $href ? 'activo' : '' ?>">
              <?= e($nome) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endforeach; ?>

  <div class="barra-lateral-rodape">
    <p>Versão 1.0 · 2026</p>
  </div>
</aside>