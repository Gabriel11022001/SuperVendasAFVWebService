<?php

namespace Servico;

interface ICategoriaServico {

    function cadastrarCategoria();

    function editarCategoria();

    function deletarCategoria();

    function buscarCategorias();

    function buscarCategoriaPeloId();

}