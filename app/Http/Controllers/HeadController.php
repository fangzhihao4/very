<?php


namespace App\Http\Controllers;


use App\Models\CommonModel;
use App\Models\OrderFiledNameModel;
use App\Models\OrderListModel;
use App\Models\OrderUploadModel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class HeadController extends Controller
{

    protected $commonModel;
    private $user_no = 10001;

    public function __construct(CommonModel $commonModel)
    {
        $this->commonModel = $commonModel;
    }

    public function indexAction()
    {
        $storeInfo = $this->storeList();
        $list = $this->getListPage([["id", ">", 0]], [["id", "desc"]]);
        return view('head/index', ["list" => $list, "store" => $storeInfo]);
    }

    public function getListPage(array $where = [], array $order = [])
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
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
        $res = $res->paginate(10)->withPath($path["path"] . $params_uri);
        return $res;
    }

    public function batchUploadStoreInfoAction()
    {
        $id = request()->input('upload_id');

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

        $this->readExcel($tmp_url, $file_suffix, $id);

        unlink($tmp_url);
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
    private function readExcel($path, $ext, $upload_id)
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
            exit('Excel表格中没有数据');
        }

        $logic_name = "物流公司";
        $logic_number = "物流单号";
        $order_number = "原始单号";

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
        for ($row = 2; $row <= $highestRow; ++$row) {
            $logic_name_value = $worksheet->getCellByColumnAndRow($logic_name_i, $row)->getValue(); //物流公司
            $logic_number_value = $worksheet->getCellByColumnAndRow($logic_number_i, $row)->getValue(); //物流单号
            $order_value = $worksheet->getCellByColumnAndRow($order_number_i, $row)->getValue(); //原始单号

            if ($order_value) {
                $this->updateOrder($upload_id, $order_value, $logic_name_value, $logic_number_value);
            }
        }

        $this->commonModel->updateRow("order_upload", $upload_id, ["update_time" => date('Y-m-d H:i:s')]);
    }

    public function downloadAction()
    {
        $id = request()->input('upload_id');
        $upload_info = OrderUploadModel::query()
            ->where('id', $id)
            ->limit(1)
            ->first();
        $name = $upload_info["name"];

        $table_filed_arr = OrderFiledNameModel::query()
            ->where('user_no', "10000")
            ->orderBy("sort")
            ->get()
            ->toArray();

        $all_info = $this->getOrderList(["o.upload_id" => $id]);


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

        $styleArray = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $worksheet->getStyle('A1:E1')->applyFromArray($styleArray)->getFont()->setSize(14);

        $store_list = $this->storeList();
        $name_store = !empty($store_list[$upload_info["user_no"]]) ? $store_list[$upload_info["user_no"]] :"未知门店";
        $len = count($all_info);
        $j = 0;
        for ($i = 0; $i < $len; $i++) {
            $j = $i + 2; //从表格第2行开始
            $row_excel = 2;
            $worksheet->setCellValueByColumnAndRow(1, $j, $name_store);
            foreach ($table_filed_arr as $key_filed => $value_filed) {
                $filed_name = $value_filed["table_field_name"];
                $data_arr = (array)$all_info[$i];
                $worksheet->setCellValueByColumnAndRow($row_excel, $j, $data_arr[$filed_name]);
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
            ->where($where)
            ->orderBy('o.sort', 'asc')
            ->get();
    }


    public function updateOrder($upload_id, $order_number, $logic_name, $logic_number)
    {

        $sql = 'update order_list set logistics_company = :logistics_company, logistics_number = :logistics_number,update_time = :update_time where upload_id = :upload_id and original_order_number = :original_order_number';
        $params = [
            'logistics_company' => $logic_name,
            'logistics_number' => $logic_number,
            'update_time' => date('Y-m-d H:i:s'),
            'upload_id' => $upload_id,
            'original_order_number' => $order_number,
        ];
        return DB::update($sql, $params);
    }

    public function storeList(){
        return $storeInfo = [
            10001 => "杭州",
            10002 => "微店",
            10003 => "团长"
        ];
    }
}