<?php

namespace Repositorio;

use Exception;
use Models\Permissao;
use PDO;

class PermissaoRepositorio extends Repositorio implements IPermissaoRepositorio {

    public function __construct(PDO $conexaoBandoDados)
    {   
        parent::__construct($conexaoBandoDados);
    }

    // cadastrar permissão
    public function cadastrarPermissao(Permissao $permissaoCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_permissoes(nome, status) VALUES(:nome, :status)");
        $stmt->bindValue(":nome", $permissaoCadastrar->nome, PDO::PARAM_STR);
        $stmt->bindValue(":status", $permissaoCadastrar->status, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            $permissaoCadastrar->permissaoId = $this->bancoDados->lastInsertId();
        } else {

            throw new Exception("Erro ao tentar-se cadastrar a permissão.");
        }
        
    }

    // editar permissão
    public function editarPermissao(Permissao $permissaoEditar) {
        $stmt = $this->bancoDados->prepare("UPDATE tb_permissoes SET nome = :nome, status = :status
        WHERE permissao_id = :permissao_id");

        $stmt->bindValue(":permissao_id", $permissaoEditar->permissaoId);
        $stmt->bindValue(":nome", $permissaoEditar->nome);
        $stmt->bindValue(":status", $permissaoEditar->status, PDO::PARAM_BOOL);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se editar a permissão.");
        }

    }

    // buscar permissão pelo id
    public function buscarPermissaoPeloId(int $idPermissao) {
        $query = "SELECT * FROM tb_permissoes WHERE permissao_id = :permissao_id";
        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":permissao_id", $idPermissao);
        $stmt->execute();

        $permissao = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($permissao)) {

            return null;
        }

        return new Permissao(
            $permissao["permissao_id"],
            $permissao["nome"],
            $permissao["status"]
        );
    }

    // buscar todas as permissões cadastradas na base de dados
    public function buscarTodasPermissoes() {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_permissoes");
        $stmt->execute();

        $permissoesArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $permissoes = [];

        foreach ($permissoesArray as $permissaoArray) {
            array_push($permissoes, new Permissao(
                $permissaoArray["permissao_id"],
                $permissaoArray["nome"],
                $permissaoArray["status"]
            ));
        }

        return $permissoes;
    }

    // deletar permissão na base de dados
    public function deletarPermissao(int $idPermissaoDeletar) {

        if (!$this->bancoDados
            ->prepare("DELETE FROM tb_permissoes WHERE permissao_id = $idPermissaoDeletar")
            ->execute()
        ) {

            throw new Exception("Erro ao tentar-se deletar a permissão na base de dados.");
        }

    }

    // buscar permissão pelo nome
    public function buscarPermissaoPeloNome(string $nomePermissao) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_permissoes WHERE nome = :nome");
        $stmt->bindValue(":nome", $nomePermissao);
        $stmt->execute();

        $permissaoArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($permissaoArray)) {
            $permissao = new Permissao();
            $permissao->permissaoId = $permissaoArray["permissao_id"];
            $permissao->nome = $permissaoArray["nome"];
            $permissao->status = $permissaoArray["status"];

            return $permissao;
        }

        return null;
    }

}