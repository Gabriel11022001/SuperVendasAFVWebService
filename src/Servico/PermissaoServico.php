<?php

namespace Servico;

use Exception;
use Models\Permissao;
use Repositorio\IPermissaoRepositorio;
use Repositorio\PermissaoRepositorio;

class PermissaoServico extends ServicoBase implements IPermissaoServico {

    private IPermissaoRepositorio $permissaoRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->permissaoRepositorio = new PermissaoRepositorio($this->bancoDados);
    }

    // cadastrar permissão
    public function cadastrarPermissao() {

        try {
            $nomePermissao = getParametro("nome");
            $status = getParametro("status");
            $errosCampos = [];

            if (empty($nomePermissao)) {
                $errosCampos[] = "Informe o nome da permissão.";
            } elseif (strlen($nomePermissao) < 3) {
                $errosCampos[] = "O nome da permissão deve possuir no mínimo 3 caracteres.";
            }

            if (!empty($errosCampos)) {

                return [
                    "ok" => false,
                    "msg" => "Erro nos campos.",
                    "dados" => $errosCampos
                ];
            }

            // validar se já existe outra permissão cadastrada com o mesmo nome
            if (!empty($this->permissaoRepositorio->buscarPermissaoPeloNome($nomePermissao))) {

                return [
                    "ok" => false,
                    "msg" => "Já existe uma permissão cadastrada com o nome informado.",
                    "dados" => []
                ];
            }

            $permissao = new Permissao();
            $permissao->nome = trim($nomePermissao);
            $permissao->status = $status;

            $this->permissaoRepositorio->cadastrarPermissao($permissao);

            return [
                "ok" => true,
                "msg" => "Permissão cadastrada com sucesso.",
                "dados" => $permissao
            ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se cadastrar a permissão.",
                "dados" => $e->getMessage()
            ];
        }

    }

    public function editarPermissao() {
        
    }

    // deletar permissão
    public function deletarPermissao() {

        try {

            if (!isset($_GET["permissao_id"]) || empty($_GET["permissao_id"])) {

                throw new Exception("Informe o parâmetro permissao_id na url.");
            }

            $permissaoIdDeletar = intval(trim($_GET["permissao_id"]));

            // validar se a permissão existe na base de dados
            if (empty($this->permissaoRepositorio->buscarPermissaoPeloId($permissaoIdDeletar))) {

                return [
                    "ok" => false,
                    "msg" => "Permissão não encontrada.",
                    "dados" => []
                ];
            }

            $this->permissaoRepositorio->deletarPermissao($permissaoIdDeletar);

            return [
                "ok" => true,
                "msg" => "Permissão deletada com sucesso.",
                "dados" => []
            ];
        } catch (Exception $e) {
            // registrar o erro no arquivo de log

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se deletar a permissão.",
                "dados" => []
            ];
        }

    }

    public function buscarPermissoes() {
        
    }

    public function buscarPermissaoPeloId() {
        
    }

}
