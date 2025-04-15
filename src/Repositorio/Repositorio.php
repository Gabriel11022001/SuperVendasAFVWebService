<?php

namespace Repositorio;

use PDO;

abstract class Repositorio {

    protected PDO $bancoDados;

    public function __construct(PDO $conexaoBancoDados)
    {
        $this->bancoDados = $conexaoBancoDados;
    }

}