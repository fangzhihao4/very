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

        $str = "https://static-cdn.verystar.net/e/zegna/img/video-null.jpg";


        $file_name = "/e/demo/aaa/erwer.jpg";
        $res = $this->aliyunUpload->uploadUrl($str, $file_name);
        var_dump($res);
    }
}
