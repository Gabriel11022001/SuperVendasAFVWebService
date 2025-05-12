<?php

use Controllers\CategoriaController;
use Controllers\ClienteController;
use Controllers\NivelAcessoController;
use Controllers\PermissaoController;
use Controllers\ProdutoController;
use Controllers\Rota;
use Controllers\UsuarioController;

require_once "autoload.php";
require_once __DIR__ . "/configurar.php";
require_once __DIR__ . "/../src/Utils/getParametro.php";

try {   
    $rota = new Rota();

    switch ($rota->getRotaAtual()) {
        case "/":
            $rota->get("/");
            break;
        case "/clientes":
            // buscar clientes paginado
            $rota->get("/clientes", ClienteController::class, "buscarClientes");
            break;
        case "/clientes/buscar-pelo-id":
            // buscar cliente pelo id
            $rota->get("/clientes/buscar-pelo-id", ClienteController::class, "buscarClientePeloId");
            break;
        case "/clientes/cadastrar":
            // cadastrar cliente
            $rota->post("/clientes/cadastrar", ClienteController::class, "cadastrarCliente");
            break;
        case "/permissoes/cadastrar":
            // cadastrar permissão
            $rota->post("/permissoes/cadastrar", PermissaoController::class, "cadastrarPermissao");
            break;
        case "/permissoes/deletar":
            // deletar permissão
            $rota->delete("/permissoes/deletar", PermissaoController::class, "deletarPermissao");
            break;
        case "/permissoes":
            // buscar todas as permissões
            $rota->get("/permissoes", PermissaoController::class, "buscarPermissoes");
            break;
        case "/permissoes/editar":
            // editar permissão
            $rota->put("/permissoes/editar", PermissaoController::class, "editarPermissao");
            break;
        case "/permissoes/buscar-pelo-id":
            // buscar permissão pelo id
            $rota->get("/permissoes/buscar-pelo-id", PermissaoController::class, "buscarPermissaoPeloId");
            break;
        case "/niveisacesso/cadastrar":
            // cadastrar nivel de acesso
            $rota->post("/niveisacesso/cadastrar", NivelAcessoController::class, "cadastrarNivelAcesso");
            break;
        case "/niveisacesso":
            // buscar niveis de acesso
            $rota->get("/niveisacesso", NivelAcessoController::class, "buscarNiveisAcesso");
            break;
        case "/niveisacesso/deletar":
            // deletar nivel de acesso
            $rota->delete("/niveisacesso/deletar", NivelAcessoController::class, "deletarNivelAcesso");
            break;
        case "/niveisacesso/buscar-pelo-id":
            // buscar nivel de acesso pelo id
            $rota->get("/niveisacesso/buscar-pelo-id", NivelAcessoController::class, "buscarNivelAcessoPeloId");
            break;
        case "/usuarios/cadastrar":
            // cadastrar usuário
            $rota->post("/usuarios/cadastrar", UsuarioController::class, "cadastrarUsuario");
            break;
        case "/categorias/cadastrar":
            // cadastrar categoria
            $rota->post("/categorias/cadastrar", CategoriaController::class, "cadastrarCategoria");
            break;
        case "/categorias":
            // buscar categorias
            $rota->get("/categorias", CategoriaController::class, "buscarCategorias");
            break;
        case "/categorias/buscar-pelo-id":
            // buscar categoria pelo id
            $rota->get("/categorias/buscar-pelo-id", CategoriaController::class, "buscarCategoriaPeloId");
            break;
        case "/categorias/editar":
            // editar categoria de produto
            $rota->put("/categorias/editar", CategoriaController::class, "editarCategoria");
            break;
        case "/produtos/cadastrar":
            // cadastrar produto
            $rota->post("/produtos/cadastrar", ProdutoController::class, "cadastrarProduto");
            break;
        case "/produtos":
            // buscar produtos
            $rota->get("/produtos", ProdutoController::class, "buscarProdutos");
            break;
        case "/produtos/alterar-status":
            // alterar o status do produto
            $rota->put("/produtos/alterar-status", ProdutoController::class, "alterarStatusProduto");
            break;
        case "/produtos/filtrar":
            // filtrar produtos
            $rota->get("/produtos/filtrar", ProdutoController::class, "filtrarProdutos");
            break;
        default:
            $rota->get("/404");
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}