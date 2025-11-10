<?php


return [
    '/' => ['HomeController', 'index'],
    '/usuarios' => ['UsuarioController', 'listar'],
    '/usuario/{id}' => ['UsuarioController', 'detalhes'],
    '/listar' => ['ListarController', 'listar'],
    '/listar/{id}' => ['ListarController', 'listar_id'],
    '/mongo' => ['ListarController', 'mongo'],
    '/Lista_query' => ['ProcessController', 'get_all_query'],
    '/Lista_teste' => ['ProcessController', 'get_all_teste'],
];