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

        $file_name = "/admin/laravel/demo/lala.jpg";    // 有意义的文件路径，便于区分
        $res = $this->aliyunUpload->uploadFile($file, $file_name);
        var_dump($res);
    }
}
