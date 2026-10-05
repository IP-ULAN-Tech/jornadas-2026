<?php
$crud = [
    'tabela'   => 'eixos',
    'titulo'   => 'Eixos Temáticos',
    'singular' => 'Eixo',
    'ordem'    => 'ordem ASC, id ASC',
    'colunas'  => [
        ['campo' => 'numero', 'label' => 'Nº'],
        ['campo' => 'titulo', 'label' => 'Título'],
        ['campo' => 'ordem',  'label' => 'Ordem'],
        ['campo' => 'ativo',  'label' => 'Estado', 'tipo' => 'booleano'],
    ],
    'campos'   => [
        ['nome' => 'numero', 'label' => 'Número', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true, 'max' => 10],
        ['nome' => 'titulo', 'label' => 'Título', 'tipo' => 'texto', 'obrigatorio' => true, 'max' => 180],
        ['nome' => 'ordem',  'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio', 'padrao' => 0],
        ['nome' => 'ativo',  'label' => 'Activo', 'tipo' => 'booleano', 'largura' => 'meio', 'padrao' => 1],
    ],
];
require __DIR__ . '/inc/crud.php';
