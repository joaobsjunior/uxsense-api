<?php

namespace App\Models;

class Device {

    private $id, $client_id, $registrator_id, $platform, $uuid, $token;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->client_id = @$data['client_id'];
        $this->registrator_id = @$data['registrator_id'];
        $this->platform = @$data['platform'];
        $this->uuid = @$data['uuid'];
        $this->token = @$data['token'];
    }

    /* GETS */

    public function getId() {
        return $this->id;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getRegistratorId() {
        return $this->registrator_id;
    }

    public function getPlatform() {
        return $this->platform;
    }

    public function getUuid() {
        return $this->uuid;
    }

    public function getToken() {
        return $this->token;
    }

    public function getData() {
        return [
            'id' => $this->getId(),
            'client_id' => $this->getClientId(),
            'registrator_id' => $this->getRegistratorId(),
            'platform' => $this->getPlatform(),
            'uuid' => $this->getUuid(),
            'token' => $this->getToken(),
        ];
    }
    public function getDataDiff() {
        return [
            'client_id' => $this->getClientId(),
            'registrator_id' => $this->getRegistratorId(),
            'platform' => $this->getPlatform(),
            'uuid' => $this->getUuid(),
            'token' => $this->getToken(),
        ];
    }
    public function getDataDB() {
        return [
            'idclient' => $this->getClientId(),
            'registratorid' => $this->getRegistratorId(),
            'platform' => $this->getPlatform(),
            'uuid' => $this->getUuid(),
            'token' => $this->getToken(),
        ];
    }

    /* SETS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setRegistratorId($registrator_id) {
        $this->registrator_id = $registrator_id;
    }

    public function setPlatform($platform) {
        $this->platform = $platform;
    }

    public function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    public function setToken($token) {
        $this->token = $token;
    }

}
