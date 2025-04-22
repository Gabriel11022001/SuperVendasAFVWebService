<?php

namespace Models;

class Permissao {

    public int $permissaoId;
    public string $nome;
    public bool $status;

    public function __construct(
        int $permissaoId = 0,
        string $nome = "",
        bool $status = true
    )
    {
        $this->permissaoId = $permissaoId;
        $this->nome = $nome;
        $this->status = $status;
    }

}