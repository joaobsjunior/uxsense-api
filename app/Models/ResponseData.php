<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

class ResponseData {

    public $isSuccess, $size, $message, $response;

    public function __construct() {
        $this->isSuccess = false;
        $this->size = 0;
        $this->message = new MessageData();
        $this->response = [];
    }

}
