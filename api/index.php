<?php

use Controllers\ClienteController;
use Controllers\Rota;

require_once "autoload.php";

try {   
    $rota = new Rota();

    // buscar clientes
    $rota->get("/clientes", ClienteController::class, "buscarClientes");
    // buscar cliente pelo id
    $rota->get("/clientes/buscar-pelo-id", ClienteController::class, "buscarClientePeloId");
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}