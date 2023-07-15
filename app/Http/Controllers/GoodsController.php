<?php


namespace App\Http\Controllers;


use App\Models\CommonModel;
use App\Models\GoodsInfoModel;
use App\Models\UserListModel;

class GoodsController extends Controller
{
    protected $commonModel;
    private $user_no = 10001;

    public function __construct(CommonModel $commonModel)
    {
        $this->commonModel = $commonModel;
    }

    public function indexAction()
    {
        $store_type = (int)request()->input('store_type', '');
        $where = [];
        if (!empty($store_type)){
            $where["type"] = $store_type;
        }
        $type = [
            1 => "小红帽",
            2 => "微店",
            3 => "团长"
        ];
        $list = $this->getListPage($where, [["id", "desc"]]);
        return view('goods/index', ["list" => $list, "type" => $type]);
    }


    public function detailAction(){
        $id = (int)request()->input('id', '');
        $info = [];
        if(!empty($id)){
            $info    =   $this->commonModel->getRow("goods_info",['id' => $id]);
            if (empty($info)) {
                return response()->failed('无此商品', '/goods/index');
            }
            return view('goods/goods_detail',['data'=>$info[0]]);
        }

        //商品页面
        return view('goods/goods_detail',['data'=>[]]);

    }


    public function buttonDetailAction(){
        $id = (int)request()->input('id', '');
        $goods_name = request()->input('goods_name', '');
        $goods_sku = request()->input('goods_sku', '');
        $type = request()->input('type', '');
        $price = request()->input('price', '');

        if (empty($goods_name) || empty($goods_sku) || empty($type) || empty($price)){
            return response()->jsonFormat(1002, '保存失败，缺少信息');
        }
        if (strlen($goods_name) > 200){
            return response()->jsonFormat(1002, '保存失败，商品名称太长');
        }
        if (strlen($goods_sku) > 40){
            return response()->jsonFormat(1002, '保存失败，商品编码太长');
        }
        if( ($type < 1) || ($type > 3)){
            return response()->jsonFormat(1002, '保存失败，店铺选择错误');
        }
        if(!is_numeric($price)){
            return response()->jsonFormat(1002, '保存失败，价格类型错误');
        }

        $params = [
            "goods_name" => $goods_name,
            "goods_sku" => $goods_sku,
            "type" => $type,
            "price" => $price
        ];


        if(!empty($id)){ //修改
            $info    =   $this->commonModel->getRow("goods_info",['id' => $id]);
            if(empty($info)){
                return response()->jsonFormat(1002, '修改失败，无此商品');
            }

            $this->commonModel->updateRow("goods_info", $id, $params);
            return response()->jsonFormat(200, '保存成功');
        }

        //新增
        $params["create_time"] = date('Y-m-d H:i:s');
        $params["update_time"] = date('Y-m-d H:i:s');
        $info    =   $this->commonModel->getRow("goods_info",['goods_sku' => $goods_sku, "type" => $type]);
        if (!empty($info) && !empty($info[0])) {
            return response()->jsonFormat(1002, '新增失败，已经有此商品');
        }

        $this->commonModel->addRow("goods_info", $params);
        return response()->jsonFormat(200, '保存成功');
    }


    public function getListPage(array $where = [], array $order = [])
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        unset($query['page']);
        $params_uri = $query ? '?' . http_build_query($query) : '';
        $path = parse_url($_SERVER['REQUEST_URI']);
        $res = GoodsInfoModel::query()
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