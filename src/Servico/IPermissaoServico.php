<?php

namespace Servico;

interface IPermissaoServico {
 
    public function cadastrarPermissao();
    
    public function editarPermissao();

    public function deletarPermissao();

    public function buscarPermissaoPeloId();

    public function buscarPermissoes();

}