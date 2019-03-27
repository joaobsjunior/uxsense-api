<?php

namespace App\Models;

class Answer {

    private $id, $date, $time, $answer, $latitude, $longitude, $idclient, $idscheduler, $client, $scheduler, $technique_id, $technique;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->date = @$data['date'];
        $this->time = @$data['time'];
        $this->answer = @$data['answer'];
        $this->latitude = @$data['latitude'];
        $this->longitude = @$data['longitude'];
        $this->setIdclient(@$data['client_id']);
        $this->setIdscheduler(@$data['scheduler_id']);
        $this->setTechniqueId(@$data['technique_id']);
    }

    /* GETS */

    public function getId() {
        return $this->id;
    }

    public function getDate() {
        return $this->date;
    }

    public function getTime() {
        return $this->time;
    }

    public function getAnswer() {
        return $this->answer;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function getIdclient() {
        return $this->idclient;
    }

    public function getIdscheduler() {
        return $this->idscheduler;
    }

    public function getTechniqueId() {
        return $this->technique_id;
    }

    public function getTechnique() {
        return $this->technique;
    }

    public function getClient() {
        return $this->client;
    }

    public function getScheduler() {
        return $this->scheduler;
    }

    public function getData() {
        return [
            "id" => $this->getId(),
            "date" => $this->getDate(),
            "time" => $this->getTime(),
            "answer" => $this->getAnswer(),
            "client" => $this->getClient()->getData(),
            "scheduler" => $this->getScheduler()->getData(),
            "latitude" => $this->getLatitude(),
            "longitude" => $this->getLongitude(),
            "technique" => $this->getTechnique()->getData(),
        ];
    }

    public function getDataDiff() {
        return [
            "date" => $this->getDate(),
            "time" => $this->getTime(),
            "answer" => $this->getAnswer(),
            "client_id" => $this->getIdclient(),
            "technique_id" => $this->getTechniqueId(),
            "scheduler_id" => $this->getIdscheduler(),
            "latitude" => $this->getLatitude(),
            "longitude" => $this->getLongitude(),
        ];
    }

    public function getDataDB() {
        return [
            "date" => $this->getDate(),
            "time" => $this->getTime(),
            "answer" => $this->getAnswer(),
            "idclient" => $this->getIdclient(),
            "idscheduler" => $this->getIdscheduler(),
            "capture_technique_id" => $this->getTechniqueId(),
            "latitude" => $this->getLatitude(),
            "longitude" => $this->getLongitude(),
        ];
    }

    /* SETS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function setAnswer($answer) {
        $this->answer = $answer;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    public function setIdclient($idclient) {
        $this->idclient = $idclient;
        $this->client = ClientBO::get($idclient);
    }

    public function setIdscheduler($idscheduler) {
        $this->idscheduler = $idscheduler;
        $this->scheduler = SchedulerBO::get($idscheduler);
    }

    public function setTechniqueId($technique_id) {
        $this->technique_id = $technique_id;
        $this->technique = TechniqueBO::get($technique_id);
    }

}
