<?php

require_once __DIR__ . '/Env.php';

class Model
{
    protected $db;

    protected $dados;
    public function __construct()
    {

        static $loaded = false;

        if (!$loaded) {
            //precisar ser esta conexao no app http://app-progestor-proscore.hostmundi.com/
            // Env::load(__DIR__ . '/../../.env');
            Env::load(__DIR__ . '/../../.env');
            $loaded = true;
        }

        $host = getenv('DB_HOST');
        $dbname = getenv('DB_DATA_BASE');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');
        $port = getenv('DB_PORT');
        $charset = getenv('DB_CHARSET') ?: 'utf8';


        //VARIAVEL QUE DECODIFICA O HOST
        gethostbyname($host);

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            // $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$pass";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $this->db = $pdo;

            // echo "<pre>";

            // print_r($this->db);
            // Conecta ao banco de dados
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // echo "Connected to PostgreSQL successfully!";
            // $this->db = new PDO($dsn, $user, $pass);
            // $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }
    public function getConnection()
    {
        return $this->db;
    }
}
