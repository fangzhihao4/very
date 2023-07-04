<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommonModel extends Model
{
    /**
     * 获取列表
     */
    public function getList($table, $pre_record = 10,$where = [["status", "=", 1]], $order = 'create_time', $type = 'desc'){
        if($pre_record == ''){
            $list = DB::table($table)
                ->where($where)
                ->orderBy($order, $type)
                ->get();
        }else{
            parse_str($_SERVER['QUERY_STRING'], $query);
            unset($query['page']);
            $list = DB::table($table)
                ->where($where)
                ->orderBy($order,$type)
                ->paginate($pre_record)
                ->withPath('/order/list'.'?'.http_build_query($query));
        }
        return $list;
    }
    public function getListTwiceOrderBy($table, $pre_record = 10,$where = [["status", "=", 1]],$order_one = 'id', $type_one = 'desc',$order_two = 'id', $type_two = 'desc',$ids = ''){
        if($pre_record == ''){
            $list = DB::table($table)
                ->where($where)
                ->orderBy($order_one, $type_one)
                ->orderBy($order_two, $type_two)
                ->get();
        }else{
            parse_str($_SERVER['QUERY_STRING'], $query);
            unset($query['page']);
            if(!empty($ids)){
                $list = DB::table($table)
                    ->where($where)
                    ->whereIn('id',$ids)
                    ->orderBy($order_one, $type_one)
                    ->orderBy($order_two, $type_two)
                    ->paginate($pre_record)
                    ->withPath('/order/list'.'?'.http_build_query($query));
            }else{
                $list = DB::table($table)
                    ->where($where)
                    ->orderBy($order_one, $type_one)
                    ->orderBy($order_two, $type_two)
                    ->paginate($pre_record)
                    ->withPath('/order/list'.'?'.http_build_query($query));
            }

        }
        return $list;
    }

    public function getListTotalNum($table, $where = []){
        $res = DB::table($table)
            ->where($where)
            ->count();
        return $res;
    }

    public function addRow($table, $params){
        $res = DB::table($table)->insert($params);
        return $res;
    }

    public function getRow($table, $where = [["status", "=", 1]], $order = 'id', $type = 'desc'){
        $res = DB::table($table)
            ->where($where)
            ->orderBy($order, $type)
            ->get();
        return $res;
    }

    public function updateRow($table, $id, $params){
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = DB::table($table)->where('id',$id)->update($params);
        return $res;
    }

    public function delList($table, $where){
        $res = DB::table($table)->where($where)->delete();
        return $res;
    }
    public function addRowReturnId($table, $params){
        $res = DB::table($table)->insert($params);
        if($res === false){
            $id = 0;
        }else{
            $id =  DB::getPdo()->lastInsertId();
        }
        return $id;
    }
    public function addBaseLog($table,$params){
        $res = DB::table($table)->insert($params);
        return $res;
    }
}