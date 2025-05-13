<?php

namespace Servico;

use DateTime;
use Exception;
use LDAP\Result;
use Models\FiltroProdutos;
use Models\Produto;
use Repositorio\CategoriaRepositorio;
use Repositorio\ICategoriaRepositorio;
use Repositorio\IProdutoRepositorio;
use Repositorio\ProdutoRepositorio;
use Utils\Resposta;

class ProdutoServico extends ServicoBase implements IProdutoServico {

    private IProdutoRepositorio $produtoRepositorio;
    private ICategoriaRepositorio $categoriaRepositorio;
    private array $limitesListagemProdutos = [5, 10, 15, 50];

    public function __construct()
    {
        parent::__construct();

        $this->produtoRepositorio = new ProdutoRepositorio($this->bancoDados);
        $this->categoriaRepositorio = new CategoriaRepositorio($this->bancoDados);        
    }

    private function validarDadosProdutoCadastro(
        string $nomeProduto,
        string $descricao,
        float $precoCompra,
        float $precoVenda,
        int $estoque,
        float $percentualDesconto,
        int $categoriaId,
        string $dataVencimento
    ) {
        $erros = [];

        if (empty($nomeProduto)) {
            $erros["nome_produto"] = "Informe o nome do produto.";
        }

        if (empty($descricao)) {
            $erros["descricao"] = "Informe a descrição do produto.";
        }

        if (empty($precoCompra)) {
            $erros["preco_compra"] = "Informe o preço de compra do produto.";
        } else if ($precoCompra <= 0) {
            $erros["preco_compra"] = "Preço de compra inválido.";
        }

        if (empty($precoVenda)) {
            $erros["preco_venda"] = "Informe o preço de venda do produto.";
        } else if ($precoVenda <= 0) {
            $erros["preco_venda"] = "Preço de venda inválido.";
        }
        
        if (!empty($percentualDesconto) && ($percentualDesconto < 0 || $percentualDesconto > 100)) {
            $erros["percentual_desconto"] = "Percentual de desconto inválido, o mesmo deve estar entre 0% e 100%.";
        }

        if (!empty($estoque) && $estoque < 0) {
            $erros["estoque"] = "Unidades em estoque do produto inválida.";
        }

        if (empty($categoriaId)) {
            $erros["categoria_id"] = "Informe a categoria do produto.";
        }

        if (!empty($dataVencimento)) {
            $dataVencimento = new DateTime($dataVencimento);
            $dataAtual = new DateTime("now");

            if ($dataVencimento < $dataAtual) {
                $erros["data_vencimento"] = "Data de vencimento inválida.";
            }

        }

        return $erros;
    }

    // cadastrar produto
    public function cadastrarProduto() {
        
        try {
            $nomeProduto = getParametro("nome_produto");
            $descricao = getParametro("descricao");
            $precoCompra = getParametro("preco_compra");
            $precoVenda = getParametro("preco_venda");
            $estoque = getParametro("estoque");
            $status = getParametro("status");
            $percentualDesconto = getParametro("percentual_desconto");
            $urlFoto = getParametro("url_foto_produto");
            $categoriaId = getParametro("categoria_id");
            $dataVencimento = getParametro("data_vencimento");
            
            // validar dados do produto
            $erros = $this->validarDadosProdutoCadastro(
                $nomeProduto,
                $descricao,
                $precoCompra,
                $precoVenda,
                $estoque,
                $percentualDesconto,
                $categoriaId,
                $dataVencimento
            );

            if (!empty($erros)) {
                Resposta::response(false, "Erros nos campos.", $erros);
            }

            // validar se já existe outro produto cadastrado com o mesmo nome
            if (!empty($this->produtoRepositorio->buscarProdutoPeloNome($nomeProduto))) {
                Resposta::response(false, "Já existe outro produto cadastrado com o mesmo nome na base de dados.");
            }

            // validar se existe uma categoria cadastrada com o categoria_id informado
            if (empty($this->categoriaRepositorio->buscarCategoriaPeloId($categoriaId))) {
                Resposta::response(false, "Não existe uma categoria cadastrada com o id informado.");
            }

            $produto = new Produto();
            $produto->nome = $nomeProduto;
            $produto->unidadesEstoque = $estoque;
            $produto->status = $status;
            $produto->precoCompra = $precoCompra;
            $produto->precoVenda = $precoVenda;
            $produto->dataVencimento = new DateTime($dataVencimento);
            $produto->categoriaId = $categoriaId;
            $produto->urlFotoProduto = $urlFoto;
            $produto->percentualDesconto = $percentualDesconto;
            $produto->descricao = $descricao;

            $this->produtoRepositorio->cadastrarProduto($produto);

            return Resposta::response(true, "Produto cadastrado com sucesso.", $produto);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se cadastrar o produto.");
        }

    }

