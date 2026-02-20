<?php


class ApiController extends Controller
{

    public function lista_dados($id = null)
    {
        echo "<pre>";
        echo "estou chamando dentro da api\n";
        print_r($id);
    }
}
