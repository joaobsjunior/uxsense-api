<?php

namespace App\Models;

class Activity {

    private $description, $id;

    public function __construct($data = []) {
        $this->description = @$data['description'];
        $this->id = @$data['id'];
    }

        /* GETS */

    public function getDescription() {
        return $this->description;
    }

    public function getId() {
        return $this->id;
    }

    public function getData() {
        return [
            'id' => $this->id,
            'description' => $this->description
        ];
    }
    
    public function getDataDiff() {
        return [
            'description' => $this->description
        ];
    }

    /* SETS */

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setId($id) {
        $this->id = $id;
    }

}
