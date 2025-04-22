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

    private function validarCamposEditarPermissao($id, $nome, $status) {
        $errosCampos = [];

        if (empty($id)) {
            $errosCampos["permissao_id"] = "Informe o id da permissão.";
        } elseif ($id < 0) {
            $errosCampos["permissao_id"] = "O id da permissão é inválido.";
        }

        if (empty($nome)) {
            $errosCampos["nome"] = "Informe o nome da permissão.";
        } elseif (strlen($nome) < 3) {
            $errosCampos["nome"] = "O nome da permissão deve possuir no mínimo 3 caracteres.";
        }

        return $errosCampos;
    }

    // editar permissão
    public function editarPermissao() {

        try {
            $idPermissaoEditar = getParametro("permissao_id");
            $nome = getParametro("nome");
            $status = getParametro("status");
            $errosCampos = $this->validarCamposEditarPermissao($idPermissaoEditar, $nome, $status);

            if (!empty($errosCampos)) {

                return [
                    "ok" => false,
                    "msg" => "Erros nos campos.",
                    "dados" => $errosCampos
                ];
            }

            // validar se existe uma permissão cadastrada com o id informado
            if (empty($this->permissaoRepositorio->buscarPermissaoPeloId($idPermissaoEditar))) {

                return [
                    "ok" => false,
                    "msg" => "Permissão não encontrada.",
                    "dados" => []
                ];
            }

            // validar se já existe outra permissão cadastrada com o mesmo nome
            $permissaoCadastradaMesmoNome = $this->permissaoRepositorio->buscarPermissaoPeloNome($nome);

            if (!empty($permissaoCadastradaMesmoNome) && $permissaoCadastradaMesmoNome->permissaoId != $idPermissaoEditar) {

                return [
                    "ok" => false,
                    "msg" => "Já existe outra permissão cadastrada com esse mesmo nome.",
                    "dados" => []
                ];
            }

            $permissaoEditar = new Permissao();
            $permissaoEditar->permissaoId = $idPermissaoEditar;
            $permissaoEditar->nome = $nome;
            $permissaoEditar->status = $status;

            $this->permissaoRepositorio->editarPermissao($permissaoEditar);

            return [
                "ok" => true,
                "msg" => "Permissão editada com sucesso.",
                "dados" => $permissaoEditar
            ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se editar a permissão na base de dados.",
                "dados" => []
            ];
        }

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

    // buscar todas as permissões
    public function buscarPermissoes() {

        try {
            $permissoes = $this->permissaoRepositorio->buscarTodasPermissoes();

            if (count($permissoes) == 0) {

                return [
                    "ok" => true,
                    "msg" => "Não existem permissões cadastradas na base de dados.",
                    "dados" => $permissoes
                ];
            }

            return [
                "ok" => true,
                "msg" => "Permissões encontradas com sucesso.",
                "dados" => $permissoes
            ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se consultar todas as permissões.",
                "dados" => []
            ];
        }
    }

    // buscar permissão pelo id
    public function buscarPermissaoPeloId() {

        try {

            if (!isset($_GET["permissao_id"])) {

                return [
                    "ok" => false,
                    "msg" => "Informe o id da permissão na url.",
                    "dados" => []
                ];
            }

            $idPermissao = trim($_GET["permissao_id"]);

            if (empty($idPermissao)) {

                return [
                    "ok" => false,
                    "msg" => "Informe o id da permissão.",
                    "dados" => []
                ];
            }

            $permissao = $this->permissaoRepositorio->buscarPermissaoPeloId($idPermissao);

            if (empty($permissao)) {

                return [
                    "msg" => "Permissão não encontrada.",
                    "ok" => false,
                    "dados" => []
                ];
            }

            return [
                "ok" => true,
                "msg" => "Permissão encontrada com sucesso.",
                "dados" => $permissao
            ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se buscar a permissão pelo id.",
                "dados" => []
            ];
        }

    }

}
