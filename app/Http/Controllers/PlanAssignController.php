<?php

namespace App\Http\Controllers;


use App\Catalog1;
use App\Catalog2;
use App\Catalog3;
use App\CatalogProduct;
use App\User;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class PlanAssignController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'plan_assign';
        $this->current_dir = 'planassign';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $company_id = Auth::user()->company_id;
            $month = !empty($request->month)?$request->month:date('Y-m');
           // $month='2019-01';
            $m1=explode('-', $month);
            $y=$m1[0];
            $m2=$m1[1];
            if($m2<10)
            $m=ltrim($m2, '0');
            else
            $m=$m2;

            $total_days=cal_days_in_month(CAL_GREGORIAN,$m,2005);
            $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            for($i = 1; $i <=  $total_days; $i++)
            {
            // add the date to the dates array
            $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
            }
            $data1 = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('location_view','location_view.l3_id','=','person.state_id')
            ->where('person_status','1')
            ->where('person.company_id',$company_id)
            ->where('users.company_id',$company_id)
            ->where('is_admin',0)
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l2_name','l3_name','rolename AS role_name','emp_code','person.id AS user_id')
            ->groupBy('person.id')
            ->orderBy('user_name');
            
            $user_records = $data1->get();
            // dd($datesArr);

            $plan = DB::table('user_incentive_details')->where('company_id',$company_id)->where('status',1)->pluck('plan_name','id');
       
        return view($this->current_dir.'.index',
            [
                'records' => $user_records,
                'month' => $month,
                'datesArr'=>$datesArr,
                'datesDisplayArr'=>$datesDisplayArr,
                'plan'=>$plan,
                'current_menu' => $this->current_menu
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
   

    public function assignPlans(Request $request)
    {
        // dd($request->plan);
      
       $plan = $request->plan; // all checked plans

      //  dd($plan);
         // dd($submodule);
         DB::beginTransaction();

          $company_id = Auth::user()->company_id;
            foreach($plan as $smkey => $smvalue)
            {
                if($smvalue != null)
                {

                    $break_plan_module = explode('|',$smvalue);

                    $delete_plan_module = DB::table('user_plan_assign')->where('user_id',$break_plan_module[0])
                                        ->where('plan_user_assigned_date',$break_plan_module[1])
                                        ->where('plan_id',$break_plan_module[2])
                                        ->delete();

                  $_plan_module = DB::table('user_plan_assign')->insert([
                    'user_id' => $break_plan_module[0],
                    'plan_id' => $break_plan_module[2],
                    'company_id'=>$company_id,
                    'plan_user_assigned_date' => $break_plan_module[1]
                  ]);
                }

            }
         

             if($_plan_module)
             {
                DB::commit();
               Session::flash('message', "Plan Assign successfully");
               Session::flash('alert-class', 'alert-success');
               return redirect('plan_assign');
             }
             else
             {
                   DB::rollback();
                   Session::flash('message', "Plan Not Assign");
                   Session::flash('alert-class', 'alert-danger');
                   return redirect('plan_assign');
             }
 
    }





}
