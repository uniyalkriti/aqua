<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table='person';
    public $timestamps=false;

    protected $guarded=[];
     public function todayMtp($uid)
    {
    	$date = date('Y-m-d');
        return  UserTodaysAttendanceEnabledLog::where('user_id',$uid)->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '$date'")->first();
       
    }
    public function dsrMonthly($cid,$user_id,$date)
    {
    	 return DB::table('user_sales_order')->select(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("SUM(user_sales_order_details.quantity * user_sales_order_details.rate) as product_quantity_amount"))->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->where('product_id','!=','0')->where('user_sales_order.user_id',$user_id)->where('user_sales_order_details.product_id',$cid)->where('user_sales_order.date',$date)->groupBy('product_id')->first();

    }
}
