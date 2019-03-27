<?php

namespace App\Models;

class Client {

    private $id, $name, $register, $password, $sex, $datebirth, $token, $email;

    public function __construct($data = [], $isCrypt = false) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
        $this->email = @$data['email'];
        $this->register = @$data['register'];
        $this->password = (!$isCrypt) ? sha1(md5(@$data['password'])) : @$data['password'];
        $this->sex = @$data['sex'];
        $this->datebirth = @$data['datebirth'];
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

    public function getPassword() {
        return $this->password;
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
        $this->password = (!$isCrypt) ? sha1(md5($password)) : $password;
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
