<?php

namespace Repositorio;

use Models\Permissao;

interface IPermissaoRepositorio {

    public function cadastrarPermissao(Permissao $permissaoCadastrar);

    public function editarPermissao(Permissao $permissaoEditar);

    public function buscarTodasPermissoes();

    public function deletarPermissao(int $idPermissaoDeletar);

    public function buscarPermissaoPeloId(int $idPermissao);

    public function buscarPermissaoPeloNome(string $nomePermissao);

}