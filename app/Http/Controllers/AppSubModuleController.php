<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\Appsubmodule;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AppSubModuleController extends Controller
{
    public function __construct()
    {
        $this->current = 'appSubModule';
        $this->module=Lang::get('common.app_sub_module');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
      

       
        $query = DB::table('master_list_sub_module')
                ->join('master_list_module','master_list_module.id','=','master_list_sub_module.module_id')
                ->select('master_list_sub_module.id','master_list_sub_module.module_id','master_list_sub_module.title_name','master_list_sub_module.status','master_list_module.name as mname','master_list_sub_module.image_name as image_name')
                ->where('master_list_sub_module.status', '!=', 9);
                
        # table sorting
        $query->orderBy('id','desc');

        $app_sub_module = $query->get();
        // dd($app_sub_module);
        return view('appSubModule.index', [
            'app_sub_module' => $app_sub_module,
            'current_menu' => $this->current
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $app_module = DB::table('master_list_module')->get();
        return view('appSubModule.create',[
            'app_module'=> $app_module,
            'current_menu' => $this->current
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // save in location2
        $validatedData = $request->validate([
            'apm' => 'required',
            'appSubModule' => 'required|max:50',
            'status' => 'required',
        ]);

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $Imagename = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    // $personImage = Appmodule::where('id',$app_module->id)->update(['icon_image' => 'app_icon_image/'.$name]);

                    $request->file('imageFile')->move("app_icon_image", $Imagename);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }
        $app_sub_module = DB::table('master_list_sub_module')->insert([
            'module_id' => trim($request->apm),
            'title_name' => trim($request->appSubModule),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_name'=> 'app_icon_image/'.$Imagename,
            'status' => trim($request->status)
        ]);


        if (!$app_sub_module) {
            DB::rollback();
        }
        if ($app_sub_module) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('appSubModule');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $encrypt_id = Crypt::decryptString($id);
        $appSubModule_info = DB::table('master_list_module')->pluck('name', 'id');
        $appSubModule_data = Appsubmodule::findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'appSubModule.edit',
            [
                'appSubModule_info'=>$appSubModule_info,
                'appSubModule_data'=>$appSubModule_data,
                'encrypt_id' => $id,
                'current_menu' => $this->current
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'apm' => 'required',
            'appSubModule' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
       
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $app_sub_module_type= [
            'module_id' => trim($request->apm),
            'title_name' => trim($request->appSubModule),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => trim($request->status)
        ];


        $app_sub_module_data= Appsubmodule::where('id', $uid)->update($app_sub_module_type);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $Imagename = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = Appsubmodule::where('id',$uid)->update(['image_name'=> 'app_icon_image/'.$Imagename,'updated_at'=>date('Y-m-d H:i:s')]);

                    $request->file('imageFile')->move("app_icon_image", $Imagename);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }

        if (!$app_sub_module_data) {
            DB::rollback();
        }

        if ($app_sub_module_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('appSubModule ');
    }
}
