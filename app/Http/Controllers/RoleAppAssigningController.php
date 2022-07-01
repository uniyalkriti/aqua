<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\AppModule;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class RoleAppAssigningController extends Controller
{
    public function __construct()
    {
        $this->current = 'roleAppAssign';
        $this->module=Lang::get('common.assign_module');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
       

        if(!empty($request->flag) && $request->flag == 1){
        $role_id=$request->role;    
        $check_app_module = array();
        $checksub_module = array();
        $role = DB::table('_role')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->where('rolename','!=','Super Admin')
        ->pluck('rolename','role_id');

        $query = DB::table('master_list_module')
        ->join('app_module','app_module.module_id','=','master_list_module.id')
        ->select('master_list_module.id as id','master_list_module.name as name','master_list_module.icon_image','master_list_module.url_status','master_list_module.module_sequence','master_list_module.url','master_list_module.created_at','master_list_module.updated_at','master_list_module.status')
        ->where('master_list_module.status', '=', 1)
        ->where('app_module.company_id',$company_id)
        ->orderBy('master_list_module.id','ASC')
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
                $mlsm=DB::table('master_list_sub_module')
                    ->join('master_list_module','master_list_module.id','=','master_list_sub_module.module_id')
                    ->join('_sub_modules','_sub_modules.sub_module_id','=','master_list_sub_module.id')
                    ->whereIn('master_list_sub_module.module_id',$arr)
                    ->where('_sub_modules.company_id',$company_id)
                    ->select('master_list_sub_module.*')
                    ->groupBy('master_list_sub_module.id')->get();
            }
            if(!empty($role_id))
            {
                $check_app_module = DB::table('role_app_module')->where('role_id',$role_id)->where('status',1)->pluck('module_id')->toArray();

                    $bottom_module_check = DB::table('role_app_module')
                                    ->where('status',1)
                                    ->where('role_id',$role_id)
                                    ->pluck('app_view_status','module_id')->toArray();


                $center_module_check = DB::table('role_app_module')
                                    ->where('status',1)
                                    ->where('role_id',$role_id)
                                    ->pluck('center_app_view_status','module_id')->toArray();

                $left_module_check = DB::table('role_app_module')
                                    ->where('status',1)
                                    ->where('role_id',$role_id)
                                    ->pluck('left_app_view_status','module_id')->toArray();

                $checksub_module = DB::table('role_sub_modules')->where('role_id',$role_id)->where('status',1)->pluck('sub_module_id')->toArray();
            }

            if (!empty($mlsm))
            {
                foreach ($mlsm as $b=>$c)
                {
                    $arr2[$c->module_id][]=$c;
                }
            }

            return view('assign.role', [
                'records' => $query,
                'arr2' => $arr2,
                'role' => $role,
                'role_id' => $role_id,
                'check_app_module' => $check_app_module,
                'checksub_module' => $checksub_module,
                'current_menu' => $this->current,


                'bottom_module_check' => $bottom_module_check,
                'center_module_check' => $center_module_check,
                'left_module_check' => $left_module_check,
                ]);
        }

            $role = DB::table('_role')
            ->where('company_id',$company_id)
            ->where('rolename','!=','Super Admin')
            ->where('status',1)
            ->pluck('rolename','role_id');

            return view('assign.role', [
                'role' => $role,
            ]);

           
      }

    public function RoleAddModules(Request $request)
    {
       $role_id = $request->role_id;

      
       $module = $request->module; // all checked modules 

        $bottomView = $request->bottomView;
       $centerView = $request->centerView;
       $leftView = $request->leftView;


        $submodule = $request->submodule;  //all checked submodules

        $sequence_array = array_filter($request->sequence,'strlen');

        $final_sequence_array = array_values($sequence_array);


        $company_id = Auth::user()->company_id;
         // dd($submodule);
        DB::beginTransaction();
        $delete_app_module = DB::table('role_app_module')->where('role_id',$role_id)->delete();

        $delete_sub_module = DB::table('role_sub_modules')->where('role_id',$role_id)->delete();


           foreach($module as $skey => $sval)
               {

                $break = explode('|',$sval);

                     $insert = [
                            'company_id' => $company_id,
                            'role_id'=> $role_id,
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

                        $roleapp_module = DB::table('role_app_module')->insert($insert);
               }

           if(!empty($submodule))
           {
                foreach($submodule as $smkey => $smvalue)
                {
                   $break_sub_module = explode('|',$smvalue);

                    $role_sub_module = DB::table('role_sub_modules')->insert([
                        'company_id' => $company_id,
                        'role_id'=> $role_id,
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
                if($role_sub_module)
                {
                    DB::commit();
                    Session::flash('message', "Module Assign successfully");
                    Session::flash('alert-class', 'alert-success');
                    return redirect('roleAppAssign');
                }
                else
                {
                    DB::rollback();
                    Session::flash('message', "Module Not Assign");
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('roleAppAssign');
                }
           }
            

            if($roleapp_module)
            {
                DB::commit();
                Session::flash('message', "Module Assign successfully");
                Session::flash('alert-class', 'alert-success');
                return redirect('roleAppAssign');
            }
            else
            {
                DB::rollback();
                Session::flash('message', "Module Not Assign");
                Session::flash('alert-class', 'alert-danger');
                return redirect('roleAppAssign');
            }
 
    }
   

   

}
