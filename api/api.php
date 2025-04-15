<?php

use Controllers\ClienteController;
use Controllers\Rota;

require_once "autoload.php";
require_once __DIR__ . "/../src/Utils/getParametro.php";

try {   
    $rota = new Rota();

    switch ($rota->getRotaAtual()) {
        case "/":
            $rota->get("/");
            break;
        case "/clientes":
            // buscar clientes paginado
            $rota->get("/clientes", ClienteController::class, "buscarClientes");
            break;
        case "/clientes/buscar-pelo-id":
            // buscar cliente pelo id
            $rota->get("/clientes/buscar-pelo-id", ClienteController::class, "buscarClientePeloId");
            break;
        case "/clientes/cadastrar":
            // cadastrar cliente
            $rota->post("/clientes/cadastrar", ClienteController::class, "cadastrarCliente");
            break;
        default:
            $rota->get("/404");
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}