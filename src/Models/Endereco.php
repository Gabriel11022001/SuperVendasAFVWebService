<?php

namespace Models;

class Endereco {

    public $enderecoId;
    public $cep;
    public $complemento;
    public $logradouro;
    public $bairro;
    public $cidade;
    public $uf;
    public $numero;
    public $clienteId;

    public function __construct()
    {
        $this->enderecoId = 0;
        $this->cep = "";
        $this->logradouro = "";
        $this->complemento = "";
        $this->cidade = "";
        $this->bairro = "";
        $this->uf = "";
        $this->numero = "";
        $this->clienteId = 0;
    }
    
}