<?php
namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Lib\AliyunUpload;

class DemoController extends Controller
{
    protected $aliyunUpload;

    public function __construct( AliyunUpload $aliyunUpload ){
        $this->aliyunUpload = $aliyunUpload;
    }

    public function indexAction()
    {
        return view('demo.index');
    }

    public function uploadAction(){
        $file = $_FILES['file'];

        $file_name = "/e/demo/aaa/erwer.jpg";
        $res = $this->aliyunUpload->uploadUrl("https://static-cdn.verystar.net/e/demo/aaa/457bb9b3615df4baee01be27d41d83c3.jpeg", $file_name);
        var_dump($res);
    }
}
