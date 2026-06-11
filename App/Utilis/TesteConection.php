<?php



class TesteConection extends Controller
{


    protected $db;


    public function __construct()
    {

        $model = new Model();
        $this->db = $model->getConnection();
    }
    public function conection()
    {
        echo "<pre>";

        print_r($this->db);
    }
}
