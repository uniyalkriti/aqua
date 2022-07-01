<?php

namespace App\Http\Controllers;

use App\_module;
use DB;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;

class DmsRegistrationController extends Controller
{

    public function __construct()
    {
        // dd('1');
        session_start();
        $auth_id = !empty($_SESSION['iclientdigimetid'])?$_SESSION['iclientdigimetid']:'0';
        $this->auth_id = $auth_id; 
        $this->dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
        $this->csa_id = !empty($_SESSION['iclientdigimetdata']['csa_id'])?$_SESSION['iclientdigimetdata']['csa_id']:'0';
        $this->dealer_code = !empty($_SESSION['iclientdigimetdata']['dealer_code'])?$_SESSION['iclientdigimetdata']['dealer_code']:'0';
        $this->current_menu = 'Order-details'; 
        if($auth_id != '0' )
        {
            // dd('1');
            $auth_id = $auth_id;

        }
        else {
            # code...
            // dd('11');
            header('Location: https://demo.msell.in/client');
            dd('1');
        }
        // dd($auth_id);   

        // if()
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request)
    {
        // dd('1');
        $details= DB::table('dms_terms_condition')->where('status',1)->first();
        return view('DMS.signup',[
            'details'=>$details,
            'current_menu'=>$this->current_menu

        ]);

    }    
    public function dms_signup(Request $request)
    {
        // dd($request);
       $validate = $request->validate([

            "full_name_of_firm" => 'required|max:200|min:3',
            "address_of_firm" => 'required|max:2000|min:10',
            "firm_type" => 'required',
            "partner_manager_director_name" => 'required|max:200|min:10',
            "res_add" => 'required|max:500|min:10',
            "perm_add" => 'required|max:500|min:10',
            "pan_no" => 'required|max:20|min:10',
            "p_contact_person_name" => 'required|max:50|min:10',
            // "p_phone_no" => 'required|max:20|min:10',
            "p_mobile_no" => 'required|max:200|min:10',
            "p_email" => 'required|max:50|min:10',
            "type_of_product_handled" => 'required|max:600|min:5',
            "tin_no" => 'required|max:30|min:10',
        ]);

        DB::beginTransaction();

       $myArr = [
            'full_name_of_firm' => !empty($request->full_name_of_firm)?$request->full_name_of_firm:' ',
            'address_of_firm' => !empty($request->address_of_firm)?$request->address_of_firm:' ',
            'firm_type' => !empty($request->firm_type)?$request->firm_type:' ',
            'partner_manager_director_name' => !empty($request->partner_manager_director_name)?$request->partner_manager_director_name:' ',
            'res_add' => !empty($request->res_add)?$request->res_add:' ',
            'perm_add' => !empty($request->perm_add)?$request->perm_add:' ',
            'pan_no' => !empty($request->pan_no)?$request->pan_no:' ',
            'p_contact_person_name' => !empty($request->p_contact_person_name)?$request->p_contact_person_name:' ',
            'p_phone_no' => !empty($request->p_phone_no)?$request->p_phone_no:'0',
            'p_mobile_no' => !empty($request->p_mobile_no)?$request->p_mobile_no:' ',
            'p_fax_no' => !empty($request->p_fax_no)?$request->p_fax_no:' ',
            'p_email' => !empty($request->p_email)?$request->p_email:' ',
            'type_of_product_handled' => !empty($request->type_of_product_handled)?$request->type_of_product_handled:' ',
            'dl_no_wholesale' => !empty($request->dl_no_wholesale)?$request->dl_no_wholesale:' ',
            'dl_no_retail' => !empty($request->dl_no_retail)?$request->dl_no_retail:' ',
            'narcotic_no' => !empty($request->narcotic_no)?$request->narcotic_no:' ',
            'tin_no' => !empty($request->tin_no)?$request->tin_no:' ',
            'central_trade_no' => !empty($request->central_trade_no)?$request->central_trade_no:' ',
            'banker_name' => !empty($request->banker_name)?$request->banker_name:' ',
            'amount' => !empty($request->amount)?$request->amount:' ',
            'dd_no' => !empty($request->dd_no)?$request->dd_no:' ',
            'sec_deposit_date' => !empty($request->sec_deposit_date)?$request->sec_deposit_date:' ',
            'bank_name' => !empty($request->bank_name)?$request->bank_name:' ',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->auth_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->auth_id,
            'server_date_time' => date('Y-m-d H:i:s'),
            'status' => 1,
            'dealer_code' => $this->dealer_code,
            'current_year' => date('Y'),
            ];
            // dd($myArr);
            $dms_registration=DB::table('DMS_REGISTRATION')->insert($myArr);
        if ($dms_registration) 
        {
            DB::commit();
            // session_start();
            $_SESSION['iclientdigimetsignup_status'] = '1';

            Session::flash('message', 'Dealer created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }
      
        return redirect()->guest(url($this->current_menu));
        // return redirect()->intended($this->current_menu);
    
    }    
}
