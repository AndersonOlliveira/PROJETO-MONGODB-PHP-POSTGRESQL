
<?php
//como náo tem o autoLoad precisa passar o nome do aquivo de conexao para poder usar dentro do porjeto
require_once __DIR__ . '../../core/MongoConect.php';

use MongoDB\Builder\Expression;

class instance extends MongoConect
{

    private $manager;
    private $dbname;
    private $collection;
    private $collection_json;
    private $collection_info;
    private $db_colletion_jobs;
    private $manager_local;
    private $db_colletion_json_dados;
    private $db_colletion_json_dados_paralizars;
    private $db_colletion_json_dados_reprocess;





    public function __construct()
    {
        $conn = MongoConect::getInstance();
        $this->manager = $conn->getManager();
        $this->dbname = $conn->getDBName();
        $this->collection = $conn->getDBColetion();
        $this->manager_local = $conn->getManager_local();
        $this->collection_json = $conn->getDBColetion_json();
        $this->collection_info = $conn->getDBColetion_info();
        $this->db_colletion_jobs = $conn->getDBColetion_jobs();
        $this->db_colletion_json_dados = $conn->getDBColetion_jobs_dados_json();
        $this->db_colletion_json_dados_reprocess = $conn->getDBColetion_jobs_dados_json_reprocess();
        $this->db_colletion_json_dados_paralizars = $conn->getDBColetion_jobs_dados_paralizar();
    }

    public function all()
    {
        $query = new MongoDB\Driver\Query([]);
        $filter = ['limit' => 1];
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
        return $cursor->toArray();
    }

    public function findById($id, $id_transacao)
    {
        //verifica se e um hash id do mongo
        if (preg_match('/^[a-f0-9]{24}$/i', $id)) {

            $filter = ['id_processo' => new MongoDB\BSON\ObjectId($id)];
        } else {

            $filter = [
                'id_processo' => (int) $id,
                'transacao_id' => (int) $id_transacao
            ];
        }



        $option = ['projection' => [
            'configuracao_json' => 1,
            'data_cadastro' => 1,
            'transacao_id' => 1,
            'id_processo' => 1,
            'campo_aquisicao' => 1,
            'status' => 1,
            'resposta_json' => 1,
            'resposta' => 1,
            'new_status' => 1,
            'sucesso' => 1,
            'id' => 1,
            '_id' => 0
        ]];

        $query = new MongoDB\Driver\Query($filter, $option);
        echo "<pre>";
        echo "que dados vem results?\n";


        print_r($query);
        print_r($this->dbname);
        print_r($this->collection_json);

        // $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection_json}", $query);

        $results = iterator_to_array($cursor);


        echo "<pre>";
        echo "que dados vem results?\n";


        print_r($results);

        // die();

        // $results = $cursor->toArray();        
        return $results ?? null;
    }

    public function findByMultiple($dados)
    {

        echo "estou chamando o findByMultiple\n";

        print_R($dados);
        //verifica se e um hash id do mongo
        $filtros = [];

        foreach ($dados as $values) {

            if (preg_match('/^[a-f0-9]{24}$/i', $values['processo_id'])) {

                $filtros[] = [
                    'id_processo' => new MongoDB\BSON\ObjectId($values['processo_id'])
                ];
            } else {

                $filtros[] = [
                    'id_processo'  => $values['processo_id'],
                    'transacao_id' => $values['transacao_id']
                ];
            }
        }

        if (empty($filtros)) {
            return [];
        }

        $options = [
            'projection' => [
                'configuracao_json' => 1,
                'data_cadastro' => 1,
                'transacao_id' => 1,
                'id_processo' => 1,
                'campo_aquisicao' => 1,
                'status' => 1,
                'resposta_json' => 1,
                'resposta' => 1,
                'new_status' => 1,
                'sucesso' => 1,
                'id' => 1,
                '_id' => 0
            ]
        ];

        $query = new MongoDB\Driver\Query(
            ['$or' => $filtros],
            $options
        );

        $cursor = $this->manager->executeQuery(
            "{$this->dbname}.{$this->collection_json}",
            $query
        );

        return $cursor->toArray();
    }

