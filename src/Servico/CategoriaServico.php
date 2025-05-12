<?php

namespace Servico;

use Exception;
use Models\Categoria;
use Repositorio\CategoriaRepositorio;
use Repositorio\ICategoriaRepositorio;
use Utils\Resposta;

class CategoriaServico extends ServicoBase implements ICategoriaServico {

    private ICategoriaRepositorio $categoriaRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->categoriaRepositorio = new CategoriaRepositorio($this->bancoDados);
    }

    // cadastrar categoria
    public function cadastrarCategoria() {
        
        try {
            $nome = getParametro("nome_categoria");
            $status = getParametro("status");
            $errosCampos = [];

            if (empty($nome)) {
                $errosCampos["nome_categoria"] = "Informe o nome da categoria.";
            } else if (strlen($nome) < 3) {
                $errosCampos["nome_categoria"] = "O nome da categoria deve ter no mínimo 3 caracteres.";
            }

            if (empty($status)) {
                $errosCampos["status"] = "Informe o status da categoria.";
            }

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            //validar se já existe outra categoria cadastrada com o mesmo nome
            if (!empty($this->categoriaRepositorio->buscarCategoriaPeloNome($nome))) {
                Resposta::response(false, "Já existe outra categoria cadastrada com o mesmo nome.");
            }

            $categoria = new Categoria(
                0,
                $nome,
                $status
            );

            $this->categoriaRepositorio->cadastrarCategoria($categoria);

            Resposta::response(true, "Categoria cadastrada com sucesso.", $categoria);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se cadastrar a categoria.");
        }

    }

    // validar campos edição categoria
    private function validarCamposEditarCategoria(string $nome, int $categoriaId) { 
        $erros = [];

        if (empty($nome)) {
            $erros["nome"] = "Informe o nome da categoria.";
        } elseif (strlen($nome) < 3) {
            $erros["nome"] = "O nome da categoria deve possuir no mínimo 3 caracteres.";
        }

        if (empty($categoriaId)) {
            $erros["categoria_id"] = "Informe o id da categoria.";
        }

        return $erros;
    }

    // editar categoria
    public function editarCategoria() {
        
        try {
            $categoriaId = getParametro("categoria_id");
            $nome = getParametro("nome");
            $status = getParametro("status");
            $errosCampos = $this->validarCamposEditarCategoria($nome, $categoriaId);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            // validar se já existe outra categoria cadastrada com o mesmo nome
            $categoriMesmoNome = $this->categoriaRepositorio->buscarCategoriaPeloNome($nome);

            if (!empty($categoriMesmoNome) && $categoriMesmoNome->categoriaId != $categoriaId) {
                Resposta::response(false, "Já existe outra categoria cadastrada com o mesmo nome.");
            }

            $categoriaEditar = new Categoria();
            $categoriaEditar->categoriaId = $categoriaId;
            $categoriaEditar->nome = $nome;
            $categoriaEditar->status = $status;

            $this->categoriaRepositorio->editarCategoria($categoriaEditar);

            Resposta::response(true, "Categoria editada com sucesso.", $categoriaEditar);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se editar a categoria.");
        }

    }

    public function deletarCategoria() {
    
    }

    // buscar categoria pelo id
    public function buscarCategoriaPeloId() {

        try {
            
            if (!isset($_GET["categoria_id"])) {
                Resposta::response(false, "Informe o id da categoria na url.");
            }

            $idCategoria = trim($_GET["categoria_id"]);

            if (empty($idCategoria)) {
                Resposta::response(false, "Informe o id da categoria na url.");
            }

            $categoria = $this->categoriaRepositorio->buscarCategoriaPeloId($idCategoria);

            if (empty($categoria)) {
                Resposta::response(false, "Categoria não encontrada.");
            }

            Resposta::response(true, "Categoria encontrada com sucesso.", $categoria);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se buscar a categoria pelo id.");
        }

    }

    // buscar categoria
    public function buscarCategorias() {

        try {
            $categorias = $this->categoriaRepositorio->buscarCategorias();

            if (empty($categorias)) {
                Resposta::response(true, "Não existem categorias cadastradas na base de dados.", []);
            }

            Resposta::response(true, "Categorias listadas com sucesso.", $categorias);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se buscar as categorias.");
        }

    }

}