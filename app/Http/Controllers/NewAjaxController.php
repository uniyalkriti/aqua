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
use App\JuniorData;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use DateTime;
use PDF;
use Illuminate\Support\Facades\Crypt;



class NewAjaxController extends Controller
{


     public function __construct()
    {

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            $this->company_id = Auth::user()->company_id;
            $this->is_admin = Auth::user()->is_admin;
            $this->without_junior = UserDetail::checkReportJunior($this->role_id,$this->company_id,$this->is_admin);

            return $next($request);
        });
    }


    # it is for regions of state
    public function cities(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {

            $data['code'] = 200;
            $data['result'] = Location3::where('location_2_id', $request->id)->where('company_id',$company_id)->where('status',1)->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # it is for regions of state
    public function catalog_product(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = CatalogProduct::where('catalog_1_id', $request->id)->where('company_id',$company_id)->where('status',1)->pluck('product_name', 'product_code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # city data using location_4 table
    public function cities_location4(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location4::where('location_3_id', $request->id)->where('company_id',$company_id)->where('status',1)->pluck('name', 'id');

            $data['result'] = Location6::join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('location_4','location_4.id','=','location_5.location_4_id')
                            ->where('location_4.location_3_id', $request->id)
                            ->where('location_6.company_id',$company_id)
                            ->where('location_6.status',1)
                            ->pluck('location_6.name', 'location_6.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function getAllVersion(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            

            $data['result'] = DB::table('version_management')
                            ->where('company_id',$request->id)
                            ->pluck('version_name','id')->toArray();
            $data['message'] = 'success';
        } 
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    #country data
    public function country(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax()) {
            $data['code'] = 200;
            $data['result'] = Location1::where('company_id',$company_id)->where('status',1)->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #state data
    public function state(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location2::where('company_id',$company_id)->where('status', 1)->where('location_1_id', $request->code)
                ->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function takeAction(Request $request)
    {	  
        // dd($request);


    	$company_id = Auth::user()->company_id;
    	$user_id = Auth::user()->id;
        $id = $request->action_id;
        $module = $request->module;
        $table = $request->tab;
        $act = $request->act;

        if ($act == 'delete' || $act == 'Sleep') {
            $act_status = 9;
        } elseif ($act == 'active') {
            $act_status = 1;
        } elseif ($act == 'inactive') {
            $act_status = 0;
        } else {
            $act_status = '1';
        }

        if ($request->ajax() && !empty($id)) {
            #module based action
            DB::beginTransaction();
            if ($module == 'User') # specific action for user module
            {

                #update user table status
                $query = DB::table('person_login')->where('company_id',$company_id)->where('person_id', $id)->update(['person_status' => $act_status]);

                if($query)
                {
                    $action_date_submit_query = DB::table('person_details')->where('company_id',$company_id)->where('person_id',$id)->update(['updated_by'=>$user_id,'deleted_deactivated_on'=>date("Y-m-d H:i:s")]);
                    if(!$action_date_submit_query)
                    {
                        DB::rollback();
                        $data['code'] = 401;
                        $data['result'] = 'fail';
                        $data['message'] = 'Action can not be completed';
                    }
                }

            }
            elseif ($module == 'Distributor') # specific action for user module
            {
                #update user table status
                $query = Dealer::where('id', $id)->where('company_id',$company_id)->update(['deleted_deactivated_on'=>date("Y-m-d H:i:s"),'dealer_status' => $act_status]);

            }
            elseif ($module == 'Retailer') # specific action for user module
            {
                #update user table status
                $query = Retailer::where('id', $id)->where('company_id',$company_id)->update(['deactivated_date_time'=>date("Y-m-d H:i:s"),'retailer_status' => $act_status]);
            }
            elseif ($module == 'IMEI') # specific action for IMEI module
            {
                #update user table status
                $query = Person::where('id', $id)->where('company_id',$company_id)->update(['imei_number' => NULL]);

            }
            elseif ($module == 'Super Stockist') # specific action for IMEI module
            {
                #update user table status
                // dd($act_status);
                $query = SS::where('c_id', $id)->where('company_id',$company_id)->update(['updated_at'=>date('Y-m-d H:i:s'),'active_status' => "$act_status"]);

            } 
            elseif ($module == 'Role') # specific action for IMEI module
            {
                #update user table status
                // dd($act_status);
                $query = DB::table('_role')
                ->where('role_id', $id)
                ->where('company_id',$company_id)
                ->update(['updated_at'=>date('Y-m-d H:i:s'),'status' => $act_status]);

            } 
             elseif ($module == 'sku_rate_list') 
            {
                $query = DB::table('product_rate_list')->where('id', $id)->where('company_id',$company_id)->update(['deactivated_on'=>date("Y-m-d H:i:s"),'status' => $act_status]);

            }


            else {
                // dd($table);
                $query = DB::table($table)
                    ->where('id', $id)
                    ->update(['status' => $act_status]);
            }
            if ($query) {
                #commit transaction
                DB::commit();
                $data['code'] = 200;
                $data['result'] = 'success';
                $data['message'] = 'success';
            } else {
                #rollback transaction
                DB::rollback();
                $data['code'] = 401;
                $data['result'] = 'fail';
                $data['message'] = 'Action can not be completed';
            }
        } else {
            #for unauthorized request
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #location 4 data
    public function location4(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location4::where('location_3_id', $request->id)->where('company_id',$company_id)->where('status',1)->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # location 5 data
    public function location5(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Area::where('location_4_id', $request->id)->where('status',1)->where('company_id',$company_id)->pluck('name', 'code');
            if (isset($request->single_flag) && ($request->single_flag) == 1) {
            } else {
                $data['dealer'] = Dealer::where('status', '1')->where('location_4_id', $request->id)->where('company_id',$company_id)->where('dealer_status',1)->pluck('name', 'id');
            }
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function location6(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location6::where('location_4_id', $request->id)->where('company_id',$company_id)->where('status',1)->pluck('name', 'code');
            if (isset($request->single_flag) && ($request->single_flag) == 1) {
            } else {
                $data['dealer'] = Dealer::where('status', '1')->where('location_4_id', $request->id)->where('company_id',$company_id)->pluck('name', 'id');
            }
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function location7(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $f = explode(",", $request->id);
            $data['code'] = 200;
            $data['result'] = Location7::join('location_6', 'location_6.code', '=', 'location_7.location_6_id')
                ->join('location_5', 'location_5.code', '=', 'location_6.location_5_id')
                ->join('location_4', 'location_4.code', '=', 'location_5.location_4_id')
                ->whereIn('location_4.id', $f)->pluck('location_7.name', 'location_7.code');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #fetch location 7  data based on ID
    public function location7_id(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $f = !empty($request->id) ? explode(",", $request->id) : '';
            $data['code'] = 200;
//print_r($request->id);die;
            $check = ($request->id == 'null' || $request->id == null || $request->id == '') ? false : true;

            $query = Location5::join('location_4', 'location_4.id', '=', 'location_5.location_4_id')->where('location_5.company_id',$company_id);
            if ($check) {
                $query->whereIn('location_4.id', $f);
            }
            $data['result'] = $query->pluck('location_5.name', 'location_5.id');

            #Distributor
            $q2 = DB::table('location_5')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_5.id')
                ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->where('location_5.company_id',$company_id);
            if ($check) {
                $q2->whereIn('location_5.location_4_id', $f);
            }
            $data['result2'] = $q2->where('dealer.name', '!=', '')->pluck('dealer.name', 'dealer.id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function location7_dist(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $f = explode(",", $request->id);
            $data['code'] = 200;
            $data['result'] = Location7::join('location_6', 'location_6.code', '=', 'location_7.location_6_id')
                ->join('location_5', 'location_5.code', '=', 'location_6.location_5_id')
                ->join('location_4', 'location_4.code', '=', 'location_5.location_4_id')
                ->select(DB::raw("CONCAT(location_7.code,'-',location_4.id) AS compound_id"), 'location_7.name')
                ->whereIn('location_4.id', $f)->pluck('location_7.name', 'compound_id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function city_wise_distributor(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::join('location_view', 'location_view.l3_code', '=', 'dealers.location_3_id', 'INNER ')
                ->where('location_view.l4_code', $request->id)
                ->where('dealer.company_id',$company_id)
                ->groupBy('dealers.id')
                ->pluck('dealers.name', 'dealers.dealer_code');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function senior_name(Request $request)
    {

        //echo"5435";die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;

            $data['result'] = User::where('status', '1')->where('company_id',$company_id)->where('role_id', $request->id)->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }   

    public function getDistributor(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id))
        {
            $data['code'] = 200;
            $id = explode(',',$request->id);
            $data['result'] = Dealer::whereIn('state_id',$id)->where('company_id',$company_id)->pluck('name','id');

            $data['message'] = 'success';

        }
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);

    }

    public function beat_wise_distributor(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::join('dealer_locations', 'dealer_locations.dealer_id', '=', 'dealers.id', 'INNER')
                ->where('dealer_locations.location_id', $request->id)
                ->where('dealer.company_id',$company_id)
                ->where('dealer_status',1)
                ->groupBy('dealers.id')
                ->pluck('dealers.name', 'dealers.id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function district(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location3::where('location_2_id', $request->id)->where('status',1)->where('company_id',$company_id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    #get dealer
    public function getBeatDealer(Request $request)
    {
    	$company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $beat=$request->id;

            $dealer=DealerLocation::join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                ->where('location_id',$beat)
                ->where('dealer.company_id',$company_id)
                ->where('dealer_location_rate_list.company_id',$company_id)
                ->where('user_id',0)
                ->groupBy('dealer_location_rate_list.dealer_id')
                ->pluck('dealer.name','dealer.id');
            $data['code']=200;
            $data['result']=$dealer;
            $data['message']='Dealer Person';
        }
        else {
                $data['code'] = 401;
                $data['result'] = '';
                $data['message'] = 'unauthorized request';
            }
            return json_encode($data);
    }

    #Multiple record on change on Region
    public function districtMultiple(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
//            $data['result'] = Location3::whereIn('location_2_id', $id)->pluck('name', 'id');
            $query = DB::table('location_view')
            ->where('l3_company_id',$company_id)
            ->where('l4_company_id',$company_id)
            ->where('l5_company_id',$company_id);
            
            if (!empty($request->id) && $request->id != 'null') {
                $query->whereIn('l2_id', $id);
            }
            $data['result'] = $query->pluck('l3_name', 'l3_id');
            $data['result2'] = $query->pluck('l6_name', 'l6_id');
            $data['result3'] = $query->pluck('l7_name', 'l7_id');
            if (!empty($request->id) && $request->id != 'null') {
                $user_query = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->whereIn('location_view.l2_id', $id)
                    ->where('l2_company_id',$company_id)
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid');
                $data['user'] = $user_query->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
            }

            $dealer_query = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->where('l2_company_id',$company_id)
                ->where('l3_company_id',$company_id)
                ->where('l4_company_id',$company_id);
            if (!empty($request->id) && $request->id != 'null') {
                $dealer_query->whereIn('location_view.l2_id', $id);
            }
            $dealer_query->where('dealer_location_rate_list.user_id', '!=', '0')
                ->groupBy('dealer_location_rate_list.dealer_id')
                ->select('dealer.name', 'dealer.id');
            $data['dealers'] = $dealer_query->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function town(Request $request)
    {
        $company_id = Auth::user()->company_id;
        
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location4::where('location_3_id', $request->id)->where('company_id',$company_id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Multiple state change
    public function townMultiple(Request $request)
    {
        $company_id = Auth::user()->company_id;
        
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);
            if (!empty($request->id) && $request->id != 'null') {
                $query = DB::table('location_view')->where('l4_company_id',$company_id)->where('l5_company_id',$company_id)->whereIn('l3_id', $id);
                $data['towns'] = $query->pluck('l6_name', 'l6_id');
                $data['beats'] = $query->pluck('l7_name', 'l7_id');
            } else {
//                $region=Location2::where('status',1)->pluck('name','id');
//                $state=Location3::where('status',1)->pluck('name','id');
                $data['towns'] = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                $data['beats'] = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            }


            if (!empty($request->id) && $request->id != 'null') {
                $data['user_data'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->whereIn('location_view.l3_id', $id)
                    ->where('l2_company_id',$company_id)
                    ->where('l3_company_id',$company_id)
                    ->where('l4_company_id',$company_id)
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->pluck('name', 'uid');
            } else {
                $data['user_data'] = DB::table('person')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->where('company_id',$company_id)
                    ->pluck('name', 'uid');
            }

            if (!empty($request->id) && $request->id != 'null') {
                $data['dealers'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->whereIn('location_view.l3_id', $id)
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->where('l2_company_id',$company_id)
                    ->where('l3_company_id',$company_id)
                    ->where('l4_company_id',$company_id)
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->select('dealer.name', 'dealer.id')
                    ->pluck('name', 'id');
            } else {
                $data['dealers'] = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            }

            $data['code'] = 200;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function beat(Request $request)
    {
        $company_id = Auth::user()->company_id;
        
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location5::where('location_4_id', $request->id)->where('company_id',$company_id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #beat multiple
    public function beatMultiple(Request $request)
    {
        $company_id = Auth::user()->company_id;
        
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);
            if (!empty($request->id) && $request->id != 'null') {
                $query = DB::table('location_view')->whereIn('l4_id', $id)
                    ->where('l4_company_id',$company_id);
                $data['beats'] = $query->pluck('l5_name', 'l5_id');
            } else {
                $data['beats'] = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            }

            if (!empty($request->id) && $request->id != 'null') {
                $data['user_data'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->whereIn('location_view.l4_id', $id)
                    ->where('l2_company_id',$company_id)
                    ->where('l3_company_id',$company_id)
                    ->where('l4_company_id',$company_id)
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->pluck('name', 'uid');
            } else {
                $data['user_data'] = DB::table('person')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('company_id',$company_id)
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            if (!empty($request->id) && $request->id != 'null') {
                $data['dealers'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->whereIn('location_view.l4_id', $id)
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->where('l2_company_id',$company_id)
                    ->where('l3_company_id',$company_id)
                    ->where('l4_company_id',$company_id)
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->select('dealer.name', 'dealer.id')
                    ->pluck('name', 'id');
            } else {
                $data['dealers'] = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            }

            $data['code'] = 200;
//            $data['result'] = Location5::where('location_4_id', $request->id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Manage ajax on multiple select of dealer
    public function distributorMultiple(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);

            $beat_query = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id');
            if ($request->id != 'null') {
                $beat_query->whereIn('dealer_location_rate_list.dealer_id', $id);
            }
            $data['beats'] = $beat_query->where('l5_company_id',$company_id)->pluck('l5_name', 'l5_id');

            $user_query = DB::table('dealer_location_rate_list')
                ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                ->where('dealer_location_rate_list.user_id', '!=', '0');
            if ($request->id != 'null') {
                $user_query->whereIn('dealer_location_rate_list.dealer_id', $id);
            }
            $user_query->groupBy('dealer_location_rate_list.user_id')
                ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid');
            $data['user_data'] = $user_query->pluck('name', 'uid');

            $data['code'] = 200;
//            $data['result'] = Location5::where('location_4_id', $request->id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Get user on change of role
    public function getUser(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->role)) {
            $role = $request->role;
            // dd($role);
            $data['code'] = 200;


            $query = DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->where('role_id', $role)
                ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as name"), 'person.id')
                ->where('person.company_id',$company_id)
                ->where('person_status',1)
                ->pluck('person.name', 'person.id');


            $data['result'] = $query;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #get senior name change of user designation
    public function getSenior(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->senior_id)) {
            $senior_id = $request->senior_id;
            $data['code'] = 200;


            $query = DB::table('_role')
                ->where('role_id','<',$senior_id)
                ->where('company_id',$company_id)
                ->pluck('rolename', 'role_id');
            // dd($query->count());
            if($query->count()==0)
            {
                // dd('s');
                $query = DB::table('_role')->where('role_id',1)->where('company_id',$company_id)->pluck('rolename', 'role_id');
                $data['result'] = $query;
                $data['message'] = 'success';
            }
            elseif(!empty($query))
            {
                $data['result'] = $query;
                $data['message'] = 'success';
            }
           
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #get user data by role id
    public function getUserByRole(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);

            $data['user_data'] = DB::table('person')
                ->whereIn('person.role_id', $id)
                ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                ->where('company_id',$company_id)
                ->pluck('name', 'uid');

            $data['code'] = 200;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function state_name(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location2::where('status', 1)->where('company_id',$company_id)->where('location_1_id', $request->id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_user_name(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        $id = explode(',', $request->id);

         $usersData = DB::table('person')
                ->join('users','users.id','=','person.id')
                ->join('person_login','person_login.person_id','=','person.id')
                ->join('location_5','location_5.id','=','person.head_quater_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->select(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as user_name"),'person.id','location_5.name as l5_name','_role.rolename')
                ->where('person_status','=','1')
                ->where('users.is_admin','!=','1')
                ->where('person.company_id',$company_id)
                ->whereIn('state_id', $id)->where('state_id','!=','0')
                ->groupBy('person.id')
                ->get()->toArray();
                // ->pluck(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as user_name"),'person.id');

        foreach ($usersData as $ukey => $uvalue) {
            $users[$uvalue->id] = $uvalue->user_name.'-'.$uvalue->rolename.'-'.$uvalue->l5_name;
        }

        if ($request->ajax() && ($request->id)) {
            $data['code'] = 200;
            $data['result'] = $users;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function get_catalog_product(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('catalog_product')->where('company_id',$company_id)->whereIn('catalog_id', $id)->where('status','=','1')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_location_5(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('location_5')->join('location_4','location_4.id','=','location_5.location_4_id')->where('location_5.company_id',$company_id)->whereIn('location_3_id', $id)->pluck('location_5.name', 'location_5.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_town_from_state(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('location_6')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->join('location_3','location_3.id','=','location_4.location_3_id')
                                ->where('location_6.company_id',$company_id)
                                ->whereIn('location_3.id', $id)
                                ->pluck('location_6.name', 'location_6.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_distributor_from_state(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('dealer')
                                ->whereIn('state_id', $id)
                                ->where('dealer.company_id',$company_id)
                                ->pluck('dealer.name', 'dealer.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function get_beat_from_distributor(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('location_7')
                                ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                                ->whereIn('dealer_id', $id)
                                ->where('location_7.company_id',$company_id)
                                ->pluck('location_7.name', 'location_7.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function get_location_6(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('location_6')->join('location_5','location_5.id','=','location_6.location_5_id')->where('location_6.company_id',$company_id)->whereIn('location_5.id', $id)->pluck('location_6.name', 'location_6.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_location_7(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('location_7')->join('location_6','location_6.id','=','location_7.location_6_id')->where('location_7.company_id',$company_id)->whereIn('location_6.id', $id)->pluck('location_7.name', 'location_7.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function get_retailer(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('retailer')->where('retailer.company_id',$company_id)->whereIn('location_id', $id)->pluck('retailer.name', 'retailer.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_dealer(Request $request)
    {
        //echo $request->id;die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('dealer')->where('dealer.company_id',$company_id)->whereIn('town_id', $id)->pluck('dealer.name', 'dealer.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }



    public function get_dealer_name(Request $request)
    {
        // dd($request;)
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id) && ($request->user_id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('dealer_location_rate_list')
                            ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                            ->join('location_6','location_6.id','=','location_7.location_6_id')
                            ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                            ->where('dealer_location_rate_list.company_id',$company_id)
                            ->where('location_7.company_id',$company_id)
                            ->where('location_6.company_id',$company_id)
                            ->where('location_6.id',$request->id)
                            ->where('dealer_location_rate_list.user_id',$request->user_id)
                            ->groupBy('dealer.id')
                            ->pluck('dealer.name','dealer.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_beat_name_new(Request $request)
    {
        // dd($request;)
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && ($request->id) && ($request->user_id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
            $data['result'] = DB::table('dealer_location_rate_list')
                            ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                            ->where('dealer_location_rate_list.company_id',$company_id)
                            ->where('location_7.company_id',$company_id)
                            ->where('dealer_location_rate_list.dealer_id',$request->id)
                            ->where('dealer_location_rate_list.user_id',$request->user_id)
                            ->groupBy('location_7.id')
                            ->pluck('location_7.name','location_7.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function get_hq(Request $request)
    {
        //echo "123";die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location3::where('location_2_id', $request->id)->where('company_id',$company_id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function role_user(Request $request)
    {
        //echo "123";die;
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = User::where('role_id', $request->id)->where('company_id',$company_id)->pluck('name', 'role_id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function state_wise_distributor(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::where('status', '1')->where('company_id',$company_id)->where('location_2_id', $request->id)->pluck('name', 'dealer_code');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function beat_wise_retailer(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Retailer::where('beat_code', $request->id)->where('company_id',$company_id)->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #beat wise distributor list (Latest)
    public function beatWiseDistributor(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $f = [];
            $f = explode(',', $request->id);
            $check = ($request->id == 'null' || $request->id == null || $request->id == '') ? false : true;
            $data['code'] = 200;
            $query = Dealer::join('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id', 'INNER')->where('company_id',$company_id);
            if ($check) {
                $query->whereIn('dealer_location_rate_list.location_id', $f);
            }
            $data['result'] = $query->groupBy('dealer.id')
                ->pluck('dealer.name', 'dealer.id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # working query when outlet is selected
//select _outlet_types.name, COUNT(retailers.outlet_type_id) from _outlet_types LEFT JOIN retailers on (_outlet_types.id=retailers.outlet_type_id AND retailers.id=14) GROUP BY _outlet_types.id
    public function beat_route_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax()) {
//            $query = _outletType::leftJoin('retailer', function ($join) use ($request) {
//                $join->on('retailer.outlet_type_id', '=', '_retailer_outlet_type.id');
//                $join->on('retailer.id', '=', $request);
//
//            });
            $temp = [];
            if (!empty($request->belt)) {
                $beltArr = $request->belt;
                if (!empty($beltArr)) {
                    $temp = DB::table('location_view')->whereIn('l4_id', $beltArr)->where('l7_company_id',$company_id)->pluck('l7_id as l5_id');
                }
            }

            if (!empty($request->beat)) {
                $temp = [];
                $temp = $request->beat;
            }

            $query = _outletType::join('retailer', 'retailer.outlet_type_id', '=', '_retailer_outlet_type.id', 'LEFT')
                    ->select("_retailer_outlet_type.outlet_type AS outlet_name", DB::raw('COUNT(retailer.outlet_type_id) as total'), '_retailer_outlet_type.id as outlet_id')
                    ->where('_retailer_outlet_type.company_id',$company_id);
            $query->groupBy('_retailer_outlet_type.id');
            if (!empty($temp)) {
                $query->whereIn('retailer.location_id', $temp);
            }
            if (!empty($request->outlet)) {
                $query->whereIn('retailer.outlet_type_id', $request->outlet);
            }
//               if(!empty($request->outlet))
//               {
//                   $query->where('retailer.outlet_type_id',$request->outlet);
//               }
            $outlet_categories = $query->get();

            $platinum = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'))->where('company_id',$company_id);
            if (!empty($temp)) {
                $platinum->whereIn('user_sales_order.location_id', $temp);
            }
            $platinum->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) >= ?', [15000])
                ->first();
            $diamond = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'))->where('company_id',$company_id);
            if (!empty($temp)) {
                $diamond->whereIn('user_sales_order.location_id', $temp);
            }
            $diamond->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) > ?', [9999])
                ->havingRaw('SUM(user_sales_order.total_sale_value) < ?', [15000])
                ->first();
            $gold = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'))->where('company_id',$company_id);
            if (!empty($temp)) {
                $gold->whereIn('user_sales_order.location_id', $temp);
            }
            $gold->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) > ?', [7499])
                ->havingRaw('SUM(user_sales_order.total_sale_value) < ?', [10000])
                ->first();
            $silver = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'))->where('company_id',$company_id);
            if (!empty($temp)) {
                $silver->whereIn('user_sales_order.location_id', $temp);
            }
            $silver->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) > ?', [4999])
                ->havingRaw('SUM(user_sales_order.total_sale_value) < ?', [7500])
                ->first();
            $semi_wholeseller = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'))->where('company_id',$company_id);
            if (!empty($temp)) {
                $semi_wholeseller->whereIn('user_sales_order.location_id', $temp);
            }
            $semi_wholeseller->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.case_qty) > ?', [7])
                ->havingRaw('SUM(user_sales_order.case_qty) < ?', [25])
                ->first();
            $wholeseller = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'))->where('company_id',$company_id);
            if (!empty($temp)) {
                $wholeseller->whereIn('user_sales_order.location_id', $temp);
            }
            $wholeseller->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.case_qty) > ?', [24])
                ->first();

            $query2 = Retailer::leftJoin('_retailer_outlet_type', '_retailer_outlet_type.id', '=', 'retailer.outlet_type_id')
                ->leftJoin('location_view', 'location_view.l7_id', '=', 'retailer.location_id')
                ->leftJoin('location_7', 'location_view.l7_id', '=', 'location_7.id')
                ->leftJoin('dealer', 'retailer.dealer_id', '=', 'dealer.id')
                ->where('retailer.company_id',$company_id);
            $temp = [];
            if (!empty($request->zone))
            {
                $zoneArr = $request->zone;
                if (!empty($zoneArr)) {
                    $temp = [];
                    $temp = DB::table('location_view')->whereIn('l1_id', $zoneArr)->where('l1_company_id',$company_id)->pluck('l7_id as l5_id');
                }
            }
            if (!empty($request->belt)) {
                $beltArr = $request->belt;
                if (!empty($beltArr)) {
                    $temp = [];
                    $temp = DB::table('location_view')->whereIn('l4_id', $beltArr)->where('l4_company_id',$company_id)->pluck('l7_id as l5_id');
                }
            }
            if (!empty($temp)) {
                $query2->whereIn('retailer.location_id', $temp);
            }
            if (!empty($request->beat)) {
                $beatArr = $request->beat;
                $query2->whereIn('retailer.location_id', $beatArr);
            }
            if (!empty($request->location_3)) {
                $location_3 = $request->location_3;
                $query2->whereIn('l3_id', $location_3);
            }
             if (!empty($request->location_4)) {
                $location_4 = $request->location_4;
                $query2->whereIn('l4_id', $location_4);
            }
               if (!empty($request->location_5)) {
                $location_5 = $request->location_5;
                $query2->whereIn('l5_id', $location_5);
            }
            if (!empty($request->distributor)) {
                $distributorArr = $request->distributor;
                $query2->whereIn('dealer.id', $distributorArr);
                $query2->where('dealer.company_id',$company_id);
            }
            if (!empty($request->outlet)) {
                $outletArr = $request->outlet;
                $query2->whereIn('retailer.outlet_type_id', $outletArr);
            }
            // if (!empty($request->day)) {
            //     $dayArr = $request->day;
            //     $query2->whereIn('location_5.day', $dayArr);
            // }
            $query2 = $query2->select('retailer.id', 'retailer.class as class', '_retailer_outlet_type.outlet_type as outlet_category', 'location_view.l7_name as beat', 'location_view.l1_name as zone', 'location_view.l2_name as region', 'location_view.l3_name as state', 'location_view.l6_name as town', 'retailer.retailer_code as outlet_id', 'retailer.name as outlet_name', 'dealer.name as dealer_name','dealer.id as dealer_id','retailer.created_on','l4_name','l5_name');
            $rows = $query2->orderBy('retailer.created_on','ASC')->get();

            return view('reports.beat-route-ajax', [
                'outlet_categories' => $outlet_categories,
                'platinum' => $platinum,
                'diamond' => $diamond,
                'gold' => $gold,
                'silver' => $silver,
                'semi_wholeseller' => $semi_wholeseller,
                'wholeseller' => $wholeseller,
                'rows' => $rows
            ]);
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function distributorsBeat(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $idArr = explode(',', $request->id);
            if ($request->id == 'null') {
                $data['result'] = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            } else {

                $query = DB::table('dealer_location_rate_list')
                    ->leftJoin('location_7', 'location_7.id', '=', 'dealer_location_rate_list.location_id')
                    ->where('dealer_location_rate_list.company_id',$company_id);
                $query->where('dealer_id', $idArr);
                $query->groupBy('location_id');
                $data['result'] = $query->pluck('location_7.name', 'location_7.id');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function town_distributor(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::where('location_3_id', $request->id)->where('dealer.company_id',$company_id)->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
#Ajax view page for market beat plan
    public function marketBeatPlanReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->month)) {
            $month = $request->month;
            $town = $request->belt;
            $distributor = $request->distributor;
            $beat = $request->beat;
            $year = date('Y', strtotime($month));

            #retialer wise month wise secondary sales data
//            $retailer_sales_data=DB::table('beat_classification')
//                ->where('beat_classification.month', $month);

            $dealer_query = Dealer::where('dealer.name', '!=', '')->where('dealer.company_id',$company_id);
            if (!empty($distributor)) {
                $dealer_query->whereIn('dealer.id', $distributor);
            }
            $dealer = $dealer_query->select('dealer.name', 'dealer.address', 'location_4.name as town', 'location_3.name as state', 'dealer_code','dealer.id as did')
                ->leftJoin('location_4', 'location_4.id', '=', 'dealer.town_id')
                ->leftJoin('location_3', 'location_3.id', '=', 'dealer.state_id')
                ->first();
            $dealer_target_q = DistributorTarget::where('dealer_id', '>', '0')->where('company_id',$company_id);
            if (!empty($distributor)) {
                $dealer_target_q->whereIn('dealer_id', $distributor);
            }
            $dealer_target = $dealer_target_q->where('session', 'Like', '%' . $year . '%')
                ->select('month', 'target', 'achievement')->get();
            //  print_r($dealer_target); exit;
            $outlet_q = UserDealerRetailer::where('dealer_id', '>', '0');
            if (!empty($distributor)) {
                $outlet_q->whereIn('dealer_id', $distributor);
            }
            $outlet = $outlet_q->select(DB::raw('COUNT(user_dealer_retailer.id) as total'))
                ->first();
            $noBeat = DB::table('location_5')->where('location_4_id', $town)
                ->select(DB::raw('COUNT(location_5.id) as total'))
                ->first();

            $isrCount_q = UserDealerRetailer::where('dealer_id', '>', '0');
            if (!empty($distributor)) {
                $isrCount_q->whereIn('dealer_id', $distributor);
            }
            $isrCount = $isrCount_q->where('person.role_id', 12)
                ->join('person', 'person.id', '=', 'user_dealer_retailer.user_id')
                ->select(DB::raw('COUNT(Distinct user_dealer_retailer.user_id) as total'))->first();
            $records_q = Dealer::leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->leftJoin('location_5', 'location_5.id', '=', 'dealer_location_rate_list.location_id')
                ->leftJoin('retailer', 'retailer.location_id', '=', 'location_5.id')
                ->select('location_5.name as market', 'location_5.id as lid');
            if (!empty($beat)) {
                $records_q->whereIn('location_5.id', $beat);
            }
            if (!empty($distributor)) {
                $records_q->whereIn('dealer_location_rate_list.dealer_id', $distributor);
            }
            $records_q->distinct('location_5.id');
            if (!empty($distributor)) {
                $records_q->where('dealer.id', $distributor);
            }
            $records = $records_q->get();

            $platinum = DB::table('beat_classification')
            ->where('beat_classification.month', $month)->where('total_sale', '>', 10000)->groupBy('l5_id','month')->pluck(DB::raw("COUNT(retailer_id)"),'l5_id');

            $diamond = DB::table('beat_classification')
            ->where('beat_classification.month', $month)->whereRaw('total_sale >=10000 and total_sale <= 12000')->groupBy('l5_id','month')->pluck(DB::raw("COUNT(retailer_id)"),'l5_id');
            
            $gold = DB::table('beat_classification')
            ->where('beat_classification.month', $month)->whereRaw('total_sale >=7500 and total_sale <= 10000')->groupBy('l5_id','month')->pluck(DB::raw("COUNT(retailer_id)"),'l5_id');

            $silver = DB::table('beat_classification')
            ->where('beat_classification.month', $month)->whereRaw('total_sale >=5000 and total_sale <= 7500')->groupBy('l5_id','month')->pluck(DB::raw("COUNT(retailer_id)"),'l5_id');
                    // dd($platinum);
            $d = [];
            if (!empty($records)) {
                foreach ($records as $k => $raw) {

                    $d[$k]['market'] = $raw->market;
                    $d[$k]['location_id'] = $raw->lid;

                    // $d[$k]['platinum'] = DB::table('beat_classification')
                    //     ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->where('total_sale', '>', 10000)->count();
                    // $d[$k]['diamond'] = DB::table('beat_classification')
                    //     ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=10000 and total_sale <= 12000')->count();
                    // $d[$k]['gold'] = DB::table('beat_classification')
                    //     ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=7500 and total_sale <= 10000')->count();
                    // $d[$k]['silver'] = DB::table('beat_classification')
                    //     ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=5000 and total_sale <= 7500')->count();
                    $d[$k]['sws'] = 0;
                    $d[$k]['ws'] = 0;


                    $d[$k]['categories'] = DB::table('_outlet_types')->leftJoin('retailer', '_outlet_types.id', '=', 'retailer.outlet_type_id')
                        ->where('retailer.location_id', $raw->lid)
                        ->select(DB::raw('count(retailer.id) as cc'), '_outlet_types.id')
                        ->groupBy('_outlet_types.id')
                        ->pluck('cc', 'id');
                    $d[$k]['t2'] = !empty($d[$k]['categories']) ? array_sum($d[$k]['categories']->toArray()) : 0;
                }
            }
            return view('reports.market-beat-plan.ajax', [
                'records' => $d,
                'dealer' => $dealer,
                'outlet' => $outlet,
                'isrCount' => $isrCount,
                'noBeat' => $noBeat,
                'dealer_target' => $dealer_target,
                'platinum'=>$platinum,
                'silver'=>$silver,
                'gold'=>$gold,
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }
    
//     #Ajax view page for market beat plan
//     public function marketBeatPlanReport(Request $request)
//     {
//         if ($request->ajax() && !empty($request->month)) {
//             $month = $request->month;
//             $town = $request->belt;
//             $distributor = $request->distributor;
//             $beat = $request->beat;
//             $year = date('Y', strtotime($month));

//             #retialer wise month wise secondary sales data
// //            $retailer_sales_data=DB::table('beat_classification')
// //                ->where('beat_classification.month', $month);

//             $dealer_query = Dealer::where('dealer.name', '!=', '');
//             if (!empty($distributor)) {
//                 $dealer_query->whereIn('dealer.id', $distributor);
//             }
//             $dealer = $dealer_query->select('dealer.name', 'dealer.address', 'location_4.name as town', 'location_3.name as state', 'dealer_code')
//                 ->leftJoin('location_4', 'location_4.id', '=', 'dealer.town_id')
//                 ->leftJoin('location_3', 'location_3.id', '=', 'dealer.state_id')
//                 ->first();
//             $dealer_target_q = DistributorTarget::where('dealer_id', '>', '0');
//             if (!empty($distributor)) {
//                 $dealer_target_q->whereIn('dealer_id', $distributor);
//             }
//             $dealer_target = $dealer_target_q->where('session', 'Like', '%' . $year . '%')
//                 ->select('month', 'target', 'achievement')->get();
//             //  print_r($dealer_target); exit;
//             $outlet_q = UserDealerRetailer::where('dealer_id', '>', '0');
//             if (!empty($distributor)) {
//                 $outlet_q->whereIn('dealer_id', $distributor);
//             }
//             $outlet = $outlet_q->select(DB::raw('COUNT(user_dealer_retailer.id) as total'))
//                 ->first();
//             $noBeat = DB::table('location_5')->where('location_4_id', $town)
//                 ->select(DB::raw('COUNT(location_5.id) as total'))
//                 ->first();

//             $isrCount_q = UserDealerRetailer::where('dealer_id', '>', '0');
//             if (!empty($distributor)) {
//                 $isrCount_q->whereIn('dealer_id', $distributor);
//             }
//             $isrCount = $isrCount_q->where('person.role_id', 12)
//                 ->join('person', 'person.id', '=', 'user_dealer_retailer.user_id')
//                 ->select(DB::raw('COUNT(Distinct user_dealer_retailer.user_id) as total'))->first();
//             $records_q = Dealer::leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
//                 ->leftJoin('location_5', 'location_5.id', '=', 'dealer_location_rate_list.location_id')
//                 ->leftJoin('retailer', 'retailer.location_id', '=', 'location_5.id')
//                 ->select('location_5.name as market', 'location_5.id as lid');
//             if (!empty($beat)) {
//                 $records_q->whereIn('location_5.id', $beat);
//             }
//             if (!empty($distributor)) {
//                 $records_q->whereIn('dealer_location_rate_list.dealer_id', $distributor);
//             }
//             $records_q->distinct('location_5.id');
//             if (!empty($distributor)) {
//                 $records_q->where('dealer.id', $distributor);
//             }
//             $records = $records_q->get();
//             $d = [];
//             if (!empty($records)) {
//                 foreach ($records as $k => $raw) {
//                     $d[$k]['market'] = $raw->market;

//                     $d[$k]['platinum'] = DB::table('beat_classification')
//                         ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->where('total_sale', '>', 10000)->count();
//                     $d[$k]['diamond'] = DB::table('beat_classification')
//                         ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=10000 and total_sale <= 12000')->count();
//                     $d[$k]['gold'] = DB::table('beat_classification')
//                         ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=7500 and total_sale <= 10000')->count();
//                     $d[$k]['silver'] = DB::table('beat_classification')
//                         ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=5000 and total_sale <= 7500')->count();
//                     $d[$k]['sws'] = 0;
//                     $d[$k]['ws'] = 0;

// //                    $d[$k]['diamond'] = Retailer::where('location_id', $raw->lid)->where('class', 2)->count();
// //                    $d[$k]['gold'] = Retailer::where('location_id', $raw->lid)->where('class', 3)->count();
// //                    $d[$k]['silver'] = Retailer::where('location_id', $raw->lid)->where('class', 4)->count();
// //                    $d[$k]['sws'] = Retailer::where('location_id', $raw->lid)->where('class', 5)->count();
// //                    $d[$k]['ws'] = Retailer::where('location_id', $raw->lid)->where('class', 6)->count();

//                     $d[$k]['categories'] = DB::table('_outlet_types')->leftJoin('retailer', '_outlet_types.id', '=', 'retailer.outlet_type_id')
//                         ->where('retailer.location_id', $raw->lid)
//                         ->select(DB::raw('count(retailer.id) as cc'), '_outlet_types.id')
//                         ->groupBy('_outlet_types.id')
//                         ->pluck('cc', 'id');
//                     $d[$k]['t2'] = !empty($d[$k]['categories']) ? array_sum($d[$k]['categories']->toArray()) : 0;
//                 }
//             }
//             return view('reports.market-beat-plan.ajax', [
//                 'records' => $d,
//                 'dealer' => $dealer,
//                 'outlet' => $outlet,
//                 'isrCount' => $isrCount,
//                 'noBeat' => $noBeat,
//                 'dealer_target' => $dealer_target
//             ]);
//         } else {
//             echo '<p class="alert-danger">No Data Found</p>';
//         }
//     }

    #Fetch user based on belt
    public function getBeltUsers(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = UserDetail::where('location_6_id', $request->id)->select(DB::raw('CONCAT_WS(" ",first_name,middle_name,last_name) as name'), 'user_id')->pluck('name', 'user_id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Multiple record on change on Region
    public function getLocation(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) && !empty($request->type)) {
            $id = explode(',',$request->id);
            $type = $request->type;
            $data['code'] = 200;
            if($request->flag == 84)
            {
                $query = DB::table('location_5')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->whereIn('location_3_id', $id)
                ->where('location_5.company_id',$company_id)
                ->where('location_5.status', '=', 1)
                ->orderBy('location_5.name','ASC')
                ->pluck('location_5.name', 'location_5.id');
            }
            else
            {
                $table = 'location_' . $type;
                $ptable_id = 'location_' . ($type - 1) . '_id';
                $query = DB::table($table)
                ->whereIn($ptable_id, $id)
                ->where('company_id',$company_id)
                ->where('status', '=', 1)
                ->orderBy($table.'.name','ASC')
                ->pluck('name', 'id');
            }
            

            


            $data['result'] = $query;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function statndard_filter_onchange(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) && !empty($request->type)) {
            $id = explode(',',$request->id);
            $type = $request->type;
            $data['code'] = 200;
            if($type == 'distributor')
            {
                $query = DB::table('dealer')
                        ->where('dealer_status',1)
                        ->where('company_id',$company_id)   
                        ->whereIn('town_id',$id)
                        ->orderBy('dealer.name','ASC')
                        ->pluck('name','id');
                $beat_array = DB::table('location_7')
                            ->whereIn('location_6_id',$id)
                            ->where('company_id',$company_id)   
                            ->where('status',1)
                            ->orderBy('location_7.name','ASC')
                            ->pluck('name','id');
                $data['beat_array'] = $beat_array;

            }
            else
            {
                $table = 'location_' . $type;
                $ptable_id = 'location_' . ($type - 1) . '_id';

                if($table == 'location_5' && $ptable_id == 'location_4_id')
                {
                    // dd('q');
                    $ptable_id = 'location_4.location_3_id';
                    $query = DB::table($table)
                            ->join('location_4','location_4.id','=','location_5.location_4_id')
                            ->whereIn($ptable_id, $id)
                            ->where('location_5.company_id',$company_id)
                            ->where('location_5.status', '=', 1)
                            ->orderBy('location_5.name','ASC')
                            ->pluck('location_5.name as name', 'location_5.id as id');
                }
               
                else
                {
                    $query = DB::table($table)
                    ->whereIn($ptable_id, $id)
                    ->where('company_id',$company_id)
                    ->where('status', '=', 1)
                    ->orderBy($table.'.name','ASC')
                    ->pluck('name', 'id');
                }
            }
            
            // dd($table);
            
            



            $data['result'] = $query;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function statndard_filter_onchange_for_user(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) ) {
            $id = explode(',',$request->id);

             $user_auth=Auth::user();
       
             if($user_auth->role_id==1 || $user_auth->is_admin=='1' || $user_auth->role_id==50)
            {
                $junior_data = array();
            }
            else
            {
                Session::forget('juniordata');
                $user_data=JuniorData::getJuniorUser($user_auth->id,$company_id);
                Session::push('juniordata', $user_auth->id);
                $junior_data = Session::get('juniordata');
            }

            // $query = array();
            $querydata =  DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('users','users.id','=','person.id')
                    ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id')
                    ->where('person_status', 1)
                    ->where('is_admin','!=', 1)
                    ->where('person.company_id',$company_id)
                    ->whereIn('state_id',$id)
                    ->orderBy('person.first_name', 'ASC');
                    if (!empty($junior_data)) 
                    {
                        $querydata->whereIn('person.id', $junior_data);
                    }
            $query = $querydata->get();
                    // ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"), 'person.id as user_id')->toArray();

                    // dd($query);
            $query_dealer =  DB::table('dealer')
                    ->where('dealer_status', 1)
                    ->where('company_id',$company_id)
                    ->whereIn('state_id',$id)
                    ->orderBy('dealer.name', 'ASC')
                    ->pluck('name', 'id');

                // dd($query);
            $data2['code'] = 200;
            $data2['result'] = ($query);
            $data2['query_dealer'] = $query_dealer;
            $data2['message'] = 'success';
                // dd($data2);
            
        } 
        else 
        {
            $data2['code'] = 401;
            $data2['result'] = '';
            $data2['query_dealer'] = '';
            $data2['message'] = 'unauthorized request';
        }
        return json_encode($data2);
    }
    public function statndard_filter_onchange_for_dealer(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) ) {
            $id = explode(',',$request->id);
            $query = array();

            $query_dealer =  DB::table('dealer')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                    ->where('dealer.dealer_status', 1)
                    ->where('dealer.company_id',$company_id)
                    ->whereIn('dealer.id',$id)
                    ->orderBy('location_7.name', 'asc')
                    ->pluck('location_7.name', 'location_7.id');


            $data2['code'] = 200;
            $data2['query_dealer'] = $query_dealer;
            $data2['message'] = 'success';
        } 
        else 
        {
            $data2['code'] = 401;
            $data2['query_dealer'] = '';
            $data2['message'] = 'unauthorized request';
        }
        return json_encode($data2);
    }
    public function getLocationForStandaradFilter(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) && !empty($request->type)) {
            $id = explode(',',$request->id);
            $type = $request->type;
            $data['code'] = 200;

            $table = 'location_' . $type;
            $ptable_id = 'location_' . ($type - 1) . '_id';

            $query = DB::table($table)
                ->whereIn($ptable_id, $id)
                ->where('company_id',$company_id)
                ->where('status', '=', 1)
                ->orderBy($table.'.name','ASC')
                ->pluck('name', 'id');
            
            if($table == 'location_7')
            {
                $dealer_details = DB::table('dealer_location_rate_list')
                                ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                                ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                                ->whereIn('l6_id',$id)
                                ->where('dealer_status',1)
                                ->where('dealer_location_rate_list.company_id',$company_id)
                                ->where('dealer.company_id',$company_id)
                                ->orderBy('dealer.name','ASC')
                                ->pluck('dealer.name','dealer.id');

                $data['dealer'] = $dealer_details;
                $data['dealer_flag'] = 1;
            }   
            $data['result'] = $query;   
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function getCatalogForStandaradFilter(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) && !empty($request->type)) {
            $id = explode(',',$request->id);
            $type = $request->type;
            $data['code'] = 200;

            $table = 'catalog_' . $type;
            $ptable_id = 'catalog_' . ($type - 1) . '_id';

            $query = DB::table($table)
                ->whereIn($ptable_id, $id)
                ->where('company_id',$company_id)
                ->where('status', '=', 1)
                ->orderBy($table.'.name','ASC')
                ->pluck('name', 'id');
            
        
            $data['result'] = $query;   
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Get CSA
    public function getCSA(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $id = $request->id;
            $data['code'] = 200;


            $query = DB::table('csa')
                ->where('state_id', $id)
                ->orderBy('csa_name','ASC')
                ->pluck('csa_name', 'c_id');


            $data['result'] = $query;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Fetch receipt
    public function getReceipt(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
//            $idArr = explode(',', $request->id);
            $user_id = $request->id;
            $date = $request->date;

            $data['code'] = 200;
            $query = DB::table('dealer_payments')
                ->where('user_id', $user_id);
            if (!empty($date)) {
                $query->whereRaw("DATE_FORMAT(dealer_payments.payment_recevied_date, '%Y-%m-%d') = '$date'");
            }
            $data['result'] = $query->pluck('payment_recevied_date', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Fetch user based on beat
    public function getBeatUsers(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            if (!empty($request->id) && $request->id != 'null') {
                $data['result'] = DB::table('dealer_location_rate_list')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.location_id', $request->id)
                    ->where('person.id', '!=', 0)
                    ->select(DB::raw('CONCAT_WS(" ",first_name,middle_name,last_name) as name'), 'person.id as user_id')
                    ->pluck('name', 'user_id');
            } else {
                $data['result'] = DB::table('person')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Ajax view page for tour program
//    public function tourProgramReport(Request $request)
//    {
//        if ($request->ajax() && !empty($request->month)) {
//
////            $user_id = $request->user;
//            $month = $request->month;
//
//            $work_status = DB::table('_task_of_the_day')->pluck('task', 'id');
//
//            $awsome_query = MonthlyTourProgram::leftJoin('location_view', 'location_view.l5_id', '=', 'monthly_tour_program.locations')
//                ->leftJoin('dealer', 'dealer.id', '=', 'monthly_tour_program.dealer_id')
//                ->leftJoin('person', 'monthly_tour_program.person_id', 'person.id')
//                ->leftJoin('location_5', 'monthly_tour_program.locations', '=', 'location_5.id')
//                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('person as p', 'p.id', '=', 'person.person_id_senior');
//            #Region filter
//            if (!empty($request->region)) {
//                $region = $request->region;
//                $awsome_query->whereIn('location_view.l2_id', $region);
//            }
//            #State filter
//            if (!empty($request->area)) {
//                $state = $request->area;
//                $awsome_query->whereIn('location_view.l3_id', $state);
//            }
//            #Town filter
//            if (!empty($request->territory)) {
//                $town = $request->territory;
//                $awsome_query->whereIn('location_view.l4_id', $town);
//            }
//            #Beat filter
//            if (!empty($request->belt)) {
//                $beat = $request->belt;
//                $awsome_query->whereIn('location_view.l5_id', $beat);
//            }
//            #Dealer filter
//            if (!empty($request->distributor)) {
//                $distributor = $request->distributor;
//                $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
//            }
//            #Role filter
//            if (!empty($request->role)) {
//                $role_id = $request->role;
//                $awsome_query->whereIn('person.role_id', $role_id);
//            }
//            #User Filter
//            if (!empty($request->user)) {
//                $ud = $request->user;
//                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
//            }
//            $awsome_query->select(DB::raw('CONCAT(p.first_name," ",p.last_name) as senior'), '_role.rolename as role', 'monthly_tour_program.id as mid', 'location_5.name as bname', DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as user_id', 'person.emp_code', 'person.head_quar', 'dealer.id as dealer_id', 'dealer.name as dealer_name', 'monthly_tour_program.working_date', 'location_view.l1_name', 'location_view.l2_name', 'location_view.l3_name', 'location_view.l4_name as town_name', 'monthly_tour_program.working_status_id', 'monthly_tour_program.pc', 'monthly_tour_program.rd', 'monthly_tour_program.arch', 'monthly_tour_program.collection', 'monthly_tour_program.primary_ord', 'monthly_tour_program.any_other_task', 'monthly_tour_program.new_outlet')
//                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m') = '$month'");
//            $plans = $awsome_query->orderBy('monthly_tour_program.working_date')->groupBy('working_date', 'person_id')->get();
//            $d = [];
//            foreach ($plans as $k => $p) {
//                $d[$k] = DealerLocation::leftJoin('location_view', 'location_view.l5_id', '=', 'dealer_location_rate_list.location_id')
//                    ->where('dealer_id', $p->dealer_id)
//                    ->select('location_view.l5_name')
//                    ->pluck('l5_name')->toArray();
//            }
//            return view('reports.tour-program.ajax', [
//                'plans' => $plans,
//                'work_status' => $work_status,
//                'beat_data' => $d,
//                'month' => $month
//            ]);
//        } else {
//            echo '<p class="alert-danger">No Data Found</p>';
//        }
//    }

#Ajax view page for tour program
    public function tourProgramReport(Request $request)
    {
        if ($request->ajax() ) {

// $user_id = $request->user;
            $company_id = Auth::user()->company_id;
            $month = $request->month;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $role_id=Auth::user()->is_admin;
            $role_id_part=Auth::user()->role_id;
            $status = $request->status;           
            if($role_id==1 || $role_id_part==50)
            {
               $datasenior='';
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                $datasenior_call=self::getJuniorUser($login_user);
                $datasenior = $request->session()->get('juniordata');
                 if(empty($datasenior)){
                     $datasenior[]=$login_user;
                            }
            }

            $work_status = DB::table('_task_of_the_day')->where('company_id',$company_id)->pluck('task', 'id');

            $awsome_query = MonthlyTourProgram::join('person', 'monthly_tour_program.person_id', 'person.id')
            ->join('person_login','person_login.person_id','=','monthly_tour_program.person_id')
            ->join('_role', '_role.role_id', '=', 'person.role_id')
            ->join('location_3', 'location_3.id', '=', 'person.state_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')
            ->join('location_1','location_1.id','=','location_2.location_1_id')

            ->join('location_6', 'location_6.id', '=', 'person.town_id')
            ->join('location_5','location_5.id','=','location_6.location_5_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')
            // ->join('_role', '_role.role_id', '=', 'person.role_id')
            ->join('person as p', 'p.id', '=', 'person.person_id_senior')
            ->where('monthly_tour_program.company_id',$company_id)
                ;
#Region filter
//             if (!empty($request->region)) {
//                 $region = $request->region;
//                 $awsome_query->whereIn('location_view.l2_id', $region);
//             }
// #State filter
//             if (!empty($request->area)) {
//                 $state = $request->area;
//                 $awsome_query->whereIn('location_view.l3_id', $state);
//             }
// #Town filter
            // if (!empty($request->territory)) {
            //     $town = $request->territory;
            //     $awsome_query->whereIn('location_view.l4_id', $town);
            // }
#Beat filter
            // if (!empty($request->belt)) {
            //     $beat = $request->belt;
            //     $awsome_query->whereIn('location_view.l5_id', $beat);
            // }
#Dealer filter
            // if (!empty($request->distributor)) {
            //     $distributor = $request->distributor;
            //     $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
            // }
#Role filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $awsome_query->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $awsome_query->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $awsome_query->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $awsome_query->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $awsome_query->whereIn('person.role_id', $role);
            }
            // if (!empty($request->role)) {
            //     $role_id = $request->role;
            //     $awsome_query->whereIn('person.role_id', $role_id);
            // }
#User Filter
            if (!empty($request->user)) {
                $ud = $request->user;
                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
            }
#Date Filter
            if (!empty($from_date) && !empty($to_date)) {
                $awsome_query->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to_date'");
            }
            ####
               if (!empty($datasenior)) 
            {
                $awsome_query->whereIn('monthly_tour_program.person_id', $datasenior);
            }
            ####Status
               if (!empty($status)) 
            {
                $awsome_query->whereIn('person_login.person_status', $status);
            }

            $awsome_query->select('monthly_tour_program.town','person_login.person_status as status',DB::raw('CONCAT_WS(" ",p.first_name,p.middle_name,p.last_name) as senior'),'person.person_id_senior as senior_id', '_role.rolename as role', 'monthly_tour_program.admin_approved', 'monthly_tour_program.id as mid', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as user_id','person.state_id as person_state', 'person.emp_code', 'person.head_quar', 'monthly_tour_program.working_date', 'location_1.name as l1_name', 'location_2.name as l2_name', 'location_3.name as l3_name','location_6.name as l6_name','location_5.name as l5_name','location_4.name as l4_name',  'monthly_tour_program.working_status_id','monthly_tour_program.dealer_id','locations', 'monthly_tour_program.pc', 'monthly_tour_program.rd', 'monthly_tour_program.arch', 'monthly_tour_program.collection', 'monthly_tour_program.primary_ord', 'monthly_tour_program.any_other_task', 'monthly_tour_program.new_outlet','person.mobile');

// $plans = $awsome_query->orderBy('monthly_tour_program.working_date')->get();
            $plans = $awsome_query->orderBy('monthly_tour_program.working_date','DESC')->groupBy('working_date', 'monthly_tour_program.person_id')->get();
//            dd($plans);
            $d = [];
            // $data = array();
            // foreach ($plans as $k => $p) {
            //     $d[$k] = DealerLocation::leftJoin('location_view', 'location_view.l7_id', '=', 'dealer_location_rate_list.location_id')
            //         ->where('dealer_id', $p->dealer_id)
            //         ->where('dealer_location_rate_list.company_id',$company_id)
            //         ->select('location_view.l7_name as l5_name')
            //         ->pluck('l7_name as l5_name')->toArray();
               
            // }
            $dealer_name_array = DB::table('dealer')
                                ->where('company_id',$company_id)
                                ->pluck('name','id')->toArray();
            // $beat_name_array = DB::table('location_7')
            //                     ->where('status',1)
            //                     ->where('company_id',$company_id)
            //                     ->pluck('name','id');

            $location_6 = DB::table('location_6')->where('company_id',$company_id)->pluck('name','id')->toArray();
            $location_7 = DB::table('location_7')->where('company_id',$company_id)->pluck('name','id')->toArray();
            
            // dd($state);
            return view('reports.tour-program.ajax', [
                'plans' => $plans,
                'work_status' => $work_status,
                // 'beat_data' => $d,
                'month' => $month,
                'dealer_name_array'=>$dealer_name_array,
                // 'beat_name_array'=> $beat_name_array,
                'from_date'=> $from_date,
                'to_date'=> $to_date,
                'location_6'=> $location_6,
                'location_7'=> $location_7,
                'company_id'=> $company_id,
                // 'state' =>$state
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

////// DAILY ATTENDANCE REPORT OLD//
    public function dailyAttendanceReportOld(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $zone = $request->region;
            $region = $request->area;
//            $town = $request->territory;
//            $beat = $request->belt;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = !empty($request->from_date) ? $request->from_date : '';
            $to_date = !empty($request->to_date) ? $request->to_date : date('Y-m-d');

            $data = UserDetail::leftJoin('user_daily_attendance', 'user_daily_attendance.user_id', '=', 'person.id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('check_out', 'check_out.user_id', '=', 'user_daily_attendance.user_id')
                ->leftJoin('check_out', function ($leftjoin) {
                    $leftjoin->on('check_out.user_id', '=', 'user_daily_attendance.user_id');
                    $leftjoin->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"));
                })
                ->leftJoin('_working_status', 'user_daily_attendance.work_status', '=', '_working_status.id')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.user_id', '=', 'user_daily_attendance.user_id')
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'");

            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $data->whereIn('location_view.l3_id', $state);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data->whereIn('user_daily_attendance.user_id', $user);
            }

            $data = $data->select(DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=person.id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id asc limit 0,1) as first_call1"),
                DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=person.id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id desc limit 0,1) as last_call"),
                DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),
                DB::raw("(select location_view.l6_name from monthly_tour_program left join location_view on location_view.l7_id=monthly_tour_program.locations WHERE monthly_tour_program.person_id=user_daily_attendance.user_id AND monthly_tour_program.working_date=DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')) as l"),
                '_role.rolename as role', '_working_status.name as work', 'user_daily_attendance.work_date as checkin', 'person.emp_code', 'location_view.l1_name as zone', 'location_view.l2_name as region', 'person.id',
                'check_out.work_date as checkout', 'check_out.attn_address', 'user_daily_attendance.track_addrs')
//                ->groupBy('user_daily_attendance.id')
                ->groupBy('user_daily_attendance.work_date', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', '_working_status.name', 'check_out.work_date', 'check_out.attn_address', 'user_daily_attendance.track_addrs', 'l', 'person.emp_code', 'location_view.l1_name', 'location_view.l2_name', 'person.id');
            $records = $data->get();
            $ids = $data->pluck('id');

            #for absent users
            $q2 = DB::table('person')
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            if (!empty($ids)) {
                $q2->whereNotIn('id', $ids);
            }
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $q2->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $q2->whereIn('location_view.l3_id', $state);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $q2->whereIn('person.id', $user);
            }
            $q2->select(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),
                '_role.rolename as role', 'person.emp_code', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                'person.id');
            $absent_records = $q2->groupBy('location_view.l3_id', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', 'person.emp_code', 'location_view.l1_name', 'location_view.l2_name', 'person.id')->get();

//            dd($absent_records[0]);

            $t = '';
            if (!empty($town)) {
                $t = Location4::where('id', $town)->pluck('name')->first();
            }
            return view('reports.daily-attendance.ajax', [
                'absentRecords' => $absent_records,
                'records' => $records,
                'town' => $t
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }
//////////////////// END OF OLD DAILY ATTENDANCE  //////////////////////////////
///////////////////// NEW DAILY ATTENDANCE ////////////////////////

    public function dailyAttendanceReport(Request $request)
    {
        //    if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date))
        {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data1 = UserDetail::select('person.id as person_id','person.region_txt as region_txt', 'person.emp_code as emp_code', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_view.l1_name as zone', 'location_view.l2_name as region', '_role.rolename as role')
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $data1->whereIn('location_view.l3_id', $state);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $record = $data1->get();

            // dd($record);
            $records = array();
            $i = 0;

            // print_r($value);
            while (strtotime($from_date) <= strtotime($to_date)) {
                foreach ($record as $key => $value) {
                    //  echo $from_date;
                    $data = DB::table('user_att_sale_view')
                        ->where('user_id', $value->person_id)
                        ->whereRaw("DATE_FORMAT(user_att_sale_view.work_date,'%Y-%m-%d') = '$from_date'")->first();
                    // dd($data);
                    if (!empty($data)) {
                        $records[$i]['person_id'] = $value->person_id;
                        $records[$i]['emp_code'] = $value->emp_code;
                        $records[$i]['uname'] = $value->uname;
                        $records[$i]['region_txt'] = $value->region_txt;
                        
                        $records[$i]['zone'] = $value->zone;
                        $records[$i]['region'] = $value->region;
                        $records[$i]['role'] = $value->role;
                        $records[$i]['track_addrs'] = $data->track_addrs;
                        $records[$i]['date'] = $from_date;
                        $records[$i]['work_date'] = date('d-M-YH:i:s', strtotime($data->work_date));
                        $records[$i]['work'] = $data->work;
                        $records[$i]['check_out_date'] = $data->check_out_date;
                        if (!empty($data->work_date) && !empty($data->check_out_date)) {
                            $c11 = new DateTime($data->work_date);
                            $c21 = new DateTime($data->check_out_date);
                            $interval1 = $c11->diff($c21);
                            $records[$i]['workinghrs'] = $interval1->format('%h') . " Hours " . $interval1->format('%i') . " Minutes";
                        } else {
                            $records[$i]['workinghrs'] = '0';
                        }

                        $records[$i]['first_call'] = $data->first_call;
                        $records[$i]['last_call'] = $data->last_call;
                        if (!empty($data->first_call) && !empty($data->last_call)) {
                            $c1 = new DateTime($data->first_call);
                            $c2 = new DateTime($data->last_call);
                            $interval = $c1->diff($c2);
                            $records[$i]['totalHrs'] = $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
                        } else {
                            $records[$i]['totalHrs'] = '0';
                        }
                        $i++;
                    } else {
                        $records[$i]['person_id'] = $value->person_id;
                        $records[$i]['emp_code'] = $value->emp_code;
                        $records[$i]['uname'] = $value->uname;
                        $records[$i]['region_txt'] = $value->region_txt;

                        $records[$i]['zone'] = $value->zone;
                        $records[$i]['region'] = $value->region;
                        $records[$i]['role'] = $value->role;
                        $records[$i]['track_addrs'] = "N/A";
                        $records[$i]['date'] = $from_date;
                        $records[$i]['work_date'] = '0';
                        $records[$i]['work'] = "N/A";
                        $records[$i]['workinghrs'] = '0';
                        $records[$i]['check_out_date'] = "";
                        $records[$i]['first_call'] = "N/A";
                        $records[$i]['last_call'] = "N/A";
                        $records[$i]['totalHrs'] = '0';
                        $i++;
                    }
                }
                $from_date = date("Y-m-d", strtotime("+1 days", strtotime($from_date)));


            }
            echo $i;
            return view('reports.daily-attendance.ajax', [
                // 'absentRecords' => $absent_records,
                'records' => $records
                // 'town' => $t
            ]);

        }
        // else 
        // {
        //     echo '<p class="alert-danger">No Data Found</p>';
        // }
    }

    public function getBrands(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = competitorBrand::where('competitor_id', $request->id)->where('status', 1)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function competitorsNewProductReport(Request $request)
    {
        if ($request->ajax() && !empty($request->user)) {

            $user = $request->user;

            $query = DB::table('competitors_launched_product')
                ->where('user_id', $user)
                ->get();

            return view('reports.competitors-new-product.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function TravellingExpensesReport(Request $request)
    {
        if ($request->ajax()) {
            $zone = $request->zone; 
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $company_id = Auth::user()->company_id;
            $login_role = Auth::user()->role_id;

            // dd($company_id);


            $check_junior_permission = DB::table('company_web_module_permission')->where('company_id',$company_id)->where('module_id',33)->where('without_junior',1)->where('role_id',$login_role)->count();

            // dd($check_junior_permission);



            if($check_junior_permission == 1){
                $role_id=Auth::user()->is_admin;
                if($role_id==1 || $role_id==50)
                {
                   $datasenior='';
                }else
                { 
                    
                    Session::forget('juniordata');
                    $login_user=Auth::user()->id;
                     
                    $datasenior_call=self::getJuniorUser($login_user);
                    $datasenior = $request->session()->get('juniordata');
                     if(empty($datasenior)){
                         $datasenior[]=$login_user;
                                }
                }
              }

              // dd($datasenior);

            // $arr = [1 => 'Bus', 2 => 'Train', 3 => 'Motorcycle', 4 => 'Taxi', 5 => 'Flight', 6 => 'Metro'];

            $arr = DB::table('_travelling_mode')->where('company_id',$company_id)->pluck('mode','id');


            $comp = DB::table('company')->where('id','=',$company_id)->first();

            $role = DB::table('_role')->where('company_id',$company_id)->pluck('rolename','role_id');
// dd($comp->title);


            if($company_id == 71){
                $location_6_data = DB::table('location_4_townexp')
                ->where('location_4_townexp.company_id',$company_id)
                ->pluck('location_4_townexp.name','location_4_townexp.id');
            }
            else{
                $location_6_data = DB::table('location_6')
                // ->join(' as d', 'd.id', '=', 'travelling_expense_bill.departureID')
                ->where('location_6.company_id',$company_id)
                // ->where('person.company_id',$company_id)
                ->pluck('location_6.name','location_6.id');

            }
            
            // $arrival_town = DB::table('travelling_expense_bill')
            //     ->join('location_6 as d', 'd.id', '=', 'travelling_expense_bill.departureID')
            //     ->where('company_id.company_id',$company_id)
            //     ->where('person.company_id',$company_id)
            //     ->pluck('location_6.name','location_6.id')
                // ->leftJoin('location_6 as a', 'a.id', '=', 'travelling_expense_bill.arrivalID')

            $query = DB::table('travelling_expense_bill')
                ->join('person', 'person.id', '=', 'travelling_expense_bill.user_id')
                ->join('location_3', 'location_3.id', '=', 'person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_1','location_1.id','=','location_2.location_1_id')

                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                // ->leftJoin('location_6 as d', 'd.id', '=', 'travelling_expense_bill.departureID')
                // ->leftJoin('location_6 as a', 'a.id', '=', 'travelling_expense_bill.arrivalID')
                ->where('travelling_expense_bill.company_id',$company_id);

            if (!empty($user_id)) {
                $query->whereIn('user_id', $user_id);
            }
               if (!empty($datasenior)) 
            {
                $query->whereIn('user_id', $datasenior);
            }
             if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $query->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $query->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $query->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $query->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $query->whereIn('person.role_id', $role);
            }
            if (!empty($from_date) && !empty($to_date)) {
                $query->whereRaw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') <='$to_date'");
            }

            // if($company_id == 43){
            //     $absents_array = array('90','93','95','96');

            //     $query->join('user_daily_attendance','user_daily_attendance.user_id','=','travelling_expense_bill.user_id')
            //             ->whereNotIn('work_status',$absents_array);
            // }
            if($company_id == 43){
                 $query_data = $query->select(DB::raw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') as travel_date"),'departureID', 'arrivalID', 'travelling_expense_bill.*', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'travelling_expense_bill.remarks','person.emp_code','person.role_id','date_time','location_2.name as l2_name','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.mobile','person.person_id_senior','travelling_expense_bill.stationary')
                ->groupBy('travelling_expense_bill.travellingDate','travelling_expense_bill.user_id')
                ->orderBy('date_time', 'DESC')
                ->get();

            }else{
            $query_data = $query->select(DB::raw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') as travel_date"),'departureID', 'arrivalID', 'travelling_expense_bill.*', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'travelling_expense_bill.remarks','person.emp_code','person.role_id','date_time','location_2.name as l2_name','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.mobile','person.person_id_senior','travelling_expense_bill.stationary')
                ->orderBy('date_time', 'DESC')
                ->get();
            }

            $attendance_data = DB::table('user_daily_attendance')
                                ->where('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
                                ->pluck('work_status',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"))->toArray();
            // dd($query_data);
            // $dealer_secondary_visit = DB::table('user_sales_order')
            //             ->groupBy('dealer_id')
            //             ->where('company_id',$company_id)
            //             ->pluck(DB::raw("COUNT(dealer_id)"),DB::raw("CONCAT(user_id,date)"))->toArray();

            $dealer_primary_visit = DB::table('user_primary_sales_order')
                        ->groupBy('created_person_id','sale_date')
                        ->where('company_id',$company_id)
                        ->pluck(DB::raw("COUNT(dealer_id)"),DB::raw("CONCAT(created_person_id,sale_date)"))->toArray();
            
            // $dealer_visit_payment = DB::table('dealer_payments')
            //                     ->groupBy('user_id','payment_recevied_date')
            //                     ->where('company_id',$company_id)
            //                     ->pluck(DB::raw("COUNT(dealer_id)"),DB::raw("CONCAT(user_id,DATE_FORMAT(payment_recevied_date,'%Y-%m-%d'))"))->toArray();
            
            // $dealer_visit_stock = DB::table('dealer_balance_stock')
            //                     ->groupBy('user_id','submit_date_time')
            //                     ->where('company_id',$company_id)
            //                     ->pluck(DB::raw("COUNT(dealer_id)"),DB::raw("CONCAT(user_id,DATE_FORMAT(submit_date_time,'%Y-%m-%d'))"))->toArray();
            

            $out = [];
            foreach($query_data as $t_key => $t_value)
            {
                $user_id = $t_value->user_id;
                $travel_date = $t_value->travel_date;

                $out[$user_id.$travel_date]['primary_qty'] = DB::table('user_primary_sales_order')
                                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                    ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                                    ->where('created_person_id',$t_value->user_id)
                                    ->where('user_primary_sales_order.company_id',$company_id)
                                    ->where('sale_date',$t_value->travel_date)
                                    ->sum(DB::raw("(pcs+(cases*quantity_per_case))"));

                $out[$user_id.$travel_date]['primary_sale'] = DB::table('user_primary_sales_order')
                                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                    ->select(DB::raw("SUM(pcs*rate) as primary_sale"))
                                    ->where('created_person_id',$t_value->user_id)
                                    ->where('user_primary_sales_order.company_id',$company_id)
                                    ->where('sale_date',$t_value->travel_date)
                                    ->first();

                $out[$user_id.$travel_date]['no_of_order'] = DB::table('user_primary_sales_order')
                                    ->where('created_person_id',$t_value->user_id)
                                    ->where('sale_date',$t_value->travel_date)
                                    ->where('company_id',$company_id)
                                    ->distinct('order_id')->COUNT('order_id');

                // $out[$user_id.$travel_date]['dealer_visit_sale'] = !empty($dealer_primary_visit[$user_id.$travel_date])?$dealer_primary_visit[$user_id.$travel_date]:'0';
                // $out[$user_id.$travel_date]['dealer_visit_payment'] = !empty($dealer_visit_payment[$user_id.$travel_date])?$dealer_visit_payment[$user_id.$travel_date]:'0';
                // $out[$user_id.$travel_date]['dealer_visit_stock'] = !empty($dealer_visit_stock[$user_id.$travel_date])?$dealer_visit_stock[$user_id.$travel_date]:'0';

                // $out[$user_id.$travel_date]['dealer_visit'] = $out[$user_id.$travel_date]['dealer_visit_sale'] + $out[$user_id.$travel_date]['dealer_visit_payment']+$out[$user_id.$travel_date]['dealer_visit_stock'];

                $out[$user_id.$travel_date]['secondary_qty'] = DB::table('user_sales_order')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                    ->where('user_id',$t_value->user_id)
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->where('date',$t_value->travel_date)
                                    ->sum('quantity');

                $out[$user_id.$travel_date]['secondary_sale'] = DB::table('user_sales_order')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                    ->select(DB::raw("SUM(rate*quantity) as secondary_sale"))
                                    ->where('user_id',$user_id)
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->where('user_sales_order_details.company_id',$company_id)
                                    // ->where('date',$travel_date)
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$travel_date'")
                                    ->where('call_status',1)
                                    // ->groupBy('user_id')
                                    ->first();

                $out[$user_id.$travel_date]['no_of_order_secondary'] = DB::table('user_sales_order')
                                    ->where('user_id',$t_value->user_id)
                                    ->where('date',$t_value->travel_date)
                                    ->where('company_id',$company_id)
                                    ->distinct('order_id')->COUNT('order_id');
                
                $out[$user_id.$travel_date]['dealer_visit'] = $out[$user_id.$travel_date]['no_of_order_secondary']+$out[$user_id.$travel_date]['no_of_order'];
                
                
            }
            // dd($out);
            if($company_id == 33){
            return view('reports.travelling-expenses.rajvaidyaajax', [
                'records' => $query_data,
                'comp' => $comp,
                'arr' => $arr,
                'location_6_data'=> $location_6_data,
                'out'=>$out,
                'role'=>$role,
            ]);

            }
            elseif($company_id == 43){
                // dd($company_id);
                 return view('reports.travelling-expenses.btwajax', [
                'records' => $query_data,
                'comp' => $comp,
                'arr' => $arr,
                'location_6_data'=> $location_6_data,
                'out'=>$out,
                'role'=>$role,
                'attendance_data'=>$attendance_data,
                'company_id'=>$company_id,
            ]);
            }


            else{
            return view('reports.travelling-expenses.ajax', [
                'records' => $query_data,
                'comp' => $comp,
                'arr' => $arr,
                'location_6_data'=> $location_6_data,
                'out'=>$out,
                'role'=>$role,
                'attendance_data'=>$attendance_data,
                'company_id'=>$company_id,
            ]);
            }
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function pendingClaimReport(Request $request)
    {
        if ($request->ajax() && !empty($request->user)) {
            $user = $request->user;

            $query = Claim::leftJoin('location_4', 'location_4.id', '=', 'pending_claim.town_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'pending_claim.distributor_id')
                ->where('user_id', $user)
//                ->groupBy('location_view.l4_id','nature_of_claim','invoice_number','claim_paper','remark','expected_resolution_date')
                ->select('location_4.name as town', 'pending_claim.nature_of_claim', 'pending_claim.invoice_number', 'pending_claim.claim_paper', 'pending_claim.remark', 'pending_claim.expected_resolution_date', 'dealer.name as dealer_name', 'pending_claim.submission_date','dealer.id as did')
                ->get();
            return view('reports.pending-claim.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function getDealer(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
//            $data['result'] = Dealer::where('location_3_id', $request->id)->pluck('name', 'id');
            $data['result'] = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_location_rate_list.dealer_id')
                ->where('l3_id', $request->id)
                ->where('dealer.id', '!=', '')
                ->where('l7_company_id',$company_id)
                ->groupBy('dealer_location_rate_list.dealer_id')
                ->pluck('dealer.name', 'dealer.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    /**
     * @function :agingReport
     * @desc: Will provide data from challan_order table with duration 30,31-45,46-60,61> days balances
     */
    public function agingReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region)) {
            $company_id = Auth::user()->company_id;
            $region = $request->region;

             $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

//            $query = Dealer::leftJoin('challan_orders', 'challan_orders.ch_dealer_id', '=', 'dealers.id')
//                ->leftJoin('location_3', 'location_3.id', '=', 'dealers.location_3_id')
//                ->leftJoin('dealer_locations', 'dealer_locations.dealer_id', '=', 'dealers.id')
//                ->where('dealers.location_3_id', $region);
//            if (!empty($request->distributor)) {
//                $query->where('dealers.id', $request->distributor);
//            }
//            $query->groupBy('dealers.id')
//                ->select(DB::raw('SUM(challan_orders.remaining) as total_remaining'), 'dealers.name', 'dealers.dealer_code',
//                    'location_3.name as state', 'dealers.name as dealer', 'dealer_locations.town');
//            $data = $query->get();

            $query = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_location_rate_list.dealer_id')
                ->leftJoin('challan_order', 'challan_order.ch_dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats', 'dealer_pay_stats.dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats_45', 'dealer_pay_stats_45.dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats_60', 'dealer_pay_stats_60.dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats_61', 'dealer_pay_stats_61.dealer_id', '=', 'dealer.id')
                ->where('l3_id', $region)
                ->where('dealer.id', '!=', '')
                ->where('dealer.company_id',$company_id);

            if (!empty($datasenior)) 
            {
                $query->whereIn('dealer_location_rate_list.user_id', $datasenior);
            }

            if (!empty($request->distributor)) {
                $query->where('dealer.id', $request->distributor);
            }
            $query->groupBy('dealer_location_rate_list.dealer_id', 'location_view.l3_name', 'location_view.l6_name');
            $data = $query->select('dealer.id', 'dealer.dealer_code', 'dealer.name', 'location_view.l3_name as state', 'location_view.l6_name as town'
                , DB::raw("MAX(ch_date) as last_invoice"), 'dealer_pay_stats.amount_sum as amount1',
                'dealer_pay_stats_45.amount_sum as amount2', 'dealer_pay_stats_60.amount_sum as amount3',
                'dealer_pay_stats_61.amount_sum as amount4', DB::raw('SUM(challan_order.remaining) as total_remaining'))
                ->get();
//            dd($data);

            return view('reports.aging.ajax', [
                'records' => $data
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function distributorStockStatusReport(Request $request)
    {
        if ($request->ajax() && !empty($request->distributor) && !empty($request->month)) {
            $month = date('Y-m-d', strtotime($request->month));

//            echo $month;die;
            $distributor = $request->distributor;

            $arr = [];

            $query = DB::table('catalog_1')->join('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
                ->join('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
//                ->leftJoin('daily_stock', 'daily_stock.product_id', '=', 'catalog_product.id')
                ->select(DB::raw("(select dealer_balance_stock.stock_qty from dealer_balance_stock WHERE dealer_id=$distributor AND DATE_FORMAT(submit_date_time,'%Y%m') =DATE_FORMAT(('$month' - INTERVAL 1 MONTH),'%Y%m') AND dealer_balance_stock.product_id=catalog_product.id ORDER BY id DESC LIMIT 0,1) as opening_stock"),
                    DB::raw("(select sum(user_primary_sales_order_details.quantity) from user_primary_sales_order left join user_primary_sales_order_details on user_primary_sales_order_details.order_id=user_primary_sales_order.order_id WHERE user_primary_sales_order.dealer_id=$distributor AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y%m') =DATE_FORMAT('$month','%Y%m') AND user_primary_sales_order_details.product_id=catalog_product.id LIMIT 0,1) as primary_sale"),
                    DB::raw("(select sum(user_sales_order_details.quantity) from user_sales_order left join user_sales_order_details on user_sales_order_details.order_id=user_sales_order.order_id WHERE user_sales_order.dealer_id=$distributor AND DATE_FORMAT(user_sales_order.date,'%Y%m') =DATE_FORMAT('$month','%Y%m') AND user_sales_order_details.product_id=catalog_product.id LIMIT 0,1) as ss"),
                    'catalog_1.name as c1', 'catalog_2.name as c2', 'catalog_product.name as product',
                    'catalog_product.id as cpid', 'catalog_product.base_price')
                ->get();
                // dd($query);
            foreach ($query as $k => $data) {
                $arr[$k]['cpid'] = $data->cpid;
                $arr[$k]['c1'] = $data->c1;
                $arr[$k]['c2'] = $data->c2;
                $arr[$k]['product'] = $data->product;
                $arr[$k]['pr_rate'] = $data->base_price;
                $arr[$k]['opening'] = !empty($data->opening_stock) ? $data->opening_stock : 0;
                $arr[$k]['primary_sale'] = !empty($data->primary_sale) ? $data->primary_sale : 0;
                $arr[$k]['seconday_sale'] = !empty($data->ss) ? $data->ss : 0;
            }

            return view('reports.distributor-stock-status.ajax', [
                'records' => $arr
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function stockInHandReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->month)) {
            $month = date('Y-m-d', strtotime($request->month));

            $m1 = strtotime('-1 month', strtotime($request->month));
            $m2 = strtotime('-2 month', strtotime($request->month));
            $m3 = strtotime('-3 month', strtotime($request->month));

            $mn1 = date('M-Y', $m1);
            $mn2 = date('M-Y', $m2);
            $mn3 = date('M-Y', $m3);

            $mf1 = date('Y-m', $m1);
            $mf2 = date('Y-m', $m2);
            $mf3 = date('Y-m', $m3);

            $region = $request->region;
            $area = $request->area;
            $territory = $request->territory;
            $belt = $request->belt;


            if (!empty($belt)) {
                $subq = 'l5_id IN(' . implode(',', $belt) . ')';
            } elseif (!empty($territory)) {
                $subq = 'l4_id IN(' . implode(',', $territory) . ')';
            } elseif (!empty($area)) {
                $subq = 'l3_id IN(' . implode(',', $area) . ')';
            } elseif (!empty($area)) {
                $subq = 'l2_id IN(' . implode(',', $region) . ')';
            } else {
                $subq = '1=1';
            }

            $query = DB::table('catalog_0')
                ->join('catalog_1', 'catalog_1.catalog_0_id', '=', 'catalog_0.id')
                ->join('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
                ->join('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
                ->select('catalog_0.name as catalog_name', 'catalog_product.name as sku', 'catalog_product.id')
                ->where('catalog_0.company_id',$company_id)
                ->where('catalog_product.company_id',$company_id)
                ->where('catalog_0.status',1)
                ->where('catalog_1.status',1)
                ->where('catalog_2.status',1)
                ->where('catalog_product.status',1)
                ->get();

            $cal = [];
            foreach ($query as $k => $data) {
                $cal[$k] = DB::table('secondary_sale as ss')
                    ->select(DB::raw("(select sum(rate*quantity) from secondary_sale WHERE product_id='$data->id' and DATE_FORMAT(date,'%Y-%m')='$mf3' and $subq) as m3"),
                        DB::raw("(select sum(rate*quantity) from secondary_sale WHERE product_id='$data->id' and company_id='$company_id' and DATE_FORMAT(date,'%Y-%m')='$mf2' and $subq) as m2"),
                        DB::raw("(select sum(rate*quantity) from secondary_sale WHERE product_id='$data->id' and company_id='$company_id' and DATE_FORMAT(date,'%Y-%m')='$mf1' and $subq) as m1"))
                    ->where('ss.company_id',$company_id)
                    ->first();
//                if ($data->id=='146')
//                {
//                    dd($cal[$k]);
//                }
            }
//            dd($cal);

//            $query = [];
            return view('reports.stock-in-hand.ajax', [
                'records' => $query,
                'm1' => $mn1,
                'm2' => $mn2,
                'm3' => $mn3,
                'cal' => $cal
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function getTownDistributor(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = DealerLocation::leftJoin('dealers', 'dealers.id', '=', 'dealer_locations.dealer_id')
                ->where('town', $request->id)->select('dealers.id', 'dealers.name')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function primarySecondarySales(Request $request)
    {
        if ($request->ajax() && !empty($request->distributor) && !empty($request->from_date) && !empty($request->to_date)) {
            $catalog=DB::table('catalog_0')->orderBy('sequence')->get();
            $query = DB::table('catalog_0')
                ->leftJoin('catalog_1', 'catalog_1.catalog_0_id', '=', 'catalog_0.id')
                ->leftJoin('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
                ->leftJoin('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
                ->select('catalog_0.name as cat_name','catalog_0.id as catid', 'catalog_product.name as sku', 'catalog_product.id','catalog_product.base_price')
                ->get();
            $dataArr=[];
            foreach ($query as $cat)
            {
                $dataArr[$cat->catid][]=$cat;
            }
                return view('reports.month-s-primary-and-secondary-sales-plan.ajax', [
                'catalog' => $catalog,
                'rows' => $dataArr
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function ucdpReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region) && ($request->area) && ($request->territory) && !empty($request->month)) {
            $query = [];
            return view('reports.ucdp.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function boardReviewReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region) && ($request->area) && ($request->territory) && !empty($request->month)) {
            $query = [];
            return view('reports.board_review.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function rwssr(Request $request)
    {
        if ($request->ajax() && !empty($request->region) && ($request->area) && ($request->territory) && !empty($request->belt)) {
            $query = [];
            return view('reports.rs-wise-secondary-sales.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function isrSoTgtMonthReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $last_year = date('Y') - 1;
            $last_year_same_month = $last_year . '-' . date('m');
            $last_month_from_filter = date('M y', strtotime($to_date));
            $first_month_from_filter = date('M y', strtotime($from_date));

            $users = $request->user;

            $isr_so_role = [];
            $isr_so_role = DB::table('_role')
                ->where('filter', 1)
                ->pluck('role_id');

            $user = DB::table('person')
                ->leftJoin('monthly_tour_program', 'monthly_tour_program.person_id', '=', 'person.id')
                ->leftJoin('position_master', 'person.position_id', '=', 'position_master.id')
                ->leftJoin('location_3', 'location_3.id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #user wise filter
            if (!empty($users)) {
                $user->whereIn('person.id', $users);
            }
            #role wise filter
            if (!empty($isr_so_role)) {
                $user->whereIn('person.role_id', $isr_so_role);
            }

            $user_data = $user->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') <='$to_date'")
                ->select('person.id as pid', '_role.rolename', 'location_3.name as state', 'position_master.name as position_name', 'person.first_name', 'person.middle_name', 'person.last_name', 'person.position_id', DB::raw('SUM(monthly_tour_program.rd) as total_rd'), DB::raw('SUM(monthly_tour_program.arch) as total_arch'),
                    DB::raw("(select sum(arch) from monthly_tour_program WHERE DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m')=$last_year_same_month) as lysm"),
                    DB::raw('SUM(monthly_tour_program.collection) as total_collection'), DB::raw('SUM(monthly_tour_program.primary_ord) as total_primary_ord'))
                ->groupBy('monthly_tour_program.person_id', 'person.first_name', 'person.middle_name', 'person.last_name')
                ->get();

            $last_month_user = DB::table('person')
                ->leftJoin('monthly_tour_program', 'monthly_tour_program.person_id', '=', 'person.id')
                ->leftJoin('position_master', 'person.position_id', '=', 'position_master.id')
                ->leftJoin('location_3', 'location_3.id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #user wise filter
            if (!empty($users)) {
                $user->whereIn('person.id', $users);
            }
            #role wise filter
            if (!empty($isr_so_role)) {
                $last_month_user->whereIn('person.role_id', $isr_so_role);
            }
            $last_month_user_data = $last_month_user->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') ='$to_date'")
                ->select('person.id as pid', DB::raw('SUM(monthly_tour_program.rd) as total_rd'), DB::raw('SUM(monthly_tour_program.arch) as total_arch'),
                    DB::raw("(select sum(arch) from monthly_tour_program WHERE DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m')=$last_year_same_month) as lysm"),
                    DB::raw('SUM(monthly_tour_program.collection) as total_collection'), DB::raw('SUM(monthly_tour_program.primary_ord) as total_primary_ord'))
                ->groupBy('monthly_tour_program.person_id', 'person.first_name', 'person.middle_name', 'person.last_name')
                ->get();
            $arr = [];
            $myArr = [];
//            dd($last_month_user_data);
            if (!empty($last_month_user_data)) {
                foreach ($last_month_user_data as $lmu) {
                    $arr[$lmu->pid] = $lmu;
                    $myArr = $arr;
                }
            }

            return view('reports.isr-so-tgt-month.ajax', [
                'records' => $user_data,
                'last_month_from_filter' => $last_month_from_filter,
                'first_month_from_filter' => $first_month_from_filter,
                'last_month_user' => $myArr
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function salesTrendsReport(Request $request)
    {
        if ($request->ajax() && !empty($request->year)) {
            $monthArr = array('APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEPT', 'OCT', 'NOV', 'DEC', 'JAN', 'FEB', 'MAR');
            $query = [];
            return view('reports.sales-trends.ajax', [
                'records' => $query,
                'monthArr' => $monthArr
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function outletOpeningStatusReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region)) {
            $query = [];
            return view('reports.outlet-opening-status.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function salesReviewReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region)) {
            $query = [];
            return view('reports.sales-review.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    function getList($parentId)
    {
        $retVal = array($parentId);

        $query = DB::table('position_master')->where('senior_position', $parentId)->pluck('id');
        foreach ($query as $key => $data) {
            $retVal = array_merge($retVal, $this->getList($data));
        }

        return $retVal;
    }

    public function competitivePriceIntelligenceReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $position = $request->position;
            $temp = [];
            $l_ids = [];

            #location based user
            if (!empty($zone) || !empty($region) || !empty($state)) {
                $location = DB::table('location_view');
                if (!empty($zone)) {
                    $location->whereIn('l1_id', $zone);
                }
                if (!empty($region)) {
                    $location->whereIn('l2_id', $region);
                }
                if (!empty($state)) {
                    $location->whereIn('l3_id', $state);
                }
                $loc_ids = $location->pluck('l3_id');

                if (!empty($loc_ids)) {
                    $l_ids = DB::table('person')->whereIn('state_id', $loc_ids)->pluck('id');
                }
            }

            #Position cal
            // if (!empty($position)) {
            //     foreach ($position as $pd) {
            //         $temp = $this->getList($pd);
            //     }
            // }
            // $junior = !empty($temp) ? array_unique($temp) : array();

            //     $user_ids = [];
            //     if (!empty($junior)) {
            //         $user_ids = DB::table('person')->whereIn('position_id', $junior)->pluck('id')->to_array();
            //      $unique_ids = !empty($user_ids) ? array_unique($user_ids) : '';
            //   // $unique_ids = !empty($Arr) ? array_unique($Arr) : '';
            //     }

            $query = DB::table('competitive_price_intelligence');
            if (!empty($unique_ids)) {
                $query->whereIn('user_id', $unique_ids);
            }
            if (!empty($request->user)) {
                $u = $request->user;
                $query->whereIn('user_id', $u);
            }
            if (!empty($l_ids)) {
                $u = $request->user;
                $query->whereIn('user_id', $l_ids);
            }

            $query_data = $query->whereRaw("DATE_FORMAT(competitive_price_intelligence.cur_date_time,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(competitive_price_intelligence.cur_date_time,'%Y-%m-%d') <='$to_date'")
                ->orderBy('cur_date_time', 'DESC')
                ->get();
            return view('reports.competitive-price-intelligence.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

    public function getZoneUser(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $location = DB::table('location_view');
            if ($request->id != 'null') {
                $ids = explode(',', $request->id);
                $location->whereIn('l1_id', $ids);
            }
            $data['region'] = $location->pluck('l2_name', 'l2_id');
            $data['state'] = $location->pluck('l3_name', 'l3_id');

            $state_ids = $location->pluck('l3_id');

            if (!empty($state_ids)) {
                $data['user'] = DB::table('person')
                    ->whereIn('state_id', $state_ids)
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function getRegionUser(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $location = DB::table('location_view');
            if ($request->id != 'null') {
                $ids = explode(',', $request->id);
                $location->whereIn('l2_id', $ids);
            }
            $data['region'] = $location->pluck('l2_name', 'l2_id');
            $data['state'] = $location->pluck('l3_name', 'l3_id');

            $state_ids = $location->pluck('l3_id');

            if ($request->id != 'null') {
                $data['user'] = DB::table('person')
                    ->whereIn('state_id', $state_ids)
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function getStateUser(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;

            if (!empty($request->id != 'null')) {
                $state_ids = explode(',', $request->id);
            }

            if (!empty($state_ids)) {
                $data['user'] = DB::table('person')
                    ->whereIn('state_id', $state_ids)
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    // ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,,person.last_name) as name'), 'person.id as uid')
                    // ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #get state wise isr and so
    public function getStateIsrsSo(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $state_id = $request->id;
            $isr_so_role = DB::table('_role')
                ->where('filter', 1)
                ->pluck('role_id');
            if ($request->id != 'null') {
                $ids = explode(',', $state_id);
                $data['user'] = DB::table('person')
                    ->whereIn('role_id', $isr_so_role)
                    ->whereIn('state_id', $ids)
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $isr_so_role = [];
                $isr_so_role = DB::table('_role')
                    ->where('filter', 1)
                    ->pluck('role_id');
                $data['user'] = DB::table('person')
                    ->whereIn('role_id', $isr_so_role)
                    ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function feedbackReport(Request $request)
    {
        if ($request->ajax() && !empty($request->user)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;


            $query = DB::table('feedbackSuggestion');

            $query_data = $query->orderBy('cur_date_time', 'DESC')
                ->where('user_id', $user_id)
                ->get();

            return view('reports.feedback.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }


    }



public function dailyPerformanceReport(Request $request)
{
    if ($request->ajax() && !empty($request->date_range_picker)) 
    {
        $explodeDate = explode(" -", $request->date_range_picker);
        $from = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to = date('Y-m-d',strtotime(trim($explodeDate[1])));

 
        $status = $request->status;
        $state = $request->area;
        $region = $request->region;
        $company_id = Auth::user()->company_id;
        $user = $request->user;
        $query = [];
        $new_arr =[];
        $checkoutarr =[];
        $otherArr =[];
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

         $role_id=Auth::user()->is_admin;           
            if($role_id==1 )
            {
               $datasenior='';
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                $datasenior_call=self::getJuniorUser($login_user);
                $datasenior = $request->session()->get('juniordata');
                 if(empty($datasenior))
                 {
                     $datasenior[]=$login_user;
                  }
            }
        #Working Status master data
        $working_status = DB::table('_working_status')
            ->where('company_id',$company_id)
            ->where('status',1)
            ->pluck('name', 'id');

        #Catalog2 master data
        $catalog = DB::table('catalog_2')
            ->where('company_id',$company_id)
           ->where('status',1)
            ->pluck('catalog_2.name', 'catalog_2.id');


        if(empty($check)){    
                $temp_rv_data = DB::table('secondary_sale')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
                    ->where('company_id',$company_id)
                    ->groupBy('date','user_id');
                
                if(!empty($state))
                {
                    $temp_rv_data->whereIn('l3_id',$request->area);
                }

                $temp_rv = $temp_rv_data->pluck(DB::raw("SUM(quantity*rate) as total_price"),DB::raw("CONCAT(user_id,date)"));
        }else{
                    $temp_rv_data = DB::table('secondary_sale')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
                    ->where('company_id',$company_id)
                    ->groupBy('date','user_id');
                
                if(!empty($state))
                {
                    $temp_rv_data->whereIn('l3_id',$request->area);
                }

                $temp_rv = $temp_rv_data->pluck(DB::raw("SUM(final_secondary_qty*final_secondary_rate) as total_price"),DB::raw("CONCAT(user_id,date)"));
        }

        if(empty($check)){
                $temp_kg_data = DB::table('secondary_sale')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
                    ->where('company_id',$company_id)
                    ->groupBy('date','user_id');

                if(!empty($state))
                {
                    $temp_kg_data->whereIn('l3_id',$request->area);
                }
                $temp_kg = $temp_kg_data->pluck(DB::raw("SUM(quantity*weight) as total_weight"),DB::raw("CONCAT(user_id,date)"));
        }else{
                    $temp_kg_data = DB::table('secondary_sale')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
                    ->where('company_id',$company_id)
                    ->groupBy('date','user_id');

                if(!empty($state))
                {
                    $temp_kg_data->whereIn('l3_id',$request->area);
                }
                $temp_kg = $temp_kg_data->pluck(DB::raw("SUM(final_secondary_qty*weight) as total_weight"),DB::raw("CONCAT(user_id,date)"));
        }

 

        $time_of_first_call_data=DB::table('secondary_sale')
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
        ->where('company_id',$company_id)
        ->groupBy('user_id','date')
        ->orderBy('time','ASC');
        if(!empty($state))
        {
            $time_of_first_call_data->whereIn('l3_id',$request->area);
        }
        $time_of_first_call = $time_of_first_call_data->pluck('time',DB::raw("CONCAT(user_id,date)"));

        $time_of_last_call_data =DB::table('secondary_sale')
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
        ->where('company_id',$company_id)
        ->groupBy('user_id','date');
        if(!empty($state))
        {
            $time_of_last_call_data->whereIn('l3_id',$request->area);
        }
        $time_of_last_call = $time_of_last_call_data->pluck(DB::raw("MAX(time)"),DB::raw("CONCAT(user_id,date)"));
       
        // dd($time_of_first_call);

        $checkout_data=DB::table('check_out')
        ->join('person','person.id','=','check_out.user_id')
        ->where('check_out.company_id',$company_id)
        ->where('person.company_id',$company_id)
        ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to'")
        ->groupBy('work_date','user_id');

        if(!empty($state))
        {
            $checkout_data->whereIn('state_id',$request->area);
        }
        $checkout = $checkout_data->select('work_date',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as concat"),'total_call as tc','total_pc as tpc','total_sale_value as tsv','remarks')->get();

        foreach ($checkout as $checkout_data => $checkout_value) 
        {
        $concat = $checkout_value->concat;
        $checkoutarr[$concat]['work_date'] = $checkout_value->work_date;
        $checkoutarr[$concat]['tc'] = $checkout_value->tc;
        $checkoutarr[$concat]['tpc'] = $checkout_value->tpc;
        $checkoutarr[$concat]['tsv'] = $checkout_value->tsv;
        $checkoutarr[$concat]['checkOutRemarks'] = $checkout_value->remarks;
        }
    
       if(empty($check)){             
       $new_arr_data_data = DB::table('secondary_sale')
            ->where('secondary_sale.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
            ->select('date','user_id','c2_id as c0_id',DB::raw("SUM(quantity*rate) as total_price"), DB::raw("SUM(quantity*weight) as total_weight"), DB::raw("COUNT(Distinct order_id) as total_row"))
            ->groupBy('c2_id','user_id','date');
            if(!empty($state))
            {
                $new_arr_data_data->whereIn('l3_id',$request->area);
            }
            $new_arr_data = $new_arr_data_data->get();
        }else{
        $new_arr_data_data = DB::table('secondary_sale')
            ->where('secondary_sale.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
            ->select('date','user_id','c2_id as c0_id',DB::raw("SUM(final_secondary_qty*final_secondary_rate) as total_price"), DB::raw("SUM(quantity*weight) as total_weight"), DB::raw("COUNT(Distinct order_id) as total_row"))
            ->groupBy('c2_id','user_id','date');
            if(!empty($state))
            {
                $new_arr_data_data->whereIn('l3_id',$request->area);
            }
            $new_arr_data = $new_arr_data_data->get();
        }
            
        foreach ($new_arr_data as $product_data => $product_value) 
        {
            $c0_id = $product_value->c0_id;
            $date = $product_value->date;
            $user_id = $product_value->user_id;
            $new_arr[$user_id.$date][$c0_id]['total_price'] = $product_value->total_price;
            $new_arr[$user_id.$date][$c0_id]['total_weight'] = $product_value->total_weight;
            $new_arr[$user_id.$date][$c0_id]['total_row'] = $product_value->total_row;
            
        }
        
       $visit_count_data = DB::table('user_sales_order')->join('location_view','location_view.l7_id','=','user_sales_order.location_id')->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to'")
       ->where('user_sales_order.company_id',$company_id)
       ->groupBy('date','user_id');
       if(!empty($state))
        {
            $visit_count_data->whereIn('l3_id',$request->area);
        }
        if (!empty($region)) 
        {
            
            $visit_count_data->whereIn('location_view.l2_id', $region);
        }
        if (!empty($user)) 
        {
            
            $visit_count_data->whereIn('user_id', $user);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $visit_count_data->whereIn('l3_id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $visit_count_data->whereIn('l4_id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $visit_count_data->whereIn('l5_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $visit_count_data->whereIn('l6_id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $visit_count_data->whereIn('dealer_id', $dealer);
        }


       $visit_count = $visit_count_data->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as count"),DB::raw("CONCAT(user_id,date)"));


       $productive_calls = DB::table('user_sales_order')->where('call_status',1)->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")->where('company_id',$company_id)->groupBy('user_id','date')->pluck(DB::raw("COUNT(DISTINCT retailer_id) as productive_count"),DB::raw("CONCAT(user_id,date)"));


       $other_data_data = DB::table('user_sales_order')
        ->join('location_view', 'location_view.l7_id', '=', 'user_sales_order.location_id')
        ->join('dealer', 'dealer.id', '=', 'user_sales_order.dealer_id')
        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to'")
        ->where('user_sales_order.company_id',$company_id)
        ->where('dealer.company_id',$company_id)
        ->select('user_id AS sale_user_id','user_sales_order.date as sale_date','location_id', 'location_view.l7_name', 'location_view.l6_name', 'user_sales_order.dealer_id', 'dealer.name as dealer_name','l7_id as l5_id',
            DB::raw("(select COUNT(id) from retailer where location_id = user_sales_order.location_id )as total_outlet"))
        ->groupBy('sale_user_id','sale_date')
        ->distinct();
        if(!empty($state))
        {
            $other_data_data->whereIn('l3_id',$request->area);
        }
        if (!empty($region)) {
            
            $other_data_data->whereIn('location_view.l2_id', $region);
        }
        if (!empty($user)) {
            
            $other_data_data->whereIn('user_id', $user);
        }

       $other_data = $other_data_data->get();
  
        if (!empty($other_data)) 
        {
            foreach ($other_data as $other_key => $other_value) 
            {
                $user_id = $other_value->sale_user_id;
                $date = $other_value->sale_date;

                $otherArr[$user_id.$date]['beat'] = $other_value->l7_name;
                $otherArr[$user_id.$date]['town'] = $other_value->l6_name;
                $otherArr[$user_id.$date]['dealer'] = $other_value->dealer_name;
                $otherArr[$user_id.$date]['beat_id'] = $other_value->location_id;
                $otherArr[$user_id.$date]['total_outlet'] = $other_value->total_outlet;

            }
        }
           
          
        $new_outlet_data  = DB::table('retailer')
            // ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
            ->join('location_view','location_view.l7_id','=','retailer.location_id')
            ->where('retailer.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d') >='$from' and DATE_FORMAT(created_on,'%Y-%m-%d') <='$to'")
            ->groupBy('created_by_person_id',DB::raw("DATE_FORMAT(created_on,'%Y-%m-%d')"));
            if(!empty($state))
            {
                $new_outlet_data->whereIn('l3_id',$request->area);
            }
            if (!empty($region)) {
                
                $new_outlet_data->whereIn('l2_id', $region);
            }
            if (!empty($user)) {
                
                $new_outlet_data->whereIn('created_by_person_id', $user);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $new_outlet_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $new_outlet_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $new_outlet_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $new_outlet_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $new_outlet_data->whereIn('dealer_id', $dealer);
            }
            // if (!empty($request->user)) 
            // {
            //     $user = $request->user;
            //     $new_outlet_data->whereIn('user_id', $user);
            // }
            // if (!empty($request->role)) 
            // {
            //     $role = $request->role;
            //     $awsome_query->whereIn('role_id', $role);
            // }
            $new_outlet = $new_outlet_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retailer_id"),DB::raw("CONCAT(created_by_person_id,DATE_FORMAT(created_on,'%Y-%m-%d'))"));



            $new_productive_outlet_data  = DB::table('retailer')
            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
            ->join('location_view','location_view.l7_id','=','retailer.location_id')
            ->where('retailer.company_id',$company_id)
            ->where('call_status','=','1')
            ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d') >='$from' and DATE_FORMAT(created_on,'%Y-%m-%d') <='$to'")
            ->groupBy('created_by_person_id',DB::raw("DATE_FORMAT(created_on,'%Y-%m-%d')"));
            if(!empty($state))
            {
                $new_productive_outlet_data->whereIn('l3_id',$request->area);
            }
            if (!empty($region)) {
                
                $new_productive_outlet_data->whereIn('l2_id', $region);
            }
            if (!empty($user)) {
                
                $new_productive_outlet_data->whereIn('created_by_person_id', $user);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $new_productive_outlet_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $new_productive_outlet_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $new_productive_outlet_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $new_productive_outlet_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $new_productive_outlet_data->whereIn('dealer_id', $dealer);
            }
            $new_productive_outlet = $new_productive_outlet_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retailer_id"),DB::raw("CONCAT(created_by_person_id,DATE_FORMAT(created_on,'%Y-%m-%d'))"));

            // dd($new_outlet);
        $awsome_query = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')->join('user_daily_attendance', 'user_daily_attendance.user_id', 'person.id')
        // ->join('location_3','location_6.id','=','person.town_id')
        ->join('location_6','location_6.id','=','person.town_id')
        ->join('location_5','location_5.id','=','person.head_quater_id')
        ->join('location_4','location_4.id','=','location_5.location_4_id')
        ->join('location_3','person.state_id','=','location_3.id')
        ->join('location_2','location_2.id','=','location_3.location_2_id')
        ->join('_role','_role.role_id','=','person.role_id')
        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
        ->select('person_id_senior','person.mobile as mobile','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person_login.person_status as status',DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),'person.id as user_id','location_3.id as l3_id','location_2.name as l2_name','location_3.name as l3_name','person.emp_code','person.head_quar','person.region_txt','user_daily_attendance.work_date','_role.rolename',DB::raw("(select CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) from person WHERE person.id=user_daily_attendance.working_with limit 0,1) as working_with"),'_working_status.name as w_s',DB::raw("DATE_FORMAT(work_date,'%d-%m-%Y') AS work_dates"),DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') AS work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') AS work_time"),'user_daily_attendance.remarks as checkInRemarks')
        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to'")
        ->where('person_status','!=',9)
        ->where('person.company_id',$company_id)
        ->where('_working_status.company_id',$company_id)
        ->where('_role.company_id',$company_id)
        ->groupBy('uname','w_s','person.id','user_daily_attendance.work_date','rolename','working_with')
        ->orderBy('user_daily_attendance.work_date','ASC');

       
        //dd($awsome_query);
          #Senior Data

        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $awsome_query->whereIn('location_3.id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $awsome_query->whereIn('location_4.id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $awsome_query->whereIn('location_5.id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $awsome_query->whereIn('location_6.id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $awsome_query->whereIn('dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $awsome_query->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $awsome_query->whereIn('role_id', $role);
        }
        if (!empty($datasenior)) 
        {
            $awsome_query->whereIn('person.id', $datasenior);
        }

            #Region filter
        // if (!empty($request->region)) {
        //     $region = $request->region;
        //     $awsome_query->whereIn('location_view.l2_id', $region);
        // }
        // #State filter
        // if (!empty($request->area)) {
        //     $state = $request->area;
        //     $awsome_query->whereIn('location_view.l3_id', $state);
        // }
        #Status filter
        if (!empty($status)) {
            $awsome_query->whereIn('person_login.person_status', $status);
        }

        #Role filter
        // if (!empty($request->role)) {
        //     $role_id = $request->role;
        //     $awsome_query->whereIn('person.role_id', $role_id);
        // }
        // #User Filter
        // if (!empty($request->user)) 
        // {
        //     $ud = $request->user;
        //     $awsome_query->whereIn('person.id', $ud);
        // }

        $query=$awsome_query->get();

        $sa = [];
        $beatArr = [];
        $townArr = [];
        $dealer_name = [];
      
        $mtp_beat=[];
        $out=[];
        if (!empty($query)) {
            foreach ($query as $key => $data) {
                
                $user=$data->user_id;
                $date=$data->work_date;
                $index=$user.$date;
                $out[$index]['status']=$data->status;
                $out[$index]['uname']=$data->uname;
                $out[$index]['mobile']=$data->mobile;
                $out[$index]['uid']=$data->user_id;
                $out[$index]['l3_name']=$data->l3_name;
                $out[$index]['l4_name']=$data->l4_name;
                $out[$index]['l5_name']=$data->l5_name;
                $out[$index]['l6_name']=$data->l6_name;
                $out[$index]['emp_code']=$data->emp_code;
                $out[$index]['person_id_senior']=$data->person_id_senior;
                // $out[$index]['rolename']=$data->rolename;
                $out[$index]['head_quar']=$data->head_quar;
                $out[$index]['region_txt']=$data->region_txt;
                $out[$index]['rolename']=$data->rolename;
                $out[$index]['working_with']=$data->working_with;
                $out[$index]['work_dates']=$data->work_dates;
                $out[$index]['work_time']=$data->work_time;
                $out[$index]['checkInRemarks']=$data->checkInRemarks;
                $out[$index]['work_date']=$data->work_date;
                // $out[$index]['l5_id']=$data->l5_id;
                $out[$index]['w_s']=$data->w_s;

                $out[$index]['mtp']=DB::table('monthly_tour_program')
                ->leftJoin('dealer','dealer.id','=','monthly_tour_program.dealer_id')
                ->leftJoin('location_6','location_6.id','=','monthly_tour_program.town')
                ->leftJoin('location_7','location_7.id','=','monthly_tour_program.locations')
                ->select('location_6.name as l4_name','dealer.name as dname','location_7.name as l5_name','location_7.id AS l5_id')
                ->where('monthly_tour_program.person_id',$user)
                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d') = DATE_FORMAT('$date', '%Y-%m-%d')")
                ->first();
                 $mtp_beat[$index]=!empty($out[$index]['mtp']->l5_id)?$out[$index]['mtp']->l5_id:'0';

                
            }
        }
        // dd($townArr);

        return view('reports.daily-performance.ajax  ', [
            'records' => $out,
           // 'query' => $query,
            'working_status' => $working_status,
            'actual_beat' => $sa,
            'beatArr' => $beatArr,
            'townArr' => $townArr,
            'dealer_name' => $dealer_name,
            // 'new_outlet' => $new_outlet,
            'kg' => $temp_kg,
            'rv' => $temp_rv,
            'checkout_data'=>$checkoutarr,
            'time_of_first_call'=>$time_of_first_call,
            'time_of_last_call'=>$time_of_last_call,
            'catalog' => $catalog,
            // 'working_beats' => $working_beats,
            'mtp_beat' => $mtp_beat,
            'new_arr' => $new_arr,
            'other_sale_arr' => $otherArr,
            'new_outlet'=>$new_outlet,
            'new_productive_outlet'=>$new_productive_outlet,
            'visit_count_data'=>$visit_count,
            'productive_calls'=>$productive_calls,
//                'rv_product' => $rv_product
        ]);
    } else {
        echo '<p class="alert-danger">No Data Found</p>';
    }
}



    // GAnesh Work
    public function newSdDistProspectingReport(Request $request)
    {
        //echo"1212";die;
        if ($request->ajax()) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;
            $from = $request->from_date;
            $to = $request->to_date;


            $query = DB::table('daily_prospecting_working');
            if (!empty($user_id)) {
                $query->whereIn('user_id', $user_id);
            }
            if (!empty($from) && !empty($to)) {
                $query->whereRaw("DATE_FORMAT(daily_prospecting_working.cur_date_time,'%Y-%m-%d') >='$from' and DATE_FORMAT(daily_prospecting_working.cur_date_time,'%Y-%m-%d') <='$to'");
            }

            $query_data = $query->orderBy('cur_date_time', 'DESC')
                ->get();
            return view('reports.new-sd-dist-prospecting.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }


    }

    public function productInvestigationReport(Request $request)
    {
        //echo"1212";die;
        if ($request->ajax() && !empty($request->user)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;


            $query = DB::table('product_investigation_report')
                ->where('user_id', $user_id);

            $query_data = $query->orderBy('date_time', 'DESC')
                ->get();
            //  print_r($query_data );die;
            return view('reports.product-investigation.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }


    }

    public function complaintReport(Request $request)
    {

        if ($request->ajax()) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;

            $from_date = $request->from_date;
            $to_date = $request->to_date;


            $query = DB::table('Complaint_report')
                ->select('person.emp_code', 'person.head_quar', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'Complaint_report.*','person.id as uid')
                ->leftJoin('person', 'person.id', '=', 'Complaint_report.user_id');

            $q = $query->orderBy('created_at', 'DESC');
            if (!empty($user_id)) {
                $q->whereIn('user_id', $user_id);
            }
            if (!empty($from_date) && !empty($to_date)) {
                $q->whereRaw("DATE_FORMAT(Complaint_report.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(Complaint_report.date,'%Y-%m-%d') <='$to_date'");
            }
            $query_data = $q->get();
            // print_r($query_data );die;
            return view('reports.complaint-report.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found123</p>';
        }


    }

    public function paymentDetailsReport(Request $request)
    {

        if ($request->ajax()) {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;

            $user = $request->user;
            $date = $request->date;
            $arr = [1 => 'cash', 2 => 'Cheque', 3 => 'NEFT/RGTDS', 4 => 'Demand Draft'];


            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $query_data = DB::table('dealer_payments')
                ->select('drawn_from_bank', 'deposited_bank', 'invoice_number', 'person.emp_code', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as user_id','dealer.id as did', 'dealer.name as dealer_name', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                    'location_view.l6_name as town_name', 'dealer_payments.*')
                ->join('person', 'person.id', '=', 'dealer_payments.user_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_payments.dealer_id')
                ->leftJoin('location_view', 'location_view.l6_id', '=', 'dealer_payments.town')
                ->where('dealer_payments.company_id',$company_id)
                ->where('dealer.company_id',$company_id)
                ->groupBy('dealer_payments.id');
                

//                ->groupBy('payment_recevied_date', 'user_id','dealer_payments.drawn_from_bank','dealer_payments.deposited_bank','dealer_payments.invoice_number','dealer.name','location_view.l1_name','location_view.l2_name','location_view.l4_name','dealer_payments.id');

            $dealer_beat = DB::table('location_view')->where('l7_company_id',$company_id);

            if (!empty($datasenior)) 
            {
                $dealer_beat->whereIn('person.id', $datasenior);
            }
            if (!empty($town)) {

                $dealer_beat->whereIn('l4_id', $town)->pluck('l5_id');
            } //Town Data
            elseif (!empty($state)) {

                $dealer_beat->whereIn('l3_id', $state)->pluck('l5_id');

            } //Beat Data
            elseif (!empty($region)) {
                $tr = [];
                $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l5_id');

            }


            if (!empty($request->distributor)) {

                $tr = [];
                $tr = $request->distributor;
            }

            if (!empty($tr)) {
                $query_data->whereIn('dealer_payments.dealer_id', $tr);
            }

// To find No of user under certain role
            $flag = [];


            if (!empty($user)) {

                $flag = [];
                $flag = $request->user;

            }


            if (!empty($flag)) {
                $query_data->where('dealer_payments.emp_id', $flag);
            }


            if (!empty($date)) {
                $query_data->whereRaw("DATE_FORMAT(payment_recevied_date, '%Y-%m-%d') = '$date'");
            }

            $query = $query_data->get();

            return view('reports.payment-details.ajax', [
                'records' => $query,
                'arr' => $arr
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }

    }

//    public function paymentDetailsReport(Request $request)
//    {
//        if ($request->ajax() && !empty($request->user)) {
//
////            $receipt = $request->receipt;
//            $user = $request->user;
//            $date = $request->date;
//            $arr = [1 => 'cash', 2 => 'Cheque', 3 => 'NEFT/RGTDS', 4 => 'Demand Draft'];
//            $data = DB::table('dealer_payments')
//                ->leftJoin('person', 'person.id', '=', 'dealer_payments.user_id')
//                ->leftJoin('location_view', 'location_view.l4_id', '=', 'dealer_payments.town')
//                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_payments.dealer_id');
//            if (!empty($user)) {
//                $data->where('user_id', $user);
//            }
//            if (!empty($date)) {
//                $data->whereRaw("DATE_FORMAT(payment_recevied_date, '%Y-%m-%d') = '$date'");
//            }
////                ->where('dealer_payments.id', $receipt)
//            $data->select('dealer_payments.invoice_number', 'dealer_payments.payment_mode', 'dealer.name as dealer_name', DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'location_view.l1_name as zone', 'dealer_payments.emp_id', 'location_view.l2_name as hq', '_role.rolename', 'dealer.name as dealer_name', 'location_view.l4_name as town', 'dealer_payments.payment_recevied_date', 'dealer_payments.amount');
//            $q1 = $data->get();
//            return view('reports.payment-details.ajax', [
//                'records' => $q1,
//                'arr' => $arr
//            ]);
//        } else {
//            echo '<p class="alert-danger">No Data Found</p>';
//        }
//    }

    //Distributer Wise Secondry Sales Trends
   public function distributerWiseSecondarySalesTrendsReport(Request $request)
    {

        if ($request->ajax() && !empty($request->year)) {
            $company_id = Auth::user()->company_id;
            $dealer = $request->dealer;
            $month_with_year = explode('-', $request->year);
            $year = $month_with_year[0];
            $month = $month_with_year[1];

            $one_year_back = $year - 1;
            $two_year_back = $year - 2;

            $month_arr = ['04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03'];

            #Dealer
            $query_data = DB::table('dealer_location_l4');
            if (!empty($dealer)) {
                $query_data->whereIn('id', $dealer);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $query_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $query_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $query_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $query_data->whereIn('l6_id', $location_6);
            }
            
            $records = $query_data->groupBy('id')->where('company_id',$company_id)->get();
            $final_data = [];
            $final_data_data  = DB::table('user_sales_order_view')
                        ->where('company_id',$company_id)
                        ->groupBy('dealer_id',DB::raw("(DATE_FORMAT(date,'%Y-%m'))"));
                        if (!empty($request->location_3)) 
                        {
                            $location_3 = $request->location_3;
                            $final_data_data->whereIn('l3_id', $location_3);
                        }
                        if (!empty($request->location_4)) 
                        {
                            $location_4 = $request->location_4;
                            $final_data_data->whereIn('l4_id', $location_4);
                        }
                        if (!empty($request->location_5)) 
                        {
                            $location_5 = $request->location_5;
                            $final_data_data->whereIn('l5_id', $location_5);
                        }
                        if (!empty($request->location_6)) 
                        {
                            $location_6 = $request->location_6;
                            $final_data_data->whereIn('l6_id', $location_6);
                        }
                        if (!empty($request->dealer)) 
                        {
                            $dealer = $request->dealer;
                            $final_data_data->whereIn('dealer_id', $dealer);
                        }
                        if (!empty($request->user)) 
                        {
                            $user = $request->user;
                            $final_data_data->whereIn('user_id', $user);
                        }
                        if (!empty($request->role)) 
                        {
                            $role = $request->role;
                            $final_data_data->whereIn('role_id', $role);
                        }
            $final_data = $final_data_data->pluck(DB::raw("SUM(total_sale_value)"),DB::raw("CONCAT(dealer_id,DATE_FORMAT(date,'%Y-%m'))"));
            
            $arr = [];
            foreach ($records as $k => $d) {
                foreach ($month_arr as $mk => $md) {
                   
                    if ($md < 4) {
                        $year = $year + 1;
                    }
                    // $query = DB::table('user_sales_order_view')->where('dealer_id', $d->id);
                    // $data = $query;
                    $d1 = $year . '-' . $md;
                    $d2 = ($year - 1) . '-' . $md;
                    $d3 = ($year - 2) . '-' . $md;
                   // echo $d->id.$d1;
                   // echo "<br>";
                    $arr[$d->id]['f1'][$md] = !empty($final_data[$d->id.$d1])?$final_data[$d->id.$d1]:'0';
                    $arr[$d->id]['f2'][$md] = !empty($final_data[$d->id.$d2])?$final_data[$d->id.$d2]:'0';
                    $arr[$d->id]['f3'][$md] = !empty($final_data[$d->id.$d3])?$final_data[$d->id.$d3]:'0';
                    // $arr[$d->id]['f1'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d1'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    // $arr[$d->id]['f2'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d2'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    // $arr[$d->id]['f3'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d3'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    if ($md < 4) {
                        $year = $year - 1;
                    }
                }

            }

//            dd($arr);

            return view('reports.distributer-wise-sales-trends.ajax', [
                'records' => $records,
                'arr' => $arr,
                'y1' => $year,
                'y2' => $one_year_back,
                'y3' => $two_year_back,
                'month' => $month,
                'monthArr' => $month_arr
            ]);
        } else {
            echo '<p class="alert-danger">No Data Found</p>';
        }
    }

public function stateWiseSecondarySalesTrendsReport(Request $request)
{
    if ($request->ajax()) 
    {   
        $company_id = Auth::user()->company_id;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        $state = $request->state;
        $array = [];
        $rv_data = [];
        $pc_data = [];

        $catalog_1 = DB::table('catalog_2')
        ->where('company_id',$company_id)
        ->where('status',1)
        ->groupBy('id')
        ->pluck('catalog_2.name', 'catalog_2.id');

        $query = DB::table('location_view')
        ->where('l1_company_id',$company_id)
        ->select('l1_id','l1_name','l3_id','l3_name','l2_name') 
        ->groupBy('l3_id');

        if(!empty($state))
        {
        $query->whereIn('l3_id',$state);
        }
        $query=$query->get();

        $pc_details_data= DB::table('secondary_sale')
        ->whereRaw("DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')<='$to_date'")
        ->where('secondary_sale.call_status','=','1')
        ->where('secondary_sale.company_id',$company_id)
        ->groupBy('l3_id','secondary_sale.c2_id')
        ->select(DB::raw('COUNT(DISTINCT secondary_sale.order_id) as pc_count'),'l3_id','c2_id');
        if(!empty($state))
        {
            $pc_details_data->whereIn('l3_id',$state);
        }
        $pc_details = $pc_details_data->get();
        foreach ($pc_details as $key => $value) 
        {
            $l3_id = $value->l3_id;
            $c0_id = $value->c2_id;
            $pc_data[$l3_id][$c0_id]['pc_count'] = $value->pc_count;

        }
        #rv details.........
        if(empty($check)){
            $rv_details_data= DB::table('secondary_sale')
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->where('secondary_sale.company_id',$company_id)
            ->groupBy('l3_id','c2_id')
            ->select(DB::raw('SUM(rate*quantity)as rv_sum'),'c2_id','l3_id');
            if(!empty($state))
            {
                $rv_details_data->whereIn('l3_id',$state);
            }
        $rv_details = $rv_details_data->get();
        }else{
            $rv_details_data= DB::table('secondary_sale')
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->where('secondary_sale.company_id',$company_id)
            ->groupBy('l3_id','c2_id')
            ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty)as rv_sum'),'c2_id','l3_id');
            if(!empty($state))
            {
                $rv_details_data->whereIn('l3_id',$state);
            }
            $rv_details = $rv_details_data->get();

        }
        foreach ($rv_details as $key => $value) 
        {
            $l3_id = $value->l3_id;
            $c0_id = $value->c2_id;
            $rv_data[$l3_id][$c0_id]['rv_sum'] = $value->rv_sum;
        }
        #rv details ends here.........

        if(empty($check)){
            $total_sale_data = DB::table('secondary_sale')
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->where('secondary_sale.company_id',$company_id)
            ->groupBy('l3_id');
        if(!empty($state))
        {
            $total_sale_data->whereIn('l3_id',$state);
        }
        $total_sale = $total_sale_data->pluck(DB::raw('SUM(rate*quantity) as total_sale'),'l3_id');
        }
        else
        {
            $total_sale_data = DB::table('secondary_sale')
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->where('secondary_sale.company_id',$company_id)
            ->groupBy('l3_id');
            if(!empty($state))
            {
                $total_sale_data->whereIn('l3_id',$state);
            }
            $total_sale = $total_sale_data->pluck(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as total_sale'),'l3_id');
        }



          $total_productive_call_data = DB::table('user_sales_order')
        ->join('location_7','location_7.id','=','user_sales_order.location_id')
        ->join('location_6','location_6.id','=','location_7.location_6_id')
        ->join('location_5','location_5.id','=','location_6.location_5_id')
        ->join('location_4','location_4.id','=','location_5.location_4_id')
        ->join('location_3','location_3.id','=','location_4.location_3_id')
        ->where('call_status','=','1')
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        ->where('user_sales_order.company_id',$company_id)
        ->groupBy('location_3.id');
        if(!empty($state))
        {
            $total_productive_call_data->whereIn('location_3.id',$state);
        }
        $total_productive_call = $total_productive_call_data->pluck(DB::raw('COUNT(DISTINCT retailer_id,date) as total_call'),'location_3.id');    


        // $total_productive_call_data = DB::table('secondary_sale')
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        // ->where('secondary_sale.company_id',$company_id)
        // ->where('call_status','=','1')
        // ->groupBy('l3_id');
        // if(!empty($state))
        // {
        //     $total_productive_call_data->whereIn('l3_id',$state);
        // }
        // $total_productive_call = $total_productive_call_data->pluck(DB::raw('COUNT(DISTINCT order_id) as productive_call'),'l3_id');


          $total_call_data = DB::table('user_sales_order')
        ->join('location_7','location_7.id','=','user_sales_order.location_id')
        ->join('location_6','location_6.id','=','location_7.location_6_id')
        ->join('location_5','location_5.id','=','location_6.location_5_id')
        ->join('location_4','location_4.id','=','location_5.location_4_id')
        ->join('location_3','location_3.id','=','location_4.location_3_id')
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        ->where('user_sales_order.company_id',$company_id)
        ->groupBy('location_3.id');
        if(!empty($state))
        {
            $total_call_data->whereIn('location_3.id',$state);
        }
        $total_call = $total_call_data->pluck(DB::raw('COUNT(DISTINCT retailer_id,date) as total_call'),'location_3.id');        

        // $total_call_data = DB::table('secondary_sale')
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        // ->where('secondary_sale.company_id',$company_id)
        // ->groupBy('l3_id');
        // if(!empty($state))
        // {
        //     $total_call_data->whereIn('l3_id',$state);
        // }
        // $total_call = $total_call_data->pluck(DB::raw('COUNT(DISTINCT order_id) as total_call'),'l3_id');




        $new_outlet_data =  DB::table('retailer')
        ->join('location_view','l7_id','=','retailer.location_id')
        ->where('retailer_status','=','1')
        ->where('retailer.company_id',$company_id)
        ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(created_on,'%Y-%m-%d')<='$to_date'")
        ->groupBy('l3_id');
        if(!empty($state))
        {
            $new_outlet_data->whereIn('l3_id',$state);
        }
        $new_outlet = $new_outlet_data->pluck(DB::raw('COUNT(DISTINCT retailer.id) as new_outlet'),'l3_id');


        $total_outlet_data = DB::table('retailer')
        ->join('location_view','l7_id','=','retailer.location_id')
        ->where('retailer_status','=','1')
        ->where('retailer.company_id',$company_id)
        ->groupBy('l3_id');
        if(!empty($state))
        {
            $total_outlet_data->whereIn('l3_id',$state);
        }
        $total_outlet = $total_outlet_data->pluck(DB::raw('COUNT(DISTINCT retailer.id) as total_outlet'),'l3_id');

        $total_active_user_data = DB::table('user_sales_order')
        ->join('person_login','user_sales_order.user_id','=','person_login.person_id')
        ->join('person','person.id','=','person_login.person_id')
        ->where('user_sales_order.company_id',$company_id)
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        ->where('person_status','1')
        ->groupBy('state_id');
        if(!empty($state))
        {
            $total_active_user_data->whereIn('state_id',$state);
        }
        $total_active_user = $total_active_user_data->pluck(DB::raw('COUNT(DISTINCT user_id) as total_active_user'),'state_id');

        $total_user_data = DB::table('person_login')
        ->join ('person','person.id','=','person_login.person_id')
        ->where('person_status','1')
        ->where('person.company_id',$company_id)
        ->groupBy('state_id');
        if(!empty($state))
        {
            $total_user_data->whereIn('state_id',$state);
        }
        $total_user = $total_user_data->pluck(DB::raw('COUNT(DISTINCT person.id) as total_user'),'state_id');

        return view('reports.state-wise-sales-trends.ajax', [
        'records' => $query,
        'arr' => $array,
        'start_date'=>$from_date,
        'end_date'=>$to_date,
        'catalog_1'=> $catalog_1,
        'pc_data'=> $pc_data,
        'rv_data'=> $rv_data,
        'total_sale'=>$total_sale,
        'total_productive_call'=>$total_productive_call,
        'total_call'=>$total_call,
        'new_outlet'=>$new_outlet,
        'total_outlet'=>$total_outlet,
        'total_active_user'=>$total_active_user,
        'total_user'=>$total_user,

        ]);
    }
    else 
    {
        echo '<p class="alert-danger">No Data Found</p>';
    }
}

############   eatos and aggarbati report starts here 
public function statewise_user_eatosReport(Request $request)
{
    if ($request->ajax()) 
    {
        // dd('d');
        $state = $request->area;
        $region = $request->region;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $array = [];
        $out =[];

        $catalog_2 =DB::table('catalog_2')
        ->join('catalog_product','catalog_2.id','=','catalog_product.catalog_id')
        ->select('catalog_2.id as id','catalog_2.name as name',DB::raw('count(catalog_product.id) as numproduct'))
        ->whereRaw("catalog_2.id=32 OR catalog_2.id=33")->groupBy('catalog_2.id')->orderBy('catalog_2.id')->get();

        $product =DB::table('catalog_view')
        ->select('product_name AS name','product_id AS id','c2_id AS catalog_id')
        ->whereRaw("c2_id IN (32,33)")->orderBy('catalog_id')->get();


        $total_sale_value_data = DB::table('secondary_sale')
        ->whereRaw("DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')<='$to_date'")
        ->whereIn('c2_id',[32,33])
        ->groupBy('date','product_id','user_id');
        if(!empty($user))
        {
             $total_sale_value_data->whereIn('user_id',$user);

        }
        if(!empty($state))
        {
             $total_sale_value_data->whereIn('l3_id',$state);

        }
        if(!empty($region))
        {
            $total_sale_value_data->whereIn('l2_id',$region);

        }
        $total_sale_value = $total_sale_value_data->pluck(DB::raw("SUM(rate*quantity)"),DB::raw("CONCAT(date,user_id,product_id)"));
        // dd($total_sale_value);

        $eatos_data_data = DB::table('secondary_sale')
        ->whereRaw("DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')<='$to_date'")
        ->where('secondary_sale.c2_id','=','32')
        ->where('secondary_sale.call_status','=','1')
        ->groupBy('date','user_id');
        if(!empty($user))
        {
           $eatos_data_data->whereIn('user_id',$user);

        }
        if(!empty($state))
        {
           $eatos_data_data->whereIn('l3_id',$state);

        }
        if(!empty($region))
        {
            $eatos_data_data->whereIn('l2_id',$region);

        }
        $eatos_data = $eatos_data_data->pluck(DB::raw('COUNT(DISTINCT secondary_sale.order_id) as order_id'),DB::raw("CONCAT(date,user_id)"));
        // dd($eatos_data);
        $aggarbati_data_data = DB::table('secondary_sale')
        ->whereRaw("DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')<='$to_date'")
        ->where('secondary_sale.c2_id','=','33')
        ->where('secondary_sale.call_status','=','1')
        ->groupBy('date','user_id');
        if(!empty($user))
        {
            $aggarbati_data_data->whereIn('user_id',$user);

        }
        if(!empty($state))
        {
              $aggarbati_data_data->whereIn('l3_id',$state);

        }
        if(!empty($region))
        {
             $aggarbati_data_data->whereIn('l2_id',$region);

        }
        $aggarbati_data = $aggarbati_data_data->pluck(DB::raw('COUNT(DISTINCT secondary_sale.order_id) as order_id'),DB::raw("CONCAT(date,user_id)"));
        // dd($aggarbati_data);
        $working_with_data=DB::table('user_daily_attendance')
        ->join('person','person.id','=','user_daily_attendance.working_with')
        ->join('location_view','location_view.l3_id','=','person.state_id')
        ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
        ->groupBy('work_date','user_id');
        if(!empty($user))
        {
          $working_with_data->whereIn('user_id',$user);

        }
        if(!empty($state))
        {
          $working_with_data->whereIn('l3_id',$state);

        }
        if(!empty($region))
        {
             $working_with_data->whereIn('l2_id',$region);

        }
        $working_with = $working_with_data->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS name"),DB::raw("CONCAT(DATE_FORMAT(work_date,'%Y-%m-%d'),user_id)"));

        // dd($working_with);
        $query1 = DB::table('user_sales_order_view')
        ->select('l1_id','l1_name','l3_id','l3_name','l6_id as l4_id','l6_name as l4_name','dealer_name','user_name','role_name','user_id','dealer_id','role_id','date')
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        ->groupBy('l1_id','l1_name','l3_id','l3_name','l6_id as l4_id','l6_name as l4_name','dealer_name','user_name','role_name','user_id','dealer_id','role_id','date');

        if(!empty($user))
        {
            $query1->whereIn('user_id',$user);

        }
        if(!empty($state))
        {
            $query1->whereIn('l3_id',$state);

        }
        if(!empty($region))
        {
            $query1->whereIn('l2_id',$region);

        }

        $query=$query1->get();

        foreach($query as $r=>$k)
        {
            $state_id=$k->l3_id;
            $zone_id = $k->l1_id;
            $town_id = $k->l4_id;
            $user_id =$k->user_id;

            $out[$user_id]['state_id']=$state_id;
            $out[$user_id]['zone_id']=$zone_id;
            $out[$user_id]['town_id']=$town_id;
            $out[$user_id]['user_id']=$user_id;

            $out[$user_id]['l3_name']=$k->l3_name;
            $out[$user_id]['l1_name']=$k->l1_name;
            $out[$user_id]['l4_name']=$k->l4_name;

            $out[$user_id]['dealer_name']=$k->dealer_name;
            $out[$user_id]['user_name']=$k->user_name;
            $out[$user_id]['role_name']=$k->role_name;
            $out[$user_id]['date']=$k->date;

        }
        //print_r($out);
        // dd($out);
        return view('reports.statewise_user_eatos.ajax', [
        'records' => $query,
        'catalog_2'=>$catalog_2,
        'product'=>$product,
        'arr' => $array,
        'start_date'=>$from_date,
        'end_date'=>$to_date,
        'details' => $out,
        'total_sale_value'=>$total_sale_value,
        'eatos_data'=>$eatos_data,
        'aggarbati_data'=>$aggarbati_data, 
        'working_with'=>$working_with
        ]);
    }
    else 
    {
        echo '<p class="alert-danger">No Data Found</p>';
    }
}
#################

//   public function budget_target_statusReport(Request $request)
//   {

//     if ($request->ajax()) {
//         $stateid = $request->area;

//         $region = $request->region;
//         $user = $request->user;

//  $year = !empty($request->year)?$request->year:date('Y-m');
//  $start_date =date('Y-m-d',strtotime($year));
//                        $end_date= date("Y-m-t", strtotime($start_date));
//                         $startTime = strtotime($start_date);
//                          $endTime = strtotime($end_date);

// for ($currentDate = $startTime; $currentDate <= $endTime;  
//                                 $currentDate += (86400)) { 
                                      
// $Store = date('Y-m-d', $currentDate); 
// $datearray[] = $Store; 
// } 
//         $arra = [];
//         $out =[];
// #$query1
 
//      $query1=DB::table('state_particular_view')
//         ->select('state_id','state','partid','particularsname','location_2.name as region')
//         ->join('location_2','location_2.id','=','state_particular_view.l2_id')
//         ->where('state_particular_view.status','1');
//     if(!empty($stateid))
//     {
//      $query1->whereIn('state_particular_view.state_id',$stateid);
//     }
//      if(!empty($region))
//     {
//      $query1->whereIn('state_particular_view.l2_id',$region);
//     }
//  //dd($query1);
// $rt=$query1->orderBy('state_particular_view.state_id','ASC')->orderBy('partid','ASC')->get();
 
//      foreach($rt as $r=>$k)
//                     { 
//              foreach($datearray as $dr=>$dk){         
       
                      
//                         $stateid=$k->state_id;
//                         $particularsname=$k->particularsname;
//                         $partid=$k->partid;
//                         if($partid==1)
//                         {    $valueData=DB::table('_budget_target')
//                                 ->select('manpower_budget','budget_user','target_outlet')
//                                 ->where('state_id',$stateid)->first();
 
//                         $out[$stateid][$dk][$particularsname]=!empty($valueData->manpower_budget)?$valueData->manpower_budget:0;
//                         }
//                         //manpower Total User(Active)
//                         elseif($partid==2)
//                         {
//                              $total_user=DB::table('person')
//                              ->join('person_login','person_id','=','person.id')
//                             ->where('person.state_id','=',$stateid)
//                             ->where('person_status','=','1')
//                             ->count('id'); 
//                              $out[$stateid][$dk][$particularsname]=!empty($total_user)?$total_user:0;
                        
                        
//                         }
//                         //BUDGETED SO+SSO+ISR _budget_target
//                         elseif($partid==3)
//                         {
//                              $valueData=DB::table('_budget_target')
//                                 ->select('budget_user','target_outlet')
//                                 ->where('state_id',$stateid)->first();
//                             $out[$stateid][$dk][$particularsname]=!empty($valueData->budget_user)?$valueData->budget_user:0;
//                         }
//                         //ACTUAL SO+SSO+ISR	Total User(SSO+SO+ISR+JSO Active)
//                         elseif($partid==4)
//                         {
//                           $total_rolewiseuser=DB::table('person')
//                              ->join('person_login','person_id','=','person.id')
//                             ->where('person.state_id','=',$stateid)
//                             ->where('person_status','=','1')
//                             ->whereIn('role_id',[40,42,12,46])
//                             ->count('id'); 
//                              $out[$stateid][$dk][$particularsname]=!empty($total_rolewiseuser)?$total_rolewiseuser:0;
//                        // $out[$stateid][$dk][$particularsname]=1002;
//                         }
//                         //ATTENDANCE MARKED	Total Attendance
//                         elseif($partid==5)
//                         {
//                      $attendancemarkeduser=DB::table('user_daily_attendance')
//                         ->join('person','person.id','=','user_daily_attendance.user_id')
//                          ->join('person_login','person_id','=','person.id')
//                         ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$dk'")
//                         ->where('person.state_id','=',$stateid)
//                          ->whereIn('person.role_id',[40,42,12,46])
//                          ->where('person_status','=','1')
//                         ->count('user_id');
                       
//                         $out[$stateid][$dk][$particularsname]=!empty($attendancemarkeduser)?$attendancemarkeduser:0;
//                         }
//                         //ACTUAL WORKED	Total Attendance apart from Leaves
//                         elseif($partid==6)
//                         {
//                        $attendancemarkeduser_notleave=DB::table('user_daily_attendance')
//                         ->join('person','person.id','=','user_daily_attendance.user_id')
//                          ->join('person_login','person_id','=','person.id')
//                         ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$dk'")
//                         ->where('person.state_id','=',$stateid)
//                          ->whereIn('person.role_id',[40,42,12,46])
//                         ->whereIn('work_status',[1,9,5,15,19])
//                         ->where('person_status','=','1')
//                         ->count('user_id') ;
//                      $out[$stateid][$dk][$particularsname]=!empty($attendancemarkeduser_notleave)?$attendancemarkeduser_notleave:0;
                       
//                         }
//                         //PRODUCTIVE CALLS	COUNT DISTINCT Retailer Sales Order
//                         elseif($partid==7)
//                         {

// $productive_calls=DB::table('user_sales_order')
// ->join('person','person.id','=','user_sales_order.user_id')
//  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$dk' ")
// ->where('person.state_id','=',$stateid)
// ->where('call_status','=','1')
//  ->count(DB::raw('DISTINCT order_id'));
//                     //   $productive_calls=DB::table('secondary_sale')
//                     //         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$dk' ")
//                     //         ->where('secondary_sale.l3_id','=',$stateid)
//                     //         ->where('call_status','=','1')
//                     //         ->count(DB::raw('DISTINCT order_id'));
       
//         $out[$stateid][$dk][$particularsname]=!empty($productive_calls)?$productive_calls:0;
                       
//                         }
//                         //SECONDARY SALE (RV)	Sales Order Value
//                           elseif($partid==8)
//                         {

                            
//                   $outuser_idrv= DB::table('secondary_sale')
//                 //   ->join('secondary_sale','secondary_sale.order_id','=','user_sales_order_view.order_id')
//                         ->whereRaw("DATE_FORMAT(secondary_sale.date,'%Y-%m-%d')='$dk' ")
//                         ->where('secondary_sale.l3_id','=',$stateid)
//                         ->sum(DB::raw("rate*quantity"));  
//             $out[$stateid][$dk][$particularsname]=!empty($outuser_idrv)?$outuser_idrv:0;
        
//                         }
//                         //TOTAL OUTLET	Total Active Retailer
//                           elseif($partid==9)
//                         {
//                             $outtotal_outlet=DB::table('retailer')
//                             ->join('location_view','l5_id','=','retailer.location_id')
//                             ->where('l3_id','=',$stateid)
//                             ->where('retailer_status','=','1')
//                            //->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$dk'")
//                             ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$start_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$dk'")
//                             ->distinct('retailer.id')
//                             ->count('retailer.id');

//                 $out[$stateid][$dk][$particularsname]=!empty($outtotal_outlet)?$outtotal_outlet:0;
                      
//                         }
//                         //TARGETED OUTLET	28 (Fixed State Wise)
//                           elseif($partid==10)
//                         {
//                               $valueData=DB::table('_budget_target')
//                                 ->select('target_outlet')
//                                 ->where('state_id',$stateid)->first();
//                             $out[$stateid][$dk][$particularsname]=!empty($valueData->target_outlet)?$valueData->target_outlet:0;
                           
//                         }else{
//                             $out[$stateid][$dk][$particularsname]=1001;
//                         }
                        
//                     }
//                     }

                   
//         return view('reports.budget_target_status.ajax', [
//             'records' => $rt,
//              'datearray' => $datearray,
//             'year'=>$year,
//              'details' => $out   
//         ]);
//     }
//      else 
//      {
//         echo '<p class="alert-danger">No Data Found</p>';
//     }
// }
################# budget target status report update #####################
     public function budget_target_statusReport(Request $request)
{

    $company_id = Auth::user()->company_id;
    if ($request->ajax()) {
        $state_id = $request->area;
        $region = $request->region;
        $user = $request->user;

    $year = !empty($request->year)?$request->year:date('Y-m');
    $start_date =date('Y-m-d',strtotime($year));
                        $end_date= date("Y-m-t", strtotime($start_date));
                            $startTime = strtotime($start_date);
                            $endTime = strtotime($end_date);

    for ($currentDate = $startTime; $currentDate <= $endTime;  
                                    $currentDate += (86400)) { 
                                        
    $Store = date('Y-m-d', $currentDate); 
    $datearray[] = $Store; 
    } 
            $arra = [];
            $out =[];
    #$query1

$region_data_q = Location2::join('location_3','location_3.location_2_id','=','location_2.id')->where('location_2.status', 1)->where('location_2.company_id',$company_id);
if(isset($state_id))
{
$region_data_q->whereIn('location_3.id',$state_id);
}
if(isset($region))
{
$region_data_q->whereIn('location_2.id',$region);
}
$region_data = $region_data_q->select('location_2.name as region_name','location_2.id as region_id','location_3.id as state_id','location_3.name as state_name')->get();


$query1=DB::table('state_particular_view')
->select('state_id','state','partid','particularsname')
//->join('location_2','location_2.id','=','state_particular_view.l2_id')
->where('state_particular_view.status','1')
->where('company_id',$company_id);
    if(!empty($state_id))
    {
    $query1->whereIn('state_particular_view.state_id',$state_id);
    }
    if(!empty($region))
    {
    $query1->whereIn('state_particular_view.l2_id',$region);
    }
//dd($query1);

$rt=$query1->orderBy('state_particular_view.state_id','ASC')->orderBy('partid','ASC')->get();

$particularsnameData = DB::table('_target_particulars')->select('name','id')->orderBy('id','ASC')->get();
// dd($particularsnameData);

foreach($rt as $r=>$k)  //loop run 340 times
{ 

$stateid=$k->state_id;
$particularsname=$k->particularsname;
$partid=$k->partid;

// Retrieve details for (partid = 1,3,10)
$valueData=DB::table('_budget_target')
->select('manpower_budget','budget_user','target_outlet')
->where('state_id',$stateid)->where('company_id',$company_id)->first();

// Retrieve details for (partid = 2)
$total_user=DB::table('person')
    ->join('person_login','person_id','=','person.id')
    ->where('person.state_id','=',$stateid)
    ->where('person_status','=',1)
    ->where('person.company_id',$company_id)
    ->count('id');  

// Retrieve details for (partid = 4)
$total_rolewiseuser=DB::table('person')
    ->join('person_login','person_id','=','person.id')
    ->where('person.state_id','=',$stateid)
    ->where('person_status','=','1')
    ->where('person.company_id',$company_id)
    ->whereIn('role_id',[40,42,12,46])
    ->count('id'); 

     foreach($datearray as $dr=>$dk)
     {   
         
                //BUDGETED MANPOWER
                if($partid==1)
                {    
                    $out[$partid][$stateid.$dk]=!empty($valueData->manpower_budget)?$valueData->manpower_budget:0;
                }
                //ACTUAL MANPOWER
                elseif($partid==2)
                {    
                    $out[$partid][$stateid.$dk]=!empty($total_user)?$total_user:0;
                }

                //BUDGETED SO+SSO+ISR+JSO 
                elseif($partid==3)
                {
                    $out[$partid][$stateid.$dk]=!empty($valueData->budget_user)?$valueData->budget_user:0;
                }
                //ACTUAL SO+SSO+ISR Total User(SSO+SO+ISR+JSO Active)
                elseif($partid==4)
                {
                 
                    $out[$partid][$stateid.$dk]=!empty($total_rolewiseuser)?$total_rolewiseuser:0;
               // $out[$stateid][$dk][$particularsname]=1002;
                }
                //TARGETED OUTLET   28 (Fixed State Wise)
                elseif($partid==10)
                {
                    $out[$partid][$stateid.$dk]=!empty($valueData->target_outlet)?$valueData->target_outlet:0;
                   
                }else{
                    $out[$partid][$stateid.$dk]='-';
                }
                
            }
        }

        // ATTENDANCE MARKED SO+SSO+ISR+JSO (for partid=5)
        $attendancemarkeduser1 =DB::table('user_daily_attendance')
                ->join('person','person.id','=','user_daily_attendance.user_id')
                ->join('person_login','person_id','=','person.id')
                 ->whereIn('person.role_id',[40,42,12,46])
                ->where('person_status','=',1)
                ->where('user_daily_attendance.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$year'");
                if(!empty($state_id))
                {
                 $attendancemarkeduser1->whereIn('person.state_id',$state_id);
                }
                $out[5] = $attendancemarkeduser1->select(DB::raw('DATE(work_date) as work_date'), DB::raw('count(user_daily_attendance.user_id) as count'),DB::raw("CONCAT(state_id,DATE_FORMAT(`work_date`,'%Y-%m-%d')) as keyID"))
                ->groupBy('person.state_id','keyID')
                ->pluck('count','keyID');

              //dd($attendancemarkeduser1);

          // ACTUAL WORKED SO+SSO+ISR+JSO  (for partid=6)  
         $attendancemarkeduser_notleave1=DB::table('user_daily_attendance')
                    ->join('person','person.id','=','user_daily_attendance.user_id')
                    ->join('person_login','person_id','=','person.id')
                    ->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$year'")
                    ->whereIn('person.role_id',[40,42,12,46])
                    ->whereIn('work_status',[1,9,5,15,19])
                    ->where('user_daily_attendance.company_id',$company_id)
                    ->where('person_status','=',1);
                    if(!empty($state_id))
                    {
                    $attendancemarkeduser_notleave1->whereIn('person.state_id',$state_id);
                    }
                    $out[6] = $attendancemarkeduser_notleave1->select(DB::raw('DATE(work_date) as work_date'), DB::raw('count(user_daily_attendance.user_id) as count'),DB::raw("CONCAT(state_id,DATE_FORMAT(`work_date`,'%Y-%m-%d')) as keyID"))
                    ->groupBy('person.state_id','keyID')
                    ->pluck('count','keyID');

           // PRODUCTIVE CALLS   (for partid=7)      
          $productive_calls1=DB::table('user_sales_order')
                    ->join('person','person.id','=','user_sales_order.user_id')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$year'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('call_status','=',1);
                    if(!empty($state_id))
                    {
                     $productive_calls1->whereIn('person.state_id',$state_id);
                    }
                    $out[7] = $productive_calls1->groupBy('person.state_id','date')
                    ->pluck(DB::raw('count(DISTINCT user_sales_order.order_id) as count'),DB::raw("CONCAT(state_id,date)"));
        
         // SECONDARY SALE (RV)  (for partid=8)         
        $outuser_idrv1= DB::table('secondary_sale')
                    ->whereRaw("DATE_FORMAT(secondary_sale.date,'%Y-%m')='$year'")
                    ->where('secondary_sale.company_id',$company_id);
                    if(!empty($state_id))
                    {
                     $outuser_idrv1->whereIn('l3_id',$state_id);
                    }
                    $out[8] = $outuser_idrv1->groupBy('l3_id','date')
                    ->pluck(DB::raw('sum(rate*quantity) as count'),DB::raw("CONCAT(l3_id,date)"));

          // TOTAL OUTLET  (for partid=9)        
         $outtotal_outlet1=DB::table('retailer')
                    ->join('location_view','l5_id','=','retailer.location_id')
                    ->where('retailer_status','=',1)
                    ->where('retailer.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m')='$year'");
                    if(!empty($state_id))
                    {
                     $outtotal_outlet1->whereIn('l3_id',$state_id);
                    }
                    $out[9] = $outtotal_outlet1->groupBy('l3_id','created_on')
                    ->pluck(DB::raw('count(retailer.id) as count'),DB::raw("CONCAT(l3_id,DATE_FORMAT(`created_on`,'%Y-%m-%d'))"));
           

      return view('reports.budget_target_status.ajax', [
                    'records' => $rt,
                    'region' => $region_data,
                    'particularsnameData' => $particularsnameData,
                    'datearray' => $datearray,
                    'year'=> $year,
                    'details' => $out   
                ]);
            }
            else 
            {
                echo '<p class="alert-danger">No Data Found</p>';
            }
}

############## budet target status report status report ends here ################################
 public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::where('person_id_senior',$code)
            ->select('id as user_id')->get();
            $num = count($details);  
            if($num>0)
            {
                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="")
                    {
                        //$product = collect([1,2,3,4]);
                        Session::push('juniordata', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        $this->getJuniorUser($res2->user_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="")
                    {
                        Session::push('juniordata', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            return 1;
    } 
    
    #Multiple record on change on Region
    public function getAny(Request $request)
    {
        if ($request->ajax() && !empty($request->id) && !empty($request->type) && !empty($request->master)) {
            $id = $request->id;
            $type = $request->type;
            $master = $request->master;
            $data['code'] = 200;

            $table = $master . '_' . $type;
            $ptable_id = $master . '_' . ($type - 1) . '_id';

            $query = DB::table($table)
                ->where($ptable_id, $id)
                ->where('status', '=', 1)
                ->pluck('name', 'id');


            $data['result'] = $query;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    #date_wise_product_wise_report starts here
    public function date_wise_product_wise_report(Request $request)
    {

        $year = $request->year;
        $state = $request->area;
        $region = $request->region;
        $user = $request->user;

        $start_date = date('Y-m-d',strtotime($year));
                      $end_date= date("Y-m-t", strtotime($start_date));
                      $startTime = strtotime($start_date);
                      $endTime = strtotime($end_date);

        for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
        {                                       
            $Store = date('Y-m-d', $currentDate); 
            $datearray[] = $Store; 
        } 
        $product_id = [10,7,1,4,11,12,5];
        $new_arr=[];
        $catalog_data = DB::table('catalog_0')->where('status',1)->whereIn('id',$product_id)->orderBy('sequence','ASC')->pluck('name','id');
        $first_part_data = Person::join('location_view','location_view.l3_id','=','person.state_id')->join('person_login','person_login.person_id','=','person.id')->select('person.id as user_id','l3_id as state_id','l3_name as state_name','l2_name as zone_name','region_txt',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))->where('person_status',1);
        if(!empty($state))
        {
            $first_part_data->whereIn('person.state_id',$state);
        }
        if(!empty($region))
        {
            $first_part_data->whereIn('l2_id',$region);
        }
        if(!empty($user))
        {
            $first_part_data->whereIn('person.id',$user);
        }
        $first_part = $first_part_data->orderBy('l3_name','ASC')->groupBy('person.id')->get();

        $product_wise_data = DB::table('secondary_sale')
            ->join('catalog_product','catalog_product.id','=','secondary_sale.product_id')
            ->select('date','user_id','c0_id',DB::raw("SUM(quantity*rate) as total_price"), DB::raw("SUM(quantity/quantity_per_case) as total_cases"), DB::raw("COUNT(Distinct order_id) as total_row"))
            ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$year'")
            ->whereIn('c0_id',$product_id)
            ->groupBy('c0_id','user_id','date');
            if(!empty($state))
            {
                $product_wise_data->whereIn('l3_id',$request->area);
            }
            if(!empty($region))
            {
                $product_wise_data->whereIn('l2_id',$region);
            }
            if(!empty($user))
            {
                $product_wise_data->whereIn('user_id',$user);
            }
            $product_wise = $product_wise_data->get();

        // $total_price_data = DB::table('secondary_sale')
        //     ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$year'")
        //     ->whereIn('c0_id',$product_id)
        //     ->groupBy('c0_id','user_id','date');
        //     if(!empty($state))
        //     {
        //         $total_price_data->whereIn('l3_id',$request->area);
        //     }
        //     if(!empty($user))
        //     {
        //         $total_price_data->whereIn('user_id',$user);
        //     }
        // $total_price =$total_price_data->pluck(DB::raw("SUM(quantity*rate) as total_price"),DB::raw("concat(user_id,date,c0_id)"));
        // dd($total_price);
        // $total_cases_data = DB::table('secondary_sale')
        //     ->join('catalog_product','catalog_product.id','=','secondary_sale.product_id')
        //     ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$year'")
        //     ->whereIn('c0_id',$product_id)
        //     ->groupBy('c0_id','user_id','date');
        //     if(!empty($state))
        //     {
        //         $total_cases_data->whereIn('l3_id',$request->area);
        //     }
        //     if(!empty($user))
        //     {
        //         $total_cases_data->whereIn('user_id',$user);
        //     }
        // $total_cases =$total_cases_data->pluck(DB::raw("SUM(quantity/quantity_per_case"),DB::raw("concat(user_id,date,c0_id)"));

        // $total_row_data = DB::table('secondary_sale')
        //     ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$year'")
        //     ->whereIn('c0_id',$product_id)
        //     ->groupBy('c0_id','user_id','date');
        //     if(!empty($state))
        //     {
        //         $total_row_data->whereIn('l3_id',$request->area);
        //     }
        //     if(!empty($user))
        //     {
        //         $total_row_data->whereIn('user_id',$user);
        //     }
        // $total_row =$total_row_data->pluck(DB::raw("COUNT(Distinct order_id) as total_row"),DB::raw("concat(user_id,date,c0_id)"));
     
        foreach ($product_wise as $product_data => $product_value) 
        {
            $c0_id = $product_value->c0_id;
            $date = $product_value->date;
            $user_id = $product_value->user_id;
            $new_arr[$user_id.$date][$c0_id]['total_price'] = $product_value->total_price;
            $new_arr[$user_id.$date][$c0_id]['total_cases'] = $product_value->total_cases;
            $new_arr[$user_id.$date][$c0_id]['total_row'] = $product_value->total_row;
            
        }


    return view('reports.product_wise_sale_report.ajax', [
                "datearray"=>$datearray,
                "first_part"=>$first_part,
                "product_wise_data"=>$new_arr,
                "year"=>$year,
                'product_id'=>$product_id,
                'catalog_data'=>$catalog_data,
                ]);
    }
    #date_wise_product_wise_report ends here

    # dailyReportingReport starts here 
    public function dailyReportingReport(Request $request)
    {
        if ($request->ajax() ) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
	    	$array = array(99,100,101,102); // for oyster


            
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50 || $this->without_junior == 0)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }
            // dd($datasenior);
            $data1 = DB::table('daily_reporting')
            // ->leftJoin('_working_with','daily_reporting.working_with','=','_working_with.id')
            ->join('person','person.id','=','daily_reporting.user_id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('location_view','location_view.l6_id','=','person.town_id')
            ->select('person_id_senior','person.mobile','daily_reporting.working_with as working_with_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l3_name','l4_name','l5_name','l6_name','l1_name','l2_name','emp_code','rolename AS role_name',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') AS time"),DB::raw("DATE_FORMAT(work_date,'%d-%m-%Y') AS date"),'work_status','work_date','person.id AS user_id','remarks','attn_address')
            ->where('daily_reporting.company_id',$company_id)
            ->where('person.company_id',$company_id)
            ->where('l1_company_id',$company_id)
            ->where('l2_company_id',$company_id)
            ->where('l3_company_id',$company_id)
            ->where('l4_company_id',$company_id)
            ->where('l5_company_id',$company_id)
            ->where('l6_company_id',$company_id)
            ->whereRaw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d') <='$to_date'");
            if($login_user == 2833){
                $data1->whereNotIn('person.state_id',$array);		
             }
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('l6_id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('user_id', $user);
            }
            $user_records = $data1->where('person_status',1)->groupBy('user_id','work_date','daily_reporting.id')->get();

            $person_name = DB::table('person')
                        ->where('company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as working_with_name"),'id');
            // dd($user_records);
            $user_record=[];
            foreach ($user_records as $key => $value) {
                $user_id=$value->user_id;
                $working_with_id=$value->working_with_id;
                $work_date=$value->work_date;
                $in=$user_id.$work_date;
                if($working_with_id == 0)
                {
                    $working_with_name = "SELF";
                }
                else
                {

                    $explode_working_with_id = explode(',',$value->working_with_id);
                    $implodeArray  = array();
                    foreach ($explode_working_with_id as $ekey => $evalue) {
                        $implodeArray[] = !empty($person_name[$evalue])?$person_name[$evalue]:'';
                    }

                    $working_with_name = implode(",",$implodeArray);
                }

                $user_record[$in]['user_name']=$value->user_name;
                $user_record[$in]['user_id']=$value->user_id;
                $user_record[$in]['l1_name']=$value->l1_name;
                $user_record[$in]['l2_name']=$value->l2_name;
                $user_record[$in]['l3_name']=$value->l3_name;
                $user_record[$in]['l4_name']=$value->l4_name;
                $user_record[$in]['l5_name']=$value->l5_name;
                $user_record[$in]['l6_name']=$value->l6_name;
                $user_record[$in]['emp_code']=$value->emp_code;
                $user_record[$in]['person_id_senior']=$value->person_id_senior;
                $user_record[$in]['mobile']=$value->mobile;
                $user_record[$in]['role_name']=$value->role_name;
                $user_record[$in]['date']=$value->date;
                $user_record[$in]['time']=$value->time;
                $user_record[$in]['working_with']=$working_with_name;
                $user_record[$in]['work_status']=$value->work_status;
                $user_record[$in]['remarks']=$value->remarks;
                $user_record[$in]['attn_address']=$value->attn_address;
                // $user_record[$in]['remarks']=
            }

            // dd($user_record);
            return view('reports.user-daily-reporting.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }
    #ends here

    #time-sale-report starts here
    public function time_sale_report(Request $request)
    {
            $company_id = Auth::user()->company_id;
            $state = $request->location_3;
            $hq = $request->location_5;
            $town = $request->location_6;
            $location_4 = $request->location_4;
            $role = $request->role;
            $user_id_id = $request->user;
            $month = !empty($request->month)?$request->month:date('Y-m');

            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            // dd($from_date,$to_date);
            $table_name = TableReturn::table_return($from_date,$to_date);
            // added by karan
            if(!empty($user_id_id))
            {
                Session::forget('juniordata');
                // $login_user=Auth::user()->id;
                $user=self::getJuniorUser($user_id_id);
                $user = $request->session()->get('juniordata');
                if(empty($user))
                {
                    $user[]=$user_id_id;
                }
            }

            $m1=explode('-', $month);
            // dd($m1);
            $y=$m1[0];
            $m2=$m1[1];
            if($m2<10)
            $m=ltrim($m2, '0');
            else
            $m=$m2;

            
            // $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

        // for($i = 1; $i <=  $total_days; $i++)
        // {
        // // add the date to the dates array
        // $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        // $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
        // }

        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);

        for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
        {                                       
            $Store = date('Y-m-d', $currentDate); 
            $datesArr[] = $Store; 
            $datesDisplayArr[] = $Store; 
        }
        $total_days=COUNT($datesArr);
        // dd($table_name);
        $person_query_data = Person::join('person_login','person_login.person_id','=','person.id')
            ->join('users','users.id','=','person.id')
            ->join('person_details','person_details.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('location_view','location_view.l3_id','=','person.state_id')
            ->select('l4_name','head_quater_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as role_name','_role.role_id as role_id','location_view.l3_name as state_name','person.id as user_id','person.emp_code as emp_code','person.mobile','person.person_id_senior','person_status','deleted_deactivated_on')
            ->where('person.company_id',$company_id)
            ->where('users.company_id',$company_id)
            ->where('l3_company_id',$company_id)
            ->where('person_status','=','1')
            ->where('is_admin','!=','1')
            ->groupBy('person.id');

        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_query_data->whereIn('l3_id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $person_query_data->whereIn('l4_id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_query_data->whereIn('l5_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_query_data->whereIn('l6_id', $location_6);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_query_data->whereIn('person.role_id', $role);
        }
        if(!empty($state))
        {
            $person_query_data->whereIn('person.state_id',$state);
        }
         if(!empty($hq))
        {
            $person_query_data->whereIn('person.head_quater_id',$hq);
        }
        if(!empty($location_4))
        {
            $person_query_data->whereIn('l4_id',$location_4);
        }
            if(!empty($town))
        {
            $person_query_data->whereIn('person.town_id',$town);
        }
          if(!empty($role))
        {
            $person_query_data->whereIn('person.role_id',$role);
        }
        if(!empty($user))
        {
            $person_query_data->whereIn('person.id',$user);
        }
        $person_query = $person_query_data->get()->toArray();
        // dd($person_query);



        $deac_person_query_data = Person::join('person_login','person_login.person_id','=','person.id')
            ->join('users','users.id','=','person.id')
            ->join('person_details','person_details.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('location_view','location_view.l3_id','=','person.state_id')
            ->select('l4_name','head_quater_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as role_name','_role.role_id as role_id','location_view.l3_name as state_name','person.id as user_id','person.emp_code as emp_code','person.mobile','person.person_id_senior','person_status','deleted_deactivated_on')
            ->whereRaw("DATE_FORMAT(deleted_deactivated_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(deleted_deactivated_on,'%Y-%m-%d')<='$to_date'")
             // ->whereRaw("DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m')='$month'")
            ->where('person.company_id',$company_id)
            ->where('l3_company_id',$company_id)
            ->where('person_status','!=','1')
            ->where('is_admin','!=',1)
            ->groupBy('person.id')
            ->get()->toArray();


        $person_query = array_merge($person_query,$deac_person_query_data);

        $location_5 = DB::table('person')
                      ->join('person_login','person_login.person_id','=','person.id')  
                      ->join('location_5','location_5.id','=','person.head_quater_id')
                      ->where('person.company_id',$company_id)  
                      ->pluck('location_5.name','person.id');

         $location_6 = DB::table('person')
                      ->join('person_login','person_login.person_id','=','person.id')  
                      ->join('location_6','location_6.id','=','person.town_id')
                      ->where('person.company_id',$company_id)  
                      ->pluck('location_6.name','person.id');              

        // dd($location_5);              

        $upto_check_in_data = DB::table('user_daily_attendance')
                        ->where('company_id',$company_id)
                        ->groupBy('user_id')
                        ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
                        ->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='09:30:00'");
        if(!empty($user))
        {
            $upto_check_in_data->whereIn('user_id',$user);
        }
        $upto_check_in = $upto_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

        $count_total_att_data = DB::table('user_daily_attendance')
                            ->where('company_id',$company_id)
                            ->groupBy('user_id')
                            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'");

        if(!empty($user))
        {
            $count_total_att_data->whereIn('user_id',$user);
        }
        $count_total_att = $count_total_att_data->pluck(DB::raw("COUNT(DISTINCT order_id) AS DATA"),"user_id");

        $upto_check_out_data = DB::table('check_out')
                            ->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
                            ->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='21:30:00'")
                            ->groupBy('user_id');
        if(!empty($user))
        {
            $upto_check_out_data->whereIn('user_id',$user);
        }
        $upto_check_out = $upto_check_out_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

        $first_time_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('work_date','user_id');
        if(!empty($user))
        {
            $first_time_data->whereIn('user_id',$user);
        }
        $first_time = $first_time_data->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

        $last_time_data = DB::table('check_out')->groupBy('work_date','user_id');
        if(!empty($user))
        {
            $last_time_data->whereIn('user_id',$user);
        }
        $last_time = $last_time_data->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));


        $first_address_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('work_date','user_id');
        if(!empty($user))
        {
            $first_time_data->whereIn('user_id',$user);
        }
        $first_address = $first_address_data->pluck('track_addrs',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

        $last_address_data = DB::table('check_out')->groupBy('work_date','user_id');
        if(!empty($user))
        {
            $last_address_data->whereIn('user_id',$user);
        }
        $last_address = $last_address_data->pluck('attn_address',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));



        // user_wise and date_wise data  starts here 
        $sale_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('user_id','date')
                    ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

        $sale_data_working_town = DB::table($table_name)
                    ->join('location_7','location_7.id','=',$table_name.'.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('user_id','date')
                    ->pluck(DB::raw("group_concat(distinct location_6.name ) as l6"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));            

        $primary_sale_data = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$from_date'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->groupBy('created_person_id','sale_date')
                    ->pluck(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as sale_value"),DB::raw("CONCAT(created_person_id,DATE_FORMAT(sale_date,'%Y-%m-%d'))"));  

         $primary_sale_data_working_town = DB::table('user_primary_sales_order')
                    ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                    ->join('location_6','location_6.id','=','dealer.town_id')
                    ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->groupBy('created_person_id','sale_date')
                    ->pluck(DB::raw("group_concat(distinct location_6.name ) as l6"),DB::raw("CONCAT(created_person_id,DATE_FORMAT(sale_date,'%Y-%m-%d'))"));            
                    // dd($sale_data_working_town);

        $travelling_expense_data = DB::table('travelling_expense_bill')
                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(travellingDate,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                    ->where('travelling_expense_bill.company_id',$company_id)
                    ->groupBy('user_id','travellingDate')
                    ->pluck(DB::raw("SUM(total) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(travellingDate,'%Y-%m-%d'))"));            

                 // dd($primary_sale_data);             

        $total_call_data = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('call_status',1)
                    ->groupBy('user_id','date')
                    ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

        $total_t_call_data = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('user_id','date')
                    ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

                    // dd($total_t_call_data);
        // user_wise and date_wise data  ends here 

        // user_wise total starts here 
        $sale_data_grand = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('user_id')
                    ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(user_id)"));

        $primary_sale_data_grand = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->groupBy('created_person_id')
                    ->pluck(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as sale_value"),DB::raw("CONCAT(created_person_id)")); 

        $travelling_expense_data_grand = DB::table('travelling_expense_bill')
                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(travellingDate,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                    ->where('travelling_expense_bill.company_id',$company_id)
                    ->groupBy('user_id')
                    ->pluck(DB::raw("SUM(total) as sale_value"),DB::raw("CONCAT(user_id)"));                       

        $total_call_data_grand = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('call_status',1)
                    ->groupBy('user_id')
                    ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id)"));

        $total_call_t_data_grand = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('user_id')
                    ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id)"));
        // user_wise total ends here 

        // date wise total start here 
        $sale_data_grand_date_filt = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('date');
                     if(!empty($user))
                        {
                            $sale_data_grand_date_filt->whereIn($table_name.'.user_id',$user);
                        }
        $sale_data_grand_date = $sale_data_grand_date_filt->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(date)"));

        $primary_sale_data_grand_date_filt = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->groupBy('sale_date');
                     if(!empty($user))
                        {
                            $primary_sale_data_grand_date_filt->whereIn('user_primary_sales_order.created_person_id',$user);
                        }
        $primary_sale_data_grand_date = $primary_sale_data_grand_date_filt->pluck(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as sale_value"),DB::raw("CONCAT(sale_date)"));  

        $travelling_expense_data_grand_date_filt = DB::table('travelling_expense_bill')
                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(travellingDate,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                    ->where('travelling_expense_bill.company_id',$company_id)
                    ->groupBy('travellingDate');
                     if(!empty($user))
                        {
                            $travelling_expense_data_grand_date_filt->whereIn('travelling_expense_bill.user_id',$user);
                        }
        $travelling_expense_data_grand_date = $travelling_expense_data_grand_date_filt->pluck(DB::raw("SUM(total) as sale_value"),DB::raw("CONCAT(travellingDate)"));                       

        $total_call_data_grand_date_filt = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where('call_status',1)
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('date');
                     if(!empty($user))
                        {
                            $total_call_data_grand_date_filt->whereIn($table_name.'.user_id',$user);
                        }
        $total_call_data_grand_date =   $total_call_data_grand_date_filt->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(date)"));

        $total_call_t_data_grand_date_filt = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('date');
                 if(!empty($user))
                    {
                        $total_call_t_data_grand_date_filt->whereIn($table_name.'.user_id',$user);
                    }
        $total_call_t_data_grand_date =  $total_call_t_data_grand_date_filt->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(date)"));
                    // dd($total_call_t_data_grand_date);
        // date wise total ends here 

        // grand total starts here 
        $grand_sale_data_grand_date_filt = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->where($table_name.'.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->select(DB::raw("SUM(rate*quantity) as sale_value"));
                    if(!empty($user))
                        {
                            $grand_sale_data_grand_date_filt->whereIn($table_name.'.user_id',$user);
                        }
        $grand_sale_data_grand_date = $grand_sale_data_grand_date_filt->first();

        $grand_primary_sale_data_grand_date_filt = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                    ->select(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as sale_value"));
                    if(!empty($user))
                        {
                            $grand_primary_sale_data_grand_date_filt->whereIn('user_primary_sales_order.created_person_id',$user);
                        }
        $grand_primary_sale_data_grand_date = $grand_primary_sale_data_grand_date_filt->first();  

        $grand_travelling_expense_data_grand_date_filt = DB::table('travelling_expense_bill')
                    ->where('travelling_expense_bill.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(travellingDate,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                    ->select(DB::raw("SUM(total) as sale_value"));
                    if(!empty($user))
                        {
                            $grand_travelling_expense_data_grand_date_filt->whereIn('travelling_expense_bill.user_id',$user);
                        }
        $grand_travelling_expense_data_grand_date = $grand_travelling_expense_data_grand_date_filt->first();                      

        $grand_total_call_data_grand_date_filt = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('call_status',1)
                    ->select(DB::raw("COUNT(order_id) as pc"));
                     if(!empty($user))
                        {
                            $grand_total_call_data_grand_date_filt->whereIn($table_name.'.user_id',$user);
                        }
                  $grand_total_call_data_grand_date = $grand_total_call_data_grand_date_filt->first();

        $grand_total_call_t_data_grand_date_filt = DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                    ->where($table_name.'.company_id',$company_id)
                    ->select(DB::raw("COUNT(order_id) as tc"));
                     if(!empty($user))
                        {
                            $grand_total_call_t_data_grand_date_filt->whereIn($table_name.'.user_id',$user);
                        }
        $grand_total_call_t_data_grand_date = $grand_total_call_t_data_grand_date_filt->first();
        // grand total ends here 

        $dealer_count_user_wise = DB::table('dealer_location_rate_list')
        						->where('company_id',$company_id)
        						->groupBy('user_id')
        						->pluck(DB::raw("COUNT(DISTINCT dealer_id) as dealer_id"),'user_id');

		$retailer_count_user_wise = DB::table('dealer_location_rate_list')
								->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
        						->where('dealer_location_rate_list.company_id',$company_id)
        						->groupBy('dealer_location_rate_list.user_id')
        						->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),'dealer_location_rate_list.user_id');
		$date = date('Y-m-d');
		$retailer_count_added_per_day = DB::table('retailer')
                    				// ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$date'")
									->where('company_id',$company_id)
									->groupBy('created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"))
									->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),DB::raw("CONCAT(created_by_person_id,date_format(created_on,'%Y-%m-%d')) as data"));

		$retailer_count_added_month = DB::table('retailer')
                    				// ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$date'")
									->where('company_id',$company_id)
                                    ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(created_on,'%Y-%m-%d')<='$to_date'")
                    				// ->whereRaw("DATE_FORMAT(created_on,'%Y-%m')='$month'")
									->groupBy('created_by_person_id')
									->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),'created_by_person_id');
									// dd($retailer_count_added_per_day);

		$retailer_count_added_per_day_on = DB::table('retailer')
                    				// ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$date'")
									->where('company_id',$company_id)
									->groupBy('created_by_person_id','created_on')
									->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),DB::raw("CONCAT(created_on)"));

        return view('reports.time-report-sale.ajax', [
             	'records' => $person_query,
                'month' => $month,
                'datesArr'=>$datesArr,
                'location_5'=>$location_5,
                'location_6'=>$location_6,
                'datesDisplayArr'=>$datesDisplayArr,
                'total_days'=>$total_days,
                'first_time'=>$first_time,
                'last_time'=>$last_time,
                'upto_check_in' =>$upto_check_in,
                'upto_check_out'=>$upto_check_out,
                'count_total_att'=>$count_total_att,
                'total_call_data'=> $total_call_data,
                'sale_data'=> $sale_data,
                'sale_data_working_town'=> $sale_data_working_town,
                'primary_sale_data'=> $primary_sale_data,
                'primary_sale_data_working_town'=> $primary_sale_data_working_town,
                'travelling_expense_data'=> $travelling_expense_data,
                'total_call_data_grand'=> $total_call_data_grand,
                'sale_data_grand'=> $sale_data_grand,
                'primary_sale_data_grand'=> $primary_sale_data_grand,
                'travelling_expense_data_grand'=> $travelling_expense_data_grand,
                'total_t_call_data'=> $total_t_call_data,
                'total_call_t_data_grand'=> $total_call_t_data_grand,
                'sale_data_grand_date'=> $sale_data_grand_date,
                'primary_sale_data_grand_date'=> $primary_sale_data_grand_date,
                'travelling_expense_data_grand_date'=> $travelling_expense_data_grand_date,
                'total_call_data_grand_date'=> $total_call_data_grand_date,
                'total_call_t_data_grand_date'=> $total_call_t_data_grand_date,
                'grand_sale_data_grand_date'=> $grand_sale_data_grand_date,
                'grand_primary_sale_data_grand_date'=> $grand_primary_sale_data_grand_date,
                'grand_travelling_expense_data_grand_date'=> $grand_travelling_expense_data_grand_date,
                'grand_total_call_data_grand_date'=> $grand_total_call_data_grand_date,
                'grand_total_call_t_data_grand_date'=> $grand_total_call_t_data_grand_date,
				'dealer_count_user_wise'=> $dealer_count_user_wise,
				'retailer_count_user_wise'=> $retailer_count_user_wise,
				'retailer_count_added_per_day'=> $retailer_count_added_per_day,
				'retailer_count_added_per_day_on'=> $retailer_count_added_per_day_on,
				'retailer_count_added_month'=> $retailer_count_added_month,
				'first_address'=> $first_address,
				'last_address'=> $last_address,
            ]);
    }
    #time-sale-report ends here

    #user_expense_report starts here 
    public function user_expense_report(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $state = $request->state;
        $user = $request->user;

        $expense_query_data = DB::table('user_expense_report')->join('_travelling_mode','_travelling_mode.id','=','user_expense_report.travelling_mode_id')->join('person','person.id','=','user_expense_report.person_id')->join('person_login','person_login.person_id','=','person.id')->join('location_3','location_3.id','=','person.state_id')->join('_role','_role.role_id','=','person.role_id')->select('location_3.name as state_name','_travelling_mode.mode as travelling_mode','rent',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'rolename','total_calls','travelling_allowance','drawing_allowance','other_expense','travelling_mode_id','start_journey','end_journey','user_expense_report.person_id','submit_date','submit_time','remarks','image_name1','image_name2','image_name3','expense_date')->where('person_status',1)->whereRaw("submit_date>='$from_date' AND submit_date<='$to_date'");
        
        if(!empty($state))
        {
            $expense_query_data->whereIn('person.state_id',$state);
        }
        if(!empty($user))
        {
            $expense_query_data->whereIn('person.id',$user);
            
        }
        $expense_query = $expense_query_data->get();
        return view('reports.user-expense-report.ajax',['expense_query'=>$expense_query]);
    }
    #user_expense_report ends here 

    #userSalesReport starts here 
    public function userSalesReport(Request $request)
    {
        if ($request->ajax()) 
        {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $town = $request->town;
            $distributor = $request->distributor;
            $beat = $request->beat;
            $user_id = $request->user_id;
            $product=$request->product;
            $call_status = $request->call_status;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $arr = [];
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

            $query_data = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                        ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                        ->join('location_7','location_7.id','=','user_sales_order.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('location_3','location_3.id','=','location_4.location_3_id')

                        ->select('person.emp_code','user_sales_order.reason as non_productive_reason_id','user_sales_order.remarks','person.mobile','_role.rolename as role_name','dealer.id as dealer_id','person.id as user_id','retailer.id as retailer_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'dealer.name as dealer_name',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS date"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') AS date_cus"),'user_sales_order.time','user_sales_order.order_id','user_sales_order.call_status','location_6.name as l6_name','location_5.name as l5_name','location_4.name as l4_name','location_3.id as l3_id','location_3.name as l3_name','retailer.name as retailer_name','retailer.other_numbers as retailer_other_number','retailer.landline as retailer_landline','user_sales_order.discount','user_sales_order.amount as order_amount','user_sales_order.discount_type','user_sales_order.total_sale_value','person.person_id_senior as senior_id','user_sales_order.image_name','location_7.name as l7_name')
                        ->where('user_sales_order.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('user_sales_order.order_id');



            // dd($check);
            // $query_data =DB::table('user_sales_order_view')
            //     // ->join('user_sales_order_details_view','user_sales_order_details_view.order_id','=','user_sales_order_view.order_id')
            //     ->select('emp_code','non_productive_reason_id','remarks','mobile','role_name','dealer_id','user_id','retailer_id','user_name AS user_name','dealer_name',DB::raw("DATE_FORMAT(date,'%d-%m-%Y') AS date"),DB::raw("DATE_FORMAT(date,'%Y-%m-%d') AS date_cus"),'time','user_sales_order_view.order_id','call_status','l6_name','l5_name','l4_name','l3_id','l3_name','retailer_name','retailer_other_number','retailer_landline','discount','amount as order_amount','discount_type','total_sale_value','senior_id','image_name')
            //     ->where('company_id',$company_id)
            //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
            //     ->groupBy('user_sales_order_view.order_id');


           if(!empty($product))
           {
                $query_data->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereIn('user_sales_order_details.product_id',$product);
           }

            if(!empty($datasenior))
            {
                $query_data->whereIn('user_id',$datasenior);
            }
            if(!empty($user_id))
            {   
                $query_data->whereIn('user_id',$user_id);
            }
            if(!empty($region))
            {
                $query_data->whereIn('l3_id',$region);
            }
            if(!empty($town))
            {
                $query_data->whereIn('l6_id',$town);
            }
            if(!empty($distributor))
            {
                $query_data->whereIn('dealer_id',$distributor);
            }
            if(!empty($beat))
            {
                $query_data->whereIn('location_id',$beat);
            }
            if(!empty($call_status))
            {
                $query_data->whereIn('call_status',$call_status);
            }

            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $query_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $query_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $query_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $query_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $query_data->whereIn('user_sales_order.dealer_id', $dealer);
            }
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $query_data->whereIn('user_id', $user);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $query_data->whereIn('person.role_id', $role);
            }


            $query=$query_data->orderBy('user_sales_order.date','ASC')->orderBy('user_sales_order.time','ASC')->get();
            // dd($query);
            $current_datre = date('Y-m-d');

            $datearray = array();
            $startTime = strtotime($from_date);
            $endTime = strtotime($to_date);

            for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
            {                                       
                $Store = date('Y-m-d', $currentDate); 
                $datearray[] = $Store; 
            }
            $product_percentage = array();
            
            // dd($datearray);
            foreach ($datearray as $key => $value) 
            {
                $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                                ->where('incentive_type',1)
                                ->where('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$value' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$value'")
                                // ->select()
                                ->get();

                foreach ($product_percentage_data as $keyi => $valuei) 
                {
                    $ans = $valuei->product_id.$valuei->state_id.$value;
                    $product_percentage[$ans] = $valuei->value_amount_percentage;
                }
            }
            // dd($product_percentage);
            
                                // ->pluck('value_amount_percentage',DB::raw("concat(product_id,state_id) as data"));
                    // dd($product_percentage);
            
            // dd($out);
            $non_productive_reason_name = DB::table("_no_sale_reason")->where('company_id',$company_id)->where('status',1)->groupBy('id')->pluck('name','id');
            $out=array();
            $proout=array();
           if (!empty($query)) 
           {
                foreach ($query as $k => $d) 
                {
                    $uid=$d->order_id;
                    if(empty($check)){
                    $proout = DB::table('user_sales_order_details')
                        ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                        ->leftJoin('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                        ->where('user_sales_order_details.order_id', $uid)
                        ->where('user_sales_order_details.company_id', $company_id)
                        ->select('user_sales_order_details.product_id','user_sales_order_details.quantity','user_sales_order_details.rate','catalog_product.name as product_name','user_sales_order_details.scheme_qty as weight');
                    }
                    else{
                    $proout = DB::table('user_sales_order_details')
                            ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                            ->leftJoin('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->where('user_sales_order_details.order_id', $uid)
                            ->where('user_sales_order_details.company_id', $company_id)
                            ->select('user_sales_order_details.product_id','user_sales_order_details.final_secondary_qty as quantity','user_sales_order_details.final_secondary_rate as rate','catalog_product.name as product_name','user_sales_order_details.scheme_qty as weight');
                    }
                    
                   // if(!empty($product))
                   // {
                   //      $proout->whereIn('product_id',$product);
                   // }
                    $out[$uid]=$proout->groupBy('user_sales_order_details.id')->get(); 
                }
            }


             $productWeight = DB::table('catalog_product')
                        ->where('company_id',$company_id)
                        ->pluck('weight','id');


            // dd($product_percentage);
            if(empty($check)){
                return view('reports.user_sale_report.ajax', [
                    'records' => $query,
                    'details' => $out,
                    'non_productive_reason_name'=>$non_productive_reason_name,
                    'product_percentage'=> $product_percentage,
                    'productWeight'=> $productWeight,
                    'company_id'=> $company_id,
                ]);
            }else{
                return view('reports.user_sale_report.Janakajax', [
                    'records' => $query,
                    'details' => $out,
                    'non_productive_reason_name'=>$non_productive_reason_name,
                    'product_percentage'=> $product_percentage,
                    'productWeight'=> $productWeight,
                    'company_id'=> $company_id,


                ]);
            }
        } 
        else 
        {
            echo '<p class="alert-danger">Data not Found</p>';
        }
    }
    #userSalesReport ends here 


     public function orderDetails(Request $request)
    {
        $order_id = $request->orderid;
        $company_id = Auth::user()->company_id;

    
        $data_return_query = DB::table('user_sales_order_details')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->select('user_sales_order_details.*','catalog_product.name as product_name',DB::raw("(rate*quantity) as amount"))
                            ->where('user_sales_order_details.order_id',$order_id)
                            ->where('user_sales_order_details.company_id',$company_id)
                            ->groupBy('user_sales_order_details.order_id','user_sales_order_details.product_id')
                            ->orderBy('user_sales_order_details.id','ASC');
                           
                
            $data_return = $data_return_query->get();

        if($data_return)
        {
            $data['code'] = 200;
            $data['data_return'] = $data_return;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        return json_encode($data);

    }

    public function orderDetailsUpdate(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        
        $product_id = $request->product_id;
        $order_id = $request->order_id;
        $quantity = $request->quantity;
        $rate = $request->rate;

        $case_qty = $request->case_qty;
        $case_rate = $request->case_rate;





       
    if(!empty($product_id)){

        foreach ($product_id as $key => $value) 
        {

            // for scheme amount 

            $query_data = DB::table('user_sales_order')
                        ->select('location_3.id as l3_id','user_sales_order_details.product_id','user_sales_order.date')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_7','location_7.id','=','user_sales_order.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('location_3','location_3.id','=','location_4.location_3_id')
                        ->where('user_sales_order_details.product_id',$value)
                        ->where('user_sales_order_details.company_id',$company_id)
                        ->where('user_sales_order_details.order_id',$order_id[$key])
                        ->first();


            $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                        ->where('incentive_type',1)
                        ->where('company_id',$company_id)
                        ->where('state_id',$query_data->l3_id)
                        ->where('product_id',$query_data->product_id)
                        ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$query_data->date' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$query_data->date'")
                        ->first();

                        // dd($product_percentage_data);


            if(!empty($product_percentage_data)){
                 $finalvalue = ($rate[$key]*$quantity[$key])*$product_percentage_data->value_amount_percentage/100;
            }else{
                $finalvalue = 0;
            }

            $finalfinalvalue[] = $rate[$key]*$quantity[$key] - $finalvalue;

            // for scheme amount end


            $update_query = DB::table('user_sales_order_details')
                        ->where('product_id',$value)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id[$key])
                        ->update([
                                    'quantity'=> $quantity[$key],
                                    'rate'=> $rate[$key],
                                    'case_rate'=> $case_rate[$key],
                                    'case_qty'=> $case_qty[$key],
                                  
                                ]);
        }
    }

    // dd($finalfinalvalue);

    $final_array_sum = array_sum($finalfinalvalue);

    $final_update_query = DB::table('user_sales_order')
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id[0])
                        ->update([
                                    'total_sale_value'=> $final_array_sum,
                                  
                                ]);


        if($final_update_query)
        {
            DB::commit();
            Session::flash('message', "Order Update successfully");
            Session::flash('alert-class', 'alert-success');

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect()->intended('user-sale');

    }

    // public function takeActionForOrder(Request $request)
    // {
    //     $id = $request->action_id; //order_id
    //     $module = $request->module;
    //     $table = $request->tab;
    //     $act = $request->act;
    //     $company_id = Auth::user()->company_id;
        
       

    //     if ($request->ajax() && !empty($id)) {
    //         DB::beginTransaction();
    //    $select = DB::table('user_sales_order_details')->where('order_id',$id)->get();

    //    foreach($select as $key => $value){
    //        $log_insertion = DB::table('user_sales_order_details_log')
    //                         ->insert(
    //                             [
    //                                 'order_id' => $value->order_id,
    //                                 'product_id' => $value->product_id,
    //                                 'rate' => $value->rate,
    //                                 // 'case_qty' => $value->case_qty,
    //                                 'quantity' => $value->quantity,
    //                                 // 'remaining_qty' => $value->remaining_qty,
    //                                 'scheme_qty' => $value->scheme_qty,
    //                                 'status' => $value->status,
    //                                 'sync_status' => $value->sync_status,
    //                             ]
    //                         );
    //    }

    //    $delete_sale_order = DB::table('user_sales_order')->where('order_id',$id)->delete();
    //    $delete_sale_order = DB::table('user_sales_order_details')->where('order_id',$id)->delete();



    //         if ($delete_sale_order) {
    //             #commit transaction
    //             DB::commit();
    //             $data['code'] = 200;
    //             $data['result'] = 'success';
    //             $data['message'] = 'success';
    //         } else {
    //             #rollback transaction
    //             DB::rollback();
    //             $data['code'] = 401;
    //             $data['result'] = 'fail';
    //             $data['message'] = 'Action can not be completed';
    //         }
    //     } else {
    //         #for unauthorized request
    //         $data['code'] = 401;
    //         $data['result'] = '';
    //         $data['message'] = 'unauthorized request';
    //     }
    //     return json_encode($data);
    // }

    #sales-man-secondary starts here
    public function sales_man_secondary_report(Request $request)
    {
        if($request->ajax())
        {
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $role = $request->role;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

             $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            // $secondary_man_report_query_data = DB::table('dealer_location_rate_list')
            // ->join('person','person.id','=','dealer_location_rate_list.user_id')
            // ->join('_role','_role.role_id','=','person.role_id')
            // ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
            // ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
            // ->join('user_sales_order','user_sales_order.user_id','=','person.id')
            // ->select('user_sales_order.amount AS total','user_sales_order.date as date','dealer.name as dealer_name','dealer_location_rate_list.dealer_id as dealer_id','rolename','l4_name as zone','l4_id as zone_id','user_sales_order.user_id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
            // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
            // ->where('person.company_id',$company_id)
            // ->groupBy('dealer_id','user_id')
            // ->orderBy('dealer.name','ASC')
            // ->orderBy('user_name','ASC');

            $scheme_sale = DB::table('user_sales_order')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('company_id',$company_id)
                            ->groupBy('dealer_id','user_id')
                            ->pluck(DB::raw("sum(total_sale_value) as total"),DB::raw("CONCAT(dealer_id,user_id) as concat"));



            $totalCall = DB::table('user_sales_order')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('company_id',$company_id)
                            ->groupBy('dealer_id','user_id')
                            ->pluck(DB::raw("COUNT(DISTINCT retailer_id,date) as totalCall"),DB::raw("CONCAT(dealer_id,user_id) as concat"));





            if(empty($check)){
            $secondary_man_report_query_data = DB::table('dealer')
                                            ->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id')
                                            ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                            ->join('person','person.id','=','user_sales_order.user_id')
                                            ->join('person_login','person_login.person_id','=','person.id')
                                            ->join('_role','_role.role_id','=','person.role_id')
                                            ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                                            ->select('person_id_senior','person.emp_code','person.mobile as mobile','l3_name','l4_name','l5_name','l6_name',DB::raw("sum(rate*quantity) as total"),'user_sales_order.date as date','dealer.name as dealer_name','user_sales_order.dealer_id as dealer_id','rolename','l6_name as zone','l6_id as zone_id','user_sales_order.user_id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("COUNT(DISTINCT retailer_id,user_sales_order.date) as productiveCall"))
                                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                            ->where('person.company_id',$company_id)
                                            ->where('user_sales_order.company_id',$company_id)
                                            ->where('person_status',1)
                                            ->where('dealer_status',1)
                                            ->groupBy('dealer_id','user_id')
                                            ->orderBy('dealer.name','ASC')
                                            ->orderBy('user_name','ASC');
            }
            else{
                $secondary_man_report_query_data = DB::table('dealer')
                                            ->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id')
                                            ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                            ->join('person','person.id','=','user_sales_order.user_id')
                                            ->join('person_login','person_login.person_id','=','person.id')
                                            ->join('_role','_role.role_id','=','person.role_id')
                                            ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                                            ->select('person_id_senior','person.emp_code',DB::raw("ROUND(sum(final_secondary_rate*final_secondary_qty),2) as total"),'user_sales_order.date as date','dealer.name as dealer_name','user_sales_order.dealer_id as dealer_id','rolename','l6_name as zone','l6_id as zone_id','user_sales_order.user_id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'l3_name','l4_name','l5_name','l6_name','person.mobile as mobile',DB::raw("COUNT(DISTINCT retailer_id,user_sales_order.date) as productiveCall"))
                                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                            ->where('person.company_id',$company_id)
                                            ->where('user_sales_order.company_id',$company_id)
                                            ->where('person_status',1)
                                            ->where('dealer_status',1)
                                            ->groupBy('dealer_id','user_id')
                                            ->orderBy('dealer.name','ASC')
                                            ->orderBy('user_name','ASC');
            }

            if(!empty($state))
            {
                $secondary_man_report_query_data->whereIn('location_view.l3_id',$state);
            }

            if(!empty($role))
            {
                $secondary_man_report_query_data->whereIn('person.role_id',$role);
            }

            if (!empty($datasenior)) 
            {
                $secondary_man_report_query_data->whereIn('person.id', $datasenior);
            }

            // if(!empty($user))
            // {
            //     $secondary_man_report_query_data->whereIn('person.id',$user);
            // }

            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $secondary_man_report_query_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $secondary_man_report_query_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $secondary_man_report_query_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $secondary_man_report_query_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $secondary_man_report_query_data->whereIn('dealer_id', $dealer);
            }
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $secondary_man_report_query_data->whereIn('person.id', $user);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $secondary_man_report_query_data->whereIn('person.role_id', $role);
            }
            $secondary_man_report_query = $secondary_man_report_query_data->get();
            // dd($secondary_man_report_query);
            
            return view('reports.sales-man-secondary.ajax', [
                    'records' => $secondary_man_report_query,
                    'scheme_sale' => $scheme_sale,
                    'company_id' => $company_id,
                    'totalCall' => $totalCall,
                ]);
        }
        else
        {
            echo '<p class="alert-danger">Data not Found</p>';
        } 
    }

    #sales-man-secondary ends here 


    #score_card_report starts here
    public function score_card_report(Request $request)
    {
        if($request->ajax())
        {
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $role = $request->role;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


            $effective_calls_data = DB::table('user_sales_order')
                               -> whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")
                               ->where('user_sales_order.company_id',$company_id)
                               ->where('user_sales_order.call_status',1)
                               ->groupBy('date','retailer_id')
                               ->pluck(DB::raw("COUNT(call_status) as count"),DB::raw("CONCAT(retailer_id,date)"));

            $catalog_product = DB::table('catalog_product')
                               ->whereIn('id',[4000,4001])
                               ->orderBy('catalog_product.id')
                               ->pluck('name','id');


            $stock_qty = DB::table('dealer_balance_stock')
                           ->join('catalog_product','catalog_product.id','dealer_balance_stock.product_id')
                           -> whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(dealer_balance_stock.submit_date_time,'%Y-%m-%d') <='$to_date'")
                           ->where('dealer_balance_stock.company_id',$company_id)
                           ->whereIn('product_id',[4000,4001])
                           ->groupBy('dealer_id')
                           ->orderBy('product_id')
                           ->pluck(DB::raw("SUM(stock_qty) as stock_qty"),DB::raw("CONCAT(dealer_id,product_id)"));

            $stock_value = DB::table('dealer_balance_stock')
                         //  ->join('catalog_product','catalog_product.id','dealer_balance_stock.product_id')
                           -> whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(dealer_balance_stock.submit_date_time,'%Y-%m-%d') <='$to_date'")
                           ->where('dealer_balance_stock.company_id',$company_id)
                           ->whereIn('product_id',[4000,4001])
                           ->groupBy('dealer_id')
                           ->orderBy('product_id')
                           ->pluck(DB::raw("SUM(stock_qty*mrp) as stock_value"),DB::raw("CONCAT(dealer_id)")); 

            $primary_order = DB::table('user_primary_sales_order')
                           ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','user_primary_sales_order.order_id')
                           -> whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")
                           ->where('user_primary_sales_order.company_id',$company_id)
                           ->groupBy('dealer_id','sale_date')
                           ->pluck(DB::raw("SUM(rate*quantity) as primary_value"),DB::raw("CONCAT(dealer_id,sale_date)"));

             $primary_order_rec_from = DB::table('user_primary_sales_order')
                            ->join('person','person.id','=','user_primary_sales_order.created_person_id')
                           -> whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")
                           ->where('user_primary_sales_order.company_id',$company_id)
                           ->groupBy('dealer_id','sale_date')
                           ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("CONCAT(dealer_id,sale_date)"));

             $user_sale_value = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                             ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("sum(rate*quantity) as total"),"user_id")
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id')
                            ->orderBy('user_sales_order.date','ASC')
                            ->orderBy('user_name','ASC')
                            ->pluck("total","user_id");

              $user_productive_calls = DB::table('user_sales_order')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id')
                            ->orderBy('user_sales_order.date','ASC')
                            ->pluck(DB::raw("COUNT(order_id) as count"),"user_id");              
               // dd($user_productive_calls);           

             $user_ret_count = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                             ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("COUNT(DISTINCT retailer_id) as rcount"),"user_id")
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id','user_sales_order.date')
                            ->orderBy('user_sales_order.date','ASC')
                            ->orderBy('user_name','ASC')
                            ->pluck("rcount","user_id");    

                            // dd($user_ret_count);  

                                                                                                                  


            $score_card_query_data = DB::table('user_sales_order')
                                    ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                    ->join('person','person.id','=','user_sales_order.user_id')
                                    ->join('person_login','person_login.person_id','=','person.id')
                                    ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                    ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                    ->select(DB::raw("sum(rate*quantity) as total"),DB::raw("COUNT(call_status) as call_status"),'user_sales_order.date as date','dealer.name as dealer_name','user_sales_order.dealer_id as dealer_id','l6_name as zone','l6_id as zone_id','user_sales_order.user_id as user_id','retailer.id as retailer_id','retailer.name as retailer_name','l4_name as hq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'user_sales_order.date as sale_date','l7_name as working_station','retailer.landline as mobile')
                                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                    ->where('person.company_id',$company_id)
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->where('person_status',1)
                                    ->where('dealer_status',1)
                                    ->groupBy('user_sales_order.retailer_id','user_sales_order.date')
                                    ->orderBy('user_sales_order.date','ASC')
                                    ->orderBy('user_name','ASC');

            if(!empty($state))
            {
                $score_card_query_data->whereIn('location_view.l3_id',$state);
            }

            if(!empty($role))
            {
                $score_card_query_data->whereIn('person.role_id',$role);
            }

            if(!empty($user))
            {
                $score_card_query_data->whereIn('person.id',$user);
            }
            $score_card_query = $score_card_query_data->get();
            // dd($score_card_query);
          
            
            return view('reports.score-card.ajax', [
                    'records' => $score_card_query,
                    'eff_calls' => $effective_calls_data,
                    'catalog_product' => $catalog_product,
                    'stock_qty' => $stock_qty,
                    'stock_value' => $stock_value,
                    'primary_order' => $primary_order,
                    'primary_order_rec_from' => $primary_order_rec_from,
                    'user_sale_value' => $user_sale_value,
                    'user_productive_calls' => $user_productive_calls,
                    'user_ret_count' => $user_ret_count,
                ]);
        }
        else
        {
            echo '<p class="alert-danger">Data not Found</p>';
        } 
    }

    #score_card_report ends here 



     #score_card_report starts here
    public function score_card_report_new(Request $request)
    {
        if($request->ajax())
        {
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $role = $request->role;
            $user = $request->user;
            $from_date = $request->from_date;
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            // dd($request->from_date);
            // $explodeDate = explode(" -", $request->date_range_picker);
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


            $effective_calls_data = DB::table('user_sales_order')
                               -> whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') ='$from_date'")
                               ->where('user_sales_order.company_id',$company_id)
                               ->where('user_sales_order.call_status',1)
                               ->groupBy('date','retailer_id')
                               ->pluck(DB::raw("COUNT(call_status) as count"),DB::raw("CONCAT(retailer_id,date)"));

            $catalog_product = DB::table('catalog_product')
                               ->whereIn('id',[4000,4001])
                               ->orderBy('catalog_product.id')
                               ->pluck('name','id');


            $stock_qty = DB::table('dealer_balance_stock')
                           ->join('catalog_product','catalog_product.id','dealer_balance_stock.product_id')
                           -> whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time,'%Y-%m-%d') ='$from_date'")
                           ->where('dealer_balance_stock.company_id',$company_id)
                           ->whereIn('product_id',[4000,4001])
                           ->groupBy('dealer_id')
                           ->orderBy('product_id')
                           ->pluck(DB::raw("SUM(stock_qty) as stock_qty"),DB::raw("CONCAT(dealer_id,product_id)"));

            $stock_value = DB::table('dealer_balance_stock')
                         //  ->join('catalog_product','catalog_product.id','dealer_balance_stock.product_id')
                           -> whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time,'%Y-%m-%d') ='$from_date'")
                           ->where('dealer_balance_stock.company_id',$company_id)
                           ->whereIn('product_id',[4000,4001])
                           ->groupBy('dealer_id')
                           ->orderBy('product_id')
                           ->pluck(DB::raw("SUM(stock_qty*mrp) as stock_value"),DB::raw("CONCAT(dealer_id)")); 

            // $primary_order = DB::table('user_primary_sales_order')
            //                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','user_primary_sales_order.order_id')
            //                -> whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') ='$from_date'")
            //                ->where('user_primary_sales_order.company_id',$company_id)
            //                ->groupBy('dealer_id','sale_date','created_person_id')
            //                ->pluck(DB::raw("SUM(rate*quantity) as primary_value"),DB::raw("CONCAT(dealer_id,sale_date,created_person_id)"));

            //  $primary_order_rec_from = DB::table('user_primary_sales_order')
            //                 ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
            //                -> whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') ='$from_date'")
            //                ->where('user_primary_sales_order.company_id',$company_id)
            //                ->groupBy('dealer_id','sale_date','created_person_id')
            //                ->pluck('name',DB::raw("CONCAT(dealer_id,sale_date,created_person_id)"));
            if(empty($check)){
             $user_sale_value = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                             ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("sum(rate*quantity) as total"),"user_id")
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id')
                            ->orderBy('user_sales_order.date','ASC')
                            ->orderBy('user_name','ASC')
                            ->pluck("total","user_id");
            }else{
                $user_sale_value = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("sum(final_secondary_rate*final_secondary_qty) as total"),"user_id")
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id')
                            ->orderBy('user_sales_order.date','ASC')
                            ->orderBy('user_name','ASC')
                            ->pluck("total","user_id");
            }

              $user_productive_calls = DB::table('user_sales_order')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id')
                            ->orderBy('user_sales_order.date','ASC')
                            ->pluck(DB::raw("COUNT(order_id) as count"),"user_id");  

                $totalproductive_calls = DB::table('user_sales_order')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->where('call_status',1)
                            ->groupBy('user_id')
                            ->orderBy('user_sales_order.date','ASC')
                            ->pluck(DB::raw("COUNT(order_id) as count"),"user_id");              
               // dd($user_productive_calls);           

             $user_ret_count = DB::table('user_sales_order')
                            // ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('person_login','person_login.person_id','=','person.id')
                             ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("COUNT(DISTINCT retailer_id) as rcount"),"user_id")
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                            ->where('person.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('user_id','user_sales_order.date')
                            ->orderBy('user_sales_order.date','ASC')
                            ->orderBy('user_name','ASC')
                            ->pluck("rcount","user_id");    

                            // dd($user_ret_count);  

        $check_in = DB::table('user_daily_attendance')
                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$from_date'")
                    ->where('user_daily_attendance.company_id',$company_id)
                    ->pluck(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as checkin"),DB::raw("CONCAT(user_id,DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')) as con"));    


        $check_out = DB::table('check_out')
                    ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')='$from_date'")
                    ->where('check_out.company_id',$company_id)
                    ->pluck(DB::raw("DATE_FORMAT(check_out.work_date,'%H:%i:%s') as checkin"),DB::raw("CONCAT(user_id,DATE_FORMAT(check_out.work_date,'%Y-%m-%d')) as con"));


        $expense = DB::table('travelling_expense_bill')         
                   ->whereRaw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d')='$from_date'")
                   ->where('travelling_expense_bill.company_id',$company_id)
                   ->groupBy('user_id','travelling_expense_bill.travellingDate')
                   ->pluck(DB::raw("SUM(total) as count"),DB::raw("CONCAT(user_id,DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d')) as con"));


                                      
        $new_outlet = DB::table('retailer')
                      ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$from_date'")  
                      ->where('retailer.company_id',$company_id)
                      ->groupBy("created_by_person_id",DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')"))
                      ->pluck(DB::raw("COUNT(id) as count"),DB::raw("CONCAT(created_by_person_id,DATE_FORMAT(retailer.created_on,'%Y-%m-%d')) as con"));

          if(empty($check)){  
          $primary_order = DB::table('user_primary_sales_order')
                           ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','user_primary_sales_order.order_id')
                           -> whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') ='$from_date'")
                           ->where('user_primary_sales_order.company_id',$company_id)
                           ->groupBy('sale_date','created_person_id')
                           ->pluck(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as primary_value"),DB::raw("CONCAT(created_person_id,sale_date)"));    
          }else{
            $primary_order = DB::table('user_primary_sales_order')
                            ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','user_primary_sales_order.order_id')
                            -> whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') ='$from_date'")
                            ->where('user_primary_sales_order.company_id',$company_id)
                            ->groupBy('sale_date','created_person_id')
                            ->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as primary_value"),DB::raw("CONCAT(created_person_id,sale_date)"));    
          }          


           // dd($primary_order);                
                                                                                                     


            $score_card_query_data = DB::table('user_sales_order')
                                    // ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                    ->join('person','person.id','=','user_sales_order.user_id')
                                    ->join('person_login','person_login.person_id','=','person.id')
                                    ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                    ->join('csa','csa.c_id','=','dealer.csa_id')
                                    ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                    ->select('csa_name',DB::raw("COUNT(call_status) as call_status"),'user_sales_order.date as date','dealer.name as dealer_name','user_sales_order.dealer_id as dealer_id','l6_name as zone','l6_id as zone_id','user_sales_order.user_id as user_id','retailer.id as retailer_id','retailer.name as retailer_name','l4_name as hq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'user_sales_order.date as sale_date','l6_name as working_station','retailer.landline as mobile')
                                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                                    ->where('person.company_id',$company_id)
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->where('person_status',1)
                                    ->where('dealer_status',1)
                                    ->groupBy('user_sales_order.retailer_id','user_sales_order.date')
                                    ->orderBy('user_sales_order.date','ASC')
                                    ->orderBy('user_name','ASC');

            if(!empty($state))
            {
                $score_card_query_data->whereIn('location_view.l3_id',$state);
            }

            if(!empty($role))
            {
                $score_card_query_data->whereIn('person.role_id',$role);
            }

            if(!empty($user))
            {
                $score_card_query_data->whereIn('person.id',$user);
            }
            $score_card_query = $score_card_query_data->get();
            // dd($score_card_query);
            $retailer_name_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                ->where('user_sales_order.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                                ->groupBy('user_sales_order.retailer_id','user_sales_order.date')
                                ->pluck(DB::raw("sum(rate*quantity) as total"),'retailer_id');
            $non_productive_call = DB::table('user_sales_order')
                                // ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                ->where('user_sales_order.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$from_date'")
                                ->groupBy('user_sales_order.retailer_id','user_sales_order.date')
                                ->where('call_status',0)
                                ->pluck(DB::raw("COUNT(call_status) as total"),'retailer_id');
            // dd($retailer_name_data);
            return view('reports.score-card-new.ajax', [
                    'records' => $score_card_query,
                    'check_in' => $check_in,
                    'check_out' => $check_out,
                    'expense' => $expense,
                    'new_outlet' => $new_outlet,
                    'eff_calls' => $effective_calls_data,
                    'catalog_product' => $catalog_product,
                    'stock_qty' => $stock_qty,
                    'stock_value' => $stock_value,
                    'primary_order' => $primary_order,
                    'totalproductive_calls'=> $totalproductive_calls,
                   // 'primary_order_rec_from' => $primary_order_rec_from,
                    'user_sale_value' => $user_sale_value,
                    'user_productive_calls' => $user_productive_calls,
                    'user_ret_count' => $user_ret_count,
                    'retailer_name_data'=> $retailer_name_data,
                    'non_productive_call'=> $non_productive_call,
                ]);
        }
        else
        {
            echo '<p class="alert-danger">Data not Found</p>';
        } 
    }

    #score_card_report ends here 







    #user monthly reports starts here
    public function user_monthy_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $role = $request->role;
        $user = !empty($request->user)?$request->user:'1224';
        $state = $request->state;
        $year = !empty($request->month)?$request->month:'2019-07';

        $location_3 = $request->location_3;
        $location_4 = $request->location_4;
        $location_5 = $request->location_5;
        $location_6 = $request->location_6;
        $user = $request->user;
        $role = $request->role;
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();



       $start_date = date('Y-m-d',strtotime($year));
                      $end_date= date("Y-m-t", strtotime($start_date));
                      $startTime = strtotime($start_date);
                      $endTime = strtotime($end_date);

        for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
        {                                       
            $Store = date('Y-m-d', $currentDate); 
            $datearray[] = $Store; 
        } 
        // dd($datearray);
        $first_call = DB::table('user_sales_order')->where('user_id',$user)->where('company_id',$company_id)->groupBy('user_id','date')->orderBy('time','ASC')->pluck('time',DB::raw("CONCAT(user_id,date)"));
        
        $last_call = DB::table('user_sales_order')->where('user_id',$user)->where('company_id',$company_id)->groupBy('user_id','date')->orderBy('time','DESC')->pluck('time',DB::raw("CONCAT(user_id,date)"));

        $productive_calls = DB::table('user_sales_order')->where('user_id',$user)->where('company_id',$company_id)->where('call_status',1)->groupBy('user_id','date')->pluck(DB::raw("COUNT(call_status) as productive_calls"),DB::raw("CONCAT(user_id,date)"));

        $total_calls = DB::table('user_sales_order')->where('user_id',$user)->where('company_id',$company_id)->groupBy('user_id','date')->pluck(DB::raw("COUNT(call_status) as productive_calls"),DB::raw("CONCAT(user_id,date)"));

        $attendance = DB::table('user_daily_attendance')->join('_working_status','_working_status.id','user_daily_attendance.work_status')->where('user_id',$user)->where('user_daily_attendance.company_id',$company_id)->groupBy('user_id','work_date')->pluck('name',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

        $working_with = DB::table('user_daily_attendance')->join('person','person.id','=','user_daily_attendance.working_with')->groupBy('user_id','work_date')->where('user_id',$user)->where('user_daily_attendance.company_id',$company_id)->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as work_with_name"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

        $dealer_details = DB::table('user_sales_order')->join('dealer','dealer.id','=','user_sales_order.dealer_id')->groupBy('dealer_id','user_sales_order.date')->where('user_id',$user)->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("group_concat(distinct(name))"),DB::raw("CONCAT(user_id,user_sales_order.date) as data"));

        $location_details = DB::table('user_sales_order')->join('location_7','location_7.id','=','user_sales_order.location_id')->groupBy('location_id','user_sales_order.date')->where('user_id',$user)->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("group_concat(distinct(name))"),DB::raw("CONCAT(user_id,user_sales_order.date) as data"));
        
        if(empty($check)){
        $total_sale_value = DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->where('user_id',$user)->where('user_sales_order.company_id',$company_id)->groupBy('user_id','date')->pluck(DB::raw("SUM(rate*quantity)"),DB::raw("CONCAT(user_id,date)"));
        }else{
            $total_sale_value = DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->where('user_id',$user)->where('user_sales_order.company_id',$company_id)->groupBy('user_id','date')->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2)"),DB::raw("CONCAT(user_id,date)"));
        }

        $mkt_plan = DB::table('monthly_tour_program')->where('person_id',$user)->where('monthly_tour_program.company_id',$company_id)->pluck('monthly_tour_program.town as town',DB::raw("CONCAT(person_id,working_date)"));

        $person_data = Person::join('person_login','person_login.person_id','=','person.id')->where('id',$user)->where('person.company_id',$company_id)->where('person_status',1)->get();

        $location_6 = DB::table('location_6')->where('company_id',$company_id)->pluck('name','id')->toArray();

        return view('reports.user-monthly-report.ajax', [
                    'first_call' => $first_call,
                    'last_call' => $last_call,
                    'productive_calls' => $productive_calls,
                    'total_calls' => $total_calls,
                    'attendance' => $attendance,
                    'working_with' => $working_with,
                    'dealer_details' => $dealer_details,
                    'location_details' => $location_details,
                    'person_data' => $person_data,
                    'datearray'=>$datearray,
                    'total_sale_value'=>$total_sale_value,
                    'mkt_plan'=>$mkt_plan,
                    'location_6'=>$location_6,
                ]);
    }
    #user monthly reports ends here

    public function advance_summary_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state;
        $location5 = $request->location5;
        $location6 = $request->location6;
        $role = $request->role;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

        $psenior_name = array();
        $effetive_coverage_area = array();
        $get_total_coverage_area = array();
        $get_total_lpc = array();
        $get_total_sale = array();
        $total_call = array();
        $total_productive_call = array();

        $scheme_amount = DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<= '$to_date'")
                        ->where('company_id',$company_id)
                        ->groupBy('user_sales_order.date')
                        ->groupBy('user_sales_order.dealer_id')
                        ->groupBy('user_sales_order.location_id')
                        ->groupBy('user_sales_order.user_id')
                        ->pluck(DB::raw('SUM(user_sales_order.total_sale_value) as sale'),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));




        $main_query = Person::join('user_sales_order','user_sales_order.user_id','=','person.id')
                        ->leftJoin('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->leftJoin('retailer','retailer.id','=','user_sales_order.retailer_id')
                        ->leftJoin('location_view','location_view.l7_id','=','user_sales_order.location_id')
                        ->select('person.mobile as mobile','l3_name','l4_name','l5_name','l6_name','l7_name','user_sales_order.user_id as user_id','_role.rolename as rolename','emp_code as ecod','l7_name as name5','l7_id as location_5_id',DB::raw("SUM(rate*quantity) as total_sale"),'l3_name as name3','l6_name as name4','l4_id as zone_id','l2_name as name2',DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') as dates"),DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') as date"),'user_sales_order.id AS uniq','user_sales_order.dealer_id AS dealer_id','person_id_senior',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'dealer.name AS dealer_name','user_sales_order.location_id AS usolid','user_sales_order.dealer_id AS did')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<= '$to_date'")
                        ->where('person.company_id',$company_id)
                        ->groupBy('user_sales_order.date')
                        ->groupBy('user_sales_order.dealer_id')
                        ->groupBy('user_sales_order.location_id')
                        ->groupBy('user_sales_order.user_id')
                        // ->orderBy('user_sales_order.date','ASC')
                        ->orderBy('zone_id','ASC')
                        ->orderBy('l7_name','ASC')
                        ->orderBy('person_name','ASC')
                        ->orderBy('user_sales_order.date','DESC');

                    // if(!empty($state))
                    // {
                    //     $main_query->whereIn('person.state_id',$state);
                    // }
                    // if(!empty($role))
                    // {
                    //     $main_query->whereIn('person.role_id',$role);
                    // }
                    // if(!empty($user))
                    // {
                    //     $main_query->whereIn('person.id',$user);
                    // }
                   if(!empty($datasenior))
                    {
                        $main_query->whereIn('person.id',$datasenior);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('l3_id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('l4_id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('l5_id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('l6_id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('person.id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }

                    $main_query_data = $main_query->get();

                    // for senior name
                    $senior_name_data = DB::table('person');
                    if(!empty($state))
                    {
                        $senior_name_data->whereIn('person.state_id',$state);
                    }
                    if(!empty($role))
                    {
                        $senior_name_data->whereIn('person.role_id',$role);
                    }
                    if(!empty($user))
                    {
                        $senior_name_data->whereIn('person.id',$user);
                    } 
                    $senior_name = $senior_name_data->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_name"),'id');
                    // end

                    // for effetive_coverage_area
                    $effetive_coverage_area_data = DB::table('user_sales_order')->where('company_id',$company_id)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT( DISTINCT retailer_id) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    // end

                    // for get_total_coverage_area
                    $get_total_coverage_area_data = DB::table('retailer')->where('company_id',$company_id)->groupBy('location_id')->pluck(DB::raw("COUNT(id)"),'location_id');
                    // end here  
                    // for get_total_lpc 
                    $get_total_lpc_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->where('user_sales_order_details.company_id',$company_id)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT(product_id) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    //  end here 

                    // for get_total_sale_data  
                    if(empty($check)){
                    $get_total_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->where('user_sales_order_details.company_id',$company_id)->groupBy('date','user_id','dealer_id','location_id')->pluck(DB::raw("SUM(rate*quantity) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    }
                    else{
                    $get_total_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->where('user_sales_order_details.company_id',$company_id)->groupBy('date','user_id','dealer_id','location_id')->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    }
                    // end here

                    // for total call
                    $total_call_data = DB::table('user_sales_order')->where('user_sales_order.company_id',$company_id)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT(DISTINCT retailer_id) as totalCall"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    // end here 

                    // for total call
                    $total_productive_call_data = DB::table('user_sales_order')->where('user_sales_order.company_id',$company_id)->where('call_status',1)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT(DISTINCT retailer_id) as prCall"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    // end here 
                    // dd($total_productive_call_data);

                    foreach ($main_query_data as $key => $value) 
                    {
                        $date = $value->dates;
                        $user_id = $value->user_id;
                        $dealer_id = $value->did;
                        $location_id = $value->usolid;
                        $person_id_senior = $value->person_id_senior;

                        $psenior_name[$user_id][$date] = !empty($senior_name[$person_id_senior])?$senior_name[$person_id_senior]:'NA';
                        // dd([$person_id_senior]);
                        // dd($user_id.$dealer_id.$date.$location_id);
                        // dd($effetive_coverage_area_data["86382019-05-30204"]);
                        $effetive_coverage_area[$user_id][$dealer_id][$date][$location_id] = !empty($effetive_coverage_area_data[$user_id.$dealer_id.$date.$location_id])?$effetive_coverage_area_data[$user_id.$dealer_id.$date.$location_id]:0;
                        
                        $get_total_coverage_area[$user_id][$dealer_id][$date][$location_id] = !empty($get_total_coverage_area_data[$location_id])?$get_total_coverage_area_data[$location_id]:0;
                        
                        $get_total_lpc[$user_id][$dealer_id][$date][$location_id] = !empty($get_total_lpc_data[$user_id.$dealer_id.$date.$location_id])?$get_total_lpc_data[$user_id.$dealer_id.$date.$location_id]:0;

                        $get_total_sale[$user_id][$dealer_id][$date][$location_id] = !empty($get_total_sale_data[$user_id.$dealer_id.$date.$location_id])?$get_total_sale_data[$user_id.$dealer_id.$date.$location_id]:0;

                        $total_call[$user_id][$dealer_id][$date][$location_id] = !empty($total_call_data[$user_id.$dealer_id.$date.$location_id])?$total_call_data[$user_id.$dealer_id.$date.$location_id]:0;

                        $total_productive_call[$user_id][$dealer_id][$date][$location_id] = !empty($total_productive_call_data[$user_id.$dealer_id.$date.$location_id])?$total_productive_call_data[$user_id.$dealer_id.$date.$location_id]:0;
                    }
                    // dd($scheme_amount);
        $checkindata = DB::table('user_daily_attendance')
                ->join('person','person.id','=','user_daily_attendance.user_id')
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
                ->groupBy('work_date','user_id');
                if (!empty($request->user)) 
                {
                    $user = $request->user;
                    $checkindata->whereIn('person.id', $user);
                }
                if (!empty($request->role)) 
                {
                    $role = $request->role;
                    $checkindata->whereIn('person.role_id', $role);
                }
                if (!empty($request->location_3)) 
                {
                    $location_3 = $request->location_3;
                    $checkindata->whereIn('state_id', $location_3);
                }
        $checkin = $checkindata->pluck('work_date',DB::raw("CONCAT(date_format(work_date,'%Y-%m-%d'),user_id) as concat_all"));
        // dd($checkin);
        $checkoutdata = DB::table('check_out')
                ->join('person','person.id','=','check_out.user_id')
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
                ->groupBy('work_date','user_id');
                if (!empty($request->user)) 
                {
                    $user = $request->user;
                    $checkoutdata->whereIn('person.id', $user);
                }
                if (!empty($request->role)) 
                {
                    $role = $request->role;
                    $checkoutdata->whereIn('person.role_id', $role);
                }
                if (!empty($request->location_3)) 
                {
                    $location_3 = $request->location_3;
                    $checkoutdata->whereIn('state_id', $location_3);
                }
        $checkout = $checkoutdata->pluck('work_date',DB::raw("CONCAT(date_format(work_date,'%Y-%m-%d'),user_id) as concat_all"));


                return view('reports.advance_summary_report.ajax', 
                [
                    'main_query_data'=>$main_query_data,
                    'psenior_name'=>$psenior_name,
                    'effetive_coverage_area'=>$effetive_coverage_area,
                    'get_total_coverage_area'=>$get_total_coverage_area,
                    'get_total_lpc'=>$get_total_lpc,
                    'get_total_sale'=>$get_total_sale,
                    'total_call'=>$total_call,
                    'total_productive_call'=>$total_productive_call,
                    'total_productive_call_data'=>$total_productive_call_data,
                    'from_date'=>$from_date,
                    'to_date'=>$to_date,
                    'check_in'=>$checkin,
                    'check_out'=>$checkout,
                    'scheme_amount'=>$scheme_amount,
                    'company_id'=>$company_id,
                 
                ]);
    }

    public function advance_summary_report_ghanta(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state;
        $location5 = $request->location5;
        $location6 = $request->location6;
        $role = $request->role;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

        $psenior_name = array();
        $effetive_coverage_area = array();
        $get_total_coverage_area = array();
        $get_total_lpc = array();
        $get_total_sale = array();
        $total_call = array();
        $total_productive_call = array();
        $scheme_amount = array();
        $total_productive_call_data = [];
        // $scheme_amount = DB::table('user_sales_order')
        //                 ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<= '$to_date'")
        //                 ->where('company_id',$company_id)
        //                 ->groupBy('user_sales_order.date')
        //                 ->groupBy('user_sales_order.dealer_id')
        //                 ->groupBy('user_sales_order.location_id')
        //                 ->groupBy('user_sales_order.user_id')
        //                 ->pluck(DB::raw('SUM(user_sales_order.total_sale_value) as sale'),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));




        $main_query = Person::join('user_sales_order','user_sales_order.user_id','=','person.id')
                        ->leftJoin('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->leftJoin('retailer','retailer.id','=','user_sales_order.retailer_id')
                        ->leftJoin('location_view','location_view.l7_id','=','user_sales_order.location_id')
                        ->select('person.mobile as mobile','l3_name','l4_name','l5_name','l6_name','l7_name','user_sales_order.user_id as user_id','_role.rolename as rolename','emp_code as ecod','l7_name as name5','l7_id as location_5_id',DB::raw("SUM(rate*quantity) as total_sale"),'l3_name as name3','l6_name as name4','l4_id as zone_id','l2_name as name2',DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') as dates"),DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') as date"),'user_sales_order.id AS uniq','user_sales_order.dealer_id AS dealer_id','person_id_senior',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'dealer.name AS dealer_name','user_sales_order.location_id AS usolid','user_sales_order.dealer_id AS did')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<= '$to_date'")
                        ->where('person.company_id',$company_id)
                        ->groupBy('user_sales_order.date')
                        ->groupBy('user_sales_order.dealer_id')
                        ->groupBy('user_sales_order.location_id')
                        ->groupBy('user_sales_order.user_id')
                        // ->orderBy('user_sales_order.date','ASC')
                        ->orderBy('zone_id','ASC')
                        ->orderBy('l7_name','ASC')
                        ->orderBy('person_name','ASC')
                        ->orderBy('user_sales_order.date','DESC');

                    // if(!empty($state))
                    // {
                    //     $main_query->whereIn('person.state_id',$state);
                    // }
                    // if(!empty($role))
                    // {
                    //     $main_query->whereIn('person.role_id',$role);
                    // }
                    // if(!empty($user))
                    // {
                    //     $main_query->whereIn('person.id',$user);
                    // }
                   if(!empty($datasenior))
                    {
                        $main_query->whereIn('person.id',$datasenior);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('l3_id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('l4_id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('l5_id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('l6_id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('person.id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }

                    $main_query_data = $main_query->get();

                    // for senior name
                    $senior_name_data = DB::table('person');
                    if(!empty($state))
                    {
                        $senior_name_data->whereIn('person.state_id',$state);
                    }
                    if(!empty($role))
                    {
                        $senior_name_data->whereIn('person.role_id',$role);
                    }
                    if(!empty($user))
                    {
                        $senior_name_data->whereIn('person.id',$user);
                    } 
                    $senior_name = $senior_name_data->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_name"),'id');
                    // end

                    // for effetive_coverage_area
                    // $effetive_coverage_area_data = DB::table('user_sales_order')->where('company_id',$company_id)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT( DISTINCT retailer_id) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    // end

                    // for get_total_coverage_area
                    // $get_total_coverage_area_data = DB::table('retailer')->where('company_id',$company_id)->groupBy('location_id')->pluck(DB::raw("COUNT(id)"),'location_id');
                    // end here  
                    // for get_total_lpc 
                    // $get_total_lpc_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->where('user_sales_order_details.company_id',$company_id)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT(product_id) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    //  end here 

                    // for get_total_sale_data  
                    if(empty($check)){
                    $get_total_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->where('user_sales_order_details.company_id',$company_id)->groupBy('date','user_id','dealer_id','location_id')->pluck(DB::raw("SUM(rate*quantity) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    }
                    else{
                    $get_total_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->where('user_sales_order_details.company_id',$company_id)->groupBy('date','user_id','dealer_id','location_id')->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    }
                    // end here

                    // for total call
                    // $total_call_data = DB::table('user_sales_order')->where('user_sales_order.company_id',$company_id)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT(order_id)"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    // end here 

                    // for total call
                    // $total_productive_call_data = DB::table('user_sales_order')->where('user_sales_order.company_id',$company_id)->where('call_status',1)->groupBy('user_id','dealer_id','date','location_id')->pluck(DB::raw("COUNT(call_status)"),DB::raw("CONCAT(user_id,dealer_id,date,location_id) as concat_all"));
                    // end here 
                    // dd($total_productive_call_data);


                    foreach ($main_query_data as $key => $value) 
                    {
                        $date = $value->dates;
                        $user_id = $value->user_id;
                        $dealer_id = $value->did;
                        $location_id = $value->usolid;
                        $person_id_senior = $value->person_id_senior;

                        $psenior_name[$user_id][$date] = !empty($senior_name[$person_id_senior])?$senior_name[$person_id_senior]:'NA';
                        // dd([$person_id_senior]);
                        // dd($user_id.$dealer_id.$date.$location_id);
                        // dd($effetive_coverage_area_data["86382019-05-30204"]);
                        $effetive_coverage_area[$user_id][$dealer_id][$date][$location_id] = !empty($effetive_coverage_area_data[$user_id.$dealer_id.$date.$location_id])?$effetive_coverage_area_data[$user_id.$dealer_id.$date.$location_id]:0;
                        
                        $get_total_coverage_area[$user_id][$dealer_id][$date][$location_id] = !empty($get_total_coverage_area_data[$location_id])?$get_total_coverage_area_data[$location_id]:0;
                        
                        $get_total_lpc[$user_id][$dealer_id][$date][$location_id] = !empty($get_total_lpc_data[$user_id.$dealer_id.$date.$location_id])?$get_total_lpc_data[$user_id.$dealer_id.$date.$location_id]:0;

                        $get_total_sale[$user_id][$dealer_id][$date][$location_id] = !empty($get_total_sale_data[$user_id.$dealer_id.$date.$location_id])?$get_total_sale_data[$user_id.$dealer_id.$date.$location_id]:0;

                        $total_call[$user_id][$dealer_id][$date][$location_id] = !empty($total_call_data[$user_id.$dealer_id.$date.$location_id])?$total_call_data[$user_id.$dealer_id.$date.$location_id]:0;

                        $total_productive_call[$user_id][$dealer_id][$date][$location_id] = !empty($total_productive_call_data[$user_id.$dealer_id.$date.$location_id])?$total_productive_call_data[$user_id.$dealer_id.$date.$location_id]:0;
                    }
                    // dd($scheme_amount);
        $checkindata = DB::table('user_daily_attendance')
                ->join('person','person.id','=','user_daily_attendance.user_id')
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
                ->groupBy('work_date','user_id');
                if (!empty($request->user)) 
                {
                    $user = $request->user;
                    $checkindata->whereIn('person.id', $user);
                }
                if (!empty($request->role)) 
                {
                    $role = $request->role;
                    $checkindata->whereIn('person.role_id', $role);
                }
                if (!empty($request->location_3)) 
                {
                    $location_3 = $request->location_3;
                    $checkindata->whereIn('state_id', $location_3);
                }
        $checkin = $checkindata->pluck('work_date',DB::raw("CONCAT(date_format(work_date,'%Y-%m-%d'),user_id) as concat_all"));
        // dd($checkin);
        $checkoutdata = DB::table('check_out')
                ->join('person','person.id','=','check_out.user_id')
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
                ->groupBy('work_date','user_id');
                if (!empty($request->user)) 
                {
                    $user = $request->user;
                    $checkoutdata->whereIn('person.id', $user);
                }
                if (!empty($request->role)) 
                {
                    $role = $request->role;
                    $checkoutdata->whereIn('person.role_id', $role);
                }
                if (!empty($request->location_3)) 
                {
                    $location_3 = $request->location_3;
                    $checkoutdata->whereIn('state_id', $location_3);
                }
        $checkout = $checkoutdata->pluck('work_date',DB::raw("CONCAT(date_format(work_date,'%Y-%m-%d'),user_id) as concat_all"));


                return view('reports.advance_summary_report.isrAjax', 
                [
                    'main_query_data'=>$main_query_data,
                    'psenior_name'=>$psenior_name,
                    'effetive_coverage_area'=>$effetive_coverage_area,
                    'get_total_coverage_area'=>$get_total_coverage_area,
                    'get_total_lpc'=>$get_total_lpc,
                    'get_total_sale'=>$get_total_sale,
                    'total_call'=>$total_call,
                    'total_productive_call'=>$total_productive_call,
                    'total_productive_call_data'=>$total_productive_call_data,
                    'from_date'=>$from_date,
                    'to_date'=>$to_date,
                    'check_in'=>$checkin,
                    'check_out'=>$checkout,
                    'scheme_amount'=>$scheme_amount,
                    'company_id'=>$company_id,
                 
                ]);
    }

    #dsr monthly satrts here 
    public function dsrMonthlyReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $catalog_product_data = DB::table('catalog_product')
                            ->where('status',1)
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->orderBy('id','asc');
                            if(!empty($request->product))
                            {
                                $catalog_product_data->whereIn('id',$request->product);
                            }
                              if(!empty($request->catalog_2))
                            {
                                $catalog_product_data->whereIn('catalog_id',$request->catalog_2);
                            }
        $catalog_product = $catalog_product_data->get();
        
        $person_details = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order','user_sales_order.user_id','=','person.id')
                        ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('rolename','person.mobile as mobile','user_sales_order.date as date','l3_name','l4_name','l5_name','l6_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person.id as user_id','person.state_id as state_id','person.emp_code','person.person_id_senior')
                        ->where('person.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('user_id','date')->orderBy('date','DESC')->orderBy('user_id','ASC');

        if (!empty($datasenior)) 
        {
            $person_details->whereIn('person.id', $datasenior);
        }
        if(!empty($state))
        {
            $person_details->where('person.state_id',$state);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_details->whereIn('l3_id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $person_details->whereIn('l4_id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_details->whereIn('l5_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_details->whereIn('l6_id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $person_details->whereIn('dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $person_details->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_details->whereIn('person.role_id', $role);
        }

        $person = $person_details->get();
        // dd($person);
        if(empty($check)){
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        else{
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.final_secondary_qty) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        // dd($dsr);
        $market_data  = DB::table('user_sales_order as uso')->join('location_7','location_7.id','=','uso.location_id')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('uso.company_id',$company_id)->pluck('location_7.name as market',DB::raw("CONCAT(user_id,date)"));

        $total_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(DISTINCT retailer_id) as total_call'),DB::raw("CONCAT(user_id,date)"));

        $productive_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->where('call_status',1)->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(DISTINCT retailer_id) as total_call'),DB::raw("CONCAT(user_id,date)"));

        if(empty($check)){
        $product_amount_data_data = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_id','date');
                            if(!empty($request->product))
                            {
                                $product_amount_data_data->whereIn('product_id',$request->product);
                            }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }else{
        $product_amount_data_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->groupBy('user_id','date');
                                if(!empty($request->product))
                                {
                                    $product_amount_data_data->whereIn('product_id',$request->product);
                                }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(final_secondary_qty*final_secondary_rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }

        $out=array();
        if (!empty($person)) {
                foreach ($person as $k => $d) {
                    $uid=$d->user_id;
                    $date= $d->date;
                    $out[$uid][$date]['user'] = $uid;
                    $out[$uid][$date]['date'] = $date;
                    $out[$uid][$date]['market'] = !empty($market_data[$uid.$date])?$market_data[$uid.$date]:'0';

                    $out[$uid][$date]['total_call'] = !empty($total_call_data[$uid.$date])?$total_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['productive_call'] = !empty($productive_call_data[$uid.$date])?$productive_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['product_amount'] = !empty($product_amount_data[$uid.$date])?$product_amount_data[$uid.$date]:'0';
                }
            }
             // dd($out);

            $scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
            ->where('company_id',$company_id)
            ->groupBy('user_id')
            ->groupBy('date')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(user_id,date) as concat"));




            return view('reports.dsr-monthly.ajax', [
            'person' => $person,
            'productData' => $out,
            'dsr'=>$dsr,
            'catalog_product' => $catalog_product,
            'company_id' => $company_id,
            'scheme_amount' => $scheme_amount,

        ]);

    }

    public function dsrMonthlyCasesReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        $catalog_product_data = DB::table('catalog_product')
                            ->where('status',1)
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->orderBy('id','asc');
                            if(!empty($request->product))
                            {
                                $catalog_product_data->whereIn('id',$request->product);
                            }
                              if(!empty($request->catalog_2))
                            {
                                $catalog_product_data->whereIn('catalog_id',$request->catalog_2);
                            }
        $catalog_product = $catalog_product_data->get();

        
        $person_details = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order','user_sales_order.user_id','=','person.id')
                        ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('rolename','person.mobile as mobile','user_sales_order.date as date','l3_name','l4_name','l5_name','l6_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person.id as user_id','person.state_id as state_id','person.emp_code','person.person_id_senior')
                        ->where('person.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('user_id','date')->orderBy('date','DESC')->orderBy('user_id','ASC');

        if(!empty($state))
        {
            $person_details->where('person.state_id',$state);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_details->whereIn('l3_id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $person_details->whereIn('l4_id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_details->whereIn('l5_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_details->whereIn('l6_id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $person_details->whereIn('dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $person_details->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_details->whereIn('person.role_id', $role);
        }

        $person = $person_details->get();
        // dd($person);

        $prod_retailer_count_data =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id);

            if(!empty($request->product))
            {
                $prod_retailer_count_data->whereIn('product_id',$request->product);
            }
        $prod_retailer_count = $prod_retailer_count_data->pluck(DB::raw("count(user_sales_order.retailer_id) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));


        if(empty($check)){
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        else{
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.final_secondary_qty) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        // dd($dsr);
        $market_data  = DB::table('user_sales_order as uso')->join('location_7','location_7.id','=','uso.location_id')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('uso.company_id',$company_id)->pluck('location_7.name as market',DB::raw("CONCAT(user_id,date)"));

        $total_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(order_id) as total_call'),DB::raw("CONCAT(user_id,date)"));

        $productive_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->where('call_status',1)->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(call_status) as total_call'),DB::raw("CONCAT(user_id,date)"));

        if(empty($check)){
        $product_amount_data_data = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_id','date');
                            if(!empty($request->product))
                            {
                                $product_amount_data_data->whereIn('product_id',$request->product);
                            }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }else{
        $product_amount_data_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->groupBy('user_id','date');
                                if(!empty($request->product))
                                {
                                    $product_amount_data_data->whereIn('product_id',$request->product);
                                }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(final_secondary_qty*final_secondary_rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }

        $out=array();
        if (!empty($person)) {
                foreach ($person as $k => $d) {
                    $uid=$d->user_id;
                    $date= $d->date;
                    $out[$uid][$date]['user'] = $uid;
                    $out[$uid][$date]['date'] = $date;
                    $out[$uid][$date]['market'] = !empty($market_data[$uid.$date])?$market_data[$uid.$date]:'0';

                    $out[$uid][$date]['total_call'] = !empty($total_call_data[$uid.$date])?$total_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['productive_call'] = !empty($productive_call_data[$uid.$date])?$productive_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['product_amount'] = !empty($product_amount_data[$uid.$date])?$product_amount_data[$uid.$date]:'0';
                }
            }
             // dd($out);
            return view('reports.dsr-monthly.CaseAjax', [
            'person' => $person,
            'productData' => $out,
            'dsr'=>$dsr,
            'prod_retailer_count'=> $prod_retailer_count,
            'catalog_product' => $catalog_product,

        ]);

    }
    #dsr monthly ends here 

    #rds wise sale starts here 
    public function rds_wise_sale_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();



        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                    ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                    ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                    ->select('person.emp_code','rolename','person.mobile as mobile','l4_name','l5_name','l6_name','l7_name','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq','location_view.l3_name as state','location_view.l6_name as city',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','retailer.name as retailer_name','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','user_sales_order.retailer_id as retailer_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->groupBy('date','user_id','retailer_id')
                    ->orderBy('user_sales_order.date','DESC');
                    if (!empty($datasenior)) 
                    {
                        $main_query->whereIn('person.id', $datasenior);
                    }
                    if(!empty($state))
                    {
                        $main_query->whereIn('location_view.l3_id',$state);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('l3_id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('l4_id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('l5_id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('l6_id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('user_id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }
                    $main_query_data = $main_query->get();

                    if(empty($check)){
                    $secondry_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->where('user_sales_order.company_id',$company_id)->groupBy('user_id','date','dealer_id','retailer_id')->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(user_id,date,dealer_id,retailer_id) as total"));
                    }else{
                    $secondry_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->where('user_sales_order.company_id',$company_id)->groupBy('user_id','date','dealer_id','retailer_id')->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),DB::raw("CONCAT(user_id,date,dealer_id,retailer_id) as total"));
                    }

                    $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

                    // dd($secondry_sale_data);
                    $secondry_sale = array();
                    $senior_name = array();
                    foreach ($main_query_data as $key => $value) 
                    {
                        $user_id = $value->user_id;
                        $retailer_id = $value->retailer_id;
                        $did = $value->did;
                        $date = $value->date;
                        $person_id_senior = $value->person_id_senior;

                        $senior_name[$user_id][$date] = $senior_name_data[$person_id_senior];

                        $secondry_sale[$user_id][$date][$did][$retailer_id] = !empty($secondry_sale_data[$user_id.$date.$did.$retailer_id])?$secondry_sale_data[$user_id.$date.$did.$retailer_id]:'0';      
                        // dd($user_id.$date.$did.$retailer_id);               
                    }
                    // dd($secondry_sale);


                     $scheme_sale_data = DB::table('user_sales_order')
                     ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                     ->where('user_sales_order.company_id',$company_id)
                     ->groupBy('user_id','date','dealer_id','retailer_id')
                     ->pluck(DB::raw("SUM(total_sale_value) as total_sale_value"),DB::raw("CONCAT(user_id,date,dealer_id,retailer_id) as total"));


                    return view('reports.rds_wise_sale.ajax', [
                               'secondry_sale'=>$secondry_sale,
                               'senior_name'=>$senior_name,
                               'main_query_data'=>$main_query_data,
                               'company_id'=>$company_id,
                               'scheme_sale_data'=>$scheme_sale_data,

                            ]);


    }

    #rds wise sale ends here 

    //#.............................
    public function get_beat_name(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $id = $request->id;
        $beat_name=DB::table('dealer_location_rate_list')
        ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
        ->where('dealer_location_rate_list.dealer_id',$id)
        ->where('dealer_location_rate_list.company_id',$company_id)
        ->pluck('location_7.name','location_7.id');
        return json_encode($beat_name);
    }
    //#.............................
    public function product_tracker_report(Request $request)
    {
        $dealer = $request->dealer_id;
        $catalog_id = $request->catalog_id;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $company_id = Auth::user()->company_id;
        $role_id = Auth::user()->role_id;
        $is_admin = Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin == 1 )
        {
            $datasenior='';
        }
        else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }
        $dealer_id_array = array();
        if(!empty($datasenior))
        {
            $dealer_id_array = DB::table('dealer_location_rate_list')
                        ->whereIn('user_id',$datasenior)
                        ->groupBy('dealer_id')
                        ->pluck('dealer_id');            
        }
        // dd($dealer_id_array);
        $product_tracker_query = DB::table('product_tracker')
                                ->where('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(date_time,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date_time,'%Y-%m-%d')<='$to_date'")
                                ->orderBy('location_identifier','ASC')
                                ->orderBy('date_time','ASC');
                                if(!empty($dealer))
                                {
                                    $product_tracker_query->whereIn('dealer_id',$dealer);
                                }
                                if(!empty($catalog_id))
                                {
                                    $product_tracker_query->whereIn('c0_id',$catalog_id);
                                }   
                                if(!empty($dealer_id_array))
                                {
                                    $product_tracker_query->whereIn('dealer_id',$dealer_id_array);
                                }
        $product_tracker_data = $product_tracker_query->get();
        
        return view('reports.product_tracker.ajax', [
            'records'=>$product_tracker_data,
            'from_date'=>$from_date,
            'to_date'=>$to_date,

         ]);
    }
    #..........................................................................dealer wise data in details starts here ...............................................##
    public function userDealerInfoReport(Request $request)
    {
        $state = $request->state;
        $user = $request->user;
        $status=$request->status;
        $dealerInfo= DB::table('user_dealer_retailer_view as udrv')->select('lv.l3_id','udrv.user_name','udrv.mobile','udrv.retailer_name','udrv.beat_id','udrv.beat_name','udrv.role_name','udrv.user_id','udrv.l3_name','udrv.l6_name as l4_name','udrv.dealer_name',DB::raw('count(distinct retailer_id) as retalier_count'),DB::raw('count(distinct beat_id) as beat_count'))->join('location_view as lv','lv.l5_id','=','udrv.beat_id')->join('dealer','dealer.id','=','udrv.dealer_id')->groupBy('user_id','dealer_id');
        if(!empty($user))
        {
            $dealerInfo->whereIn('udrv.user_id',$user);
        }
        if(!empty($state))
        {
            $dealerInfo->whereIn('lv.l3_id',$state);
        }
        if(!empty($status))
        {
            $dealerInfo->whereIn('dealer.dealer_status',$status);
        }


        $userDealerInfo = $dealerInfo->get();
        return view('reports.user_dealer_info.ajax', [
                    'dealerInfo' => $userDealerInfo
                ]);

    }
    #....................................................senior herirchacy starts here ...............................................................................##
    public function seniorInfoReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $user = $request->user;
        $role=$request->role;
        $seniorInfo= DB::table('person as p1')->SELECT('p1.emp_code','p1.mobile','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_2.name as l2_name','p1.id as user_id','_role.role_id','lv.l2_id','dealer.name as dealer_name','dlrl.dealer_id as dealer_id','p1.role_id as prole_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'p1.head_quar as person_head_quarter','p1.id as pid','p1.person_id_senior as senior_id','p1.person_id_senior as person_id_senior',DB::raw('(select(rolename)from _role where role_id= prole_id) as p_role_name'),DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where person.id=p1.person_id_senior ) as senior_name"),DB::raw('(select(person.role_id)from person where person.id=p1.person_id_senior) as senior_role_id1'),DB::raw('(select(rolename) from _role where role_id = senior_role_id1) as senior_role_name'),DB::raw('(select(person.head_quar)from person where person.id = senior_id) as head_quar_name'),DB::raw('(select(person_id_senior)from person where person.id = senior_id) as person_id_super_senior'),DB::raw('(select(person.role_id)from person where person.id = person_id_super_senior) as super_senior_designation_id'),DB::raw('(select(rolename)from _role where role_id= super_senior_designation_id) as super_senior_designation'),DB::raw("(select(CONCAT_WS(' ',first_name,middle_name,last_name))from person where person.id = person_id_super_senior) as super_senior_name"),DB::raw('(select(person_id_senior)from person where person.id = person_id_super_senior) as person_id_super_super_senior'),DB::raw('(select(person.role_id)from person where person.id = person_id_super_super_senior) as super_super_senior_designation_id'),DB::raw('(select(rolename)from _role where role_id= super_super_senior_designation_id) as super_super_senior_designation'),DB::raw("(select(CONCAT_WS(' ',first_name,middle_name,last_name))from person where person.id = person_id_super_super_senior) as super_super_senior_name"),DB::raw('group_concat(distinct l7_name) as beat_name'))
        ->join('person_login as pl','pl.person_id','=','p1.id') 
        ->join('location_3', 'location_3.id', '=', 'p1.state_id')
        ->join('location_2','location_2.id','=','location_3.location_2_id')
        ->join('location_1','location_1.id','=','location_2.location_1_id')

        ->join('location_6', 'location_6.id', '=', 'p1.town_id')
        ->join('location_5','location_5.id','=','location_6.location_5_id')
        ->join('location_4','location_4.id','=','location_5.location_4_id')
        ->join('_role','_role.role_id','=','p1.role_id')  
        ->leftJoin('dealer_location_rate_list as dlrl','dlrl.user_id','=','p1.id') 
        ->leftJoin('dealer','dealer.id','=','dlrl.dealer_id')
        ->leftJoin('location_view as lv','lv.l7_id','=','dlrl.location_id')
        ->where('p1.company_id',$company_id)
        ->where('pl.person_status',1)
        ->groupBy('p1.id','dlrl.dealer_id')
        ->orderBy('p1.first_name' ,'asc');

        //dd($seniorInfo);
  
        if(!empty($user))
        {
            $seniorInfo->whereIn('p1.id',$user);
        }
        if(!empty($state))
        {
            $seniorInfo->whereIn('lv.l3_id',$state);
        }
        if(!empty($role))
        {
            $seniorInfo->whereIn('_role.role_id',$role);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $seniorInfo->whereIn('location_3.id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $seniorInfo->whereIn('location_4.id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $seniorInfo->whereIn('location_5.id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $seniorInfo->whereIn('location_6.id', $location_6);
        }
        // if (!empty($request->role)) 
        // {
        //     $role = $request->role;
        //     $data1->whereIn('person.role_id', $role);
        // }
        $seniorInfoReport=$seniorInfo->get();
        return view('reports.senior_info.ajax', [
                    'seniorInfo' => $seniorInfoReport,

                ]);

    }

     #expnse status change starts here
    public function changeExpenseStatus(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $status = $request->status;
        $user_id = $request->user_id;
        $date = $request->date;
        $id = $request->id;
        $auth_id=Auth::user()->id;
        // dd($user_id);

        $change_status_query = DB::table('travelling_expense_bill')
                                ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')='$date'")
                                ->where('user_id',$user_id)
                                ->where('company_id',$company_id)
                                ->where('id',$id)
                                ->update(['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')]);

        // dd($change_status_query);
        if($change_status_query)
        {
            $insert_in_log = DB::table('travell_expense_bill_log')->insert([
                            "expense_id"=>$id,
                            "user_id"=>$user_id,
                            "company_id"=>$company_id,
                            "status"=>$status,
                            "created_by"=>$auth_id,
                            "created_at"=>date("Y-m-d H:i:s"),
                ]);
            DB::commit();
            $data['code'] = 200;
            $data['result'] = 'success';
            $data['message'] = 'success';
            
        }
        else {
            #for unauthorized request
            DB::rollback();
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
    } 
    #expnse status change ends here


 #expnse delete change starts here
    public function deleteExpense(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = $request->user_id;
        $date = $request->date;
        $id = $request->id;
        $auth_id=Auth::user()->id;
        // dd($user_id);

        $change_status_query = DB::table('travelling_expense_bill')
                                ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')='$date'")
                                ->where('user_id',$user_id)
                                ->where('company_id',$company_id)
                                ->where('id',$id)
                                ->first();

        // dd($change_status_query->id);
        if($change_status_query)
        {
            $insert_in_log = DB::table('travelling_expense_bill_deleted_log')->insert([
                            "expense_id"=>$change_status_query->id,
                            "company_id"=>$change_status_query->company_id,
                            "travellingDate"=>$change_status_query->travellingDate,
                            "arrivalTime"=>$change_status_query->arrivalTime,
                            "departureTime"=>$change_status_query->departureTime,
                            "distance"=>$change_status_query->distance,
                            "fare"=>$change_status_query->fare,
                            "da"=>$change_status_query->da,
                            "hotel"=>$change_status_query->hotel,
                            "postage"=>$change_status_query->postage,
                            "telephoneExpense"=>$change_status_query->telephoneExpense,
                            "conveyance"=>$change_status_query->conveyance,
                            "misc"=>$change_status_query->misc,
                            "total"=>$change_status_query->total,
                            "arrivalID"=>$change_status_query->arrivalID,
                            "departureID"=>$change_status_query->departureID,
                            "travelModeID"=>$change_status_query->travelModeID,
                            "date_time"=>$change_status_query->date_time,
                            "lat_lng"=>$change_status_query->lat_lng,
                            "geo_address"=>$change_status_query->geo_address,
                            "mcc_mnc"=>$change_status_query->mcc_mnc,
                            "unique_id"=>$change_status_query->unique_id,
                            "order_id"=>$change_status_query->order_id,
                            "user_id"=>$change_status_query->user_id,
                            "status"=>$change_status_query->status,
                            "remarks"=>$change_status_query->remarks,
                            "image_name1"=>$change_status_query->image_name1,
                            "image_name2"=>$change_status_query->image_name2,
                            "image_name3"=>$change_status_query->image_name3,
                            "deleted_by"=>$auth_id,
                            "updated_at"=>date("Y-m-d H:i:s"),
                ]);

            if($insert_in_log){

                   $delete_query = DB::table('travelling_expense_bill')
                             ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')='$date'")
                             ->where('user_id',$user_id)
                             ->where('company_id',$company_id)
                             ->where('id',$id)
                             ->delete(); 

                             if($delete_query){
                                DB::commit();
                                $data['code'] = 200;
                                $data['result'] = 'success';
                                $data['message'] = 'success';

                             }
                             else{

                                  DB::rollback();
                                 $data['code'] = 401;
                                 $data['result'] = '';
                                 $data['message'] = 'unauthorized request';

                             }
                }

            else{

                    DB::rollback();
                    $data['code'] = 401;
                    $data['result'] = '';
                    $data['message'] = 'unauthorized request';

                }    
     
            
        }
        else {
            #for unauthorized request
            DB::rollback();
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
    } 
    #expnse delete change ends here



    #show_expense_log_data starts here
    public function  show_expense_log_data(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = $request->user_id;
        $id = $request->id;
        $date = $request->date;
        // dd($date);
        $fetch_query = DB::table('travell_expense_bill_log')
                    ->join('travelling_expense_bill','travelling_expense_bill.id','=','travell_expense_bill_log.expense_id')
                    ->join('person','person.id','=','travell_expense_bill_log.user_id')
                    ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'travellingDate as date','travell_expense_bill_log.status as status',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = travell_expense_bill_log.created_by) as modified_by"),"travell_expense_bill_log.created_at as modified_time")
                    ->where('travell_expense_bill_log.user_id',$user_id)
                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')='$date'")
                    ->where('expense_id',$id)
                    ->where('travell_expense_bill_log.company_id',$company_id)
                    ->get();
                    // dd($fetch_query);
                    foreach ($fetch_query as $key => $value) 
                    {
                        $out['user_name'] = $value->user_name; 
                        $out['status'] = ($value->status==1)?'Approved':'Not Approved'; 
                        $out['modified_by'] = $value->modified_by; 
                        $out['modified_time'] = $value->modified_time; 
                        $out['date'] = $value->date; 
                        $data1[] = $out;
                    }
                    // dd($data1);
        if(!empty($data1))
        {
            $data['user_data'] = $data1;
            $data['code'] = 200;
            $data['result'] = 'success';
            $data['message'] = 'success';
        }
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
    // dd(json_encode($data));
    return json_encode($data);
    }
    #show_expense_log_data ends here 


      #edit_expense starts here
    public function  edit_travelling_expense(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = $request->user_id;
        $id = $request->id;
        $date = $request->date;
        // dd($date);
        $fetch_query = DB::table('travelling_expense_bill')
                        ->select('postage','hotel','da','telephoneExpense','conveyance','misc','fare','user_id','id','travellingDate as date','distance')
                        ->where('travellingDate',$date)
                        ->where('user_id',$user_id)
                        ->where('company_id',$company_id)
                        ->where('id',$id)
                        ->get()->toArray();
                    // dd($fetch_query);
               
                    // dd($data1);
        if(!empty($fetch_query))
        {
            $data['result'] = $fetch_query;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
    // dd(json_encode($data));
    return json_encode($data);
    }


   
    public function submit_expense_edit(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = $request->user_id;
        $id = $request->id;
        $date = $request->date;
        $distance = $request->distance;
        $total = $request->fare+$request->da+$request->postage+$request->telephoneExpense+$request->conveyance+$request->hotel+$request->misc;

        $myArr = [
            'distance'=>$request->distance,
            'fare'=>$request->fare,
            'da'=>$request->da,
            'postage'=>$request->postage,
            'telephoneExpense'=>$request->telephoneExpense,
            'conveyance'=>$request->conveyance,
            'hotel'=>$request->hotel,
            'misc'=>$request->misc,
            'total'=>$total,
            'updated_at'=>date('Y-m-d H:i:s')

        ];
        $update_expense = DB::table('travelling_expense_bill')
                        ->where('id',$id)
                        ->where('travellingDate',$date)
                        ->where('user_id',$user_id)
                        ->where('company_id',$company_id)
                        ->update($myArr);
        if($update_expense)
        {
            return redirect()->intended('travelling-expenses');
        }
    }


    ##................................... Modern Trade Reports starts here  ..........................................##
     public function userSalesReportModern(Request $request)
        {
            if ($request->ajax()) {
                
                $company_id = Auth::user()->company_id;
                $region = $request->region;
                $area = $request->area;
                $user_id = $request->user_id;
                $product=$request->product;
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

                $arr = [];
                $query_data =DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                ->join('location_view','person.state_id','=','location_view.l3_id')
                ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'dealer.name AS dealer_name',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS date"),'order_id','call_status','l3_name','customer_name AS retailer_name','customer_number AS customer_number','image_name')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                // ->where('_role.role_id','=','60')
                ->where('location_id',0)
                ->where('retailer_id',0)
                ->where('user_sales_order.company_id',$company_id)
                ->groupBy('user_sales_order.order_id');

                if(!empty($user_id))
                 {   
                    $query_data->whereIn('user_id',$user_id);
                }
            if(!empty($region)){

                    $query_data->whereIn('l2_id',$region);
            }
            if(!empty($area)){

                    $query_data->whereIn('l3_id',$area);
            }

    
                $query=$query_data->get();
            //dd($query);
                 $out=array();
                 $proout=array();
            if (!empty($query)) {
                foreach ($query as $k => $d) {
                    $uid=$d->order_id;
                    $proout = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->where('user_sales_order.order_id', $uid)
                            ->where('user_sales_order.company_id',$company_id)
                            ->select('quantity','rate','catalog_product.name as product_name');
                    
                   
                   if(!empty($product))
                   {
                    $proout->whereIn('product_id',$product);
                   }
                   $out[$uid]=$proout->get(); 
                }
            }
            // dd($out);
                return view('reports.user_sale_report_modern.ajax', [
                    'records' => $query,
                    'details' => $out
                ]);
            } else {
                echo '<p class="alert-danger">Data Not Found</p>';
            }
        }
    ##................................... Modern Trade Reports ends here  ..........................................##

    public function manacle_overall_report(Request $request)
    {
        $date = !empty($request->month)?$request->month:date('Y-m');

        $user_sale_count = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$date'")
                        ->groupBy('user_sales_order.company_id')
                        ->pluck(DB::raw("COUNT( DISTINCT user_id) as user_id"),'user_sales_order.company_id');

        $attendance_count = DB::table('user_daily_attendance')
                        ->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$date'")
                        ->groupBy('company_id')
                        ->pluck(DB::raw("COUNT( DISTINCT user_id) as user_id"),'user_daily_attendance.company_id');

        $user_primary_count = DB::table('user_primary_sales_order')
                            ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                            ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$date'")
                            ->groupBy('user_primary_sales_order.company_id')
                            ->pluck(DB::raw("COUNT( DISTINCT created_person_id) as created_person_id"),'user_primary_sales_order.company_id');

        $active_details = DB::table('person_login')
                        ->join('person_details','person_details.person_id','=','person_login.person_id')
                        ->whereRaw("DATE_FORMAT(created_on,'%Y-%m')='$date'")
                        ->where('person_status',1)
                        ->groupBy('person_login.company_id')
                        ->pluck(DB::raw("COUNT( DISTINCT person_login.person_id) as id"),'person_login.company_id');

        $active_details_data = DB::table('person_login')
                        ->join('person_details','person_details.person_id','=','person_login.person_id')
                        // ->whereRaw("DATE_FORMAT(created_on,'%Y-%m')='$date'")
                        ->where('person_status',1)
                        ->groupBy('person_login.company_id')
                        ->pluck(DB::raw("COUNT( DISTINCT person_login.person_id) as id"),'person_login.company_id');

        $deactive_details = DB::table('person_login')
                        ->join('person_details','person_details.person_id','=','person_login.person_id')
                        ->whereRaw("DATE_FORMAT(deleted_deactivated_on,'%Y-%m')='$date'")
                        ->where('person_status',0)
                        ->groupBy('person_login.company_id')
                        ->pluck(DB::raw("COUNT( DISTINCT person_login.person_id) as id"),'person_login.company_id');

        $deleted_details = DB::table('person_login')
                        ->join('person_details','person_details.person_id','=','person_login.person_id')
                        ->whereRaw("DATE_FORMAT(created_on,'%Y-%m')='$date'")
                        ->where('person_status',9)
                        ->groupBy('person_login.company_id')
                        ->pluck(DB::raw("COUNT( DISTINCT person_login.person_id) as id"),'person_login.company_id');

        $ta_da_allowance_user_count = DB::table('travelling_expense_bill')
                                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$date'")
                                    ->groupBy('company_id')
                                    ->pluck(DB::raw("COUNT( DISTINCT user_id) as user_id"),'company_id');

        $coampany_details = DB::table('company')
                        ->where('status',1)
                        ->groupBy('id')
                        ->get();

        return view('reports.manacle_reports.ajax', [
                    'records' => $coampany_details,
                    'user_sale_count'=> $user_sale_count,
                    'attendance_count'=> $attendance_count,
                    'user_primary_count'=> $user_primary_count,
                    'active_details'=> $active_details,
                    'deactive_details'=> $deactive_details,
                    'deleted_details'=> $deleted_details,
                    'active_details_data'=> $active_details_data,
                    'date'=> $date,
                    'ta_da_allowance_user_count'=> $ta_da_allowance_user_count,
                ]);

    }

    public function dealer_counter_sale_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


        $main_query = DB::table('counter_sale_summary')
                    ->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
                    ->join('catalog_product','catalog_product.id','=','counter_sale_details.product_id')
                    ->leftJoin('person','person.id','=','counter_sale_summary.created_by_person')
                    ->leftJoin('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','counter_sale_summary.dealer_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                    // ->join('location_3','location_3.id','=','dealer.state_id')
                    // ->join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('csa','csa.c_id','=','dealer.csa_id')
                    ->select('l4_name','person.mobile','person_id_senior','rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("group_concat(distinct l7_name) as l7_name"),DB::raw("group_concat(distinct l6_name) as l6_name"),DB::raw("group_concat(distinct l5_name) as l5_name"),'sale_date','counter_sale_summary.order_id','csa.csa_name','dealer.id as dealer_id','dealer.name as dealer_name','l3_name','catalog_product.name as product_name','counter_sale_details.cases as cases','counter_sale_details.case_rate as case_rate','counter_sale_details.pcs as pieces','counter_sale_details.pcs_rate as piece_rate',DB::raw("((counter_sale_details.cases*counter_sale_details.case_rate)+(counter_sale_details.pcs*counter_sale_details.pcs_rate)) as total_sale"))
                    ->whereRaw("DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')<= '$to_date'")
                    ->where('counter_sale_summary.company_id',$company_id)         
                      // ->where('person.company_id',$company_id)         
                      ->where('dealer.company_id',$company_id) 
                      ->groupBy('counter_sale_summary.created_by_person','counter_sale_summary.dealer_id','counter_sale_summary.sale_date','counter_sale_details.product_id','counter_sale_details.order_id');
                      // if(!empty($state))
                      // {
                      //     $main_query->whereIn('location_3.id',$state);
                      // }
                      // if(!empty($location2))
                      // {
                      //     $main_query->whereIn('location_2.id',$location2);
                      // }
                        if (!empty($request->location_3)) 
                        {
                            $location_3 = $request->location_3;
                            $main_query->whereIn('l3_id', $location_3);
                        }
                        if (!empty($request->location_4)) 
                        {
                            $location_4 = $request->location_4;
                            $main_query->whereIn('l4_id', $location_4);
                        }
                        if (!empty($request->location_5)) 
                        {
                            $location_5 = $request->location_5;
                            $main_query->whereIn('l5_id', $location_5);
                        }
                        if (!empty($request->location_6)) 
                        {
                            $location_6 = $request->location_6;
                            $main_query->whereIn('l6_id', $location_6);
                        }
                        if (!empty($request->role)) 
                        {
                            $role = $request->role;
                            $main_query->whereIn('person.role_id', $role);
                        }

        $main_query_data = $main_query->get();    

        // $location_6_data = DB::table('dealer')
        //             ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
        //             ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
        //             ->join('location_6','location_6.id','=','location_7.location_6_id')
        //             ->groupBy('dealer.id')
        //             ->pluck(,'dealer.id'); 

        // $location_5_data = DB::table('dealer')
        //             ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
        //             ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
        //             ->join('location_6','location_6.id','=','location_7.location_6_id')
        //             ->join('location_5','location_5.id','=','location_6.location_5_id')
        //             ->groupBy('dealer.id')
        //             ->pluck(,'dealer.id'); 



               

                    // dd($main_query_data);
                return view('reports.dealer_counter_sale_report.ajax', 
                [
                    'main_query_data'=>$main_query_data,
                    'from_date'=>$from_date,
                    'to_date'=>$to_date,
                    // 'location_6_data'=>$location_6_data,
                    // 'location_5_data'=>$location_5_data,
                 
                ]);
    }

    public function dms_dealer_complaint_details_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


        $main_query = DB::table('complaint_feedback_data')
                    ->join('dealer','dealer.id','=','complaint_feedback_data.dealer_id')
                    ->join('location_3','location_3.id','=','dealer.state_id')
                    ->join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('_complaint_feedback_array','_complaint_feedback_array.id','=','complaint_feedback_data.complaint_feedback_id')
                    ->select('complaint_feedback_data.*','_complaint_feedback_array.name as complaint_feedback_name','dealer.name as dealer_name','dealer.other_numbers as dealer_no','location_3.name as l3_name','location_2.name as l2_name')
                    ->whereRaw("DATE_FORMAT(complaint_feedback_data.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(complaint_feedback_data.date,'%Y-%m-%d')<= '$to_date'")
                    ->where('complaint_feedback_data.company_id',$company_id)
                    ->where('dealer.company_id',$company_id)
                    ->groupBy('order_id');
                      if(!empty($state))
                      {
                          $main_query->whereIn('location_3.id',$state);
                      }
                      if(!empty($location2))
                      {
                          $main_query->whereIn('location_2.id',$location2);
                      }

        $main_query_data = $main_query->get();    



               

                    // dd($main_query_data);
                return view('reports.dms_dealer_complaint_report.ajax', 
                [
                    'main_query_data'=>$main_query_data,
                    'from_date'=>$from_date,
                    'to_date'=>$to_date,
                    
                 
                ]);
    }


    public function fullfillment_sale_report(Request $request)
    {
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $user_id = $request->user_id;
        $location_3 = $request->location_3;
        $location_4 = $request->location_4;
        $location_5 = $request->location_5;
        $location_6 = $request->location_6;
       
        $division = $request->division;
        $dealer_id = $request->dealer;
        $retailer_id = $request->retailer;
        $company_id = Auth::user()->company_id;

        $fullfillment_data_details= DB::table('fullfillment_order')
                            ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                            ->join('purchase_order','purchase_order.order_id','=','fullfillment_order.order_id')
                            ->join('dealer','dealer.id','=','fullfillment_order.dealer_id')
                            ->join('location_3','location_3.id','=','dealer.state_id')
                            ->join('location_2','location_2.id','=','location_3.location_2_id')
                            // ->join('_role','_role.role_id','=','person.role_id')
                            // ->join('person_login','person_login.person_id','=','person.id')
                            // ->join('location_view','location_view.l7_id','=','retailer.location_id')
                            ->select('purchase_order.invoice_no_p','invoice_date','reciept_image','dealer.other_numbers as dealer_no','dealer.id as dealer_id','fullfillment_order.time as time','fullfillment_order.order_id as order_id','fullfillment_order.date as date','location_3.name as l3_name','location_2.name as l2_name','dealer.name as dealer_name')
                            ->whereRaw("DATE_FORMAT(fullfillment_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(fullfillment_order.date,'%Y-%m-%d')<='$to_date' ")
                            ->where('fullfillment_order.company_id',$company_id)
                            ->groupBy('fullfillment_order.order_id');
                            if(!empty($user_id))
                            {
                                $fullfillment_data_details->whereIn('person.id',$user_id);
                            }
                            if(!empty($location_3))
                            {
                                $fullfillment_data_details->whereIn('dealer.state_id',$location_3);
                            }
                            if(!empty($location_4))
                            {
                                $fullfillment_data_details->whereIn('l4_id',$location_4);
                            }
                            if(!empty($location_5))
                            {
                                $fullfillment_data_details->whereIn('l5_id',$location_5);
                            }
                            if(!empty($location_6))
                            {
                                $fullfillment_data_details->whereIn('l6_id',$location_6);
                            }
                            
                            if(!empty($division))
                            {
                                $fullfillment_data_details->whereIn('dealer.division_id',$division);
                            }
                            if(!empty($dealer_id))
                            {
                                $fullfillment_data_details->whereIn('dealer.id',$dealer_id);
                            }

                            if(!empty($retailer_id))
                            {
                                $fullfillment_data_details->whereIn('retailer.id',$retailer_id);
                            }


        $fullfillment_data = $fullfillment_data_details->get();
        // dd($fullfillment_data);
        $out = array();
        foreach ($fullfillment_data as $key => $value) 
        {
            $uid=$value->order_id;
            $proout = DB::table('fullfillment_order_details')
        	->join('catalog_product','catalog_product.id','=','fullfillment_order_details.product_id')
            ->where('fullfillment_order_details.order_id', $uid)
            ->where('fullfillment_order_details.company_id',$company_id)
            ->select('product_fullfiment_cases','product_fullfiment_scheme_qty','catalog_product.name as product_name','product_case_rate',DB::raw("SUM(product_case_rate*product_fullfiment_cases) as sale_value"));
            
           if(!empty($product))
           {
                $proout->whereIn('product_id',$product);
           }
            $out[$uid]=$proout->groupBy('fullfillment_order_details.id')->get(); 
        }
        // dd($out);

        return view('reports.fullifillment_order.ajax', [
                    'main_query_data' => $fullfillment_data,
                    'sub_query_data' => $out,
                    'from_date'=> $from_date,
                    'to_date'=> $to_date,
                   

                ]);
    }


    public function common_fullfillment_sale_report(Request $request)
    {
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $user_id = $request->user_id;
        $location_3 = $request->location_3;
        $location_4 = $request->location_4;
        $location_5 = $request->location_5;
        $location_6 = $request->location_6;
       
        $division = $request->division;
        $dealer_id = $request->dealer;
        $retailer_id = $request->retailer;
        $company_id = Auth::user()->company_id;

       


        $fullfillment_data_details= DB::table('fullfillment_order')
                            // ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                            ->join('person','person.id','=','fullfillment_order.created_by')
                            // ->join('location_3','location_3.id','=','person.state_id')
                            // ->join('location_2','location_2.id','=','location_3.location_2_id')
                            ->join('_role','_role.role_id','=','person.role_id')
                            ->join('retailer','retailer.id','=','fullfillment_order.retailer_id')
                            ->join('location_view','location_view.l7_id','=','retailer.location_id')
                            ->select('person.emp_code','person_id_senior','person.mobile as mobile','rolename','retailer.other_numbers','retailer.landline','retailer.id as retailer_id','fullfillment_order.time as time','fullfillment_order.order_id as order_id','fullfillment_order.date as date','l3_name','l2_name','l4_name','l5_name','l6_name','l7_name','retailer.name as retailer_name','person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'))
                            ->whereRaw("DATE_FORMAT(fullfillment_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(fullfillment_order.date,'%Y-%m-%d')<='$to_date' ")
                            ->where('fullfillment_order.company_id',$company_id)
                            ->orderBy('fullfillment_order.date','DESC')
                            ->groupBy('fullfillment_order.order_id');
                       
                            if (!empty($request->location_3)) 
                            {
                                $location_3 = $request->location_3;
                                $tracking->whereIn('location_3.id', $location_3);
                            }
                            if (!empty($request->location_4)) 
                            {
                                $location_4 = $request->location_4;
                                $tracking->whereIn('location_4.id', $location_4);
                            }
                            if (!empty($request->location_5)) 
                            {
                                $location_5 = $request->location_5;
                                $tracking->whereIn('location_5.id', $location_5);
                            }
                            if (!empty($request->location_6)) 
                            {
                                $location_6 = $request->location_6;
                                $tracking->whereIn('location_6.id', $location_6);
                            }
                            if (!empty($request->role)) 
                            {
                                $role = $request->role;
                                $tracking->whereIn('person.role_id', $role);
                            }
                            #User filter
                            if (!empty($user)) {
                                $tracking->whereIn('person.id', $user);
                            }
                            if (!empty($dealer)) {
                                $tracking->whereIn('dealer_id', $dealer);
                            }
                            

                            if(!empty($retailer_id))
                            {
                                $fullfillment_data_details->whereIn('retailer.id',$retailer_id);
                            }


        $fullfillment_data = $fullfillment_data_details->get();
        // dd($fullfillment_data);
        $out = array();
        foreach ($fullfillment_data as $key => $value) 
        {
            $uid=$value->order_id;
            $proout = DB::table('fullfillment_order_details')
            ->where('order_id', $uid)
            ->where('company_id',$company_id)
            ->select('product_fullfiment_cases','product_fullfiment_scheme_qty','product_name','product_case_rate','product_rate as piece_rate','product_fullfiment_qty',DB::raw("SUM((product_case_rate*product_fullfiment_cases)+(product_rate*product_fullfiment_qty)) as sale_value"));
            
           if(!empty($product))
           {
                $proout->whereIn('product_id',$product);
           }
            $out[$uid]=$proout->groupBy('id')->get(); 
        }
        // dd($out);

        return view('reports.fullifillment_order.commonajax', [
                    'main_query_data' => $fullfillment_data,
                    'sub_query_data' => $out,
                    'from_date'=> $from_date,
                    'to_date'=> $to_date,
                   

                ]);
    }

    public function dms_submit_reciept_no(Request $request)
    {

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = DB::table('fullfillment_order')
                                ->where('order_id',$request->order_id)
                                ->where('dealer_id',$request->dealer_id)
                                ->update(['reciept_image' => 'reciept-images/'.$name]);

                    $request->file('imageFile')->move("reciept-images", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
            $data['code'] = 200;
            $data['result'] = '';
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);


    }

    public function attendanceSummaryReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        // dd($state);

        $start = strtotime($request->from_date);
        $end = strtotime($request->to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }



        $state_data = DB::table('location_3')->where('status',1)->where('company_id',$company_id);
        if(!empty($state))
        {
            $state_data->whereIn('location_3.id',$state);
        }

        $state = $state_data->pluck('name','id');

        $main_query = DB::table('user_daily_attendance')
                      ->join('person','person.id','=','user_daily_attendance.user_id')  
                      ->join('location_3','location_3.id','=','person.state_id')
                      ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                      ->where('user_daily_attendance.company_id',$company_id)
                      ->where('person.company_id',$company_id)
                      ->where('location_3.company_id',$company_id)
                      ->groupBy('location_3.id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                      ->pluck(DB::raw("COUNT( DISTINCT user_id) as user_id"),DB::raw("CONCAT(location_3.id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));

                    //   dd($main_query);


            return view('reports.user-attendance-summary.ajax', [
            'records' => $main_query,
            'state' => $state,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'datearray'=> $datearray,
            'datediff'=> $datediff,
            

        ]);
            




    }


    public function salesManSecondarySalesReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


        $start = strtotime($request->from_date);
        $end = strtotime($request->to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }




        $person_data = DB::table('person')
                       ->join('_role','_role.role_id','=','person.role_id')
                       ->join('location_3','location_3.id','=','person.state_id')
                       ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','person.mobile','location_3.name as state','person.person_id_senior')
                       ->where('person.company_id',$company_id) 
                       ->where('_role.company_id',$company_id) 
                       ->where('location_3.company_id',$company_id) 
                       ->groupBy('person.id');
                       if(!empty($state))
                       {
                           $person_data->whereIn('location_3.id',$state);
                       }
                       if(!empty($user))
                       {
                           $person_data->whereIn('person.id',$user);
                       }
        $person = $person_data->get();               




        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');


        $main_query = DB::table('user_sales_order')
                      ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')  
                      ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date' ")
                      ->where('user_sales_order.company_id',$company_id)
                      ->where('user_sales_order_details.company_id',$company_id)
                      ->groupBy('user_id','date')
                      ->pluck(DB::raw("SUM((rate*quantity)+(case_rate*user_sales_order_details.case_qty)) as sale"),DB::raw("CONCAT(user_id,date) as data"));



            return view('reports.salesManSecondarySalesReport.ajax', [
            'records' => $main_query,
            'person' => $person,
            'senior_name_data' => $senior_name_data,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'datearray'=> $datearray,
            'datediff'=> $datediff,
            

        ]);
            
    }


    public function salesTeamAttendanceSummaryReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;


        $start = strtotime($request->from_date);
        $end = strtotime($request->to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }


        $person_data = DB::table('person')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_6','location_6.id','=','person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','person.mobile','location_3.name as state','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.person_id_senior','person.emp_code')
                ->where('person.company_id',$company_id) 
                ->where('_role.company_id',$company_id) 
                ->where('location_3.company_id',$company_id) 
                ->groupBy('person.id');
                if(!empty($request->location_3))
                {
                    $person_data->whereIn('location_3.id',$request->location_3);
                }
                 if(!empty($request->location_4))
                {
                    $person_data->whereIn('location_4.id',$request->location_4);
                }
                 if(!empty($request->location_5))
                {
                    $person_data->whereIn('location_5.id',$request->location_5);
                }
                 if(!empty($request->location_6))
                {
                    $person_data->whereIn('location_6.id',$request->location_6);
                }
                if(!empty($user))
                {
                    $person_data->whereIn('person.id',$user);
                }
        $person = $person_data->get();   

        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

        
        $working_status_header = DB::table('_working_status')->where('company_id',$company_id)->where('status',1)->pluck('name','id')->toArray();

        $working_status_header_count = count($working_status_header);

        $working_status = DB::table('user_daily_attendance')
                         ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                         ->where('user_daily_attendance.company_id',$company_id)
                         ->where('_working_status.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                        ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                        ->pluck('name',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));

        $att_time = DB::table('user_daily_attendance')
                        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                        ->where('user_daily_attendance.company_id',$company_id)
                        ->where('_working_status.company_id',$company_id)
                       ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                       ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                       ->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i-%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));                


        $working_status_data = DB::table('user_daily_attendance')
                        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                        ->where('user_daily_attendance.company_id',$company_id)
                        ->where('_working_status.company_id',$company_id)
                       ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                       ->groupBy('user_id','_working_status.id')
                       ->pluck(DB::raw("COUNT(user_id) as count"),DB::raw("CONCAT(user_id,_working_status.id) as data"));  
                       

        return view('reports.sales-team-attendance-summary.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            'working_status_header' => $working_status_header,
            'working_status' => $working_status,
            'working_status_header_count' => $working_status_header_count,
            'working_status_data' => $working_status_data,
            'att_time' => $att_time,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'datearray'=> $datearray,
            'datediff'=> $datediff,
            

        ]);



    }


    public function callTimeSummaryReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        // $from_date = $request->from_date;
        // $to_date = $request->to_date;

         $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


        $start = strtotime($from_date);
        $end = strtotime($to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $this->without_junior == 0)
        {
           $datasenior='';
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
             
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
             if(empty($datasenior)){
                 $datasenior[]=$login_user;
                        }
        }


        $person_data = DB::table('person')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_6','location_6.id','=','person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','person.mobile','location_3.name as state','person.person_id_senior','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.emp_code')
                ->where('person.company_id',$company_id) 
                ->where('_role.company_id',$company_id) 
                ->where('location_3.company_id',$company_id) 
                ->groupBy('person.id');
                if (!empty($datasenior)) 
                {
                    $person_data->whereIn('person.id', $datasenior);
                }
                if(!empty($state))
                {
                    $person_data->whereIn('location_3.id',$state);
                }
                if(!empty($request->user))
                {
                    $person_data->whereIn('person.id',$request->user);
                }
                 if(!empty($request->role))
                {
                    $person_data->whereIn('person.role_id',$request->role);
                }
                 if(!empty($request->location_3))
                {
                    $person_data->whereIn('location_3.id',$request->location_3);
                }
                 if(!empty($request->location_4))
                {
                    $person_data->whereIn('location_4.id',$request->location_4);
                }
                 if(!empty($request->location_5))
                {
                    $person_data->whereIn('location_5.id',$request->location_5);
                }
                   if(!empty($request->location_6))
                {
                    $person_data->whereIn('location_6.id',$request->location_6);
                }
        $person = $person_data->get();   

        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');


        $first_call = DB::table('user_sales_order')
                      ->where('company_id',$company_id)  
                      ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date' ")
                      ->groupBy('user_id','date')
                      ->pluck(DB::raw("MIN(time) as tie"),DB::raw("CONCAT(user_id,date) as data"));  


        $last_call = DB::table('user_sales_order')
                      ->where('company_id',$company_id)  
                      ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date' ")
                      ->groupBy('user_id','date')
                      ->pluck(DB::raw("MAX(time) as tie"),DB::raw("CONCAT(user_id,date) as data"));                



        return view('reports.call-time-summary.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'datearray'=> $datearray,
            'datediff'=> $datediff,
            'first_call'=> $first_call,
            'last_call'=> $last_call,
            

        ]);


    }

    public function notificationNonContactedReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $person_data = DB::table('sale_reason_remarks')
                    ->join('retailer','retailer.id','=','sale_reason_remarks.retailer_id')
                    ->join('person','person.id','=','sale_reason_remarks.user_id')
                    ->join('location_3','location_3.id','=','person.state_id')
                    ->join('location_6','location_6.id','=','person.town_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->select('sale_reason_remarks.date','sale_reason_remarks.time','sale_reason_remarks.sale_remarks','sale_reason_remarks.retailer_id','retailer.name as retailer_name','person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.mobile','location_3.name as state','person.person_id_senior','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.emp_code')
                    ->where('person.company_id',$company_id) 
                    ->where('retailer.company_id',$company_id) 
                    ->where('sale_reason_remarks.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(sale_reason_remarks.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(sale_reason_remarks.date,'%Y-%m-%d')<='$to_date' ")
                    ;
                    if(!empty($request->location_3))
                    {
                        $person_data->whereIn('location_3.id',$request->location_3);
                    }
                    if(!empty($request->location_4))
                    {
                        $person_data->whereIn('location_4.id',$request->location_4);
                    }
                    if(!empty($request->location_5))
                    {
                        $person_data->whereIn('location_5.id',$request->location_5);
                    }
                    if(!empty($request->location_6))
                    {
                        $person_data->whereIn('location_6.id',$request->location_6);
                    }
                    if(!empty($user))
                    {
                        $person_data->whereIn('person.id',$user);
                    }
        $person = $person_data->get();   





        return view('reports.notification-non-contacted.ajax', [
            'records' => $person,          

        ]);


    }

    public function dealerWiseSSReport(Request $request)
    {
        if($request->ajax())
        {
            $company_id = Auth::user()->company_id;
            $csa = $request->csa;
            $dealer = $request->dealer;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


            $dealer_data = DB::table('dealer')
                            ->join('csa','csa.c_id','=','dealer.csa_id')    
                            ->select('dealer.id as dealer_id','csa.c_id as csa_id','dealer.name as dealer_name','csa.csa_name as csa_name')
                            ->where('dealer.company_id',$company_id)
                            ->where('csa.company_id',$company_id)
                            ->groupBy('dealer.id');
                            if(!empty($csa))
                            {
                                $dealer_data->whereIn('csa.c_id',$csa);
                            }
                            if(!empty($dealer))
                            {
                                $dealer_data->whereIn('dealer.id',$dealer);
                            }
            $dealer = $dealer_data->get();

            $dealer_ss = DB::table('user_sales_order')
                         ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')  
                         ->where('user_sales_order.company_id',$company_id)     
                         ->where('user_sales_order_details.company_id',$company_id)    
                         ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date' ")
                         ->groupBy('dealer_id')
                         ->pluck(DB::raw("SUM((rate*quantity)+(case_rate*user_sales_order_details.case_qty)) as sale"),'dealer_id');


            $dealer_counter = DB::table('counter_sale_summary')
                              ->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')  
                              ->where('counter_sale_summary.company_id',$company_id)     
                              ->where('counter_sale_details.company_id',$company_id)    
                              ->whereRaw("DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')<='$to_date' ")
                              ->groupBy('dealer_id')
                              ->pluck(DB::raw("SUM(case_rate*cases) as sale"),'dealer_id');

            
            return view('reports.dealerWiseSSReport.ajax', [
                    'records' => $dealer,
                    'dealer_ss' => $dealer_ss,
                    'dealer_counter' => $dealer_counter,
                ]);
        }
     
    }


    public function mobileOnOffReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $person_data = DB::table('user_mobile_details')
                       ->join('person','person.id','=','user_mobile_details.user_id')
                       ->join('_role','_role.role_id','=','person.role_id')
                       ->join('location_3','location_3.id','=','person.state_id')
                        ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                       ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','location_3.name as state','person.person_id_senior',
                       'user_mobile_details.device_name','user_mobile_details.device_manuf',DB::raw("DATE_FORMAT(user_mobile_details.server_date_time,'%Y-%m-%d') as date"),DB::raw("DATE_FORMAT(user_mobile_details.server_date_time,'%H:%i:%s') as time"),'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.emp_code','person.mobile')
                       ->where('person.company_id',$company_id) 
                       ->where('_role.company_id',$company_id) 
                       ->where('location_3.company_id',$company_id) 
                       ->where('user_mobile_details.company_id',$company_id) 
                       ->whereRaw("DATE_FORMAT(user_mobile_details.server_date_time,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_mobile_details.server_date_time,'%Y-%m-%d')<='$to_date' ");

                       if(!empty($request->location_3))
                       {
                           $person_data->whereIn('location_3.id',$request->location_3);
                       }
                        if(!empty($request->location_4))
                       {
                           $person_data->whereIn('location_4.id',$request->location_4);
                       }
                        if(!empty($request->location_5))
                       {
                           $person_data->whereIn('location_5.id',$request->location_5);
                       }
                        if(!empty($request->location_6))
                       {
                           $person_data->whereIn('location_6.id',$request->location_6);
                       }
                       if(!empty($user))
                       {
                           $person_data->whereIn('person.id',$user);
                       }
            $person = $person_data->get();  


        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

          
        return view('reports.mobile-on-off.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            'from_date'=> $from_date,
            'to_date'=> $to_date,           

        ]);


    }


    public function userComplaintReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $person_data = DB::table('Complaint_report')
                       ->join('person','person.id','=','Complaint_report.user_id')
                       ->join('_role','_role.role_id','=','person.role_id')
                       ->join('location_3','location_3.id','=','person.state_id')
                       ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                       ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','location_3.name as state','person.person_id_senior','Complaint_report.*','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.emp_code','person.mobile')
                       ->where('person.company_id',$company_id) 
                       ->where('_role.company_id',$company_id) 
                       ->where('location_3.company_id',$company_id) 
                       ->where('Complaint_report.company_id',$company_id) 
                       ->whereRaw("DATE_FORMAT(Complaint_report.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(Complaint_report.date,'%Y-%m-%d')<='$to_date' ");

                       if(!empty($request->location_3))
                       {
                           $person_data->whereIn('location_3.id',$request->location_3);
                       }
                        if(!empty($request->location_4))
                       {
                           $person_data->whereIn('location_4.id',$request->location_4);
                       }
                        if(!empty($request->location_5))
                       {
                           $person_data->whereIn('location_5.id',$request->location_5);
                       }
                        if(!empty($request->location_6))
                       {
                           $person_data->whereIn('location_6.id',$request->location_6);
                       }
                       if(!empty($user))
                       {
                           $person_data->whereIn('person.id',$user);
                       }
            $person = $person_data->get();  


        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

          
        return view('reports.user-complaint.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            'from_date'=> $from_date,
            'to_date'=> $to_date,           

        ]);


    }

    public function userSalesReportBtw(Request $request)
    {
        if ($request->ajax()) 
        {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $user_id = $request->user_id;
            $product=$request->product;
            $call_status = $request->call_status;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $arr = [];
            $query_data =DB::table('user_sales_order_view')
                ->select('non_productive_reason_id','remarks','mobile','role_name','dealer_id','user_id','retailer_id','user_name AS user_name','dealer_name',DB::raw("DATE_FORMAT(date,'%d-%m-%Y') AS date"),'order_id','call_status','l3_name','retailer_name','retailer_other_number','retailer_landline','emp_code','senior_id','l7_name')
                ->where('company_id',$company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'");

            if(!empty($user_id))
            {   
                $query_data->whereIn('user_id',$user_id);
            }
            if(!empty($region))
            {
                $query_data->whereIn('l3_id',$region);
            }
            if(!empty($call_status))
            {
                $query_data->whereIn('call_status',$call_status);
            }


            $query=$query_data->get();
            // dd($query);

            $senior_name = DB::table('person')
                           ->where('company_id',$company_id)
                           ->pluck(DB::raw('CONCAT_WS(" ",first_name,middle_name,last_name) as user_name'),'id')->toArray();  



            $product_percentage = DB::table('product_wise_scheme_plan_details')
                                ->where('incentive_type',1)
                                ->where('company_id',$company_id)
                                ->pluck('value_amount_percentage','product_id');
            $non_productive_reason_name = DB::table("_no_sale_reason")->where('company_id',$company_id)->where('status',1)->groupBy('id')->pluck('name','id');
            $out=array();
            $proout=array();
           if (!empty($query)) 
           {
                foreach ($query as $k => $d) 
                {
                    $uid=$d->order_id;
                     $proout = DB::table('user_sales_order_details_view')
                    ->where('order_id', $uid)
                    ->select('product_id','quantity','rate','product_name','scheme_qty as weight');
                    
                   if(!empty($product))
                   {
                        $proout->whereIn('product_id',$product);
                   }
                    $out[$uid]=$proout->get(); 
                }
            }
            // dd($out);
            return view('reports.user_sale_report.Btwajax', [
                'records' => $query,
                'details' => $out,
                'non_productive_reason_name'=>$non_productive_reason_name,
                'product_percentage'=> $product_percentage,
                'senior_name'=> $senior_name,
            ]);
        } 
        else 
        {
            echo '<p class="alert-danger">Data not Found</p>';
        }
    }


    ############################################## for target Reports #############################################################

    public function superStockistSkuMonthlyTargetReportDetails(Request $request)
    {
        if ($request->ajax()) 
        {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $user_id = $request->user_id;
            $product=$request->product;
            $call_status = $request->call_status;
            $month = $request->month;
          
            $arr = [];

            $query_data = DB::table('master_target')
                           ->join('csa','csa.c_id','=','master_target.csa_id')
                           ->join('location_3','location_3.id','=','csa.state_id')
                           ->join('catalog_product','catalog_product.id','=','master_target.product_id')
                           ->where('master_target.company_id',$company_id)
                           ->where('flag',2)
                           ->select('csa.csa_name','location_3.name as state_name','catalog_product.name as product_name','master_target.quantity_cases','master_target.csa_id','master_target.product_id')
                           ->whereRaw("DATE_FORMAT(master_target.from_date, '%Y-%m') = '$month'");

                               if(!empty($user_id))
                            {   
                                $query_data->whereIn('created_person_id',$user_id);
                            }
                            if(!empty($region))
                            {
                                $query_data->whereIn('location_3.id',$region);
                            }
                            if(!empty($call_status))
                            {
                                $query_data->whereIn('call_status',$call_status);
                            }

            $query=$query_data->groupBy('master_target.csa_id','master_target.product_id')->get()->toArray();


            $district = DB::table('csa')
                         ->join('location_view','location_view.l3_id','=','csa.state_id')   
                    //   ->join('csa_location_5','csa_location_5.csa_id','=','csa.c_id')
                    //   ->join('location_5','location_5.id','=','csa_location_5.csa_location_5_id')
                      ->where('csa.company_id',$company_id)
                      ->groupBy('csa.c_id')
                      ->pluck(DB::raw("group_concat(distinct(l5_name))"),'csa.c_id');

                    //   dd($district);


            // $user_name = DB::table('ss_user_primary_sales_order')    
            //             ->join('ss_user_primary_sales_order_details','ss_user_primary_sales_order_details.order_id','=','ss_user_primary_sales_order.order_id')      
            //             ->join('person','person.id','=','ss_user_primary_sales_order.created_person_id')     
            //             ->where('ss_user_primary_sales_order.company_id',$company_id)
            //             ->groupBy('ss_user_primary_sales_order.dealer_id','ss_user_primary_sales_order_details.product_id') 
            //             ->whereRaw("DATE_FORMAT(ss_user_primary_sales_order.sale_date, '%Y-%m') = '$month'")
            //             ->pluck(DB::raw("group_concat(distinct(first_name))"),DB::raw("CONCAT(dealer_id,ss_user_primary_sales_order_details.product_id) as data"));


            $user_name = DB::table('person')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->join('csa','c_id','=','dealer.csa_id')
                        ->where('csa.company_id',$company_id)
                        ->where('person.role_id',196)
                        ->groupBy('csa.c_id')
                        ->pluck(DB::raw("group_concat(distinct(first_name))"),'csa.c_id');



            $achivement_cases = DB::table('ss_user_primary_sales_order')    
                        ->join('ss_user_primary_sales_order_details','ss_user_primary_sales_order_details.order_id','=','ss_user_primary_sales_order.order_id')      
                        ->join('catalog_product','catalog_product.id','=','ss_user_primary_sales_order_details.product_id')     
                        ->where('ss_user_primary_sales_order.company_id',$company_id)
                        ->groupBy('ss_user_primary_sales_order.dealer_id','ss_user_primary_sales_order_details.product_id') 
                        ->whereRaw("DATE_FORMAT(ss_user_primary_sales_order.sale_date, '%Y-%m') = '$month'")
                        ->pluck(DB::raw("sum((cases)+(pcs/catalog_product.quantity_per_case)) as total"),DB::raw("CONCAT(dealer_id,ss_user_primary_sales_order_details.product_id) as data"));  


            $ss_case_rate = DB::table('product_rate_list')
                            ->where('company_id',$company_id)
                            ->groupBy('product_id')
                            ->pluck('ss_case_rate','product_id');







            // dd($achivement_cases);
           
            // dd($out);
            return view('reports.super_stockist_sku_month_target_report_details.ajax', [
                'records' => $query,
                'district' => $district,
                'user_name' => $user_name,
                'achivement_cases' => $achivement_cases,
                'ss_case_rate' => $ss_case_rate,
            ]);
        } 
        else 
        {
            echo '<p class="alert-danger">Data not Found</p>';
        }
    }



    public function distributorSkuMonthlyTargetReportDetails(Request $request)
    {
        if ($request->ajax()) 
        {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $user_id = $request->user_id;
            $product=$request->product;
            $call_status = $request->call_status;
            $month = $request->month;
          
            $arr = [];

            $query_data = DB::table('master_target')
                          ->join('dealer','dealer.id','=','master_target.distributor_id')
                           ->join('location_3','location_3.id','=','dealer.state_id')

                           ->join('csa','csa.c_id','=','dealer.csa_id')
                           ->join('catalog_product','catalog_product.id','=','master_target.product_id')
                           ->where('master_target.company_id',$company_id)
                           ->where('flag',2)
                           ->select('location_3.name as state_name','csa.csa_name','dealer.name as distributor','catalog_product.name as product_name','master_target.quantity_cases','master_target.distributor_id as dealer_id','master_target.product_id')
                           ->whereRaw("DATE_FORMAT(master_target.from_date, '%Y-%m') = '$month'");


                               if(!empty($user_id))
                            {   
                                $query_data->whereIn('created_person_id',$user_id);
                            }
                            if(!empty($region))
                            {
                                $query_data->whereIn('location_3.id',$region);
                            }
                            if(!empty($call_status))
                            {
                                $query_data->whereIn('call_status',$call_status);
                            }

            $query=$query_data->groupBy('master_target.distributor_id','master_target.product_id')->get()->toArray();


            // $district = DB::table('csa')
            //           ->join('csa_location_5','csa_location_5.csa_id','=','csa.c_id')
            //           ->join('location_5','location_5.id','=','csa_location_5.csa_location_5_id')
            //           ->where('csa.company_id',$company_id)
            //           ->groupBy('csa.c_id')
            //           ->pluck(DB::raw("group_concat(distinct(name))"),'csa.c_id');


            // $user_name = DB::table('ss_user_primary_sales_order')    
            //             ->join('ss_user_primary_sales_order_details','ss_user_primary_sales_order_details.order_id','=','ss_user_primary_sales_order.order_id')      
            //             ->join('person','person.id','=','ss_user_primary_sales_order.created_person_id')     
            //             ->where('ss_user_primary_sales_order.company_id',$company_id)
            //             ->groupBy('ss_user_primary_sales_order.dealer_id','ss_user_primary_sales_order_details.product_id') 
            //             ->whereRaw("DATE_FORMAT(ss_user_primary_sales_order.sale_date, '%Y-%m') = '$month'")
            //             ->pluck(DB::raw("group_concat(distinct(first_name))"),DB::raw("CONCAT(dealer_id,ss_user_primary_sales_order_details.product_id) as data"));


            $so_user_name = DB::table('person')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->where('person.company_id',$company_id)
                        // ->where('person.role_id',198)
                        ->groupBy('dealer_location_rate_list.dealer_id')
                        ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');


            // $ado_user_name = DB::table('person')
            //         ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
            //         ->where('person.company_id',$company_id)
            //         ->where('person.role_id',140)
            //         ->groupBy('dealer_location_rate_list.dealer_id')
            //         ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');            




            $achivement_cases = DB::table('user_primary_sales_order')    
                        ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')      
                        ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')     
                        ->where('user_primary_sales_order.company_id',$company_id)
                        ->groupBy('user_primary_sales_order.dealer_id','user_primary_sales_order_details.product_id') 
                        ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date, '%Y-%m') = '$month'")
                        ->pluck(DB::raw("sum((cases)+(pcs/catalog_product.quantity_per_case)) as total"),DB::raw("CONCAT(dealer_id,user_primary_sales_order_details.product_id) as data"));  



            $dealer_rate = DB::table('product_rate_list')
                            ->where('company_id',$company_id)
                            ->groupBy('product_id')
                            ->pluck('dealer_rate','product_id');







            // dd($achivement_cases);
           
            // dd($out);
            return view('reports.distributor_sku_month_target_report_details.ajax', [
                'records' => $query,
                'so_user_name' => $so_user_name,
                // 'ado_user_name' => $ado_user_name,
                'achivement_cases' => $achivement_cases,
                'dealer_rate' => $dealer_rate,
            ]);
        } 
        else 
        {
            echo '<p class="alert-danger">Data not Found</p>';
        }
    }


    public function target_ss_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->location_3;
        $location2 = $request->location_2;
        $role = $request->role;
        $user = $request->user;
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $month = $request->month;


        $main_query = DB::table('master_target')
                     ->join('csa','csa.c_id','=','master_target.csa_id')
                     ->join('location_3','location_3.id','=','csa.state_id')
                     ->join('location_2','location_2.id','=','location_3.location_2_id')
                     ->join('person','person.id','=','master_target.created_by')   
                     ->join('catalog_product','catalog_product.id','=','master_target.product_id')
                     ->select('month','order_id','location_3.name as state','csa.csa_name','csa.c_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'catalog_product.name as product_name','quantity_cases_rate','quantity_cases','sale_value','person.id as user_id')
                     ->where('master_target.month',$month)
                     ->where('master_target.company_id',$company_id)         
                     ->where('csa.company_id',$company_id)         
                     ->where('person.company_id',$company_id)      
                     ->where('catalog_product.company_id', $company_id)
                     ->where('master_target.csa_id','!=', 0)
                     ->groupBy('master_target.id');
                     if(!empty($state))
                     {
                         $main_query->whereIn('location_3.id',$state);
                     }
                     if(!empty($location2))
                     {
                         $main_query->whereIn('location_2.id',$location2);
                     }

        $main_query_data = $main_query->get();    




                    // dd($main_query_data);
                return view('reports.target_ss_report.ajax', 
                [
                    'main_query_data'=>$main_query_data,
                    'month'=>$month,
                 
                ]);
    }

    public function target_db_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->location_3;
        $location2 = $request->location_2;
        $role = $request->role;
        $user = $request->user;
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $month = $request->month;


        $main_query = DB::table('master_target')
                     ->join('dealer','dealer.id','=','master_target.distributor_id')
                     ->join('location_3','location_3.id','=','dealer.state_id')
                     ->join('location_2','location_2.id','=','location_3.location_2_id')
                     ->join('person','person.id','=','master_target.created_by')   
                     ->join('catalog_product','catalog_product.id','=','master_target.product_id')
                     ->select('month','order_id','location_3.name as state','dealer.name as dealer_name','dealer.id as dealer_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'catalog_product.name as product_name','quantity_cases_rate','quantity_cases','sale_value','person.id as user_id','dealer.csa_id')
                     ->where('master_target.month',$month)
                     ->where('master_target.company_id',$company_id)         
                     ->where('dealer.company_id',$company_id)         
                     ->where('person.company_id',$company_id)      
                     ->where('catalog_product.company_id', $company_id)
                     ->where('master_target.distributor_id','!=', 0)
                     ->groupBy('master_target.id');
                     if(!empty($state))
                     {
                         $main_query->whereIn('location_3.id',$state);
                     }
                     if(!empty($location2))
                     {
                         $main_query->whereIn('location_2.id',$location2);
                     }

        $main_query_data = $main_query->get();    


        $ss_name = DB::table('csa')->where('active_status',1)->where('company_id',$company_id)->pluck('csa_name','c_id');

        $dealer_town = DB::table('dealer')
                       ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id') 
                       ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                       ->join('location_6','location_6.id','=','location_7.location_6_id')
                       ->where('dealer.company_id',$company_id)
                       ->where('dealer_location_rate_list.company_id',$company_id)
                       ->where('location_7.company_id',$company_id)
                       ->where('location_6.company_id',$company_id)
                       ->groupBy('dealer.id')
                       ->pluck(DB::raw("group_concat(distinct location_6.name) as user_name"),'dealer.id'); 

        $dealer_dist = DB::table('dealer')
                       ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id') 
                       ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                       ->join('location_6','location_6.id','=','location_7.location_6_id')
                       ->join('location_5','location_5.id','=','location_6.location_5_id')
                       ->where('dealer.company_id',$company_id)
                       ->where('dealer_location_rate_list.company_id',$company_id)
                       ->where('location_7.company_id',$company_id)
                       ->where('location_6.company_id',$company_id)
                       ->groupBy('dealer.id')
                       ->pluck(DB::raw("group_concat(distinct location_5.name) as user_name"),'dealer.id');                




                    // dd($main_query_data);
                return view('reports.target_db_report.ajax', 
                [
                    'main_query_data'=>$main_query_data,
                    'month'=>$month,
                    'ss_name'=>$ss_name,
                    'dealer_town'=>$dealer_town,
                    'dealer_dist'=>$dealer_dist,
                 
                ]);
    }
    ############################################## for target Reports ends #############################################################

    public function dms_new_calling_report(Request $request)
    {
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        // $user_id = $request->user_id;
        // $location_3 = $request->location_3;
        // $location_4 = $request->location_4;
        // $location_5 = $request->location_5;
        // $location_6 = $request->location_6;
       
        $division = $request->division;
        $dealer_id = $request->dealer;
        $retailer_id = $request->retailer;
        $company_id = Auth::user()->company_id;
        $date = date('Y-m-d');

        $asm_name = DB::table('person')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('role_id',248)
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');

        $dms_calling_type = DB::table('dms_calling_type')
                            ->where('company_id',$company_id)
                            ->pluck('name','id');     
                            
                            

        if(!empty($dealer_id)){

        $check = DB::table('dms_new_calling')    
                ->where('dms_new_calling.company_id',$company_id) 
                ->whereRaw("DATE_FORMAT(dms_new_calling.date, '%Y-%m-%d') = '$date'")
                ->whereIn('dms_new_calling.distributor_id',$dealer_id)    
                ->pluck('distributor_id')->toArray();   
                
                
        $result = array_diff($dealer_id,$check);     

        }

        else{
            $result = array('0');
        }
        





        $data1 = DB::table('dealer')
                ->join('location_3','location_3.id','=','dealer.state_id')
                ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_contact','location_3.name as dealer_state')
                ->where('dealer.company_id',$company_id)
                ->where('dealer_status',1)
                ->whereIn('dealer.id',$result)
                ->groupBy('dealer_id')
                ->get()->toArray();





        $data2_query = DB::table('dms_new_calling')
                ->join('dealer','dealer.id','=','dms_new_calling.distributor_id')
                ->join('location_3','location_3.id','=','dealer.state_id')
                ->join('dms_calling_type','dms_calling_type.id','=','dms_new_calling.calling_type')
                ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_contact','location_3.name as dealer_state','dms_calling_type.name as calling_type_name','dms_new_calling.*')
                ->where('dms_new_calling.company_id',$company_id) 
                ->whereRaw("DATE_FORMAT(dms_new_calling.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(dms_new_calling.date, '%Y-%m-%d') <= '$to_date'");
                if(!empty($dealer_id)){
                  $data2_query->whereIn('dms_new_calling.distributor_id',$dealer_id);       
                }
        $data2 =  $data2_query->groupBy('dealer.id','dms_new_calling.date')->get()->toArray();


        $final = array_merge($data1,$data2);

        

        // dd($dms_calling_type);
       

        return view('reports.dms_new_calling.ajax', [
                    'main_query_data' => $final,
                    'asm_name' => $asm_name,
                    'dms_calling_type' => $dms_calling_type,
                  
                   

                ]);
    }

    public function dms_submit_new_calling(Request $request)
    {

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

    
        if (!empty($request->team_remarks) && !empty($request->calling_type) && !empty($request->dealer_id)) {

            if(!empty($request->date)){
                $myArr = [
                    'support_team_remarks' => $request->team_remarks,
                    'calling_type' => $request->calling_type,
                    'updated_at' => date('Y-m-d H:i:d'),
                    'updated_by' => $user_id,
                ];

                $update = DB::table('dms_new_calling')->where('company_id',$company_id)->where('date','=',$request->date)->where('distributor_id',$request->dealer_id)->update($myArr);

            }
            else{

                $myArr = [
                    'company_id' => $company_id,
                    'distributor_id' => $request->dealer_id,
                    'support_team_remarks' => $request->team_remarks,
                    'calling_type' => $request->calling_type,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'created_at' => date('Y-m-d H:i:d'),
                    'created_by' => $user_id,
                ];

                $insert = DB::table('dms_new_calling')->insert($myArr);

            }

            $data['code'] = 200;
            $data['result'] = '';
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);


    }



    public function dms_order_enquiry_report(Request $request)
    {
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        // $user_id = $request->user_id;
        // $location_3 = $request->location_3;
        // $location_4 = $request->location_4;
        // $location_5 = $request->location_5;
        // $location_6 = $request->location_6;
       
        $division = $request->division;
        $dealer_id = $request->dealer;
        $retailer_id = $request->retailer;
        $company_id = Auth::user()->company_id;

        $asm_name = DB::table('person')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('role_id',248)
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');

        $dms_calling_type = DB::table('dms_calling_type')
                            ->where('company_id',$company_id)
                            ->pluck('name','id');            




        $data2_query = DB::table('dms_dealer_enquiry_data')
                ->join('dealer','dealer.id','=','dms_dealer_enquiry_data.dealer_id')
                ->join('location_3','location_3.id','=','dealer.state_id')
                ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_contact','location_3.name as dealer_state','dms_dealer_enquiry_data.*','dms_dealer_enquiry_data.id as enquiry_id')
                ->where('dms_dealer_enquiry_data.company_id',$company_id) 
                ->whereRaw("DATE_FORMAT(dms_dealer_enquiry_data.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(dms_dealer_enquiry_data.date, '%Y-%m-%d') <= '$to_date'");
                if(!empty($dealer_id)){
                $data2_query->whereIn('dms_dealer_enquiry_data.dealer_id',$dealer_id);
                }       
        $data2 = $data2_query->get()->toArray();

      

        // dd($dms_calling_type);
       

        return view('reports.dms_order_enquiry.ajax', [
                    'main_query_data' => $data2,
                    'asm_name' => $asm_name,
                    'dms_calling_type' => $dms_calling_type,
                  
                   

                ]);
    }


    public function dms_update_enquiry_details(Request $request)
    {

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

    
        if (!empty($request->team_remarks) && !empty($request->enquiry_id) && !empty($request->dealer_id) && !empty($request->date)) {

           
                $myArr = [
                    'support_team_remarks' => $request->team_remarks,
                    'support_team_remarks_submit_date_time' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'support_team_updated_at' => date('Y-m-d H:i:s'),
                    'support_team_updated_by' => $user_id,
                ];

                $update = DB::table('dms_dealer_enquiry_data')->where('company_id',$company_id)->where('date','=',$request->date)->where('dealer_id',$request->dealer_id)->where('id',$request->enquiry_id)->update($myArr);

            
            $data['code'] = 200;
            $data['result'] = '';
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);


    }

    public function dms_dealer_details_for_document_report(Request $request)
    {
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        // $user_id = $request->user_id;
        // $location_3 = $request->location_3;
        // $location_4 = $request->location_4;
        // $location_5 = $request->location_5;
        // $location_6 = $request->location_6;
       
        $division = $request->division;
        $dealer_id = $request->dealer;
        $retailer_id = $request->retailer;
        $company_id = Auth::user()->company_id;
        $master_documents = DB::table('dms_document_master')->where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $asm_name = DB::table('person')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('role_id',248)
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');

        $dms_calling_type = DB::table('dms_calling_type')
                            ->where('company_id',$company_id)
                            ->pluck('name','id');            




        $data2_query = DB::table('dealer')
                ->join('location_3','location_3.id','=','dealer.state_id')
                ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_contact','location_3.name as dealer_state')
                ->where('dealer.company_id',$company_id); 
                // ->whereRaw("DATE_FORMAT(dms_dealer_enquiry_data.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(dms_dealer_enquiry_data.date, '%Y-%m-%d') <= '$to_date'");
                if(!empty($dealer_id)){
                $data2_query->whereIn('dealer.id',$dealer_id);
                }       
        $data2 = $data2_query->get()->toArray();

        $document_data = DB::table('dms_dealer_document_details')->where('company_id',$company_id)->get();
        $document_data_master = DB::table('dms_document_master')->where('company_id',$company_id)->pluck('name','id');
        $out = [];
        foreach ($document_data as $key => $value) 
        {
            $out[$value->dealer_id.$value->document_id.'image'] = $value->document_image;
            $out[$value->dealer_id.'date'] = $value->date;
            $out[$value->dealer_id.'time'] = $value->time;
        }

        // dd($out);
       

        return view('DmsDealerDocument.ajax', [
                    'main_query_data' => $data2,
                    'asm_name' => $asm_name,
                    'documen_data'=>$out,
                    'document_data_master'=>$document_data_master,
                    'master_documents'=> $master_documents,
                    'dms_calling_type' => $dms_calling_type,
                  
                   

                ]);
    }

    public function dms_upload_documents_dealer(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        if ($request->hasFile('imageFile')) {
          
                try {

                    $files = $request->file('imageFile');
                    $inc = 0;

                    foreach($files as $file_key => $file)
                    {
                        $name_random = date('YmdHis').$inc;
                        $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
                        $random_no = substr($str, 0,2);  // return always a new string 
                        $custom_image_name = date('YmdHis').$random_no;
                        $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
                        $file_name[] = $imageName;
                        $destinationPath = public_path('/dealer_documents/');
                        $file->move($destinationPath , $imageName);
                        $delete_data = DB::table('dms_dealer_document_details')
                                    ->where('dealer_id',$request->dealer_id)
                                    ->where('document_id',!empty($request->document_id[$file_key])?$request->document_id[$file_key]:'0')
                                    ->delete();

                        $personImage = DB::table('dms_dealer_document_details')->insert([
                                        'document_image' => 'dealer_documents/'.$imageName,
                                        'document_id' => !empty($request->document_id[$file_key])?$request->document_id[$file_key]:'0',
                                        'dealer_id' => $request->dealer_id,
                                        'company_id' => $company_id,
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'server_date_time' => date('Y-m-d H:i:s'),
                                    ]);
                        $inc++;

                    }

                  
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
           
            $data['code'] = 200;
            $data['result'] = '';
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);

    }
    public function order_wise_pdf_format(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;
        $quer_data = DB::table('user_sales_order')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                ->join('product_type','product_type.id','=','catalog_product.product_type')
                ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                ->select('catalog_product.name as product_name','final_secondary_qty','final_secondary_rate','retailer.name as retailer_name','user_sales_order.order_id as order_id','user_sales_order.date as sale_date','retailer.other_numbers as retailer_no','product_type.name as primary_unit','discount','remarks')
                ->where('user_sales_order.company_id',$company_id)
                ->where('user_sales_order.order_id',$order_id)
                ->groupBy('user_sales_order.order_id','product_id')
                ->get();

        $coampany_details = DB::table('company')->where('id',$company_id)->first();

        $order_discount = DB::table('user_sales_order')
                ->where('user_sales_order.company_id',$company_id)
                ->where('user_sales_order.order_id',$order_id)
                ->pluck('discount','order_id');

        $customPaper = array(0, 0, 1240, 1748);
        $pdf_name = $order_id.'.pdf';
        // dd($pdf_name);
        $pdf = PDF::loadView('pdf/pdf', ['coampany_details' => $coampany_details,'data_query'=>$quer_data]);
        $pdf->setPaper($customPaper);

        $pdf->save(public_path('pdf/'.$pdf_name));
            // return $pdf->download('some-filename.pdf');
        
        $pdf_path = public_path() . '/pdf/' .$pdf_name;

        if(!empty($quer_data))
        {
            $data['code'] = 200;
            $data['pdf_name'] = $pdf_name;
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['pdf_name'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    public function skuSalesReport(Request $request)
    {
        if($request->ajax())
        {
            // dd($request);
            $company_id = Auth::user()->company_id;
            $location_3 = $request->location_3;
            $location_4 = $request->location_4;
            $location_5 = $request->location_5;
            $location_6 = $request->location_6;
            $location_7 = $request->location_7;
            $user = $request->user;
            $dealer = $request->dealer;
            $role = $request->role;
            $product = $request->product;
            $retailer = $request->retailer;
            $product = $request->product;
            


            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            if(empty($check)){
            $secondary_man_report_query_data = DB::table('user_sales_order')
                                               ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                               ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id') 
                                               ->join('location_7','location_7.id','=','user_sales_order.location_id')  
                                               ->join('location_6','location_6.id','=','location_7.location_6_id')  
                                               ->join('location_5','location_5.id','=','location_6.location_5_id')  
                                               ->join('location_4','location_4.id','=','location_5.location_4_id')  
                                               ->join('location_3','location_3.id','=','location_4.location_3_id')  
                                               ->select(DB::raw("round(sum(rate*quantity),2) as total_value"),DB::raw("sum(quantity) as total_quantity"),'catalog_product.name as product_name','catalog_product.itemcode','product_id')
                                               ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                               ->where('user_sales_order.company_id',$company_id)
                                               ->where('catalog_product.company_id',$company_id)
                                               ->groupBy('product_id');

            }
            else{
                $secondary_man_report_query_data = DB::table('user_sales_order')
                                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')  
                                            ->join('location_7','location_7.id','=','user_sales_order.location_id')  
                                            ->join('location_6','location_6.id','=','location_7.location_6_id')  
                                            ->join('location_5','location_5.id','=','location_6.location_5_id')  
                                            ->join('location_4','location_4.id','=','location_5.location_4_id')  
                                            ->join('location_3','location_3.id','=','location_4.location_3_id')  
                                            ->select(DB::raw("round(sum(final_secondary_rate*final_secondary_qty),2) as total_value"),DB::raw("sum(final_secondary_qty) as total_quantity"),'catalog_product.name as product_name','catalog_product.itemcode','product_id')
                                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                            ->where('user_sales_order.company_id',$company_id)
                                            ->where('catalog_product.company_id',$company_id)
                                            ->groupBy('product_id');
            }

            if(!empty($request->location_3))
            {
                $secondary_man_report_query_data->whereIn('location_3.id',$request->location_3);
            }
             if(!empty($request->location_4))
            {
                $secondary_man_report_query_data->whereIn('location_4.id',$request->location_4);
            }
            if(!empty($request->location_5))
            {
                $secondary_man_report_query_data->whereIn('location_5.id',$request->location_5);
            }
            if(!empty($request->location_6))
            {
                $secondary_man_report_query_data->whereIn('location_6.id',$request->location_6);
            }
            if(!empty($request->location_7))
            {
                $secondary_man_report_query_data->whereIn('location_7.id',$request->location_7);
            }

            if(!empty($retailer))
            {
                $secondary_man_report_query_data->whereIn('user_sales_order.retailer_id',$retailer);
            }

            if(!empty($request->product))
            {
                $secondary_man_report_query_data->whereIn('user_sales_order_details.product_id',$request->product);
            }

             if(!empty($request->user))
            {
                $secondary_man_report_query_data->whereIn('user_sales_order.user_id',$request->user);
            }

               if(!empty($request->dealer))
            {
                $secondary_man_report_query_data->whereIn('user_sales_order.dealer_id',$request->dealer);
            }


       
            $secondary_man_report_query = $secondary_man_report_query_data->get();


            return view('reports.skuSales.ajax', [
                    'records' => $secondary_man_report_query,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'retailer' => $retailer,
                    'location_3'=> $request->location_3,
                    'location_4'=> $request->location_4,
                    'location_5'=> $request->location_5,
                    'location_6'=> $request->location_6,
                    'location_7'=> $request->location_7,
                    'user'=> $request->user,
                    'dealer'=> $request->dealer,
                    'role'=> $request->role,
                    'product'=> $request->product,
                    // 'state' => $state,
                    // 'head_quarter' => $head_quarter,
                    // 'town' => $town,
                    // 'beat' => $beat,
                ]);
        }
        else
        {
            echo '<p class="alert-danger">Data not Found</p>';
        } 
    }

    public function skuSalesDetails(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $product_id = $request->productid;
        $from_date = $request->fromdate;
        $to_date = $request->todate;
        $retailer_string = $request->retailer_filter;
        $state_string = $request->state_filter;
        $location_4_string = $request->location_4_string;
        $location_5_string = $request->location_5_string;
        $location_6_string = $request->location_6_string;
        $location_7_string = $request->location_7_string;
        $user_string = $request->user_string;
        $dealer_string = $request->dealer_string;
        $role_string = $request->role_string;
        $product_string = $request->product_string;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($retailer_string)){
            $retailer = "";
        }else{
        $retailer = explode(",",$retailer_string);
        }
        if(empty($state_string)){
            $state = "";
        }else{
        $state = explode(",",$state_string);
        }

        if(empty($location_4_string)){
            $location_4 = "";
        }else{
        $location_4 = explode(",",$location_4_string);
        }

        if(empty($location_5_string)){
            $location_5 = "";
        }else{
        $location_5 = explode(",",$location_5_string);
        }

        if(empty($location_6_string)){
            $location_6 = "";
        }else{
        $location_6 = explode(",",$location_6_string);
        }


        if(empty($location_7_string)){
            $location_7 = "";
        }else{
        $location_7 = explode(",",$location_7_string);
        }

        if(empty($user_string)){
            $user = "";
        }else{
        $user = explode(",",$user_string);
        }

        if(empty($dealer_string)){
            $dealer = "";
        }else{
        $dealer = explode(",",$dealer_string);
        }

          if(empty($role_string)){
            $role = "";
        }else{
        $role = explode(",",$role_string);
        }


        if(empty($product_string)){
            $product = "";
        }else{
        $product = explode(",",$product_string);
        }




        // dd($user);
        if(empty($check)){
            $data_return_query = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->join('location_7','location_7.id','=','user_sales_order.location_id')  
                            ->join('location_6','location_6.id','=','location_7.location_6_id')  
                            ->join('location_5','location_5.id','=','location_6.location_5_id')  
                            ->join('location_4','location_4.id','=','location_5.location_4_id')  
                            ->join('location_3','location_3.id','=','location_4.location_3_id')  
                            ->select('user_sales_order_details.order_id','retailer.name as retailer_name','catalog_product.name as product_name','quantity as quantity','rate as rate',DB::raw("round((quantity*rate),2) as amount"))
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('user_sales_order_details.product_id',$product_id)
                            ->where('user_sales_order_details.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_sales_order_details.order_id','user_sales_order_details.product_id');
                            if(!empty($retailer))
                            {
                                $data_return_query->whereIn('user_sales_order.retailer_id',$retailer);
                            } 
                            if(!empty($state))
                            {
                                $data_return_query->whereIn('location_3.id',$state);
                            }
                            if(!empty($location_4))
                            {
                                $data_return_query->whereIn('location_4.id',$location_4);
                            }
                            if(!empty($location_5))
                            {
                                $data_return_query->whereIn('location_5.id',$location_5);
                            }
                            if(!empty($location_6))
                            {
                                $data_return_query->whereIn('location_6.id',$location_6);
                            }
                            if(!empty($location_7))
                            {
                                $data_return_query->whereIn('location_7.id',$location_7);
                            }

                            if(!empty($user))
                            {
                                $data_return_query->whereIn('user_sales_order.user_id',$user);
                            }

                            if(!empty($dealer))
                            {
                                $data_return_query->whereIn('user_sales_order_details.dealer_id',$dealer);
                            }

                             if(!empty($role))
                            {
                                $data_return_query->whereIn('user_sales_order.role_id',$role);
                            }

                            //    if(!empty($product))
                            // {
                            //     $data_return_query->whereIn('user_sales_order_details.product_id',$product);
                            // }
                
            $data_return = $data_return_query->get();

        }else{
            $data_return_query = DB::table('user_sales_order')
                       ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                       ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                       ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                       ->join('location_7','location_7.id','=','user_sales_order.location_id')  
                       ->join('location_6','location_6.id','=','location_7.location_6_id')  
                       ->join('location_5','location_5.id','=','location_6.location_5_id')  
                       ->join('location_4','location_4.id','=','location_5.location_4_id')  
                       ->join('location_3','location_3.id','=','location_4.location_3_id')  
                       ->select('user_sales_order_details.order_id','retailer.name as retailer_name','catalog_product.name as product_name','final_secondary_qty as quantity','final_secondary_rate as rate',DB::raw("round((final_secondary_qty*final_secondary_rate),2) as amount"))
                       ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                       ->where('user_sales_order_details.product_id',$product_id)
                       ->where('user_sales_order_details.company_id',$company_id)
                       ->where('user_sales_order.company_id',$company_id)
                       ->groupBy('user_sales_order_details.order_id','user_sales_order_details.product_id');
                       if(!empty($retailer))
                       {
                           $data_return_query->whereIn('user_sales_order.retailer_id',$retailer);
                       } 
                       if(!empty($state))
                       {
                           $data_return_query->whereIn('location_3.id',$state);
                       }
                       if(!empty($location_4))
                        {
                            $data_return_query->whereIn('location_4.id',$location_4);
                        }
                        if(!empty($location_5))
                        {
                            $data_return_query->whereIn('location_5.id',$location_5);
                        }
                        if(!empty($location_6))
                        {
                            $data_return_query->whereIn('location_6.id',$location_6);
                        }
                        if(!empty($location_7))
                        {
                            $data_return_query->whereIn('location_7.id',$location_7);
                        }

                        if(!empty($user))
                        {
                            $data_return_query->whereIn('user_sales_order.user_id',$user);
                        }

                        if(!empty($dealer))
                        {
                            $data_return_query->whereIn('user_sales_order_details.dealer_id',$dealer);
                        }

                         if(!empty($role))
                        {
                            $data_return_query->whereIn('user_sales_order.role_id',$role);
                        }
            $data_return = $data_return_query->get();
            

        }

        $finalData = array();
        foreach ($data_return as $key => $value) {
            $data['order_id'] = $value->order_id;
            $data['retailer_name'] = $value->retailer_name;
            $data['product_name'] = $value->product_name;
            $data['quantity'] = $value->quantity;
            $data['rate'] = $value->rate;
            $data['amount'] = $value->amount;

            // if(empty($check)){
            //     $details = DB::table('user_sales_order_details')
            //                 ->select('order_id','name','rate','quantity as qty')
            //                 ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
            //                 ->where('order_id',$value->order_id)
            //                 ->where('user_sales_order_details.company_id',$company_id)
            //                 ->get()->toArray();

            // }else{
            //        $details = DB::table('user_sales_order_details')
            //                 ->select('order_id','name','final_secondary_rate as rate','final_secondary_qty as qty')
            //                 ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
            //                 ->where('order_id',$value->order_id)
            //                 ->where('user_sales_order_details.company_id',$company_id)
            //                 ->get()->toArray();
            // }




            // $data['orderDetail'] = $details;

            $finalData[] = $data;
        }


        if($finalData)
        {
            $data['code'] = 200;
            $data['data_return'] = $finalData;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        return json_encode($data);

    }

    public function ExportSkuSales(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $product_id = $request->productid;
        $from_date = $request->fromdate;
        $to_date = $request->todate;
        $retailer_string = $request->retailer_filter;
        $state_string = $request->state_filter;
        $head_quarter_string = $request->head_quarter_filter;
        $town_string = $request->town_filter;
        $beat_string = $request->beat_filter;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($retailer_string)){
            $retailer = "";
        }else{
        $retailer = explode(",",$retailer_string);
        }
        if(empty($state_string)){
            $state = "";
        }else{
        $state = explode(",",$state_string);
        }

        if(empty($head_quarter_string)){
            $head_quarter = "";
        }else{
        $head_quarter = explode(",",$head_quarter_string);
        }

        if(empty($town_string)){
            $town = "";
        }else{
        $town = explode(",",$town_string);
        }

        if(empty($beat_string)){
            $beat = "";
        }else{
        $beat = explode(",",$beat_string);
        }



        $output ='';
        if(empty($check)){
            $data_return_query = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->join('location_7','location_7.id','=','user_sales_order.location_id')  
                            ->join('location_6','location_6.id','=','location_7.location_6_id')  
                            ->join('location_5','location_5.id','=','location_6.location_5_id')  
                            ->join('location_4','location_4.id','=','location_5.location_4_id')  
                            ->join('location_3','location_3.id','=','location_4.location_3_id')  
                            ->select('user_sales_order_details.order_id','retailer.name as retailer_name','catalog_product.name as product_name','quantity as quantity','rate as rate',DB::raw("quantity*rate as amount"))
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('user_sales_order_details.product_id',$product_id)
                            ->where('user_sales_order_details.company_id',$company_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_sales_order_details.order_id','user_sales_order_details.product_id');
                            if(!empty($retailer))
                            {
                                $data_return_query->whereIn('user_sales_order.retailer_id',$retailer);
                            } 
                            if(!empty($state))
                            {
                                $data_return_query->whereIn('location_3.id',$state);
                            }
                            if(!empty($head_quarter))
                            {
                                $data_return_query->whereIn('location_5.id',$head_quarter);
                            }
                            if(!empty($town))
                            {
                                $data_return_query->whereIn('location_6.id',$town);
                            }
                            if(!empty($beat))
                            {
                                $data_return_query->whereIn('location_7.id',$beat);
                            }
                
            $data_return = $data_return_query->get();

        }else{
            $data_return_query = DB::table('user_sales_order')
                       ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                       ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                       ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                       ->join('location_7','location_7.id','=','user_sales_order.location_id')  
                       ->join('location_6','location_6.id','=','location_7.location_6_id')  
                       ->join('location_5','location_5.id','=','location_6.location_5_id')  
                       ->join('location_4','location_4.id','=','location_5.location_4_id')  
                       ->join('location_3','location_3.id','=','location_4.location_3_id')  
                       ->select('user_sales_order_details.order_id','retailer.name as retailer_name','catalog_product.name as product_name','final_secondary_qty as quantity','final_secondary_rate as rate',DB::raw("final_secondary_qty*final_secondary_rate as amount"))
                       ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                       ->where('user_sales_order_details.product_id',$product_id)
                       ->where('user_sales_order_details.company_id',$company_id)
                       ->where('user_sales_order.company_id',$company_id)
                       ->groupBy('user_sales_order_details.order_id','user_sales_order_details.product_id');
                       if(!empty($retailer))
                       {
                           $data_return_query->whereIn('user_sales_order.retailer_id',$retailer);
                       } 
                       if(!empty($state))
                       {
                           $data_return_query->whereIn('location_3.id',$state);
                       }
                       if(!empty($head_quarter))
                       {
                           $data_return_query->whereIn('location_5.id',$head_quarter);
                       }
                       if(!empty($town))
                       {
                           $data_return_query->whereIn('location_6.id',$town);
                       }
                       if(!empty($beat))
                       {
                           $data_return_query->whereIn('location_7.id',$beat);
                       }
            }
            $data_return = $data_return_query->get();

            $output .="S.No,Order No,Customer Name,Name,Qty.,Rate,Amount";
            $output .="\n";
            $i=1;

            foreach ($data_return as $key => $value) 
            {
              
                  
                    $customer_name = !empty($value->retailer_name)?str_replace(",","|",$value->retailer_name):'NA';
                 
                    $product_name = str_replace(",","|",$value->product_name);
                 

                    $output .=$i.',';
                    $output .='#'.$value->order_id.'#'.',';
                    $output .=$customer_name.',';
                    $output .=$product_name.',';
                    $output .=$value->quantity.',';
                    $output .=$value->rate.',';
                    $output .=$value->amount.',';          
                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=ExportSkuSales.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
}


    public function skuSalesPrimaryReport(Request $request)
    {
        if($request->ajax())
        {
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $head_quarter = $request->head_quarter;
            $town = $request->town;
            // $beat = $request->beat;
            $retailer = $request->retailer;
            $product = $request->product;
         
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            if(empty($check)){
            $secondary_man_report_query_data = DB::table('user_primary_sales_order')
                                               ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                               ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id') 
                                               ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')  
                                            //    ->join('location_6','location_6.id','=','dealer.town_id')  
                                            //    ->join('location_5','location_5.id','=','location_6.location_5_id')  
                                            //    ->join('location_4','location_4.id','=','location_5.location_4_id')  
                                            //    ->join('location_3','location_3.id','=','location_4.location_3_id')  
                                               ->select(DB::raw("round(sum(rate*pcs),2) as total_value"),DB::raw("sum(pcs) as total_quantity"),'catalog_product.name as product_name','catalog_product.itemcode','product_id')
                                               ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                                               ->where('user_primary_sales_order.company_id',$company_id)
                                               ->where('catalog_product.company_id',$company_id)
                                               ->groupBy('product_id');

            }
            else{
                $secondary_man_report_query_data = DB::table('user_primary_sales_order')
                                               ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                               ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id') 
                                               ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')  
                                            //    ->leftJoin('location_6','location_6.id','=','dealer.town_id')  
                                            //    ->leftJoin('location_5','location_5.id','=','location_6.location_5_id')  
                                            //    ->leftJoin('location_4','location_4.id','=','location_5.location_4_id')  
                                            //    ->leftJoin('location_3','location_3.id','=','location_4.location_3_id')  
                                               ->select(DB::raw("round(sum(final_secondary_rate*final_secondary_qty),2) as total_value"),DB::raw("sum(final_secondary_qty) as total_quantity"),'catalog_product.name as product_name','catalog_product.itemcode','product_id')
                                               ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                                               ->where('user_primary_sales_order.company_id',$company_id)
                                               ->where('catalog_product.company_id',$company_id)
                                               ->groupBy('product_id');
            }

            // if(!empty($state))
            // {
            //     $secondary_man_report_query_data->whereIn('location_3.id',$state);
            // }
            // if(!empty($head_quarter))
            // {
            //     $secondary_man_report_query_data->whereIn('location_5.id',$head_quarter);
            // }
            // if(!empty($town))
            // {
            //     $secondary_man_report_query_data->whereIn('location_6.id',$town);
            // }
            // if(!empty($beat))
            // {
            //     $secondary_man_report_query_data->whereIn('location_7.id',$beat);
            // }

            if(!empty($retailer))
            {
                $secondary_man_report_query_data->whereIn('user_primary_sales_order.dealer_id',$retailer);
            }

            if(!empty($product))
            {
                $secondary_man_report_query_data->whereIn('user_primary_sales_order_details.product_id',$product);
            }


       
            $secondary_man_report_query = $secondary_man_report_query_data->get();
            // dd($secondary_man_report_query);
            
            $datasend = $request->all();

            // dd($datasend);


            return view('reports.skuSalesPrimary.ajax', [
                    'records' => $secondary_man_report_query,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'retailer' => $retailer,
                    'state' => $state,
                    'head_quarter' => $head_quarter,
                    'town' => $town,
                    'request' => $datasend,
                    // 'beat' => $beat,
                ]);
        }
        else
        {
            echo '<p class="alert-danger">Data not Found</p>';
        } 
    }



    public function skuSalesPrimaryDetails(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $product_id = $request->productid;
        $from_date = $request->fromdate;
        $to_date = $request->todate;
        $retailer_string = $request->retailer_filter;
       
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
      
        // dd($request['request']['retailer']);
        if(empty($check)){
            $data_return_query = DB::table('user_primary_sales_order')
                            ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                            ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                            ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                            ->select('user_primary_sales_order_details.order_id','dealer.name as dealer_name','catalog_product.name as product_name','pcs as quantity','rate as rate',DB::raw("round((pcs*rate),2) as amount"))
                            ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                            ->where('user_primary_sales_order_details.product_id',$product_id)
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->where('user_primary_sales_order.company_id',$company_id);
                            // if(!empty($retailer))
                            // {
                            //     $data_return_query->whereIn('user_primary_sales_order.dealer_id',$retailer);
                            // } 

                              if(!empty($request['request']['retailer']))
                                {
                                    $data_return_query->whereIn('user_primary_sales_order.dealer_id',$request['request']['retailer']);
                                }

                          
                
            $data_return = $data_return_query->groupBy('user_primary_sales_order_details.order_id','user_primary_sales_order_details.product_id')->get();

        }else{
            $data_return_query = DB::table('user_primary_sales_order')
                            ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                            ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                            ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                            ->select('user_primary_sales_order_details.order_id','dealer.name as dealer_name','catalog_product.name as product_name',DB::raw('SUM(final_secondary_qty) as quantity'),'final_secondary_rate as rate',DB::raw("round(SUM(final_secondary_qty*final_secondary_rate),2) as amount"))
                            ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                            ->where('user_primary_sales_order_details.product_id',$product_id)
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->where('user_primary_sales_order.company_id',$company_id);
                  
                        if(!empty($request['request']['retailer']))
                        {
                            $data_return_query->whereIn('user_primary_sales_order.dealer_id',$request['request']['retailer']);
                        }
                
            $data_return = $data_return_query->groupBy('user_primary_sales_order_details.order_id','user_primary_sales_order_details.product_id')->get();
            

        }


        if($data_return)
        {
            $data['code'] = 200;
            $data['data_return'] = $data_return;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        return json_encode($data);

    }


     public function finalStockReport(Request $request)
    {
        if($request->ajax())
        {
            $company_id = Auth::user()->company_id;
            $csa = $request->csa;
            $dealer = $request->dealer;
            $user = $request->user;
            // $explodeDate = explode(" -", $request->date_range_picker);
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

            $dealer_data = DB::table('stock')
                            ->join('dealer','dealer.id','=','stock.dealer_id')
                            ->join('catalog_product','catalog_product.id','=','stock.product_id')
                            ->select('dealer.id as dealer_id','dealer.name as dealer_name','catalog_product.name as product_name','qty as quantity','rate as rate',DB::raw("(rate*qty) as total_stock"))
                            ->where('stock.company_id',$company_id)
                            ->where('dealer.company_id',$company_id)
                            ->where('catalog_product.company_id',$company_id)
                            ->groupBy('dealer_id','product_id');
                              if(!empty($csa))
                            {
                                $dealer_data->whereIn('csa.c_id',$csa);
                            }
                            if(!empty($dealer))
                            {
                                $dealer_data->whereIn('dealer.id',$dealer);
                            }
            $dealer = $dealer_data->get();

            // dd($dealer);
       

          

            
            return view('reports.finalStockReport.ajax', [
                    'records' => $dealer,
                   
                ]);
        }
     
    }

    public function search_url_details(Request $request)
    {
        $url_name = $request->url_name;

        $paernt_module = DB::table('web_module')
                        ->join('modules_bucket','modules_bucket.id','=','web_module.module_id')
                        ->select('modules_bucket.title')
                        ->where('web_module.title','LIKE','%'.$url_name.'%')
                        ->first();
        if(!empty($paernt_module))
        {
            $paernt_sub_module = DB::table('sub_web_module')
                        ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                        ->select('sub_web_module_bucket.title')
                        ->where('sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->first();
            if(!empty($paernt_sub_module))
            {
                $data['code'] = 200;
                $data['data_return'] = $paernt_sub_module->title;
            }
            $paernt_sub_sub_module = DB::table('sub_sub_web_module')
                        ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                        ->select('sub_sub_web_module_bucket.title')
                        ->where('sub_sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->first();
            if(!empty($paernt_sub_sub_module))
            {
                $data['code'] = 200;
                $data['data_return'] = $paernt_sub_sub_module->title;
            }
            if(empty($paernt_sub_module) && empty($paernt_sub_sub_module))
            {
                $data['code'] = 200;
                $data['data_return'] = $paernt_module->title;
            }
            
            
        }
        else
        {
            $paernt_sub_module = DB::table('sub_web_module')
                        ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                        ->select('sub_web_module_bucket.title')
                        ->where('sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->first();
            if(!empty($paernt_sub_module))
            {
                $data['code'] = 200;
                $data['data_return'] = $paernt_sub_module->title;
            }
            $paernt_sub_sub_module = DB::table('sub_sub_web_module')
                        ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                        ->select('sub_sub_web_module_bucket.title')
                        ->where('sub_sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->first();
            // dd($paernt_sub_sub_module);
            if(!empty($paernt_sub_sub_module))
            {
                $data['code'] = 200;
                $data['data_return'] = $paernt_sub_sub_module->title;
            }
            if(empty($paernt_sub_module) && empty($paernt_sub_sub_module))
            {
                $data['code'] = 401;
                $data['data_return'] = array();
            }
            



        }
        return json_encode($data);

    }
    public function autocomplete_search_url(Request $request)
    {
        // $url_name = $request->url_name;
        $url_name = $request->get('term', '');
        $company_id = Auth::user()->company_id;

        $paernt_module = DB::table('web_module')
                        ->join('modules_bucket','modules_bucket.id','=','web_module.module_id')
                        ->select('web_module.title')
                        ->where('web_module.company_id',$company_id)
                        ->where('web_module.title','LIKE','%'.$url_name.'%')
                        ->get();
        if(!empty($paernt_module))
        {
            $data = array();

            $paernt_sub_module = DB::table('sub_web_module')
                        ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                        ->select('sub_web_module.title')
                        ->where('sub_web_module.company_id',$company_id)
                        ->where('sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->get();
            if(!empty($paernt_sub_module))
            {
                foreach ($paernt_sub_module as $product) {
                    $data[] = array('value' => $product->title, 'title' => $product->title);
                }
                // $data['code'] = 200;
                // $data[] = $data;
            }
            $paernt_sub_sub_module = DB::table('sub_sub_web_module')
                        ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                        ->select('sub_sub_web_module.title')
                        ->where('sub_sub_web_module.company_id',$company_id)
                        ->where('sub_sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->get();
            if(!empty($paernt_sub_sub_module))
            {
                foreach ($paernt_sub_sub_module as $product) {
                    $data[] = array('value' => $product->title, 'title' => $product->title);
                }
                // $data['code'] = 200;
                // $data[] = $data;
            }
            if(empty($paernt_sub_module) && empty($paernt_sub_sub_module))
            {
                foreach ($paernt_module as $product) {
                    $data[] = array('value' => $product->title, 'title' => $product->title);
                }
                // $data['code'] = 200;
                // $data[] = $paernt_module->title;
            }
            
            
        }
        else
        {
            $data = array();

            $paernt_sub_module = DB::table('sub_web_module')
                        ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                        ->select('sub_web_module.title')
                        ->where('sub_web_module.company_id',$company_id)
                        ->where('sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->get();
            if(!empty($paernt_sub_module))
            {
                foreach ($paernt_sub_module as $product) {
                    $data[] = array('value' => $product->title, 'title' => $product->title);
                }
                // $data['code'] = 200;
                // $data[] = $data;
            }
            $paernt_sub_sub_module = DB::table('sub_sub_web_module')
                        ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                        ->select('sub_sub_web_module.title')
                        ->where('sub_sub_web_module.company_id',$company_id)
                        ->where('sub_sub_web_module.title','LIKE','%'.$url_name.'%')
                        ->get();
            if(!empty($paernt_sub_sub_module))
            {
                foreach ($paernt_sub_sub_module as $product) {
                    $data[] = array('value' => $product->title, 'title' => $product->title);
                }
                // $data['code'] = 200;
                // $data[] = $data;
            }
            if(empty($paernt_sub_module) && empty($paernt_sub_sub_module))
            {
                // $data['code'] = 401;
                // $data[] = array();
            }
            



        }
        // dd($data);
        // foreach($data as $element) {
        //     $hash = $element['value'];
        //     $unique_array[$hash] = $element;
        // }
        return $data;

    }


     public function salesTeamAttendanceSummaryPatanajaliReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->location_3;
        $location_4 = $request->location_4;
        $location_5 = $request->location_5;
        $location_6 = $request->location_6;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $start = strtotime($from_date);
        $end = strtotime($to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }


        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $this->without_junior == 0)
        {
           $datasenior='';
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
             
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
             if(empty($datasenior)){
                 $datasenior[]=$login_user;
                        }
        }


        $person_data = DB::table('person')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_6','location_6.id','=','person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','person.mobile','location_3.name as state','person.person_id_senior','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.weekly_off_data')
                ->where('person.company_id',$company_id) 
                ->where('_role.company_id',$company_id) 
                ->where('location_3.company_id',$company_id) 
                ->groupBy('person.id');
                  if (!empty($datasenior)) 
                {
                    $person_data->whereIn('person.id', $datasenior);
                }
                if(!empty($state))
                {
                    $person_data->whereIn('location_3.id',$state);
                }
                if(!empty($location_4))
                {
                    $person_data->whereIn('location_4.id',$location_4);
                }
                if(!empty($location_5))
                {
                    $person_data->whereIn('location_5.id',$location_5);
                }
                if(!empty($location_6))
                {
                    $person_data->whereIn('location_6.id',$location_6);
                }
                if(!empty($user))
                {
                    $person_data->whereIn('person.id',$user);
                }
                 if(!empty($role))
                {
                    $person_data->whereIn('person.role_id',$role);
                }
        $person = $person_data->get();   

        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

        $upto_check_in_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id') ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')<='09:30:00'");
        if(!empty($user))
        {
            $upto_check_in_data->whereIn('user_id',$user);
        }
        $upto_check_in = $upto_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

        $after_check_in_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id') ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='09:30:00'");
        if(!empty($user))
        {
            $after_check_in_data->whereIn('user_id',$user);
        }
        $after_check_in = $after_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");


        $total_att_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id') ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ");
        if(!empty($user))
        {
            $total_att_data->whereIn('user_id',$user);
        }
        $total_att = $total_att_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

      

        $working_status = DB::table('user_daily_attendance')
                         ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                         ->where('user_daily_attendance.company_id',$company_id)
                         ->where('_working_status.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                        ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                        ->pluck('name',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));

                        // dd($working_status);

        $att_time = DB::table('user_daily_attendance')
                        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                        ->where('user_daily_attendance.company_id',$company_id)
                        ->where('_working_status.company_id',$company_id)
                       ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                       ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                       ->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i-%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));  


        $checkOutTime = DB::table('check_out')
                        ->where('check_out.company_id',$company_id)
                       ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(check_out.work_date,'%Y-%m-%d')<='$to_date' ")
                       ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                       ->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i-%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));                


        $holiday = DB::table('holiday')->where('company_id',$company_id)->where('status',1)->pluck('name','date')->toArray();         
      
                       

        return view('reports.salesTeamAttendanceSummaryPatanajaliReport.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            'working_status' => $working_status,
            'att_time' => $att_time,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'datearray'=> $datearray,
            'datediff'=> $datediff,
            'checkOutTime'=> $checkOutTime,
            'holiday'=> $holiday,
            'upto_check_in'=> $upto_check_in,
            'after_check_in'=> $after_check_in,
            'total_att'=> $total_att,
            

        ]);



    }



     public function userAttendanceTimePatanajaliReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->location_3;
        $location_4 = $request->location_4;
        $location_5 = $request->location_5;
        $location_6 = $request->location_6;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $start = strtotime($from_date);
        $end = strtotime($to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }


        $person_data = DB::table('person')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_6','location_6.id','=','person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','person.mobile','location_3.name as state','person.person_id_senior','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.weekly_off_data')
                ->where('person.company_id',$company_id) 
                ->where('_role.company_id',$company_id) 
                ->where('location_3.company_id',$company_id) 
                ->groupBy('person.id');
                if(!empty($state))
                {
                    $person_data->whereIn('location_3.id',$state);
                }
                if(!empty($location_4))
                {
                    $person_data->whereIn('location_4.id',$location_4);
                }
                if(!empty($location_5))
                {
                    $person_data->whereIn('location_5.id',$location_5);
                }
                if(!empty($location_6))
                {
                    $person_data->whereIn('location_6.id',$location_6);
                }
                if(!empty($user))
                {
                    $person_data->whereIn('person.id',$user);
                }
                 if(!empty($role))
                {
                    $person_data->whereIn('person.role_id',$role);
                }
        $person = $person_data->get();   

        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

        $upto_check_in_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id') ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')<='09:30:00'");
        if(!empty($user))
        {
            $upto_check_in_data->whereIn('user_id',$user);
        }
        $upto_check_in = $upto_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

        $after_check_in_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id') ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='09:30:00'");
        if(!empty($user))
        {
            $after_check_in_data->whereIn('user_id',$user);
        }
        $after_check_in = $after_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");


        $total_att_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id') ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ");
        if(!empty($user))
        {
            $total_att_data->whereIn('user_id',$user);
        }
        $total_att = $total_att_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

      

        $working_status = DB::table('user_daily_attendance')
                         ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                         ->where('user_daily_attendance.company_id',$company_id)
                         ->where('_working_status.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                        ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                        ->pluck('name',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));

                        // dd($working_status);

        $att_time = DB::table('user_daily_attendance')
                        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                        ->where('user_daily_attendance.company_id',$company_id)
                        ->where('_working_status.company_id',$company_id)
                       ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date' ")
                       ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                       ->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));  


        $checkOutTime = DB::table('check_out')
                        ->where('check_out.company_id',$company_id)
                       ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(check_out.work_date,'%Y-%m-%d')<='$to_date' ")
                       ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                       ->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as data"));                


        $holiday = DB::table('holiday')->where('company_id',$company_id)->where('status',1)->pluck('name','date')->toArray();         
      
                       

        return view('reports.userAttendanceTimePatanajaliReport.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            'working_status' => $working_status,
            'att_time' => $att_time,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'datearray'=> $datearray,
            'datediff'=> $datediff,
            'checkOutTime'=> $checkOutTime,
            'holiday'=> $holiday,
            'upto_check_in'=> $upto_check_in,
            'after_check_in'=> $after_check_in,
            'total_att'=> $total_att,
            

        ]);



    }


    public function dailyAttendanceEditReport(Request $request)
    {
        if ($request->ajax() ) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $date = $request->date;
         


            
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

            $attendence = DB::table('user_daily_attendance')
                        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                        ->where('user_daily_attendance.company_id',$company_id)
                        ->where('_working_status.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$date'")
                        ->select('user_daily_attendance.user_id','_working_status.name as work_status',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as checkin_time"),'user_daily_attendance.track_addrs as checkin_location','user_daily_attendance.remarks')
                        ->groupBy('user_id')
                        ->get();

            $attendence_array = array();
            foreach ($attendence as $akey => $avalue) {
                $attendence_user_id = $avalue->user_id;

                $attendence_array[$attendence_user_id]['user_id'] = $avalue->user_id;
                $attendence_array[$attendence_user_id]['work_status'] = $avalue->work_status;
                $attendence_array[$attendence_user_id]['checkin_time'] = $avalue->checkin_time;
                $attendence_array[$attendence_user_id]['checkin_location'] = $avalue->checkin_location;
                $attendence_array[$attendence_user_id]['checkin_remarks'] = $avalue->remarks;

            }

            $checkout = DB::table('check_out')
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')='$date'")
                        ->select('user_id',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as checkout_time"),'attn_address as checkout_location','remarks as checkout_remarks')
                        ->groupBy('user_id')
                        ->get();

            $checkout_array = array();
            foreach ($checkout as $ckey => $cvalue) {
                    $checkout_user_id = $cvalue->user_id;

                    $checkout_array[$checkout_user_id]['user_id'] = $cvalue->user_id;
                    $checkout_array[$checkout_user_id]['checkout_time'] = $cvalue->checkout_time;
                    $checkout_array[$checkout_user_id]['checkout_location'] = $cvalue->checkout_location;
                    $checkout_array[$checkout_user_id]['checkout_remarks'] = $cvalue->checkout_remarks;

                    }            




            $data1 = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->Join('_role','_role.role_id','=','person.role_id')
                    ->Join('location_view','location_view.l3_id','=','person.state_id')
                    ->where('person_status','=','1')
                    ->where('person.company_id',$company_id)
                    ->where('person_login.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'person_id_senior','person.mobile','rolename AS role_name','l3_name','l4_name','l5_name','l6_name','l1_name','l2_name','emp_code');
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('l6_id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }

            $user_records = $data1->groupBy('person.id')->get();


            $person_name = DB::table('person')
                            ->where('company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'person.id')->toArray();

            $work_status_drop = DB::table('_working_status')->where('company_id',$company_id)->pluck('name','id');
          
            $user_record=[];
            foreach ($user_records as $key => $value) {
                $user_id=$value->user_id;
                $in=$user_id;
              
                $user_record[$in]['user_name']=$value->user_name;
                $user_record[$in]['user_id']=$value->user_id;
                $user_record[$in]['l1_name']=$value->l1_name;
                $user_record[$in]['l2_name']=$value->l2_name;
                $user_record[$in]['l3_name']=$value->l3_name;
                $user_record[$in]['l4_name']=$value->l4_name;
                $user_record[$in]['l5_name']=$value->l5_name;
                $user_record[$in]['l6_name']=$value->l6_name;
                $user_record[$in]['emp_code']=$value->emp_code;
                $user_record[$in]['person_id_senior']=$value->person_id_senior;
                $user_record[$in]['senior_name']=!empty($person_name[$value->person_id_senior])?$person_name[$value->person_id_senior]:'';
                $user_record[$in]['mobile']=$value->mobile;
                $user_record[$in]['role_name']=$value->role_name;

                $user_record[$in]['work_status']=!empty($attendence_array[$value->user_id]['work_status'])?$attendence_array[$value->user_id]['work_status']:'';
                $user_record[$in]['checkin_time']=!empty($attendence_array[$value->user_id]['checkin_time'])?$attendence_array[$value->user_id]['checkin_time']:'';
                $user_record[$in]['checkin_location']=!empty($attendence_array[$value->user_id]['checkin_location'])?$attendence_array[$value->user_id]['checkin_location']:'';
                $user_record[$in]['checkin_remarks']=!empty($attendence_array[$value->user_id]['checkin_remarks'])?$attendence_array[$value->user_id]['checkin_remarks']:'';

                $user_record[$in]['checkout_time']=!empty($checkout_array[$value->user_id]['checkout_time'])?$checkout_array[$value->user_id]['checkout_time']:'';
                $user_record[$in]['checkout_location']=!empty($checkout_array[$value->user_id]['checkout_location'])?$checkout_array[$value->user_id]['checkout_location']:'';
                $user_record[$in]['checkout_remarks']=!empty($checkout_array[$value->user_id]['checkout_remarks'])?$checkout_array[$value->user_id]['checkout_remarks']:'';

               

                // $user_record[$in]['remarks']=
            }

            // dd($user_record);
            return view('reports.dailyAttendanceEditReport.ajax', [
                'records' => $user_record,
                'date' => $date,
                'work_status_drop' => $work_status_drop,
            ]);

        }
    }


       public function attendanceDetails(Request $request)
    {

        $company_id = Auth::user()->company_id;
        $date = $request->date;
        $user_id = $request->user_id;
        $flag = $request->flag;

        $user_name = DB::table('person')->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"))->where('company_id',$company_id)->where('person.id',$user_id)->first();

         $attendance = DB::table('user_daily_attendance')
                            ->select('user_daily_attendance.user_id','user_daily_attendance.work_status as work_status',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as checkin_time"),'user_daily_attendance.track_addrs as checkin_location','user_daily_attendance.remarks')
                            ->where('company_id',$company_id)
                            ->where('user_id',$user_id)
                            ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$date'")
                            ->first();

        $checkout = DB::table('check_out')
                            ->select('user_id',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as checkout_time"),'attn_address as checkout_location','remarks as checkout_remarks')
                            ->where('company_id',$company_id)
                            ->where('user_id',$user_id)
                            ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')='$date'")
                            ->first();

        $final_array = array();
        if($flag == "AttendenceCheckoutUpdate"){
            $final_array[0]['user_id'] = $user_id;
            $final_array[0]['user_name'] = $user_name->user_name;
            $final_array[0]['att_work_status'] = $attendance->work_status;
            $final_array[0]['checkin_time'] = $attendance->checkin_time;
            $final_array[0]['checkin_location'] = $attendance->checkin_location;
            $final_array[0]['checkin_remarks'] = $attendance->remarks;
            $final_array[0]['checkout_time'] = $checkout->checkout_time;
            $final_array[0]['checkout_location'] = $checkout->checkout_location;
            $final_array[0]['checkout_remarks'] = $checkout->checkout_remarks;
            $final_array[0]['flag'] = $flag;
            $final_array[0]['date'] = $date;
        }elseif($flag == "AttendenceCheckoutInsert"){
            $final_array[0]['user_id'] = $user_id;
            $final_array[0]['user_name'] = $user_name->user_name;
            $final_array[0]['att_work_status'] = '';
            $final_array[0]['checkin_time'] = '';
            $final_array[0]['checkin_location'] = '';
            $final_array[0]['checkin_remarks'] = '';
            $final_array[0]['checkout_time'] = '';
            $final_array[0]['checkout_location'] = '';
            $final_array[0]['checkout_remarks'] = '';
            $final_array[0]['flag'] = $flag;
            $final_array[0]['date'] = $date;
        }elseif($flag == "AttendenceUpdateCheckoutInsert"){
            $final_array[0]['user_id'] = $user_id;
            $final_array[0]['user_name'] = $user_name->user_name;
            $final_array[0]['att_work_status'] = $attendance->work_status;
            $final_array[0]['checkin_time'] = $attendance->checkin_time;
            $final_array[0]['checkin_location'] = $attendance->checkin_location;
            $final_array[0]['checkin_remarks'] = $attendance->remarks;
            $final_array[0]['checkout_time'] = '';
            $final_array[0]['checkout_location'] = '';
            $final_array[0]['checkout_remarks'] = '';
            $final_array[0]['flag'] = $flag;
            $final_array[0]['date'] = $date;
        }elseif($flag == "AttendenceInsertCheckoutUpdate"){
            $final_array[0]['user_id'] = $user_id;
            $final_array[0]['user_name'] = $user_name->user_name;
            $final_array[0]['att_work_status'] = '';
            $final_array[0]['checkin_time'] = '';
            $final_array[0]['checkin_location'] = '';
            $final_array[0]['checkin_remarks'] = '';
            $final_array[0]['checkout_time'] = $checkout->checkout_time;
            $final_array[0]['checkout_location'] = $checkout->checkout_location;
            $final_array[0]['checkout_remarks'] = $checkout->checkout_remarks;
            $final_array[0]['flag'] = $flag;
            $final_array[0]['date'] = $date;
        }

        // dd($final_array);
   

        if($final_array)
        {
            $data['code'] = 200;
            $data['final_array'] = $final_array;

        }
        else
        {
            $data['code'] = 401;
            $data['final_array'] = array();


        }
        return json_encode($data);

    }

     public function attendanceDetailsUpdate(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $date = $request->return_date;
        $user_id = $request->user_id;
        $flag = $request->return_flag;
        $checkin_time = $request->checkin_time;
        $checkin_location = $request->checkin_location;
        $checkin_remarks = $request->checkin_remarks;
        $checkout_time = $request->checkout_time;
        $checkout_location = $request->checkout_location;
        $checkout_remarks = $request->checkout_remarks;
        $work_status = $request->work_status;

        $final_check_in_date = $date.' '.$checkin_time;
        $final_check_out_date = $date.' '.$checkout_time;

        $curr_date = "'".date('Y-m-d H:i:s')."'";

        $orderid = date('YmdHis');
        // dd($final_check_out_date);
        // $attendanceArray = array();
        // $checkOutArray = array();



        if($flag == "AttendenceCheckoutUpdate"){
            $attendanceArray = [
                'work_date' => $final_check_in_date,
                'track_addrs' => $checkin_location,
                'work_status' => $work_status,
                'remarks' => $checkin_remarks,
                'server_date' => $curr_date,
                'att_status' => '2',
            ];

            $checkOutArray = [
                'work_date' => $final_check_out_date,
                'attn_address' => $checkout_location,
                'remarks' => $checkout_remarks,
                'company_id' => $company_id,
                'server_date_time' => $curr_date,
                'check_out_status' => '2',
            ];
                if(!empty($checkin_time) && !empty($checkout_time)){
                $attendance_updation = DB::table('user_daily_attendance')
                                        ->where('company_id',$company_id)
                                        ->where('user_id',$user_id)
                                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$date'")
                                        ->update($attendanceArray);

                $checkout_updation = DB::table('check_out')
                                        ->where('company_id',$company_id)
                                        ->where('user_id',$user_id)
                                        ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')='$date'")
                                        ->update($checkOutArray);
                }
        }

        elseif($flag == "AttendenceCheckoutInsert"){

            $attendanceArray = [
                'work_date' => $final_check_in_date,
                'track_addrs' => $checkin_location,
                'remarks' => $checkin_remarks,
                'work_status' => $work_status,
                'order_id' => $orderid,
                'company_id' => $company_id,
                'server_date' => $curr_date,
                'user_id' => $user_id,
                'mnc_mcc_lat_cellid' => '0.0',
                'lat_lng' => '0.0',
                'att_status' => '2',
            ];

            $checkOutArray = [
                'work_date' => $final_check_out_date,
                'user_id' => $user_id,
                'attn_address' => $checkout_location,
                'remarks' => $checkout_remarks,
                'company_id' => $company_id,
                'server_date_time' => $curr_date,
                'mnc_mcc_lat_cellid' => '0.0',
                'lat_lng' => '0.0',
                'image_name' => '.jpg',
                'order_id' => $orderid,
                'check_out_status' => '2',
            ];

            if(!empty($checkin_time)){
            $attendance_updation = DB::table('user_daily_attendance')->insert($attendanceArray);
            }
            if(!empty($checkout_time)){
            $checkout_updation = DB::table('check_out')->insert($checkOutArray);
            }
        }

        elseif($flag == "AttendenceUpdateCheckoutInsert"){

            $attendanceArray = [
                'work_date' => $final_check_in_date,
                'track_addrs' => $checkin_location,
                'remarks' => $checkin_remarks,
                'work_status' => $work_status,
                'att_status' => '2',
            ];

            $checkOutArray = [
                'work_date' => $final_check_out_date,
                'user_id' => $user_id,
                'attn_address' => $checkout_location,
                'remarks' => $checkout_remarks,
                'company_id' => $company_id,
                'server_date_time' => $curr_date,
                'mnc_mcc_lat_cellid' => '0.0',
                'lat_lng' => '0.0',
                'image_name' => '.jpg',
                'order_id' => $orderid,
                'check_out_status' => '2',
            ];
            if(!empty($checkin_time)){
            $attendance_updation = DB::table('user_daily_attendance')
                                    ->where('company_id',$company_id)
                                    ->where('user_id',$user_id)
                                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$date'")
                                    ->update($attendanceArray);
            }
            if(!empty($checkout_time)){
            $checkout_updation = DB::table('check_out')->insert($checkOutArray);
            }
        }

        elseif($flag == "AttendenceInsertCheckoutUpdate"){

            $attendanceArray = [
                'work_date' => $final_check_in_date,
                'track_addrs' => $checkin_location,
                'remarks' => $checkin_remarks,
                'work_status' => $work_status,
                'order_id' => $orderid,
                'mnc_mcc_lat_cellid' => '0.0',
                'lat_lng' => '0.0',
                'att_status' => '2',
            ];

            $checkOutArray = [
                'work_date' => $final_check_out_date,
                'attn_address' => $checkout_location,
                'remarks' => $checkout_remarks,
                'company_id' => $company_id,
                'server_date_time' => $curr_date,
                'check_out_status' => '2',
            ];
            if(!empty($checkin_time)){
            $attendance_updation = DB::table('user_daily_attendance')->insert($attendanceArray);
            }
            if(!empty($checkout_time)){
            $checkout_updation = DB::table('check_out')
                                    ->where('company_id',$company_id)
                                    ->where('user_id',$user_id)
                                    ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')='$date'")
                                    ->update($checkOutArray);
            }
        }


         $data['code'] = 200;
         $data['final_array'] = 'DONE';
        return json_encode($data);




        //  if($attendance_updation && $checkout_updation)
        // {
        //     $data['code'] = 200;
        //     $data['final_array'] = 'DONE';

        // }
        // else
        // {
        //     $data['code'] = 401;
        //     $data['final_array'] = 'FAULT';


        // }
        // return json_encode($data);


    }

    public function user_dealer_beat_retailer_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_data_data = User::join('person','person.id','=','users.id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                    ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                    ->join('location_7','location_7.id','=','location_view.l7_id')
                    ->select('person.*','dealer.name as dealer_name','person.id as user_id','dealer.id as dealer_id','dealer.other_numbers as dealer_no','location_view.*','_role.rolename as rolename')
                    ->where('person.company_id',$company_id)
                    ->where('is_admin','!=',1)
                    ->where('person_status',1)
                    ->where('dealer.dealer_status',1)
                    ->where('location_7.status',1)
                    ->groupBy('person.id','location_id','dealer_id');
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $user_data_data->whereIn('l3_id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $user_data_data->whereIn('l4_id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $user_data_data->whereIn('l5_id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $user_data_data->whereIn('l6_id', $location_6);
                    }
                    if (!empty($request->location_7)) 
                    {
                        $location_7 = $request->location_7;
                        $user_data_data->whereIn('l7_id', $location_7);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $user_data_data->whereIn('dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $user_data_data->whereIn('person.id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $user_data_data->whereIn('person.role_id', $role);
                    }
        $user_data = $user_data_data->get();

        // $beat_data = DB::table('location_7')
        //             ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
        //             ->where('location_7.company_id',$company_id)
        //             ->where('location_7.status',1) 
        //             ->pluck('location_7.name','user_id');


        $retailer_count = DB::table('retailer')
                        ->where('company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('location_id')
                        ->pluck(DB::raw("COUNT(retailer.id) as count"),'location_id');


        return view('reports.userDealerBeatDetails.ajax', [
                'records' => $user_data,
                'retailer_count' => $retailer_count,
                // 'work_status_drop' => $work_status_drop,
            ]);
    }
    public function get_user_assign_retailer(Request $request)
    {
        $dealer_id = $request->dealer_id;
        $user_id = $request->user_id;
        $company_id = Auth::user()->company_id;
        $l7_id = $request->l7_id;
        $retailer_count = DB::table('retailer')
                        ->where('dealer_id',$dealer_id)
                        ->where('location_id',$l7_id)
                        ->where('company_id',$company_id)
                        ->where('retailer_status',1)
                        ->select('retailer.name as retailer_name','retailer.landline as retailer_number')
                        ->groupBy('retailer.id')
                        ->get();

        if(!empty($retailer_count))
        {
            $data['code'] = 200;
            $data['result'] = $retailer_count;
            $data['final_array'] = 'DONE';
            
        }
        else
        {
            $data['code'] = 200;
            $data['result'] = array();
            $data['final_array'] = 'not found';
            // return json_encode($data);
        }
        return json_encode($data);
    }


     public function customerOrderReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $user_data_data = DB::table('customer_order_form_aeris')
                    ->join('catalog_product','catalog_product.id','=','customer_order_form_aeris.product_id')
                    ->join('person','person.id','=','customer_order_form_aeris.created_by')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('location_6','location_6.id','=','person.town_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('location_3','location_3.id','=','location_4.location_3_id')
                    ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("DATE_FORMAT(customer_order_form_aeris.created_at,'%Y-%m-%d') as orderDate"),'customer_name','customer_contact_no','customer_email_id','customer_add','catalog_product.name as product','number_of_unit','track_addr','person.id as user_id')
                    ->where('person_status',1)
                    ->whereRaw("DATE_FORMAT(customer_order_form_aeris.created_at,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(customer_order_form_aeris.created_at,'%Y-%m-%d')<='$to_date' ")
                    ->groupBy('customer_order_form_aeris.id');
                       if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $user_data_data->whereIn('l3_id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $user_data_data->whereIn('l4_id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $user_data_data->whereIn('l5_id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $user_data_data->whereIn('l6_id', $location_6);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $user_data_data->whereIn('person.id', $user);
                    }

        $user_data = $user_data_data->get();

        // dd($user_data);

        return view('reports.customerOrderReport.ajax', [
                'records' => $user_data,
            ]);
    }

    


      public function userSalesSummaryRajdhaniReport(Request $request)
    {
        if ($request->ajax() ) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $array = array(99,100,101,102); // for oyster


            
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }



            $dailyAttendenceData = DB::table('user_daily_attendance')
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
                                ->whereNotIn('work_status',[234,232,227,236]) // leave,holiday,absent,w/o
                                ->where('company_id',$company_id)
                                ->groupBy('user_id')
                                ->pluck(DB::raw("COUNT(order_id) as order_id"),'user_id');




            $data1 = DB::table('person')
                    // ->join('person','person.id','=','user_sales_order.user_id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('location_3','location_3.id','=','person.state_id')
                    ->join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('location_1','location_1.id','=','location_2.location_1_id')

                    ->join('location_6','location_6.id','=','person.town_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'person_id_senior','person.mobile','emp_code','rolename AS role_name','person.id AS user_id','location_1.name as l1_name','location_2.name as l2_name','location_3.name as l3_name')
                    // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")  
                    // ->where('user_sales_order.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('person_login.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('location_1.company_id',$company_id)
                    ->where('location_2.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('location_4.company_id',$company_id)
                    ->where('location_5.company_id',$company_id)
                    ->where('location_6.company_id',$company_id);
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_2.id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_3.id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_records = $data1->groupBy('person.id')->get();

        
            $person_name = DB::table('person')
                        ->where('company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as working_with_name"),'id');


            $tcu = DB::table('user_sales_order')
                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")  
                ->where('company_id',$company_id)
                ->groupBY('user_id')
                ->pluck(DB::raw("count(call_status) AS tc"),'user_id');  


            $pcu = DB::table('user_sales_order')
                ->where('call_status','1')
                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")  
                ->where('company_id',$company_id)
                ->groupBY('user_id')
                ->pluck(DB::raw("count(call_status) AS tc"),'user_id');  



            $total_sale_weight = DB::table('user_sales_order')   
                             ->join('user_sales_order_details','user_sales_order_details.order_id','user_sales_order.order_id')
                             ->join('catalog_product','catalog_product.id','user_sales_order_details.product_id')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")  
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('user_sales_order_details.company_id',$company_id)
                            ->where('catalog_product.company_id',$company_id)
                            ->groupBY('user_id')
                            ->pluck(DB::raw("SUM(user_sales_order_details.quantity*catalog_product.weight) AS weight"),'user_id');   



            $total_sale_weight_primary = DB::table('user_primary_sales_order')   
                             ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','user_primary_sales_order.order_id')
                             ->join('catalog_product','catalog_product.id','user_primary_sales_order_details.product_id')
                            ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")  
                            ->groupBY('created_person_id')
                            ->pluck(DB::raw("sum(((user_primary_sales_order_details.cases*quantity_per_case)+user_primary_sales_order_details.pcs)*catalog_product.weight) AS weight"),'created_person_id');




                      

            $user_record=[];
            foreach ($user_records as $key => $value) {
                $user_id=$value->user_id;
           
                $user_record[$user_id]['user_name']=$value->user_name;
                $user_record[$user_id]['user_id']=$value->user_id;
                $user_record[$user_id]['l1_name']=$value->l1_name;
                $user_record[$user_id]['l2_name']=$value->l2_name;
                $user_record[$user_id]['l3_name']=$value->l3_name;
             
                $user_record[$user_id]['emp_code']=$value->emp_code;
                $user_record[$user_id]['person_id_senior']=$value->person_id_senior;
                $user_record[$user_id]['mobile']=$value->mobile;
                $user_record[$user_id]['role_name']=$value->role_name;
                $user_record[$user_id]['senior_name']=!empty($person_name[$value->person_id_senior])?$person_name[$value->person_id_senior]:'';
                $user_record[$user_id]['tc']=!empty($tcu[$user_id])?$tcu[$user_id]:'0';
                $user_record[$user_id]['pc']=!empty($pcu[$user_id])?$pcu[$user_id]:'0';

                $user_record[$user_id]['secondary_weight']=!empty($total_sale_weight[$user_id])?$total_sale_weight[$user_id]:'0';
                $user_record[$user_id]['primary_weight']=!empty($total_sale_weight_primary[$user_id])?$total_sale_weight_primary[$user_id]:'0';

                $user_record[$user_id]['working_day']=!empty($dailyAttendenceData[$user_id])?$dailyAttendenceData[$user_id]:'0';



               
            }

            $price = array_column($user_record, 'tc');

            array_multisort($price, SORT_DESC, $user_record);

            // dd($price);
            return view('reports.userSalesSummaryRajdhani.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }




    public function dsrMonthlyForNehaReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $catalog_product_data = DB::table('catalog_product')
                            ->where('status',1)
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->orderBy('id','asc');
                            if(!empty($request->product))
                            {
                                $catalog_product_data->whereIn('id',$request->product);
                            }
                              if(!empty($request->catalog_2))
                            {
                                $catalog_product_data->whereIn('catalog_id',$request->catalog_2);
                            }
        $catalog_product = $catalog_product_data->get();
        
        $person_details = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('location_2','location_2.id','=','location_3.location_2_id')
                        ->join('location_1','location_1.id','=','location_2.location_1_id')

                        ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('rolename','person.mobile as mobile','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person.id as user_id','person.state_id as state_id','person.emp_code','person.person_id_senior')
                        ->where('person.company_id',$company_id)
                        ->where('person_login.person_status','=','1')
                        ->groupBy('user_id')->orderBy('user_id','ASC');

        if (!empty($datasenior)) 
        {
            $person_details->whereIn('person.id', $datasenior);
        }
        if(!empty($state))
        {
            $person_details->where('person.state_id',$state);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_details->whereIn('l3_id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $person_details->whereIn('l4_id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_details->whereIn('l5_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_details->whereIn('l6_id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $person_details->whereIn('dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $person_details->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_details->whereIn('person.role_id', $role);
        }

        $person = $person_details->get();
        // dd($person);


        $totalCounter = DB::table('person')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                        ->join('retailer','retailer.location_id','=','location_7.id')
                        ->where('retailer.retailer_status','=','1')
                        ->where('dealer.dealer_status','=','1')
                        ->where('dealer_location_rate_list.user_id','!=','0')
                        ->where('location_7.status','=','1')
                        ->where('person.company_id',$company_id)
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(retailer.id) as count"),"person.id");


        $uniqueTotalCall = DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->where('company_id',$company_id)
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(DISTINCT retailer_id,date) as tc"),"user_id");



        $uniqueTotalCall = DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->where('company_id',$company_id)
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(DISTINCT retailer_id,date) as tc"),"user_id");


        $uniqueProductiveCall = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(DISTINCT retailer_id,date) as tc"),"user_id");

        $uniqueProductiveCall = array();


        $ProductType = DB::table('product_type')->where('status','1')->where('company_id',$company_id)->pluck('name','id')->toArray(); 

        $finalProductTypeOut = array();

        foreach ($ProductType as $key => $value) {
               $finalProductTypeOut[$key] = $value;
           }   
           $finalProductTypeOut['0'] = "Pieces";



           $finalCatalogProduct = DB::table('product_type')
                                ->where('product_type.company_id',$company_id)
                                ->groupBy('product_type.id')
                                ->pluck('flag_neha','product_type.id')->toArray();





        $dsr =  DB::table('user_sales_order')
                   ->select(DB::raw("SUM(user_sales_order_details.quantity) as quantity"),DB::raw("CONCAT(user_id,product_id,final_product_type) as concat"),"final_product_type",DB::raw("COUNT(DISTINCT retailer_id,date) as uniqueProductiveCall"),DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as sale_value"),"catalog_product.id as productId","catalog_product.quantity_per_case as quantity_per_case","catalog_product.quantiy_per_other_type as quantiy_per_other_type","catalog_product.final_product_type","user_sales_order.user_id")
                   ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                   ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                   ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                   ->groupBy('product_id','user_id')
                   ->where('user_sales_order.company_id',$company_id)
                   ->where('user_sales_order_details.company_id',$company_id)
                   ->where('catalog_product.company_id',$company_id)
                   ->get();


        $finalOutDsr = array();
        $finalSaleOutDsr = array();

        foreach ($dsr as $dsrkey => $dsrvalue) {

            $finalConcat = $dsrvalue->concat;
            $productId = $dsrvalue->productId;

            $userProductConcat = $dsrvalue->user_id.$dsrvalue->productId;

            $flagNeha = !empty($finalCatalogProduct[$dsrvalue->final_product_type])?$finalCatalogProduct[$dsrvalue->final_product_type]:'0';


            if($flagNeha == '0'){
                 $finalOutDsr[$finalConcat]['quantity'] =  $dsrvalue->quantity;
            }else{


                if($flagNeha == '1'){
                    $finalOutDsr[$finalConcat]['quantity'] =  ($dsrvalue->quantity/$dsrvalue->quantity_per_case);
                }elseif($flagNeha == '2'){
                    $finalOutDsr[$finalConcat]['quantity'] =  ($dsrvalue->quantity/$dsrvalue->quantiy_per_other_type);
                }else{
                    $finalOutDsr[$finalConcat]['quantity'] =  '0';
                }
            }

            $finalOutDsr[$finalConcat]['concat'] =  $finalConcat;
            $finalOutDsr[$finalConcat]['flagNeha'] =  $flagNeha;
            $finalOutDsr[$finalConcat]['final_product_type'] =  $dsrvalue->final_product_type;


            $finalSaleOutDsr[$userProductConcat]['uniqueProductiveCall'] =  $dsrvalue->uniqueProductiveCall;
            $finalSaleOutDsr[$userProductConcat]['sale_value'] =  $dsrvalue->sale_value;




        }


        // $finalSaleOutDsr = array();
        // $finalOutDsr = array();


                   // dd($finalSaleOutDsr);



            return view('reports.dsrMonthlyForNehaReport.ajax', [
            'person' => $person,
            'catalog_product' => $catalog_product,
            'company_id' => $company_id,
            'totalCounter' => $totalCounter,
            'uniqueTotalCall' => $uniqueTotalCall,
            'uniqueProductiveCall' => $uniqueProductiveCall,
            'finalProductTypeOut' => $finalProductTypeOut,
            'finalOutDsr' => $finalOutDsr,
            'finalSaleOutDsr' => $finalSaleOutDsr,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'company_id' => $company_id,

        ]);

    }



     public function get_outlet_details(Request $request)
    {
        $user_id = $request->user_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
    
        // dd($request);


         $uniqueTotalCall = DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->where('company_id',$company_id)
                        ->where('user_id',$user_id)
                        ->groupBy('retailer_id','date')
                        ->pluck('retailer_id');
                        // ->pluck(DB::raw("COUNT(DISTINCT retailer_id,date) as tc"),"user_id");

                        // dd($uniqueTotalCall);



        


        $queryDetails = DB::table('person')
                         ->select('retailer.name as retailer_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'dealer.name as dealer_name','location_7.name as beat',DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d') created_on"),'retailer.id as retailer_id','dealer.id as dealer_id','person.id as user_id','retailer.landline','location_3.name as state','location_6.name as town')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('location_3','location_3.id','=','location_4.location_3_id')
                        ->join('retailer','retailer.location_id','=','location_7.id')
                        ->where('retailer.retailer_status','=','1')
                        ->where('dealer.dealer_status','=','1')
                        ->where('dealer_location_rate_list.user_id','!=','0')
                        ->where('location_7.status','=','1')
                        ->where('person.company_id',$company_id)
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->where('person.id',$user_id);
                        if(!empty($uniqueTotalCall)){
                        $queryDetails->whereNotIn('retailer.id',$uniqueTotalCall);
                        }

        $query =   $queryDetails->get();

                        // dd($query);

       


        $f_out = array();
        foreach ($query as $key => $value) 
        {
            $out['retailer_name'] = $value->retailer_name;
            $out['user_name'] = $value->user_name;
            $out['dealer_name'] = $value->dealer_name;
            $out['beat'] = $value->beat;
            $out['created_on'] = $value->created_on;
            $out['user_n'] = Crypt::encryptString($value->user_id);
            $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
            $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
            $out['landline'] = $value->landline;
            $out['state'] = $value->state;
            $out['town'] = $value->town;

           
            $f_out[] = $out;
        }

                // dd($query);
      
        if(!empty($query))
        {
            $data['code'] = 200;
            $data['result_data'] = $f_out;
            
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
          
            $data['result'] = '';
        }
        return json_encode($data);
        
    }




    public function unbilledOutletReport(Request $request)
    {

        if ($request->ajax() ) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $array = array(99,100,101,102); // for oyster


            
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }



            $q=Retailer::where('retailer.retailer_status','=','1')
                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                ->join('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')
                ->leftJoin('person','person.id','=','retailer.created_by_person_id')
                ->join('dealer','retailer.dealer_id','=','dealer.id');


            if (!empty($datasenior)) 
            {
                $q->whereIn('person.id', $datasenior);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $q->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $q->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $q->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $q->whereIn('l6_id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $q->whereIn('person.role_id', $role);
            }
            
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $q->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $q->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $q->whereIn('user_id', $user);
            }


     

        $data = $q->select('l3_name','l4_name','l5_name','l6_name','l7_name as beat_name','person.id as user_id','dealer_id','dealer.name as dealer_name','person.emp_code',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.mobile','retailer.name as retailer_name','retailer.landline','retailer.id as retailer_id')
            ->where('l7_company_id',$company_id)
            ->where('retailer.company_id',$company_id)
            ->where('_retailer_outlet_type.company_id',$company_id)
            ->groupBy('retailer.id')
            ->orderByRaw('TRIM(retailer.name) ASC')
            ->get();



        $productiveRetailerSales = DB::table('user_sales_order')
                                ->where('company_id',$company_id)
                                ->where('call_status','=','1')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                                ->pluck('retailer_id','retailer_id');



        $NonProductiveRetailerSales = DB::table('user_sales_order')
                                ->where('company_id',$company_id)
                                ->where('call_status','=','0')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                                ->pluck('retailer_id','retailer_id');



        $tsiUserforNeha = DB::table('retailer')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->where('person_login.person_status','=','1')
                        ->where('person.role_id','=','180')
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        ->groupBy('retailer.id')
                        ->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');


        $tsiUserIdforNeha = DB::table('retailer')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->where('person_login.person_status','=','1')
                        ->where('person.role_id','=','180')
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        ->groupBy('retailer.id')
                        ->pluck(DB::raw("GROUP_CONCAT(DISTINCT person.person_id_senior) as user_id"),'retailer.id');


        $tsiUserMobileforNeha = DB::table('retailer')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->where('person_login.person_status','=','1')
                        ->where('person.role_id','=','180')
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        ->groupBy('retailer.id')
                        ->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',person.mobile)) as mobile"),'retailer.id');


        $userNames = DB::table('person')
                    ->where('company_id',$company_id)
                    ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');



        // dd($tsiUserMobileforNeha);


            $finalRecords=array();
            foreach ($data as $key => $value) {
                
                $retailer_id=$value->retailer_id;

                $tsiUserId = !empty($tsiUserIdforNeha[$retailer_id])?$tsiUserIdforNeha[$retailer_id]:'';

                $explodetsi = explode(',',$tsiUserId);

                $snone = array();
                $senior_one_id = array();
                foreach($explodetsi as $ekey => $eval){
                    $snone[] = !empty($userNames[$eval])?$userNames[$eval]:'';
                    $senior_one_id[] = $eval;
                }



              $second_senior_id = DB::table('person')
                    ->where('company_id',$company_id)
                    ->whereIn('person.id',$senior_one_id)
                    ->pluck(DB::raw("GROUP_CONCAT(DISTINCT person.person_id_senior) as user_id"),DB::raw("GROUP_CONCAT(DISTINCT person.person_id_senior) as user_id"));

                $sntwo = array();
                foreach($second_senior_id as $sekey => $seval){
                    $sntwo[] = !empty($userNames[$seval])?$userNames[$seval]:'';
                }



                if(!empty($productiveRetailerSales[$retailer_id])){
                    $status = 'Productive';
                }elseif (!empty($NonProductiveRetailerSales[$retailer_id])) {
                    $status = 'Non-Productive';
                }else{
                    $status = 'No Visit';
                }



                $user_record['state']=$value->l3_name;
                $user_record['area']=$value->l4_name;
                $user_record['hq']=$value->l5_name;
                $user_record['town']=$value->l6_name;
                // $user_record['emp_code']=$value->emp_code;
                $user_record['user_id']=!empty($tsiUserIdforNeha[$retailer_id])?$tsiUserIdforNeha[$retailer_id]:'';

                $user_record['user_name']=!empty($tsiUserforNeha[$retailer_id])?$tsiUserforNeha[$retailer_id]:'';
                $user_record['senior_name_one']=implode(',',$snone);
                $user_record['senior_name_two']=implode(',',$sntwo);
                // $user_record['user_id']=$value->user_id;
                // $user_record['role']=$value->l5_name;
                $user_record['mobile_no']=!empty($tsiUserMobileforNeha[$retailer_id])?$tsiUserMobileforNeha[$retailer_id]:'';;
                $user_record['distributor_name']=$value->dealer_name;
                $user_record['beat_name']=$value->beat_name;
                $user_record['retailer_name']=$value->retailer_name;
                $user_record['retailer_contact']=$value->landline;
                $user_record['sale_remarks']=$status;
                $finalRecords[] = $user_record;
               
            }


            // dd($finalRecords);


            return view('reports.unbilledOutletReport.ajax', [
                'records' => $finalRecords,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }




    public function userPrimarySalesReport(Request $request)
    {
        if ($request->ajax()) 
        {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $town = $request->town;
            $distributor = $request->distributor;
            $beat = $request->beat;
            $user_id = $request->user_id;
            $product=$request->product;
            $call_status = $request->call_status;
            // $explodeDate = explode(" -", $request->date_range_picker);
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $from_date = !empty($request->from_date)?$request->from_date:'';
            $to_date = !empty($request->to_date)?$request->to_date:'';
            $arr = [];
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            if(empty($from_date) && empty($to_date)){
                $status = '1';
            }else{
                $status = '0';
            }


            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $sale_amount = DB::table('user_primary_sales_order')
                           ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id') 
                           ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")
                           ->where('user_primary_sales_order.company_id',$company_id)
                           ->groupBy('user_primary_sales_order.order_id')
                           ->pluck(DB::raw("ROUND(SUM(final_secondary_qty*final_secondary_rate),2) as amount"),'user_primary_sales_order.order_id');


             $query_data = DB::table('user_primary_sales_order')
                ->leftJoin('person', 'person.id', '=', 'user_primary_sales_order.created_person_id')
                ->join('dealer', 'dealer.id', '=', 'user_primary_sales_order.dealer_id')
                ->join('csa','csa.c_id','=','dealer.csa_id')
                ->join('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->join('location_view', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->select('l3_id','csa_name','csa_code','dealer_code','location_view.l4_name', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as uid', 'dealer.name as dealer_name','dealer.id as did', 'user_primary_sales_order.*')
                ->where('user_primary_sales_order.company_id',$company_id);

                  



       

            if(!empty($datasenior))
            {
                $query_data->whereIn('user_id',$datasenior);
            }
            if(!empty($user_id))
            {   
                $query_data->whereIn('user_id',$user_id);
            }
            if(!empty($region))
            {
                $query_data->whereIn('l3_id',$region);
            }
            if(!empty($town))
            {
                $query_data->whereIn('l6_id',$town);
            }
            if(!empty($distributor))
            {
                $query_data->whereIn('dealer_id',$distributor);
            }
            if(!empty($beat))
            {
                $query_data->whereIn('location_id',$beat);
            }
            if(!empty($call_status))
            {
                $query_data->whereIn('call_status',$call_status);
            }

            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $query_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $query_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $query_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $query_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $query_data->whereIn('user_sales_order.dealer_id', $dealer);
            }
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $query_data->whereIn('user_id', $user);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $query_data->whereIn('person.role_id', $role);
            }


            if($status == '1'){
            $query_data->groupBy('user_primary_sales_order.order_id')
            ->orderBy('user_primary_sales_order.janak_order_sequence','DESC')
            ->take('50');
            }else{
            $query_data->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")
            ->groupBy('user_primary_sales_order.order_id')
            ->orderBy('user_primary_sales_order.janak_order_sequence','DESC');
            }

            $query = $query_data->get();

            // dd($query);
            $current_datre = date('Y-m-d');

            $datearray = array();
            $startTime = strtotime($from_date);
            $endTime = strtotime($to_date);

            for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
            {                                       
                $Store = date('Y-m-d', $currentDate); 
                $datearray[] = $Store; 
            }
            $product_percentage = array();
            
            foreach ($datearray as $key => $value) 
            {
                $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                                ->where('incentive_type',1)
                                ->where('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$value' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$value'")
                                // ->select()
                                ->get();

                foreach ($product_percentage_data as $keyi => $valuei) 
                {
                    $ans = $valuei->product_id.$valuei->state_id.$value;
                    $product_percentage[$ans] = $valuei->value_amount_percentage;
                }
            }
         
            $non_productive_reason_name = DB::table("_no_sale_reason")->where('company_id',$company_id)->where('status',1)->groupBy('id')->pluck('name','id');
            $out=array();
            $proout=array();
           if (!empty($query)) 
           {
                foreach ($query as $k => $d) 
                {
                    $uid=$d->order_id;
               
                    $proout = DB::table('user_primary_sales_order_details')
                                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                                ->select('catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.final_secondary_qty as cases','user_primary_sales_order_details.final_secondary_qty as cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.final_secondary_rate as pr_rate','user_primary_sales_order_details.order_id')
                                ->where('user_primary_sales_order_details.company_id',$company_id)
                                ->where('order_id', $uid);

                    
                   if(!empty($product))
                   {
                        $proout->whereIn('product_id',$product);
                   }
                    $out[$uid]=$proout->groupBy('user_primary_sales_order_details.id')->groupBy('order_id','product_id')->get(); 
                }
            }


             $productWeight = DB::table('catalog_product')
                        ->where('company_id',$company_id)
                        ->pluck('weight','id');


            // dd($out);

                return view('reports.userPrimarySalesReport.Janakajax', [
                    'records' => $query,
                    'order_detial_arr' => $out,
                    'non_productive_reason_name'=>$non_productive_reason_name,
                    'product_percentage'=> $product_percentage,
                    'productWeight'=> $productWeight,
                    'company_id'=> $company_id,


                ]);
        } 
        else 
        {
            echo '<p class="alert-danger">Data not Found</p>';
        }
    }




        public function primaryOrderDetails(Request $request)
    {
        $order_id = $request->orderid;
        $company_id = Auth::user()->company_id;

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        if(empty($check)){
             $data_return_query = DB::table('user_primary_sales_order_details')
                            ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                            ->select('user_primary_sales_order_details.*','catalog_product.name as product_name',DB::raw("((rate*quantity)+(cases*pr_rate)) as amount"))
                            ->where('user_primary_sales_order_details.order_id',$order_id)
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->groupBy('user_primary_sales_order_details.order_id','user_primary_sales_order_details.product_id')
                            ->orderBy('user_primary_sales_order_details.id','ASC');

        }else{
              $data_return_query = DB::table('user_primary_sales_order_details')
                            ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                            ->select('user_primary_sales_order_details.*','catalog_product.name as product_name',DB::raw("(final_secondary_rate*final_secondary_qty) as amount"))
                            ->where('user_primary_sales_order_details.order_id',$order_id)
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->groupBy('user_primary_sales_order_details.order_id','user_primary_sales_order_details.product_id')
                            ->orderBy('user_primary_sales_order_details.id','ASC');
        }
    
      
                           
                
            $data_return = $data_return_query->get();

        if($data_return)
        {
            $data['code'] = 200;
            $data['data_return'] = $data_return;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        return json_encode($data);

    }

    public function primaryOrderDetailsUpdate(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        
        $product_id = $request->product_id;
        $order_id = $request->order_id;
        $quantity = $request->quantity;
        $rate = $request->rate;

        // $case_qty = $request->case_qty;
        // $case_rate = $request->case_rate;

        // dd($request);



       
    if(!empty($product_id)){

        foreach ($product_id as $key => $value) 
        {

            // for scheme amount 

            

            $finalfinalvalue[] = $rate[$key]*$quantity[$key];

            // for scheme amount end


            $update_query = DB::table('user_primary_sales_order_details')
                        ->where('product_id',$value)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id[$key])
                        ->update([
                                    'final_secondary_qty'=> $quantity[$key],
                                    'secondary_qty'=> $quantity[$key],
                                    'quantity'=> $quantity[$key],
                                    'final_secondary_rate'=> $rate[$key],
                                  
                                ]);
        }
    }

    // dd($finalfinalvalue);

    $final_array_sum = array_sum($finalfinalvalue);

    $final_update_query = DB::table('user_primary_sales_order')
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id[0])
                        ->update([
                                    'amount_before_discount'=> $final_array_sum,
                                    'amount_after_discount'=> $final_array_sum,
                                  
                                ]);


        if($final_update_query)
        {
            DB::commit();
            Session::flash('message', "Order Update successfully");
            Session::flash('alert-class', 'alert-success');

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect()->intended('userPrimarySales');

    }



       public function primaryOrderWisePdfFormat(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;

        $amountData = DB::table('user_primary_sales_order_details')
                    ->select(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as amount_after_discount"))
                    ->where('company_id',$company_id)
                    ->where('order_id',$order_id)
                    ->first();

        $words = $this->getIndianCurrency($amountData->amount_after_discount);

        // dd($words);

        $quer_data = DB::table('user_primary_sales_order')
                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                ->join('product_type','product_type.id','=','catalog_product.product_type')
                ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                ->select('catalog_product.name as product_name','final_secondary_qty','final_secondary_rate','dealer.name as dealer_name','user_primary_sales_order.order_id as order_id','user_primary_sales_order.sale_date as sale_date','dealer.landline as mobile','dealer.address','user_primary_sales_order.dispatch_through','user_primary_sales_order.destination','product_type.name as primary_unit','discount_value as discount','remarks','user_primary_sales_order.janak_order_sequence')
                ->where('user_primary_sales_order.company_id',$company_id)
                ->where('user_primary_sales_order.order_id',$order_id)
                ->groupBy('user_primary_sales_order.order_id','product_id')
                ->get();

        $coampany_details = DB::table('company')->where('id',$company_id)->first();


        // dd($quer_data);
    

        $customPaper = array(0, 0, 1240, 1748);
        $pdf_name = $order_id.'.pdf';
        // dd($pdf_name);
        $pdf = PDF::loadView('pdf/pdfPrimary', ['coampany_details' => $coampany_details,'data_query'=>$quer_data,'words'=>$words]);
        $pdf->setPaper($customPaper);

        $pdf->save(public_path('pdf/'.$pdf_name));
            // return $pdf->download('some-filename.pdf');
        
        $pdf_path = public_path() . '/pdf/' .$pdf_name;

        if(!empty($quer_data))
        {
            $data['code'] = 200;
            $data['pdf_name'] = $pdf_name;
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['pdf_name'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }



    public function getIndianCurrency(float $number)
        {
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null;
            $digits_length = strlen($no);
            $i = 0;
            $str = array();
            $words = array(0 => '', 1 => 'one', 2 => 'two',
                3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
                7 => 'seven', 8 => 'eight', 9 => 'nine',
                10 => 'ten', 11 => 'eleven', 12 => 'twelve',
                13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
                19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
                40 => 'forty', 50 => 'fifty', 60 => 'sixty',
                70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
            $digits = array('', 'hundred','thousand','lakh', 'crore');
            while( $i < $digits_length ) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
            return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
        }
    



    public function beatWiseSaleReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();



        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                    ->join('location_7','location_7.id','=','user_sales_order.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('location_3','location_3.id','=','location_4.location_3_id')
                    ->select('person.emp_code','rolename','person.mobile as mobile','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_7.name as l7_name','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','location_7.id as location_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->groupBy('user_id','location_id')
                    ->orderBy('user_sales_order.date','DESC');
                    if (!empty($datasenior)) 
                    {
                        $main_query->whereIn('person.id', $datasenior);
                    }
                    if(!empty($state))
                    {
                        $main_query->whereIn('location_3.id',$state);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('location_3.id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('location_4.id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('location_5.id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('location_6.id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('user_sales_order.dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('user_sales_order.user_id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }
                    $main_query_data = $main_query->get();

                    // dd($main_query_data);

                    if(empty($check)){
                    $secondry_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->where('user_sales_order.company_id',$company_id)->groupBy('user_id','location_id')->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(user_id,location_id) as total"));
                    }else{
                    $secondry_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->where('user_sales_order.company_id',$company_id)->groupBy('user_id','location_id')->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),DB::raw("CONCAT(user_id,location_id) as total"));
                    }

                    $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');

                     $senior_role_data = DB::table('person')
                            ->join('_role','_role.role_id','=','person.role_id')
                             ->where('person.company_id',$company_id)
                             ->groupBy('person.id')
                             ->pluck('rolename','id');


                    $seniorId = DB::table('person')
                                ->where('company_id',$company_id)
                                ->pluck('person_id_senior','id');


                    $secondry_sale = array();
                    $senior_name = array();
                    $senior_senior_name = array();
                    foreach ($main_query_data as $key => $value) 
                    {
                        $user_id = $value->user_id;
                        $location_id = $value->location_id;
                      
                        $person_id_senior = $value->person_id_senior;

                        $seniorSeniorId = $seniorId[$person_id_senior];

                        $senior_name[$user_id]['senior_name'] = $senior_name_data[$person_id_senior];
                        $senior_name[$user_id]['senior_role'] = $senior_role_data[$person_id_senior];


                         $senior_senior_name[$person_id_senior]['senior_name'] = $senior_name_data[$seniorSeniorId];
                        $senior_senior_name[$person_id_senior]['senior_role'] = $senior_role_data[$seniorSeniorId];

                        $secondry_sale[$user_id.$location_id] = !empty($secondry_sale_data[$user_id.$location_id])?$secondry_sale_data[$user_id.$location_id]:'0';      
                    }

                 


                     $scheme_sale_data = DB::table('user_sales_order')
                     ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                     ->where('user_sales_order.company_id',$company_id)
                     ->groupBy('user_id','location_id')
                     ->pluck(DB::raw("SUM(total_sale_value) as total_sale_value"),DB::raw("CONCAT(user_id,location_id) as total"));


                     $retailerCount = DB::table('retailer')
                                    ->where('company_id',$company_id)
                                    ->groupBy('retailer.location_id')
                                    ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as count"),'retailer.location_id');




                    return view('reports.beatWiseSaleReport.ajax', [
                               'secondry_sale'=>$secondry_sale,
                               'senior_name'=>$senior_name,
                               'senior_senior_name'=>$senior_senior_name,
                               'main_query_data'=>$main_query_data,
                               'company_id'=>$company_id,
                               'scheme_sale_data'=>$scheme_sale_data,
                               'retailerCount'=>$retailerCount,

                            ]);


    }



     public function primaryOrderUpdate(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        
        $product_id = $request->product_id;
        $order_id = $request->order_id;
        $quantity = $request->quantity;
        $rate = $request->rate;

        $case_qty = $request->cases;
        $case_rate = $request->case_rate;

        // dd($request);



       
    if(!empty($product_id)){

        foreach ($product_id as $key => $value) 
        {

          
            $update_query = DB::table('user_primary_sales_order_details')
                        ->where('product_id',$value)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id[$key])
                        ->update([
                                    'pcs'=> $quantity[$key],
                                    'cases'=> $case_qty[$key],
                                ]);
        }
    }

    


        if($update_query)
        {
            DB::commit();
            Session::flash('message', "Order Update successfully");
            Session::flash('alert-class', 'alert-success');

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect()->intended('distributer-stock-report');

    }





    public function stockOrderDetails(Request $request)
    {
        $order_id = $request->orderid;
        $company_id = Auth::user()->company_id;


        $data_return_query = DB::table('dealer_balance_stock')
                            ->join('catalog_product','catalog_product.id','=','dealer_balance_stock.product_id')
                            // ->select('dealer_balance_stock.*','catalog_product.name as product_name',DB::raw("((rate*quantity)+(cases*pr_rate)) as amount"))
                            ->select('catalog_product.name as product_name','dealer_balance_stock.stock_qty as pcs','dealer_balance_stock.cases','dealer_balance_stock.mrp as pr_rate','dealer_balance_stock.pcs_mrp as rate','dealer_balance_stock.order_id','dealer_balance_stock.product_id',DB::raw("((stock_qty*pcs_mrp)+(cases*mrp)) as amount"))
                            ->where('dealer_balance_stock.order_id',$order_id)
                            ->where('dealer_balance_stock.company_id',$company_id)
                            ->groupBy('dealer_balance_stock.order_id','dealer_balance_stock.product_id')
                            ->orderBy('dealer_balance_stock.id','ASC');
      
                           
                
            $data_return = $data_return_query->get();

        if($data_return)
        {
            $data['code'] = 200;
            $data['data_return'] = $data_return;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        return json_encode($data);

    }


      public function primaryStockUpdate(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        
        $product_id = $request->product_id;
        $order_id = $request->order_id;
        $quantity = $request->quantity;
        $rate = $request->rate;

        $case_qty = $request->cases;
        $case_rate = $request->case_rate;

        // dd($request);



       
    if(!empty($product_id)){

        foreach ($product_id as $key => $value) 
        {

          
            $update_query = DB::table('dealer_balance_stock')
                        ->where('product_id',$value)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id[$key])
                        ->update([
                                    'stock_qty'=> $quantity[$key],
                                    'cases'=> $case_qty[$key],
                                ]);
        }
    }

    


        if($update_query)
        {
            DB::commit();
            Session::flash('message', "Order Update successfully");
            Session::flash('alert-class', 'alert-success');

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect()->intended('distributer-stock-report');

    }




    public function hitkaryBeatWiseSaleReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();



        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                    ->join('location_7','location_7.id','=','user_sales_order.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('location_3','location_3.id','=','location_4.location_3_id')
                    ->select('person.emp_code','rolename','person.mobile as mobile','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_7.name as l7_name','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','location_7.id as location_id',DB::raw("COUNT(DISTINCT user_sales_order.location_id,user_sales_order.date) as beat_visit"))
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->groupBy('location_id')
                    ->orderBy('location_3.name','ASC');
                    if (!empty($datasenior)) 
                    {
                        $main_query->whereIn('person.id', $datasenior);
                    }
                    if(!empty($state))
                    {
                        $main_query->whereIn('location_3.id',$state);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('location_3.id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('location_4.id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('location_5.id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('location_6.id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('user_sales_order.dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('user_sales_order.user_id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }
                    $main_query_data = $main_query->get();

                    // dd($main_query_data);

                    if(empty($check)){
                    $secondry_sale_data = DB::table('user_sales_order_details')
                                            ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                            ->where('user_sales_order.company_id',$company_id)->groupBy('location_id','catalog_2.id')
                                            ->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(location_id,catalog_2.id) as total"));
                    }else{
                    $secondry_sale_data = DB::table('user_sales_order_details')
                                    ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                    ->where('user_sales_order.company_id',$company_id)->groupBy('location_id','catalog_2.id')
                                    ->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),DB::raw("CONCAT(location_id,catalog_2.id) as total"));
                    }

               


                    // $secondry_sale = array();
                
                    // foreach ($main_query_data as $key => $value) 
                    // {
                    //     $location_id = $value->location_id;
                    //     $secondry_sale[$location_id] = !empty($secondry_sale_data[$location_id])?$secondry_sale_data[$location_id]:'0';      
                    // }

                 

                    $category = DB::table('catalog_2')
                                ->where('company_id',$company_id)
                                ->where('status','=','1')
                                ->pluck('name','id');
                  


                    



                    return view('reports.hitkaryBeatWiseSaleReport.ajax', [
                               'secondry_sale'=>$secondry_sale_data,
                               'main_query_data'=>$main_query_data,
                               'company_id'=>$company_id,
                               'category'=>$category,

                            ]);


    }



    public function hitkaryRetailerWiseSaleReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();



        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                    ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                    ->join('location_7','location_7.id','=','user_sales_order.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('location_3','location_3.id','=','location_4.location_3_id')
                    ->select('person.emp_code','rolename','person.mobile as mobile','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_7.name as l7_name','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','location_7.id as location_id',DB::raw("COUNT(DISTINCT user_sales_order.retailer_id,user_sales_order.date) as retailer_visit"),'retailer.id as retailer_id','retailer.name as retailer_name')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->groupBy('retailer_id')
                    ->orderBy('location_3.name','ASC');
                    if (!empty($datasenior)) 
                    {
                        $main_query->whereIn('person.id', $datasenior);
                    }
                    if(!empty($state))
                    {
                        $main_query->whereIn('location_3.id',$state);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('location_3.id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('location_4.id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('location_5.id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('location_6.id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('user_sales_order.dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('user_sales_order.user_id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }
                    $main_query_data = $main_query->get();

                    // dd($main_query_data);

                    if(empty($check)){
                    $secondry_sale_data = DB::table('user_sales_order_details')
                                            ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                            ->where('user_sales_order.company_id',$company_id)->groupBy('retailer_id','catalog_2.id')
                                            ->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(retailer_id,catalog_2.id) as total"));
                    }else{
                    $secondry_sale_data = DB::table('user_sales_order_details')
                                    ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                    ->where('user_sales_order.company_id',$company_id)->groupBy('retailer_id','catalog_2.id')
                                    ->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),DB::raw("CONCAT(retailer_id,catalog_2.id) as total"));
                    }

                    $category = DB::table('catalog_2')
                                ->where('company_id',$company_id)
                                ->where('status','=','1')
                                ->pluck('name','id');


                    return view('reports.hitkaryRetailerWiseSaleReport.ajax', [
                               'secondry_sale'=>$secondry_sale_data,
                               'main_query_data'=>$main_query_data,
                               'company_id'=>$company_id,
                               'category'=>$category,

                            ]);


    }



    public function hitkaryUserWiseSaleReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();



        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50)
        {
        $datasenior='';
        $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            
            $datasenior_call=self::getJuniorUser($login_user);
            Session::push('juniordata', $login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior)){
                $datasenior[]=$login_user;
                        }
        }


        $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                    ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                    ->join('location_7','location_7.id','=','user_sales_order.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('location_3','location_3.id','=','location_4.location_3_id')
                    ->select('person.emp_code','rolename','person.mobile as mobile','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_7.name as l7_name','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','location_7.id as location_id','retailer.id as retailer_id','retailer.name as retailer_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.mobile')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->groupBy('user_id')
                    ->orderBy('location_3.name','ASC');
                    if (!empty($datasenior)) 
                    {
                        $main_query->whereIn('person.id', $datasenior);
                    }
                    if(!empty($state))
                    {
                        $main_query->whereIn('location_3.id',$state);
                    }
                    if (!empty($request->location_3)) 
                    {
                        $location_3 = $request->location_3;
                        $main_query->whereIn('location_3.id', $location_3);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $main_query->whereIn('location_4.id', $location_4);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $main_query->whereIn('location_5.id', $location_5);
                    }
                    if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $main_query->whereIn('location_6.id', $location_6);
                    }
                    if (!empty($request->dealer)) 
                    {
                        $dealer = $request->dealer;
                        $main_query->whereIn('user_sales_order.dealer_id', $dealer);
                    }
                    if (!empty($request->user)) 
                    {
                        $user = $request->user;
                        $main_query->whereIn('user_sales_order.user_id', $user);
                    }
                    if (!empty($request->role)) 
                    {
                        $role = $request->role;
                        $main_query->whereIn('person.role_id', $role);
                    }
                    $main_query_data = $main_query->get();

                    // dd($main_query_data);

                    if(empty($check)){
                    $secondry_sale_data = DB::table('user_sales_order_details')
                                            ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                            ->where('user_sales_order.company_id',$company_id)->groupBy('user_id','catalog_2.id')
                                            ->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(user_id,catalog_2.id) as total"));
                    }else{
                    $secondry_sale_data = DB::table('user_sales_order_details')
                                    ->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')
                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                    ->where('user_sales_order.company_id',$company_id)->groupBy('user_id','catalog_2.id')
                                    ->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),DB::raw("CONCAT(user_id,catalog_2.id) as total"));
                    }

                    $category = DB::table('catalog_2')
                                ->where('company_id',$company_id)
                                ->where('status','=','1')
                                ->pluck('name','id');

                    $userWiseBeats = DB::table('dealer_location_rate_list')
                                    ->where('company_id',$company_id)
                                    ->groupBy('user_id')
                                    ->pluck(DB::raw("COUNT(DISTINCT location_id) as count"),'user_id');
                                    


                    return view('reports.hitkaryUserWiseSaleReport.ajax', [
                               'secondry_sale'=>$secondry_sale_data,
                               'main_query_data'=>$main_query_data,
                               'company_id'=>$company_id,
                               'category'=>$category,
                               'userWiseBeats'=>$userWiseBeats,

                            ]);


    }



     public function skuOrderDetails(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $order_id = $request->orderid;
     
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
       
         if(empty($check)){
                $details = DB::table('user_sales_order_details')
                            ->select('order_id','name','rate','quantity as qty')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->where('order_id',$order_id)
                            ->where('user_sales_order_details.company_id',$company_id)
                            ->get()->toArray();

            }else{
                   $details = DB::table('user_sales_order_details')
                            ->select('order_id','name','final_secondary_rate as rate','final_secondary_qty as qty')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->where('order_id',$order_id)
                            ->where('user_sales_order_details.company_id',$company_id)
                            ->get()->toArray();
            }

       


        if($details)
        {
            $data['code'] = 200;
            $data['data_return'] = $details;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        // dd($data);
        return json_encode($data);

    }



    public function distributorAssignReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->area;
        $location2 = $request->location2;
        $role = $request->role;
        $user = $request->user;
        // $from_date = $request->from_date;
        // $to_date = $request->to_date;

        //  $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


        // $start = strtotime($from_date);
        // $end = strtotime($to_date);


        // $datearray = array();
        // $datediff =  ($end - $start)/60/60/24;
        // $datearray[] = $from_date;

        // for($i=0 ; $i<$datediff;$i++)
        // {
        //     $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        // }


        $person_data = DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_6','location_6.id','=','person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->select('person.id as user_id',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','person.mobile','location_3.name as state','person.person_id_senior','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.emp_code')
                ->where('person.company_id',$company_id) 
                ->where('_role.company_id',$company_id) 
                ->where('location_3.company_id',$company_id) 
                ->where('person_login.person_status','=','1')
                ->groupBy('person.id');
                if(!empty($state))
                {
                    $person_data->whereIn('location_3.id',$state);
                }
                if(!empty($request->user))
                {
                    $person_data->whereIn('person.id',$request->user);
                }
                 if(!empty($request->role))
                {
                    $person_data->whereIn('person.role_id',$request->role);
                }
                 if(!empty($request->location_3))
                {
                    $person_data->whereIn('location_3.id',$request->location_3);
                }
                 if(!empty($request->location_4))
                {
                    $person_data->whereIn('location_4.id',$request->location_4);
                }
                 if(!empty($request->location_5))
                {
                    $person_data->whereIn('location_5.id',$request->location_5);
                }
                   if(!empty($request->location_6))
                {
                    $person_data->whereIn('location_6.id',$request->location_6);
                }
        $person = $person_data->get();   

        $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');


        $asignDsitributor = DB::table('dealer_location_rate_list')
                            ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                            ->where('dealer.company_id',$company_id)
                            ->where('dealer_location_rate_list.company_id',$company_id)
                            ->where('dealer.dealer_status','=','1')
                            ->groupBy('user_id')
                            ->pluck(DB::raw("GROUP_CONCAT(DISTINCT dealer.name) as dealer_name"),'user_id');



                  



        return view('reports.distributorAssignReport.ajax', [
            'records' => $person,
            'senior_name_data' => $senior_name_data,
            // 'from_date'=> $from_date,
            // 'to_date'=> $to_date,
            // 'datearray'=> $datearray,
            // 'datediff'=> $datediff,
            'asignDsitributor'=> $asignDsitributor,
            
            

        ]);


    }



    public function skuWiseCounterSaleReport(Request $request)
    {
        if ($request->ajax()) 
        {
            $company_id = Auth::user()->company_id;
            $region = $request->region;
            $town = $request->town;
            $distributor = $request->distributor;
            $beat = $request->beat;
            $user_id = $request->user_id;
            $product=$request->product;
            $call_status = $request->call_status;
            $startMonth = $request->startMonth;
            $endMonth = $request->endMonth;


            $date1 = date('Y-m-01',strtotime($startMonth));
            $date2 = date('Y-m-t',strtotime($endMonth));
            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);
            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);
            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);
            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
             $from_date =  date('Y-m',strtotime($startMonth));
             $to_date =  date('Y-m',strtotime($endMonth));
            $start = strtotime($from_date);
            $end = strtotime($to_date);    
            $monthArray = array();
            $monthArray[] = $startMonth;
            for($i=0 ; $i<$diff;$i++)
            {
                $monthArray[] = date('Y-m', strtotime($monthArray[$i] .' +1 month'));
            }


            $table_name = TableReturn::table_return($date1,$date2);
            $arr = [];
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $senior_name = DB::table('person')
                            ->where('company_id',$company_id)
                            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');



            $assignRetailerData = DB::table('retailer')
                            ->select('retailer.id as retailer_id','retailer.name as retailer_name','user_id')
                            ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
                            ->join('person','person.id','=','dealer_location_rate_list.user_id')
                            ->where('retailer.company_id',$company_id)
                            ->where('dealer_location_rate_list.company_id',$company_id)
                            ->where('user_id','!=','0')
                            ->groupBy('user_id','retailer.id');
                            if (!empty($request->location_3)) 
                            {
                                $location_3 = $request->location_3;
                                $assignRetailerData->whereIn('person.state_id', $location_3);
                            }
                              if (!empty($request->role)) 
                            {
                                $role = $request->role;
                                $assignRetailerData->whereIn('person.role_id', $role);
                            }
            $assignRetailer = $assignRetailerData->get();

            $userWiseRetailer = array();
            foreach ($assignRetailer as $arkey => $arvalue) {
                $userWiseRetailer[$arvalue->user_id][] = $arvalue;
            }


            // dd($userWiseRetailer);



            $query_data = DB::table('person')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_3.name as state_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','person.emp_code','rolename','person.person_id_senior','person.mobile')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->where('person.company_id',$company_id)
                        ->where('location_6.company_id',$company_id)
                        ->where('location_5.company_id',$company_id)
                        ->where('location_4.company_id',$company_id)
                        ->where('location_3.company_id',$company_id)
                        ->where('person_login.person_status','=','1')
                        ->groupBy('person.id');
                        if (!empty($request->location_3)) 
                        {
                            $location_3 = $request->location_3;
                            $query_data->whereIn('location_3.id', $location_3);
                        }
                        if (!empty($request->location_4)) 
                        {
                            $location_4 = $request->location_4;
                            $query_data->whereIn('location_4.id', $location_4);
                        }
                        if (!empty($request->location_5)) 
                        {
                            $location_5 = $request->location_5;
                            $query_data->whereIn('location_5.id', $location_5);
                        }
                        if (!empty($request->location_6)) 
                        {
                            $location_6 = $request->location_6;
                            $query_data->whereIn('location_6.id', $location_6);
                        }
                     
                        if (!empty($request->user)) 
                        {
                            $user = $request->user;
                            $query_data->whereIn('person.id', $user);
                        }
                        if (!empty($request->role)) 
                        {
                            $role = $request->role;
                            $query_data->whereIn('person.role_id', $role);
                        }


            $query = $query_data->get();

            $userDetail = array();
            foreach ($query as $qkey => $qvalue) {
                $userDetail[$qvalue->user_id] = $qvalue;
            }
            $CatalogProduct = DB::table('catalog_product')
                                ->where('company_id',$company_id)
                                ->whereIn('id',$product)
                                ->pluck('name','id');


            // for calc neha
            $ProductType = DB::table('product_type')->where('status','1')->where('company_id',$company_id)->pluck('name','id')->toArray(); 
            $finalProductTypeOut = array();
            foreach ($ProductType as $key => $value) {
               $finalProductTypeOut[$key] = $value;
           }   
           $finalProductTypeOut['0'] = "Pieces";

           // dd($finalProductTypeOut);


            $finalCatalogProduct = DB::table('product_type')
                                ->where('product_type.company_id',$company_id)
                                ->groupBy('product_type.id')
                                ->pluck('flag_neha','product_type.id')->toArray();
             // for calc neha ends

            // dd($finalCatalogProduct)
         



            $saleDataQuery = DB::table($table_name)
                        ->select(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),$table_name.'.user_id',$table_name.'.retailer_id','user_sales_order_details.product_id',DB::raw("DATE_FORMAT(date,'%Y-%m') as month"),'catalog_product.final_product_type','catalog_product.quantity_per_case as quantity_per_case','catalog_product.quantiy_per_other_type as quantiy_per_other_type',DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as sale_value"))
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$date1' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date2'")
                        ->groupBy('user_sales_order_details.product_id',$table_name.'.user_id',$table_name.'.retailer_id','month')
                        ->where($table_name.'.company_id',$company_id)
                        ->where('user_sales_order_details.company_id',$company_id)
                        ->where('catalog_product.company_id',$company_id)
                        ->whereIn('catalog_product.id',$product)
                        ->get();

            $finalOutDsr = array();
            $finalOutDsrSale = array();
            foreach ($saleDataQuery as $dsrkey => $dsrvalue) {

                $productId = $dsrvalue->product_id;
                $flagNeha = !empty($finalCatalogProduct[$dsrvalue->final_product_type])?$finalCatalogProduct[$dsrvalue->final_product_type]:'0';
                $finalConcat = $dsrvalue->user_id.$dsrvalue->retailer_id.$dsrvalue->month.$dsrvalue->product_id.$dsrvalue->final_product_type;

                $finalConcatForSale = $dsrvalue->user_id.$dsrvalue->retailer_id.$dsrvalue->month.$dsrvalue->product_id;




                if($flagNeha == '0'){
                     $finalOutDsr[$finalConcat]['quantity'] =  $dsrvalue->product_quantity;
                }else{


                    if($flagNeha == '1'){
                        $finalOutDsr[$finalConcat]['quantity'] =  ($dsrvalue->product_quantity/$dsrvalue->quantity_per_case);
                    }elseif($flagNeha == '2'){
                        $finalOutDsr[$finalConcat]['quantity'] =  ($dsrvalue->product_quantity/$dsrvalue->quantiy_per_other_type);
                    }else{
                        $finalOutDsr[$finalConcat]['quantity'] =  '0';
                    }
                }

                $finalOutDsr[$finalConcat]['concat'] =  $finalConcat;
                $finalOutDsr[$finalConcat]['flagNeha'] =  $flagNeha;
                $finalOutDsr[$finalConcat]['final_product_type'] =  $dsrvalue->final_product_type;


                $finalOutDsrSale[$finalConcatForSale] =  $dsrvalue->sale_value;


            }

            // dd($finalOutDsr);

           
                return view('reports.skuWiseCounterSaleReport.ajax', [
                    'userDetail' => $userDetail,
                    'userWiseRetailer' => $userWiseRetailer,
                    'monthArray' => $monthArray,
                    'senior_name' => $senior_name,
                    'company_id'=> $company_id,
                    'CatalogProduct'=> $CatalogProduct,
                    'finalProductTypeOut' => $finalProductTypeOut,
                    'finalOutDsr' => $finalOutDsr,
                    'finalOutDsrSale' => $finalOutDsrSale,

                ]);
        } 
        else 
        {
            echo '<p class="alert-danger">Data not Found</p>';
        }
    }


    public function takeActionIsGolden(Request $request)
    {     
        // dd($request);


        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $id = $request->id;
   
   
        if ($request->ajax() && !empty($id)) {
            #module based action
            DB::beginTransaction();
            
            $checkGolden = DB::table('retailer')
                            ->where('company_id',$company_id)
                            ->where('id',$id)
                            ->first();



         
            if($checkGolden->is_golden == 1){
            $query = DB::table('retailer')->where('id', $id)->where('company_id',$company_id)->update(['is_golden_approved'=>'1']);
            }else{
                  DB::rollback();
                $data['code'] = 401;
                $data['result'] = 'fail';
                $data['message'] = 'First Make This Oulet A Golden Outlet!!';
                return json_encode($data);

            }


           
            if ($query) {
                #commit transaction
                DB::commit();
                $data['code'] = 200;
                $data['result'] = 'success';
                $data['message'] = 'success';
                return json_encode($data);
            } else {
                #rollback transaction
                DB::rollback();
                $data['code'] = 401;
                $data['result'] = 'fail';
                $data['message'] = 'Already A Approved Golden Outlet';
                return json_encode($data);
            }
        } else {
            #for unauthorized request
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
            return json_encode($data);
        }
        return json_encode($data);
    }


    public function check_read_write_permission(Request $request){

        $company_id = Auth::user()->company_id;
        $is_admin = Auth::user()->is_admin;
        if ($request->ajax() && ($request->title)) {
            $title = $request->title;
            // dd($title);
            $title = str_replace('/public/','',$title);
            $data['code'] = 200;
            $permissions = DB::table('company_web_module_permission')
                    ->join('modules_bucket','modules_bucket.id','=','company_web_module_permission.module_id')
                    ->where('company_web_module_permission.company_id',$company_id)
                    ->where('modules_bucket.title',$title)
                    ->where('role_id',Auth::user()->role_id)
                    ->first();
            // dd($permissions);
            $permissions_2_layer = DB::table('company_sub_web_module_permission')
                    ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','company_sub_web_module_permission.sub_module_id')
                    ->where('company_sub_web_module_permission.company_id',$company_id)
                    ->where('sub_web_module_bucket.title',$title)
                    ->where('role_id',Auth::user()->role_id)
                    ->first();
                    // dd($permissions_2_layer);

            $checkReportJuniorWise = DB::table('company_web_module_permission')
                                    ->where('company_web_module_permission.company_id',$company_id)
                                    ->where('role_id',Auth::user()->role_id)
                                    ->where('module_id','=','33')
                                    ->first();  // check if module assign junior wise or not




            if($is_admin != 1){

                // only for report section code
                if(!empty($checkReportJuniorWise)){
                    if($checkReportJuniorWise->without_junior == 1){
                        $data['without_junior'] = '1'; // 1 means report run junior wise
                    }
                    else{
                        $data['without_junior'] = '0'; // 0 means report run without junior wise like admin
                    }
                }else{
                        $data['without_junior'] = '1'; // 1 means report run junior wise
                }
                // only for report section code



                if(!empty($permissions)){
                    

                    if($permissions->add_status == 1){
                        $data['create_permission'] = '1';
                    }
                    else{
                        $data['create_permission'] = '0';
                    }
                    if($permissions->edit_status == 1){
                        $data['edit_permission'] = '1';
                    }
                    else{
                        $data['edit_permission'] = '0';
                    }
                    if($permissions->delete_status == 1){
                        $data['delete_permission'] = '1';
                    }
                    else{
                        $data['delete_permission'] = '0';
                    }
                    
                    
                }else{
                    if(!empty($permissions_2_layer)){
                        if($permissions_2_layer->add_status == 1){
                            $data['create_permission'] = '1';
                        }
                        else{
                            $data['create_permission'] = '0';
                        }
                        if($permissions_2_layer->edit_status == 1){
                            $data['edit_permission'] = '1';
                        }
                        else{
                            $data['edit_permission'] = '0';
                        }
                        if($permissions_2_layer->delete_status == 1){
                            $data['delete_permission'] = '1';
                        }
                        else{
                            $data['delete_permission'] = '0';
                        }
                    }else{
                        $data['create_permission'] = '0';
                        $data['edit_permission'] = '0';
                        $data['delete_permission'] = '0';
                    }
                    
                }
            }else{
                $data['create_permission'] = '1';
                $data['edit_permission'] = '1';
                $data['delete_permission'] = '1';
            }
            
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

}