    public function editarProduto() {
        
    }

    public function deletarProduto() {
        
    }

    // buscar produto pelo id
    public function buscarProdutoPeloId() {
        
        try {

            if (!isset($_GET["produto_id"])) {
                Resposta::response(false, "Informe o id do produto na url.");
            }

            $produtoId = trim($_GET["produto_id"]);

            if (empty($produtoId)) {
                Resposta::response(false, "Informe o id do produto na url.");
            }

            $produto = $this->produtoRepositorio->buscarProdutoPeloId($produtoId);

            if (empty($produto)) {
                Resposta::response(true, "Produto não encontrado na base de dados.");
            }

            Resposta::response(true, "Produto encontrado com sucesso.", $produto);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se consultar o produto pelo id.");
        }

    }

    // buscar produtos de forma paginada
    public function buscarProdutos() {
        
        try {
            $paginaAtual = getParametro("pagina_atual");
            $elementosPorPagina = getParametro("elementos_por_pagina");

            if ($paginaAtual <= 0) {
                $paginaAtual = 1;
            }

            if (!in_array($elementosPorPagina, $this->limitesListagemProdutos)) {
                $elementosPorPagina = $this->limitesListagemProdutos[ 0 ];
            }

            $produtos = $this->produtoRepositorio->buscarProdutos($paginaAtual, $elementosPorPagina);

            if (empty($produtos)) {
                Resposta::response(true, "Não existem produtos cadastrados na base de dados.", []);
            }

            Resposta::response(true, "Produtos encontrados com sucesso.", $produtos);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se buscar os produtos.");
        }

    }

    // filtrar produtos
    public function filtrarProdutos() {
        
        try {   
            $filtroProdutos = new FiltroProdutos();
            $filtroProdutos->setPaginaAtual(getParametro("pagina_atual"));
            $filtroProdutos->setElementosPorPagina(getParametro("elementos_por_pagina"));
            $filtroProdutos->nomeProduto = getParametro("nome_produto");
            $filtroProdutos->status = getParametro("status");
            $filtroProdutos->descricao = getParametro("descricao");
            $filtroProdutos->estoque = getParametro("estoque");
            $filtroProdutos->categoriaProduto = getParametro("categoria_produto");
            $filtroProdutos->dataEntradaEstoqueInicial = getParametro("data_entrada_estoque_inicial");
            $filtroProdutos->dataEntradaEstoqueFinal = getParametro("data_entrada_estoque_final");
            $filtroProdutos->dataVencimentoInicial = getParametro("data_vencimento_inicial");
            $filtroProdutos->dataVencimentoFinal = getParametro("data_vencimento_final");
            $filtroProdutos->precoVendaInicial = getParametro("preco_venda_inicial");
            $filtroProdutos->precoVendaFinal = getParametro("preco_venda_final");

            $errosFiltro = $filtroProdutos->validarFiltro();

            if (!empty($errosFiltro)) {
                Resposta::response(false, "Erros no filtro.", $errosFiltro);
            }

            $produtos = $this->produtoRepositorio->filtrarProdutos($filtroProdutos);

            if (count($produtos) > 0) {
                Resposta::response(true, "Produtos listados com sucesso.", $produtos);
            }
            
            Resposta::response(true, "Produtos não encontrados.", []);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se filtrar os produtos.");
        }

    }

    // alterar o status do produto
    public function alterarStatusProduto() {
        
        try {
            $produtoId = getParametro("produto_id");
            $novoStatus = getParametro("status");

            if (empty($produtoId)) {
                Resposta::response(false, "Informe o id do produto.");
            }

            // validar se existe um produto cadastrado com o id informado
            $produtoAlterarStatus = $this->produtoRepositorio->buscarProdutoPeloId($produtoId);
            
            if (empty($produtoAlterarStatus)) {
                Resposta::response(false, "Produto não encontrado.");
            }

            $this->produtoRepositorio->alterarStatusProduto($produtoId, $novoStatus);

            Resposta::response(true, "O status do produto foi alterado com sucesso.");
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se alterar o status do produto.");
        }

    }

    public function registrarEntradaProdutoEstoque() {
        
    }

    public function registrarSaidaProdutoEstoque() {
        
    }

}