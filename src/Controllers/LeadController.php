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

    // buscar leads
    public function buscarLeads() {

        return $this->leadServico->buscarLeads();
    }

    // deletar lead na base de dados
    public function deletarLead() {

        return $this->leadServico->deletarLead();
    }

    // registrar status do lead
    public function registrarStatusLead() {

        return $this->leadServico->registrarStatusLead();
    }

}