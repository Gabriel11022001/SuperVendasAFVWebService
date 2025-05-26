<?php

namespace Servico;

use DateTime;
use Exception;
use Models\Lead;
use Models\LeadStatus;
use Repositorio\ILeadRepositorio;
use Repositorio\LeadRepositorio;
use Utils\Constantes;
use Utils\Resposta;

class LeadServico extends ServicoBase implements ILeadServico {

    private ILeadRepositorio $leadRepositorio;
    private Constantes $constantes;

    public function __construct()
    {
        parent::__construct();

        $this->constantes = new Constantes();
        $this->leadRepositorio = new LeadRepositorio($this->bancoDados);
    }

    private function validarCamposCadastroLead($camposValidar) {
        $errosCampos = array();

        if ($camposValidar["tipo_pessoa"] != $this->constantes->pf &&
        $camposValidar["tipo_pessoa"] != $this->constantes->pj) {
            $errosCampos["tipo_pessoa"] = "Tipo de pessoa inválido.";
        } else {

            if (empty($camposValidar["telefone"])) {
                $errosCampos["telefone"] = "Informe o telefone.";
            }

            if (empty($camposValidar["email"])) {
                $errosCampos["email"] = "Informe o e-mail.";
            }

            if ($camposValidar["tipo_pessoa"] === $this->constantes->pf) {

                if (empty($camposValidar["nome_completo"])) {
                    $errosCampos["nome_completo"] = "Informe o nome completo.";
                } elseif (strlen($camposValidar["nome_completo"]) < 3) {
                    $errosCampos["nome_completo"] = "O nome completo do lead deve possuir no mínimo 3 caracteres.";
                }

                if (empty($camposValidar["cpf"])) {
                    $errosCampos["cpf"] = "Informe o cpf.";
                }

                if (empty($camposValidar["data_nascimento"])) {
                    $errosCampos["data_nascimento"] = "Informe a data de nascimento.";
                } else {
                    
                }

            } else {

            }

        }

        return $errosCampos;
    }

    // cadastrar lead
    public function cadastrarLead() {
        $this->bancoDados->beginTransaction();

        try {
            $tipoPessoa = getParametro("tipo_pessoa");
            $telefone = getParametro("telefone");
            $email = getParametro("email");
            $ativo = getParametro("ativo");
            $nomeCompleto = getParametro("nome_completo");
            $cpf = getParametro("cpf");
            $dataNascimento = getParametro("data_nascimento");
            $genero = getParametro("genero");
            $nomePai = getParametro("nome_pai");
            $nomeMae = getParametro("nome_mae");
            $tipoDocumento = getParametro("tipo_documento");
            $numeroDocumento = getParametro("numero_documento");
            $razaoSocial = getParametro("razao_social");
            $cnpj = getParametro("cnpj");
            $dataFundacao = getParametro("data_fundacao");
            $valorPatrimonio = getParametro("valor_patrimonio");
            $vendedorId = getParametro("vendedor_id");

            $errosCampos = $this->validarCamposCadastroLead([
                "tipo_pessoa" => $tipoPessoa,
                "telefone" => $telefone,
                "email" => $email,
                "nome_completo" => $nomeCompleto,
                "cpf" => $cpf,
                "data_nascimento" => $dataNascimento,
                "genero" => $genero,
                "nome_pai" => $nomePai,
                "nome_mae" => $nomeMae,
                "tipo_documento" => $tipoDocumento,
                "numero_documento" => $numeroDocumento,
                "razao_social" => $razaoSocial,
                "cnpj" => $cnpj,
                "data_fundacao" => $dataFundacao,
                "valor_patrimonio" => $valorPatrimonio 
            ]);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            if ($tipoPessoa === $this->constantes->pf) {
                // cadastrar lead pf

                // validar se já existe outro lead cadastrado com o mesmo cpg na base de dados
                if (!empty($this->leadRepositorio->buscarLeadCpf($cpf))) {
                    Resposta::response(false, "Já existe outro lead cadastrado com o mesmo cpf na base de dados.");
                }

                // validar se já existe outro lead cadastrado com o mesmo e-mail na base de dadas
                if (!empty($this->leadRepositorio->buscarLeadPeloEmail($email))) {
                    Resposta::response(false, "Já existe outro lead cadastrado com o mesmo e-mail na base de dados.");
                }

                $leadCadastrar = new Lead();
                $leadCadastrar->tipoPessoa = $tipoPessoa;
                $leadCadastrar->telefone = $telefone;
                $leadCadastrar->email = $email;
                $leadCadastrar->ativo = $ativo;
                $leadCadastrar->dataCadastro = new DateTime("now");
                $leadCadastrar->vendedorId = $vendedorId;
                $leadCadastrar->nomeCompleto = $nomeCompleto;
                $leadCadastrar->genero = $genero;
                $leadCadastrar->tipoDocumento = $tipoDocumento;
                $leadCadastrar->numeroDocumento = $numeroDocumento;
                $leadCadastrar->dataNascimento = new DateTime($dataNascimento);
                $leadCadastrar->nomeMae = $nomeMae;
                $leadCadastrar->nomePai = $nomePai;

                $this->leadRepositorio->cadastrarLead($leadCadastrar);

                // registrar status do lead
                $this->registrarStatusLeadNovo($leadCadastrar);

                $this->bancoDados->commit();

                Resposta::response(true, "Lead cadastrado com sucesso.", $leadCadastrar);
            } else {
                // cadastrar lead pj
            }

        } catch (Exception $e) {
            $this->bancoDados->rollBack();

            Resposta::response(false, "Erro ao tentar-se cadastrar o lead.");
        }

    }

    // registrar status de um lead que acabou de ser cadastrado
    private function registrarStatusLeadNovo(Lead $lead) {
        $leadStatus = new LeadStatus();

        $leadStatus->leadId = $lead->leadId;
        $leadStatus->status = $this->constantes->statusLeadAQualificar;
        $leadStatus->dataCadastroStatus = new DateTime("now");

        $this->leadRepositorio->registrarStatusLead($leadStatus);

        $lead->statusLead[] = $leadStatus;
    }

    public function editarLead() {
        
    }

    public function deletarLead() {
        
    }

    public function remanejarLeads() {
        
    }

    public function buscarLeadPeloId() {
        
    }

    public function buscarLeads() {
        
    }

}
