<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AnswerBO;
use App\Models\Answer;

class AnswerController extends Controller {

    public function getIndex(Request $request) {
        $data = AnswerBO::listAll();
        if ($data) {
            $size = count($data['answers']);
            for ($i = 0; $i < $size; $i++) {
                $data['answers'][$i] = $data['answers'][$i]->getData();
            }
        }else{
            $data = [];
            $data['answers'] = [];
        }

        return new Response($data, 200);
    }

    public function postIndex(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        // Answers always belong to the client that owns the authenticated
        // device; the client id sent in the payload is ignored.
        $idclient = $request->attributes->get('device')->idclient;
        $array['client_id'] = $idclient;
        $isIsset = isset($array['date']) && isset($array['time']) && isset($array['answer']) && isset($array['scheduler_id']) && isset($array['technique_id']);
        $isNotEmpty = $isIsset && ($array['date']) !== "" && ($array['time']) !== "" && ($array['answer']) !== "" && ($array['scheduler_id']) !== "" && ($array['technique_id']) !== "";
        $answer = new Answer($array);
        if ($answer->getId()) {
            $answer_old = AnswerBO::get($answer->getId(), $idclient);
            if (!$answer_old) {
                return new Response([], 404);
            }
            $diff = array_diff_assoc($answer->getDataDB(), $answer_old->getDataDB());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            unset($diff['idclient']);
            $answer = count($diff) > 0 ? AnswerBO::change($answer->getId(), $diff, $idclient) : $answer_old;
            if ($answer) {
                $response = new Response($answer->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $answer = AnswerBO::register($answer);
            if ($answer) {
                $response = new Response($answer->getData());
            }
        }
        return $response;
    }

}
