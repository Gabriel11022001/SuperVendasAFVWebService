<?php

namespace Servico;

use Exception;
use Models\NivelAcesso;
use Models\Permissao;
use Repositorio\INivelAcessoRepositorio;
use Repositorio\IPermissaoRepositorio;
use Repositorio\NivelAcessoRepositorio;
use Repositorio\PermissaoRepositorio;
use Utils\Resposta;

class NivelAcessoServico extends ServicoBase implements INivelAcessoServico { 

    private INivelAcessoRepositorio $nivelAcessoRepositorio;
    private IPermissaoRepositorio $permissaoRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->nivelAcessoRepositorio = new NivelAcessoRepositorio($this->bancoDados);
        $this->permissaoRepositorio = new PermissaoRepositorio($this->bancoDados);
    }

    // cadastrar nivel de acesso
    public function cadastrarNivelAcesso() {
        
        try {
            $nome = getParametro("nome");
            $status = getParametro("status");
            $permissoes = getParametro("permissoes");
            $errosCampos = [];

            if (empty($nome)) {
                $errosCampos["nome"] = "Informe o nome do nivel de acesso.";
            } elseif (strlen($nome) < 3) {
                $errosCampos["nome"] = "O nome do nível de acesso não deve ser menor que 3 caracteres.";
            }

            if (empty($permissoes)) {
                $errosCampos["permissoes"] = "Informe pelo menos uma permissão para o nível de acesso.";
            }

            if (!empty($errosCampos)) {

                return [
                    "ok" => false,
                    "msg" => "Erros nos campos.",
                    "dados" => $errosCampos
                ];
            }

            // validar se já existe outro nivel de acesso cadastrado com o mesmo nome
            if (!empty($this->nivelAcessoRepositorio->buscarNivelAcessoPeloNome($nome))) {

                return [
                    "ok" => false,
                    "msg" => "Já existe um nivel de acesso cadastrado com o mesmo nome.",
                    "dados" => []
                ]; 
            }

            $permissoesQueNaoExistem = [];

            // validar se as permissões existem
            foreach ($permissoes as $permissaoId) {

                if (empty($this->permissaoRepositorio->buscarPermissaoPeloId($permissaoId))) {
                    $permissoesQueNaoExistem[] = $permissaoId;
                }

            }

            if (!empty($permissoesQueNaoExistem)) {

                return [
                    "ok" => false,
                    "msg" => "Foram informadas permissões que não estão cadastradas na base de dados.",
                    "dados" => $permissoesQueNaoExistem
                ]; 
            }

            $nivelAcesso = new NivelAcesso();
            $nivelAcesso->nome = $nome;
            $nivelAcesso->status = $status;
            
            foreach ($permissoes as $permissaoId) {
                $permissao = new Permissao();
                $permissao->permissaoId = $permissaoId;

                $nivelAcesso->permissoes[] = $permissao;
            }

            $this->nivelAcessoRepositorio->cadastrarNivelAcesso($nivelAcesso);

            return [
                "ok" => true,
                "msg" => "Nível acesso cadastrado com sucesso.",
                "dados" => $nivelAcesso
            ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se cadastrar o nivel de acesso na base de dados.",
                "dados" => []
            ];
        }

    }

    public function editarNivelAceso() {

    }

    // deletar nivel de acesso
    public function deletarNivelAcesso() {
        
        try {
            
            if (!isset($_GET["nivel_acesso_id"])) {

                return [
                    "ok" => "false",
                    "msg" => "Informe o parâmetro nivel_acesso_id na url.",
                    "dados" => [] 
                ];
            }

            $id = trim($_GET["nivel_acesso_id"]);

            if (empty($id)) {

                return [
                    "ok" => false,
                    "msg" => "Informe o nivel de acesso.",
                    "dados" => []
                ];
            }

            // validar se existe um nivel de acesso cadastrado com o id informado
            if (empty($this->nivelAcessoRepositorio->buscarNivelAcessoPeloId($id))) {

                return [
                    "ok" => false,
                    "dados" => [],
                    "msg" => "Nível de acesso não encontrado."
                ];
            }

            $this->nivelAcessoRepositorio->deletarNivelAcesso($id);

            return [ "ok" => true, "msg" => "Nivel de acesso deletado com sucesso.", "dados" => [] ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "msg" => "Erro ao tentar-se deletar o nivel de acesso.",
                "dados" => []
            ];
        }

    }

    // buscar todos os niveis de acesso
    public function buscarNiveisAcesso() {
        
        try {
            $niveisAcesso = $this->nivelAcessoRepositorio->buscarNiveisAcesso();

            if (count($niveisAcesso) == 0) {

                return [
                    "ok" => true,
                    "msg" => "Não existem niveis de acesso cadastrados na base de dados.",
                    "dados" => []
                ];
            }

            return [
                "ok" => true,
                "Níveis de acesso encontrados com sucesso.",
                "dados" => $niveisAcesso
            ];
        } catch (Exception $e) {

            return [
                "ok" => false,
                "dados" => $e->getMessage(),
                "msg" => "Erro ao tentar-se consultar os niveis de acesso."
            ];
        }

    }

    // buscar nivel de acesso pelo id
    public function buscarNivelAcessoPeloId() {

        try {

            if (!isset($_GET["nivel_acesso_id"])) {
                Resposta::response(false, "Informe o parâmetro nivel_acesso_id na url.");
            }

            $id = trim($_GET["nivel_acesso_id"]);

            if (empty($id)) {
                Resposta::response(false, "Informe o id do nivel de acesso.");
            }

            $nivelAcesso = $this->nivelAcessoRepositorio->buscarNivelAcessoPeloId($id);

            if (empty($nivelAcesso)) {
                Resposta::response(false, "Nivel de acesso não encontrado.");
            }

            Resposta::response(true, "Nível de acesso encontrado com sucesso.", $nivelAcesso);
        } catch (Exception $e) {
            // registrar no arquivo de log

            Resposta::response(false, "Erro ao tentar-se buscar o nivel de acesso pelo id.");
        }

    }

}
