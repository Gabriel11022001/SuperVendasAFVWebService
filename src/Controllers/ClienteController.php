<?php

namespace Controllers;

use Servico\ClienteServico;
use Servico\IClienteServico;

class ClienteController {

    private IClienteServico $clienteServico;

    public function __construct()
    {
        $this->clienteServico = new ClienteServico();
    }

    // buscar clientes de forma paginada
    public function buscarClientes() {

        return $this->clienteServico->buscarClientes();
    }

    // cadastrar cliente
    public function cadastrarCliente() {

        return $this->clienteServico->cadastrarCliente();
    }

    // editar cliente
    public function editarCliente() {

        return $this->clienteServico->editarCliente();
    }

    // deletar cliente
    public function deletarCliente() {

        return $this->clienteServico->deletarCliente();
    }

    // alterar o status do cliente
    public function alterarStatusCliente() {

        return $this->clienteServico->alterarStatusCliente();
    }

    // buscar o cliente pelo id
    public function buscarClientePeloId() {
        
        return $this->clienteServico->buscarClientePeloId();
    }

}