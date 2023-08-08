<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {
    public static function login($login, $password) {
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN" => $login
        ));

        if (count($result) === 0) {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }

        $data = $result[0];

        if (password_verify($password, $data["despassword"]) === true) {
            $user = new User();

            $user->setiduser($data["iduser"]);
            var_dump($user);
            exit;
        } else {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
    }
}