<?php

namespace Repositorio;

use Models\NivelAcesso;

interface INivelAcessoRepositorio {

    function cadastrarNivelAcesso(NivelAcesso $nivelAcesso);

    function editarNivelAcesso(NivelAcesso $nivelAcesso);

    function buscarNiveisAcesso();

    function buscarNivelAcessoPeloId(int $idNivelAcesso);

    function deletarNivelAcesso(int $idNivelAcessoDeletar);

    function buscarNivelAcessoPeloNome(string $nomeNivelAcesso);

}