<?php

namespace Repositorio;

use Exception;
use Models\NivelAcesso;
use Models\Permissao;
use PDO;

class NivelAcessoRepositorio extends Repositorio implements INivelAcessoRepositorio {

    public function __construct(PDO $conexaoBancoDados)
    {
        parent::__construct($conexaoBancoDados);
    }

    // cadastrar nivel de acesso
    public function cadastrarNivelAcesso(NivelAcesso $nivelAcesso) {
        $this->bancoDados->beginTransaction();

        $stmt = $this->bancoDados->prepare("INSERT INTO tb_niveis_acesso(nome, status) VALUES(:nome, :status)");
        $stmt->bindValue(":nome", $nivelAcesso->nome);
        $stmt->bindValue(":status", $nivelAcesso->status, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            $idNivelAcesso = $this->bancoDados->lastInsertId();

            // cadastrar na tb_permissao_nivel_acesso
            foreach ($nivelAcesso->permissoes as $permissao) {
                $stmt = $this->bancoDados->prepare("INSERT INTO tb_permissao_nivel_acesso(nivel_acesso_id, permissao_id)
                VALUES(:nivel_acesso_id, :permissao_id)");
                $stmt->bindValue(":nivel_acesso_id", $idNivelAcesso);
                $stmt->bindValue(":permissao_id", $permissao->permissaoId);

                if (!$stmt->execute()) {
                    $this->bancoDados->rollBack();

                    throw new Exception("Erro ao tentar-se cadastrar o nivel de acesso.");
                }

            }

            $nivelAcesso->nivelAcessoId = $idNivelAcesso;

            $this->bancoDados->commit();
        } else {
            $this->bancoDados->rollBack();

            throw new Exception("Erro ao tentar-se cadastrar o nivel de acesso.");
        }

    }

    public function editarNivelAcesso(NivelAcesso $nivelAcesso) {
        
    }

    // deletar nivel de acesso
    public function deletarNivelAcesso(int $idNivelAcessoDeletar) {
        // deletar na tb_permissao_nivel_acesso
        $this->bancoDados->beginTransaction();

        $stmt = $this->bancoDados->prepare("DELETE FROM tb_permissao_nivel_acesso WHERE nivel_acesso_id = :nivel_acesso_id");
        $stmt->bindValue(":nivel_acesso_id", $idNivelAcessoDeletar);

        if (!$stmt->execute()) {
            $this->bancoDados->rollBack();

            throw new Exception("Erro ao tentar-se deletar o nivel de acesso.");
        }

        // deletar na tb_niveis_acesso
        $stmt = $this->bancoDados->prepare("DELETE FROM tb_niveis_acesso WHERE nivel_acesso_id = :nivel_acesso_id");
        $stmt->bindValue(":nivel_acesso_id", $idNivelAcessoDeletar);

        if (!$stmt->execute()) {
            $this->bancoDados->rollBack();

            throw new Exception("Erro ao tentar-se deletar o nivel de acesso.");
        }

        $this->bancoDados->commit();
    }

    // buscar todos os niveis de acesso
    public function buscarNiveisAcesso() {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_niveis_acesso ORDER BY nome ASC");
        $stmt->execute();

        $niveisAcesso = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $niveisAcessoRetorno = [];

        foreach ($niveisAcesso as $nivelAcessoArray) {
            $nivelAcesso = new NivelAcesso();

            $nivelAcesso->nivelAcessoId = $nivelAcessoArray["nivel_acesso_id"];
            $nivelAcesso->nome = $nivelAcessoArray["nome"];
            $nivelAcesso->status = $nivelAcessoArray["status"];

            // buscar permissões
            $stmt = $this->bancoDados->prepare("SELECT tb_permissoes.permissao_id, tb_permissoes.nome, tb_permissoes.status FROM tb_permissoes, tb_permissao_nivel_acesso
            WHERE tb_permissoes.permissao_id = tb_permissao_nivel_acesso.permissao_id
            AND tb_permissao_nivel_acesso.nivel_acesso_id = :nivel_acesso_id");
            $stmt->bindValue(":nivel_acesso_id", $nivelAcesso->nivelAcessoId);
            $stmt->execute();

            $permissoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($permissoes as $permissaoArray) {
                $permissao = new Permissao();
                $permissao->permissaoId = $permissaoArray["permissao_id"];
                $permissao->nome = $permissaoArray["nome"];
                $permissao->status = $permissaoArray["status"];
                
                $nivelAcesso->permissoes[] = $permissao;
            }

            $niveisAcessoRetorno[] = $nivelAcesso;
        }

        return $niveisAcessoRetorno;
    }

    // buscar nivel de acesso pelo id
    public function buscarNivelAcessoPeloId(int $idNivelAcesso) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_niveis_acesso WHERE nivel_acesso_id = :nivel_acesso_id");
        $stmt->bindValue(":nivel_acesso_id", $idNivelAcesso);
        $stmt->execute();
        $nivelAcessoArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($nivelAcessoArray)) {
            $nivelAcesso = new NivelAcesso();
            $nivelAcesso->nivelAcessoId = $idNivelAcesso;
            $nivelAcesso->nome = $nivelAcessoArray["nome"];
            $nivelAcesso->status = $nivelAcessoArray["status"];

            // obter as permissões
            $nivelAcesso->permissoes = $this->getPermissoesNivelAcesso($idNivelAcesso);

            return $nivelAcesso;
        }

        return null;
    }

    // buscar nivel de acesso pelo nome
    public function buscarNivelAcessoPeloNome(string $nomeNivelAcesso) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_niveis_acesso WHERE nome = :nome");
        $stmt->bindValue(":nome", $nomeNivelAcesso);
        $stmt->execute();
        $nivelAcessoArray = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($nivelAcessoArray)) {
            $nivelAcesso = new NivelAcesso();
            $nivelAcesso->nivelAcessoId = $nivelAcessoArray["nivel_acesso_id"];
            $nivelAcesso->nome = $nivelAcessoArray["nome"];
            $nivelAcesso->status = $nivelAcessoArray["status"];

            // obter as permissões
            $nivelAcesso->permissoes = $this->getPermissoesNivelAcesso($nivelAcesso->nivelAcessoId);

            return $nivelAcesso;
        }

        return null;
    }

    // obter as permissões relacionadas ao nivel de acesso
    private function getPermissoesNivelAcesso(int $idNivelAcesso) {
        $stmt = $this->bancoDados->prepare("SELECT tb_permissoes.permissao_id, tb_permissoes.nome, tb_permissoes.status FROM
        tb_permissoes, tb_permissao_nivel_acesso
        WHERE tb_permissoes.permissao_id = tb_permissao_nivel_acesso.permissao_id
        AND tb_permissao_nivel_acesso.nivel_acesso_id = :nivel_acesso_id");
        $stmt->bindValue(":nivel_acesso_id", $idNivelAcesso);
        $stmt->execute();

        $permissoesArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $permissoes = [];

        if (!empty($permissoesArray)) {
            // possui permissões

            foreach ($permissoesArray as $permissaoArray) {
                $permissao = new Permissao(
                    $permissaoArray["permissao_id"],
                    $permissaoArray["nome"],
                    $permissaoArray["status"]
                );

                $permissoes[] = $permissao;
            }

        }

        return $permissoes;
    }

}