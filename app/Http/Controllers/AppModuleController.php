<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\Appmodule;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AppModuleController extends Controller
{
    public function __construct()
    {
        $this->current = 'appModule';
        $this->module=Lang::get('common.app_module');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
      

       
        $query = DB::table('master_list_module')
                ->select('id','name','title_name','status','icon_image')
                ->where('status', '!=', 9);
                
        # table sorting
        $query->orderBy('id','desc');

        $app_module = $query->get();
        // dd($app_module);
        return view('appModule.index', [
            'app_module' => $app_module,
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
        return view('appModule.create',[
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
       
        $validatedData = $request->validate([
            'appModule' => 'required|max:50',
            'apptitle' => 'required|max:50',
            'status' => 'required',
        ]);

     
        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $app_module = Appmodule::create([
            'name' => trim($request->appModule),
            'title_name' => trim($request->apptitle),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => trim($request->status)
        ]);
        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = Appmodule::where('id',$app_module->id)->update(['icon_image' => 'app_icon_image/'.$name]);

                    $request->file('imageFile')->move("app_icon_image", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }

        if (!$app_module) {
            DB::rollback();
        }
        if ($app_module) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('appModule');
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
        $appModule_info = DB::table('master_list_module')->pluck('name', 'id');
        $appModule_data = Appmodule::findOrFail($encrypt_id);
       
        return view(
            'appModule.edit',
            [
                'appModule_info'=>$appModule_info,
                'appModule_data'=>$appModule_data,
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
            'appModule' => 'required|max:50',
            'apptitle' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $app_module= [
            'name' => trim($request->appModule),
            'title_name' => trim($request->apptitle),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => trim($request->status)
        ];


        $app_module_data= Appmodule::where('id', $uid)->update($app_module);
        // // dd($request);
        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = DB::table('master_list_module')->where('id',$uid)->update(['icon_image' => 'app_icon_image/'.$name]);

                    $request->file('imageFile')->move("app_icon_image", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }
        // if ($request->hasFile('imageFile')) 
        // {
                
        //         $image = $request->file('imageFile');
        //         $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        //         $random_no = substr($str, 0,2);  // return always a new string
        //         $custom_image_name = 'app_icon_image/'.date('YmdHis').$random_no;
        //         $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
        //         $destinationPath = public_path('/app_icon_image/' . $imageName);


        //         Image::make($image)->save($destinationPath);
        // }

        // $update_attendance_arr = [
        //         'icon_image' => $imageName,
        //         // 'updated_at's => date('Y-m-d H:i:s'),    
        //     ];
        // $update_image_attendance = DB::table('master_list_module')
        //                         ->where('id',$uid)
        //                         // ->where('company_id',$company_id)
        //                         ->update($update_attendance_arr);
        if (!$app_module_data) {
            DB::rollback();
        }

        if ($app_module_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('appModule ');
    }
}
