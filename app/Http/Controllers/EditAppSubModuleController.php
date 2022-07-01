<?php

namespace App\Http\Controllers;

use App\_module;
use DB;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Company;
use App\EditAppSubModule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class EditAppSubModuleController extends Controller
{
    public function __construct()
    {

        $this->current = 'editAppSubModule';
        $this->module = Lang::get('common.editappsubmodule');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;

        $filtercompany = $request->company;

       
        #url data
        $query = EditAppSubModule::join('company','company.id','=','_sub_modules.company_id')->select('company.title as cname','company.base_url as base_url','_sub_modules.*')->where('_sub_modules.status', '!=', 9);


        # table sorting
        $query->orderBy('_sub_modules.created_at','desc');

        if(!empty($filtercompany))
        {
           $query->where('company.id',$filtercompany); 
        }

        $editappsubmodule = $query->get();

        $company = DB::table('company')->where('status',1)->pluck('name','id');

        return view('editAppSubModule.index', [
            'editappsubmodule' => $editappsubmodule,
            'company' => $company,
            'current_menu' => $this->current
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     $company_id = Company::where('status',1)->get();
      
    //     return view('url.create', [
    //         'company_id' => $company_id,
    //         'current_menu' => $this->current
    //     ]);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {

    //     $validatedData = $request->validate([
    //         'company' => 'required',
    //         'signin' => 'required',
    //         'sync' => 'required',
    //         'version' => 'required',
    //     ]);

    //     $company_id = $request->company;

    //     DB::beginTransaction();
    //     /**
    //      * @des: save data in Location5 table
    //      */
    //     $url = Url::create([
    //         'company_id' => $company_id,
    //         'signin_url' => $request->signin,
    //         'sync_post_url' => $request->sync,
    //         'version_code' => $request->version,
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'updated_at' => date('Y-m-d H:i:s'),
            

    //     ]);

    //     if (!$url) {
    //         DB::rollback();
    //     }
    //     if ($url) {
    //         DB::commit();
    //         Session::flash('message', "$this->module created successfully");
    //         Session::flash('alert-class', 'alert-success');
    //     } else {
    //         DB::rollback();
    //         Session::flash('message', 'Something went wrong!');
    //         Session::flash('alert-class', 'alert-danger');
    //     }

    //     return redirect('url');

    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $encrypt_id = Crypt::decryptString($id);
        $company = Company::where('status',1)->pluck('name','id');
        $appsubmodule_data = EditAppSubModule::where('status',1)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'editAppSubModule.edit',
            [
                'appsubmodule_data'=>$appsubmodule_data,
                'encrypt_id' => $id,
                'company' => $company,
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
            'tname' => 'required',
        ]);


        $uid = Crypt::decryptString($id);

        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $appsubmodule = [
            'sub_module_name' => $request->tname,
        ];

        $appsubmodule_data= EditAppSubModule::where('id', $uid)->update($appsubmodule);

        if (!$appsubmodule_data) {
            DB::rollback();
        }

        if (isset($appsubmodule)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('editAppSubModule');
    }
}
