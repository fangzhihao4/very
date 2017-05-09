<?php
namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;

class DemoController extends Controller
{
    public function indexAction()
    {
        return view('demo.index');
    }
}
