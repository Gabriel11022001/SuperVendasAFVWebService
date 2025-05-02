<?php

namespace Repositorio;

use Exception;
use Models\NivelAcesso;
use Models\Permissao;
use Models\Usuario;
use PDO;

class UsuarioRepositorio extends Repositorio implements IUsuarioRepositorio {

    public function __construct(PDO $conexaoBancoDados)
    {
        parent::__construct($conexaoBancoDados);
    }

    // cadastrar usuário
    public function cadastrarUsuario(Usuario $usuarioCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_usuarios(nome_completo, email, login, senha, status, data_cadastro, nivel_acesso_id)
        VALUES(:nome_completo, :email, :login, :senha, :status, :data_cadastro, :nivel_acesso_id)");
        
        $stmt->bindValue(":nome_completo", $usuarioCadastrar->nomeCompleto);
        $stmt->bindValue(":email", $usuarioCadastrar->email);
        $stmt->bindValue(":login", $usuarioCadastrar->login);
        $stmt->bindValue(":senha", $usuarioCadastrar->senha);
        $stmt->bindValue(":status", $usuarioCadastrar->status);
        $stmt->bindValue(":data_cadastro", $usuarioCadastrar->dataCadastro);
        $stmt->bindValue(":nivel_acesso_id", $usuarioCadastrar->nivelAcessoId);

        if ($stmt->execute()) {
            $usuarioCadastrar->usuarioId = $this->bancoDados->lastInsertId();
        } else {

            throw new Exception("Erro ao tentar-se cadastrar o usuário!");
        }

    }

    // editar usuário
    public function editarUsuario(Usuario $usuarioEditar) {
        $stmt = $this->bancoDados->prepare("UPDATE tb_usuarios SET nome_completo = :nome_completo, email = :email,
        status = :status, nivel_acesso_id = :nivel_acesso_id
        WHERE usuario_id = :usuario_id");

        $stmt->bindValue(":usuario_id", $usuarioEditar->usuarioId);
        $stmt->bindValue(":nome_completo", $usuarioEditar->nomeCompleto);
        $stmt->bindValue(":email", $usuarioEditar->email);
        $stmt->bindValue(":status", $usuarioEditar->status);
        $stmt->bindValue(":nivel_acesso_id", $usuarioEditar->nivelAcessoId);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se editar o usuário!");
        }

    }

    public function buscarUsuarios(int $paginaAtual, int $elementosPorPagina) {

    }

