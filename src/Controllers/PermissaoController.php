<?php

namespace Controllers;

use Servico\IPermissaoServico;
use Servico\PermissaoServico;

class PermissaoController {

    private IPermissaoServico $permissaoServico;

    public function __construct()
    {
        $this->permissaoServico = new PermissaoServico();
    }

    // cadastrar permissão
    public function cadastrarPermissao() {

        return $this->permissaoServico->cadastrarPermissao();
    }

    // deletar permissão
    public function deletarPermissao() {

        return $this->permissaoServico->deletarPermissao();
    }

}