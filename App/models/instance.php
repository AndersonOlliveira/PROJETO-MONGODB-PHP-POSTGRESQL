
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

    public function __construct()
    {
        $conn = MongoConect::getInstance();
        $this->manager = $conn->getManager();
        $this->dbname = $conn->getDBName();
        $this->collection = $conn->getDBColetion();
        $this->collection_json = $conn->getDBColetion_json();
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

            $filter = ['id_processo' => $id, 'transacao_id' => $id_transacao];
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
        // $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection_json}", $query);
        $results = iterator_to_array($cursor);;
        // $results = $cursor->toArray();        
        return $results ?? null;
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
        foreach ($data as $dados) {

            if (isset($daods['transacao_id']))
                continue;

            $filter = ['transacao_id' => $dados['transacao_id']];

            $inser = ['$set' => $dados];

            $bulk->update(
                $filter,
                $inser,
                ['upsert' => true, 'multi' => false]
            );

            // $bulk->insert($dados);
        }
        if (count($data) > 0) {

            return $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection_json}", $bulk);
        }
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



    public function data_all()
    {

        $option = ['limit' => 8];

        $query = new MongoDB\Driver\Query([], $option);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection_json}", $query);
        return $cursor->toArray();
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
}
