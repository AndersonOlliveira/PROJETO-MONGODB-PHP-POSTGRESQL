<?php
date_default_timezone_set('America/Sao_Paulo');


require_once 'App/core/App.php';
require_once 'App/core/Controller.php';
require_once 'App/core/Model.php';
require_once 'App/Utilis/Arquivos.php';


set_time_limit(0);

function logInfo($mensagem){
    echo "[" . date('H:i:s') . "] $mensagem\n";
}

$tempo_esperara = 60;

logInfo("Iniciando a aplicação...");

while (true){

    try{

        logInfo('Executando o Loop da Aplicação...');
        sleep(2);
        $app = new App();
        $app->processar(null,1000);
        // $app->processar(371);
        logInfo("Aguardando {$tempo_esperara} segundos para a próximo interação...");
        sleep($tempo_esperara);


    }catch(Exception $e){

        logInfo("Erro ao executar a aplicação: " . $e->getMessage());
        sleep($tempo_esperara);
    }
}

?>