<?php

namespace Repositorio;

use DateTime;
use Exception;
use Models\Categoria;
use Models\FiltroProdutos;
use Models\Produto;
use PDO;

class ProdutoRepositorio extends Repositorio implements IProdutoRepositorio {

    public function __construct(PDO $bancoDados)
    {
        parent::__construct($bancoDados);
    }

    // cadastrar produto na base de dados
    public function cadastrarProduto(Produto $produtoCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_produtos(nome, descricao, preco_compra, preco_venda, status, percentual_desconto,
        url_foto_produto, unidades_estoque, data_vencimento, data_entrada_estoque, categoria_id) VALUES(:nome, :descricao, :preco_compra, :preco_venda,
        :status, :percentual_desconto, :url_foto_produto, :unidades_estoque, :data_vencimento, :data_entrada_estoque, :categoria_id)");

        $stmt->bindValue(":nome", $produtoCadastrar->nome);
        $stmt->bindValue(":descricao", $produtoCadastrar->descricao);
        $stmt->bindValue(":status", $produtoCadastrar->status);
        $stmt->bindValue(":preco_compra", $produtoCadastrar->precoCompra);
        $stmt->bindValue(":preco_venda", $produtoCadastrar->precoVenda);
        $stmt->bindValue(":percentual_desconto", $produtoCadastrar->percentualDesconto);
        $stmt->bindValue(":url_foto_produto", $produtoCadastrar->urlFotoProduto);
        $stmt->bindValue(":unidades_estoque", $produtoCadastrar->unidadesEstoque);
        $stmt->bindValue(":data_vencimento", !empty($produtoCadastrar->dataVencimento) ? $produtoCadastrar->dataVencimento->format("Y-m-d") : null);
        $stmt->bindValue(":data_entrada_estoque", !empty($produtoCadastrar->dataEntradaEstoque) ? $produtoCadastrar->dataEntradaEstoque->format("Y-m-d") : null);
        $stmt->bindValue(":categoria_id", $produtoCadastrar->categoriaId);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se cadastrar o produto na base de dados.");
        }

