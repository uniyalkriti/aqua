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

class SsController extends Controller
{
    public function __construct()
    {
        $this->current_menu='csa';

        $this->status_table='csa';
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

        $q=SS::where('company_id',$company_id)->where('csa.active_status','!=',9);

        # search functionality
        if (!empty($request->search)) {
            $key = $request->search;
            $q->where(function ($subq) use ($key) {
                $subq->Where('csa.csa_name', 'LIKE',  '%'.$key.'%');
            });
        }

        $data = $q->select('csa.*','csa.c_id AS id',DB::raw("DATE_FORMAT(created_date_time,'%d-%m-%Y') AS created_on"))
            ->where('company_id',$company_id)
            ->orderBy('csa.c_id', 'desc')
            ->get();


        return view($this->current_menu.'.index', [
            'records' => $data,
            'status_table' => $this->status_table,
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
        $location3 = Location3::where('company_id',$company_id)->where('status', '=', '1')->pluck('name', 'id');

        $division = DB::table('division_master')->where('company_id',$company_id)->where('active_status', '=', '1')->pluck('division_name', 'id');



        return view($this->current_menu.'.create',[
            'current_menu'=>$this->current_menu,
            'location3' => $location3,
            'division' => $division,
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

        $validate = $request->validate([
            'location_4' => 'required',
            'location_3' => 'required',
            'ss_name' => 'required',
            'contact_person' => 'required|min:2|max:30',
            'address' => 'required|min:2|max:200',
            'mobile' => 'required|min:10|max:10',
            'email' => 'required|max:50',
            'tin_no' => 'max:20',
           
        ]);

        $beat=$request->location_4;
        $company_id = Auth::user()->company_id;

        /* Start Transaction*/
        DB::beginTransaction();
        $myArr = [
        	'state_id' => trim($request->location_3),
        	'active_status' => trim($request->status),
            'town' => trim($request->location_4),
            'csa_name' => trim($request->ss_name),
            'csa_code' => trim($request->ss_code),
            'adress' => trim($request->address),
            'mobile' => trim($request->mobile),
            'email' =>trim($request->email),
            'gst_no' =>trim($request->tin_no),
            'division_id' =>trim($request->division),
            'company_id' => $company_id,
            'contact_person' => trim($request->contact_person),
            'created_date_time' => date('Y-m-d H:i:s')
        ];


        $ss=SS::create($myArr);


        if ($ss) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
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
        #decrypt id
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;

        $q=Person::join('person_details','person_details.person_id','=','person.id','inner')
            ->join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as full_name"),'person.*','person_login.person_username','person_login.person_password','_role.rolename')
            ->where('company_id',$company_id)
            ->where('person.id',$uid)->first();


        return view($this->current_menu.'.view',[
            'user'=>$q,
            'id'=>$id
        ]);
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
        $location1=[];
        $location2=[];
        $location3=[];
        $location4=[];

        $ss=SS::select('*')->where('company_id',$company_id)->where('c_id',$uid)->first();
        // dd($ss);
        $location=[];
        if (!empty($ss->town))
        {
            $location=DB::table('location_3')
                ->where('id',$ss->state_id)
                ->where('company_id',$company_id)
                ->first();
        }
    //    dd($location);
       #Location 1 data
        $location3 = Location3::where('company_id',$company_id)->where('status', '=', '1')->pluck('name', 'id');

        $division = DB::table('division_master')->where('company_id',$company_id)->where('active_status', '=', '1')->pluck('division_name', 'id');




        return view($this->current_menu.'.edit',[
            'current_menu'=>$this->current_menu,
            'ss' => $ss,
            'location' => $location,
            'encrypt_id'=>$id,
            'location3'=>$location3,
            'division'=>$division,


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
            'location_4' => 'required',
            'location_3' => 'required',
            'ss_name' => 'required|min:2|max:150',
            'address' => 'required|min:2|max:200',
            'mobile' => 'required|min:10|max:10',
            'email' => 'required|max:50',
            'tin_no' => 'max:20',

        ]);

        $beat=$request->location_4;
        /* Start Transaction*/
        DB::beginTransaction();
        $myArr = [
        	'state_id' => trim($request->location_3),
        	'active_status' => trim($request->status),
            'town' => trim($request->location_4),
            'csa_name' => trim($request->ss_name),
            'csa_code' => trim($request->ss_code),
            'adress' => trim($request->address),
            'mobile' => trim($request->mobile),
            'email' =>trim($request->email),
            'contact_person' =>trim($request->contact_person),
            'gst_no' =>trim($request->tin_no),
            'division_id' =>trim($request->division),
            'updated_at' => date('Y-m-d H:i:s'),
            'active_status'=> trim($request->status)
        ];

        $ss=SS::select('*')->where('company_id',$company_id)->where('c_id',$uid)->first();
        $update=$ss->where('c_id',$uid)->update($myArr);


        if ($update) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);
    }

}
