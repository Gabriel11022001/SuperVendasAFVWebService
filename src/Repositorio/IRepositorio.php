<?php

namespace Repositorio;

interface IRepositorio {

    public function iniciarTransacao();

    public function rollBackTransacao();

    public function commitarTransacao();

}