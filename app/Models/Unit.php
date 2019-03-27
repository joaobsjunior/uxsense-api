<?php

namespace App\Models;

class Unit {

    private $id, $name, $latitude, $longitude;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
        $this->latitude = @$data['latitude'];
        $this->longitude = @$data['longitude'];
    }



    /* GETS */

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getGeolocation() {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function getData() {
        return [
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'id' => $this->id,
        ];
    }

     public function getDataDiff() {
        return [
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /* SETS */

    public function setName($name) {
        $this->name = $name;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    public function setLongitude($longitude){
        $this->longitude = $longitude;
    }

    public function setPassword($password) {
        $this->password = sha1(md5($password));
    }

    public function setId($id) {
        $this->id = $id;
    }

}
