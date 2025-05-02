<?php

namespace Servico;

use Exception;
use Repositorio\IUsuarioRepositorio;
use Repositorio\UsuarioRepositorio;
use Utils\Resposta;

class LoginServico extends ServicoBase implements ILoginServico {

    private IUsuarioRepositorio $usuarioRepositorio;

    public function __construct()
    {
        parent::__construct();
        
        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
    }

    private function validarCamposLogin(string $login, string $senha) {
        $erros = [];

        return $erros;
    }

    // login
    public function login() {
        
        try {
            $login = getParametro("login");
            $senha = getParametro("senha");
            $errosCampos = $this->validarCamposLogin($login, $senha);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos de login.", $errosCampos);
            }

            $senha = md5($senha);

            $usuario = $this->usuarioRepositorio->buscarUsuarioPeloLoginSenha($login, $senha);

            if (empty($usuario)) {
                Resposta::response(false, "Login ou senha inválidos.");
            }

            Resposta::response(true, "Login efetuado com sucesso.", $usuario);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se realizar login.", false);
        }

    }

    public function alterarSenha() {
        
    }

    public function logout() {
        
    }
    
}
