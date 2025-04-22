<?php

namespace Models;

class Usuario {

    public int $usuarioId;
    public string $nomeCompleto;
    public string $email;
    public string $login;
    public string $senha;
    public string $status;
    public string $dataCadastro;
    public int $nivelAcessoId = 0;
    public NivelAcesso|null $nivelAcesso = null;

}