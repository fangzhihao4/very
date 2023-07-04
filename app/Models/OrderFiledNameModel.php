<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class OrderFiledNameModel extends Model
{
    public $timestamps = false;
    protected $table = 'order_field_name';
    protected $fillable = ['id','user_no','table_field_name','excel_field_name','field_length'];

    public $head_filed = ["物流公司","快递单号"];
}