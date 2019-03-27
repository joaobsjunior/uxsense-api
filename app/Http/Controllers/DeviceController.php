<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DeviceBO;
use App\Models\Device;

class DeviceController extends Controller {

    public function index() {
        $response = new Response([], 200);
        $data = DeviceBO::listAll();
        if ($data) {
            $size = count($data['devices']);
            for ($i = 0; $i < $size; $i++) {
                $data['devices'][$i] = $data['devices'][$i]->getData();
            }
            $response = new Response($data);
        }
        return $response;
    }

    public function store(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['name']);
        $isNotEmpty = !empty($array['name']);
        $device = new Device($array);
        if ($device->getId()) {
            $device_old = DeviceBO::get($device->getId());
            $diff = array_diff_assoc($device->getData(), $device_old->getData());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $device = DeviceBO::change($device->getId(), $diff);
            if ($device) {
                $response = new Response($device->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $device = DeviceBO::register($device);
            if ($device) {
                $response = new Response($device->getData());
            }
        }
        return $response;
    }

    public function show(Request $request, $id) {
        $response = new Response([], 400);
        $data = DeviceBO::get($id);
        if ($data) {
            $data = $data->getData();
            $response = new Response($data);
        }
        return $response;
    }

    public function destroy(Request $request, $id) {
        $response = new Response([], 400);
        if ($id) {
            if (DeviceBO::delete($id)) {
                $response = new Response([], 200);
            }
        }
        return $response;
    }

}