    // deletar usuário
    public function deletarUsuario(int $idUsuarioDeletar) {
        $stmt = $this->bancoDados->prepare("DELETE FROM tb_usuarios WHERE usuario_id = :usuario_id");
        $stmt->bindValue(":usuario_id", $idUsuarioDeletar);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-de deletar o usuário!");
        }

    }

    // buscar usuário pelo login e senha
    public function buscarUsuarioPeloLoginSenha(string $login, string $senha) {
        $query = "SELECT tb_usuarios.usuario_id, tb_usuarios.nome_completo, tb_usuarios.email, tb_usuarios.login,
        tb_usuarios.status, tb_usuarios.nivel_acesso_id, tb_niveis_acesso.nome, tb_niveis_acesso.status AS status_nivel_acesso
        FROM tb_usuarios, tb_niveis_acesso
        WHERE tb_usuarios.nivel_acesso_id = tb_niveis_acesso.nivel_acesso_id
        AND tb_usuarios.login = :login AND tb_usuarios.senha = :senha";
        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":login", $login);
        $stmt->bindValue(":senha", $senha);
        $stmt->execute();
        $usuarioArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($usuarioArray)) {

            return null;
        }

        $usuario = new Usuario();
        $nivelAcesso = new NivelAcesso();

        $nivelAcesso->nivelAcessoId = $usuarioArray["nivel_acesso_id"];
        $nivelAcesso->nome = $usuarioArray["nome"];
        $nivelAcesso->status = $usuarioArray["status_nivel_acesso"];

        $usuario->nivelAcesso = $nivelAcesso;

        $usuario->usuarioId = $usuarioArray["usuario_id"];
        $usuario->nomeCompleto = $usuarioArray["nome_completo"];
        $usuario->email = $usuarioArray["email"];
        $usuario->login = $usuarioArray["login"];
        $usuario->status = $usuarioArray["status"];

        // obter as permissões do nivel de acesso do usuário
        $usuario->nivelAcesso->permissoes = $this->buscarPermissoesNivelAcessoUsuario($usuario->nivelAcesso->nivelAcessoId);

        return $usuario;
    }

    // buscar usuário pelo e-mail
    public function buscarUsuarioPeloEmail(string $email) {
        $query = "SELECT tb_usuarios.usuario_id, tb_usuarios.nome_completo, tb_usuarios.email, tb_usuarios.login,
        tb_usuarios.status, tb_usuarios.nivel_acesso_id, tb_niveis_acesso.nome, tb_niveis_acesso.status AS status_nivel_acesso
        FROM tb_usuarios, tb_niveis_acesso
        WHERE tb_usuarios.nivel_acesso_id = tb_niveis_acesso.nivel_acesso_id
        AND tb_usuarios.email = :email";
        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":email", $email);
        $stmt->execute();
        $usuarioArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($usuarioArray)) {

            return null;
        }

        $usuario = new Usuario();
        $nivelAcesso = new NivelAcesso();

        $nivelAcesso->nivelAcessoId = $usuarioArray["nivel_acesso_id"];
        $nivelAcesso->nome = $usuarioArray["nome"];
        $nivelAcesso->status = $usuarioArray["status_nivel_acesso"];

        $usuario->nivelAcesso = $nivelAcesso;

        $usuario->usuarioId = $usuarioArray["usuario_id"];
        $usuario->nomeCompleto = $usuarioArray["nome_completo"];
        $usuario->email = $usuarioArray["email"];
        $usuario->login = $usuarioArray["login"];
        $usuario->status = $usuarioArray["status"];

        // obter as permissões do nivel de acesso do usuário
        $usuario->nivelAcesso->permissoes = $this->buscarPermissoesNivelAcessoUsuario($usuario->nivelAcesso->nivelAcessoId);

        return $usuario;
    }

    public function buscarUsuarioPeloLogin(string $login) {
        
    }

    // buscar usuário pelo nome
    public function buscarUsuarioPeloNome(string $nome) {
        $query = "SELECT tb_usuarios.usuario_id, tb_usuarios.nome_completo, tb_usuarios.email, tb_usuarios.login,
        tb_usuarios.status, tb_usuarios.nivel_acesso_id, tb_niveis_acesso.nome, tb_niveis_acesso.status AS status_nivel_acesso
        FROM tb_usuarios, tb_niveis_acesso
        WHERE tb_usuarios.nivel_acesso_id = tb_niveis_acesso.nivel_acesso_id
        AND tb_usuarios.nome_completo = :nome_completo";
        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":nome_completo", $nome);
        $stmt->execute();
        $usuarioArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($usuarioArray)) {

            return null;
        }

        $usuario = new Usuario();
        $nivelAcesso = new NivelAcesso();

        $nivelAcesso->nivelAcessoId = $usuarioArray["nivel_acesso_id"];
        $nivelAcesso->nome = $usuarioArray["nome"];
        $nivelAcesso->status = $usuarioArray["status_nivel_acesso"];

        $usuario->nivelAcesso = $nivelAcesso;

        $usuario->usuarioId = $usuarioArray["usuario_id"];
        $usuario->nomeCompleto = $usuarioArray["nome_completo"];
        $usuario->email = $usuarioArray["email"];
        $usuario->login = $usuarioArray["login"];
        $usuario->status = $usuarioArray["status"];

        // obter as permissões do nivel de acesso do usuário
        $usuario->nivelAcesso->permissoes = $this->buscarPermissoesNivelAcessoUsuario($usuario->nivelAcesso->nivelAcessoId);

        return $usuario;
    }

    // buscar permissões relacionadas ao nivel de acesso do usuário
    private function buscarPermissoesNivelAcessoUsuario(int $idNivelAcessoUsuario): array {
        $permissoes = [];

        $stmt = $this->bancoDados->prepare("SELECT tb_permissoes.permissao_id, tb_permissoes.nome, tb_permissoes.status
        FROM tb_permissoes, tb_niveis_acesso, tb_permissao_nivel_acesso
        WHERE tb_permissoes.permissao_id = tb_permissao_nivel_acesso.permissao_id
        AND tb_niveis_acesso.nivel_acesso_id = tb_permissao_nivel_acesso.nivel_acesso_id
        AND tb_niveis_acesso.nivel_acesso_id = :id_nivel_acesso");
        $stmt->bindValue(":id_nivel_acesso", $idNivelAcessoUsuario);
        $stmt->execute();
        $permissoesArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($permissoesArray as $permissaoArray) {
            $permissao = new Permissao(
                $permissaoArray["permissao_id"],
                $permissaoArray["nome"],
                $permissaoArray["status"]
            );

            $permissoes[] = $permissao;
        }

        return $permissoes;
    }

}