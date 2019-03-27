<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Scheduler;
use App\Models\SchedulerBO;

class SchedulerController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $response = new Response([], 200);
        $data = $request->input();
        $params = [];
        foreach ($data as $key => $value) {
            if (trim($data[$key]) != "") {
                $params[$key] = $data[$key];
            }
        }
        $data = SchedulerBO::listAll($params);
        if ($data) {
            $size = count($data['schedulers']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['schedulers'][$i] = $data['schedulers'][$i]->getData();
                }
            }
            $response = new Response($data);
        }
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['date']) && isset($array['time']) && isset($array['question_id']);
        $isNotEmpty = !empty($array['date']) && !empty($array['time']) && !empty($array['question_id']);
        $scheduler = new Scheduler($array);
        if ($scheduler->getId()) {
            $scheduler_old = SchedulerBO::get($scheduler->getId());
            $diff = array_diff_assoc($scheduler->getDataDiff(), $scheduler_old->getDataDiff());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $scheduler = SchedulerBO::change($scheduler->getId(), $diff);
            if ($scheduler) {
                $response = new Response($scheduler->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $scheduler = SchedulerBO::register($array);
            if ($scheduler) {
                if ($scheduler["result"] == 'SUCESSO') {
                    $response = new Response($scheduler);
                } else {
                    $response = new Response($scheduler, 400);
                }
            }
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $response = new Response([], 400);
        if ($id) {
            $data = SchedulerBO::get($id);
            if ($data) {
                $response = new Response($data->getData());
            }
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $response = new Response([], 400);
        if ($id) {
            $data = SchedulerBO::delete($id);
            $data = ['deleted' => $data];
            $response = new Response($data);
        }
        return $response;
    }

    public function pendentByClient() {
        $data = SchedulerBO::getSchedulerPendentByClient();
        if ($data) {
            return new Response(SchedulerBO::getSchedulerPendentByClient()->getData(), 200);
        }
        return new Response("", 200);
    }

}
