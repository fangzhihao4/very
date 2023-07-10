<?php


namespace App\Http\Controllers;


use App\Models\CommonModel;
use App\Models\UserListModel;

class UserController extends Controller
{
    protected $commonModel;
    private $user_no = 10001;

    public function __construct(CommonModel $commonModel)
    {
        $this->commonModel = $commonModel;
    }

    public function indexAction()
    {
        $list = $this->getListPage([], [["id", "desc"]]);
        return view('user/index', ["list" => $list]);
    }

    public function detailAction(){

        $id = (int)request()->input('id', '');
        $info = [];
        if(!empty($id)){
            //获取上架的定制
            $info    =   $this->commonModel->getRow("user_list",['id' => $id]);
            if (empty($info)) {
                return response()->failed('无此用户', '/user/index');
            }
            return view('user/user_detail',['data'=>$info[0]]);
        }

        //用户页面
        return view('user/user_detail',['data'=>[]]);

    }

    public function buttonDetailAction(){
        $id = (int)request()->input('id', '');
        $username = request()->input('username', '');
        $password = request()->input('password', '');
        $status = request()->input('status', '');

        if (empty($username)){
            return response()->jsonFormat(1002, '保存失败，缺少账号');
        }
        if (strlen($username) > 20){
            return response()->jsonFormat(1002, '保存失败，账号太长');
        }
        if (empty($password)){
            return response()->jsonFormat(1002, '保存失败，缺少密码');
        }
        if (strlen($password) > 20){
            return response()->jsonFormat(1002, '保存失败，密码太长');
        }
        if( ($status < 1) || ($status > 2)){
            return response()->jsonFormat(1002, '修改失败，状态异常');
        }

        $params = [
            "username" => $username,
            "password" => $password,
            "status" => $status
        ];


        if(!empty($id)){ //修改
            $info    =   $this->commonModel->getRow("user_list",['id' => $id]);
            if(empty($info)){
                return response()->jsonFormat(1002, '修改失败，无此用户');
            }

            $this->commonModel->updateRow("user_list", $id, $params);
            return response()->jsonFormat(200, '保存成功');
        }

        //新增
        $params["type"] = 2;
        $params["create_time"] = date('Y-m-d H:i:s');
        $params["update_time"] = date('Y-m-d H:i:s');
        $info    =   $this->commonModel->getRow("user_list",['username' => $username]);
        if (!empty($info) && !empty($info[0])) {
            return response()->jsonFormat(1002, '新增失败，已经有此账号');
        }

        $this->commonModel->addRow("user_list", $params);
        return response()->jsonFormat(200, '保存成功');
    }




    public function getListPage(array $where = [], array $order = [])
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        unset($query['page']);
        $params_uri = $query ? '?' . http_build_query($query) : '';
        $path = parse_url($_SERVER['REQUEST_URI']);
        $res = UserListModel::query()
            ->where($where);

        if (!empty($order)) {
            foreach ($order as $k => $v) {
                $res->orderBy($v[0], $v[1]);
            }
        }
        $res = $res->paginate(10)->withPath($path["path"] . $params_uri);
        return $res;
    }
}