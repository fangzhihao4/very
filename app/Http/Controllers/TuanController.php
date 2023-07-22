<?php


namespace App\Http\Controllers;


use App\Http\service\CommonService;
use App\Models\CommonModel;
use App\Models\GoodsInfoModel;
use App\Models\OrderFiledNameModel;
use App\Models\OrderUploadModel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TuanController extends Controller
{
    protected $commonModel;
    protected $commonService;
    private $user_no = 10003;
    private $user_name = "胖奶油团长";

    public function __construct(CommonModel $commonModel, CommonService  $commonService)
    {
        $this->commonModel = $commonModel;
        $this->commonService = $commonService;
    }

    public function indexAction()
    {
        $statusList = $this->commonService->uploadStatus();
        $list = $this->getListPage(["user_no" => $this->user_no], [["id", "desc"]]);
        return view('tuan/index', ["list" => $list, "status" => $statusList]);
    }

    public function getListPage(array $where = [], array $order = [])
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        $pagesize = 10;
        if (!empty($query["pagesize"]) && in_array((int)$query["pagesize"],[10,20,50])){
            $pagesize = $query["pagesize"];
        }
        unset($query['page']);
        $params_uri = $query ? '?' . http_build_query($query) : '';
        $path = parse_url($_SERVER['REQUEST_URI']);
        $res = OrderUploadModel::query()
            ->where($where);

        if (!empty($order)) {
            foreach ($order as $k => $v) {
                $res->orderBy($v[0], $v[1]);
            }
        }
        $res = $res->paginate($pagesize)->withPath($path["path"] . $params_uri);
        return $res;
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
        $data_upload = [
            "user_no" => $this->user_no,
            "type" => 1,
            "name" => $file_name,
            "status" => 1,
            "create_time" => date('Y-m-d H:i:s'),
            "update_time" => date('Y-m-d H:i:s')
        ];
        try{
            $id = $this->commonModel->addRowReturnId("order_upload", $data_upload);
            $res = $this->readExcel($tmp_url, $file_suffix, $this->user_no, $id);
            if (!empty($res)){
                $this->commonModel->delList("order_upload",["id" => $id]);
                return response()->jsonFormat($res["code"], $res["message"]);
            }
        }catch (\Exception $exception){
            $error = [
                "name" => "上传胖奶油团长店铺excel错误",
                "message" => substr($exception,0, 2000),
                "create_time" => date('Y-m-d H:i:s'),
                "update_time" => date('Y-m-d H:i:s')
            ];
            $this->commonModel->addRow("error_log",$error);
            $this->commonService->delDirAndFile($tmp_url,true);
            $this->commonModel->delList("order_upload",["id" => $id]);
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
    private function readExcel($path, $ext, $user_no, $upload_id)
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

        $campaignsBannerInfo = OrderFiledNameModel::query()
            ->where('user_no', $this->user_no)
            ->orderBy("sort")
            ->get()
            ->toArray();
        $goods_list = GoodsInfoModel::query()
            ->where('type', 3)
            ->get()
            ->toArray();

        $price_list = array_column($goods_list, NULL, 'goods_sku');
        $data_list = array_column($campaignsBannerInfo, NULL, 'excel_field_name');

        $name_arr = [];
        $order_field_name_id = 1;
        for ($i = 1; $i <= $highestColumnIndex; $i++) {
            $name = $worksheet->getCellByColumnAndRow($i, 1)->getValue();
            if (!empty($name) && !empty($data_list[$name])) {
                if ($data_list[$name]["table_field_name"] == "original_order_number"){
                    $order_field_name_id = $i;
                }
                $name_arr[$i] = $name;
            }
        }
        $common_data_arr = [];
        $store_data_arr = [];
        $store_info_arr = [];
        for ($row = 2; $row <= $highestRow; ++$row) {
            $common_data = [];
            $store_data = [];
            $common_data["upload_id"] = $upload_id;
            $common_data["user_no"] = $user_no;
            $common_data["store_name"] = $this->user_name;
            $common_data["create_time"] = date('Y-m-d H:i:s');
            $common_data["update_time"] = date('Y-m-d H:i:s');
            $common_data["status"] = 1;

            $store_data["upload_id"] = $upload_id;
            $store_data["user_no"] = $user_no;

            $store_info["upload_id"] = $upload_id;
            $store_info["user_no"] = $user_no;

            $goods_total_price = 0;
            $goods_price = 0;
            $goods_num = 0;
            $value = $worksheet->getCellByColumnAndRow($order_field_name_id, $row)->getValue(); //订单号没有不算行
            if (empty($value)){
                continue;
            }

            foreach ($name_arr as $key_i => $value_name) {
                $table_name_data = $data_list[$value_name];
                if (!$table_name_data) {
                    continue;
                }
                $value = $worksheet->getCellByColumnAndRow($key_i, $row)->getValue(); //姓名
                if ('商品编码' == $value_name){
                    if (empty($price_list[$value])){
                        return ["code" => 10001, "message"=>"商品管理无此商品，请确认后重新上传,商品编码 " . $value];
                    }
                    $goods_price = !empty($price_list[$value]) ? $price_list[$value]["price"] : 0;
                }
                if ('数量' == $value_name){
                    $goods_num = $value;
                }

                $common_data["sort"] = $row;
                if ($table_name_data["type"] == 1) {
                    $common_data[$table_name_data["table_field_name"]] = $value;
                } elseif ($table_name_data["type"] == 2) {
                    $store_data[$table_name_data["table_field_name"]] = $value;
                }else{
                    $store_info[$table_name_data["table_field_name"]] = $value;
                }


            }

            if (!empty($goods_num) && !empty($goods_price)){
                $goods_total_price = $goods_num * $goods_price;
            }
            $common_data["total_product_price"] = "";
            if (!empty($goods_total_price) && ($goods_total_price != 0) ){
                $common_data["total_product_price"] = $goods_total_price;
            }

            $common_data["payment_time"] = $common_data["order_time"];
            $common_data["total_receivable"] = $common_data["total_product_price"];
            $common_data["product_price"] = $goods_price;
            $common_data["warehouse_name"] = "无锡电商牛奶仓";
            $common_data["distributor"] = "胖奶油";

            $store_data["original_order_number"] = $common_data["original_order_number"];
            $store_info["original_order_number"] = $common_data["original_order_number"];

            array_push($common_data_arr, $common_data);
            array_push($store_data_arr, $store_data);
            array_push($store_info_arr, $store_info);
        }
//        DB::beginTransaction();
        $this->commonModel->addRow("order_list", $common_data_arr);
        $this->commonModel->addRow("order_tuan", $store_data_arr);
//        DB::rollBack();
        return [];

    }


    public function downloadAction()
    {
        $id    = request()->input('upload_id');
        $upload_info = OrderUploadModel::query()
            ->where('id', $id)
            ->limit(1)
            ->first();

        if (empty($upload_info)){
            return response()->jsonFormat(1001, '错误的下载信息');
        }
        if ($upload_info["status"] == 3){
            $params_upload = [
                "status" => 4,
                "update_time" => date('Y-m-d H:i:s')
            ];
            OrderUploadModel::query()
                ->where("id", $upload_info["id"])
                ->update($params_upload);
        }


        $name = $upload_info["name"];
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
//设置工作表标题名称
        $worksheet->setTitle($name);

        $table_filed_arr = OrderFiledNameModel::query()
            ->where('user_no', $this->user_no)
            ->orderBy("sort")
            ->get()
            ->toArray();

        $all_info = $this->getOrderList(["o.upload_id" => $id]);

        //表头
        $row_excel = 1;
        foreach ($table_filed_arr as $key_filed => $value_filed) {
            $worksheet->setCellValueByColumnAndRow($row_excel, 1, $value_filed["excel_field_name"]);
            $row_excel++;
        }


//        $styleArray = [
//            'font' => [
//                'bold' => true
//            ],
//            'alignment' => [
//                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
//            ],
//        ];
//
//        $worksheet->getStyle('A1:E1')->applyFromArray($styleArray)->getFont()->setSize(14);

        $len = count($all_info);
        $j = 0;
        for ($i = 0; $i < $len; $i++) {
            $j = $i + 2; //从表格第2行开始
            $row_excel = 1;
            foreach ($table_filed_arr as $key_filed => $value_filed) {
                $filed_name = $value_filed["table_field_name"];
                $data_arr = (array)$all_info[$i];
                $worksheet->setCellValueByColumnAndRow($row_excel, $j, (String)$data_arr[$filed_name]);
                $row_excel++;
            }
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


    public function getOrderList(array $where)
    {
        return DB::table('order_list as o')
            ->leftJoin('order_tuan as t', 'o.original_order_number', '=', 't.original_order_number')
            ->select('o.*', 't.*')
            ->where($where)
            ->orderBy('o.sort', 'asc')
            ->get();
    }
}