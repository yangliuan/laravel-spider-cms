<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GraphicsController extends Controller
{
    public function index(Request $request)
    {

        return view('graphics');
    }
}
