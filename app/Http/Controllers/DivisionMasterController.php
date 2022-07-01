<?php

namespace App\Http\Controllers;

use App\Dealer;
use App\DealerLocation;
use App\DealerPersonLogin;
use App\Location1; 
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\SS;
use DB;
use Auth;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class DivisionMasterController extends Controller
{
    public function __construct()
    {
        $this->current_menu='division_master';

        $this->active_status_table='division_master';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    
        $company_id = Auth::user()->company_id;
        $pagination = !empty($request->perpage) ? $request->perpage : 10;

        $q=DB::table('division_master')->where('company_id',$company_id)->where('active_status','!=',9);

        # search functionality
        // if (!empty($request->search)) {
        //     $key = $request->search;
        //     $q->where(function ($subq) use ($key) {
        //         $subq->Where('csa.csa_name', 'LIKE',  '%'.$key.'%');
        //     });
        // }

        // $data = $q->select('csa.*','csa.c_id AS id',DB::raw("DATE_FORMAT(created_date_time,'%d-%m-%Y') AS created_on"))
        //     ->where('company_id',$company_id)
        //     ->orderBy('csa.c_id', 'desc')
        //     ->get();

        $data = $q->get();


        return view($this->current_menu.'.index', [
            'records' => $data,
            'active_status_table' => $this->active_status_table,
            'current_menu'=>$this->current_menu
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #Location 1 data
        $company_id = Auth::user()->company_id;
        // $location3 = DB::table('division_master')->where('company_id',$company_id)->where('active_status', '=', '1')->pluck('name', 'id');


        return view($this->current_menu.'.create',[
            'current_menu'=>$this->current_menu,
            // 'location3' => $location3,
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
        $validate = $request->validate([
            'division_code' => 'required',
            'division_name' => 'required',
            'location' => 'required',
            'operator_name' => 'required',
            'active_status' => 'required',
            'sequence' => 'required'           
        ]);

        // $beat=$request->location_4;
        $company_id = Auth::user()->company_id;

        /* Start Transaction*/

        $transaction = DB::table('division_master')->insert([
                            'company_id' => $company_id,
                            'division_code' => trim($request->division_code),
                            'division_name' => trim($request->division_name),
                            'location' => trim($request->location),
                            'operator_name' => trim($request->operator_name),
                            'active_status' => trim($request->active_status),
                            'sequence' => trim($request->sequence) ,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->id,
                        ]);

        if ($transaction) {
            Session::flash('message', Lang::get('Division created successfully'));
            Session::flash('class', 'success');
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        #decrypt id
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        
        $ss=DB::table('division_master')->where('company_id',$company_id)->where('id',$uid)->first();
        
        return view($this->current_menu.'.edit',[
            'current_menu'=>$this->current_menu,
            'ss' => $ss,
            'encrypt_id'=>$id,
        ]);
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
        #decrypt id
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;

        $validate = $request->validate([
            'division_code' => 'required',
            'division_name' => 'required',
            'location' => 'required',
            'operator_name' => 'required',
            'active_status' => 'required',
            'sequence' => 'required'           
        ]);

        /* Start Transaction*/
        
        $update = DB::table('division_master')->where('id', $uid)->update([
                        'company_id' => $company_id,
                        'division_code' => trim($request->division_code),
                        'division_name' => trim($request->division_name),
                        'location' => trim($request->location),
                        'operator_name' => trim($request->operator_name),
                        'active_status' => trim($request->active_status),
                        'sequence' => trim($request->sequence) ,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                    ]);

        if ($update) {
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);
    }

}
