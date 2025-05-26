<?php

namespace Servico;

interface ILeadServico {

    function cadastrarLead();

    function editarLead();

    function deletarLead();

    function buscarLeads();

    function buscarLeadPeloId();

    function remanejarLeads();

}
