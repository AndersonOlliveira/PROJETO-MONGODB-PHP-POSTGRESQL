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
    private $db_colletion_info;
    private $db_colletion_jobs;
    private $manager_local;
    private $db_colletion_json_dados;
    private $db_colletion_json_dados_reprocess;
    private $db_colletion_json_dados_paralizar;


    private function __construct()
    {

        //chamo o arquivo .env que contem as infos de conexoes
        Env::load(__DIR__ . '/../../.env');

        $host = getenv('BD_MONGO_HOST');
        $port = getenv('BD_MONGO_PORT') ?: 27017;
        $user = getenv('BD_MONGO_USER');
        $pass = getenv('BD_MONGO_PASS');
        $BD_MONGO_BD_AUTH_SOURCE = getenv('BD_MONGO_BD_AUTH_SOURCE');
        $this->dbname = getenv('BD_MONGO_BD_NAME');
        $this->db_colletion = getenv('BD_MONGO_BD_COLLETION');
        $this->db_colletion_json = getenv('BD_MONGO_BD_COLLETION_JSON');
        $this->db_colletion_info = getenv('BD_MONGO_BD_COLLETION_INFO');
        $this->db_colletion_jobs = getenv('BD_MONGO_BD_COLLETION_JOBS');
        $this->db_colletion_json_dados = getenv('BD_MONGO_BD_COLLETION_JSON_DADOS');
        $this->db_colletion_json_dados_reprocess = getenv('BD_MONGO_BD_COLLETION_JSON_DADOS_REPROCESS');
        $this->db_colletion_json_dados_paralizar = getenv('BD_MONGO_BD_COLLETION_JSON_DADOS_PARALIZAR');



        $auth = $user ? "$user:$pass@" : "";

        $this->manager_local = "$host:$port";

        // $uri = "mongodb://{$auth}{$host}:{$port}";
        $uri = "mongodb://{$auth}{$host}:{$port}/{$user}?authSource={$BD_MONGO_BD_AUTH_SOURCE}";

        $options = [
            "tls" => true,
            "tlsAllowInvalidCertificates" => true, // se o certificado for autoassinado
        ];
        // MONGO_URI_AUTH = f"mongodb://{MONGO_USER}:{MONGO_PASS}@{MONGO_HOST}:{MONGO_PORT}/{MONGO_USER}?authSource={BD_MONGO_BD_AUTH_SOURCE}"


        try {
            $this->manager = new Manager($uri, $options);
            echo "estou conectado ao mongoDB\n";
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
    }
    public function getDBColetion_json()
    {
        return $this->db_colletion_json;
    }
    public function getDBColetion_info()
    {
        return $this->db_colletion_info;
    }

    public function getManager_local()
    {
        return $this->manager_local;
    }


    public function getDBColetion_jobs()
    {
        return $this->db_colletion_jobs;
    }

    public function getDBColetion_jobs_dados_json()
    {
        return $this->db_colletion_json_dados;
    }

    public function getDBColetion_jobs_dados_json_reprocess()
    {
        return $this->db_colletion_json_dados_reprocess;
    }
    public function getDBColetion_jobs_dados_paralizar()
    {
        return $this->db_colletion_json_dados_paralizar;
    }
}
