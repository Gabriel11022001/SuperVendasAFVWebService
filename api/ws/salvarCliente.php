<?php

use Models\Cliente;
use Repositorio\ClienteRepositorio;
use Utils\ObterQueryCadastroCliente;

require_once "../autoload.php";

try {
    var_dump(ObterQueryCadastroCliente::obterQuery([
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
    ]));
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}