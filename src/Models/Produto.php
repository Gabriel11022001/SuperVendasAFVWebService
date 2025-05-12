<?php

namespace Models;

use DateTime;

class Produto {

    public int $produtoId;
    public string $nome;
    public string $descricao;
    public bool $status;
    public float $precoCompra;
    public float $precoVenda;
    public float $percentualDesconto;
    public Categoria|null $categoria;
    public int $categoriaId;
    public string $urlFotoProduto;
    public int $unidadesEstoque;
    public DateTime|null $dataVencimento;
    public DateTime|null $dataEntradaEstoque;

    public function __construct()
    {
        $this->produtoId = 0;
        $this->nome = "";
        $this->descricao = "";
        $this->status = true;
        $this->precoCompra = 0;
        $this->precoVenda = 0;
        $this->percentualDesconto = 0;
        $this->categoria = null;
        $this->categoriaId = 0;
        $this->urlFotoProduto = "";
        $this->unidadesEstoque = 0;
        $this->dataVencimento = null;
        $this->dataEntradaEstoque = null;
    }

}