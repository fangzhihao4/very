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

        $res = file_get_contents("https://static.verystar.cn/e/zegna/img/video-null.jpg");
        $base64 = base64_encode($res);

        $file_name = "/e/demo/aaa/erwer.jpg";
        $res = $this->aliyunUpload->uploadBase64($base64, $file_name);
        var_dump($res);
    }
}
