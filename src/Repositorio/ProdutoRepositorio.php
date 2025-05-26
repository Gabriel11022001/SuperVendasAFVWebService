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

    // editar produto
    public function editarProduto(Produto $produtoEditar) {
        $stmt = $this->bancoDados->prepare("UPDATE tb_produtos SET nome = :nome, status = :status, preco_compra = :preco_compra,
        preco_venda = :preco_venda, data_vencimento = :data_vencimento, categoria_id = :categoria_id,
        url_foto_produto = :url_foto_produto, descricao = :descricao, unidades_estoque = :unidades_estoque,
        percentual_desconto = :percentual_desconto
        WHERE produto_id = :produto_id");

        $stmt->bindValue(":produto_id", $produtoEditar->produtoId);
        $stmt->bindValue(":nome", $produtoEditar->nome);
        $stmt->bindValue(":status", $produtoEditar->status);
        $stmt->bindValue(":preco_compra", $produtoEditar->precoCompra);
        $stmt->bindValue(":preco_venda", $produtoEditar->precoVenda);
        $stmt->bindValue(":data_vencimento", empty($produtoEditar->dataVencimento) ? null : $produtoEditar->dataVencimento->format("Y-m-d"));
        $stmt->bindValue(":categoria_id", $produtoEditar->categoriaId);
        $stmt->bindValue(":descricao", $produtoEditar->descricao);
        $stmt->bindValue(":unidades_estoque", $produtoEditar->unidadesEstoque);
        $stmt->bindValue(":url_foto_produto", $produtoEditar->urlFotoProduto);
        $stmt->bindValue(":percentual_desconto", $produtoEditar->percentualDesconto);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se editar o produto.");
        }

    }

    // deletar produto
    public function deletarProduto(int $idProdutoDeletar) {
        $stmt = $this->bancoDados->prepare("DELETE FROM tb_produtos WHERE produto_id = :produto_id");
        $stmt->bindValue(":produto_id", $idProdutoDeletar);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se deletar o produto na base de dados.");
        }

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

        if (empty($produtoArray)) {

            return null;
        }

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

                if (empty($prodArray["data_vencimento"])) {
                    $produto->dataVencimento = null;
                } else {
                    $produto->dataVencimento = new DateTime($prodArray["data_vencimento"]);
                }

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

    // alterar o status do produto
    public function alterarStatusProduto(int $produtoId, bool $novoStatus) {
        $stmt = $this->bancoDados->prepare("UPDATE tb_produtos SET status = :status WHERE produto_id = :produto_id");
        $stmt->bindValue(":status", $novoStatus, PDO::PARAM_BOOL);
        $stmt->bindValue(":produto_id", $produtoId);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se alterar o status do produto.");
        }

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

    // registrar saida de estoque do produto
    public function registrarSaidaProdutoEstoque(int $produtoId, int $unidadesSaida) {
        $stmt = $this->bancoDados->prepare("SELECT unidades_estoque FROM tb_produtos WHERE produto_id = :produto_id");
        $stmt->bindValue(":produto_id", $produtoId);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($produto)) {
            $unidadesEstoque = $produto["unidades_estoque"];
            $novaQuantidadeUnidadesEstoque = $unidadesEstoque - $unidadesSaida;

            $stmt = $this->bancoDados->prepare("UPDATE tb_produtos SET unidades_estoque = :unidades_estoque WHERE produto_id = :produto_id");
            $stmt->bindValue(":produto_id", $produtoId);
            $stmt->bindValue(":unidades_estoque", $novaQuantidadeUnidadesEstoque);

            if (!$stmt->execute()) {

                throw new Exception("Erro ao tentar-se debitar a quantidade de unidades em estoque do produto.");
            }

        }

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
        $produto->status = $produtoArray["status"];
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

    // buscar produtos pelo id da categoria
    public function buscarProdutosPelaCategoria(int $idCategoriaProduto) {
        $stmt = $this->bancoDados->prepare("SELECT p.produto_id, p.nome, p.status, p.descricao, p.preco_compra, p.preco_venda,
        p.data_entrada_estoque, p.data_vencimento, p.percentual_desconto, p.url_foto_produto,
        p.unidades_estoque, p.categoria_id, c.nome AS nome_categoria, c.status AS status_categoria
        FROM tb_produtos AS p, tb_categorias AS c
        WHERE p.categoria_id = c.categoria_id
        AND p.categoria_id = :categoria_id");

        $stmt->bindValue(":categoria_id", $idCategoriaProduto);
        $stmt->execute();
        $produtosArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $produtos = [];

        foreach ($produtosArray as $produtoArray) {
            $produto = new Produto();
            $produto->produtoId = $produtoArray["produto_id"];
            $produto->nome = $produtoArray["nome"];
            $produto->status = $produtoArray["status"];
            $produto->descricao = $produtoArray["descricao"];
            $produto->precoCompra = $produtoArray["preco_compra"];
            $produto->precoVenda = $produtoArray["preco_venda"];
            $produto->dataVencimento = empty($produtoArray["data_vencimento"]) ? null : new DateTime($produtoArray["data_vencimento"]);
            $produto->dataEntradaEstoque = empty($produtoArray["data_entrada_estoque"]) ? null : new DateTime($produtoArray["data_entrada_estoque"]);
            $produto->percentualDesconto = $produtoArray["percentual_desconto"];
            $produto->urlFotoProduto = $produtoArray["url_foto_produto"];
            $produto->unidadesEstoque = $produtoArray["unidades_estoque"];
            $produto->categoriaId = $produtoArray["categoria_id"];
            $produto->categoria = new Categoria(
                $produtoArray["categoria_id"],
                $produtoArray["nome_categoria"],
                $produtoArray["status_categoria"]
            );

            $produtos[] = $produto;
        }

        return $produtos;
    }

}