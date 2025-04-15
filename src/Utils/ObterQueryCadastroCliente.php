<?php

namespace Utils;

class ObterQueryCadastroCliente {

    // obter query para cadastrar cliente na base de dados
    public static function obterQuery(array $campos = []) {
        $query = "INSERT INTO (";

        foreach ($campos as $indice => $campo) {

            if ($indice < count($campos) - 1) {
                $query .= $campo . ", ";
            } else {
                $query .= $campo;
            }

        }

        $query .= ") VALUES(";

        foreach ($campos as $indice => $campo) {

            if ($indice < count($campos) - 1) {
                $query .= ":" . $campo . ", ";
            } else {
                $query .= ":" . $campo;
            }

        }

        $query .= ");";

        return $query;
    }

}