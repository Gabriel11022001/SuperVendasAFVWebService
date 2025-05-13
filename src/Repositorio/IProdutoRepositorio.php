<?php

namespace Repositorio;

use DateTime;
use Models\FiltroProdutos;
use Models\Produto;

interface IProdutoRepositorio {

    function cadastrarProduto(Produto $produtoCadastrar);

    function editarProduto(Produto $produtoEditar);

    function deletarProduto(int $idProdutoDeletar);

    function buscarProdutos(int $paginaAtual, int $elementosPorPagina);

    function buscarProdutoPeloId(int $idProduto);

    function registrarEntradaProdutoEstoque(int $produtoId, int $unidadesEntrada, DateTime $dataVencimentoProduto);

    function registrarSaidaProdutoEstoque(int $produtoId, int $unidadesSaida);

    function alterarStatusProduto(int $produtoId, bool $novoStatus);

    function filtrarProdutos(FiltroProdutos $filtroProdutos);

    function buscarProdutoPeloNome(string $nomeProduto);

    function buscarProdutosPelaCategoria(int $idCategoriaProduto);

}