<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function home(Request $request)
    {
        return view('front.home');
    }

    public function usage(Request $request)
    {
        return view('front.usage');
    }

}
