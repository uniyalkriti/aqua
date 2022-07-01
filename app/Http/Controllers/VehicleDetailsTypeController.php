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
use App\VehicleType;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Image;
class VehicleDetailsTypeController extends Controller
{
    public function __construct()
    {
        $this->current = 'vehicle_details';
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

       
        $query = VehicleType::select('id','name','status','sequence')
                ->where('status', '!=', 9)
                ->where('company_id',$company_id);



        # table sorting
        $query->orderBy('id','desc');

        $working_type = $query->get();
        // dd($working_type);
        return view('vehicle_details.index', [
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
        $working_type = VehicleType::where('company_id',$company_id)->orderBy('sequence','desc')->first();
        // dd($working_type);
        return view('vehicle_details.create',[
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
        $work_type = VehicleType::create([
            'name' => trim(strtoupper($request->workType)),
            'sequence' => $request->sequence,
            'weight' => $request->weight,
            'min_qty' => !empty($request->min_qty)?$request->min_qty:'0',
            'max_qty' => !empty($request->max_qty)?$request->max_qty:'0',
            'company_id' => $company_id,
            'status' => trim($request->status),
            'created_at'=> date('Y-m-d H:i:s'),
            'created_by'=>Auth::user()->id,
        ]);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = VehicleType::where('id',$work_type->id)->update(['image_name' => 'circular_image/'.$name]);

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

        return redirect('vehicle_details');
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
        $workType_info = VehicleType::where('company_id',$company_id)->pluck('name', 'id');
        $workType_data = VehicleType::where('company_id',$company_id)->where('id',$encrypt_id)->first();
        // dd($workType_data);
        return view('vehicle_details.edit',
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
            'sequence'=>'required'
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $work_type= [
            'name' => trim(strtoupper($request->workType)),
            'status' => trim($request->status),
            'sequence' => $request->sequence,
            'weight' => $request->weight,
            'min_qty' => !empty($request->min_qty)?$request->min_qty:'0',
            'max_qty' => !empty($request->max_qty)?$request->max_qty:'0',
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];


        $work_type_data= VehicleType::where('id', $uid)->where('company_id',$company_id)->update($work_type);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = VehicleType::where('id',$uid)->where('company_id',$company_id)->update(['image_name' => 'circular_image/'.$name]);

                    $request->file('imageFile')->move("circular_image", $name);
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

        return redirect('vehicle_details');
    }
}
