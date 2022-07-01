<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\Urlassign;
use App\Company;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class UrlAssigningController extends Controller
{
    public function __construct()
    {
        $this->current = 'assign';
        $this->module=Lang::get('common.assign_url');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
       
        // dd($request);
        if(!empty($request->flag) && $request->flag == 1){

        $company1=$request->company; 
        $version_filter_id=$request->version_filter; 
        // dd($version_filter);
        $assign_url = [];
        // dd($company1);
           
        if(Auth::user()->api_token == '5767')
        {
            $user_id = Auth::user()->id;
            $company = Company::where('status',1)->where('created_by',$user_id)->pluck('name','id');
        }
        else
        {
            $company = Company::where('status',1)->pluck('name','id');
        }   

        $version_management = DB::table('version_management')->where('company_id',$company1)->where('id',$version_filter_id)->orderBy('id','DESC')->first();
        $version_filter = DB::table('version_management')->orderBy('id','ASC')->pluck('version_name','id');
        
        

        $query = DB::table('url_list')
        ->where('status', '!=', 9)
        ->orderBy('id','ASC')
        ->get();
        if(!empty($company1))
        {
            $assign_url = DB::table('assign_url_list')
                    ->where('status',1)
                    ->where('company_id',$company1)
                    ->where('v_name',$version_filter_id)
                    ->pluck('url_list_id')->toArray();
        }
        

        // dd($assign_url);
            return view('urlassign.index', [
                'records' => $query,
                'company' => $company,
                'company1' => $company1,
                'version_filter'=>$version_filter,
                'version_management' => $version_management,
                'current_menu' => $this->current,
                'assign_url'=>$assign_url,
                ]);
        }
      

            if(Auth::user()->api_token == '5767')
            {
                $user_id = Auth::user()->id;
                $company = Company::where('status',1)->where('created_by',$user_id)->pluck('name','id');
            }
            else
            {
                $company = Company::where('status',1)->pluck('name','id');
            }   
            $version_filter = DB::table('version_management')->orderBy('id','ASC')->pluck('version_name','id');
            return view('urlassign.index', [
                'company' => $company,
                 'version_filter'=>$version_filter,
            ]);
          
           
      }

    public function addUrl(Request $request)
    {
       $company = $request->company1;
       $version_filter = $request->version_filter;

      
       $module = $request->module; // all checked modules 
        // dd($module);
       $user = Auth::User()->id;

       
         // dd($submodule);
        DB::beginTransaction();
        $delete_url_list = DB::table('assign_url_list')->where('company_id',$company)->where('v_name',$version_filter)->delete();

           foreach($module as $skey => $sval)
               {

                $break = explode('|',$sval);

                     $insert = [
                            'company_id' => $company,
                            'url_list_id' => $break[0],
                            'code' => $break[1],
                            'url_list' => $break[2],
                            'v_name' => $break[3],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $user,
                            'status' => 1
                        ];

                        $app_module = Urlassign::create($insert);
               }


             if($app_module)
             {
                DB::commit();
               Session::flash('message', "Url Assign successfully");
               Session::flash('alert-class', 'alert-success');
               return redirect('urlassign');
             }
             else
             {
                   DB::rollback();
                   Session::flash('message', "Url Not Assign");
                   Session::flash('alert-class', 'alert-danger');
                   return redirect('urlassign');
             }
 
    }
   

   

}
