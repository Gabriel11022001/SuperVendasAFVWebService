<?php

namespace Utils\Validadores;

class ValidaDadosCadastroCliente {

    // validar campos do cadastro de cliente pf/pj
    public static function validar(
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
    ) {
        $erros = [];

        if (empty($tipoPessoa)) {
            $erros["tipo_pessoa"] = "Informe o tipo de pessoa do cliente.";
        } elseif ($tipoPessoa != "pf" && $tipoPessoa != "pj") {
            $erros["tipo_pessoa"] = "Tipo de pessoa inválido!";
        }

        if (empty($enderecos)) {
            $erros["enderecos"] = "Informe pelo menos um endereço para o cliente.";
        }

        return $erros;
    }

    private static function validarPessoaFisica(
        $nomeCompleto,
        $cpf,
        $dataNascimento,
        $genero,
        $tipoDocumento,
        $documento,
        $nomeMae,
        $nomePai
    ) {

    }

    private static function validarPessoaJuridica(
        $razaoSocial,
        $cnpj,
        $valorPatrimonio,
        $dataFundacao
    ) {

    }

}