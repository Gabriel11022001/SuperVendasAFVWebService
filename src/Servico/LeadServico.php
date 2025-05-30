<?php

namespace Servico;

use DateTime;
use Exception;
use Models\Lead;
use Models\LeadStatus;
use Repositorio\ILeadRepositorio;
use Repositorio\IUsuarioRepositorio;
use Repositorio\LeadRepositorio;
use Repositorio\UsuarioRepositorio;
use Utils\Constantes;
use Utils\Resposta;

class LeadServico extends ServicoBase implements ILeadServico {

    private ILeadRepositorio $leadRepositorio;
    private IUsuarioRepositorio $usuarioRepositorio;
    private Constantes $constantes;
    private array $generos = [
        "Masculino",
        "Feminino"
    ];

    public function __construct()
    {
        parent::__construct();

        $this->constantes = new Constantes();
        $this->leadRepositorio = new LeadRepositorio($this->bancoDados);
        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
    }

    private function validarCamposCadastroLead($camposValidar) {
        $errosCampos = array();

        if ($camposValidar["tipo_pessoa"] != $this->constantes->pf &&
        $camposValidar["tipo_pessoa"] != $this->constantes->pj) {
            $errosCampos["tipo_pessoa"] = "Tipo de pessoa inválido.";
        } else {

            if (empty($camposValidar["vendedor_id"])) {
                $errosCampos["vendedor_id"] = "Informe o id do vendedor.";
            }

            if (empty($camposValidar["telefone"])) {
                $errosCampos["telefone"] = "Informe o telefone.";
            }

            if (empty($camposValidar["email"])) {
                $errosCampos["email"] = "Informe o e-mail.";
            }

            // validar campos da pessoa fisica
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

                if (empty($camposValidar["genero"])) {
                    $errosCampos["genero"] = "Informe o gênero.";
                } elseif (!in_array($camposValidar["genero"], $this->generos)) {
                    $errosCampos["genero"] = "Gênero inválido.";
                }

                if (empty($camposValidar["tipo_documento"])) {
                    $errosCampos["tipo_documento"] = "Informe o tipo de documento.";
                }

                if (empty($camposValidar["numero_documento"])) {
                    $errosCampos["numero_documento"] = "Informe o número do documento.";
                }

            } else {
                // validar campos da pessoa juridica
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
                "tipo_documento" => $tipoDocumento,
                "numero_documento" => $numeroDocumento,
                "razao_social" => $razaoSocial,
                "cnpj" => $cnpj,
                "data_fundacao" => $dataFundacao,
                "valor_patrimonio" => $valorPatrimonio ,
                "vendedor_id" => $vendedorId
            ]);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            // validar se existe um usuário cadastrado com o id informado
            if (!$this->usuarioRepositorio->validarExisteUsuarioComIdInformado($vendedorId)) {
                Resposta::response(false, "Vendedor não encontrado na base de dados.");
            }

            // validar se já existe outro lead cadastrado com o mesmo e-mail na base de dadas
            if (!empty($this->leadRepositorio->buscarLeadPeloEmail($email))) {
                Resposta::response(false, "Já existe outro lead cadastrado com o mesmo e-mail na base de dados.");
            }

