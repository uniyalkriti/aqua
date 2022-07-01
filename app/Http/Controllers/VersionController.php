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
use App\Version;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function __construct()
    {

        $this->current = 'version';
        $this->module = Lang::get('common.version');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;

       
        #version data
        $query = Version::join('company','company.id','=','version_management.company_id')->select('company.title as cname','company.base_url as base_url','version_management.*')->where('version_management.status', '!=', 9);

        # table sorting
        $query->orderBy('version_management.created_at','desc');

        $version = $query->get();

        return view('version.index', [
            'version' => $version,
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
        $company_id = Company::where('status',1)->get();
      
        return view('version.create', [
            'company_id' => $company_id,
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

// dd($request);
        $validatedData = $request->validate([
            'company' => 'required',
            'vname' => 'required',
            'vcode' => 'required',
        ]);

        $company_id = $request->company;

        DB::beginTransaction();
        /**
         * @des: save data in Location5 table
         */
        $version = Version::create([
            'company_id' => $company_id,
            'version_name' => $request->vname,
            'version_code' => $request->vcode,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);

        if (!$version) {
            DB::rollback();
        }
        if ($version) {
            DB::commit();
            Session::flash('message', "version created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('version');

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
        $company = Company::where('status',1)->pluck('name','id');
        $version_data = Version::where('status',1)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'version.edit',
            [
                'version_data'=>$version_data,
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
            'company' => 'required',
            'vname' => 'required',
            'vcode' => 'required',
        ]);


        $uid = Crypt::decryptString($id);

        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $version = [
            'company_id' => $request->company,
            'version_name' => $request->vname,
            'version_code' => $request->vcode,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $version_data= Version::where('id', $uid)->update($version);

        if (!$version_data) {
            DB::rollback();
        }

        if (isset($version)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('version');
    }
}
