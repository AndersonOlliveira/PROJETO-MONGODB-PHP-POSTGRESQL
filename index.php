 <?php


    date_default_timezone_set('America/Sao_Paulo');


    require_once 'App/core/App.php';
    require_once 'App/core/Controller.php';
    require_once 'App/core/Model.php';
    require_once 'App/Utilis/Arquivos.php';


    set_time_limit(0);

    function logInfo($mensagem)
    {
        echo "[" . date('H:i:s') . "] $mensagem\n";
    }

    $tempo_esperara = 20;
    $id = null;
    $quantidade = 20;
    // logInfo("Iniciando a aplicação...");
    $app = new App();


    if (php_sapi_name() == 'cli') {
        while (true) {

            try {

                logInfo('Executando o Loop da Aplicação...');
                sleep($tempo_esperara);

                $app->processar($id, $quantidade);

                logInfo("Aguardando {$tempo_esperara} segundos para a próximo interação...");
                sleep($tempo_esperara);
            } catch (Throwable $e) {

                logInfo(
                    "ERRO: " . $e->getMessage()
                );

                logInfo(
                    "Arquivo: " . $e->getFile() .
                        " | Linha: " . $e->getLine()
                );

                logInfo(
                    "Trace: " . $e->getTraceAsString()
                );

                logInfo(
                    "Aguardando {$tempo_esperara} segundos antes de tentar novamente..."
                );

                // sleep($tempo_esperara);

                logInfo("Erro ao executar a aplicação: " . $e->getMessage());
                sleep($tempo_esperara);
            }
        }
    } else {

        // $app->processar(null, $quantidade);
    }
