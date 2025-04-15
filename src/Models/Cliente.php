<?php

namespace Models;

class Cliente {

    public int $clienteId;
    public string $tipoPessoa;
    public string $telefonePrincipal;
    public string $telefoneSecundario;
    public string $emailPrincipal;
    public string $emailSecundario;
    public bool $status;
    public array $enderecos = [];

    // pessoa fisica
    public string $nomeCompleto;
    public string $cpf;
    public string|null $dataNascimento;
    public string $tipoDocumento;
    public string $documento;
    public string $genero;
    public string $nomeMae;
    public string $nomePai;
    
    // pessoa juridica
    public string $razaoSocial;
    public string $cnpj;
    public string|null $dataFundacao;
    public float $valorPatrimonio;

    public function __construct()
    {
        $this->clienteId = 0;
        $this->tipoPessoa = "";
        $this->telefonePrincipal = "";
        $this->telefoneSecundario = "";
        $this->emailPrincipal = "";
        $this->emailSecundario = "";
        $this->status = true;
        $this->nomeCompleto = "";
        $this->cpf = "";
        $this->dataNascimento = "";
        $this->tipoDocumento = "";
        $this->documento = "";
        $this->genero = "";
        $this->nomeMae = "";
        $this->nomePai = "";
        $this->razaoSocial = "";
        $this->cnpj = "";
        $this->dataFundacao = "";
        $this->valorPatrimonio = 0;   
    }
    
}