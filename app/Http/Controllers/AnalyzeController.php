<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AnalyzeController extends Controller
{
    public $remove = ["answer", "idclient", "idanswer", "sent", "idscheduler", "capture_technique_id", "idcapture_technique", "description", "idsubgroup", "idunit"];

    public function getAll(Request $request) {
       $response = new Response([], 400);
       $resultSet = DB::table('answer')
                ->select('*', 'capture_technique.name as capture_technique_name')
                ->join('capture_technique', 'answer.capture_technique_id', 'capture_technique.idcapture_technique')
                ->join('scheduler', 'answer.idscheduler', 'scheduler.idscheduler')
                ->join('question', 'scheduler.idquestion', 'question.idquestion')
                ->join('team', 'scheduler.idteam', 'team.idteam')
                ->get();
       foreach ($resultSet as &$value) {
           $json = json_decode($value->answer);
           $value->max = $json->max;
           $value->min = $json->min;
           $value->type = $json->type;
           foreach($json->data as $key => &$answer) {
               $value->{$key} = $answer;
           }
           foreach ($this->remove as $key => $attribute) {
                unset($value->{$attribute});   
           }
           
       }
       return new Response($resultSet, 200);
    }
    
    public function getByTechnique(Request $request, $id) {
        $response = new Response([], 400);
        if(isset($id) && empty(trim($id))){
            return $response;
        }
       $resultSet = DB::table('answer')
                ->select('*', 'capture_technique.name as capture_technique_name')
                ->join('capture_technique', 'answer.capture_technique_id', 'capture_technique.idcapture_technique')
                ->join('scheduler', 'answer.idscheduler', 'scheduler.idscheduler')
                ->join('question', 'scheduler.idquestion', 'question.idquestion')
                ->join('team', 'scheduler.idteam', 'team.idteam')
                ->where('capture_technique.idcapture_technique', $id)
                ->get();
       foreach ($resultSet as &$value) {
           $json = json_decode($value->answer);
           $value->max = $json->max;
           $value->min = $json->min;
           $value->type = $json->type;
           foreach($json->data as $key => &$answer) {
               $value->{$key} = $answer;
           }
           foreach ($this->remove as $key => $attribute) {
                unset($value->{$attribute});   
           }
           
       }
       return new Response($resultSet, 200);
    }

}
