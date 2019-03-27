<?php

namespace App\Models;

class Scheduler {

    private $id, $time, $date, $question, $team, $technique, $question_id, $team_id, $technique_id;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->time = @$data['time'];
        $this->date = @$data['date'];
        $this->setQuestionId(@$data['question_id']);
        $this->setTeamId(@$data['team_id']);
        $this->setTechniqueId(@$data['technique_id']);
    }

    /* GETS */

    public function getDate() {
        return $this->date;
    }

    public function getTime() {
        return $this->time;
    }

    public function getId() {
        return $this->id;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function getTeam() {
        return $this->team;
    }

    public function getTechnique() {
        return $this->technique;
    }

    public function getQuestionId() {
        return $this->question_id;
    }

    public function getTeamId() {
        return $this->team_id;
    }

    public function getTechniqueId() {
        return $this->technique_id;
    }

    public function getData() {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'question' => $this->question->getData(),
            'team' => $this->team->getData(),
            'technique' => $this->technique->getData()
        ];
    }

    public function getDataDiff() {
        return [
            'date' => $this->date,
            'time' => $this->time,
            'question_id' => $this->getQuestionId(),
            'team_id' => $this->getTeamId(),
            'technique_id' => $this->getTechniqueId()
        ];
    }

    public function getDataDB() {
        return [
            'date' => $this->date,
            'time' => $this->time,
            'idquestion' => $this->getQuestionId(),
            'idteam' => $this->getTeamId(),
            'capture_technique_id' => $this->getTechniqueId()
        ];
    }


    /* SETS */

    public function setDate($date) {
        if (Validation::isDate($date)) {
            $this->date = $date;
        }
    }

    public function setTime($time) {
        if (Validation::isTime($time)) {
            $this->time = $time;
        }
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setQuestionId($id) {
        $this->question_id = $id;
        if (!$this->question) {
            $this->question = QuestionBO::get($id);
        }
    }

    public function setTeamId($id) {
        $this->team_id = $id;
        if (!$this->team) {
            $this->team = TeamBO::get($id);
        }
    }

    public function setTechniqueId($id) {
        $this->technique_id = $id;
        if (!$this->technique) {
            $this->technique = TechniqueBO::get($id);
        }
    }

    public function setQuestion(Question $question) {
        $this->question = $question;
    }

    public function setTeam(Team $team) {
        $this->team = $team;
    }

    public function setTechnique(Technique $technique) {
        $this->technique = $technique;
    }

}
