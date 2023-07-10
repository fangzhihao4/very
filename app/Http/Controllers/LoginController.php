<?php


namespace App\Http\Controllers;


use App\Models\CommonModel;

class LoginController extends Controller
{
    protected $commonModel;
    public function __construct(CommonModel $commonModel)
    {
        $this->commonModel = $commonModel;
    }
    public function loginAction(){
        return view('login.login');
    }

    public function buttonLoginAction(){
        $username = request()->input('username', '');
        $password = request()->input('password', '');

        if (empty($username)){
            return response()->jsonFormat(1002, '登录失败，没有账号');
        }
        if (empty($password)){
            return response()->jsonFormat(1002, '登录失败，没有密码');
        }

        $info    =   $this->commonModel->getRow("user_list",['username' => $username]);
        if (empty($info) || empty($info[0])) {
            return response()->jsonFormat(1002, '登录失败，没有该账号');
        }

        if ($info[0]->status != 1){
            return response()->jsonFormat(1002, '登录失败，该账号被禁用');
        }

        if ($password != $info[0]->password){
            return response()->jsonFormat(1002, '登录失败，密码错误');
        }
        $user_info = [
            "id" => $info[0]->id,
            "type" => $info[0]->id,
            "username" => $info[0]->username
        ];
        session(["user_info"=>$user_info]);
        return response()->jsonFormat(200, '登录成功');
    }

    public function logoutAction(){
        session(["user_info"=>[]]);
        return view('login.login');
    }

}