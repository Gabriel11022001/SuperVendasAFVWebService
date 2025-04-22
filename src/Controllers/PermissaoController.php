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

    // buscar permissoes
    public function buscarPermissoes() {

        return $this->permissaoServico->buscarPermissoes();
    }

    // editar permissão
    public function editarPermissao() {

        return $this->permissaoServico->editarPermissao();
    }

    // buscar permissão pelo id
    public function buscarPermissaoPeloId() {

        return $this->permissaoServico->buscarPermissaoPeloId();
    }

}