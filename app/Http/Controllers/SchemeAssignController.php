<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\SchemePlan;
use App\Company;
use App\SchemePlanDetails;
use App\JuniorData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use DB;
use Image;

class SchemeAssignController extends Controller
{
	public function index( Request $request)
	{ 
        $retailer_data = array();
        $company_id_auth = Auth::user()->company_id;
		
        $flag = !empty($request->flag)?$request->flag:'';
        $plan_id = $request->plan_id;
        $date_range = $request->date_range_picker;
        $plan_array = [];
        $dealer_data = [];
        $retailer_data = [];
        $from_date = '';
        $to_date = '';

		
		if($flag==1)
		{
            $retailer_data_query = DB::table('retailer')
                            ->join('location_7','location_7.id','=','retailer.location_id')
                            ->join('location_6','location_6.id','=','location_7.location_6_id')
                            ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('location_4','location_4.id','=','location_5.location_4_id')
                            ->join('location_3','location_3.id','=','location_4.location_3_id')
                            ->select('retailer.*')
                            ->where('retailer_status',1)
                            ->where('retailer.company_id',$company_id_auth)
                            ->where('location_7.company_id',$company_id_auth)
                            ->where('location_6.company_id',$company_id_auth)
                            ->where('location_5.company_id',$company_id_auth)
                            ->where('location_4.company_id',$company_id_auth)
                            ->where('location_3.company_id',$company_id_auth);
                            if(!empty($request->state)){
                                $retailer_data_query->whereIn('location_3.id',$request->state);
                            }
            $retailer_data =   $retailer_data_query->get()->toArray();





            $dealer_data_query = DB::table('dealer')
                        ->join('location_3','location_3.id','=','dealer.state_id')
                        ->select('dealer.id','dealer.name')
                        ->where('dealer_status',1)
                        ->where('dealer.company_id',$company_id_auth);
                        if(!empty($request->state)){
                            $dealer_data_query->whereIn('location_3.id',$request->state);
                        }
            $dealer_data = $dealer_data_query->get()->toArray();


            $plan_array = SchemePlan::where('status',1)->where('id',$plan_id)->where('company_id',$company_id_auth)->pluck('scheme_name','id')->toArray();

            $location_3 = DB::table('location_3')->where('status',1)->where('company_id',$company_id_auth)->pluck('name','id');

            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
		}
		else
		{
     
            $plan_array = SchemePlan::where('status',1)->where('company_id',$company_id_auth)->pluck('scheme_name','id');
            $location_3 = DB::table('location_3')->where('status',1)->where('company_id',$company_id_auth)->pluck('name','id');

		}
		return view('SchemeAssign.index',[


                'plan_array'=> $plan_array,
                'dealer_data'=>$dealer_data,
                'retailer_data'=>$retailer_data,
                'from_date'=> $from_date,
                'to_date'=> $to_date,
                'plan_id'=> $plan_id,
                'location_3'=> $location_3,


			]);

	}
	
    public function store(Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'plan_id' => 'required|max:50',
            // 'retailer_data_id' => 'required',
    
            // 'dealer_data_id' => 'required',
        ]);
        $plan_id = $request->plan_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $dealer_data_id = $request->dealer_data_id;
        $retailer_data_id = $request->retailer_data_id;
        $user_id = Auth::user()->id;
        $company_id = Auth::user()->company_id;
        $scheme_assign_retailer_submit = '';
        $scheme_assign_dealer_submit = '';
        DB::beginTransaction();
// dd($retailer_data_id);
        if(!empty($retailer_data_id))
        {
            $delete_retailer_id = DB::table('scheme_assign_retailer')->whereIn('retailer_id',$retailer_data_id)->where('plan_id',$plan_id)->where('plan_assigned_to_date',$to_date)->where('plan_assigned_from_date',$from_date)->delete();
            foreach($retailer_data_id as $key => $value)
            {
                $scheme_assign_retailer_submit = DB::table('scheme_assign_retailer')->insert([
                    'plan_id'=>$plan_id,
                    'retailer_id'=>$value,
                    'plan_assigned_to_date'=>$to_date,
                    'plan_assigned_from_date'=>$from_date,
                    'company_id'=> $company_id,
                    'status'=> 1,
                    'created_by'=> $user_id,
                    'created_at'=> date("Y-m-d H:i:s"),
                    'updated_at'=> date("Y-m-d H:i:s"),

                ]);
            }
        }
        if(!empty($dealer_data_id))
        {
            $delete_dealer_id = DB::table('scheme_assign_dealer')->whereIn('dealer_id',$dealer_data_id)->where('plan_assigned_to_date',$to_date)->where('plan_assigned_from_date',$from_date)->delete();
            foreach($dealer_data_id as $d_key => $d_value)
            {
                $scheme_assign_dealer_submit = DB::table('scheme_assign_dealer')->insert([
                    'plan_id'=>$plan_id,
                    'dealer_id'=>$d_value,
                    'plan_assigned_to_date'=>$to_date,
                    'plan_assigned_from_date'=>$from_date,
                    'company_id'=> $company_id,
                    'status'=> 1,
                    'created_by'=> $user_id,
                    'created_at'=> date("Y-m-d H:i:s"),
                    'updated_at'=> date("Y-m-d H:i:s"),

                ]);

            }
        }
       
        if($scheme_assign_dealer_submit || $scheme_assign_retailer_submit)
        {
            DB::commit();
            Session::flash('message', "Plan Assign successfully");
            Session::flash('alert-class', 'alert-success');
            return redirect('schemeAssign');
        }
        else
        {
            DB::rollback();
            Session::flash('message', "Plan Not Assign");
            Session::flash('alert-class', 'alert-danger');
            return redirect('schemeAssign');
        }

    }
    
}