<?php

namespace Repositorio;

use DateTime;
use Exception;
use Models\Lead;
use Models\LeadStatus;
use PDO;

class LeadRepositorio extends Repositorio implements ILeadRepositorio {

    public function __construct(PDO $conexaoBancoDados)
    {
        parent::__construct($conexaoBancoDados);
    }

    // cadastrar lead
    public function cadastrarLead(Lead $leadCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_leads(tipo_pessoa, telefone, email, data_cadastro, ativo, vendedor_id,
        nome_completo, cpf, data_nascimento, genero, nome_mae, nome_pai, tipo_documento, numero_documento, razao_social,
        cnpj, data_fundacao, valor_patrimonio, vendedor_id_anterior)
        VALUES (:tipo_pessoa, :telefone, :email, :data_cadastro, :ativo, :vendedor_id,
        :nome_completo, :cpf, :data_nascimento, :genero, :nome_mae, :nome_pai, :tipo_documento, :numero_documento, :razao_social,
        :cnpj, :data_fundacao, :valor_patrimonio, :vendedor_id_anterior)");

        $stmt->bindValue(":tipo_pessoa", $leadCadastrar->tipoPessoa);
        $stmt->bindValue(":telefone", $leadCadastrar->telefone);
        $stmt->bindValue(":email", $leadCadastrar->email);
        $stmt->bindValue(":data_cadastro", $leadCadastrar->dataCadastro->format("Y-m-d H:i:s"));
        $stmt->bindValue(":ativo", $leadCadastrar->ativo);
        $stmt->bindValue(":vendedor_id", $leadCadastrar->vendedorId);
        $stmt->bindValue(":nome_completo", $leadCadastrar->nomeCompleto);
        $stmt->bindValue(":cpf", $leadCadastrar->cpf);
        $stmt->bindValue(":data_nascimento", empty($leadCadastrar->dataNascimento) ? null : $leadCadastrar->dataNascimento->format("Y-m-d"));
        $stmt->bindValue(":genero", $leadCadastrar->genero);
        $stmt->bindValue(":nome_mae", $leadCadastrar->nomeMae);
        $stmt->bindValue(":nome_pai", $leadCadastrar->nomePai);
        $stmt->bindValue(":tipo_documento", $leadCadastrar->tipoDocumento);
        $stmt->bindValue(":numero_documento", $leadCadastrar->numeroDocumento);
        $stmt->bindValue(":razao_social", $leadCadastrar->razaoSocial);
        $stmt->bindValue(":cnpj", $leadCadastrar->cnpj);
        $stmt->bindValue(":data_fundacao", empty($leadCadastrar->dataFundacao) ? null : $leadCadastrar->dataFundacao->format("Y-m-d"));
        $stmt->bindValue(":valor_patrimonio", $leadCadastrar->valorPatrimonio);
        $stmt->bindValue(":vendedor_id_anterior", $leadCadastrar->vendedorIdAnterior);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se cadastrar o lead.");
        }

        $leadCadastrar->leadId = $this->bancoDados->lastInsertId();
    }

    public function editarLead(Lead $leadEditar) {
        
    }

    public function deletarLead(Lead $leadDeletar) {
        
    }

    public function alterarVendedorLead(int $idVendedorNovo, int $idVendedorAnterior) {
        
    }

    // buscar lead pelo id
    public function buscarLeadPeloId(int $idLeadConsultar) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_leads WHERE lead_id = :lead_id");
        $stmt->bindValue(":lead_id", $idLeadConsultar);
        $stmt->execute();
        $leadArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($leadArray)) {

            return null;
        }
        
        $lead = new Lead();
        $lead->leadId = $leadArray["lead_id"];
        $lead->tipoPessoa = $leadArray["tipo_pessoa"];
        $lead->telefone = $leadArray["telefone"];
        $lead->email = $leadArray["email"];
        $lead->dataCadastro = new DateTime($leadArray["data_cadastro"]);
        $lead->vendedorId = $leadArray["vendedor_id"];
        $lead->ativo = $leadArray["ativo"];

        $lead->nomeCompleto = !empty($leadArray["nome_completo"]) ? $leadArray["nome_completo"] : "";
        $lead->cpf = !empty($leadArray["cpf"]) ? $leadArray["cpf"] : "";
        $lead->genero = !empty($leadArray["genero"]) ? $leadArray["genero"] : "";
        $lead->nomePai = !empty($leadArray["nome_pai"]) ? $leadArray["nome_pai"] : "";
        $lead->nomeMae = !empty($leadArray["nome_mae"]) ? $leadArray["nome_mae"] : "";
        $lead->tipoDocumento = !empty($leadArray["tipo_documento"]) ? $leadArray["tipo_documento"] : "";
        $lead->numeroDocumento = !empty($leadArray["numero_documento"]) ? $leadArray["numero_documento"] : "";
        $lead->dataNascimento = empty($leadArray["data_nascimento"]) ? null : new DateTime($leadArray["data_nascimento"]);

        $lead->razaoSocial = !empty($leadArray["razao_social"]) ? $leadArray["razao_social"] : "";
        $lead->cnpj = !empty($leadArray["cnpj"]) ? $leadArray["cnpj"] : "";
        $lead->valorPatrimonio = !empty($leadArray["valor_patrimonio"]) ? $leadArray["valor_patrimonio"] : 0;
        $lead->dataFundacao = empty($leadArray["data_fundacao"]) ? null : new DateTime($leadArray["data_fundacao"]);

        // obter os status do lead
        $lead->statusLead = $this->buscarHistoricoStatusLead($idLeadConsultar);

        return $lead;
    }

    // obter o histórico de status do lead
    private function buscarHistoricoStatusLead(int $idLead) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_leads_status WHERE lead_id = :lead_id
        ORDER BY data_cadastro DESC");
        $stmt->bindValue(":lead_id", $idLead);
        $stmt->execute();
        $statusArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $statusLead = [];

        foreach ($statusArray as $status) {
            $statusObj = new LeadStatus();

            $statusObj->leadStatusId = $status["lead_status_id"];
            $statusObj->status = $status["status"];
            $statusObj->dataCadastroStatus = new DateTime($status["data_cadastro"]);

            $statusLead[] = $statusObj;
        }

        return $statusLead;
    }

    public function buscarLeads(int $idVendedor, int $paginaAtual, int $elementosPorPagina) {
        
    }

    // registrar o status do lead
    public function registrarStatusLead(LeadStatus $leadStatusRegistrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_leads_status(status, data_cadastro, lead_id) VALUES(:status, :data_cadastro, :lead_id)");
        $stmt->bindValue(":status", $leadStatusRegistrar->status);
        $stmt->bindValue(":data_cadastro", $leadStatusRegistrar->dataCadastroStatus->format("Y-m-d h:i:s"));
        $stmt->bindValue(":lead_id", $leadStatusRegistrar->leadId);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se registrar o status do lead na base de dados.");
        }

        $leadStatusRegistrar->leadStatusId = $this->bancoDados->lastInsertId();
    }

    public function buscarLeadPeloEmail(string $email) {

    }

    public function buscarLeadPeloTelefone(string $telefone) {
        
    }

    public function buscarLeadCpf(string $cpf) {
        
    }

    public function buscarLeadPeloNumeroDocumento(string $numeroDocumento) {
        
    }

    public function buscarLeadPeloCnpj(string $cnpj) {
        
    }

}
