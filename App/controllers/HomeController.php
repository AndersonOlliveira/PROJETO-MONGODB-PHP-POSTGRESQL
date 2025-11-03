<?php



class HomeController extends Controller {
    


    public function index() {
        // $usuarioModel = $this->model('Usuario');
        $list_data_mongo = $this->model('instance');

        // echo "<pre>";
        // echo "/<pre>";
        // print_r($list_data_mongo->find());
        // $usuarios = $usuarioModel->listarUsuarios();
        // echo "<pre>";
        $model = $list_data_mongo->all();


        // print_r($model);
        // echo "</pre>";
      

    //     $mongoModel = new \App\models\instance(); 
    
    // // 2. Chama o método de listagem do Model
    // $logs = $mongoModel->listarLogs();

    // print_r($logs);

    // $this->view('home', ['logs' => $logs]);

        // print_r($model);

        $this->view('home', ['usuarios' => $model]);

        // $this->view('home', ['usuarios' => $model]);
    }

    public function listar(){

        return $this->view('listar');
        
    }
}