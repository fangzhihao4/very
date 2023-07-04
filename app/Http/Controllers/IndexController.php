<?php


namespace App\Http\Controllers;


class IndexController extends Controller
{
    public function indexAction()
    {
        return view('common/index');
    }
}