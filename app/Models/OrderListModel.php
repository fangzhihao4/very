<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderListModel extends Model
{
    public $timestamps = false;
    protected $table = 'order_list';

    public function updateActivityWeight($id,$weight) {
        $sql = 'select upload_id from '.$this->table.' where original_order_number in :original_order_number';
        $params = [
            'weight' => $weight,
            'update_time' => date('Y-m-d H:i:s'),
            'id' => $id
        ];
        return DB::update($sql,$params);
    }
}