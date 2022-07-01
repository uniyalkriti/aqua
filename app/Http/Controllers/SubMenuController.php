<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\MainMenu;
use App\SubMenu;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class SubMenuController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Menu';
        $this->module=Lang::get('common.SubMenu');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        //  $cities = $request->city;
        //  $state = $request->state;
        // $company_id = Auth::user()->company_id;

        #super stock data
        $query = SubMenu::select('id', 'module_id','sub_module_name', 'title', 'status', 'is_main', 'parent_sub_module_id', 'created_at', 'updated_at')
                ->where('sub_web_module_bucket.status', '!=', 9);
              


        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;

            $query->where(function ($subq) use ($q) {
                $subq->where('name', 'LIKE', '%' . $q . '%');
            });
        }
        # status filter enable it by setting 'status' named form-element on get request
        // if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
        //     $query->where('status', $request->status);
        // }
        # table sorting
      //  $query->orderBy('location_2.created_at','desc');

        $mainmenu = $query->get();
        // dd($location1);
        return view('Menu.index', [
            'mainmenu' => $mainmenu,
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
        // $company_id = Auth::user()->company_id;
         $MainMenu = MainMenu::where('status','1')->get();
        // $SubMenu=SubMenu::where('company_id',$company_id)->get();
        return view('Menu.create',[
            'main_menu' =>$MainMenu,
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
            'sub_module_name' => 'required|max:50',
            'status' => 'required',
            'module_id' => 'required',
            'title' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $location2 = SubMenu::insert([
            'sub_module_name' => trim($request->sub_module_name),
            'title' => trim($request->title),
            'module_id' => trim($request->module_id),
            'status' => trim($request->status),
            'created_at' => date('Y-m-d H:i:s',strtotime('now')),
            'updated_at' => date('Y-m-d H:i:s',strtotime('now'))
        ]);


        if (!$location2) {
            DB::rollback();
        }
        if ($location2) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('Menu');
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
         
         $mainmenu_info = SubMenu::where('id',$encrypt_id)->pluck('sub_module_name','title', 'id');
       
        // $loc_info = Location2::where('company_id',$company_id)->pluck('name', 'id');
         $loc_data = MainMenu::where('status','1')->pluck('name', 'id');
         $Mainmenu_data = SubMenu::where('id',$encrypt_id)->findOrFail($encrypt_id);
           //dd($Mainmenu_data);
     
        return view(
            'Menu.edit',
            [
                'location1_info'=>$mainmenu_info,
                'main_menu'=>$loc_data,
                'loc_data'=>$Mainmenu_data,
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
        $validatedData = $request->validate([
            'sub_module_name' => 'required|max:50',
            'status' => 'required',
             'module_id' => 'required',
            'title' => 'required'
        ]);

        $uid = Crypt::decryptString($id);
       //$company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $location= [
            'sub_module_name' => trim($request->sub_module_name),
            'title' => trim($request->title),
             'module_id' => trim($request->module_id),
            'status' => trim($request->status)
        ];


        $lM_data= SubMenu::where('id', $uid)->update($location);

        if (!$lM_data) {
            DB::rollback();
        }

        if ($lM_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('Menu ');
    }
}
