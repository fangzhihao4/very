<?php


namespace App\Http\Controllers;


use App\Http\service\CommonService;
use App\Models\CommonModel;
use App\Models\OrderFiledNameModel;
use App\Models\OrderListModel;
use App\Models\OrderUploadModel;
use Illuminate\Support\Facades\DB;
use PHPExcel_Cell_DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Console\Input\Input;

class HeadController extends Controller
{

    protected $commonModel;
    protected $commonService;
    private $user_no = 10000;

    public function __construct(CommonModel $commonModel, CommonService $commonService)
    {
        $this->commonModel = $commonModel;
        $this->commonService = $commonService;
    }

    public function indexAction()
    {
        $storeInfo = $this->storeList();
        $statusList = $this->commonService->uploadStatus();
        $list = $this->getListPage([["id", ">", 0]], [["id", "desc"]]);
        return view('head/index', ["list" => $list, "store" => $storeInfo, "status" => $statusList]);
    }

    public function delUploadAction()
    {
        $id = (int)request()->input('id', '');
        if (!empty($id)) {
            $this->commonModel->delList("order_upload", ['id' => $id]);
            $this->commonModel->delList("order_list", ['upload_id' => $id]);
            $this->commonModel->delList("order_hang", ['upload_id' => $id]);
            $this->commonModel->delList("order_tuan", ['upload_id' => $id]);
            $this->commonModel->delList("order_wei", ['upload_id' => $id]);
            $this->commonModel->delList("order_wei_info", ['upload_id' => $id]);
        }
        return response()->jsonFormat(200, '删除成功');
    }

    public function getListPage(array $where = [], array $order = [])
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        $pagesize = 10;
        if (!empty($query["pagesize"]) && in_array((int)$query["pagesize"], [10, 20, 50])) {
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

        $this->readCsv($tmp_url);


        try {
//            $this->readExcel($tmp_url, $file_suffix);
            $this->readCsv($tmp_url);
        } catch (\Exception $exception) {
            $error = [
                "name" => "上传ERP店铺excel错误",
                "message" => substr($exception, 0, 2000),
                "create_time" => date('Y-m-d H:i:s'),
                "update_time" => date('Y-m-d H:i:s')
            ];
            $this->commonModel->addRow("error_log", $error);
            $this->commonService->delDirAndFile($tmp_url, true);
            return response()->jsonFormat(10003, "上传excel或解析excel异常，请确定excel后重试");
        }

        unlink($tmp_url);
        $this->commonService->delDirAndFile($tmp_url, true);
//
//        $file_data = isset($return_file['data']) ? $return_file['data'] : '';
//        if (empty($return_file) || empty($file_data)) {
//            return response()->jsonFormat(1003, '上传文件异常，请稍后重试');
//        }

        return response()->jsonFormat(200, "上传成功");

    }

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    private function readExcel($path, $ext)
    {
//        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('csv');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($path); //载入excel表格

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

        $lines = $highestRow - 2;
        if ($lines <= 0) {
            exit('Excel表格中没有数据');
        }

        $logic_name = "物流公司";
        $logic_number = "物流单号";
        $order_number = "原始单号";

        $goods_list = [];

        $logic_name_i = 0;
        $logic_number_i = 0;
        $order_number_i = 0;

        for ($i = 1; $i <= $highestColumnIndex; $i++) {
            $name = $worksheet->getCellByColumnAndRow($i, 1)->getValue();
            if ($logic_name == $name) {
                $logic_name_i = $i;
            }
            if ($logic_number == $name) {
                $logic_number_i = $i;
            }
            if ($order_number == $name) {
                $order_number_i = $i;
            }
        }
        $all_info = [];
        for ($row = 2; $row <= $highestRow; ++$row) {
            $logic_name_value = $worksheet->getCellByColumnAndRow($logic_name_i, $row)->getValue(); //物流公司
            $logic_number_value = $worksheet->getCellByColumnAndRow($logic_number_i, $row)->getValue(); //物流单号
            $order_value = $worksheet->getCellByColumnAndRow($order_number_i, $row)->getValue(); //原始单号

            $order_head = substr($order_value, 0, 2);
            if ($order_head == "LP") {
                $order_value = substr($order_value, 2, strlen($order_value));
            }
            if (empty($all_info[$order_value])) {
                $all_info[$order_value] = [
                    "logic_name" => $logic_name_value,
                    "logic_number" => $logic_number_value
                ];
            } else {
                $all_info[$order_value]["logic_name"] = $all_info[$order_value]["logic_name"] . ";" . $logic_name_value;
                $all_info[$order_value]["logic_number"] = $all_info[$order_value]["logic_number"] . ";" . $logic_number_value;
            }
//            if (!empty($order_value)) {
//                $this->updateOrder($order_value, $logic_name_value, $logic_number_value);
//            }
            array_push($goods_list, $order_value);
        }

        if (!empty($all_info)){
            foreach ($all_info as $k_all => $v_all){
                $this->updateOrder($k_all, $v_all["logic_name"], $v_all["logic_number"]);
            }
        }


        if (!empty($goods_list)) {
            $upload_ids = OrderListModel::query()
                ->select("upload_id")
                ->whereIn("original_order_number", $goods_list)
                ->groupBy("upload_id")
                ->get()
                ->toArray();
            if (!empty($upload_ids)) {
                $params_upload = [
                    "status" => 2,
                    "update_time" => date('Y-m-d H:i:s')
                ];
                OrderUploadModel::query()
                    ->whereIn("id", $upload_ids)
                    ->update($params_upload);
            }
        }


    }

