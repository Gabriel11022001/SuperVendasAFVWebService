<?php

namespace Controllers;

use Servico\IProdutoServico;
use Servico\ProdutoServico;

class ProdutoController {

    private IProdutoServico $produtoServico;

    public function __construct()
    {
        $this->produtoServico = new ProdutoServico();
    }

    // cadastrar produto
    public function cadastrarProduto() {

        return $this->produtoServico->cadastrarProduto();
    }

    // buscar produtos
    public function buscarProdutos() {

        return $this->produtoServico->buscarProdutos();
    }

    // alterar o status do produto
    public function alterarStatusProduto() {

        return $this->produtoServico->alterarStatusProduto();
    }

    // filtrar produtos
    public function filtrarProdutos() {

        return $this->produtoServico->filtrarProdutos();
    }

    // buscar produto pelo id
    public function buscarProdutoPeloId() {

        return $this->produtoServico->buscarProdutoPeloId();
    }

    // editar produto
    public function editarProduto() {

        return $this->produtoServico->editarProduto();
    }

}