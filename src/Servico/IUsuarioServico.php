<?php

namespace Servico;

interface IUsuarioServico {

    function cadastrarUsuario();

    function editarUsuario();

    function buscarUsuarios();

    function deletarUsuario();

}