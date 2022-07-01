<?php

namespace App\Http\Controllers;

use App\User;
use App\GiftMaster;
use App\UserDetail;
use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LeaveRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Lang;
use Session;
use Crypt;
class GiftControllerMaster extends Controller
{

	public function __construct()
    {
        $this->current = 'Gift-Master';
        $this->module=Lang::get('common.vehicle_details');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $company_id = Auth::user()->company_id;

       
        $query = GiftMaster::join('person','person.id','=','gift_master_logs.user_id')
    			->join('person_details','person_details.person_id','=','person.id','inner')
                ->join('person_login','person_login.person_id','=','person.id','inner')
                ->join('users','users.id','=','person.id')
                ->join('_role','_role.role_id','=','person.role_id','inner')
                ->select('gift_master_logs.gift_image','gift_master_logs.id','gift_title as name','user_id','gift_master_logs.sequence','gift_master_logs.status',DB::raw("(CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'rolename','person.mobile as mobile')
                ->where('gift_master_logs.status', '!=', 9)
                ->where('gift_master_logs.company_id',$company_id);



        # table sorting
        $query->orderBy('person.id','desc');

        $working_type = $query->get();
        // dd($working_type);
        return view('gift_master.index', [
            'working_type' => $working_type,
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
        $company_id = Auth::user()->company_id;
        $user_details = UserDetail::user_details_fetch($company_id);
        $working_type = GiftMaster::where('company_id',$company_id)->orderBy('sequence','desc')->first();
        // dd($working_type);
        return view('gift_master.create',[
            'working_type'=> $working_type,
            'user_details'=>$user_details,
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
            'workType' => 'required|max:50',
            'status' => 'required',
            'sequence' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

		$auth_id = Auth::user()->id;
        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $work_type = GiftMaster::create([
            'gift_title' => trim(strtoupper($request->workType)),
            'status' => trim($request->status),
            'company_id'=>$company_id,
            'user_id' => $request->user_id,
            'created_by' => $auth_id,
            'created_at' => date('Y-m-d H:i:s'),
            
        ]);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = GiftMaster::where('id',$work_type->id)->update(['gift_image' => 'gift_images/'.$name]);

                    $request->file('imageFile')->move("circular_image", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }

        if (!$work_type) {
            DB::rollback();
        }
        if ($work_type) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('Gift-Master');
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
        $company_id = Auth::user()->company_id;
        $user_details = UserDetail::user_details_fetch($company_id);
        $workType_info = GiftMaster::where('company_id',$company_id)->pluck('gift_title as name', 'id');
        $workType_data = GiftMaster::where('company_id',$company_id)->where('id',$encrypt_id)->first();
        // dd($workType_data);
        return view('gift_master.edit',
            [
                'workType_info'=>$workType_info,
                'workType_data'=>$workType_data,
                'encrypt_id' => $id,
                'user_details'=>$user_details,
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
            'workType' => 'required|max:50',
            'status' => 'required',
            'sequence'=>'required'
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $work_type= [
            'gift_title' => trim(strtoupper($request->workType)),
            'status' => trim($request->status),
            'user_id' => $request->user_id,
           
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];


        $work_type_data= GiftMaster::where('id', $uid)->where('company_id',$company_id)->update($work_type);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = GiftMaster::where('id',$uid)->where('company_id',$company_id)->update(['gift_image' => 'gift_images/'.$name]);

                    $request->file('imageFile')->move("gift_images", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }
        if (!$work_type_data) {
            DB::rollback();
        }

        if ($work_type_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('Gift-Master');
    }
}