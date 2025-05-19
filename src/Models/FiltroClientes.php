<?php

namespace Models;

use DateTime;

class FiltroClientes {

    public int $paginaAtual = 1;
    public int $elementosPorPagina = 5;

    public string $tipoPessoa = "";
    public string $telefone = "";
    public string $email = "";
    public bool|null $status = null; 

    public string $nomeCompleto = "";
    public string $cpf = "";
    public string|null $dataNascimentoInicio = "";
    public string|null $dataNascimentoFinal = "";
    public string $numeroDocumento = "";

    public string $razaoSocial = "";
    public string $cnpj = "";

    // validar dados do filtro
    public function validar(): array {
        $erros = [];

        if (!empty($this->tipoPessoa)) {

            if ($this->tipoPessoa != "pf" && $this->tipoPessoa != "pj") {
                $erros["tipo_pessoa"] = "Tipo de pessoa inválido.";
            }

        }

        if (!empty($this->dataNascimentoInicio) && empty($this->dataNascimentoFinal)) {
            $erros["data_nascimento_final"] = "Informe a data de nascimento final.";
        } elseif (!empty($this->dataNascimentoFinal) && empty($this->dataNascimentoInicio)) {
            $erros["data_nascimento_inicio"] = "Informe a data de nascimento inicial.";
        } else {

            if (!empty($this->dataNascimentoInicio) && !empty($this->dataNascimentoFinal)) {
                $dataNascimentoInicio = new DateTime($this->dataNascimentoInicio);
                $dataNascimentoFinal = new DateTime($this->dataNascimentoFinal);

                if ($dataNascimentoFinal < $dataNascimentoInicio) {
                    $erros["data_nascimento_final"] = "A data de nascimento final deve ser maior ou igual a data de nascimento inicial.";
                }

            }

        }

        return $erros;
    }

    public function setParametrosPaginacao(int $paginaAtual, int $elementosPorPagina) {

        if ($paginaAtual <= 0) {
            $this->paginaAtual = 1;
        } else {
            $this->paginaAtual = $paginaAtual;
        }

        if ($elementosPorPagina < 5 || $elementosPorPagina > 10) {
            $this->elementosPorPagina = 5;
        } else {
            $this->elementosPorPagina = $elementosPorPagina;
        }

    }

}