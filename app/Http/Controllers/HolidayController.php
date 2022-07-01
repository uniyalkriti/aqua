<?php

namespace App\Http\Controllers;


use App\Location1;
use App\Location2;
use App\Holiday;
use App\User;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class HolidayController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'holiday';
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


        $query = Holiday::where('holiday.status', '!=', '2')->where('company_id',$company_id);
        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;
            $query->where(function ($subq) use ($q) {
                $subq->where('holiday.name', 'LIKE', $q . '%');
            });
        }

        $data = $query->select('holiday.*')
            ->orderBy('id', 'desc')
            ->paginate($pagination);
       // dd($data);

        return view('holiday.index',
            [
                'records' => $data,
                'current_menu' => $this->current_menu
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

        $holiday = Holiday::where('status', '!=', '2')->where('company_id',$company_id)->pluck('name', 'id');

        return view('holiday.create',
            ['current_menu' => $this->current_menu,
                'holiday' => $holiday,
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
            'date' => 'required',
        ]);


        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            // 'name' => trim($request->holiday),
            'company_id' => $company_id,
            'name' => $request->holiday,
            'day' => date("l",strtotime($request->date)),
            'date' => trim($request->date),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // dd($myArr);

        $holiday = Holiday::create($myArr);
        if ($holiday) {
            DB::commit();
            Session::flash('message', Lang::get('common.holiday').' created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended('holiday');

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
        $holiday_data = Holiday::where('holiday.id',$uid)->where('company_id',$company_id)->first();

      

        return view('holiday.edit',
            [
                'holiday_data' => $holiday_data,
                'encrypt_id' => $id,
                'current_menu' => $this->current_menu
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
            'date' => 'required',
        ]);

        $id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;

        $holiday_data = Holiday::where('company_id',$company_id)->findOrFail($id);

        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            'name' => trim($request->holiday),
            'day' => date("l",strtotime($request->date)),
            'date' => $request->date,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $holiday = $holiday_data->update($myArr);

        if ($holiday) {
            DB::commit();
            Session::flash('message', Lang::get('common.location3').' updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended('/holiday');
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
