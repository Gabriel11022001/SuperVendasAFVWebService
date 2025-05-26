<?php

namespace Models;

use DateTime;

class Lead {

    public int $leadId;
    public string $tipoPessoa;
    public string $telefone;
    public string $email;
    public DateTime $dataCadastro;
    public bool $ativo;
    public int $vendedorId;
    public int|null $vendedorIdAnterior;

    // pessoa fisica
    public string $nomeCompleto;
    public string $cpf;
    public string $genero;
    public string $nomePai;
    public string $nomeMae;
    public string $tipoDocumento;
    public string $numeroDocumento;
    public DateTime|null $dataNascimento;

    // pessoa juridica
    public string $razaoSocial;
    public string $cnpj;
    public DateTime|null $dataFundacao;
    public float $valorPatrimonio;

    public array $statusLead;

    public function __construct()
    {
        $this->leadId = 0;
        $this->tipoPessoa = "";
        $this->telefone = "";
        $this->email = "";
        $this->dataCadastro = new DateTime();
        $this->ativo = true;
        $this->nomeCompleto = "";
        $this->cpf = "";
        $this->genero = "";
        $this->nomePai = "";
        $this->nomeMae = "";
        $this->tipoDocumento = "";
        $this->numeroDocumento = "";
        $this->dataNascimento = null;
        $this->razaoSocial = "";
        $this->cnpj = "";
        $this->dataFundacao = null;
        $this->valorPatrimonio = 0;

        $this->statusLead = array();
        $this->vendedorId = 0;
        $this->vendedorIdAnterior = null;
    }

}