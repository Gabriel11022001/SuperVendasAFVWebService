<?php

// gerenciar autoload
function carregarNamespace($nomeClasse) {
    $diretorio_classes = __DIR__ . "/../src/";

    // Substitui \ por / e adiciona .php
    $caminhoClasse = $diretorio_classes . str_replace("\\", "/", $nomeClasse) . ".php";

    if (file_exists($caminhoClasse)) {

        require_once $caminhoClasse;
    } else {

        throw new Exception("Classe " . $caminhoClasse . " não encontrada!");
    }

}

// registrar o autoload
spl_autoload_register("carregarNamespace");