            // cadastrar lead pf
            if ($tipoPessoa === $this->constantes->pf) {

                // validar se já existe outro lead cadastrado com o mesmo cpf na base de dados
                if (!empty($this->leadRepositorio->buscarLeadCpf($cpf))) {
                    Resposta::response(false, "Já existe outro lead cadastrado com o mesmo cpf na base de dados.");
                }

                $leadCadastrar = new Lead();
                $leadCadastrar->tipoPessoa = $tipoPessoa;
                $leadCadastrar->telefone = $telefone;
                $leadCadastrar->email = $email;
                $leadCadastrar->cpf = $cpf;
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

            Resposta::response(false, "Erro ao tentar-se cadastrar o lead.", $e->getMessage());
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

    // deletar lead
    public function deletarLead() {
        $this->bancoDados->beginTransaction();

        try {
        
            if (!isset($_GET["lead_id"])) {
                Resposta::response(false, "Informe o id do lead na url.");
            }

            if (empty($_GET["lead_id"])) {
                Resposta::response(false, "Informe o id do lead na url para deleção.");
            }

            $idLeadDeletar = $_GET["lead_id"];
            $lead = $this->leadRepositorio->buscarLeadPeloId($idLeadDeletar);

            if (empty($lead)) {
                Resposta::response(false, "Lead não encontrado.");
            }

            $this->leadRepositorio->deletarLead($lead);

            $this->bancoDados->commit();

            Resposta::response(true, "Lead deletado com sucesso.");
        } catch (Exception $e) {
            $this->bancoDados->rollBack();

            Resposta::response(false, "Erro ao tentar-se deletar o lead na base de dados.");
        }

    }

    // remanejar leads para outro vendedor
    public function remanejarLeads() {
        $this->bancoDados->beginTransaction();

        try {
            $vendedorId = getParametro("vendedor_id");
            $leads = getParametro("leads");

            if (empty($vendedorId)) {
                Resposta::response(false, "Informe o id do vendedor.");
            }

            if (empty($leads)) {
                Resposta::response(false, "Informe os leads que serão remanejados.");
            }

            // validar se existe um vendedor cadastrado com o id informado na base de dados
            if (empty($this->usuarioRepositorio->validarExisteUsuarioComIdInformado($vendedorId))) {
                Resposta::response(false, "Vendedor não encontrado na base de dados.");
            }

            $leadsForamRemanejados = [];
            $leadsNaoForamRemanejados = [];

            foreach ($leads as $leadId) {
                $lead = $this->leadRepositorio->buscarLeadPeloId($leadId);

                if (empty($lead)) {
                    $leadsNaoForamRemanejados[] = [
                        "motivo" => "Lead não encontrado na base de dados",
                        "id_lead" => $leadId
                    ];
                } else {
                    $this->leadRepositorio->alterarVendedorLead($vendedorId, $lead->vendedorIdAnterior, $leadId);

                    $leadsForamRemanejados[] = [
                        "id_lead" => $leadId,
                        "nome_lead" => $lead->tipoPessoa === "pf" ? $lead->nomeCompleto : $lead->razaoSocial,
                        "documento" => $lead->tipoPessoa === "pf" ? $lead->cpf : $lead->cnpj
                    ];
                }

            }

            $this->bancoDados->commit();

            Resposta::response(true, "Remanejamento de leads realizado com sucesso.", [
                "leads_remanejados" => $leadsForamRemanejados,
                "leads_nao_foram_remanejados" => $leadsNaoForamRemanejados
            ]);
        } catch (Exception $e) {
            $this->bancoDados->rollBack();

            Resposta::response(false, "Erro ao tentar-se remanejar os leads.");
        }

    }

    // buscar lead pelo id
    public function buscarLeadPeloId() {
        
        try {

            if (!isset($_GET["lead_id"])) {
                Resposta::response(false, "Informe o id do lead na url.");
            }

            if (empty($_GET["lead_id"])) {
                Resposta::response(false, "Informe o id do lead.");
            }

            $idLead = trim($_GET["lead_id"]);

            $lead = $this->leadRepositorio->buscarLeadPeloId($idLead);

            if (empty($lead)) {
                Resposta::response(false, "Lead não encontrado.");
            }

            Resposta::response(true, "Lead encontrado com sucesso.", $lead);
        } catch (Exception $e) {
            Resposta::response(false, "Lead não encontrado na base de dados.");
        }

    }

    // buscar leads do vendedor
    public function buscarLeads() {
        
        try {
            
            if (!isset($_GET["pagina_atual"]) || !isset($_GET["elementos_por_pagina"]) || !isset($_GET["id_vendedor"])) {
                Resposta::response(false, "Informe a pagina atual, a quantidade de elementos por pagina e o id do vendedor na url.");
            }

            $paginaAtual = 0;
            $elementosPorPagina = 0;

            if (empty($_GET["pagina_atual"]) || $_GET["pagina_atual"] <= 0) {
                $paginaAtual = 1;
            } else {
                $paginaAtual = $_GET["pagina_atual"];
            }

            if (empty($_GET["elementos_por_pagina"]) || ($_GET["elementos_por_pagina"] <= 0 || $_GET["elementos_por_pagina"] > 10)) {
                $elementosPorPagina = 5;
            } else {
                $elementosPorPagina = $_GET["elementos_por_pagina"];
            }

            $vendedorId = $_GET["id_vendedor"];

            // validar se existe um vendedor cadastrado com o id informado
            if (empty($this->usuarioRepositorio->validarExisteUsuarioComIdInformado($vendedorId))) {
                Resposta::response(false, "Não existe um vendedor cadastrado com o id informado.");
            }

            $leadsVendedor = $this->leadRepositorio->buscarLeads($vendedorId, $paginaAtual, $elementosPorPagina);

            if (count($leadsVendedor) == 0) {
                Resposta::response(true, "Não existem leads cadastrados para o vendedor logado.", array());
            }

            Resposta::response(true, "Leads listados com sucesso.", $leadsVendedor);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se listar os leads.");
        }

    }

    // registrar status do lead
    public function registrarStatusLead() {

        try {
            $leadId = getParametro("lead_id");
            $novoStatus = getParametro("status");

            if (empty($leadId)) {
                Resposta::response(false, "Informe o id do lead.");
            }

            if (empty($novoStatus)) {
                Resposta::response(false, "Informe o status do lead.");
            }

            if ($novoStatus != $this->constantes->statusLeadAQualificar
            && $novoStatus != $this->constantes->statusLeadQualificado
            && $novoStatus != $this->constantes->statusLeadDesqualificado
            && $novoStatus != $this->constantes->statusLeadCliente
            && $novoStatus != $this->constantes->statusLeadEmNegociacao
            && $novoStatus != $this->constantes->statusLeadPerdido) {
                Resposta::response(false, "Status inválido.");
            }

            if ($this->leadRepositorio->buscarLeadPeloId($leadId) == null) {
                Resposta::response(false, "Não existe um lead cadastrado com esse id na base de dados.");
            }

            $status = new LeadStatus();
            $status->leadId = $leadId;
            $status->dataCadastroStatus = new DateTime("now");
            $status->status = $novoStatus;

            $this->leadRepositorio->registrarStatusLead($status);

            Resposta::response(true, "O status " . $novoStatus . " foi registrado para o lead com sucesso.", $status);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se registrar o status para o lead.");
        }

    }

}
