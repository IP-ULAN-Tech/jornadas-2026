<?php
$crud = [
    'tabela'   => 'cronograma',
    'titulo'   => 'Cronograma',
    'singular' => 'Item',
    'ordem'    => 'ordem ASC',
    'colunas'  => [
        ['campo' => 'data', 'label' => 'Data'],
        ['campo' => 'texto', 'label' => 'Texto'],
        ['campo' => 'tipo', 'label' => 'Tipo'],
        ['campo' => 'ordem', 'label' => 'Ordem'],
    ],
    'campos'   => [
        ['nome' => 'data', 'label' => 'Data curta (ex.: 15 SET)', 'tipo' => 'texto', 'largura' => 'meio', 'obrigatorio' => true, 'max' => 20],
        ['nome' => 'texto', 'label' => 'Texto', 'tipo' => 'texto', 'obrigatorio' => true, 'max' => 180],
        ['nome' => 'tipo', 'label' => 'Tipo', 'tipo' => 'select', 'largura' => 'meio',
         'opcoes' => ['normal' => 'Normal', 'evento' => 'Evento (destacado)']],
        ['nome' => 'ordem', 'label' => 'Ordem', 'tipo' => 'numero', 'largura' => 'meio'],
    ],
];
require __DIR__ . '/inc/crud.php';
