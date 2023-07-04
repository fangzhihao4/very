<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class BeforeMiddle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
//        return response()->redirectTo('common/index');
    }

    /***
     * 获取签名
     *
     * @param $params
     * @param $app_secret
     * @return string
     */
    private function getSign($params, $app_secret)
    {
        ksort($params, SORT_STRING);
        $arg = [];
        foreach ($params as $key => $val) {
            if (trim($key) === "sign" || trim($val) === "") {
                continue;
            } else {
                $arg[] = $key . "=" . $val;
            }
        }

        $arg = implode('&', $arg);
        return hash('sha256', $arg . $app_secret);
    }

    /**
     * 模拟提交参数，支持https提交 可用于各类api请求
     * @param string $url ： 提交的地址
     * @param array $data :POST数组或json字符串
     * @param string $method : POST/GET，默认GET方式
     * @return mixed
     */
    private function http($url, $data = '', $method = 'GET')
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            if (is_string($data)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 2); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return $result; // 返回数据
    }
}