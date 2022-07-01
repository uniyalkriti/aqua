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
use App\UrlList;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class UrlListController extends Controller
{
    public function __construct()
    {

        $this->current = 'urlList';
        $this->module = Lang::get('common.urllist');
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
        $query = UrlList::select('url_list.*')->where('url_list.status', '!=', 9);

        # table sorting
        $query->orderBy('url_list.created_at','desc');

        $urllist = $query->get();

        return view('urlList.index', [
            'urllist' => $urllist,
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
      
        return view('urlList.create', [
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

        $validatedData = $request->validate([
            'Code' => 'required',
            'urllist' => 'required',
            //'version' => 'required',
        ]);

       // $company_id = $request->company;
         $user_id = Auth::user()->id;

        DB::beginTransaction();
        /**
         * @des: save data in Location5 table
         */
        $urllist = UrlList::create([
           // 'company_id' => $company_id,
            'code' => $request->Code,
            'url_list' => $request->urllist,
           // 'v_name' => $request->version,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $user_id,
            

        ]);

        if (!$urllist) {
            DB::rollback();
        }
        if ($urllist) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('urllist');

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
        $urllist_data = UrlList::where('status',1)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'urlList.edit',
            [
                'urllist_data'=>$urllist_data,
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
            'Code' => 'required',
            'urllist' => 'required',
           // 'version' => 'required',
        ]);


        $uid = Crypt::decryptString($id);

        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $urllist = [
           // 'company_id' => $request->company,
            'code' => $request->Code,
            'url_list' => $request->urllist,
           // 'v_name' => $request->version,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $urllist_data= UrlList::where('id', $uid)->update($urllist);

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

        return redirect('urllist');
    }
}
