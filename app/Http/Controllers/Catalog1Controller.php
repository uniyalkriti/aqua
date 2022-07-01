<?php

namespace App\Http\Controllers;


use App\Catalog1;
use App\User;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class Catalog1Controller extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'catalog_1';
        $this->current_dir = 'catalog1';
        $this->table = 'catalog_0';

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $query = Catalog1::where('company_id',$company_id)->where('status', '!=', '9');
        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;
            $query->where(function ($subq) use ($q) {
                $subq->where('catalog_0.name', 'LIKE', $q . '%');
            });
        }

        $data = $query
            ->orderBy('id', 'desc')
            ->get();

        return view($this->current_dir.'.index',
            [
                'records' => $data,
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
        #Location 1 data
        $company_id = Auth::user()->company_id;
        $cat_data = Catalog1::where('company_id',$company_id)->where('status', '=', '1')->pluck('name', 'id');

        return view($this->current_dir.'.create',
            ['current_menu' => $this->current_menu,
                'cat_data' => $cat_data,
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
            'catalog_1' => 'required|min:2|max:100'
        ]);
        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            'name' => trim($request->catalog_1),
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d'),
            'company_id' => $company_id,
            'status' => 1
        ];

        $user = Catalog1::create($myArr);
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
        $catalog_data = Catalog1::where('company_id',$company_id)->findOrFail($uid);

        return view($this->current_dir.'.edit',
            [
                'catalog_data' => $catalog_data,
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
            'catalog_1' => 'required|min:2|max:100'
        ]);
        $company_id = Auth::user()->company_id;
        $id = Crypt::decryptString($id);
        $location_data = Catalog1::where('company_id',$company_id)->findOrFail($id);

        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            'name' => trim($request->catalog_1),
            'created_by' => Auth::user()->id,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $location = $location_data->update($myArr);

        if ($location) {
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
