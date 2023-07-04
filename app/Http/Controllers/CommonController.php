<?php


namespace App\Http\Controllers;



class CommonController extends Controller
{
    public function indexAction()
    {
        return view('common/index');
    }
}