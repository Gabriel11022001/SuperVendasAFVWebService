<?php

namespace Repositorio;

use PDO;

abstract class Repositorio {

    protected PDO $bancoDados;

    public function __construct(PDO $conexaoBancoDados)
    {
        $this->bancoDados = $conexaoBancoDados;
    }

    public function iniciarTransacao() {
        $this->bancoDados->beginTransaction();
    }

    public function rollBackTransacao() {
        $this->bancoDados->rollBack();
    }

    public function commitarTransacao() {
        $this->bancoDados->commit();
    }

}