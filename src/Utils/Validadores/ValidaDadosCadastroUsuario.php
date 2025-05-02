<?php

namespace Utils\Validadores;

enum StatusCliente {
    case ATIVO;
    case INATIVO;
    case BLOQUEADO;
}

class ValidaDadosCadastroUsuario {

    public static function validar(
        string $nomeCompleto,
        string $email,
        string $login,
        string $status,
        string $senha,
        int $nivelAcessoId
    ) {
        $errosCampos = [];

        if (empty($nomeCompleto)) {
            $errosCampos["nome_completo"] = "Informe o nome completo do usuário.";
        } elseif (strlen($nomeCompleto) < 3 || strlen($nomeCompleto) > 100) {
            $errosCampos["nome_completo"] = "O nome completo do usuário deve ter entre 3 e 100 caracateres.";
        }

        if (empty($email)) {
            $errosCampos["email"] = "Informe o e-mail do usuário.";
        }

        if (empty($login)) {
            $errosCampos["login"] = "Informe o login do usuário.";
        } elseif (!self::validarLogin($login)) {
            $errosCampos["login"] = "Login inválido.";
        }

        if (empty($senha)) {
            $errosCampos["senha"] = "Informe a senha do usuário.";
        } elseif (!self::validarSenha($senha)) {
            $errosCampos["senha"] = "Senha inválida.";
        }

        return $errosCampos;
    }

    // validar login do usuário
    private static function validarLogin(string $login) {

        if (strlen($login) < 6 || strlen($login) > 100) {

            return false;
        }

        return true;
    }

    // validar senha do usuário
    private static function validarSenha(string $senha) {

        return true;
    }

}