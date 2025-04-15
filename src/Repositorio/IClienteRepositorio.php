<?php

namespace Repositorio;

use Models\Cliente;

interface IClienteRepositorio {

    function cadastrarCliente(Cliente $clienteCadastrar);

    function editarCliente(Cliente $clienteEditar);

    function buscarClientes(int $paginaAtual, int $elementosPorPagina);

    function buscarClientePeloId(int $idClienteConsultar);

    function deletarCliente(int $idClienteDeletar);

    function alterarStatusCliente(int $idClienteAlterarStatus, bool $novoStatus);

}