        $produtoCadastrar->produtoId = $this->bancoDados->lastInsertId();
    }

    public function editarProduto(Produto $produtoEditar) {
        
    }

    public function deletarProduto(int $idProdutoDeletar) {
        
    }

    // buscar produto pelo id
    public function buscarProdutoPeloId(int $idProduto) {
        $stmt = $this->bancoDados->prepare("SELECT p.produto_id, p.nome, p.descricao, p.preco_compra, p.preco_venda,
        p.status, p.data_entrada_estoque, p.data_vencimento, p.url_foto_produto, p.categoria_id, p.unidades_estoque,
        c.nome AS nome_categoria, c.status AS status_categoria
        FROM tb_produtos AS p INNER JOIN tb_categorias AS c
        ON p.categoria_id = c.categoria_id
        AND p.produto_id = :produto_id");

        $stmt->bindValue(":produto_id", $idProduto);
        $stmt->execute();

        $produtoArray = $stmt->fetch(PDO::FETCH_ASSOC);

        $produto = new Produto();
        $produto->produtoId = $idProduto;
        $produto->nome = $produtoArray["nome"];
        $produto->descricao = $produtoArray["descricao"];
        $produto->status = $produtoArray["status"];
        $produto->precoCompra = $produtoArray["preco_compra"];
        $produto->precoVenda = $produtoArray["preco_venda"];
        $produto->categoriaId = $produtoArray["categoria_id"];
        $produto->dataEntradaEstoque = !empty($produtoArray["data_entrada_estoque"]) ? new DateTime($produtoArray["data_entrada_estoque"]) : null;
        $produto->dataVencimento = !empty($produtoArray["data_vencimento"]) ? new DateTime($produtoArray["data_vencimento"]) : null;
        $produto->urlFotoProduto = $produtoArray["url_foto_produto"];
        $produto->unidadesEstoque = $produtoArray["unidades_estoque"];
        $produto->categoria = new Categoria(
            $produtoArray["categoria_id"],
            $produtoArray["nome_categoria"],
            $produtoArray["status_categoria"]
        );

        return $produto;
    }

    // buscar produtos de forma paginada
    public function buscarProdutos(int $paginaAtual, int $elementosPorPagina) {
        $stmt = $this->bancoDados->prepare("SELECT p.produto_id, p.nome, p.status, p.descricao, p.preco_compra, p.preco_venda,
        p.data_entrada_estoque, p.data_vencimento, p.percentual_desconto, p.url_foto_produto,
        p.unidades_estoque, p.categoria_id, c.nome AS nome_categoria, c.status AS status_categoria
        FROM tb_produtos AS p, tb_categorias AS c
        WHERE p.categoria_id = c.categoria_id
        LIMIT :limit OFFSET :offset");

        $stmt->bindValue(":limit", $elementosPorPagina);
        $stmt->bindValue(":offset", ($paginaAtual - 1) * $elementosPorPagina);
        $stmt->execute();

        $produtosArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $produtos = [];

        if (!empty($produtosArray)) {

            foreach ($produtosArray as $prodArray) {
                $produto = new Produto();
                $produto->produtoId = $prodArray["produto_id"];
                $produto->nome = $prodArray["nome"];
                $produto->status = $prodArray["status"];
                $produto->descricao = $prodArray["descricao"];
                $produto->precoCompra = $prodArray["preco_compra"];
                $produto->precoVenda = $prodArray["preco_venda"];
                $produto->dataEntradaEstoque = empty($prodArray["data_entrada_estoque"]) ? null : new DateTime($prodArray["data_entrada_estoque"]);
                $produto->dataVencimento = empty($prodArray["data_vencimento"]) ? null : new DateTime($prodArray["data_vencimento"]);
                $produto->unidadesEstoque = $prodArray["unidades_estoque"];
                $produto->categoriaId = $prodArray["categoria_id"];
                $produto->urlFotoProduto = $prodArray["url_foto_produto"];
                $produto->categoria = new Categoria(
                    $prodArray["categoria_id"],
                    $prodArray["nome_categoria"],
                    $prodArray["status_categoria"]
                );

                $produtos[] = $produto;
            }

        }

        return $produtos;
    }

    public function alterarStatusProduto(int $produtoId, bool $novoStatus) {
        
    }

    // filtrar produtos na base de dados
    public function filtrarProdutos(FiltroProdutos $filtroProdutos) {
        $query = "SELECT p.produto_id, p.nome, p.descricao, p.unidades_estoque, p.status,
        p.data_entrada_estoque, p.data_vencimento, p.categoria_id, p.preco_compra, p.preco_venda,
        c.nome AS nome_categoria, c.status AS status_categoria
        FROM tb_produtos AS p INNER JOIN tb_categorias AS c
        OM p.categoria_id = c.categoria_id ";

        if (!empty($filtroProdutos->nomeProduto)) {
            $query .= " AND p.nome = :nome ";
        }

        if (!empty($filtroProdutos->descricao)) {
            $query .= " AND p.descricao = :descricao ";
        }

        if (!empty($filtroProdutos->estoque)) {
            $query .= " AND p.unidades_estoque = :unidades_estoque ";
        }

        if (!empty($filtroProdutos->categoriaProduto)) {
            $query .= " AND p.categoria_id = :categoria_id ";
        }

        if ($filtroProdutos->status != null) {
            $query .= " AND p.status = :status ";
        }

        if (!empty($filtroProdutos->precoVendaInicial) && !empty($filtroProdutos->precoVendaFinal)) {
            $query .= " AND p.preco_venda >= :preco_venda_inicial AND p.preco_venda <= :preco_venda_final ";
        }

        $query .= " ORDER BY p.nome ASC";

        $stmt = $this->bancoDados->prepare($query);

        if (!empty($filtroProdutos->nomeProduto)) {
            $stmt->bindValue(":nome", $filtroProdutos->nomeProduto);
        }

        if (!empty($filtroProdutos->descricao)) {
            $stmt->bindValue(":descricao", $filtroProdutos->descricao);
        }

        if (!empty($filtroProdutos->estoque)) {
            $stmt->bindValue(":unidades_estoque", $filtroProdutos->estoque);
        }

        if (!empty($filtroProdutos->categoriaProduto)) {
            $stmt->bindValue(":categoria_id", $filtroProdutos->categoriaProduto);
        }

        if ($filtroProdutos->status != null) {
            $stmt->bindValue(":status", $filtroProdutos->status);
        }

        if (!empty($filtroProdutos->precoVendaInicial) && !empty($filtroProdutos->precoVendaFinal)) {
            $stmt->bindValue(":preco_venda_inicial", $filtroProdutos->precoVendaInicial);
            $stmt->bindValue(":preco_venda_final", $filtroProdutos->precoVendaFinal);
        }

        $stmt->execute();
        $produtosArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $produtos = [];

        if (!empty($produtosArray)) {

            foreach ($produtosArray as $produtoArray) {
                $produto = new Produto();

                $produto->produtoId = $produtoArray["produto_id"];
                $produto->nome = $produtoArray["nome"];
                $produto->descricao = $produtoArray["descricao"];
                $produto->status = $produtoArray["status"];
                $produto->unidadesEstoque = $produtoArray["unidades_estoque"];
                $produto->dataVencimento = !empty($produtoArray["data_vencimento"]) ? new DateTime($produtoArray["data_vencimento"]) : null;
                $produto->dataEntradaEstoque = !empty($produtoArray["data_entrada_estoque"]) ? new DateTime($produtoArray["data_entrada_estoque"]) : null;
                $produto->categoriaId = $produtoArray["categoria_id"];
                $produto->precoCompra = $produtoArray["preco_compra"];
                $produto->precoVenda = $produtoArray["preco_venda"];
                $produto->categoria = new Categoria(
                    $produtoArray["categoria_id"],
                    $produtoArray["nome_categoria"],
                    $produtoArray["status_categoria"]
                );

                $produtos[] = $produto;
            }

        }

        return $produtos;
    }

    public function registrarEntradaProdutoEstoque(int $produtoId, int $unidadesEntrada, DateTime $dataVencimentoProduto) {
        
    }

    public function registrarSaidaProdutoEstoque(int $produtoId, int $unidadesSaida) {
        
    }

    // buscar produto pelo nome
    public function buscarProdutoPeloNome(string $nomeProduto) {
        $stmt = $this->bancoDados->prepare("SELECT p.produto_id, p.nome, p.status, p.descricao, p.preco_compra, p.preco_venda,
        p.data_entrada_estoque, p.data_vencimento, p.percentual_desconto, p.url_foto_produto,
        p.unidades_estoque, p.categoria_id, c.nome AS nome_categoria, c.status AS status_categoria
        FROM tb_produtos AS p, tb_categorias AS c
        WHERE p.categoria_id = c.categoria_id
        AND p.nome = :nome_produto");
        $stmt->bindValue(":nome_produto", $nomeProduto);
        $stmt->execute();

        $produtoArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($produtoArray)) {

            return null;
        }

        $produto = new Produto();
        $produto->produtoId = $produtoArray["produto_id"];
        $produto->nome = $produtoArray["nome"];
        $produto->descricao = $produtoArray["descricao"];
        $produto->status = $produtoArray["array"];
        $produto->categoriaId = $produtoArray["categoria_id"];
        $produto->precoCompra = $produtoArray["preco_compra"];
        $produto->precoVenda = $produtoArray["preco_venda"];
        $produto->unidadesEstoque = $produtoArray["unidades_estoque"];
        $produto->dataEntradaEstoque = empty($produtoArray["data_entrada_estoque"]) ? null : new DateTime($produtoArray["data_entrada_estoque"]);
        $produto->dataVencimento = empty($produtoArray["data_vencimento"]) ? null : new DateTime($produtoArray["data_vencimento"]);
        $produto->status = $produtoArray["status"];

        $produto->categoria = new Categoria(
            $produtoArray["categoria_id"],
            $produtoArray["nome_categoria"],
            $produtoArray["status_categoria"]
        );

        return $produto;
    }

}