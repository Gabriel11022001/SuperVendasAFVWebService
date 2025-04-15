<?php

namespace Repositorio;

use Exception;
use Models\Cliente;
use Models\Endereco;
use PDO;
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
        $stmt->bindValue(":data_nascimento", $clienteCadastrar->cpf);
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
                    uf
                ) VALUES(
                    :cep,
                    :logradouro,
                    :complemento,
                    :bairro,
                    :cidade,
                    :numero,
                    :uf
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
        
    }

    // deletar cliente na base de dados
    public function deletarCliente(int $idClienteDeletar) {
        
    }

    // alterar status do cliente na base de dados
    public function alterarStatusCliente(int $idClienteAlterarStatus, bool $novoStatus) {
        
    }

}