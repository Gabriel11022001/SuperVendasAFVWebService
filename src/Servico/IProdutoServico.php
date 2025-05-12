<?php

namespace Servico;

interface IProdutoServico {

    function cadastrarProduto();

    function editarProduto();

    function deletarProduto();

    function buscarProdutos();

    function buscarProdutoPeloId();

    function alterarStatusProduto();

    function filtrarProdutos();

    function registrarEntradaProdutoEstoque();

    function registrarSaidaProdutoEstoque();

}