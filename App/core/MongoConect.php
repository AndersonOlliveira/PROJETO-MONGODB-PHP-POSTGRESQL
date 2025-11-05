<?php


use MongoDB\Driver\Manager;
use MongoDB\Driver\BulkWrite;

class MongoConect
{
    private static $instance = null;
    private $client;
    private $manager;
    private $dbname;
    private $db_colletion;
    private $db_colletion_json;

    private function __construct()
    { 

        //chamo o arquivo .env que contem as infos de conexoes
        Env::load(__DIR__ . '/../../.env');

        $host = getenv('BD_MONGO_HOST');
        $port = getenv('BD_MONGO_PORT') ?: 27017;
        $user = getenv('BD_MONGO_USER');
        $pass = getenv('BD_MONGO_PASS');
        $this->dbname = getenv('BD_MONGO_BD_NAME');
        $this->db_colletion = getenv('BD_MONGO_BD_COLLETION');
        $this->db_colletion_json = getenv('BD_MONGO_BD_COLLETION_JSON');

        $auth = $user ? "$user:$pass@" : "";
        $uri = "mongodb://{$auth}{$host}:{$port}";

        try {

            $this->manager = new Manager($uri);

        } catch (Exception $e) {
          
            die("Erro ao conectar ao MongoDB: " . $e->getMessage());
        }
    }


    public function saveLog($data)
    {
        $bulk = new BulkWrite;
        $bulk->insert($data);

        try {
            // Insere no banco/coleção
            $this->manager->executeBulkWrite('nome_banco.nome_tabela_log', $bulk);
        } catch (MongoDB\Driver\Exception\Exception $e) {
            // Lidar com erros
            echo "Erro ao salvar log: " . $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new MongoConect();
        }
        return self::$instance;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function getDBName()
    {
        return $this->dbname;
    } 
      public function getDBColetion()
    {
        return $this->db_colletion;
    }  public function getDBColetion_json()
    {
        return $this->db_colletion_json;
    }
}
