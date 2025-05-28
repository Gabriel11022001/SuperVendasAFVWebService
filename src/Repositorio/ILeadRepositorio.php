<?php

namespace Repositorio;

use Models\Lead;
use Models\LeadStatus;

interface ILeadRepositorio {

    function cadastrarLead(Lead $leadCadastrar);

    function editarLead(Lead $leadEditar);

    function deletarLead(Lead $leadDeletar);

    function alterarVendedorLead(int $idVendedorNovo, int|null $idVendedorAnterior, int $idLead);

    function buscarLeadPeloId(int $idLeadConsultar);

    function buscarLeads(int $idVendedor, int $paginaAtual, int $elementosPorPagina);

    function registrarStatusLead(LeadStatus $leadStatusRegistrar);

    function buscarLeadPeloEmail(string $email);

    function buscarLeadPeloTelefone(string $telefone);

    function buscarLeadCpf(string $cpf);

    function buscarLeadPeloNumeroDocumento(string $numeroDocumento);

    function buscarLeadPeloCnpj(string $cnpj);

}