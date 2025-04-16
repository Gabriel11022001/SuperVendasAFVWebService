<?php

namespace Repositorio;

use Exception;
use Models\Cliente;
use Models\Endereco;
use PDO;
use Utils\MapearArrayClientesEmListaObjetosCliente;
use Utils\ObterQueryCadastroCliente;

class ClienteRepositorio extends Repositorio implements IClienteRepositorio {

    private array $camposTabelaClientes = [
        "tipo_pessoa",
        "telefone_principal",
        "telefone_secundario",
        "email_principal",
        "email_secundario",
        "status",
        "nome_completo",
        "cpf",
        "data_nascimento",
        "genero",
        "tipo_documento",
        "numero_documento",
        "nome_mae",
        "nome_pai",
        "razao_social",
        "cnpj",
        "data_fundacao",
        "valor_patrimonio"
    ];

    public function __construct(PDO $conexaoBancoDados)
    {
        parent::__construct($conexaoBancoDados);
    }

    // cadastrar cliente na base de dados
    public function cadastrarCliente(Cliente $clienteCadastrar) {
        $query = ObterQueryCadastroCliente::obterQuery($this->camposTabelaClientes);

        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":tipo_pessoa", $clienteCadastrar->tipoPessoa);
        $stmt->bindValue(":telefone_principal", $clienteCadastrar->telefonePrincipal);
        $stmt->bindValue(":telefone_secundario", $clienteCadastrar->telefoneSecundario);
        $stmt->bindValue(":email_principal", $clienteCadastrar->emailPrincipal);
        $stmt->bindValue(":email_secundario", $clienteCadastrar->emailSecundario);
        $stmt->bindValue(":status", $clienteCadastrar->status);
        $stmt->bindValue(":nome_completo", $clienteCadastrar->nomeCompleto);
        $stmt->bindValue(":cpf", $clienteCadastrar->cpf);
        $stmt->bindValue(":data_nascimento", $clienteCadastrar->dataNascimento);
        $stmt->bindValue(":tipo_documento", $clienteCadastrar->tipoDocumento);
        $stmt->bindValue(":numero_documento", $clienteCadastrar->documento);
        $stmt->bindValue(":genero", $clienteCadastrar->genero);
        $stmt->bindValue(":nome_pai", $clienteCadastrar->nomePai);
        $stmt->bindValue(":nome_mae", $clienteCadastrar->nomeMae);
        $stmt->bindValue(":razao_social", $clienteCadastrar->razaoSocial);
        $stmt->bindValue(":cnpj", $clienteCadastrar->cnpj);
        $stmt->bindValue(":data_fundacao", $clienteCadastrar->dataFundacao);
        $stmt->bindValue(":valor_patrimonio", $clienteCadastrar->valorPatrimonio);

