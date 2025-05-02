<?php

namespace Controllers;

use Servico\IUsuarioServico;
use Servico\UsuarioServico;

class UsuarioController {

    private IUsuarioServico $usuarioServico;

    public function __construct()
    {
        $this->usuarioServico = new UsuarioServico();
    }

    // cadastrar usuário
    public function cadastrarUsuario() {

        return $this->usuarioServico->cadastrarUsuario();
    }

}