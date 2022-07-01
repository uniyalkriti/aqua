<?php

namespace App\Http\Controllers;


use App\_module;
use App\Location1;
use App\Location2;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;


class Location1Controller extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Location';
        $this->module=Lang::get('common.location1');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $cities = $request->city;
        $state = $request->state;
        $company_id = Auth::user()->company_id;


        #super stock data
        $query = Location1::where('status', '!=', 2)->where('company_id',$company_id);


        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;

            $query->where(function ($subq) use ($q) {
                $subq->where('name', 'LIKE', '%' . $q . '%');
            });
        }
        # status filter enable it by setting 'status' named form-element on get request
        if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
            $query->where('status', $request->status);
        }

        # table sorting
        $query->orderBy('created_at','desc');

        $location1 = $query
                    ->get();
                    // ->paginate($pagination);

        return view('location1.index', [
            'location1' => $location1,
            'menu' => $this->menu,
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
        return view('location1.create',[
            'menu' => $this->menu,
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

            'zone' => 'required|max:50',
            'status' => 'required'
        ]);


        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $location1 = DB::table('location_1')->insert([

            'name' => trim($request->zone),
            'company_id' =>$company_id,
            'status' => trim($request->status)

        ]);

        if (!$location1) {
            DB::rollback();
        }
        if ($location1) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('/location1');
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
        $loc_data = Location1::where('company_id',$company_id)->findOrFail($encrypt_id);
        return view(
            'location1.edit',
            [
                'loc_data'=>$loc_data,
                'encrypt_id' => $id,
                'menu' => $this->menu,
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

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location1 Table
         */
        $location= [
            'name' => trim($request->name),
            'status' => trim($request->status)
        ];


        $l1_data= Location1::where('id', $uid)->where('company_id',$company_id)->update($location);

        if (!$l1_data) {
            DB::rollback();
        }

        if ($l1_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location1');
    }
}
