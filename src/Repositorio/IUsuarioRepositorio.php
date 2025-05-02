<?php

namespace Repositorio;

use Models\Usuario;

interface IUsuarioRepositorio {

    function cadastrarUsuario(Usuario $usuarioCadastrar);

    function editarUsuario(Usuario $usuarioEditar);

    function buscarUsuarioPeloLoginSenha(string $login, string $senha);

    function deletarUsuario(int $idUsuarioDeletar);

    function buscarUsuarios(int $paginaAtual, int $elementosPorPagina);

    function buscarUsuarioPeloEmail(string $email);

    function buscarUsuarioPeloLogin(string $login);

    function buscarUsuarioPeloNome(string $nome);

}
