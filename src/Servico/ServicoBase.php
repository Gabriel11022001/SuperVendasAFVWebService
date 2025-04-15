<?php

namespace Servico;

use PDO;
use Utils\BancoDados;

class ServicoBase {

    protected PDO $bancoDados;

    public function __construct()
    {
        // obter conexão com o banco de dados para passar para os repositórios       
        $this->bancoDados = BancoDados::conectarBancoDados(); 
    }

}