    private function readCsv($filename)
    {
        if (empty ($filename)) {
            return ["code" => 10001, "没有数据"];
        }
        $handle = fopen($filename, 'r');
        $result = $this->input_csv($handle); //解析csv
        $len_result = count($result);
        if ($len_result == 0) {
            return ["code" => 10001, "没有数据"];
        }
        $title_count = count($result[0]);
        $data_values = "";
        $logic_name_i = 0;
        $logic_number_i = 1;
        $order_number_i = 2;
        $logic_name = "物流公司";
        $logic_number = "物流单号";
        $order_number = "原始单号";
        for ($i_t = 0; $i_t < $title_count; $i_t++) {
            if ("物流公司" == $result[0][$i_t]) {
                $logic_name_i = $i_t;
            }
            if ("物流单号" == $result[0][$i_t]) {
                $logic_number_i = $i_t;
            }
            if ("原始单号" == $result[0][$i_t]) {
                $order_number_i = $i_t;
            }
        }

        $goods_list = [];
        $all_info = [];
        for ($i = 1; $i < $len_result; $i++) { //循环获取各字段值
            $logic_name_value = $result[$i][$logic_name_i]; //物流公司
            $logic_number_value = $result[$i][$logic_number_i]; //物流单号
            $logic_number_value = str_replace(array("\r\n", "\r", "\n", "=", "\"", "'"), "", $logic_number_value);
            $order_value = $result[$i][$order_number_i]; //原始单号
            if (empty($order_value)) {
                continue;
            }

            $order_head = substr($order_value, 0, 2);
            if ($order_head == "LP") {
                $order_value = substr($order_value, 2, strlen($order_value));
            }

            if (empty($all_info[$order_value])) {
                $all_info[$order_value] = [
                    "logic_name" => $logic_name_value,
                    "logic_number" => $logic_number_value
                ];
            } else {
                $all_info[$order_value]["logic_name"] = $all_info[$order_value]["logic_name"] . ";" . $logic_name_value;
                $all_info[$order_value]["logic_number"] = $all_info[$order_value]["logic_number"] . ";" . $logic_number_value;
            }

//            if (!empty($order_value)) {
//                $this->updateOrder($order_value, $logic_name_value, $logic_number_value);
//            }
            array_push($goods_list, $order_value);

        }

        if (!empty($all_info)){
            foreach ($all_info as $k_all => $v_all){
                $this->updateOrder($k_all, (string)$v_all["logic_name"], (string)$v_all["logic_number"]);
            }
        }

        if (!empty($goods_list)) {
            $upload_ids = OrderListModel::query()
                ->select("upload_id")
                ->whereIn("original_order_number", $goods_list)
                ->groupBy("upload_id")
                ->get()
                ->toArray();
            if (!empty($upload_ids)) {
                $params_upload = [
                    "status" => 3,
                    "update_time" => date('Y-m-d H:i:s')
                ];
                OrderUploadModel::query()
                    ->whereIn("id", $upload_ids)
                    ->update($params_upload);
            }
        }

        fclose($handle); //关闭指针
        return [];
    }

    function input_csv($handle)
    {
        $out = array();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        $res = eval('return '.iconv('gbk','utf-8',var_export($out,true)).';');
        if (empty($res)){
            return $out;
        }else{
            return $res;
        }
    }

