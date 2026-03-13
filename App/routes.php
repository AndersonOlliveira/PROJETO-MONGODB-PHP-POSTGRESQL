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
    '/Lista_json' => ['ProcessController', 'get_all_json'],
    '/api/get_dados/{id}' => ['ApiController', 'lista_dados'],
    '/api/up_dados_paralisar' => ['ApiController', 'inserir_info_paralizados_true'],
    '/api/up_dados_cancelar' => ['ApiController', 'inserir_info_cancelados_true'],
    '/api/get_upDados' => ['ApiController', 'inserir_info_paralizados'],
    '/api/up_dados_reprocessar' => ['ApiController', 'inserir_info_paralizados_reprocessar'],
    '/api/verify_data_reprocess/{id}/{contrato}' => ['ApiController', 'verificar_data_reprocessar'],
    '/api/get_fingers/{id}' => ['ApiController', 'push_fingers'],
    '/api/alter_status_dies' => ['ApiController', 'push_status_dies'],
    '/api/teste' => ['ApiController', 'testes'],
    '/api/get_info_reprocess/{id}' => ['ApiController', 'info_reprocess'],
    '/api/get_info_paralizar/{id}' => ['ApiController', 'info_paralizar_die'],
    '/api/reprocess_jobs/{id}' => ['ApiController', 'gerar_novo_arquivo']
    // '/api/alter_status_die/{id}/{contrato}' => ['ApiController', 'push_status_die']

];
