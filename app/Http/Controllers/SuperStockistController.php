<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location2;
use App\SuperStockist;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;

class SuperStockistController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'MASTERS';
        $this->module=Lang::get('common.super_stockist');
    }

    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $cities = $request->city;
        $state = $request->state;


        #super stock dataz
        $query = SuperStockist::join('location_2','super_stockists.location_id', '=','location_2.id')
            ->select('location_2.name as location_name', 'super_stockists.*')->where('super_stockists.status','!=',2);

        // $query = SuperStockist::where('status','!=',2);

        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;

            $query->where(function ($subq) use ($q) {
                $subq->where('super_stockists.name', 'LIKE', '%' . $q . '%');
            });
        }
        # status filter enable it by setting 'status' named form-element on get request
        if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
            $query->where('status', $request->status);
        }
        # table sorting
        if (($request->otype == 'asc' || $request->otype == 'desc') && ($request->oby == 'name' || $request->oby == 'department' || $request->oby == 'role' || $request->oby == 'status')) {
            $order = $request->oby;

        }
        if (empty($request->oby)) {
            $order = 'updated_at';
        }
        $oby = empty($request->otype) ? $request->otype : 'DESC';
        $query->orderBy($order, $oby);

        $stock = $query->paginate($pagination);



        return view('superstockist.index', [
            'superstocks' => $stock,
            'menu' =>$this->menu,
            'current_menu' => $this->current
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     **/
    public function create()
    {
        # location_3(state) Filler Menu
        $state = Location2::where('status',1)->get();
        return view('superstockist.create',[
            'state'=> $state,
            'menu' =>$this->menu,
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
            'ss_code' => 'required|max:70',
            'name' => 'required|max:150',
            'state' => 'required|max:20',
            'status' => 'required'
        ]);



        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $stock = SuperStockist::create([
            'ss_code' => trim($request->ss_code),
            'name' => trim($request->name),
            'location_id' => trim($request->state),
            'status' => trim($request->status)
        ]);
        //echo "<pre>";print_r($stock);die;

        if (!$stock) {
            DB::rollback();
        }
        if ($stock) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('super-stockist');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        #fetch the state data to fill state
        $state = Location2::where('status',1)->get();
        #decrypt id
        $uid = Crypt::decryptString($id);
        #fetch super stockist details data by eloquent so we can access related data as well
        $super_stock = SuperStockist::findOrFail($uid);
        $loc_id = $super_stock->location_id;
        $state_code = Location2::where('code', $loc_id)->first();

        #fetch multiple array superstockist edit view
        return view('superstockist.edit',
            ['loc_id'=> $loc_id,
                'state'=>$state,
                'encrypt_id'=>$id,
                'state_code' => $state_code,
                'super_stock'=> $super_stock,
                'menu' =>$this->menu,
                'current_menu' => $this->current
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
        $user_id = Crypt::decryptString($id);

        DB::beginTransaction();
        /**
         * @des: user array data in user table
         */
        $stock = [
            'ss_code' => trim($request->ss_code),
            'name' => trim($request->name),
            'location_id' => trim($request->state),
            'status' => trim($request->status)
        ];


        $stock_data = SuperStockist::where('id', $user_id)->update($stock);
        if (!$stock_data) {
            DB::rollback();
        }

        if ($stock_data) {
            DB::commit();
            Session::flash('message', "$this->module updated successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('/super-stockist');


    }
}
