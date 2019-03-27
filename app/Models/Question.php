<?php

namespace App\Models;

class Question {

    private $question, $id;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->question = @$data['question'];
    }

    /* GETS */

    public function getQuestion() {
        return $this->question;
    }

    public function getId() {
        return $this->id;
    }

    public function getData() {
        return [
            'id' => $this->id,
            'question' => $this->question
        ];
    }
    
    public function getDataDiff() {
        return [
            'question' => $this->question
        ];
    }

    /* SETS */

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function setId($id) {
        $this->id = $id;
    }

}
