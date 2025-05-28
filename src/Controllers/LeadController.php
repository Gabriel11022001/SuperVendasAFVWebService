<?php

namespace Controllers;

use Servico\ILeadServico;
use Servico\LeadServico;

class LeadController {

    private ILeadServico $leadServico;

    public function __construct()
    {
        $this->leadServico = new LeadServico();    
    }

    // cadastrar lead
    public function cadastrarLead() {

        return $this->leadServico->cadastrarLead();
    }

    // remanejar leads
    public function remanejarLeads() {

        return $this->leadServico->remanejarLeads();
    }

    // buscar lead pelo id
    public function buscarLeadPeloId() {

        return $this->leadServico->buscarLeadPeloId();
    }

}