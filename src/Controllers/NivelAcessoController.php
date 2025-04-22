<?php

namespace Controllers;

use Servico\INivelAcessoServico;
use Servico\NivelAcessoServico;

class NivelAcessoController {

    private INivelAcessoServico $nivelAcessoServico;

    public function __construct()
    {
        $this->nivelAcessoServico = new NivelAcessoServico();
    }

    // cadastrar nivel de acesso
    public function cadastrarNivelAcesso() {

        return $this->nivelAcessoServico->cadastrarNivelAcesso();
    }

    // buscar niveis de acesso
    public function buscarNiveisAcesso() {

        return $this->nivelAcessoServico->buscarNiveisAcesso();
    }

    // deletar nivel de acesso
    public function deletarNivelAcesso() {

        return $this->nivelAcessoServico->deletarNivelAcesso();
    }

}