<?php

// obter parâmetro vindo no corpo da requisição
function getParametro($nome = "") {

    if (empty($nome)) {

        throw new Exception("Informe o nome do parâmetro!");
    }

    $dadosCorpoRequisicao = file_get_contents('php://input');
    $dadosCorpoRequisicaoObj = json_decode($dadosCorpoRequisicao);
    $propriedadesObjetosRequisicao = get_object_vars($dadosCorpoRequisicaoObj);

    if (!key_exists($nome, $propriedadesObjetosRequisicao)) {

        throw new Exception('No objeto json não existe uma propriedade definida com o nome: ' . $nome);
    }

    if (is_string($propriedadesObjetosRequisicao[ $nome ])) {
        
        return trim($propriedadesObjetosRequisicao[ $nome ]);
    }

    return $propriedadesObjetosRequisicao[ $nome ];
}