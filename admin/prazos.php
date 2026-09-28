<?php
$crud = [
    'tabela'   => 'prazos',
    'titulo'   => 'Prazos de Submissão',
    'singular' => 'Prazo',
    'ordem'    => 'ordem ASC',
    'colunas'  => [
        ['campo' => 'data', 'label' => 'Data'],
        ['campo' => 'titulo', 'label' => 'Título'],
        ['campo' => 'destaque', 'label' => 'Destaque', 'tipo' => 'booleano'],
        ['campo' => 'ordem', 'label' => 'Ordem'],
    ],
    'campos'   => [
        ['nome' => 'data', 'label' => 'Data', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true],
        ['nome' => 'titulo', 'label' => 'Título', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true],
        ['nome' => 'destaque', 'label' => 'Destacar visualmente', 'tipo' => 'booleano'],
        ['nome' => 'ordem', 'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio'],
    ],
];
require __DIR__ . '/inc/crud.php';
