<?php

namespace App\Http\Controllers;

use App\DealerLocation;
use App\Location3;
use App\Location4;
use App\UserDetail;
use DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Image;
use App\_module;
use App\_role;
use App\AppModule;
use App\AppSubModule;
use App\Location2;
use App\User;
use App\UserAppModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'MASTERS';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $cities = $request->city;
        $state = $request->state;

        #user data
        $query = User::join('user_details', 'user_details.user_id', '=', 'users.id')->where('user_id', '!=', Auth::user()->id)->where('users.status', '!=', 2);

        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;
            $query->where(function ($subq) use ($q) {
                $subq->where('name', 'LIKE', '%' . $q . '%')->orWhere('email', 'LIKE', '%' . $q . '%');
            });
        }

        # status filter enable it by setting 'status' named form-element on get request
        if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
            $query->where('users.status', $request->status);
        }
        # state filter
        if (!empty($state)) {
            $query->where('user_details.location_2_id', $state);
        }
        # city filter
        if (is_array($cities) && array_filter($cities)) {
            foreach ($cities as $key => $city) {
                if ($key > 0)
                    $query->orwhere('user_details.location_3_id', $city);
                else
                    $query->where('user_details.location_3_id', $city);
            }
        }

        # table sorting
        if (($request->otype == 'asc' || $request->otype == 'desc') && ($request->oby == 'name' || $request->oby == 'department' || $request->oby == 'role' || $request->oby == 'status')) {

            if ($request->oby == 'department') {
                $order = 'dept_id';
            } elseif ($request->oby == 'role') {
                $order = 'user_details.role_id';
            } else {
                $order = $request->oby;
            }
        }
        if (empty($request->oby)) {
            $order = 'user_details.updated_at';
        }
        $oby = empty($request->otype) ? $request->otype : 'DESC';
        $query->orderBy($order, $oby);

        $users = $query->paginate($pagination);

        # Role data for filter menu
        $role_data = _role::where('status', 1)->get();

        #State data for filter menu
        $state_data = Location2::all();

        return view('user.index',
            [
                'users' => $users,
                'role_data' => $role_data,
                'state_data' => $state_data,
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
        #State(Location2) data for filter menu
        $state_data = Location2::all();

        # App settings data
        $appData = AppModule::where('app_type', 1)->get();

        # Role data for filter menu
        $role_data = _role::where('status', 1)->pluck('name', 'id');

        # App module data
        $app_module_data = AppModule::where('status', 1)->get();

        # App sub module data
        $sub_module = AppSubModule::where('status', '=', 1)->get();

        return view('user.create',
            ['state_data' => $state_data,
                'appData' => $appData,
                'role_data' => $role_data,
                'app_module_data' => $app_module_data,
                'sub_module' => $sub_module,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $imageName = '';

        $validatedData = $request->validate([
            'first_name' => 'required|max:70',
            'last_name' => 'required|max:70',
            'dob' => 'date',
            'gender' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'state' => 'required',
            'region' => 'required',
            'city' => 'required',
            'emp_code' => 'required',
            'distributor' => 'required',
            'address' => 'required',
            'mobile_no' => 'required|integer',
            'photo' => 'mimes:jpeg,bmp,png,jpg',
            'role' => 'required',
        ]);

        if (!empty($request->photo)) {
            $imageName = time() . '.' . $request->photo->getClientOriginalExtension();
            $request->photo->move(public_path('users-profile'), $imageName);
        }

        DB::beginTransaction();
        /**
         * @des: save data in user table
         */

        $user = User::create([
            'name' => trim($request->first_name) . ' ' . trim($request->last_name),
            'email' => trim($request->email),
            'password' => bcrypt($request->password),
            'profile_image' => !empty($imageName) ? 'users-profile/' . $imageName : '',
            'api_token' => '',
            'role_id' => !empty($request->role) ? $request->role : 0,
            'status' => 1,
        ]);


        if (!$user) {
            DB::rollback();
        }

//        dd($request->city);

        /**
         * @desc: save data in users_details table
         */
        $userDetails = UserDetail::create([
            'user_id' => $user->id,
            'employee_code' => trim($request->emp_code),
            'first_name' => trim($request->first_name),
            'middle_name' => '',
            'last_name' => trim($request->last_name),
            'dob' => !empty($request->dob) ? date("Y-m-d", strtotime($request->dob)) : '',
            'gender' => $request->gender,
            'alt_no' => 0,
            'email_id' => trim($request->email),
            'location_2_id' => $request->state,
            'senior_id' => $request->seniorname,
            'location_3_id' => $request->region,
//            'location_4_id' => $request->city,
            'address' => trim($request->address),
            'mobile' => trim($request->mobile_no),
            'role_id' => !empty($request->role) ? $request->role : 0,
//            'status' => 1,
            'location_1_id' => 1,
            'imei' => 1,
            'image_name' => !empty($imageName) ? 'users-profile/' . $imageName : '',

        ]);

        # Set permissions
        $add = $request->add_permissions;
        $view = $request->view_permissions;
        $edit = $request->edit_permissions;
        $hidden = $request->hidden_val;


        if (count($hidden) > 0) {
            foreach ($hidden as $key => $permissionArr) {
                if (count($permissionArr) > 0) {
                    foreach ($permissionArr as $submodule_id => $p2) {
                        $test[] = array(
                            'user_id' => $user->id,
                            'module_id' => $key,
                            'sub_module_id' => $submodule_id,
                            'edit' => isset($edit[$key][$submodule_id]) ? 1 : 0,
                            'add' => isset($add[$key][$submodule_id]) ? 1 : 0,
                            'view' => isset($view[$key][$submodule_id]) ? 1 : 0
                        );
                    }
                }
            }
        }

        $distributor = $request->distributor;

        $dl = DealerLocation::where('dealer_id', $distributor)->pluck('dealer_id', 'location_id');
        // print_r($dl);exit;
        $myDataArr = [];
        foreach ($dl as $key => $value) {
            $myDataArr[] = [
                'dealer_code' => $value,
                'beat_code' => $key,
                'user_code' => $user->employee_code
            ];
        }


        if (count($myDataArr) > 0) {
            $user_dealer = UserDealerLocation::insert($myDataArr);

            if (!$user_dealer) {
                DB::rollback();
            }
        }


        if (isset($test)) {

            #insert data for permission
            $user_app_permission = UserAppModulePermission::insert($test);
        }


        if ($user && $userDetails) {
            DB::commit();
            Session::flash('message', 'User created successfully');
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('/user-management');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        # Handle exception when encrypted string is invalid
        try {
            $user_id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            return abort(404);
        }
        #location2 data
        $location2=Location2::where('status',1)->pluck('name','id');

        return view('user.assign',
            [
                'location2' => $location2,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $region = [];
        $city = [];

        #decrypt id
        $uid = Crypt::decryptString($id);

        #fetch user details data by eloquent so we can access related data as well
        $user_data = User::findOrFail($uid);

        $location2_id = $user_data->userDetails->location_2_id;
        $location3_id = $user_data->userDetails->location_3_id;

        // dd($role_name);
        if (!empty($location2_id)) {
            $region = Location3::where('location_2_id', $location2_id)->pluck('name', 'code');
        }
        // dd($region);
        if (!empty($location2_id)) {
            $city = Location4::where('location_3_id', $location3_id)->pluck('name', 'code');
        }


        #State data for filter menu
        $state_data = Location2::all();

        # App settings data
        $appData = AppModule::where('app_type', 1)->get();

        #user app modules permission
        $permission = UserAppModulePermission::where('user_id', $uid)->get();


        # Role data for filter menu
        $role_data = _role::where('status', 1)->pluck('name', 'id');

        # App module data
        $app_module_data = AppModule::where('status', 1)->get();

        # App sub module data
        $sub_module = AppSubModule::where('status', '=', 1)->get();

        return view('user.edit',
            ['state_data' => $state_data,
                'appData' => $appData,
                'role_data' => $role_data,
                'app_module_data' => $app_module_data,
                'sub_module' => $sub_module,
                'user' => $user_data,
                'region' => $region,
                'city' => $city,
                'encrypt_id' => $id,
                'permission' => $permission,
                'menu' => $this->menu,
                'current_menu' => $this->current
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

        $user_id = Crypt::decryptString($id);
        $imageName = '';
        //        $validatedData = $request->validate([
        //            'first_name' => 'required|max:70',
        //            'last_name' => 'required|max:70',
        //            'dob' => 'date',
        //            'gender' => 'required',
        //            'email' => 'required|email|unique:users,email,'.$id,
        //            'password' => 'min:6',
        //            'state' => 'required',
        //            'region' => 'required',
        //            'city' => 'required',
        //            'address' => 'required',
        //            'mobile_no' => 'required|integer',
        //            'photo' => 'mimes:jpeg,bmp,png,jpg',
        //            'role' => 'required',
        //        ]);

        $data = UserDetail::where('user_id', '=', $user_id)->first();


        DB::beginTransaction();
        /**
         * @des: user array data in user table
         */
        $userArr = [
            'name' => trim($request->first_name) . ' ' . trim($request->last_name),
            'email' => trim($request->email),
            'api_token' => '',
            'role_id' => !empty($request->role) ? $request->role : 0,
            'status' => 1,
        ];
        # Optional password
        if (!empty($request->password)) {
            $userArr['password'] = bcrypt($request->password);
        }
        # Optional image upload
        if ($request->hasFile('photo')) {
            $imageName = time() . '.' . $request->photo->getClientOriginalExtension();
            $request->photo->move(public_path('users-profile'), $imageName);
            $userArr['profile_image'] = $imageName = 'users-profile/' . $imageName;
        }


        $user = User::where('id', $user_id)->update($userArr);
        if (!$user) {
            DB::rollback();
        }

        # array for user_details
        $userDetailsArr = [
            'first_name' => trim($request->first_name),
            'middle_name' => '',
            'last_name' => trim($request->last_name),
            'dob' => !empty($request->dob) ? date("Y-m-d", strtotime($request->dob)) : '',
            'gender' => $request->gender,
            'email_id' => trim($request->email),
            'location_2_id' => $request->state,
            'location_3_id' => $request->region,
//            'location_4_id' => $request->city,
            'address' => trim($request->address),
            'mobile' => trim($request->mobile_no),
            'role_id' => !empty($request->role) ? $request->role : 0

        ];

        if (!empty($imageName)) {
            $userDetailsArr['image_name'] = $imageName;
        }

        /**
         * @desc: update data in users_details table
         */
        $userDetails = UserDetail::where('user_id', $user_id)->update($userDetailsArr);

        # Set permissions
        $add = $request->add_permissions;
        $view = $request->view_permissions;
        $edit = $request->edit_permissions;
        $hidden = $request->hidden_val;


        if (count($hidden) > 0) {
            foreach ($hidden as $key => $permissionArr) {
                if (count($permissionArr) > 0) {
                    foreach ($permissionArr as $submodule_id => $p2) {
                        $test[] = array(
                            'user_id' => $user_id,
                            'module_id' => $key,
                            'sub_module_id' => $submodule_id,
                            'edit' => isset($edit[$key][$submodule_id]) ? 1 : 0,
                            'add' => isset($add[$key][$submodule_id]) ? 1 : 0,
                            'view' => isset($view[$key][$submodule_id]) ? 1 : 0
                        );
                    }
                }
            }
        }


        if (isset($test)) {
            # Delete old data for permission
            $delete_old = UserAppModulePermission::where('user_id', $user_id)->delete();

            #insert data for permission
            $user_app_permission = UserAppModulePermission::insert($test);
        }


        if ($user && $userDetails) {
            DB::commit();
            Session::flash('message', 'User updated successfully');
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('/user-management');
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
