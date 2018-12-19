<?php
namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Lib\AliyunUpload;

class DemoController extends Controller
{
    public function indexAction()
    {
        return view('demo.index');
    }

    public function uploadAction(){
        $file = $_FILES['file'];

        $upload = new AliyunUpload();
        $res = $upload->uploadFile($file, "/e/lala.jpg");
        var_dump($res);
    }
}
