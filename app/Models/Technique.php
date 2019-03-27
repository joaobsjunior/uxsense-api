<?php

namespace App\Models;

class Technique {

    protected $id, $name, $description;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
        $this->description = @$data['description'];
    }

    /* GETS */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getData() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        ];
    }

    public function getDataDiff() {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        ];
    }

    /* SETS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

}
