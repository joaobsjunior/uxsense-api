<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class QuestionBO {

    public static function register(Question $question) {
        $data = $question->getDataDiff();
        unset($data['id']);
        $callback = DB::table('question')->insertGetId($data);
        if ($callback) {
            $question->setId($callback);
            return $question;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('question')->where('idquestion', $id)->update($diff);
        if ($callback) {
            return QuestionBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('question')->where('idquestion', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function listAll() {
        $data = DB::table('question')
                ->orderBy('question', 'asc')
                ->get();
        $return = [];
        $return['questions'] = [];
        
        
        foreach ($data as $value) {
            $return['questions'][] = (new Question([
                'id' => $value->idquestion,
                'question' => $value->question
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('question')->where('idquestion', $id)->first();
        if ($data) {
            return (new Question([
                'id' => $data->idquestion,
                'question' => $data->question
            ]));
        }
        return false;
    }

}
