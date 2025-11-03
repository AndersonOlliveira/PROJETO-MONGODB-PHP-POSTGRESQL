
<?php
//como náo tem o autoLoad precisa passar o nome do aquivo de conexao para poder usar dentro do porjeto
require_once __DIR__ . '../../core/MongoConect.php';

class instance extends MongoConect  {
  
    private $manager;
    private $dbname;
    private $collection;

    public function __construct()
    {
        $conn = MongoConect::getInstance();
        $this->manager = $conn->getManager();
        $this->dbname = $conn->getDBName();
        $this->collection = $conn->getDBColetion();
    }

    public function all()
    {
            $query = new MongoDB\Driver\Query([]);
            $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
            return $cursor->toArray();

    }

    public function findById($id)
    {
        $filter = ['_id' => new MongoDB\BSON\ObjectId($id)];
        $query = new MongoDB\Driver\Query($filter);
        $cursor = $this->manager->executeQuery("{$this->dbname}.{$this->collection}", $query);
        $results = $cursor->toArray();
        return $results[0] ?? null;
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
