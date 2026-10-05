<?php
$crud = [
    'tabela'   => 'modalidades',
    'titulo'   => 'Modalidades de Participação',
    'singular' => 'Modalidade',
    'ordem'    => 'ordem ASC',
    'colunas'  => [
        ['campo' => 'letra', 'label' => 'Letra'],
        ['campo' => 'titulo', 'label' => 'Título'],
        ['campo' => 'descricao', 'label' => 'Descrição'],
        ['campo' => 'ordem', 'label' => 'Ordem'],
    ],
    'campos'   => [
        ['nome' => 'letra', 'label' => 'Letra', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true, 'max' => 4],
        ['nome' => 'titulo', 'label' => 'Título', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true],
        ['nome' => 'descricao', 'label' => 'Descrição', 'tipo' => 'textarea', 'obrigatorio' => true],
        ['nome' => 'ordem', 'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio'],
    ],
];
require __DIR__ . '/inc/crud.php';
