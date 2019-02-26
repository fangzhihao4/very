<?php
namespace App\Lib;

class AdminOperationLog
{
    private $admin_operation_log_url;
    private $admin_operation_log_app_key;
    private $admin_operation_log_app_secret;

    public function __construct(){
        $this->admin_operation_log_url = env("ADMIN_OPERATION_LOG_URL");
        $this->admin_operation_log_app_key = env("ADMIN_OPERATION_LOG_APP_KEY");
        $this->admin_operation_log_app_secret = env("ADMIN_OPERATION_LOG_APP_SECRET");
    }

    /**
     * 添加后台操作日志log
     * @param string $system_name   操作的菜单
     * @param string $operation     操作的内容（比如添加菜单，修改菜单之类的描述）
     * @param $target        (操作的数据的唯一识别)
     * @param string $before_data   操作前的数据内容
     * @param string $after_data    操作后的数据内容
     * @return mixed
     */
    public function adminCollect(string $system_name, string $operation, $target, string $before_data = "", string $after_data = ""){
        $time = date("Y-m-d H:i:s");
        // 如果不是对接open的品牌系统，请修改这里获取登录用户的信息
        $auth_id = session("user_info")["auth_info"]["auth_id"];
        $auth_app_key = session("user_info")["auth_info"]["app_key"];
        $user_id = session("user_info")["user"]["user_id"];
        $username = session("user_info")["user"]["user_name"];
        $router = request()->path();
        $ip = $this->getIP();
        $params = [
            "auth_id"=>$auth_id,
            "app_key"=>$this->admin_operation_log_app_key,
            "auth_app_key"=>$auth_app_key,
            "user_id"=>$user_id,
            "username"=>$username,
            "router"=>$router,
            "system_name"=>$system_name,
            "operation"=>$operation,
            "target"=>$target,
            "before_data"=>$before_data,
            "after_data"=>$after_data,
            "ip"=>$ip,
            "create_time"=>$time,
            "update_time"=>$time,
        ];
        $create_sign_params = [
            "auth_id"=>$auth_id,
            "app_key"=>$this->admin_operation_log_app_key,
            "auth_app_key"=>$auth_app_key,
            "user_id"=>$user_id,
            "username"=>$username,
        ];
        $sign = $this->getSign($create_sign_params, $this->admin_operation_log_app_secret);
        $params["sign"] = $sign;

        return $this->http($this->admin_operation_log_url . "/log/collect/adminoperationcollect", json_encode($params), "POST");
    }

    private function http($url, $data = '', $method = 'GET', $header=[]){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if (is_string($data)) {
                $header[] = 'Content-Type: application/json';
                $header[] = 'Content-Length: ' . strlen($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
            }
        }

        if( !empty($header) ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, 3); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return $result; // 返回数据
    }

    private function getSign(array $params, $app_secret){
        $arr = [];
        ksort($params, SORT_STRING);
        foreach ($params as $k => $v) {
            if ($k === 'sign' || $v === '') {
                continue;
            }
            $arr[] = $k . '=' . $v;
        }
        $raw_str = implode('&', $arr) . $app_secret;
        return hash('sha256', $raw_str);
    }

    private function getIP(){
        // 判断服务器是否允许$_SERVER
        if(isset($_SERVER)){
            if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            }else{
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }

        return $realip;
    }
}