    public function listarDadosDosProcessos()
    {
        $option = [
            'projection' => [
                'id_processo' => 1,
                'status' => 1,
                'resposta_json' => 1,
                'new_status' => 1,
                'sucesso' => 1,
                '_id' => 0
            ]
        ];

        $query = new MongoDB\Driver\Query([], $option);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection_json}", $query);
        return iterator_to_array($cursor);
    }
    public function insert($data)
    {

        $bulk = new MongoDB\Driver\BulkWrite;
        $operacoes = 0;

        foreach ($data as $dados) {

            if (!isset($dados['transacao_id'])) {
                continue;
            }

            $filter = ['transacao_id' => $dados['transacao_id']];
            $inser  = ['$set' => $dados];

            $bulk->update(
                $filter,
                $inser,
                ['upsert' => true, 'multi' => false]
            );

            $operacoes++;
        }

        // Só executa se tiver operações
        if ($operacoes > 0) {
            return $this->manager->executeBulkWrite(
                "{$this->dbname}.{$this->collection_json}",
                $bulk
            );
        }

        return false;
    }

    public function update($id, $data)
    {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data],
            ['multi' => false, 'upsert' => false]
        );
        return $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection}", $bulk);
    }

    public function delete($id)
    {

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete(['_id' => new MongoDB\BSON\ObjectId($id)], ['limit' => 1]);

        try {
            $result = $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection_json}", $bulk);

            if ($result->getDeletedCount() > 0) {
                echo "Documento deletado com sucesso!\n";
            } else {
                echo " Nenhum documento encontrado com esse ID.\n";
            }

            return $result;
        } catch (MongoDB\Driver\Exception\Exception $e) {

            echo " Erro ao deletar documento: " . $e->getMessage() . "\n";
        }
    }

    public function delete_all($dados)
    {

        $bulk = new MongoDB\Driver\BulkWrite;

        foreach ($dados as $id) {

            $bulk->delete(['_id' => new MongoDB\BSON\ObjectId($id)]);
        }

        try {
            //deleta em lote os ids
            $result = $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection_json}", $bulk);

            if ($result->getDeletedCount() > 0) {
                echo "Documento deletado com sucesso!\n";
            } else {
                echo " Nenhum documento encontrado com esse ID.\n";
            }

            return $result;
        } catch (MongoDB\Driver\Exception\Exception $e) {

            echo " Erro ao deletar documento: " . $e->getMessage() . "\n";
        }
    }



    public function data_alla()
    {


        $bulk = new MongoDB\Driver\BulkWrite();


        try {

            $manager = new MongoDB\Driver\Manager("mongodb://{$this->manager_local}");

            $bulk->delete([]);

            // $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            // $result = $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection_json}", $bulk, $writeConcern);
            $result = $manager->executeBulkWrite("{$this->dbname}.{$this->collection_json}", $bulk);


            if ($result->getDeletedCount() > 0) {
                echo "Documento(s) deletado(s) com sucesso!\n";
            } else {
                echo "Nenhum documento encontrado para deletar.\n";
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erro ao executar operação: " . $e->getMessage() . "\n";
        }
    }


    public function get_size_database()
    {
        try {
            $command = new MongoDB\Driver\Command(['dbStats' => 1]);
            $stats = $this->manager->executeCommand($this->dbname, $command);
            $statsArray = $stats->toArray();
            if (count($statsArray) > 0) {
                $sizeInBytes = $statsArray[0]->dataSize;
                return $sizeInBytes;
            } else {
                echo "Nenhum dado retornado para as estatísticas do banco de dados.\n";
                return null;
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erro ao obter estatísticas do banco de dados: " . $e->getMessage() . "\n";
            return null;
        }
    }

    public function get_qta_row()
    {
        try {


            $command = new MongoDB\Driver\Command([
                'count' => $this->collection_json
            ]);

            $result = $this->manager->executeCommand($this->dbname, $command);
            $response = current($result->toArray());

            if ($response->n > 0) {
                return $response->n;
            } else {
                echo "Nenhum dado retornado para as estatísticas do banco de dados.\n";
                return null;
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erro ao obter estatísticas do banco de dados: " . $e->getMessage() . "\n";
            return null;
        }
    }


    public function up_valor_modulos($data)
    {

        echo "<pre>";
        print_R($data);

        $dados_final = [
            'processo_id'       => (int) $data['processo_id'],
            'valor_original'    => (float) $data['valor_original'],
            'valor_geral'       => (float) $data['valor_geral'],
            'data_atualizacao' => $data['data_atualizacao'],
            'dados'             => $data[0]['dados']
        ];


        $bulk = new MongoDB\Driver\BulkWrite;


        if (isset($dados_final['processo_id'])) {
            $filter = ['processo_id' => $dados_final['processo_id']];


            $query = new MongoDB\Driver\Query($filter);
            $cursor = $this->manager->executeQuery(
                "{$this->dbname}.{$this->collection_info}",
                $query
            );


            $resultado = $cursor->toArray();
            $jaExiste = false;

            if (count($resultado) > 0) {
                $jaExiste = true;
            }

            if ($jaExiste) {
                $info_return =  [
                    'status'  => false,
                    'message' => 'Registro já existe, não foi inserido'
                ];
            }

            if (!$jaExiste) {
                $bulk->insert($dados_final);

                $result = $this->manager->executeBulkWrite(
                    "{$this->dbname}.{$this->collection_info}",
                    $bulk
                );

                echo "Inseridos:  " . $result->getUpsertedCount() . "\n";
                echo "Atualizados: " . $result->getModifiedCount() . "\n";

                $info_return =  [
                    'status'  => true,
                    'message' => 'Registro inserido com sucesso'
                ];
            }

            return $info_return;

            // $update = ['$set' => $data];

            // $bulk->update(
            //     $filter,
            //     $update,
            //     ['upsert' => true, 'multi' => false]
            // );


            // if (!empty(($data))) {

            //     $result = $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection_info}", $bulk);
            // }

            // echo "Inseridos:  " . $result->getUpsertedCount() . "\n";
            // echo "Atualizados: " . $result->getModifiedCount() . "\n";
        }


        // $filter = ['processo_id' => $dados['processo_id']];

        // $inser = ['$set' => $data];

        // $bulk->update(
        //     $filter,
        //     $inser,
        //     ['upsert' => true, 'multi' => false]
        // );

        // $bulk->insert($data);
    }

    public function inset_json_dados($dadosJson, $nome_arquivo)
    {


        $db_conect = $nome_arquivo == 'infoReprocess.json' ? $this->db_colletion_json_dados_reprocess : $this->db_colletion_json_dados;
        // echo "estou chamando dentro da instancia\n";

        // print_r("Coleção: {$this->dbname}.{$this->db_colletion_json_dados}\n");

        $bulk = new MongoDB\Driver\BulkWrite;
        $operacoes = 0;

        $data  = json_decode($dadosJson, true);

        foreach ($data as $dados) {

            if (!isset($dados['id_process'])) {
                continue;
            }

            $filter = ['id_process' => $dados['id_process']];
            $inser  = ['$set' => $dados];

            $bulk->update(
                $filter,
                $inser,
                ['upsert' => true, 'multi' => false]
            );

            $operacoes++;
        }

        try {

            if ($operacoes > 0) {

                $result = $this->manager->executeBulkWrite(
                    "{$this->dbname}.{$db_conect}",
                    $bulk
                );

                return [
                    'success' => true,
                    'inserted' => $result->getInsertedCount(),
                    'modified' => $result->getModifiedCount(),
                    'upserted' => $result->getUpsertedCount(),
                    'matched'  => $result->getMatchedCount(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Nenhuma operação executada'
            ];
        } catch (MongoDB\Driver\Exception\Exception $e) {

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    //para inserir o paralizar pegando o finger e data e e id do processo 

    public function insert_all_paralizar($dadosJson)
    {


        $bulk = new MongoDB\Driver\BulkWrite;
        $operacoes = 0;

        $data  = json_decode($dadosJson, true);

        if (!is_array($data)) {
            return [
                'success' => false,
                'message' => 'JSON inválido ou não é um array'
            ];
        }


        $filter = ['id_processo' => $data['id_processo']];
        $inser  = ['$set' => $data];

        $bulk->update(
            $filter,
            $inser,
            ['upsert' => true, 'multi' => false]
        );

        $operacoes++;

        try {

            if ($operacoes > 0) {

                $result = $this->manager->executeBulkWrite(
                    "{$this->dbname}.{$this->db_colletion_json_dados_paralizars}",
                    $bulk
                );

                return [
                    'success' => true,
                    'inserted' => $result->getInsertedCount(),
                    'modified' => $result->getModifiedCount(),
                    'upserted' => $result->getUpsertedCount(),
                    'matched'  => $result->getMatchedCount(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Nenhuma operação executada'
            ];
        } catch (MongoDB\Driver\Exception\Exception $e) {

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }


    //BUSCO A DATA PARA NO MONGO PARA SANER 

    public function get_data_paralizar()
    {
        $option = [
            'projection' => [
                'id_processo' => 1,
                'contrato' => 1,
                'paralisado' => 1,
                'data' => 1,
                'data_finalizacao' => 1,
                '_id' => 0
            ]
        ];

        $query = new MongoDB\Driver\Query([], $option);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->db_colletion_json_dados_paralizars}", $query);
        return iterator_to_array($cursor);
    }
}
