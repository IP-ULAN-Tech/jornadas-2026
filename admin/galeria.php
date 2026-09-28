<?php
$crud = [
    'tabela'   => 'galeria_itens',
    'titulo'   => 'Galeria',
    'singular' => 'Imagem',
    'subpasta' => 'galeria',
    'ordem'    => 'ordem ASC',
    'colunas'  => [
        ['campo' => 'imagem', 'label' => 'Imagem', 'tipo' => 'imagem', 'classe' => 'col-img'],
        ['campo' => 'legenda', 'label' => 'Legenda'],
        ['campo' => 'ordem', 'label' => 'Ordem'],
        ['campo' => 'ativo', 'label' => 'Estado', 'tipo' => 'booleano'],
    ],
    'campos'   => [
        ['nome' => 'imagem', 'label' => 'Imagem', 'tipo' => 'imagem', 'obrigatorio' => true],
        ['nome' => 'legenda', 'label' => 'Legenda', 'tipo' => 'texto', 'max' => 200],
        ['nome' => 'ordem', 'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio'],
        ['nome' => 'ativo', 'label' => 'Activo', 'tipo' => 'booleano', 'largura' => 'meio', 'padrao' => 1],
    ],
    'campo_imagem' => 'imagem',
];
require __DIR__ . '/inc/crud.php';
exigir_editor_ou_super();
