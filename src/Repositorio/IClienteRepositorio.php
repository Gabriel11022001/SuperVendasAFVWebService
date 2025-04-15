<?php

namespace Repositorio;

use Models\Cliente;

interface IClienteRepositorio extends IRepositorio {

    function cadastrarCliente(Cliente $clienteCadastrar);

    function editarCliente(Cliente $clienteEditar);

    function buscarClientes(int $paginaAtual, int $elementosPorPagina);

    function buscarClientePeloId(int $idClienteConsultar);

    function deletarCliente(int $idClienteDeletar);

    function alterarStatusCliente(int $idClienteAlterarStatus, bool $novoStatus);

    function buscarClientePeloEmailPrincipal(string $emailPrincipal);

    function buscarClientePeloTelefonePrincipal(string $telefonePrincipal);

}