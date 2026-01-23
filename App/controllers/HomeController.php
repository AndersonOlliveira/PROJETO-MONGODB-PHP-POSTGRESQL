<?php



class HomeController extends Controller {
    


    public function index() {
     
        $list_data_mongo = $this->model('instance');

        $model = $list_data_mongo->all();


        $this->view('home', ['usuarios' => $model]);

    }

    public function listar(){

        return $this->view('listar');
        
    }
}