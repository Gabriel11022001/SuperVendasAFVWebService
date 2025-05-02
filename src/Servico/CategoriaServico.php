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

    public function editarCategoria() {
        
    }

    public function deletarCategoria() {
        
    }

    public function buscarCategoriaPeloId()
    {

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