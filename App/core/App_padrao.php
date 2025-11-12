<?php

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        // Carrega o arquivo de rotas
        $routes = require 'App/routes.php';

        // Captura a URI atual
        if (php_sapi_name() === 'cli') {
            // Execução no terminal
            $requestUri = '/';
        } else {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        $basePath =   'app-progestor-proscore.hostmundi.com/home/proscore/mvc/';
        $requestUri = str_replace($basePath, '/', $requestUri);
        $requestUri = rtrim($requestUri, '/');

        if ($requestUri == '') {
            $requestUri = '/';
        }

        if (isset($routes[$requestUri])) {
            [$controller, $method] = $routes[$requestUri];
        } else {

            $url = $this->parseUrl();

            $controller = ucfirst($url[0] ?? 'home') . 'Controller';

            //se for enviando um número como parametro ele executa aqui
            if (is_numeric($url[1])) {

                if (isset($url[1]) && is_numeric($url[1])) {
                    $method = ($url[0] ?? 'index') . '_id';


                    $this->params = [$url[1]];
                } else {
                    $method = $url[1] ?? 'index';
                    $this->params = array_slice($url, 2);
                }
            } else {
                if (isset($url[1])) {
                    $id = $url[1];
                    if (preg_match('/^[a-f0-9]{24}$/i', $id)) {
                        $method = ($url[0] ?? 'index') . '_id'; // Ex: listar_id
                        $this->params = [$id];
                    } else {
                        $method = $url[1];
                        $this->params = array_slice($url, 2);
                    }
                } else {
                    $method = 'index';
                    $this->params = [];
                }
            }
        }

        $this->controller = $controller;
        $this->method = $method;

        $controllerPath = "App/controllers/{$this->controller}.php";


        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $this->controller = new $this->controller;

            if (method_exists($this->controller, $this->method)) {
                $result =  call_user_func_array([$this->controller, $this->method], $this->params);


                // if ($result !== null) {
                //     echo "<pre>";
                //     print_r($result);
                //     echo "</pre>";
                // }

            } else {
                echo " Método {$this->method} não encontrado!";
            }
        } else {
            echo "Controller {$this->controller} não encontrado!";
        }
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return ['home'];
    }
}
