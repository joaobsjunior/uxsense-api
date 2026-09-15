<?php

namespace App\Models;

class User {

    private $id, $name, $login, $password, $token, $plainPassword;

    /**
     * @param array $data
     * @param bool  $isCrypt  true when $data['password'] already is a stored hash
     */
    public function __construct($data = [], $isCrypt = false) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->login = $data['login'] ?? null;
        $this->token = $data['token'] ?? null;
        $this->setPassword($data['password'] ?? null, $isCrypt);
    }

    /* GETS */

    public function getName() {
        return $this->name;
    }

    public function getLogin() {
        return $this->login;
    }

    /**
     * The stored (hashed) password.
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * The plain text password supplied by the request, if any.
     */
    public function getPlainPassword() {
        return $this->plainPassword;
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
        if ($isCrypt) {
            $this->password = $password;
            $this->plainPassword = null;
        } elseif ($password === null || $password === '') {
            $this->password = null;
            $this->plainPassword = null;
        } else {
            $this->plainPassword = (string) $password;
            $this->password = Helpers::hashPassword($password);
        }
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setToken($token) {
        $this->token = $token;
    }

}
