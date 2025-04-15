<?php

namespace Utils;

use Models\Cliente;
use Models\Endereco;

class MapearArrayClientesEmListaObjetosCliente {

    public static function mapear($clientesArray = []) {

        if (count($clientesArray) == 0) {

            return [];
        }

        $clientes = [];

        foreach ($clientesArray as $clienteArrayItem) {
            $cliente = new Cliente();
            $cliente->clienteId = $clienteArrayItem["cliente_id"];
            $cliente->tipoPessoa = $clienteArrayItem["tipo_pessoa"];
            $cliente->telefonePrincipal = $clienteArrayItem["telefone_principal"];
            $cliente->telefoneSecundario = $clienteArrayItem["telefone_secundario"];
            $cliente->emailPrincipal = $clienteArrayItem["email_principal"];
            $cliente->emailSecundario = $clienteArrayItem["email_secundario"];
            $cliente->status = $clienteArrayItem["status"];
            $cliente->nomeCompleto = $clienteArrayItem["nome_completo"];
            $cliente->cpf = $clienteArrayItem["cpf"];
            $cliente->dataNascimento = $clienteArrayItem["data_nascimento"];
            $cliente->genero = $clienteArrayItem["genero"];
            $cliente->tipoDocumento = $clienteArrayItem["tipo_documento"];
            $cliente->documento = $clienteArrayItem["numero_documento"];
            $cliente->nomePai = $clienteArrayItem["nome_pai"];
            $cliente->nomeMae = $clienteArrayItem["nome_mae"];
            $cliente->razaoSocial = $clienteArrayItem["razao_social"];
            $cliente->cnpj = $clienteArrayItem["cnpj"];
            $cliente->dataFundacao = $clienteArrayItem["data_fundacao"];
            $cliente->valorPatrimonio = $clienteArrayItem["valor_patrimonio"];

            // mapear os endereços
            $enderecos = [];

            foreach ($clienteArrayItem["enderecos"] as $endereco) {
                $enderecoCliente = new Endereco();
                $enderecoCliente->enderecoId = $endereco["endereco_id"];
                $enderecoCliente->cep = $endereco["cep"];
                $enderecoCliente->complemento = $endereco["complemento"];
                $enderecoCliente->logradouro = $endereco["logradouro"];
                $enderecoCliente->bairro = $endereco["bairro"];
                $enderecoCliente->cidade = $endereco["cidade"];
                $enderecoCliente->uf = $endereco["uf"];
                $enderecoCliente->numero = $endereco["numero"];

                $enderecos[] = $endereco;
            }

            $cliente->enderecos = $enderecos;

            $clientes[] = $cliente;
        }

        return $clientes;
    }

}