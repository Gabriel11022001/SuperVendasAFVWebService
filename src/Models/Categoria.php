<?php

namespace Models;

class Categoria {

    public int $categoriaId;
    public string $nome;
    public string $status;

    public function __construct(
        int $categoriaId = 0,
        string $nome = "",
        bool $status = true
    )
    {
        $this->categoriaId = $categoriaId;
        $this->nome = $nome;
        $this->status = $status;
    }

}