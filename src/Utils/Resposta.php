<?php

namespace Utils;

class Resposta {

    public static function response(
        bool $ok = true,
        string $msg = "",
        mixed $dados = null
    ) {
        $resp = [
            "ok" => $ok,
            "msg" => $msg,
            "dados" => $dados
        ];

        echo json_encode($resp);
        exit();
    }

}