
<?php
//como náo tem o autoLoad precisa passar o nome do aquivo de conexao para poder usar dentro do porjeto
require_once __DIR__ . '../../core/MongoConect.php';

class instance extends MongoConect  {
  
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
            $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
            return $cursor->toArray();

    }

    public function findById($id)
    {
        //verifica se e um hash id do mongo
        if (preg_match('/^[a-f0-9]{24}$/i', $id)) {
         
            $filter = ['id' => new MongoDB\BSON\ObjectId($id)];
          
        } else {
            $filter = ['id' => $id];
        
        }
     
        
        $option = ['projection' => ['configuracao_json' => 1, 'data_cadastro' => 1,
        'transacao_id' => 1, 'id_processo' => 1, 'campo_aquisicao' => 1, 'status' => 1,
        'resposta_json' => 1, 'resposta' => 1, 
        'new_status' => 1, 'sucesso' => 1, 'id' => 1 ,'_id' => 0]];
    
        $query = new MongoDB\Driver\Query($filter, $option);
        // $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection_json}", $query);
        $results = $cursor->toArray();
        
         return $results ?? null;
    }

    public function insert($data)
    {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($data);
        return $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection}", $bulk);
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
        return $this->manager->executeBulkWrite("{$this->dbname}.{$this->collection}", $bulk);
    }
    }
