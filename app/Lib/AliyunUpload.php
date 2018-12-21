<?php
namespace App\Lib;

class AliyunUpload
{
    private $upload_url;
    private $app_key;
    private $app_secret;

    public function __construct(){
        $this->upload_url = env("VS_UPLOAD_URL");
        $this->app_key = env("VS_API_KEY");
        $this->app_secret = env("VS_API_SECRET");
    }

    /**
     * 上传图片
     * @param string $file
     * @param $upload_filename
     * @param int $timeout
     * @return mixed
     */
    public function uploadImg(string $file, $upload_filename, $timeout = 3){
        $nonce_str = date("YmdHis").mt_rand(10000000, 99999999);
        $params = [
            "app_key"=>$this->app_key,
            "nonce_str"=>$nonce_str,
            "filename"=>$upload_filename
        ];
        $sign = $this->getSign($params, $this->app_secret);
        $params["sign"] = $sign;
        $params['file'] = new \CURLFile(realpath($file));

        $res = $this->http($this->upload_url."/v1/aliyun/uploadimg", $params, "POST", [], $timeout);
        return $res;
    }

    /**
     * 上传文件
     * @param string $file
     * @param $upload_filename
     * @param int $timeout
     * @return mixed
     */
    public function uploadFile(string $file, $upload_filename, $timeout = 10){
        $nonce_str = date("YmdHis").mt_rand(10000000, 99999999);
        $params = [
            "app_key"=>$this->app_key,
            "nonce_str"=>$nonce_str,
            "filename"=>$upload_filename
        ];
        $sign = $this->getSign($params, $this->app_secret);
        $params["sign"] = $sign;
        $params['file'] = new \CURLFile(realpath($file));

        $res = $this->http($this->upload_url."/v1/aliyun/uploadfile", $params, "POST", [], $timeout);
        return $res;
    }

    /**
     * 上传url地址，支持图片和文件
     * @param string $file
     * @param $upload_filename
     * @param int $timeout
     * @return mixed
     */
    public function uploadUrl(string $file, $upload_filename, $timeout = 5){
        $nonce_str = date("YmdHis").mt_rand(10000000, 99999999);
        $params = [
            "app_key"=>$this->app_key,
            "nonce_str"=>$nonce_str,
            "filename"=>$upload_filename,
            "url"=>$file
        ];
        $sign = $this->getSign($params, $this->app_secret);
        $params["sign"] = $sign;

        $res = $this->http($this->upload_url."/v1/aliyun/uploadurl", $params, "POST", [], $timeout);
        return $res;
    }

    /**
     * 上传base64
     * @param string $file
     * @param $upload_filename
     * @param int $timeout
     * @return mixed
     */
    public function uploadBase64(string $file, $upload_filename, $timeout = 5){
        $nonce_str = date("YmdHis").mt_rand(10000000, 99999999);
        $params = [
            "app_key"=>$this->app_key,
            "nonce_str"=>$nonce_str,
            "filename"=>$upload_filename,
        ];
        $sign = $this->getSign($params, $this->app_secret);
        $params["sign"] = $sign;
        $params['base64_data'] = $file;

        $res = $this->http($this->upload_url."/v1/aliyun/uploadbase64", $params, "POST", [], $timeout);
        return $res;
    }

    /**
     * 获取签名
     * @param $data
     * @param $secret
     * @return string
     */
    private function getSign($data, $secret){
        ksort($data);
        $str = '';
        foreach ($data as $key=>$val){
            $str .= $key.'='.$val.'&';
        }

        return hash('sha256',substr($str,0,-1).$secret);
    }

    private function http($url, $data, $method, array $header=[], $timeout = 5){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if (is_string($data)) {
                $header[] = "Content-Type: application/json";
                $header[] = 'Content-Length: ' . strlen($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
        }
        if( !empty($header) ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return $result; // 返回数据
    }
}