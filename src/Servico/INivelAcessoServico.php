<?php

namespace Servico;

interface INivelAcessoServico {

    function cadastrarNivelAcesso();

    function editarNivelAceso();

    function deletarNivelAcesso();

    function buscarNiveisAcesso();

    function buscarNivelAcessoPeloId();

}