<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\AppModule;
use App\Company;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AssigningController extends Controller
{
    public function __construct()
    {
        $this->current = 'assign';
        $this->module=Lang::get('common.assign_module');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
       

        if(!empty($request->flag) && $request->flag == 1){

        $app_module_check = array();
        $_sub_modules_check = array();
        $company1=$request->company;    
        if(Auth::user()->api_token == '5767')
        {
            $user_id = Auth::user()->id;
            $company = Company::where('status',1)->where('created_by',$user_id)->pluck('name','id');
        }
        else
        {
            $company = Company::where('status',1)->pluck('name','id');
        }   

        $query = DB::table('master_list_module')
        ->select('id','name','icon_image','url_status','module_sequence','url','created_at','updated_at','status')
        ->where('status', '!=', 9)
        ->orderBy('id','ASC')
        ->get();
        

            $arr=[];
            
            foreach ($query as $k=>$a)
            {
                $arr[]=$a->id;
            }
            $mlsm=[];
            $arr2=[];

            if (!empty($arr))
            {
                $mlsm=DB::table('master_list_sub_module')->join('master_list_module','master_list_module.id','=','master_list_sub_module.module_id')
                    ->whereIn('module_id',$arr)
                    ->select('master_list_sub_module.*')
                    ->groupBy('master_list_sub_module.id')->get();
            }
            if(!empty($company1))
            {
                $app_module_check = DB::table('app_module')
                                    ->where('status',1)
                                    ->where('company_id',$company1)
                                    ->pluck('module_id')->toArray();

                $bottom_module_check = DB::table('app_module')
                                    ->where('status',1)
                                    ->where('company_id',$company1)
                                    ->pluck('app_view_status','module_id')->toArray();


                $center_module_check = DB::table('app_module')
                                    ->where('status',1)
                                    ->where('company_id',$company1)
                                    ->pluck('center_app_view_status','module_id')->toArray();

                $left_module_check = DB::table('app_module')
                                    ->where('status',1)
                                    ->where('company_id',$company1)
                                    ->pluck('left_app_view_status','module_id')->toArray();


                $_sub_modules_check = DB::table('_sub_modules')
                                    ->where('status',1)
                                    ->where('company_id',$company1)
                                    ->pluck('sub_module_id')->toArray();
            }

            if (!empty($mlsm))
            {
                foreach ($mlsm as $b=>$c)
                {
                    $arr2[$c->module_id][]=$c;
                }
            }

            return view('assign.index', [
                'records' => $query,
                'arr2' => $arr2,
                'company' => $company,
                'company1' => $company1,
                'app_module_check' => $app_module_check,

                'bottom_module_check' => $bottom_module_check,
                'center_module_check' => $center_module_check,
                'left_module_check' => $left_module_check,


                '_sub_modules_check' => $_sub_modules_check,
                'current_menu' => $this->current
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

            return view('assign.index', [
                'company' => $company,
            ]);

           
      }

      public function addModules(Request $request)
    {
       $company = $request->company1;
       // dd('1');
      
       $module = $request->module; // all checked modules 

       $bottomView = $request->bottomView;
       $centerView = $request->centerView;
       $leftView = $request->leftView;


        $submodule = $request->submodule;  //all checked submodules

        $sequence_array = array_filter($request->sequence,'strlen');

        $final_sequence_array = array_values($sequence_array);
       
        // dd($bottomView);
         DB::beginTransaction();
       $delete_app_module = DB::table('app_module')->where('company_id',$company)->delete();

       $delete_sub_module = DB::table('_sub_modules')->where('company_id',$company)->delete();


           foreach($module as $skey => $sval)
               {

                $break = explode('|',$sval);
                // dd($bottomView[$skey]);
                     $insert = [
                            'company_id' => $company,
                            'module_id' => $break[0],
                            'title_name' => $break[1],
                            'module_sequence' => !empty($final_sequence_array[$skey])?$final_sequence_array[$skey]:'0',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'app_view_status' => !empty($bottomView[$break[0]])?'1':'0',
                            'center_app_view_status' => !empty($centerView[$break[0]])?'1':'0',
                            'left_app_view_status' => !empty($leftView[$break[0]])?'1':'0',
                            'status' => 1
                        ];

                        $app_module = AppModule::create($insert);
               }


            foreach($submodule as $smkey => $smvalue)
              {
                   $break_sub_module = explode('|',$smvalue);
                   // dd($break_sub_module);
                  $_sub_module = DB::table('_sub_modules')->insert([
                    'company_id' => $company,
                    'sub_module_id' => $break_sub_module[0],
                    'sub_module_name' => $break_sub_module[1],
                    'path' => "",
                    'image_name' => "",
                    'module_sequence' => $smkey+1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ]);

             }

             if($_sub_module)
             {
                DB::commit();
               Session::flash('message', "Module Assign successfully");
               Session::flash('alert-class', 'alert-success');
               return redirect('Modules');
             }
             else
             {
                   DB::rollback();
                   Session::flash('message', "Module Not Assign");
                   Session::flash('alert-class', 'alert-danger');
                   return redirect('Modules');
             }
 
    }
   

   

}
