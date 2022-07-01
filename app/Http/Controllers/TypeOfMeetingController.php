<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\Travellingtype;
use App\Workingtype;
use App\OutletCategory;
use App\DailySchedule;
use App\ReturnTypeDamage;
use App\MeetingType;
use App\TypeOfMeeting;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class TypeOfMeetingController extends Controller
{
    public function __construct()
    {
        $this->current = 'type_of_meeting';
        $this->module=Lang::get('common.type_of_meeting');
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

       
        $query = TypeOfMeeting::select('id','name','status','sequence')
                ->where('status', '!=', 9)
                ->where('company_id',$company_id);



        # table sorting
        $query->orderBy('id','desc');

        $working_type = $query->get();
        // dd($working_type);
        return view('TypeOfMeeting.index', [
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
        $working_type = TypeOfMeeting::where('company_id',$company_id)->orderBy('sequence','desc')->first();
        // dd($working_type);
        return view('TypeOfMeeting.create',[
            'working_type'=> $working_type,
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


        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $work_type = TypeOfMeeting::insert([
            'name' => trim($request->workType),
            'code' => $request->code,
            'sequence' => $request->sequence,
            'company_id' => $company_id,
            'status' => trim($request->status),
            'created_at'=> date('Y-m-d H:i:s'),
            'created_by'=>Auth::user()->id,
        ]);


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

        return redirect('type_of_meeting');
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
        $workType_info = TypeOfMeeting::where('company_id',$company_id)->pluck('name', 'id');
        $workType_data = TypeOfMeeting::where('company_id',$company_id)->where('id',$encrypt_id)->first();
        // dd($workType_data);
        return view('TypeOfMeeting.edit',
            [
                'workType_info'=>$workType_info,
                'workType_data'=>$workType_data,
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
            'workType' => 'required|max:50',
            'status' => 'required',
            'sequence'=>'required',
            'code' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $work_type= [
            'name' => trim(($request->workType)),
            'status' => trim($request->status),
            'code' => $request->code,
            'sequence' => $request->sequence,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];


        $work_type_data= TypeOfMeeting::where('id', $uid)->where('company_id',$company_id)->update($work_type);

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

        return redirect('type_of_meeting');
    }
}
