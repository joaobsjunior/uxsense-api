<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

class MessageData {

    public $id, $code, $message, $dateException, $nameCurrentResource, $additionalInformation;

    public function __construct() {
        $this->dateException = new \DateTime();
    }

    public function setMessage($msgEnum, $values) {
        if ($msgEnum && $values) {
            $compiler = _::template($msgEnum);
            $this->message = $compiler($values);
            return;
        }
        $this->message = $msgEnum;
    }

}
