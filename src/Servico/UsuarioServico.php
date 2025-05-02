<?php

namespace Servico;

use DateTime;
use Exception;
use Models\Usuario;
use Repositorio\INivelAcessoRepositorio;
use Repositorio\IUsuarioRepositorio;
use Repositorio\NivelAcessoRepositorio;
use Repositorio\UsuarioRepositorio;
use Utils\Resposta;
use Utils\Validadores\ValidaDadosCadastroUsuario;

class UsuarioServico extends ServicoBase implements IUsuarioServico {

    private IUsuarioRepositorio $usuarioRepositorio;
    private INivelAcessoRepositorio $nivelAcessoRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
        $this->nivelAcessoRepositorio = new NivelAcessoRepositorio($this->bancoDados);
    }

    // cadastrar usuário
    public function cadastrarUsuario() {
        
        try {
            $nomeCompleto = getParametro("nome_completo");
            $status = getParametro("status");
            $email = getParametro("email");
            $login = getParametro("login");
            $senha = getParametro("senha");
            $nivelAcessoId = getParametro("nivel_acesso_id");
            $errosCampos = ValidaDadosCadastroUsuario::validar(
                $nomeCompleto,
                $email,
                $login,
                $status,
                $senha,
                $nivelAcessoId
            );

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            // validar se existe um nivel de acesso cadastrado com o id informado
            $nivelAcesso = $this->nivelAcessoRepositorio->buscarNivelAcessoPeloId($nivelAcessoId);

            if (empty($nivelAcesso)) {
                Resposta::response(false, "Nõa existem um nivel de acesso cadastrado com o id informado.");
            }

            // validar se já existe outro usuário cadastrado com o mesmo nome
            if (!empty($this->usuarioRepositorio->buscarUsuarioPeloNome($nomeCompleto))) {
                Resposta::response(false, "Já existe outro usuário cadastrado com o mesmo nome.");
            }

            // validar se já existe um usuário cadastrado com o mesmo e-mail

            // validar se já existe um usuário cadastrado com o mesmo login

            $usuario = new Usuario();
            $usuario->nomeCompleto = $nomeCompleto;
            $usuario->email = $email;
            $usuario->login = $login;
            $usuario->senha = md5($senha);
            $usuario->status = $status;
            $usuario->nivelAcessoId = $nivelAcessoId;
            $usuario->nivelAcesso = $nivelAcesso;

            $dataCadastro = new DateTime("now");
            $usuario->dataCadastro = $dataCadastro->format("Y-m-d H:i:s");

            $this->usuarioRepositorio->cadastrarUsuario($usuario);

            Resposta::response(true, "Usuário cadastrado com sucesso.", $usuario);
        } catch (Exception $e) {
            // registrar erro no arquivo de log

            Resposta::response(false, "Erro ao tentar-se cadastrar um usuário.", $e->getMessage());
        }
    }

    public function editarUsuario() {
        
    }

    public function buscarUsuarios() {
        
    }

    public function deletarUsuario() {
        
    }

}