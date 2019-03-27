<?php

namespace App\Models;

class User {

    private $id, $name, $login, $password, $token;

    public function __construct($data = [], $isCrypt = false) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
        $this->login = @$data['login'];
        $this->password = (!$isCrypt) ? sha1(md5(@$data['password'])) : @$data['password'];
        $this->token = @$data['token'];
    }

    /* GETS */

    public function getName() {
        return $this->name;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getId() {
        return $this->id;
    }

    public function getToken() {
        return $this->token;
    }

    public function getData() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'login' => $this->getLogin(),
            'token' => $this->getToken(),
        ];
    }

    public function getDataDiff() {
        return [
            'name' => $this->getName(),
            'login' => $this->getLogin(),
            'token' => $this->getToken(),
            'password' => $this->getPassword()
        ];
    }
    
    public function getDataDB() {
        return [
            'name' => $this->getName(),
            'login' => $this->getLogin(),
            'token' => $this->getToken(),
            'password' => $this->getPassword()
        ];
    }

    /* SETS */

    public function setName($name) {
        $this->name = $name;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setPassword($password, $isCrypt = false) {
        $this->password = (!$isCrypt) ? sha1(md5($password)) : $password;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setToken($token) {
        $this->token = $token;
    }

}
