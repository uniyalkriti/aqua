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
use App\EditAppModule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class EditAppModuleController extends Controller
{
    public function __construct()
    {

        $this->current = 'editAppModule';
        $this->module = Lang::get('common.editappmodule');
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
        $query = EditAppModule::join('company','company.id','=','app_module.company_id')->select('company.title as cname','company.base_url as base_url','app_module.*')->where('app_module.status', '!=', 9);


        # table sorting
        $query->orderBy('app_module.created_at','desc');

        if(!empty($filtercompany))
        {
           $query->where('company.id',$filtercompany); 
        }

        $editappmodule = $query->get();

        $company = DB::table('company')->where('status',1)->pluck('name','id');

        return view('editAppModule.index', [
            'editappmodule' => $editappmodule,
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
        $appmodule_data = EditAppModule::where('status',1)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'editAppModule.edit',
            [
                'appmodule_data'=>$appmodule_data,
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
        $appmodule = [
            'title_name' => $request->tname,
        ];

        $appmodule_data= EditAppModule::where('id', $uid)->update($appmodule);

        if (!$appmodule_data) {
            DB::rollback();
        }

        if (isset($appmodule)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('editAppModule');
    }
}
