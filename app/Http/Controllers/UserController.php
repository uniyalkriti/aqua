<?php

namespace App\Http\Controllers;

use App\Company;
use App\Location1;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use App\PersonDetail;
use App\JuniorData;
use App\CommonFilter;
use App\PersonLogin;
use DB;
use App\User;
// use App\Division;
use App\Person;
use App\_role;
use Auth;
use App\UserTodaysAttendanceEnabledLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->current_menu='user';

        $this->status_table='person';
        $this->table = 'user_todays_attendance_enabled_log';

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $role = $request->role;
        $location_6 = $request->location_6;
        $app_version = $request->app_version;
        $user = $request->user;
        $state = $request->state;
        $company_id = Auth::user()->company_id;


        
        $user_auth=Auth::user();

        $master_documents = DB::table('dms_document_master')->where('company_id',$company_id)->where('status',1)->pluck('name','id');


        if($user_auth->role_id==1 || $user_auth->is_admin=='1' || $user_auth->role_id==50 || $user_auth->role_id==312)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user_auth->id,$company_id);
            Session::push('juniordata', $user_auth->id);
            $junior_data = Session::get('juniordata');
        }

        $q=Person::join('person_details','person_details.person_id','=','person.id','inner')
            ->join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            // ->join('location_6','location_6.id','=','person.town_id')
            // ->join('location_5','location_5.id','=','person.head_quater_id')
            // ->join('location_4','location_4.id','=','location_5.location_4_id')
            ->join('location_3','person.state_id','=','location_3.id')
            // ->select('person_details.created_on as created_on','person_details.address as personaddress','location_4.name as l4_name','location_6.name as town_name','location_5.name as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
            //     WHERE p1.id=person.person_id_senior LIMIT 1) as srname"),'person_details.deleted_deactivated_on as deactivate_date','person_login.last_mobile_access_on as last_sync')
             ->select('person_details.created_on as created_on','person_details.address as personaddress','person.town_id as town_name','person.head_quater_id as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
                WHERE p1.id=person.person_id_senior LIMIT 1) as srname"),'person_details.deleted_deactivated_on as deactivate_date','person_login.last_mobile_access_on as last_sync')
            ->where('person.company_id',$company_id)
            ->where('location_3.company_id',$company_id)
            ->where('_role.company_id',$company_id)
            ->where('person_status','!=','9')
            ->where('person.id','!=','1');

        #state filter 
        if(!empty($state))
        {
            $q->whereIn('person.state_id',$state);
        }
        if(!empty($junior_data))
        {
            $q->whereIn('person.id',$junior_data);
        }
        #role filter 
        if(!empty($app_version))
        {
            $q->whereIn('person.version_code_name',$app_version);
        }
        if(!empty($role))
        {
            $q->whereIn('_role.role_id',$role);
        }
        if(!empty($user))
        {
            $q->whereIn('person.id',$user);
        }
        if(!empty($location_6))
        {
            $q->whereIn('person.town_id',$location_6);
        }
        if(!empty($request->status))
        {
            if($request->status==2)
            {
                $q->where('person_login.person_status',0);
            }
            else
            {
                $q->where('person_login.person_status',$request->status);
            }
        }
        if(!empty($request->location_4))
        {
            $q->join('location_5','location_5.id','=','person.head_quater_id')->whereIn('location_5.location_4_id',$request->location_4);
        }
        if(!empty($request->location_5))
        {
            $q->whereIn('head_quater_id',$request->location_5);
        }

        # search functionality
        if (!empty($request->search)) {
            $key = $request->search;
            $q->where(function ($subq) use ($key) {
                $subq->where('person.first_name', 'LIKE',  $key . '%');
                $subq->orWhere('person.middle_name', 'LIKE',  '%'.$key.'%');
                $subq->orWhere('person.last_name', 'LIKE',  '%'.$key);
            });
        }

        $data = $q->orderBy('person.id', 'desc')
            ->paginate($pagination);
            // ->get();

       // $srname=Person::select('person.*')
         //   ->where('id',$data->person_id_senior)->first();

            $depoData = DB::table('person')
            ->join('location_5','location_5.id','=','person.head_quater_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')
            ->where('person.company_id',$company_id)
            ->where('location_5.company_id',$company_id)
            ->where('location_4.company_id',$company_id)
            ->groupBy('person.id')
            ->pluck('location_4.name','person.id')->toArray();


        #Location 1 data
        $location1 = Location1::where('status', '=', '1')->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $state = Location3::where('status', '=', '1')->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $head_quater = Location5::where('status', '=', '1')->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $location_6 = Location6::where('status', '=', '1')->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $location_4 = Location4::where('status', '=', '1')->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $location_5 = Location5::where('status', '=', '1')->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $version = DB::table('version_management')->where('company_id',$company_id)->pluck(DB::raw("CONCAT('Version: ',version_name,'/',version_code) as version"),DB::raw("CONCAT('Version: ',version_name,'/',version_code) as version"));
        $user = Person::join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->where('person.company_id',$company_id)
            ->where('is_admin','!=',1)
            ->where('person_status',1)
            ->pluck('name', 'uid');
       // dd($user);
        // $division = Division::where('status', '=', '1')->where('division_type',1)->where('company_id',$company_id)->pluck('name', 'id');
        // dd($division);
        $role = _role::orderBy('role_sequence','ASC')->where('status',1)->where('company_id',$company_id)->pluck('rolename', 'role_id');

        $product_rate_list_assign_part = DB::table('app_other_module_assign')
                                        ->where('company_id',$company_id)
                                        ->get();
            // dd($product_rate_list_assign_part);

        //  $distributor_details=[];
        // if (!empty($data))
        // {
        //     foreach ($data as $k=>$v)
        //     {
        //         $uid=$v->id;
        //         $data[$k]['details']=DB::table('dealer_location_rate_list')
        //         ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
        //         ->select('user_id','dealer.id as dealer_id','dealer.name AS dealer_name',DB::raw("count(dealer_location_rate_list.location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT udr.id) FROM retailer AS udr WHERE udr.dealer_id=dealer_location_rate_list.dealer_id LIMIT 1) AS retailer_count"))
        //         ->where('dealer_location_rate_list.user_id',$uid)
        //         ->where('dealer_location_rate_list.company_id',$company_id)
        //         ->where('dealer.company_id',$company_id)
        //         ->where('dealer_status',1)
        //         ->groupBy('dealer_id','retailer_count')
        //         ->get();
        //         // $data[$k]['details']=DB::table('dealer_location_rate_list')
        //         // ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
        //         // ->select('dealer.name AS dealer_name',DB::raw("count(location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT retailer_id) FROM retailer  WHERE dealer_location_rate_list.user_id=$uid AND dealer_location_rate_list.dealer_id=retailer.dealer_id LIMIT 1) AS retailer_count"))
        //         // ->where('dealer_location_rate_list.user_id',$uid)
        //         // ->groupBy('dealer_name','retailer_count')
        //         // ->get();
        //     }
        // }
     //  dd($distributor_details);
        $person_documents_data = DB::table('person_documents_data')->where('company_id',$company_id)->groupBy('userid','document_id')->pluck('document_image',DB::raw("CONCAT(userid,document_id)"));


        $location_3 = DB::table('location_3')->where('company_id',$company_id)->where('status','=','1')->pluck('name','id');

        return view('user.index', [
            'records' => $data,
            'status_table' => $this->status_table,
            'table' => $this->table,
            'current_menu'=>$this->current_menu,
            'location1' => $location1,
            'state' => $state,
            'role' => $role,
            // 'division'=> $division,
            'head_quater'=> $head_quater,
            'person_documents_data' => $person_documents_data,
            'location_6'=> $location_6,
            'location_4'=> $location_4,
            'location_5'=> $location_5,
            'location_3'=> $location_3,
            'master_documents'=>$master_documents,
            'depoData'=>$depoData,

            'user'=> $user,
            'version'=> $version,
            'company_id'=> $company_id,
            'product_rate_list_assign_part'=> $product_rate_list_assign_part,
        ]);

    }

    public function user_upload_documents(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->hasFile('imageFile')) {
          
                try {

                    $files = $request->file('imageFile');
                    $inc = 0;

                    foreach($files as $file_key => $file)
                    {
                        $name_random = date('YmdHis').$inc;
                        $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
                        $random_no = substr($str, 0,2);  // return always a new string 
                        $custom_image_name = date('YmdHis').$random_no;
                        $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
                        $file_name[] = $imageName;
                        $destinationPath = public_path('/dealer_documents/');
                        $file->move($destinationPath , $imageName);
                        $delete_data = DB::table('person_documents_data')
                                    ->where('userid',$request->userid)
                                    ->where('document_id',!empty($request->document_id[$file_key])?$request->document_id[$file_key]:'0')
                                    ->delete();

                        $personImage = DB::table('person_documents_data')->insert([
                                        'document_image' => 'dealer_documents/'.$imageName,
                                        'document_id' => !empty($request->document_id[$file_key])?$request->document_id[$file_key]:'0',
                                        'userid' => $request->userid,
                                        'company_id' => $company_id,
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'server_date_time' => date('Y-m-d H:i:s'),
                                    ]);
                        $inc++;

                    }

                  
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
           
            $data['code'] = 200;
            $data['result'] = '';
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    // public function testdatauser(Request $request)
    // {
    //     $pagination = !empty($request->perpage) ? $request->perpage : 10;
    //     $role = $request->role;
    //     $location_6 = $request->location_6;
    //     $app_version = $request->app_version;
    //     $user = $request->user;
    //     $state = $request->state;
    //     $company_id = Auth::user()->company_id;

    //     $q=Person::join('person_details','person_details.person_id','=','person.id','inner')
    //         ->join('person_login','person_login.person_id','=','person.id','inner')
    //         ->join('_role','_role.role_id','=','person.role_id','inner')
    //         ->leftJoin('location_6','location_6.id','=','person.town_id')
    //         ->leftJoin('location_5','location_5.id','=','person.head_quater_id')
    //         ->leftJoin('location_4','location_4.id','=','location_5.location_4_id')
    //         ->leftJoin('location_3','person.state_id','=','location_3.id')
    //         ->select('person_details.created_on as created_on','person_details.address as personaddress','location_6.name as town_name','location_5.name as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
    //             WHERE p1.id=person.person_id_senior LIMIT 1) as srname"))
    //         ->where('person.company_id',$company_id)
    //         ->where('location_3.company_id',$company_id)
    //         ->where('_role.company_id',$company_id)
    //         ->where('person_status','!=','9')
    //         ->where('person.id','!=','1');

    //     #state filter 
    //     if(!empty($state))
    //     {
    //         $q->whereIn('person.state_id',$state);
    //     }
    //     #role filter 
    //     if(!empty($app_version))
    //     {
    //         $q->whereIn('person.version_code_name',$app_version);
    //     }
    //     if(!empty($role))
    //     {
    //         $q->whereIn('_role.role_id',$role);
    //     }
    //     if(!empty($user))
    //     {
    //         $q->whereIn('person.id',$user);
    //     }
    //     if(!empty($location_6))
    //     {
    //         $q->whereIn('location_6.id',$location_6);
    //     }
    //     if(!empty($request->status))
    //     {
    //         if($request->status==2)
    //         {
    //             $q->where('person_login.person_status',0);
    //         }
    //         else
    //         {
    //             $q->where('person_login.person_status',$request->status);
    //         }
    //     }
    //     if(!empty($request->head_quater))
    //     {
    //         $q->whereIn('location_4.id',$request->head_quater);
    //     }

    //     # search functionality
    //     if (!empty($request->search)) {
    //         $key = $request->search;
    //         $q->where(function ($subq) use ($key) {
    //             $subq->where('person.first_name', 'LIKE',  $key . '%');
    //             $subq->orWhere('person.middle_name', 'LIKE',  '%'.$key.'%');
    //             $subq->orWhere('person.last_name', 'LIKE',  '%'.$key);
    //         });
    //     }

    //     $data = $q->orderBy('person.id', 'desc')
    //        ->paginate($pagination);
    //         // ->get();

    //    // $srname=Person::select('person.*')
    //      //   ->where('id',$data->person_id_senior)->first();

    //     #Location 1 data
    //     $location1 = Location1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
    //     $state = Location3::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
    //     $head_quater = Location5::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
    //     $location_6 = Location6::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
    //     $version = DB::table('version_management')->where('company_id',$company_id)->pluck(DB::raw("CONCAT('Version: ',version_name,'/',version_code) as version"),DB::raw("CONCAT('Version: ',version_name,'/',version_code) as version"));
    //     $user = Person::join('users','users.id','=','person.id')
    //         ->join('person_login','person_login.person_id','=','person.id')
    //         ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
    //         ->where('person.company_id',$company_id)
    //         ->where('is_admin','!=',1)
    //         ->where('person_status',1)
    //         ->pluck('name', 'uid');
    //    // dd($user);
    //     // $division = Division::where('status', '=', '1')->where('division_type',1)->where('company_id',$company_id)->pluck('name', 'id');
    //     // dd($division);
    //     $role = _role::orderBy('role_sequence','ASC')->where('status',1)->where('company_id',$company_id)->pluck('rolename', 'role_id');

    //     //  $distributor_details=[];
    //     // if (!empty($data))
    //     // {
    //     //     foreach ($data as $k=>$v)
    //     //     {
    //     //         $uid=$v->id;
    //     //         $data[$k]['details']=DB::table('dealer_location_rate_list')
    //     //         ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
    //     //         ->select('user_id','dealer.id as dealer_id','dealer.name AS dealer_name',DB::raw("count(dealer_location_rate_list.location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT udr.id) FROM retailer AS udr WHERE udr.dealer_id=dealer_location_rate_list.dealer_id LIMIT 1) AS retailer_count"))
    //     //         ->where('dealer_location_rate_list.user_id',$uid)
    //     //         ->where('dealer_location_rate_list.company_id',$company_id)
    //     //         ->where('dealer.company_id',$company_id)
    //     //         ->where('dealer_status',1)
    //     //         ->groupBy('dealer_id','retailer_count')
    //     //         ->get();
    //     //         // $data[$k]['details']=DB::table('dealer_location_rate_list')
    //     //         // ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
    //     //         // ->select('dealer.name AS dealer_name',DB::raw("count(location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT retailer_id) FROM retailer  WHERE dealer_location_rate_list.user_id=$uid AND dealer_location_rate_list.dealer_id=retailer.dealer_id LIMIT 1) AS retailer_count"))
    //     //         // ->where('dealer_location_rate_list.user_id',$uid)
    //     //         // ->groupBy('dealer_name','retailer_count')
    //     //         // ->get();
    //     //     }
    //     // }
    //   // dd($distributor_details);
        

    //     return view('user.testIndex', [
    //         'records' => $data,
    //         'status_table' => $this->status_table,
    //         'table' => $this->table,
    //         'current_menu'=>$this->current_menu,
    //         'location1' => $location1,
    //         'state' => $state,
    //         'role' => $role,
    //         // 'division'=> $division,
    //         'head_quater'=> $head_quater,
    //         // 'distributor_details' => $distributor_details,
    //         'location_6'=> $location_6,
    //         'user'=> $user,
    //         'version'=> $version,
    //     ]);

    // }

    public function get_user_assign_distributor(Request $request)
    {
        $uid = $request->user_id;
        // dd($uid);
        $company_id = Auth::user()->company_id;
        $query=DB::table('dealer_location_rate_list')
                ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                ->select('location_id','user_id','dealer.id as dealer_id','dealer.name AS dealer_name',DB::raw("count(dealer_location_rate_list.location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT udr.id) FROM retailer AS udr WHERE udr.dealer_id=dealer_location_rate_list.dealer_id and retailer_status = 1 LIMIT 1) AS retailer_count"))
                ->where('dealer_location_rate_list.user_id',$uid)
                ->where('dealer_location_rate_list.company_id',$company_id)
                ->where('dealer.company_id',$company_id)
                ->where('dealer_status',1)
                ->where('location_7.status',1)
                ->groupBy('dealer_id')
                ->get();
        $person_details = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id','inner')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image')
                        ->where('person.id',$uid)
                        ->first();
        $beat_id_retailer = DB::table('dealer_location_rate_list')
                            ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                            ->where('dealer_location_rate_list.user_id',$uid)
                            ->where('dealer_location_rate_list.company_id',$company_id)
                            ->where('location_7.status',1)                        
                            ->groupBy('dealer_location_rate_list.location_id')
                            ->pluck('dealer_location_rate_list.location_id');

        $actual_retailer_count = DB::table('retailer')
                                ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
                                ->where('user_id',$uid)
                                ->whereIn('dealer_location_rate_list.location_id',$beat_id_retailer)
                                ->where('dealer_location_rate_list.company_id',$company_id)
                                ->where('retailer.retailer_status',1)
                                ->groupBy('dealer_location_rate_list.dealer_id')
                                ->pluck(DB::raw("COUNT(retailer.id) as count"),'dealer_location_rate_list.dealer_id');


            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($uid,$company_id);
            Session::push('juniordata', $uid);
            $junior_data = Session::get('juniordata');

        // dd($junior_data);
        $user_details = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('location_3','location_3.id','=','person.state_id')
                    ->select('location_3.name as state_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'rolename','person.id as user_id','mobile')
                    ->where('person.company_id',$company_id)
                    ->where('person_status',1)
                    ->whereIn('person.id',$junior_data)
                    ->get();

        if(!empty($query))
        {
            $data['code'] = 200;
            $data['result'] = $query;
            $data['user_name_details'] = $person_details->user_name;
            $data['actual_retailer_count'] = $actual_retailer_count;
            $data['person_image'] = $person_details->person_image;
            $data['junior_Details'] = $user_details;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['user_name_details'] = '';
            $data['person_image'] = '';
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
        
    }
    public function get_beat_details_dealer(Request $request)
    {
        $uid = $request->user_id;
        $dealer_id = $request->dealer_id;
        // dd($uid);
        $company_id = Auth::user()->company_id;
        $query  =  DB::table('dealer_location_rate_list')
                ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')

                ->join('location_6','location_6.id','=','location_7.location_6_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->join('location_3','location_3.id','=','location_4.location_3_id')
             
                ->select('location_7.name as l7_name','dealer_location_rate_list.*','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name')
                ->where('dealer_location_rate_list.user_id',$uid)
                ->where('dealer_location_rate_list.dealer_id',$dealer_id)
                ->where('dealer_location_rate_list.company_id',$company_id)   
                ->where('location_7.company_id',$company_id)   
                ->where('location_6.company_id',$company_id)   
                ->where('location_5.company_id',$company_id)   
                ->where('location_4.company_id',$company_id)   
                ->where('location_3.company_id',$company_id)   
                ->where('location_7.status',1)                     
                ->groupBy('dealer_location_rate_list.location_id')
                ->get();
        $retailer_count_beat = DB::table('retailer')
                        ->where('company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('location_id')
                        ->pluck(DB::raw("COUNT(id) as id"),'location_id');
        if(!empty($query))
        {
            $data['code'] = 200;
            $data['result_data'] = $query;
            $data['retailer_count_beat'] = $retailer_count_beat;
            
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
          
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
        
    }
    public function get_retailer_details(Request $request)
    {
        $uid = $request->user_id;
        $dealer_id = $request->dealer_id;
        // dd($uid);
        $company_id = Auth::user()->company_id;
        $query  =  DB::table('dealer_location_rate_list')
                ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                ->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
                ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                ->select('dealer.name as dealer_name','retailer.name as retailer_name','location_7.name as l7_name')
                ->where('dealer_location_rate_list.user_id',$uid)
                ->where('dealer_location_rate_list.dealer_id',$dealer_id)
                ->where('dealer_location_rate_list.company_id',$company_id)   
                ->where('location_7.status',1)                     
                ->where('retailer_status',1)                     
                ->where('dealer_status',1)                     
                ->groupBy('retailer.id')
                ->get();
        if(!empty($query))
        {
            $data['code'] = 200;
            $data['result_data'] = $query;
            
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
          
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = Auth::user()->company_id;
        $roles=DB::table('_role')->where('company_id',$company_id)->where('status',1)->orderBy('role_sequence','ASC')->pluck('rolename','role_id');

        $company=Company::where('id',$company_id)->pluck('name','id');

        $state=Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6=Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $head_quater = Location5::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
         $product_rate_list_assign_part = DB::table('app_other_module_assign')
                                        ->where('company_id',$company_id)
                                        ->where('module_id',5)
                                        ->get();
        // $division = Division::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        $user=DB::table('person')->where('company_id',$company_id)->where('id','!=',1)->orderBy('id','desc')->take(10)->get();
        return view('user.create',[
            'current_menu'=>$this->current_menu,
            'user'=>$user,
            'roles'=>$roles,
            // 'division'=>$division,
            'company' => $company,
            'head_quater'=> $head_quater,
            'product_rate_list_assign_part'=> $product_rate_list_assign_part,
            'state' => $state,
            'location_6'=> $location_6,
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
        $validate = $request->validate([
            'first_name' => 'required|min:2|max:20',
           
            'last_name' => 'required|min:2|max:20',
            'designation' => 'required',
            'email' => 'required|unique:users|min:2|max:40',
            'password' => 'required|min:2|max:20',
            'senior_person' => 'required',
            'state' => 'required',
            
            'company' => 'required',
            'mobile' => 'required|min:10|max:10'
        ]);

        # for make user grouping on the bhelaf of company 
        $company_id = Auth::user()->company_id;
        $company_name_data = Company::where('id',$company_id)->first();
        $company_name = $company_name_data->name;

        $check = DB::table('person_login')
                ->where('person_username',trim(str_replace('@','_',$request->email).'@'.$company_name))
                ->where('company_id',$company_id)
                ->count();
        if($check>0)
        {
            Session::flash('message', 'User Already Exist!!');
            Session::flash('class', 'danger');
            return redirect()->intended($this->current_menu);
        }
        $check2 = DB::table('person_login')
                ->where('person_username',trim(($request->email).'@'.$company_name))
                ->where('company_id',$company_id)
                ->count();
        if($check2>0)
        {
            Session::flash('message', 'User Already Exist!!');
            Session::flash('class', 'danger');
            return redirect()->intended($this->current_menu);
        }
        // dd($company_name);
        $myArr = [
            'first_name' => trim(ucfirst($request->first_name)),
            'middle_name' => trim(ucfirst($request->middle_name)),
            'last_name' => trim(ucfirst($request->last_name)),
            'role_id' => trim($request->designation),
            'person_id_senior' => trim($request->senior_person),
            'version_code_name' => '',
            'resigning_date' => date('Y-m-d'),
            'head_quater_id' =>trim($request->head_quater),
            'weekly_off_data' =>trim($request->weekly),
            'mobile' => trim($request->mobile),
            'email' => trim($request->email_o),
            'state_id' => trim($request->state),
            'town_id'=> trim($request->location_6),
            'emp_code' => trim($request->emp_code),
            'company_id' => $company_id,
            'rate_list_flag'=> !empty($request->rate_list_flag)?$request->rate_list_flag:'1',
            'joining_date' => trim($request->joining_date),
            'created_by' => Auth::user()->id,
            'status' => $request->status
        ];


        $person=Person::create($myArr);

        $myArr2=[
            'person_id'=>$person->id,
            'address'=>trim($request->address),
            'residential_lat_lng'=>$request->residential_lat_lng,
            'company_id' => $company_id,
            'gender'=>'M',
            'created_on'=>date('Y-m-d H:i:s')
        ];
        $person2=PersonDetail::create($myArr2);

        $myArr3=[
            'person_id'=>$person->id,
            'emp_id'=>trim($request->emp_code),
            'company_id' => $company_id,
            'person_username'=>trim(str_replace('@','_',$request->email).'@'.$company_name),
            'person_password'=>DB::raw("AES_ENCRYPT('".trim($request->password)."', '".Lang::get('common.db_salt')."')"),
            'person_status'=>$request->status
        ];
        $person3=PersonLogin::create($myArr3);
        $myArr4=[
            'id'=>$person->id,
            'role_id'=>trim($request->designation),
            'email'=>trim(str_replace('@','_',$request->email).'@'.$company_name),
            'password'=>bcrypt(trim($request->password)),
            'company_id' => $company_id,
            'original_pass'=>$request->password,
            'status'=>1,
            'created_at'=>date('Y-m-d H:i:s'),

        ];
        $person4=User::create($myArr4);
        if ($person && $person3 && $person2 && $person4) {
            DB::commit();
            Session::flash('message', 'User created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        //you can use this code for upload image to `public/storage/ directory :

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = PersonLogin::where('person_id',$person->id)->update(['person_image' => 'users-profile/'.$name]);

                    $request->file('imageFile')->move("users-profile", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }

        return redirect()->intended($this->current_menu);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {    

        if(!empty($request->date_range_picker)){
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        }else{
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        }
        #decrypt id
        $uid = Crypt::decryptString($id);
        // dd($uid);
        $company_id = Auth::user()->company_id;
        $date = !empty($request->date)?$request->date:date("Y-m-d");

        // $month_date = date('Y-m',strtotime($date));
        $month_date = date('Y-m',strtotime($from_date));


        $q=Person::join('person_details','person_details.person_id','=','person.id','inner')
            ->join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            ->select('person.*','person_login.person_username','person_login.person_password','person_login.person_image','_role.rolename',DB::raw("DATE_FORMAT(last_mobile_access_on,'%d-%m-%Y% %H:%i:%s') last_mobile_access_on"))
            ->where('person.id',$uid)
            ->where('person.company_id',$company_id)
            ->where('person_login.company_id',$company_id)
            ->where('_role.company_id',$company_id)
            ->where('person_details.company_id',$company_id)
            ->first();

        $senior_person=Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_name"))
            ->where('id',$q->person_id_senior)->where('company_id',$company_id)->first();

        $state=Person::join('location_3','location_3.id','=','person.state_id','inner')
            ->select('location_3.*')
            ->where('location_3.id',$q->state_id)
            ->where('person.company_id',$company_id)
            ->where('location_3.company_id',$company_id)
            ->first();

        $attendance=Person::join('user_daily_attendance','user_daily_attendance.user_id','=','person.id','inner')
            ->selectRaw('COUNT(user_id) as attd')
            ->where('user_id',$uid)
            // ->whereRaw(" DATE_FORMAT(work_date,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(work_date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<= '$to_date'")
            ->where('user_daily_attendance.company_id',$company_id)
            ->where('person.company_id',$company_id)
            ->first();

        // $day_count=date('t');
        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }
        $day_count = count($datearray);
        // $day_count=date('t');


        $attd_per=ROUND(($attendance->attd/$day_count)*100);


        $attd_per=ROUND(($attendance->attd/$day_count)*100);



        $pc=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
            // ->selectRaw('COUNT(user_id) as pc')
            ->selectRaw('COUNT(DISTINCT retailer_id,date) as pc')
            ->where('user_id',$uid)
            ->where('call_status','1')
            ->where('person.company_id',$company_id)
            ->where('user_sales_order.company_id',$company_id)
            // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
            ->first();


        $npc=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
            ->selectRaw('COUNT(DISTINCT retailer_id,date) as npc')
            ->where('user_id',$uid)
            ->where('call_status','0')
            ->where('person.company_id',$company_id)
            ->where('user_sales_order.company_id',$company_id)
            // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
            ->first();

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

         if(empty($check)){
        $tv=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            // ->selectRaw('SUM(total_sale_value) as tv')
            ->selectRaw(DB::raw('SUM(user_sales_order_details.rate*user_sales_order_details.quantity) as tv'))
            ->where('user_id',$uid)
            ->where('person.company_id',$company_id)
            ->where('user_sales_order.company_id',$company_id)
            // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
            ->first();
        }else{
        $tv=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            // ->selectRaw('SUM(total_sale_value) as tv')
            ->selectRaw(DB::raw('SUM(user_sales_order_details.final_secondary_rate*user_sales_order_details.final_secondary_qty) as tv'))
            ->where('user_id',$uid)
            ->where('person.company_id',$company_id)
            ->where('user_sales_order.company_id',$company_id)
            // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
            ->first();
        }

        $unique_sku_billed = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->selectRaw('COUNT(DISTINCT product_id) as unique_sku')
                        ->where('user_id',$uid)
                        ->where('user_sales_order.company_id',$company_id)
                        ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
                        ->first();
  

        $nr=Person::join('retailer','retailer.created_by_person_id','=','person.id','inner')
            ->selectRaw('COUNT(retailer.id) as nr')
            ->where('created_by_person_id',$uid)
            ->where('person.company_id',$company_id)
            ->where('retailer.company_id',$company_id)
            // ->whereRaw(" DATE_FORMAT(created_on,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(created_on,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(created_on,'%Y-%m-%d')<= '$to_date'")
            ->first();
        
        $toc=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
            ->selectRaw('COUNT(DISTINCT retailer_id,date) as toc')
            ->where('person.company_id',$company_id)
            ->where('user_sales_order.company_id',$company_id)
            ->where('user_id',$uid)
            // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
            ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
            ->first(); 

        $last =  explode("-",date($month_date.'-t'));
         
        $lastdate = $last[2];
        $monthex = explode("-",$month_date);
        $table = array();
        $table['cols'] = array(

            array('label' => 'date', 'type' => 'string'),
            array('label' => 'Target', 'type' => 'number'),
           array('label' => 'Achieved', 'type' => 'number'),

        );
        $rows = array();
        $qtar=Person::join('monthly_tour_program','monthly_tour_program.person_id','=','person.id','inner')
                    ->selectRaw('SUM(rd) as target')
                    ->where('person_id',$uid)
                    ->where('person.company_id',$company_id)
                    ->where('monthly_tour_program.company_id',$company_id)
                    // ->whereRaw(" DATE_FORMAT(working_date,'%Y-%m')= '$month_date'")
                    ->whereRaw(" DATE_FORMAT(working_date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<= '$to_date'")
                    ->first();

                    
        $target = $qtar->target;
        //die($target);
        $daytarget = $target/$lastdate;
        $curr = date("d");
        $currmonth = date("m");
        $achieved_total = 0;
        for($i=1;$i<=$lastdate;$i++)
        {
            if($i<10)
            {
            $ac_date =  $month_date."-0".$i;
            }
            else
            {
              $ac_date =  $month_date."-".$i;  
            }

            $qach=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
                    ->selectRaw('SUM(total_sale_value) as achieved')
                    ->where('user_id',$uid)
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
                    ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
                    ->first();


           $achieved = $qach->achieved;
           $achieved_total = $achieved_total +$achieved;

     
            $temp = array();
            // the following line will used to slice the Pie chart
            $temp[] = array('v' => (string) $i); 
            $temp[] = array('v' => (float) $target); 
           // echo $monthex[1];
           // echo "ANKUSh<br/>".$currmonth;
            if($curr >= $i && $currmonth == $monthex[1])
            {
                $temp[] = array('v' => (float) $achieved_total);
            }
            else if($currmonth != $monthex[1])
            {
                $temp[] = array('v' => (float) $achieved_total);    
            }
            $rows[] = array('c' => $temp);
        }


        $table['rows'] = $rows;
        $jsonTable = json_encode($table);   

        $device_info=Person::join('user_mobile_details','user_mobile_details.user_id','=','person.id','inner')
                ->select('device_name','device_manuf','device_version',DB::raw("DATE_FORMAT(server_date_time,'%d-%m-%Y% %H:%i:%s') server_date_time"))
                ->where('user_mobile_details.company_id',$company_id)
                ->where('person.company_id',$company_id)
                ->orderBy('user_mobile_details.id','DESC')
                ->where('user_id',$uid)->first();

                // $today=$date;
            $today=date('Y-m-d');


        $today_visit = DB::table('monthly_tour_program')
            ->join('dealer','dealer.id','=','monthly_tour_program.dealer_id')
            ->join('location_5','monthly_tour_program.locations','=','location_5.id')
            ->join('_working_status','monthly_tour_program.working_status_id','=','_working_status.id')
            ->select('dealer.name AS dealer_name','location_5.name AS beat','_working_status.name AS working_status','monthly_tour_program.rd')
            ->where('person_id',$uid)
            ->where('dealer.company_id',$company_id)
            ->where('location_5.company_id',$company_id)
            ->where('_working_status.company_id',$company_id)
            ->whereRaw(" DATE_FORMAT(working_date,'%Y-%m-%d')= '$today'")->first();

        $today_booking = DB::table('user_sales_order')
             ->select(DB::raw("sum(total_sale_value) AS total_sale_value,sum(total_sale_qty) AS total_sale_qty,(select count(call_status) FROM user_sales_order AS u1 WHERE u1.user_id=user_sales_order.user_id AND call_status=1 AND u1.date=user_sales_order.date) AS pc,(select count(call_status) FROM user_sales_order AS u1 WHERE u1.user_id=user_sales_order.user_id AND call_status=0 AND u1.date=user_sales_order.date) AS npc"))
            ->where('user_id',$uid)
            ->where('company_id',$company_id)
            ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')= '$today'")
            ->groupBy('user_id','date')->first();

            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($uid,$company_id);
            Session::push('juniordata', $uid);
            $junior_data = Session::get('juniordata');

        // dd($junior_data);
        $user_details = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'rolename','person.id as user_id')
                    ->where('person.company_id',$company_id)
                    ->where('person_status',1)
                    ->whereIn('person.id',$junior_data)
                    ->get();
        $beat_count = DB::table('dealer_location_rate_list')
                    ->whereIn('user_id',$junior_data)
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->groupBy('user_id')
                    ->pluck(DB::raw("COUNT(DISTINCT location_id) as beat"),'user_id');
        $dealer_count = DB::table('dealer_location_rate_list')
                    ->whereIn('user_id',$junior_data)
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->groupBy('user_id')
                    ->pluck(DB::raw("COUNT(DISTINCT dealer_id) as dealer"),'user_id');

        $work_status = DB::table('_working_status')
                        ->select('id','name','color_status')
                        ->where('company_id',$company_id)
                        ->get();


   

        $work_status_attendance = DB::table('user_daily_attendance')
        ->where('user_id',$uid)
        ->where('company_id',$company_id)
        ->whereRaw(" DATE_FORMAT(work_date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<= '$to_date'")
        ->groupBy('user_daily_attendance.work_status')
        ->pluck(DB::raw("COUNT(user_id) as attd"),'work_status as count');

        $assign_beat = DB::table('dealer_location_rate_list')
                        ->where('company_id', $company_id)
                        ->where('user_id',$uid)
                        ->selectRaw('COUNT(DISTINCT location_id) as assign_beat')
                        ->first();
    //   dd($assign_beat);

        

        return view('user.view',[
            'user'=>$q,
            'id'=>$id,
            'senior_user_id'=> $uid,
            'dashboard_user_id'=> $uid,
            'from_date'=>$from_date,
            'to_date'=>$to_date,
            'date'=>$date,
            'senior_person'=>$senior_person,
            'state'=>$state,
            'attendance'=>$attendance,
            'attd_per'=>$attd_per,
            'pc'=>$pc,
            'npc'=>$npc,
            'tv'=>$tv,
            'nr'=>$nr,
            'jsonTable'=>$jsonTable,
            'device_info'=>$device_info,
            'today_visit'=>$today_visit,
            'today_booking'=>$today_booking,
            'outlet_coverage'=>$toc,
            'user_details'=>$user_details,
            'beat_count'=> $beat_count,
            'dealer_count'=> $dealer_count,
            'unique_sku_billed'=> $unique_sku_billed,
            'work_status'=> $work_status,
            'work_status_attendance'=> $work_status_attendance,
            'day_count'=>$day_count,
            'assign_beat'=>$assign_beat,
            
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        #decrypt id
        $company_id = Auth::user()->company_id;

        $uid = Crypt::decryptString($id);

        $person=Person::findOrFail($uid);

        $personDetails=PersonDetail::where('person_id',$uid)->where('company_id',$company_id)->first();

        $personLogin=PersonLogin::select(DB::raw("person_username,person_image,AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"))
            ->where('company_id',$company_id)
            ->where('person_id',$uid)->first();

        $company=Company::where('id',$company_id)->pluck('name','id');

        $state=Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6=Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');

        $head_quater = Location5::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        // $division = Division::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        $person_id_senior=$person->person_id_senior;

        $senior=Person::select(DB::raw('CONCAT_WS(" ",first_name,middle_name,last_name) as senior_name'),'person.*')->where('company_id',$company_id)->find($person_id_senior);

        $roles=DB::table('_role')->where('company_id',$company_id)->orderBy('role_sequence','ASC')->pluck('rolename','role_id');

        $user=DB::table('person')->where('company_id',$company_id)->where('id','!=',1)->orderBy('id','desc')->take(10)->get();

        $product_rate_list_assign_part = DB::table('app_other_module_assign')
                                        ->where('company_id',$company_id)
                                        ->where('module_id',5)
                                        ->get();
                                        // dd($product_rate_list_assign_part);
        return view('user.edit',[
            'current_menu'=>$this->current_menu,
            'user'=>$user,
            'roles'=>$roles,
            'person'=>$person,
            'encrypt_id' => $id,
            'company' => $company,
            // 'division'=>$division,
            'state' => $state,
            'senior' => $senior,
            'head_quater'=> $head_quater,
            'personDetails'=>$personDetails,
            'personLogin'=>$personLogin,
            'location_6'=> $location_6,
            'product_rate_list_assign_part'=> $product_rate_list_assign_part,
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
        #decrypt id
        $company_id = Auth::user()->company_id;
        $company_name_data = Company::where('id',$company_id)->first();
        $company_name = $company_name_data->name;

        $uid = Crypt::decryptString($id);

        $validate = $request->validate([
            'first_name' => 'required|min:2|max:20',
            'middle_name' => '  max:20',
            'last_name' => 'required|min:2|max:20',
            'email' => 'unique:users,email,'.$uid,
            'designation' => 'required',
            'senior_person' => 'required',
            'state' => 'required',
            'company' => 'required',
            'location_6'=>'required',
            'mobile' => 'required|min:10|max:10'
        ]);

        $myArr = [
            'first_name' => trim(ucfirst($request->first_name)),
            'middle_name' => trim(ucfirst($request->middle_name)),
            'last_name' => trim(ucfirst($request->last_name)),
            'role_id' => trim($request->designation),
            'person_id_senior' => trim($request->senior_person),
            'head_quater_id' =>trim($request->head_quater),
            'weekly_off_data' =>trim($request->weekly),
            'mobile' => trim($request->mobile),
            'email' => trim($request->email_o),
            'state_id' => trim($request->state),
            'emp_code' => trim($request->emp_code),
            'rate_list_flag'=> !empty($request->rate_list_flag)?$request->rate_list_flag:'1',
            'town_id' => trim($request->location_6),
            'company_id' => trim($request->company),
            'joining_date' => trim($request->joining_date),
//            'created_by' => Auth::user()->id,
            'status' => $request->status
        ];
        $uidAr = array($uid);
        $check = DB::table('person_login')
                ->where('person_username',trim(str_replace('@','_',$request->email).'@'.$company_name))
                ->whereNotIn('person_id',$uidAr)
                ->where('company_id',$company_id)
                ->count();
        if($check>0)
        {
            Session::flash('message', 'User Already Exist!!');
            Session::flash('class', 'danger');
            return redirect()->intended($this->current_menu);
        }
        $check2 = DB::table('person_login')
                ->where('person_username',trim(($request->email).'@'.$company_name))
                ->whereNotIn('person_id',$uidAr)
                ->where('company_id',$company_id)
                ->count();
        if($check2>0)
        {
            Session::flash('message', 'User Already Exist!!');
            Session::flash('class', 'danger');
            return redirect()->intended($this->current_menu);
        }
        $check=self::mappiny_recursive($uid,$company_id,$uid);
        if($check == 1)
        {
            Session::flash('message', 'Not Update Please Map right Senior');
            Session::flash('class', 'success');
            return redirect()->intended($this->current_menu);

        }
        // dd($check);  
        $person=Person::find($uid);
        $person->update($myArr);

        $myArr2=[
            'person_id'=>$uid,
            'address'=>trim($request->address),
            'residential_lat_lng'=>$request->residential_lat_lng,
            'gender'=>'M',
        ];
        $person2=PersonDetail::where('person_id',$uid)->where('company_id',$company_id)->update($myArr2);

        $myArr3=[
           // 'person_id'=>$uid,
            'emp_id'=>trim($request->emp_code),
            'person_username'=>trim(str_replace('@','_',$request->email).'@'.$company_name),
            'person_password'=>DB::raw("AES_ENCRYPT('".trim($request->password)."', '".Lang::get('common.db_salt')."')"),
            'person_status'=>$request->status
        ];
        $person3=PersonLogin::where('person_id',$uid)->where('company_id',$company_id)->update($myArr3);

        $myArr4=[
            'id'=>$person->id,
            'email'=>trim(str_replace('@','_',$request->email).'@'.$company_name),
            'role_id'=>trim($request->designation),
            'password'=>bcrypt(trim($request->password)),
            'original_pass'=>trim($request->password),
            'status'=>1,
            'updated_at'=>date('Y-m-d H:i:s'),
        ];
        $person4=User::where('id',$uid)->where('company_id',$company_id)->update($myArr4);

        //you can use this code for upload image to `public/storage/ directory :

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = PersonLogin::where('person_id',$uid)->where('company_id',$company_id)->update(['person_image' => 'users-profile/'.$name]);

                    $request->file('imageFile')->move("users-profile", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }

        if ($person) {
            DB::commit();
            Session::flash('message', 'User updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);
    }
    public  function mappiny_recursive($code,$company_id,$static)
    {
        $res1="";
        $res2="";
        // dd($code);
        // dd($code);
        // $array[] = $code; 
        $details = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select('person.person_id_senior as user_id','person.company_id as p_company_id')
            ->where('person.id',$code)
            ->where('person.company_id',$company_id)
            ->where('person_login.company_id',$company_id)
            ->where('person_id_senior','!=',0)
            ->where('is_admin',0)
            ->where('person_status',1)
            ->get();
            // dd($details);
            $num = count($details);  
            if($num>0)
            {

                foreach($details as $key=>$res2)
                {
                    if($res2->user_id == $static)
                    {
                        // dd('1');
                       return 1;
                       
                        
                    }
                    else
                    {
                        Self::mappiny_recursive($res2->user_id,$res2->p_company_id,$static);


                    }
                }
                
            }
            else
            {
                return 0;
            }
    
            // dd(Session::get('juniordata'))
            
            return 0;
    }
    // public function mtpEnable(Request $request)
    // {
    //     $id = $request->user_id;
    //     $user_id = Crypt::decryptString($id);
    //     $mtp_enable = $request->mtp_enable;
    //     $attandence_mtp = $request->attandence_mtp;
        
    //     DB::beginTransaction();
    //     $myArr2 = [
    //         'is_mtp_enabled' => $mtp_enable
    //     ];
    //     $myArr = [
    //         'user_id' => $user_id,
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'is_enabled' => $attandence_mtp
    //     ];
    //     if(!empty($myArr2))
    //     {
    //     $mtp_query = Person::where('id',$user_id)->update($myArr2);
    //     }
    //     if(!$mtp_query)
    //     {
    //         DB::rollback();
    //         Session::flash('message', 'Something went wrong!');
    //         Session::flash('class', 'danger');

    //     }
    //     if(!empty($myArr))
    //     {
    //         $mtp_log_query = UserTodaysAttendanceEnabledLog::create($myArr);     
    //     }
    //     if(!$mtp_log_query)
    //     {
    //         DB::rollback();
    //         Session::flash('message', 'Something went wrong!');
    //         Session::flash('class', 'danger');
            
    //     }
     
    //     DB::commit();
    //     Session::flash('message', 'success');
    //     Session::flash('class', 'success');
    //     return redirect()->intended($this->current_menu);
    // }

    
    public function EnableAction(Request $request)
    {

        $company_id = Auth::user()->company_id;
        $user_id = $request->action_id;
        $module = $request->module;
        $table = $request->tab;
        $act = $request->act;
        if($act == 'Enable')
        {
            $action = 1;
        }
        elseif($act == 'Disable')
        {
            $action = 2;
        }
    

        if ($request->ajax() && !empty($user_id)) {
            #module based action
            DB::beginTransaction();
            if ($module == 'Today-Enable') # specific action for user module
            {
                #update user table status
                // dd($action);
                  $myArr = [
                    'user_id' => $user_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'company_id' => $company_id,
                    'is_enabled' => $action
                ];
                if(!empty($myArr))
                {   
                    $query = UserTodaysAttendanceEnabledLog::create($myArr); 


                }
                if($query->count()>0)
                {
                    $myArr2= ['today_att_enabled' => $action,'today_att_enabled_at' => date('Y-m-d')];
             
                    $query = Person::where('id',$user_id)->where('company_id',$company_id)->update($myArr2);
                    
                }

            } elseif($module = 'Mtp-Enable') {
                $myArr2 = [
                    'is_mtp_enabled' => $action,
                ];
                $query = Person::where('id',$user_id)->where('company_id',$company_id)->update($myArr2);

            }
            if (!empty($query)) {
                #commit transaction
                
                DB::commit();
                $data['code'] = 200;
                $data['result'] = 'success';
                $data['message'] = 'success';
            } else {
                #rollback transaction
                DB::rollback();
                $data['code'] = 401;
                $data['result'] = 'fail';
                $data['message'] = 'Action can not be completed';
            }
        } else {
            #for unauthorized request
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

        #delete dealer on user starts here 
    public function deleteAction(Request $request)
    {
        $dealer_id = $request->action_id;
        $user_id = $request->user_id;
        // dd($dealer_id);   
        DB::beginTransaction();
        
        $beat_id = DB::table('dealer_location_rate_list')
                    ->select('location_id')
                    ->where('dealer_id',$dealer_id)
                    ->where('user_id',$user_id)
                    ->get();
                    
        $dealer_delete_query = DB::table('dealer_location_rate_list')
                            ->where('dealer_id',$dealer_id)
                            ->where('user_id',$user_id)
                            ->delete();

        if($dealer_delete_query)
        {
            
            foreach ($beat_id as $key => $value) 
            {
                $delete_log  = DB::table('dealer_location_rate_list_log')
                        ->insert(['dealer_id' => $dealer_id,
                                   'user_id' => $user_id,
                                   'location_id'=>$value->location_id,
                                   'delete_date' => date("Y-m-d H:i:s"), 
                            ]);
            }
            if($delete_log)
            {
                DB::commit();
                $data['code'] = 200;
                $data['result'] = 'success';
                $data['message'] = 'success';

            }
            
            
        }

        else {
            #for unauthorized request
            DB::rollback();
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
    }

    // for holiday
    public function EnableHolidayAction(Request $request)
    {

        $company_id = Auth::user()->company_id;
        $updated_by = Auth::user()->id;
        $updated_at = date('Y-m-d H:i:s');
        $user_id = $request->action_id;
        $module = $request->module;
        $table = $request->tab;
        $act = $request->act;
        if($act == 'Enable')
        {
            $action = 1;
        }
        elseif($act == 'Disable')
        {
            $action = 2;
        }
    

        if ($request->ajax() && !empty($user_id)) {
            #module based action
            DB::beginTransaction();
            
                $myArr2 = [
                    'is_holiday_enabled' => $action,
                    'is_holiday_updated_by' => $updated_by,
                    'is_holiday_updated_at' => $updated_at,
                ];
                $query = Person::where('id',$user_id)->where('company_id',$company_id)->update($myArr2);

            if (!empty($query)) {
                #commit transaction
                
                DB::commit();
                $data['code'] = 200;
                $data['result'] = 'success';
                $data['message'] = 'success';
            } else {
                #rollback transaction
                DB::rollback();
                $data['code'] = 401;
                $data['result'] = 'fail';
                $data['message'] = 'Action can not be completed';
            }
        } else {
            #for unauthorized request
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    // for holiday ends
    # delete dealer ends here 
    public function get_immediate_junior_list(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $uid = $request->user_id;
        // dd($uid);
        $person_details = array();
        $junior_list = Person::join('users','users.id','=','person.id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->select(DB::raw("CONCAT(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id')
                    ->where('person_status',1)
                    ->where('person_id_senior',$uid)
                    ->where('is_admin',0)
                    ->where('person.company_id',$company_id)
                    ->get()->toArray();
        // dd($junior_list);
        $person_details = Person::join('users','users.id','=','person.id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select(DB::raw("CONCAT(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id')
                        ->where('person_status',1)
                        ->where('is_admin',0)
                        ->where('person.company_id',$company_id)
                        ->get()->toArray();
        // dd($person_details);
        $role_filter = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id')->toArray();
        // dd($role_filter);
        if(COUNT($junior_list)>0)
        {
            $data['code'] = 200;
            $data['result'] = $junior_list;
            $data['person'] = $person_details;
            $data['role'] = $role_filter;
            $data['message'] = 'Success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
        
    }

    public function junior_list_submit_for_new_user(Request $request)
    {
        // dd($request);
        $juniors_user_id = $request->junior_id;
        $assign_user_id = $request->assign_user_id;
        $delete_user_id = $request->delete_user_id;
        $user_id = Auth::user()->id;
        $company_id = Auth::user()->company_id;

        foreach ($juniors_user_id as $key => $value) 
        {
            $insert_Arr = 
            [
                'person_id_senior' => $assign_user_id[$key]
            ];
            $person_update = Person::where('person.id',$value)->where('company_id',$company_id)->update($insert_Arr);
        }
        if($person_update)
        {
            $insert_in_person_details = PersonDetail::where('company_id',$company_id)->where('person_id',$delete_user_id)->update(['deleted_deactivated_on'=>date('Y-m-d H:i:s')]);

            $change_status = PersonLogin::where('company_id',$company_id)->where('person_id',$delete_user_id)->update(['person_status'=>9]);

            if($insert_in_person_details && $change_status)
            {
                DB::commit();
                Session::flash('message', 'User Delete successfully');
                Session::flash('class', 'success');
            }
            else
            {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
            }

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);

    }
    public function junior_list_assign_to_senior(Request $request)
    {
        // dd($request);
        $senior_user_id = $request->senior_user_id;
        $user_id = $request->user_id;
        $company_id = Auth::user()->company_id;
        
        $not_beat = DB::table('dealer_location_rate_list')
                    ->where('user_id',$senior_user_id)
                    ->where('company_id',$company_id)
                    ->pluck('location_id')->toArray();

        $dealer = DB::table('dealer_location_rate_list')
                    ->where('user_id',$senior_user_id)
                    ->where('company_id',$company_id)
                    ->pluck('dealer_id')->toArray();

        $user_data = DB::table('dealer_location_rate_list')
                 ->whereIn('user_id',$user_id)
                 ->whereNotIn('dealer_id',$dealer)
                 ->whereNotIn('location_id',$not_beat)
                 ->groupBy('dealer_id','location_id')
                 ->where('company_id',$company_id)
                 ->get();
        $myArr = [];
        if(!empty($user_data))
        {
            foreach ($user_data as $key => $value) 
            {
                $myArr = [
                    'dealer_id' => $value->dealer_id,
                    'user_id' => $senior_user_id,
                    'location_id' => $value->location_id,
                    'company_id' => $company_id,
                    'server_date' => date('Y-m-d H:i:s'),

                ];
                
            }
            // dd($myArr);
             $insert_query = DB::table('dealer_location_rate_list')->insert($myArr);
             if($insert_query)
             {
                DB::commit();
                Session::flash('message', 'User Assign successfully');
                Session::flash('class', 'success');
             }
             else
             {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
             }
        }
        else
        {
            Session::flash('message', 'Already Assign!');
            Session::flash('class', 'danger');
        }
        return redirect()->intended($this->current_menu);

        

    }

    public function delete_user_details(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $role = $request->role;
        $location_6 = $request->location_6;
        $app_version = $request->app_version;
        $user = $request->user;
        $state = $request->state;
        $company_id = Auth::user()->company_id;

        $q=Person::join('person_details','person_details.person_id','=','person.id','inner')
            ->join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            ->leftJoin('location_6','location_6.id','=','person.town_id')
            ->leftJoin('location_5','location_5.id','=','person.head_quater_id')
            ->leftJoin('location_4','location_4.id','=','location_5.location_4_id')
            ->leftJoin('location_3','person.state_id','=','location_3.id')
            ->select('person_details.created_on as created_on','person_details.address as personaddress','location_6.name as town_name','location_5.name as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
                WHERE p1.id=person.person_id_senior LIMIT 1) as srname"))
            ->where('person.company_id',$company_id)
            ->where('location_3.company_id',$company_id)
            ->where('_role.company_id',$company_id)
            ->where('person_status','=','9')
            ->where('person.id','!=','1');

        #state filter 
        if(!empty($state))
        {
            $q->whereIn('person.state_id',$state);
        }
        #role filter 
        if(!empty($app_version))
        {
            $q->whereIn('person.version_code_name',$app_version);
        }
        if(!empty($role))
        {
            $q->whereIn('_role.role_id',$role);
        }
        if(!empty($user))
        {
            $q->whereIn('person.id',$user);
        }
        if(!empty($location_6))
        {
            $q->whereIn('location_6.id',$location_6);
        }
        if(!empty($request->status))
        {
            if($request->status==2)
            {
                $q->where('person_login.person_status',0);
            }
            else
            {
                $q->where('person_login.person_status',$request->status);
            }
        }
        if(!empty($request->head_quater))
        {
            $q->whereIn('location_4.id',$request->head_quater);
        }

        # search functionality
        if (!empty($request->search)) {
            $key = $request->search;
            $q->where(function ($subq) use ($key) {
                $subq->where('person.first_name', 'LIKE',  $key . '%');
                $subq->orWhere('person.middle_name', 'LIKE',  '%'.$key.'%');
                $subq->orWhere('person.last_name', 'LIKE',  '%'.$key);
            });
        }

        $data = $q->orderBy('person.id', 'desc')
           ->paginate($pagination);
            // ->get();

       // $srname=Person::select('person.*')
         //   ->where('id',$data->person_id_senior)->first();

        #Location 1 data
        $location1 = Location1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        $state = Location3::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        $location_6 = Location6::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        $version = DB::table('version_management')->where('company_id',$company_id)->pluck(DB::raw("CONCAT('Version: ',version_name,'/',version_code) as version"),DB::raw("CONCAT('Version: ',version_name,'/',version_code) as version"));
        $user = Person::join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->where('person.company_id',$company_id)
            ->where('is_admin','!=',1)
            ->where('person_status',1)
            ->pluck('name', 'uid');
       // dd($user);
        // $division = Division::where('status', '=', '1')->where('division_type',1)->where('company_id',$company_id)->pluck('name', 'id');
        // dd($division);
        $role = _role::orderBy('role_sequence','ASC')->where('status',1)->where('company_id',$company_id)->pluck('rolename', 'role_id');

        $product_rate_list_assign_part = DB::table('app_other_module_assign')
                                        ->where('company_id',$company_id)
                                        ->get();
            // dd($product_rate_list_assign_part);

        //  $distributor_details=[];
        // if (!empty($data))
        // {
        //     foreach ($data as $k=>$v)
        //     {
        //         $uid=$v->id;
        //         $data[$k]['details']=DB::table('dealer_location_rate_list')
        //         ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
        //         ->select('user_id','dealer.id as dealer_id','dealer.name AS dealer_name',DB::raw("count(dealer_location_rate_list.location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT udr.id) FROM retailer AS udr WHERE udr.dealer_id=dealer_location_rate_list.dealer_id LIMIT 1) AS retailer_count"))
        //         ->where('dealer_location_rate_list.user_id',$uid)
        //         ->where('dealer_location_rate_list.company_id',$company_id)
        //         ->where('dealer.company_id',$company_id)
        //         ->where('dealer_status',1)
        //         ->groupBy('dealer_id','retailer_count')
        //         ->get();
        //         // $data[$k]['details']=DB::table('dealer_location_rate_list')
        //         // ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
        //         // ->select('dealer.name AS dealer_name',DB::raw("count(location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT retailer_id) FROM retailer  WHERE dealer_location_rate_list.user_id=$uid AND dealer_location_rate_list.dealer_id=retailer.dealer_id LIMIT 1) AS retailer_count"))
        //         // ->where('dealer_location_rate_list.user_id',$uid)
        //         // ->groupBy('dealer_name','retailer_count')
        //         // ->get();
        //     }
        // }
     //  dd($distributor_details);
        

        return view('user.delete', [
            'records' => $data,
            'status_table' => $this->status_table,
            'table' => $this->table,
            'current_menu'=>$this->current_menu,
            'location1' => $location1,
            'state' => $state,
            'role' => $role,
            // 'division'=> $division,
            'head_quater'=> $head_quater,
            // 'distributor_details' => $distributor_details,
            'location_6'=> $location_6,
            'user'=> $user,
            'version'=> $version,
            'product_rate_list_assign_part'=> $product_rate_list_assign_part,
        ]);

    }


      public function getUserProductiveCallModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $company_id = Auth::user()->company_id;



        $user_details = DB::table('user_sales_order')
                            ->select(DB::raw('COUNT(DISTINCT retailer_id) as pc'),'user_sales_order.date')
                            ->where('user_id',$user_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->where('call_status','1')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->groupBy('date')
                            ->get();




         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['date'] = $value->date;
                $out['pc'] = $value->pc;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }


    
     public function getUserNonProductiveCallModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $company_id = Auth::user()->company_id;



        $user_details = DB::table('user_sales_order')
                            ->select(DB::raw('COUNT(DISTINCT retailer_id) as pc'),'user_sales_order.date')
                            ->where('user_id',$user_id)
                            ->where('company_id',$company_id)
                            ->where('call_status','0')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->groupBy('date')
                            ->get();





         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['date'] = $value->date;
                $out['pc'] = $value->pc;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }


    public function getUserSecondarySaleModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        if(empty($check)){
        $user_details = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->select(DB::raw('SUM(rate*quantity) as sale_value'),'user_sales_order.date')
                            ->where('user_id',$user_id)
                            ->where('user_sales_order.company_id',$company_id)
                            // ->where('call_status','1')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->groupBy('date')
                            ->get();
        }else{
              $user_details = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as sale_value'),'user_sales_order.date')
                            ->where('user_id',$user_id)
                            ->where('user_sales_order.company_id',$company_id)
                            // ->where('call_status','1')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->groupBy('date')
                            ->get();
        }





         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['date'] = $value->date;
                $out['sale_value'] = $value->sale_value;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }


     public function getNewRetailersDetailsModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $company_id = Auth::user()->company_id;



        $user_details = DB::table('retailer')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->select('retailer.id as retailer_id','retailer.name as retailer_name','retailer.track_address','retailer.landline','location_7.name as beat_name')
                        ->where('created_by_person_id',$user_id)
                        ->where('retailer.company_id',$company_id)
                        ->whereRaw(" DATE_FORMAT(created_on,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(created_on,'%Y-%m-%d')<= '$to_date'")
                        ->groupBy('retailer.id')
                        ->get();


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['retailer_name'] = $value->retailer_name;
                $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
                $out['track_address'] = $value->track_address;
                $out['landline'] = $value->landline;
                $out['beat_name'] = $value->beat_name;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }


     public function getOutletCoverageModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $company_id = Auth::user()->company_id;


         // $toc=Person::join('user_sales_order','user_sales_order.user_id','=','person.id','inner')
         //    ->selectRaw('COUNT(DISTINCT retailer_id) as toc')
         //    ->where('person.company_id',$company_id)
         //    ->where('user_sales_order.company_id',$company_id)
         //    ->where('user_id',$uid)
         //    // ->whereRaw(" DATE_FORMAT(date,'%Y-%m')= '$month_date'")
         //    ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
         //    ->first(); 




        $user_details = DB::table('user_sales_order')
                        ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                        ->select('retailer.name as retailer_name','retailer.id as retailer_id','retailer.landline','retailer.lat_long','retailer.track_address',DB::raw("COUNT(DISTINCT order_id) as visit_number"))
                        ->where('user_id',$user_id)
                        ->where('user_sales_order.company_id',$company_id)
                        ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
                        ->groupBy('retailer.id')
                        ->get(); 


      


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['retailer_name'] = $value->retailer_name;
                $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
                $out['track_address'] = $value->track_address;
                $out['lat_long'] = $value->lat_long;
                $out['landline'] = $value->landline;
                $out['visit_number'] = $value->visit_number;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }


    public function getPrimarySalesModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';

         $users_dealer = DB::table('dealer_location_rate_list')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->select('dealer_id')
                        ->where('person.id',$user_id)
                        ->groupBy('dealer_id')
                        ->pluck('dealer_id')->toArray();


      
        $user_details_data = DB::table('purchase_order');
            $user_details_data->join('purchase_order_details', function($join)
                 {
                   $join->on('purchase_order_details.order_id', '=', 'purchase_order.order_id');
                   $join->on('purchase_order_details.purchase_inv', '=', 'purchase_order.challan_no');

                 })
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->whereIn('dealer.id',$users_dealer)
            ->whereRaw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')<='$to_date'")
            ->select(DB::raw("ROUND(SUM(purchase_order_details.total_amount),3) AS total_primary_sale_value"),DB::raw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d') as order_date"));
        $user_primary_details = $user_details_data->groupBy('order_date')->get();


       


         if(!empty($user_primary_details))
        {
            $f_out = array();
            foreach ($user_primary_details as $key => $value) 
            {
                $out['order_date'] = $value->order_date;
                $out['total_primary_sale_value'] = $value->total_primary_sale_value;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }


     public function getSKUSalesModal(Request $request)
    {
     
        $user_id = !empty($request->user_id)?explode(',',$request->user_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $company_id = Auth::user()->company_id;



         // $unique_sku_billed = DB::table('user_sales_order')
         //                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
         //                ->selectRaw('COUNT(DISTINCT product_id) as unique_sku')
         //                ->where('user_id',$uid)
         //                ->whereRaw(" DATE_FORMAT(date,'%Y-%m-%d')>= '$from_date' AND  DATE_FORMAT(date,'%Y-%m-%d')<= '$to_date'")
         //                ->first();


        $user_details = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                            ->select(DB::raw('SUM(rate*quantity) as sale_value'),DB::raw('SUM(quantity) as sale_quantity'),'catalog_product.name as product_name')
                            ->where('user_sales_order.user_id',$user_id)
                            ->where('user_sales_order.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->groupBy('catalog_product.id')
                            ->get();





         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['sale_value'] = $value->sale_value;
                $out['sale_quantity'] = $value->sale_quantity;
                $out['product_name'] = $value->product_name;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

    }



    public function assign_mult_states(Request $request)
{
    if ($request->multstate && !empty($request->uusid)) {
        // dd($request);
        $multstate = $request->multstate;
        $company_id = Auth::user()->company_id;


        $delete = DB::table('user_multiple_state')->where('user_id',$request->uusid)->where('company_id',$company_id)->delete();

        foreach($multstate as $mkey => $mval)
        {
            $myArr = [
                'user_id' => $request->uusid,
                'state_id' => $mval,
                'company_id' => $company_id,
            ];
            $person=DB::table('user_multiple_state')->insert($myArr);
        }

        if ($person) {
            Session::flash('message', 'State Assign successfully');
            Session::flash('class', 'success');
        }
        return redirect()->intended('user');
    }
    else{

        Session::flash('message', 'State Not Assign successfully');
        Session::flash('class', 'success');
        return redirect()->intended('user');
    }
 
    
        

}


public function TotalBeatModal(Request $request)
    {

       
        $user_id = $request->user_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;


        $assign_total_beat = DB::table('dealer_location_rate_list')
                        ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('location_3','location_3.id','=','location_4.location_3_id')
                        ->join('location_2','location_2.id','=','location_3.location_2_id')
                        ->join('location_1','location_1.id','=','location_2.location_1_id')
                        ->where('dealer_location_rate_list.user_id',$user_id)
                        ->select('location_7.id as beat_id','location_7.name as beat_name','location_6.name as town','location_5.name as district','location_4.name as depot','location_3.name as state','location_2.name as region','location_1.name as zone')
                        ->groupBy('location_7.id')
                        ->get();
       
//dd($assign_total_beat);

         if(!empty($assign_total_beat))
        {
            $f_out = array();
            foreach ($assign_total_beat as $key => $value) 
            {
                $out['beat_id'] = $value->beat_id;
                $out['beat_name'] = $value->beat_name;
                $out['town'] = $value->town;
                $out['district'] = $value->district;
                $out['depot'] = $value->depot;
                $out['state'] = $value->state;
                $out['region'] = $value->region;
                $out['zone'] = $value->zone;
                $f_out[] = $out;
            }
            $data['assign_total_beat'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        return json_encode($data);

     }


}
