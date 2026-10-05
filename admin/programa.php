<?php
$crud = [
    'tabela'   => 'programa_dias',
    'titulo'   => 'Programa — Dias e Flyers',
    'singular' => 'Dia',
    'subpasta' => 'programa',
    'ordem'    => 'ordem ASC',
    'colunas'  => [
        ['campo' => 'numero', 'label' => 'Dia'],
        ['campo' => 'data',   'label' => 'Data'],
        ['campo' => 'semana', 'label' => 'Dia da semana'],
        ['campo' => 'flyer',  'label' => 'Cartaz', 'tipo' => 'imagem', 'classe' => 'col-img'],
    ],
    'campos'   => [
        ['nome' => 'numero', 'label' => 'Dia', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true],
        ['nome' => 'data', 'label' => 'Data', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true],
        ['nome' => 'semana', 'label' => 'Dia da semana', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true],
        ['nome' => 'intro', 'label' => 'Descrição', 'tipo' => 'texto', 'obrigatorio' => true, 'max' => 255],
        ['nome' => 'flyer', 'label' => 'Cartaz oficial', 'tipo' => 'imagem'],
        ['nome' => 'ordem', 'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio'],
    ],
    'campo_imagem' => 'flyer',
];
require __DIR__ . '/inc/crud.php';
