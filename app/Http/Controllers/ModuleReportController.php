<?php

namespace App\Http\Controllers;

use App\_outletType;
use App\Area;
use App\Catalog1;
use App\Claim;
use App\competitorBrand;
use App\CompetitorsPriceLog;
use App\competitorsProduct;
use App\DailyStock;
use App\Dealer;
use App\DealerLocation;
use App\DealerRetailer;
use App\DistributorTarget;
use App\Feedback;
use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5; 
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\SS;
use App\Person;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use App\TableReturn;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use DateTime;
use PDF;
use Illuminate\Support\Facades\Crypt;



class ModuleReportController extends Controller
{

    public function modules_array_data(Request $request)
    {
        $arr = array();
        return view('reports.module-check-status.index', [
                'records' => $arr,
            ]);
    }
    public function modules_array_data_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_details = UserDetail::user_details_fetch($company_id);
        // dd($user_details);
        $app_module = DB::table('app_module')
                    ->join('master_list_module','master_list_module.id','=','app_module.module_id')
                    ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name')
                    ->where('app_module.company_id',$company_id)
                    // ->where('master_list_module.id',2)
                    ->where('app_module.status',1)
                    ->where('master_list_module.status',1)
                    ->orderBy('app_module.module_sequence','ASC')
                    ->get();
        // dd($app_module);
        $sub_module = DB::table('_sub_modules')
                    ->join('master_list_sub_module','master_list_sub_module.id','=','_sub_modules.sub_module_id')
                    ->select('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id')
                    ->where('_sub_modules.company_id',$company_id)
                    ->where('_sub_modules.status',1)
                    ->where('master_list_sub_module.status',1)
                    ->orderBy('_sub_modules.module_sequence','ASC')
                    ->get();
        // dd($app_module);

        foreach($sub_module as $key => $value)
        {
            $out['sub_modules_name'] = $value->sub_module_name;
            $out['sub_modules_id'] = $value->sub_module_id;
            $f_out[$value->module_id][] = $out;
        }

        foreach($app_module as $key => $value){
            if(!empty($value->table_name) && !empty($value->select_data) && !empty($value->group_by)){
                $d_out[$value->module_id] = DB::table($value->table_name)
                        ->select($value->select_data)
                        ->where('company_id',$company_id)
                        // ->where('user_id',$user_id)
                        ->groupBy($value->group_by)
                        ->pluck('user_id','user_id');    
            }
        }
        dd($d_out);
        // foreach($app_module as $key1 => $value1)
        // {
        //     foreach($f_out[$value1->module_id] as $key => $value)
        //     {
                
        //     }

        // }
        // dd($f_out);
                    // ->pluck('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name');
        return view('reports.module-check-status.ajax', [
                'app_module' => $app_module,
                'sub_module' => $f_out,
                'user_details'=>$user_details,
            ]);
    }

}