    public function downloadAction()
    {
        $table_filed_arr = OrderFiledNameModel::query()
            ->where('user_no', "10000")
            ->orderBy("sort")
            ->get()
            ->toArray();

        $all_info = $this->getOrderList(["o.status" => 1]);

        $name = "ERP订单导出" . date('Y-m-d') . ".xlsx";

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        //设置工作表标题名称
        $worksheet->setTitle($name);

        //表头
        $row_excel = 2;
        $worksheet->setCellValueByColumnAndRow(1, 1, "店铺名称");
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

//        $worksheet->getStyle('A1:E1')->applyFromArray($styleArray)->getFont()->setSize(14);

//        $store_list = $this->storeList();
        $len = count($all_info);
        $j = 2;//从表格第2行开始

        $upload_ids = [];
        for ($i = 0; $i < $len; $i++) {

            $data_arr = (array)$all_info[$i];
            array_push($upload_ids, $data_arr["upload_id"]);

            $goods_sku = $data_arr["merchant_code"];
            $goods_all_price = $data_arr["total_product_price"];
//            $goods_nums = $data_arr["number_of_product_pieces"];
            if (strrpos($goods_sku,";") && ($data_arr["user_no"] == "10002")){
                $wei_info = $this->commonModel->getRow("order_wei",["upload_id" => $data_arr["upload_id"], "original_order_number" => $data_arr["original_order_number"]]);
                $goods_num_arr =[];
                if (!empty($wei_info) && !empty($wei_info[0])){
                    $goods_num_arr = explode(";", $wei_info[0]->number_of_product_pieces);
                }
                $goods_sku_arr = explode(";", $goods_sku);
                foreach ($goods_sku_arr as $k_g =>  $goods_value){
                    $goods_info = $this->commonModel->getRow("goods_info", ["goods_sku" => $goods_value]);
                    $goods_price = 0;
                    if (!empty($goods_info) && !empty($goods_info[0])){
                        $goods_price = (float)$goods_info[0]->price;
                    }
                    $goods_num = !empty($goods_num_arr[$k_g]) ? $goods_num_arr[$k_g] : 0;
                    $goods_all_price = (float)$goods_price * (float)$goods_num;
                    $worksheet = $this->excelInfo($worksheet, $j, $table_filed_arr, $data_arr, $goods_value, $goods_all_price, (float)$goods_price, $goods_num);
                    $j++; //从表格第2行开始
                }
            }else{
                $worksheet = $this->excelInfo($worksheet, $j, $table_filed_arr, $data_arr, $goods_sku, $goods_all_price,(float)$data_arr["product_price"], $data_arr["product_quantity"]);
                $j++; //从表格第2行开始
            }

//            $name_store = "牛奶分销";
////            $name_store = $data_arr["store_name"];
//            $worksheet->setCellValueByColumnAndRow(1, $j, $name_store);
//            foreach ($table_filed_arr as $key_filed => $value_filed) {
//                $filed_name = $value_filed["table_field_name"];
//
//                $value_value = !empty($data_arr[$filed_name]) ? $data_arr[$filed_name] : "";
//
//                if ($filed_name == "original_order_number") {
//                    $value_value = !empty($data_arr[$filed_name]) ? "LP" . $data_arr[$filed_name] : "";
//                }
//                if ($filed_name == "distributor") {
//                    $value_value = $data_arr["store_name"];
//                }
//                $worksheet->setCellValueByColumnAndRow($row_excel, $j, $value_value);
//                $row_excel++;
//            }
        }

        if (!empty($upload_ids)) {
            $params_upload = [
                "status" => 2,
                "update_time" => date('Y-m-d H:i:s')
            ];
            OrderUploadModel::query()
                ->whereIn("id", $upload_ids)
                ->update($params_upload);
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


    public function excelInfo($worksheet, $j, $table_filed_arr, $data_arr, $goods_sku, $goods_all_price, $goods_price, $goods_num){
        $name_store = "牛奶分销";
//            $name_store = $data_arr["store_name"];
        $worksheet->setCellValueByColumnAndRow(1, $j, $name_store);
        $row_excel = 2;
        foreach ($table_filed_arr as $key_filed => $value_filed) {
            $filed_name = $value_filed["table_field_name"];

            $value_value = !empty($data_arr[$filed_name]) ? $data_arr[$filed_name] : "";

            if ($filed_name == "original_order_number") {
                $value_value = !empty($data_arr[$filed_name]) ? "LP" . $data_arr[$filed_name] : "";
            }
//            if ($filed_name == "distributor") {
//                $value_value = $data_arr["store_name"];
//            }
            if($filed_name == "merchant_code"){
                $value_value = $goods_sku;
            }
            if ($filed_name == "total_product_price"){
                $value_value = $goods_all_price;
            }
            if($filed_name == "product_price"){
                $value_value = $goods_price;
            }
            if ($filed_name == "product_quantity"){
                $value_value = $goods_num;
            }
            $worksheet->setCellValueExplicit([$row_excel , $j], (string)$value_value,PHPExcel_Cell_DataType::TYPE_STRING);
            $row_excel++;
        }
        return $worksheet;
    }


    public function getOrderList(array $where)
    {
        return DB::table('order_list as o')
            ->where($where)
            ->orderBy('o.sort', 'asc')
            ->limit(5000)
            ->get();
    }


    public function updateOrder($order_number, $logic_name, $logic_number)
    {

        $sql = 'update order_list set logistics_company = :logistics_company, logistics_number = :logistics_number, update_time = :update_time, status = :status where original_order_number = :original_order_number';
        $params = [
            'logistics_company' => (string)$logic_name,
            'logistics_number' => (string)$logic_number,
            'update_time' => date('Y-m-d H:i:s'),
            'status' => 2,
            'original_order_number' => (string)$order_number,
        ];
        return DB::update($sql, $params);
    }

    public function storeList()
    {
        return $storeInfo = [
            10001 => "小红帽",
            10002 => "微店",
            10003 => "胖奶油团长",
            10004 => "锟仔妈妈团长",
            10005 => "猫家严选"
        ];
    }
}