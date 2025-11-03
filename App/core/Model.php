<?php

 require_once __DIR__ . '/Env.php';

class Model {
    protected $db;

    protected $dados;
    public function __construct() {

        static $loaded = false;
       
        if(!$loaded){
           
            Env::load(__DIR__ . '../../../.env');
            $loaded = true;

        }

        $host = getenv('DB_HOST');
        $dbname = getenv('DB_DATA_BASE');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');
        $port = getenv('DB_PORT');
        $charset = getenv('DB_CHARSET') ?: 'utf8';

        
        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$pass";
            $pdo = new PDO($dsn);
             // Conecta ao banco de dados
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "Connected to PostgreSQL successfully!";
            // $this->db = new PDO($dsn, $user, $pass);
            // $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }
}