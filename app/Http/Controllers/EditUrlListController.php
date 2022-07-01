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
use App\EditUrlList;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class EditUrlListController extends Controller
{
    public function __construct()
    {

        $this->current = 'editUrlList';
        $this->module = Lang::get('common.editurllist');
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
        $query = EditUrlList::join('company','company.id','=','assign_url_list.company_id')->join('version_management','version_management.id','=','assign_url_list.v_name')->select('version_management.version_name','company.title as cname','company.base_url as base_url','assign_url_list.*')->where('assign_url_list.status', '!=', 9);


        # table sorting
        $query->orderBy('assign_url_list.created_at','desc');

        if(!empty($filtercompany))
        {
           $query->where('company.id',$filtercompany); 
        }

        $editurllist = $query->get();

        $company = DB::table('company')->where('status',1)->pluck('name','id');

        return view('editUrlList.index', [
            'editurllist' => $editurllist,
            'company' => $company,
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

        $url_list = DB::table('url_list')->where('status',1)->get();

      
        return view('editUrlList.create', [
            'company_id' => $company_id,
            'url_list' => $url_list,
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

        $user = Auth::user()->id;

        $validatedData = $request->validate([
            'company' => 'required',
            'version' => 'required',
            'url' => 'required',
            'acode' => 'required',
            'aurl' => 'required',
        ]);

        $company_id = $request->company;

        DB::beginTransaction();
        /**
         * @des: save data in Location5 table
         */
        $url = EditUrlList::create([
            'company_id' => $company_id,
            'url_list_id' => $request->url,
            'code' => $request->acode,
            'url_list' => $request->aurl,
            'v_name' => $request->version,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $user,
            

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

        return redirect('editUrlList');

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
      
        $assignurl_data = EditUrlList::where('status',1)->findOrFail($encrypt_id);

        $version = DB::table('version_management')->where('company_id',$assignurl_data->company_id)->pluck('version_name','id');
       // dd($assignurl_data->company_id);
        return view(
            'editUrlList.edit',
            [
                'assignurl_data'=>$assignurl_data,
                'encrypt_id' => $id,
                'company' => $company,
                'version' => $version,
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
            'code' => 'required',
            'urllist' => 'required',
            'version' => 'required',
        ]);


        $uid = Crypt::decryptString($id);

        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $urllist = [
            'code' => $request->code,
            'url_list' => $request->urllist,
            'v_name' => $request->version,
        ];

        $urllist_data= EditUrlList::where('id', $uid)->update($urllist);

        if (!$urllist_data) {
            DB::rollback();
        }

        if (isset($urllist)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('editUrlList');
    }

      //#............................................................................. 
      public function get_version_by_company(Request $request)
      {
          $id = $request->id;
          $company_id = explode(',',$id);
          $version_name=DB::table('version_management')
          ->where('company_id',$company_id)
          ->pluck('version_name','id');
          return json_encode($version_name);
  
      }
      //#............................................................................. 




}
