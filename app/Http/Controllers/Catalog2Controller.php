<?php

namespace App\Http\Controllers;


use App\Catalog1;
use App\Catalog2;
use App\User;
use Illuminate\Http\Request;
use Auth;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class Catalog2Controller extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'catalog_2';
        $this->current_dir = 'catalog2';
        $this->table = 'catalog_1';

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $company_id = Auth::user()->company_id;
        $catalog_0_id = $request->catalog_0;
        $catalog_0_array = array();

        $query = Catalog2::leftJoin('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
            ->where('catalog_1.status', '!=', '9')
            ->where('catalog_0.company_id',$company_id)
            ->where('catalog_1.company_id',$company_id);
        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;
            $query->where(function ($subq) use ($q) {
                $subq->where('catalog_1.name', 'LIKE', $q . '%'); 
            });
        }

        if(!empty($catalog_0_id))
        {
            $query->whereIn('catalog_0.id',$catalog_0_id);

            $catalog_0_array = DB::table('catalog_0')
                                ->where('company_id',$company_id)
                                ->whereIn('id',$catalog_0_id)
                                ->where('status',1)
                                ->pluck('id')->toArray();
        }

        $data = $query->select('catalog_1.*','catalog_0.name as cat1','catalog_0.id as cat1id')
            ->orderBy('catalog_1.id', 'desc')
            ->get();

        $catalog_0 = DB::table('catalog_0')->where('status',1)->where('company_id',$company_id)->pluck('name','id')->toArray();

// dd($data);
        return view($this->current_dir.'.index',
            [
                'records' => $data,
                'catalog_0'=> $catalog_0,
                'catalog_0_array'=>$catalog_0_array,
                'current_menu' => $this->current_menu,
                'table' => $this->table,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #Catalog 1 data
        $company_id = Auth::user()->company_id;
        $cat1 = Catalog1::where('company_id',$company_id)->where('status', '!=', '9')->pluck('name', 'id');

        return view($this->current_dir.'.create',
            ['current_menu' => $this->current_menu,
                'cat1' => $cat1,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'catalog_2' => 'required',
            'catalog_1' => 'required',
            'color_picker' => 'required',
        ]);

        DB::beginTransaction();

        /**
         * @des: save data in Project table
         */
        // $color = '#ff4000';
        $myArr = [
            'name' => trim($request->catalog_2),
            'catalog_0_id' => trim($request->catalog_1),
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d'),
            'c1_color_code' => $request->color_picker,
            'status' => 1
        ];

        $user = Catalog2::create($myArr);
        if ($user) {
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        #decrypt id
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        #fetch Project
        $catalog_data = Catalog2::where('company_id',$company_id)->findOrFail($uid);

        #Catalog 1 data
        $cat1 = Catalog1::where('company_id',$company_id)->where('status', '!=', '9')->pluck('name', 'id');

        return view($this->current_dir.'.edit',
            [
                'catalog_data' => $catalog_data,
                'cat1' => $cat1,
                'encrypt_id' =>$id,
                'current_menu'=>$this->current_menu
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'catalog_2' => 'required',
            'catalog_1' => 'required',
        ]);
        $company_id = Auth::user()->company_id;
        $id = Crypt::decryptString($id);
        $catalog_data = Catalog2::where('company_id',$company_id)->findOrFail($id);

        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            'name' => trim($request->catalog_2),
            'catalog_0_id' => trim($request->catalog_1),
            'updated_at' => date("Y-m-d H:i:s"),
            'c1_color_code' => $request->color_picker,
            'created_by' => Auth::user()->id,
        ];

        $catalog = $catalog_data->update($myArr);

        if ($catalog) {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
