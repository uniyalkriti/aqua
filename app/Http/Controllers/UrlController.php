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
use App\Url;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function __construct()
    {

        $this->current = 'url';
        $this->module = Lang::get('common.interface');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;

       
        #url data
        $query = Url::join('company','company.id','=','interface_url.company_id')->select('company.title as cname','company.base_url as base_url','interface_url.*')->where('interface_url.status', '!=', 9);

        # table sorting
        $query->orderBy('interface_url.created_at','desc');

        $url = $query->get();

        return view('url.index', [
            'url' => $url,
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
      
        return view('url.create', [
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
            'signin' => 'required',
            'sync' => 'required',
            'version' => 'required',
        ]);

        $company_id = $request->company;

        DB::beginTransaction();
        /**
         * @des: save data in Location5 table
         */
        $delete_query = Url::where('version_code',$request->version)->where('company_id',$company_id)->delete();
        
        $url = Url::create([
            'company_id' => $company_id,
            'signin_url' => $request->signin,
            'sync_post_url' => $request->sync,
            'image_url'=>$request->image_url,
            'version_code' => $request->version,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            

        ]);

        if (!$url) {
            DB::rollback();
        }
        if ($url) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('url');

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
        $url_data = Url::where('status',1)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'url.edit',
            [
                'url_data'=>$url_data,
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
            'signin' => 'required',
            'sync' => 'required',
            'version' => 'required',
        ]);


        $uid = Crypt::decryptString($id);

        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $url = [
            'company_id' => $request->company,
            'signin_url' => $request->signin,
            'sync_post_url' => $request->sync,
            'image_url' => $request->image_url,
            'version_code' => $request->version,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $url_data= Url::where('id', $uid)->update($url);

        if (!$url_data) {
            DB::rollback();
        }

        if (isset($url)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('url');
    }
}
