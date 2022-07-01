<?php

namespace App\Http\Controllers;


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
use App\JuniorData;
// use App\juniordata;
use App\User;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use App\DealerLocation;
use Illuminate\Http\Request;
use DB;
use Auth;
use Illuminate\Support\Facades\Session;
use DateTime;

class AjaxDistributorAssignController extends Controller
{
    #................for assign .....................................##
    public function getLocationForAssign(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->id) && !empty($request->type)) {
            $id2 = $request->id;

            $id = explode(',',$id2);
            $type = $request->type;
            $data['code'] = 200;

            $table = 'location_' . $type;
            $ptable_id = 'location_' . ($type - 1) . '_id';

            $query = DB::table($table)
                ->whereIn($ptable_id, $id)
                ->where('company_id',$company_id)
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

    #................for assign .Ends here .................................##

    #......................Filter Distributor...............................##
    public function distributorList(Request $request)
    {
        if ($request->ajax() OR !empty($request->location_4) OR !empty($request->location_3) OR !empty($request->location_2) OR !empty($request->location_1)) {
            $role = $request->role;
            $data['code'] = 200;

            $location_1 =  $request->location_1;
            $location_2 =   $request->location_2;
            $location_3 =   $request->location_3;
            $location_4 =   $request->location_4;
            $location_5 =   $request->location_5;
            $location_6 =   $request->location_6;
            $company_id = Auth::user()->company_id;

            $uuid = $request->uuid;
            $query1 = DB::table('dealer_location_rate_list')
                ->join('location_view', 'location_view.l7_id', '=', 'dealer_location_rate_list.location_id', 'inner')
                ->join('dealer', 'dealer.id', '=', 'dealer_location_rate_list.dealer_id', 'inner')
                ->where('dealer_location_rate_list.company_id',$company_id)
                ->where('dealer.company_id',$company_id)
                ->where('dealer_status',1)
                ->select('dealer.*');

                 #location_1 filter
                    if (!empty($request->location_1)) {
                        $query1->whereIn('location_view.l1_id', $location_1);
                    }

                    #location_2 filter
                    if (!empty($request->location_2)) {
                    $query1->whereIn('location_view.l2_id', $location_2);
                    }

                     #location_3 filter
                     if (!empty($request->location_3)) {
                        $query1->whereIn('location_view.l3_id', $location_3);
                        }

                           #location_4 filter
                     if (!empty($request->location_4)) {
                        $query1->whereIn('location_view.l4_id', $location_4);
                        }
                          #location_5 filter
                     if (!empty($request->location_5)) {
                        $query1->whereIn('location_view.l5_id', $location_5);
                        }
                          #location_6 filter
                     if (!empty($request->location_6)) {
                        $query1->whereIn('location_view.l6_id', $location_6);
                        }
              $query = $query1 ->groupBy('dealer.id')
                               ->get();

            return view('ajax.distributor', [
                'rows' => $query,
                'uuid' => $uuid
            ]);

        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    #......................Filter Distributor Ends here...............................##
    #......................Filter Beat  starts here...............................##
    public function distributorBeat(Request $request)
    {
        if ($request->ajax() && !empty($request->dealer_id)) {
            $role = $request->role;
            $data['code'] = 200;

            $dealer_check = $request->dealer_check;
            if(empty($dealer_check))
            {
                $data['code'] = 401;
                $data['result'] = '';
                $data['message'] = 'unauthorized request';
                return json_encode($data);

            }
            $user_id = $request->user_id;
            $query = DB::table('dealer_location_rate_list')
                ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                ->whereIn('dealer_location_rate_list.dealer_id', $dealer_check)
                ->select('dealer_id','dealer.name',DB::raw("COUNT(distinct location_id) as beat_count"))
                ->groupBy('dealer_id')
                ->orderBy('beat_count','DESC')
                ->get();

                $dsr=[];
                foreach ($query as $key => $value) {
                    $dealer_id=$value->dealer_id;    
                  
                    $dsr[$dealer_id]=DB::table('dealer_location_rate_list')->join('location_view', 'location_view.l7_id', '=', 'dealer_location_rate_list.location_id', 'inner')->select('dealer_id','location_view.l7_id as l5_id', 'location_view.l7_name as l5_name')->where('dealer_location_rate_list.dealer_id', $dealer_id)
                    ->groupBy('l7_id')
                   ->get()->toArray();

                }

// print_r($dsr); exit;
               
            $dlrl = DB::table('dealer_location_rate_list')
                ->whereIn('dealer_location_rate_list.dealer_id', $dealer_check)
                ->where('dealer_location_rate_list.user_id', $user_id)
                ->pluck('location_id')
                ->toArray();

            $dlrl_1 = DB::table('dealer_location_rate_list')
            ->whereIn('dealer_location_rate_list.dealer_id', $dealer_check)
            ->where('dealer_location_rate_list.user_id', $user_id)
            ->pluck('dealer_id')
            ->toArray();


            return view('ajax.beat', [
                'rows' => $query,
                'dsr' =>$dsr,
                'dealer_id' => $dealer_check,
                'user_id' => $user_id,
                'dlrl' => $dlrl,
                'dlrl_1' => $dlrl_1,
            ]);

        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

   
    #......................Filter Beat  Ends here...............................##
    #......................Assign beat final subbmission starts here...............................##
    public function assignBeat(Request $request)
    {
        if ($request->ajax() && !empty($request->beat)) {
            $role = $request->role;
            $data['code'] = 200;
            $beat = $request->beat;
            $distributor = $request->distributor;
            $company_id = Auth::user()->company_id;

              DB::beginTransaction();
               $delete_dlrl = DealerLocation::whereIn('dealer_id', $distributor)
                    ->where('company_id',$company_id)
                    ->where('user_id', $request->user_id)
                    ->delete();

            $myArr = [];
            if(!empty($request->senior_assing))
            {
                Session::forget('seniorData');
                $fetch_senior_id=JuniorData::getSeniorUser($request->user_id,$company_id);
                $senior_data = Session::get('seniorData');
            }
            // dd($senior_data);
                foreach ($beat as $d => $b) {
                    foreach($b as $bk => $bv){
                        $myArr = [
                            'dealer_id' => $d,
                            'location_id' => $bv,
                            'user_id' => $request->user_id,
                            'company_id' => $company_id,
                            'server_date' => date('Y-m-d H:i:s')
                        ];
                        if(!empty($request->senior_assing))
                        {
                            foreach ($senior_data as $key => $value) {
                                $check = DealerLocation::where('location_id',$bv)->where('dealer_id',$d)->where('user_id',$value)->count();
                                // dd($check);
                                if($check>0)
                                {
                                    $is_set = 0;
                                }
                                else
                                {
                                    $myArr2 = [
                                        'dealer_id' => $d,
                                        'location_id' => $bv,
                                        'user_id' => $value,
                                        'company_id' => $company_id,
                                        'server_date' => date('Y-m-d H:i:s')
                                    ];
                                    $dlrl2 = DealerLocation::insert($myArr2);
                                }
                                
                            }    
                        }                   
                        

                        

                        $dlrl = DealerLocation::insert($myArr);
                        if(!$dlrl && !$dlrl2)
                        {
                            DB::rollback();
                            $data['code'] = 401;
                            $data['result'] = '';
                            $data['message'] = 'unauthorized request';
                            return json_encode($data);
                        }
                    }
                } 
            // dd($dlrl);
           if($dlrl)
           {       
                DB::commit();           
                $data['code'] = 200;
                $data['result'] = 'Successfully Saved.';
                $data['message'] = 'Successfully Saved.';   
            } 
         else 
         {
            DB::rollback();
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        // die();
    }
    else
    {
        $data['code'] = 401;
        $data['result'] = '';
        $data['message'] = 'unauthorized request';
    }

        return json_encode($data);
    }
    #......................Assign beat final subbmission Ends here...............................##




}
