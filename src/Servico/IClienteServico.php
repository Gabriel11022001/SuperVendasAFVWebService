<?php

namespace Servico;

interface IClienteServico {

    public function cadastrarCliente();

    public function editarCliente();

    public function buscarClientes();

    public function deletarCliente();

    public function alterarStatusCliente();

    public function buscarClientePeloId();

}