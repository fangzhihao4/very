<?php


namespace App\Http\Controllers;


use App\Http\service\CommonService;
use App\Models\CommonModel;
use App\Models\GoodsInfoModel;
use App\Models\OrderFiledNameModel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class GoodsController extends Controller
{
    protected $commonModel;
    protected $commonService;

    private $user_no = 10001;

    public function __construct(CommonModel $commonModel, CommonService $commonService)
    {
        $this->commonModel = $commonModel;
        $this->commonService = $commonService;
    }


    public function storeAction(){
        return [
            1 => "小红帽",
            2 => "微店",
            3 => "团长"
        ];
    }

    public function indexAction()
    {
        $store_type = (int)request()->input('store_type', '');
        $where = [];
        if (!empty($store_type)){
            $where["type"] = $store_type;
        }
        $type = $this->storeAction();
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

    public function delAction(){
        $id = (int)request()->input('id', '');
        if(!empty($id)){
            $this->commonModel->delList("goods_info",['id' => $id]);
        }
        return response()->jsonFormat(200, '删除成功');
    }

    public function delAllAction(){
        $this->commonModel->delList("goods_info",[]);
        return response()->jsonFormat(200, '删除成功');
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

    public function downloadAction()
    {
        $name = "商品信息". date('Y-m-d') . ".xlsx";
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        //设置工作表标题名称
        $worksheet->setTitle($name);

        $goods_list = DB::table("goods_info")
            ->get()
            ->toArray();

        $worksheet->setCellValueByColumnAndRow(1, 1, "店铺");
        $worksheet->setCellValueByColumnAndRow(2, 1, "商品名称");
        $worksheet->setCellValueByColumnAndRow(3, 1, "商品编码");
        $worksheet->setCellValueByColumnAndRow(4, 1, "商品价格");

        $store_list = $this->storeAction();


        $len = count($goods_list);
        $j = 0;
        for ($i = 0; $i < $len; $i++) {
            $j = $i + 2; //从表格第2行开始
            $info = $goods_list[$i];
            $store_name = !empty($store_list[$info->type]) ? $store_list[$info->type] : "其他";
            $worksheet->setCellValueByColumnAndRow(1, $j, $store_name);
            $worksheet->setCellValueByColumnAndRow(2, $j, $info->goods_name);
            $worksheet->setCellValueByColumnAndRow(3, $j, $info->goods_sku);
            $worksheet->setCellValueByColumnAndRow(4, $j, $info->price);
        }

        $styleArrayBody = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '666666'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $total_rows = $len + 1;
//添加所有边框/居中
        $worksheet->getStyle('A1:E' . $total_rows)->applyFromArray($styleArrayBody);

        $filename = $name;
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }


    public function batchUploadStoreInfoAction()
    {
        $file = $_FILES['storeFile'];
        $file_name = $file['name'];
        $file_suffix = substr($file_name, -4);
        $tmp_name = $file['tmp_name'];
        $file_size = $file['size'];

        if (empty($tmp_name)) {
            return response()->jsonFormat(1001, '没有上传的文件');
        }
        if ($file_size >= 1024000 * 5) {
            return response()->jsonFormat(1002, '文件最大可上传5M,请分批上传');
        }

        $save_path = public_path('file') . '/';
        $tmp_url = $save_path . md5($file_name . microtime(true) . mt_rand(0, 999)) . '.' . $file_suffix;
        if (!file_exists($save_path)) {
            mkdir("$save_path", 0777, true);
        }

        if (!move_uploaded_file($tmp_name, $tmp_url)) {
            return response()->jsonFormat(1003, '上传文件异常，请稍后重试');
        }

        try {
            $res = $this->readExcel($tmp_url, $file_suffix);
            if (!empty($res)){
                return response()->jsonFormat($res["code"], $res["message"]);
            }
        }catch (\Exception $exception){
            $error = [
                "name" => "上传商品信息错误",
                "message" => substr($exception,0, 2000),
                "create_time" => date('Y-m-d H:i:s'),
                "update_time" => date('Y-m-d H:i:s')
            ];
            $this->commonModel->addRow("error_log",$error);
            $this->commonService->delDirAndFile($tmp_url,true);
            return response()->jsonFormat(10003, "上传excel或解析excel异常，请确定excel后重试");
        }
        unlink($tmp_url);
        $this->commonService->delDirAndFile($tmp_url,true);
        return response()->jsonFormat(200, "上传成功");

    }

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    private function readExcel($path, $ext )
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($path); //载入excel表格

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
        $lines = $highestRow - 2;
        if ($lines <= 0) {
            return ["code" => 10001, "message"=>"Excel表格中没有数据"];
        }

        $lines = $highestRow - 2;
        if ($lines <= 0) {
            return ["code" => 10001, "message"=>"Excel表格中没有数据"];
        }

        $goods_list = GoodsInfoModel::query()
            ->get()
            ->toArray();

        $all_goods = [];
        for ($i =0; $i < count($goods_list); $i++){
            $goods_info = $goods_list[$i];
            if (!empty($all_goods[$goods_info["type"]])){
                $type_list = $all_goods[$goods_info["type"]];
                $type_list[$goods_info["goods_sku"]] = $goods_info;
                $all_goods[$goods_info["type"]] = $type_list;
            }else{
                $type_list = [];
                $type_list[$goods_info["goods_sku"]] = $goods_info;
                $all_goods[$goods_info["type"]] = $type_list;
            }
        }
        $name_arr = [];
        for ($i = 1; $i <= $highestColumnIndex; $i++) {
            $name = $worksheet->getCellByColumnAndRow($i, 1)->getValue();
            $name_arr[$i] = $name;
        }
        $add_data_arr = [];
        $store_type = "";
        $goods_sku = "";
        $goods_name = "";
        $goods_price = "";
        $store_data_arr = array_flip($this->storeAction());
//        var_export($name_arr);exit;
        for ($row = 2; $row <= $highestRow; ++$row) {
            foreach ($name_arr as $key_i => $value_name) {
                $value = $worksheet->getCellByColumnAndRow($key_i, $row)->getValue();
                if ('店铺' == $value_name){
                    $store_type = !empty($store_data_arr[$value]) ? $store_data_arr[$value] : 0;
                }
                if ('商品名称' == $value_name){
                    $goods_name = $value;
                }

                if('商品编码' == $value_name){
                    $goods_sku = $value;
                }

                if('商品价格' == $value_name){
                    $goods_price = is_numeric($value) ? $value : 0;
                }

            }
            if (empty($goods_sku)){
                continue;
            }

            $add_data = [
                "type" => $store_type,
                "goods_name" => $goods_name,
                "goods_sku" => (string)$goods_sku,
                "price" => $goods_price,
                "create_time" => date('Y-m-d H:i:s'),
                "update_time" => date('Y-m-d H:i:s')
            ];
            if (empty($all_goods[$store_type]) || empty($all_goods[$store_type][$goods_sku])){
                array_push($add_data_arr,$add_data);
            }else{
                $this->commonModel->updateRowWhere("goods_info",["type" => $store_type, "goods_sku" => (string)$goods_sku], $add_data);
            }
        }

        $this->commonModel->addRow("goods_info", $add_data_arr);
        return [];
    }
}