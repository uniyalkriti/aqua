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
use App\LeaveType;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class CommonMasterWithImageController extends Controller
{
    public function __construct()
    {
        $this->current = 'leave_type';
        $this->module=Lang::get('common.leave_type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $domain_name_custom = $_SERVER['REQUEST_URI'];
        // dd($domain_name_custom);
        $table_name = str_replace('/public/','', $domain_name_custom);
        // dd($table_name);
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $company_id = Auth::user()->company_id;

       
        $query = DB::table($table_name)
                ->select('id','name','status','sequence','image_name','link')
                ->where('status', '!=', 9)
                ->where('company_id',$company_id);



        # table sorting
        $query->orderBy('id','desc');

        $working_type = $query->get();
        // dd($working_type);
        return view('CommonMasterWithImage.index', [
            'working_type' => $working_type,
            'table_name'=>$table_name,
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
        $domain_name_custom = $_SERVER['REQUEST_URI'];
        $table_name_1 = str_replace('/public/','', $domain_name_custom);
        $table_name = str_replace('/create','', $table_name_1);
        $working_type = DB::table($table_name)->where('company_id',$company_id)->orderBy('sequence','desc')->first();
        // dd($working_type);
        return view('CommonMasterWithImage.create',[
            'working_type'=> $working_type,
            'table_name'=> $table_name,
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
        $domain_name_custom = $_SERVER['REQUEST_URI'];
        $table_name = str_replace('/public/','', $domain_name_custom);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $imageName = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    // $personImage = PersonLogin::where('person_id',$person->id)->update(['person_image' => 'users-profile/'.$name]);

                    $request->file('imageFile')->move("common-images", $imageName);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }

        $work_type = DB::table($table_name)->insert([
            'name' => trim(strtoupper($request->workType)),
            'sequence' => $request->sequence,
            'link' => $request->link,
            'image_name' => !empty($imageName)?'common-images/'.$imageName:'',
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

        return redirect($table_name);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        // dd($request);
        $encrypt_id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        // $domain_name_custom = $_SERVER['REQUEST_URI'];
        // $table_name1 = str_replace('/public/','', $domain_name_custom);
        $table_name = $request->table_name;

        $workType_info = DB::table($table_name)->where('company_id',$company_id)->pluck('name', 'id');
        $workType_data = DB::table($table_name)->where('company_id',$company_id)->where('id',$encrypt_id)->first();
        // dd($workType_data);
        return view('CommonMasterWithImage.edit',
            [
                'workType_info'=>$workType_info,
                'workType_data'=>$workType_data,
                'table_name'=>$table_name,
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
            'sequence'=>'required'
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        // $domain_name_custom = $_SERVER['REQUEST_URI'];
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */

        
        $work_type= [
            'name' => trim(($request->workType)),
            // 'image_name' => !empty($imageName)?'common-images/'.$imageName:'',
            'status' => trim($request->status),
            'link' => $request->link,
            'sequence' => $request->sequence,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];

        $table_name = $request->table_name;
        // $table_name = str_replace('/public/','', $domain_name_custom);

        $work_type_data= DB::table($table_name)->where('id', $uid)->where('company_id',$company_id)->update($work_type);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $imageName = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to iam
                    $personImage = DB::table($table_name)->where('id', $uid)->where('company_id',$company_id)->update(['image_name' => 'common-images/'.$imageName]);

                    $request->file('imageFile')->move("common-images", $imageName);
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

        return redirect($table_name);
    }
}
