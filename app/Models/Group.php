<?php

namespace App\Models;

class Group {

    protected $name, $id;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
    }

    /* GETS */

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getData() {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
    public function getDataDiff() {
        return [
            'name' => $this->name
        ];
    }

    /* SETS */

    public function setName($name) {
        $this->name = $name;
    }

    public function setId($id) {
        $this->id = $id;
    }

}
