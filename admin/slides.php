<?php
$crud = [
    'tabela'   => 'slides',
    'titulo'   => 'Slides do Hero',
    'singular' => 'Slide',
    'subpasta' => 'slides',
    'ordem'    => 'ordem ASC, id ASC',
    'colunas'  => [
        ['campo' => 'numero',  'label' => 'Nº'],
        ['campo' => 'titulo',  'label' => 'Título'],
        ['campo' => 'imagem',  'label' => 'Imagem', 'tipo' => 'imagem', 'classe' => 'col-img'],
        ['campo' => 'ordem',   'label' => 'Ordem'],
        ['campo' => 'ativo',   'label' => 'Estado', 'tipo' => 'booleano'],
    ],
    'campos'   => [
        ['nome' => 'numero', 'label' => 'Número', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true, 'max' => 10],
        ['nome' => 'titulo', 'label' => 'Título', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true, 'max' => 180],
        ['nome' => 'legenda', 'label' => 'Legenda da figura', 'tipo' => 'texto', 'max' => 200],
        ['nome' => 'imagem', 'label' => 'Imagem', 'tipo' => 'imagem'],
        ['nome' => 'ordem', 'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio', 'padrao' => 0],
        ['nome' => 'ativo', 'label' => 'Activo', 'tipo' => 'booleano', 'largura' => 'meio', 'padrao' => 1],
    ],
    'campo_imagem' => 'imagem',
];
require __DIR__ . '/inc/crud.php';
