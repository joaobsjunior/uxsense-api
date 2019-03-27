<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ActivityBO;
use App\Models\Activity;

class ActivityController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $response = new Response([], 200);
        $data = ActivityBO::listAll();
        if ($data) {
            $size = count($data['activities']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['activities'][$i] = $data['activities'][$i]->getData();
                }
            }
            $response = new Response($data);
        }
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
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
        $isIsset = isset($array['description']);
        $isNotEmpty = !empty($array['description']);
        $activity = new Activity($array);
        if ($activity->getId()) {
            $activity_old = ActivityBO::get($activity->getId());
            $diff = array_diff_assoc($activity->getData(), $activity_old->getData());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $activity = ActivityBO::change($activity->getId(), $diff);
            if ($activity) {
                $response = new Response($activity->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $activity = ActivityBO::register($activity);
            if ($activity) {
                $response = new Response($activity->getData());
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
            $data = ActivityBO::get($id);
            if ($data) {
                $response = new Response($data->getData());
            }
        }
        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $response = new Response([], 412);
        $data = ActivityBO::delete($id);
        if($data){
            $response = new Response([],200);
        }
        return $response;
    }

}
