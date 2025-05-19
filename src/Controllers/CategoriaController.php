<?php

namespace Controllers;

use Servico\CategoriaServico;
use Servico\ICategoriaServico;

class CategoriaController {

    private ICategoriaServico $categoriaServico;

    public function __construct()
    {
        $this->categoriaServico = new CategoriaServico();   
    }

    // cadastrar categoria
    public function cadastrarCategoria() {

        return $this->categoriaServico->cadastrarCategoria();
    }

    // buscar todas as categorias cadastradas na base de dados
    public function buscarCategorias() {

        return $this->categoriaServico->buscarCategorias();
    }

    // buscar categoria pelo id
    public function buscarCategoriaPeloId() {

        return $this->categoriaServico->buscarCategoriaPeloId();
    }

    // editar categoria de produto
    public function editarCategoria() {

        return $this->categoriaServico->editarCategoria();
    }

    // deletar categoria
    public function deletarCategoria() {

        return $this->categoriaServico->deletarCategoria();
    }

    // cadastrar multiplas categorias
    public function cadastrarMultiplasCategorias() {

        return $this->categoriaServico->cadastrarMultiplasCategorias();
    }

}