<?php

namespace Models;

use DateTime;

class FiltroProdutos {

    private array $maximosElementosPorPagina = [5, 10, 15, 50];
    private int $paginaAtual = 0;
    private int $elementosPorPagina = 0;
    public string $nomeProduto = "";
    public string $descricao = "";
    public int|null $estoque = null;
    public int $categoriaProduto = 0;
    public DateTime|null $dataEntradaEstoqueInicial = null;
    public DateTime|null $dataEntradaEstoqueFinal = null;
    public DateTime|null $dataVencimentoInicial = null;
    public DateTime|null $dataVencimentoFinal = null;
    public bool|null $status = null;
    public float $precoVendaInicial = 0;
    public float $precoVendaFinal = 0;

    public function validarFiltro() {
        $erros = [];

        return $erros;
    }

    public function setPaginaAtual(int $paginaAtual) {

        if ($paginaAtual <= 0) {
            $this->paginaAtual = 1;
        } else {
            $this->paginaAtual = $paginaAtual;
        }

    }

    public function getPaginaAtual() {

        return $this->paginaAtual;
    }

    public function setElementosPorPagina(int $elementosPorPagina) {

        if (!in_array($elementosPorPagina, $this->maximosElementosPorPagina)) {
            $this->elementosPorPagina = $this->maximosElementosPorPagina[ 0 ];
        } else {
            $this->elementosPorPagina = $elementosPorPagina;
        }

    }

    public function getElementosPorPagina() {

        return $this->elementosPorPagina;
    }

}