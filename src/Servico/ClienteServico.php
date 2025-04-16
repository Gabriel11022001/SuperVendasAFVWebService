<?php

namespace Servico;

use Exception;
use Models\Cliente;
use Models\Endereco;
use Repositorio\ClienteRepositorio;
use Repositorio\IClienteRepositorio;
use Utils\Validadores\ValidaDadosCadastroCliente;

class ClienteServico extends ServicoBase implements IClienteServico {

    private IClienteRepositorio $clienteRepositorio;
    private $maximoClientesPorPagina = 10;
    private $minimoClientesPorPagina = 5;

    public function __construct()
    {
        parent::__construct();
        $this->clienteRepositorio = new ClienteRepositorio($this->bancoDados);
    }

    // cadastrar cliente na base de dados
    public function cadastrarCliente() {

        // iniciar transação
        $this->clienteRepositorio->iniciarTransacao();

        try {
            $tipoPessoa = getParametro("tipo_pessoa");
            $telefonePrincipal = getParametro("telefone_principal");
            $telefoneSecundario = getParametro("telefone_secundario");
            $emailPrincipal = getParametro("email_principal");
            $emailSecundario = getParametro("email_secundario");
            $status = true;
            $nomeCompleto = getParametro("nome_completo");
            $cpf = getParametro("cpf");
            $dataNascimento = getParametro("data_nascimento");
            $genero = getParametro("genero");
            $tipoDocumento = getParametro("tipo_documento");
            $documento = getParametro("documento");
            $nomeMae = getParametro("nome_mae");
            $nomePai = getParametro("nome_pai");
            $razaoSocial = getParametro("razao_social");
            $valorPatrimonio = getParametro("valor_patrimonio");
            $cnpj = getParametro("cnpj");
            $dataFundacao = getParametro("data_fundacao");
            $enderecos = getParametro("enderecos");

            $errosValidarCamposCadastroCliente = ValidaDadosCadastroCliente::validar(
                $tipoPessoa,
                $telefonePrincipal,
                $telefoneSecundario,
                $emailPrincipal,
                $emailSecundario,
                $nomeCompleto,
                $cpf,
                $dataNascimento,
                $genero,
                $tipoDocumento,
                $documento,
                $nomeMae,
                $nomePai,
                $razaoSocial,
                $cnpj,
                $valorPatrimonio,
                $dataFundacao,
                $enderecos
            );

            if (!empty($errosValidarCamposCadastroCliente)) {

                return [ "ok" => false, "msg" => "Erro nos campos.", "dados" => $errosValidarCamposCadastroCliente ];
            }

            // validar se já existe outro cliente cadastrado com o mesmo telefone principal
            $clienteCadastradoMesmoTelefonePrincipal = $this->clienteRepositorio->buscarClientePeloTelefonePrincipal(
                $telefonePrincipal
            );

            if (!empty($clienteCadastradoMesmoTelefonePrincipal)) {

                return [
                    "ok" => false,
                    "msg" => "Já existe outro cliente cadastrado com o mesmo telefone principal.",
                    "dados" => []
                ];
            }
            
            // validar se já existe outro cliente cadastrado com o mesmo e-mail principal

            $cliente = new Cliente();
            $cliente->telefonePrincipal = $telefonePrincipal;
            $cliente->telefoneSecundario = $telefoneSecundario;
            $cliente->emailPrincipal = $emailPrincipal;
            $cliente->emailSecundario = $emailSecundario;
            $cliente->status = $status;
            $cliente->tipoPessoa = $tipoPessoa;
            $cliente->nomeCompleto = $nomeCompleto;
            $cliente->cpf = $cpf;
            $cliente->dataNascimento = empty($dataNascimento) ? null : $dataNascimento;
            $cliente->genero = $genero;
            $cliente->nomeMae = $nomeMae;
            $cliente->nomePai = $nomePai;
            $cliente->tipoDocumento = $tipoDocumento;
            $cliente->documento = $documento;
            $cliente->razaoSocial = $razaoSocial;
            $cliente->cnpj = $cnpj;
            $cliente->dataFundacao = empty($dataFundacao) ? null : $dataFundacao;
            $cliente->valorPatrimonio = $valorPatrimonio;

            foreach ($enderecos as $enderecoArray) {
                $endereco = new Endereco();
                
                $endereco->cep = $enderecoArray->cep;
                $endereco->logradouro = $enderecoArray->logradouro;
                $endereco->complemento = $enderecoArray->complemento;
                $endereco->bairro = $enderecoArray->bairro;
                $endereco->cidade = $enderecoArray->cidade;
                $endereco->numero = $enderecoArray->numero;
                $endereco->uf = $enderecoArray->uf;

                $cliente->enderecos[] = $endereco;
            }

            if ($tipoPessoa === "pf") {
                // cliente pessoa fisica
                
                // validar se já existe outro cliente cadastrado com o mesmo nome

                // validar se já existe outro cliente cadastrado com o mesmo cpf

                // validar se já existe outro cliente cadastrado com o mesmo número de documento

                $this->clienteRepositorio->cadastrarCliente($cliente);

                // comitar a transação
                $this->clienteRepositorio->commitarTransacao();

                return [
                    "ok" => true,
                    "msg" => "Cliente cadastrado com sucesso.",
                    "dados" => $cliente
                ];
            } else {
                // cliente pessoa juridica
            }

        } catch (Exception $e) {
            // registrar o erro no arquivo de log

            // fazer rollback da transação
            $this->clienteRepositorio->rollBackTransacao();

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se cadastrar o cliente no banco de dados.",
                "dados" => []
            ];
        }

    }

    public function editarCliente() {
        
    }

    public function deletarCliente() {
        
    }

    // buscar cliente pelo id
    public function buscarClientePeloId() {

        try {

            if (!isset($_GET["cliente_id"]) || empty(trim($_GET["cliente_id"]))) {

                throw new Exception("Informe o parâmetro cliente_id na url.");
            }

            $clienteId = intval(trim($_GET["cliente_id"]));

            $cliente = $this->clienteRepositorio->buscarClientePeloId($clienteId);

            if (empty($cliente)) {

                return [
                    "ok" => false,
                    "msg" => "Cliente não encontrado.",
                    "dados" => []
                ];
            }

            return [
                "ok" => true,
                "msg" => "Cliente encontrado.",
                "dados" => $cliente
            ];
        } catch (Exception $e) {
            // registrar erro no arquivo de log

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se consultar o cliente pelo id.",
                "dados" => []
            ];
        }

    }

    // buscar clientes paginado
    public function buscarClientes() {

        try {
            $paginaAtual = $_GET["pagina_atual"];
            $elementosPorPagina = $_GET["elementos_pagina"];

            if ($paginaAtual <= 0) {    
                $paginaAtual = 1;
            }

            if ($elementosPorPagina > $this->maximoClientesPorPagina
            || $elementosPorPagina < $this->minimoClientesPorPagina) {
                $elementosPorPagina = $this->minimoClientesPorPagina;
            }

            $clientes = $this->clienteRepositorio->buscarClientes(
                $paginaAtual,
                $elementosPorPagina
            );

            if (count($clientes) === 0) {

                return [
                    "ok" => true,
                    "msg" => "Nenhum cliente listado na pagina.",
                    "dados" => []
                ];
            }

            return [
                "ok" => true,
                "msg" => "Clientes encontrados com sucesso.",
                "dados" => $clientes
            ];
        } catch (Exception $e) {
            // registrar erro no arquivo de log

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se consultar os clientes no banco de dados.",
                "dados" => []
            ];
        }

    }

    public function alterarStatusCliente() {
        
    }

}