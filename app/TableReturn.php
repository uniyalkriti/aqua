<?php

namespace App;
use DB;
use Lang;
use Illuminate\Database\Eloquent\Model;

class TableReturn extends Model
{
    protected $guarded = array();
    protected $table='person';

    public static function table_return($from_date,$to_date)
    {

        $current_date = date('Y-m-d');
        $current_month = date('Y-m');
        $last_3_month = date('Y-m',strtotime('-3 month')); // 2021-04
        $last_6_month = date('Y-m',strtotime('-6 month')); // 2021-01
        // dd($from_date);

        $custom_from_date = date('Y-m',strtotime($from_date));
        // dd($current_month);
        if($custom_from_date >= $current_month)
        {
            $table = 'user_sales_order_1_month';
        }
        elseif($custom_from_date >= $last_3_month)
        {
            // $table = 'user_sales_order';
            $table = 'user_sales_order_3_month';
        }
        elseif($custom_from_date >= $last_6_month)
        {
            $table = 'user_sales_order';
            // $table = 'user_sales_order_6_month';
        }
        else
        {
            $table = 'user_sales_order';
        }
        // dd($table);
        return $table;
    }
}
