<?php

namespace Controllers;

use Exception;

class Rota {

    private $rotaAtualRequisicao = "";
    private $tipoRequisicao = "";

    public function __construct()
    {
        // obter a rota atual e o tipo de requisição http
        $this->rotaAtualRequisicao = $_SERVER["PATH_INFO"];
        // obter o tipo de requisição
        $this->tipoRequisicao = $_SERVER["REQUEST_METHOD"];
    }

    private function validarControllerExiste($controller) {
        $caminho = __DIR__ . $controller . ".php";
        $caminho = str_replace("ControllersControllers", "Controllers", $caminho);
        $caminho = str_replace("\\", "/", $caminho);

        if (is_file($caminho)) {

            return true;
        }

        return false;
    }

    // tratar requisições http-get
    public function get($endpoint, $controller, $action) {

        if (empty($endpoint)) {

            throw new Exception("Informe o endpoint!");
        }

        if (empty($controller)) {

            throw new Exception("Informe a controller!");
        }

        if (empty($action)) {   

            throw new Exception("Informe a action da controller!");
        }

        // validar se existem a controller em questão
        if (!$this->validarControllerExiste($controller)) {

            throw new Exception("A controller " . $controller . " não existe!");
        }

        // validar se a controller possui a action em questão

        // validar se o endpoint bate com o endpoint atual
        if ($this->rotaAtualRequisicao === $endpoint
        && $this->tipoRequisicao === "GET") {
            $controllerRequisicao = new $controller();
            $controllerRequisicao->$action();
        }

    }

    // tratar requisições http-post
    public function post($endpoint, $controller, $action) {
        
    }

}