        if ($stmt->execute()) {
            $idClienteCadastrado = $this->bancoDados->lastInsertId();
            $clienteCadastrar->clienteId = $idClienteCadastrado;

            // cadastrar os endereços
            foreach ($clienteCadastrar->enderecos as $enderecoCadastrar) {
                $stmt = $this->bancoDados->prepare("INSERT INTO tb_enderecos(
                    cep,
                    logradouro,
                    complemento,
                    bairro,
                    cidade,
                    numero,
                    uf,
                    cliente_id
                ) VALUES(
                    :cep,
                    :logradouro,
                    :complemento,
                    :bairro,
                    :cidade,
                    :numero,
                    :uf,
                    :cliente_id
                );");

                $stmt->bindValue(":cep", $enderecoCadastrar->cep);
                $stmt->bindValue(":logradouro", $enderecoCadastrar->logradouro);
                $stmt->bindValue(":complemento", $enderecoCadastrar->complemento);
                $stmt->bindValue(":bairro", $enderecoCadastrar->bairro);
                $stmt->bindValue(":cidade", $enderecoCadastrar->cidade);
                $stmt->bindValue(":numero", $enderecoCadastrar->numero);
                $stmt->bindValue(":uf", $enderecoCadastrar->uf);
                $stmt->bindValue(":cliente_id", $idClienteCadastrado);

                if ($stmt->execute()) {
                    $enderecoCadastrar->enderecoId = $this->bancoDados->lastInsertId();
                } else {

                    throw new Exception("Erro ao tentar-se cadastrar o endereço do cliente.");
                }

            }

        } else {

            throw new Exception("Erro ao tentar-se cadastrar o cliente na base de dados.");
        }

    }

    // editar cliente na base de dados
    public function editarCliente(Cliente $clienteEditar) {
        
    }

    // buscar cliente pelo id na base de dados
    public function buscarClientePeloId(int $idClienteConsultar) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_clientes WHERE cliente_id = :cliente_id;");
        $stmt->bindValue(":cliente_id", $idClienteConsultar);
        $stmt->execute();
        $dadosCliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($dadosCliente)) {
            $cliente = new Cliente();
            $cliente->clienteId = $dadosCliente["cliente_id"];
            $cliente->tipoPessoa = $dadosCliente["tipo_pessoa"];
            $cliente->telefonePrincipal = $dadosCliente["telefone_principal"];
            $cliente->telefoneSecundario = $dadosCliente["telefone_secundario"];
            $cliente->emailPrincipal = $dadosCliente["email_principal"];
            $cliente->emailSecundario = $dadosCliente["email_secundario"];
            $cliente->status = $dadosCliente["status"];
            $cliente->nomeCompleto = $dadosCliente["nome_completo"];
            $cliente->cpf = $dadosCliente["cpf"];
            $cliente->dataNascimento = $dadosCliente["data_nascimento"];
            $cliente->genero = $dadosCliente["genero"];
            $cliente->tipoDocumento = $dadosCliente["tipo_documento"];
            $cliente->documento = $dadosCliente["numero_documento"];
            $cliente->nomeMae = $dadosCliente["nome_mae"];
            $cliente->nomePai = $dadosCliente["nome_pai"];
            $cliente->razaoSocial = $dadosCliente["razao_social"];
            $cliente->cnpj = $dadosCliente["cnpj"];
            $cliente->valorPatrimonio = $dadosCliente["valor_patrimonio"];
            $cliente->dataFundacao = $dadosCliente["data_fundacao"];

            // buscar os endereços do cliente
            $stmt = $this->bancoDados->prepare("SELECT * FROM tb_enderecos WHERE cliente_id = :cliente_id;");
            $stmt->bindValue(":cliente_id", $idClienteConsultar);
            $stmt->execute();
            $enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($enderecos) > 0) {

                foreach ($enderecos as $enderecoArray) {
                    $endereco = new Endereco();
                    $endereco->enderecoId = $enderecoArray["endereco_id"];
                    $endereco->cep = $enderecoArray["cep"];
                    $endereco->logradouro = $enderecoArray["logradouro"];
                    $endereco->complemento = $enderecoArray["complemento"];
                    $endereco->bairro = $enderecoArray["bairro"];
                    $endereco->cidade = $enderecoArray["cidade"];
                    $endereco->uf = $enderecoArray["uf"];
                    $endereco->numero = $enderecoArray["numero"];

                    $cliente->enderecos[] = $endereco;
                }

            }

            return $cliente;
        }

        return null;
    }

    // buscar clientes de forma paginada na base de dados
    public function buscarClientes(int $paginaAtual, int $elementosPorPagina) {
        $query = "SELECT * FROM tb_clientes LIMIT :limit OFFSET :offset";
        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":limit", $elementosPorPagina);
        $stmt->bindValue(":offset",($paginaAtual - 1) * $elementosPorPagina);
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($clientes) > 0) {
            // obter os endereços dos clientes
            
            for ($i = 0; $i < count($clientes); $i++) {
                $enderecos = $this->obterEnderecosCliente($clientes[$i]["cliente_id"]);

                $clientes[$i]["enderecos"] = $enderecos;
            }

        }

        return MapearArrayClientesEmListaObjetosCliente::mapear($clientes);
    }

    // obter os endereços de cada cliente
    private function obterEnderecosCliente($clienteId) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_enderecos WHERE cliente_id = :cliente_id");
        $stmt->bindValue(":cliente_id", $clienteId);
        $stmt->execute();
        $enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $enderecos;
    }

    // deletar cliente na base de dados
    public function deletarCliente(int $idClienteDeletar) {
        
    }

    // alterar status do cliente na base de dados
    public function alterarStatusCliente(int $idClienteAlterarStatus, bool $novoStatus) {

    }

    // buscar cliente pelo e-mail principal
    public function buscarClientePeloEmailPrincipal(string $emailPrincipal) {
        
    }

    // buscar cliente pelo telefone principal
    public function buscarClientePeloTelefonePrincipal(string $telefonePrincipal) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_clientes WHERE telefone_principal = :telefone_principal");
        $stmt->bindValue(":telefone_principal", $telefonePrincipal);
        $stmt->execute();

        $clienteArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($clienteArray)) {
            // buscar endereços do cliente
            $enderecos = $this->obterEnderecosCliente($clienteArray["cliente_id"]);

            $cliente = new Cliente();
            $cliente->clienteId = $clienteArray["cliente_id"];
            $cliente->tipoPessoa = $clienteArray["tipo_pessoa"];
            $cliente->telefonePrincipal = $clienteArray["telefone_principal"];
            $cliente->telefoneSecundario = $clienteArray["telefone_secundario"];
            $cliente->emailPrincipal = $clienteArray["email_principal"];
            $cliente->emailSecundario = $clienteArray["email_secundario"];
            $cliente->status = $clienteArray["status"];
            $cliente->nomeCompleto = $clienteArray["nome_completo"];
            $cliente->cpf = $clienteArray["cpf"];
            $cliente->dataNascimento = $clienteArray["data_nascimento"];
            $cliente->genero = $clienteArray["genero"];
            $cliente->nomeMae = $clienteArray["nome_mae"];
            $cliente->nomePai = $clienteArray["nome_pai"];
            $cliente->tipoDocumento = $clienteArray["tipo_documento"];
            $cliente->documento = $clienteArray["numero_documento"];
            $cliente->razaoSocial = $clienteArray["razao_social"];
            $cliente->valorPatrimonio = $clienteArray["valor_patrimonio"];
            $cliente->cnpj = $clienteArray["cnpj"];
            $cliente->dataFundacao = $clienteArray["data_fundacao"];

            foreach ($enderecos as $enderecoArray) {
                $endereco = new Endereco();

                $endereco->enderecoId = $enderecoArray["endereco_id"];
                $endereco->cep = $enderecoArray["cep"];
                $endereco->complemento = $enderecoArray["complemento"];
                $endereco->logradouro = $enderecoArray["logradouro"];
                $endereco->cidade = $enderecoArray["cidade"];
                $endereco->bairro = $enderecoArray["bairro"];
                $endereco->numero = $enderecoArray["numero"];
                $endereco->uf = $enderecoArray["uf"];

                $cliente->enderecos[] = $endereco;
            }

            return $cliente;
        } else {

            return null;
        }

    }

}
