<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Technique;
use App\Models\TechniqueBO;

class TechniqueController extends Controller
{
    public function index() {
        $response = new Response([], 200);
        $data = TechniqueBO::listAll();
        if ($data) {
            $size = count($data['techniques']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['techniques'][$i] = $data['techniques'][$i]->getData();
                }
            }
            $response = new Response($data);
        }
        return $response;
    }
}
