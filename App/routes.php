<?php


return [
    '/' => ['HomeController', 'index'],
    '/usuarios' => ['UsuarioController', 'listar'],
    '/usuario/{id}' => ['UsuarioController', 'detalhes'],
    '/listar' => ['ListarController', 'listar'],
    '/listar/{id}' => ['ListarController', 'listar_id'],
    '/teste' => ['ListarController', 'teste_teste'],
    '/mongo' => ['ListarController', 'mongo'],
    '/mongoSize' => ['ListarController', 'mongo_size'],
    '/cpu' => ['ProcessController', 'cpu_server'],
    '/Lista_query' => ['ProcessController', 'get_all_query'],
    '/Lista_teste' => ['ProcessController', 'get_all_teste'],
    '/arquivo' => ['ProcessController', 'teste_envio'],
    '/headers' => ['ProcessController', 'c_headers'],
    '/ler_arquivo' => ['ProcessController', 'ler_arquivo'],
    '/gera_arquivo' => ['ProcessController', 'gerar_arquivo'],
];
