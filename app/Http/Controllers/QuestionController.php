<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\QuestionBO;
use App\Models\Question;

class QuestionController extends Controller
{
    public function index() {
        $response = new Response([], 200);
        $data = QuestionBO::listAll();
        if ($data) {
            $size = count($data['questions']);
            for ($i = 0; $i < $size; $i++) {
                $data['questions'][$i] = $data['questions'][$i]->getData();                
            }
            $response = new Response($data);
        }else{
            $data = [];
        }
        return $response;
    }

    public function store(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['question']);
        $isNotEmpty = !empty($array['question']);
        $question = new Question($array);
        if ($question->getId() && false) {
            $question_old = QuestionBO::get($question->getId());
            $diff = array_diff_assoc($question->getData(), $question_old->getData());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $question = QuestionBO::change($question->getId(), $diff);
            if ($question) {
                $response = new Response($question->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $question = QuestionBO::register($question);
            if ($question) {
                $response = new Response($question->getData());
            }
        }
        return $response;
    }

    public function show(Request $request, $id) {
        //TODO
    }

    public function destroy(Request $request, $id) {
        $response = new Response([], 412);
        $data = QuestionBO::delete($id);
        if($data){
            $response = new Response([],200);
        }
        return $response;
    }    
}
