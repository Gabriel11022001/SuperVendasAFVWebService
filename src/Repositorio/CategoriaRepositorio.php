<?php

namespace Repositorio;

use Exception;
use Models\Categoria;
use PDO;

class CategoriaRepositorio extends Repositorio implements ICategoriaRepositorio {

    public function __construct(PDO $bancoDados)
    {   
        parent::__construct($bancoDados);
    }

    // cadastrar categoria
    public function cadastrarCategoria(Categoria $categoriaCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_categorias(nome, status) VALUES(:nome, :status)");
        $stmt->bindValue(":nome", $categoriaCadastrar->nome);
        $stmt->bindValue(":status", $categoriaCadastrar->status);
        
        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se cadastrar a categoria.");
        }

        $categoriaCadastrar->categoriaId = $this->bancoDados->lastInsertId();
    }

    public function editarCategoria(Categoria $categoriaEditar) {
        
    }

    // buscar categoria pelo id
    public function buscarCategoriaPeloId(int $idCategoria) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_categorias WHERE categoria_id = :categoria_id");
        $stmt->bindValue(":categoria_id", $idCategoria);
        $stmt->execute();
        $categoriaArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($categoriaArray)) {
            $categoria = new Categoria();
            $categoria->categoriaId = $categoriaArray["categoria_id"];
            $categoria->nome = $categoriaArray["nome"];
            $categoria->status = $categoriaArray["status"];

            return $categoria;
        }

        return null;
    }

    // buscar todas as categorias
    public function buscarCategorias() {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_categorias ORDER BY nome ASC");
        $stmt->execute();
        $categoriasArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $categorias = [];

        foreach ($categoriasArray as $categoriaArray) {
            $categorias[] = new Categoria(
                $categoriaArray["categoria_id"],
                $categoriaArray["nome"],
                $categoriaArray["status"]
            );
        }

        return $categorias;
    }

    public function deletarCategoria(int $categoriaIdDeletar) {

    }

    // buscar categoria pelo nome
    public function buscarCategoriaPeloNome(string $nomeCategoria) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_categorias WHERE nome = :nome");
        $stmt->bindValue(":nome", $nomeCategoria);
        $stmt->execute();
        $categoriaArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($categoriaArray)) {
            
            return null;
        }

        $categoria = new Categoria();
        $categoria->categoriaId = $categoriaArray["categoria_id"];
        $categoria->nome = $categoriaArray["nome"];
        $categoria->status = $categoriaArray["status"];

        return $categoria;
    }

}