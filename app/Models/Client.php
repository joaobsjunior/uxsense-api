<?php

namespace App\Models;

class Client {

    private $id, $name, $register, $password, $sex, $datebirth, $token, $email, $plainPassword;

    /**
     * @param array $data
     * @param bool  $isCrypt  true when $data['password'] already is a stored hash
     */
    public function __construct($data = [], $isCrypt = false) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->register = $data['register'] ?? null;
        $this->sex = $data['sex'] ?? null;
        $this->datebirth = $data['datebirth'] ?? null;
        $this->setPassword($data['password'] ?? null, $isCrypt);
    }

    /* GETS */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRegister() {
        return $this->register;
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

    public function getSex() {
        return $this->sex;
    }

    public function getDatebirth() {
        return $this->datebirth;
    }

    public function getToken() {
        return $this->token;
    }

    public function getData() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'register' => $this->getRegister(),
            'sex' => $this->getSex(),
            'datebirth' => $this->getDatebirth(),
        ];
    }

    public function getDataDiff($showPassword = false) {
        $data = [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'register' => $this->getRegister(),
            'sex' => $this->getSex(),
            'datebirth' => $this->getDatebirth(),
        ];
        if ($showPassword) {
            $data['password'] = $this->getPassword();
        }
        return $data;
    }

    /* SETS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setRegister($register) {
        $this->register = $register;
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

    public function setSex($sex) {
        $this->sex = $sex;
    }

    public function setDatebirth($datebirth) {
        if (Validation::isDate($datebirth)) {
            $this->datebirth = $datebirth;
        }
    }

}
