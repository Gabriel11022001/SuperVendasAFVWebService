<?php

namespace Repositorio;

use Models\Categoria;

interface ICategoriaRepositorio {

    function cadastrarCategoria(Categoria $categoriCadastrar);

    function editarCategoria(Categoria $categoriaEditar);

    function buscarCategorias();

    function deletarCategoria(int $categoriaIdDeletar);

    function buscarCategoriaPeloId(int $idCategoria);

    function buscarCategoriaPeloNome(string $nomeCategoria);

    function iniciarTransacaoCategoria();

    function rollbackTransacaoCategoria();

    function comitarTransacaoCategoria();

}