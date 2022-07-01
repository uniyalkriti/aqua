<?php

namespace App\Http\Controllers;

use App\Dealer;
use App\DealerLocation;
use App\DealerPersonLogin;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Retailer;
use App\ChallanOrder;
use DB;
use Auth;
use App\Person;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\Stock;
use App\UserSalesOrder;
use App\CatalogProduct;
use Mail;
class DMSDealerController extends Controller
{
    public function __construct()
    {
        $this->current_menu='dms_dealer';

        $this->status_table='dealer';
        $this->menu_status = 'master';

        session_start();

        $auth_id = !empty($_SESSION['iclientdigimetid'])?$_SESSION['iclientdigimetid']:'0';
        $this->auth_id = $auth_id; 
        $this->dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
        $this->csa_id = !empty($_SESSION['iclientdigimetdata']['csa_id'])?$_SESSION['iclientdigimetdata']['csa_id']:'0';
        $this->dealer_code = !empty($_SESSION['iclientdigimetdata']['dealer_code'])?$_SESSION['iclientdigimetdata']['dealer_code']:'0';
        $this->role_id = !empty($_SESSION['iclientdigimetdata']['urole'])?$_SESSION['iclientdigimetdata']['urole']:'0';
        // $this->dealer_code = '20602';
        // $this->current_menu = 'Order-details'; 
        // $this->date = '2021-01-01';
        if($auth_id != '0' )
        {
            // dd('1');
            $auth_id = $auth_id;
            $this->menu_status = 'adminMenuDms';

            if($this->role_id != '1')
            {
                header('Location: http://demo.msell.in/client/index.php?option=logout');
                dd('1');
            }

        }

        else {
            # code...
            $this->auth_id = !empty(Auth::user()->id)?Auth::user()->id:'0';
            // dd('11');
            header('Location: http://demo.msell.in/client');
            dd('1');
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $dealer_name = $request->distributor;
        $state = $request->state;
        $town = $request->town;
        $status = $request->status;
        $depo_filter = $request->depo_filter;
        $ter_arr = $request->ter_arr;
        // dd($request);
        
        $data_email = DB::table('ACC_MAST')
                    // ->where('ACC_CODE',$dealer_code)
                    ->pluck('EMAIL','ACC_CODE');


        $email_content = DB::table('mail_content_dealer')
                    ->where('status',1)
                    ->first();


        // dd($data_email);
        $q=Dealer::leftJoin('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
            // ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
            ->where('dealer.dealer_status','!=',9);

         #Distributor filter
         if (!empty($request->distributor)) {
            $dealer = $request->distributor;
            $q->whereIn('dealer.id', $dealer);
        }
        #status filter
         if (!empty($status)) {
            if($status==2)
            {
                $q->where('dealer_status',0);
            }
            else
            {
                $q->where('dealer_status',$status);
            }
        }

        #state filter
        if(!empty($state))
        {
            $q->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')->whereIn('l1_id',$state);
        }
        if(!empty($ter_arr))
        {
            $q->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')->whereIn('l2_id',$ter_arr);
        }
        #Town filter
        if(!empty($depo_filter))
        {
            $q->whereIn('dealer_person_login.div_code_main',$depo_filter);
        }

        $data = $q->groupBy('dealer.id')
            ->select('dealer.*','dealer_person_login.dealer_id as user_login','dealer_person_login.uname',DB::raw("AES_DECRYPT(pass,'".Lang::get('common.db_salt')."') AS person_password"),'div_code_main')
            ->orderBy('dealer.id', 'desc')
           ->paginate($pagination);
        // dd();
        if(!empty($ter_arr) && count($data)<=0)
        {
             $q=Dealer::leftJoin('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
            // ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
            ->where('dealer.dealer_status','!=',9);

            $q->join('location_view','location_view.l4_id','=','dealer.city_id')->whereIn('l2_id',$ter_arr);
            if(!empty($depo_filter))
            {
                $q->whereIn('dealer_person_login.div_code_main',$depo_filter);
            }
            if (!empty($request->distributor)) {
                $dealer = $request->distributor;
                $q->whereIn('dealer.id', $dealer);
            }
            $data = $q->groupBy('dealer.id')
            ->select('dealer.*','dealer_person_login.dealer_id as user_login','dealer_person_login.uname',DB::raw("AES_DECRYPT(pass,'".Lang::get('common.db_salt')."') AS person_password"),'div_code_main')
            ->orderBy('dealer.id', 'desc')
           // ->get();
           ->paginate($pagination);
           // dd($data);
        }
        // dd($data);
        $state = Location1::where('status', '!=', '2')->pluck('name', 'id');
        $town = Location4::where('status', '!=', '2')->pluck('name', 'id'); 

        $location_view_arr = DB::table('location_view')->pluck('l2_name','l4_id');

        $dealer_name=DB::table('dealer')
        ->where('dealer_status',1)
        ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as name"),'id');
        $arr=[];

        foreach ($data as $k=>$a)
        {
            $arr[]=$a->id;
        }
        $dlrl=[];
        $arr2=[];
        if (!empty($arr))
        {
            $dlrl=DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id','inner')
                ->whereIn('dealer_id',$arr)
//                ->where('user_id',0)
                // ->groupBy('dealer_location_rate_list.dealer_id','dealer_location_rate_list.location_id','dealer_location_rate_list.user_id','l1_name','l2_name','l3_name','l4_name','l5_name')
                // ->select('dealer_location_rate_list.dealer_id','dealer_location_rate_list.location_id','dealer_location_rate_list.user_id','l1_name','l2_name','l3_name','l4_name','l5_name')
                ->groupBy('dealer_location_rate_list.location_id','dealer_id')->get();
        }
        if (!empty($dlrl))
        {
            foreach ($dlrl as $b=>$c)
            {
                $arr2[$c->dealer_id][]=$c;
            }
        }
        // dd($arr2);
        $location3=Location1::where('status',1)->pluck('name','id');
        $ter_arr=Location2::where('status',1)->pluck('name','id');
        $beat_count = DB::table('dealer_location_rate_list')->groupBy('dealer_id')->pluck(DB::raw("COUNT( DISTINCT location_id) as beat"),'dealer_id');

        $custom_filter = 'Dealer';
        $role = DB::table('_role')->where('status',1)->where('rolename', 'LIKE',  '%'.$custom_filter . '%')->pluck('rolename','role_id');


        
        $user_assign_data = DB::table('person')
                    // ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    // ->join('users','users.id','=','person.id')
                    // ->join('_role','_role.role_id','=','person.role_id')
                    ->where('person_status',1)
                    // ->where('dealer_status',1)
                    // ->where('is_admin','!=',1)
                    ->orderBy('person.role_id','ASC')
                    ->groupBy('dealer_id');
                    // if(!empty($user))
                    // {
                    //     $user_assign_data->whereIn('user_id',$user);
                    // }
                    // if(!empty($request->role_id))
                    // {
                    //     $user_assign_data->whereIn('person.role_id',$request->role_id);
                    // }
        $user_assign = $user_assign_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT person.first_name,' ',middle_name,' ',last_name) as user_name"),'dealer_id');

        // dd($this->menu_status);
        $details_login = DB::table('dealer_person_login')->pluck('lastlogout','dealer_id');

        $depo_filter = DB::table('dealer')
                        ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                        ->where('dealer_status',1)
                        ->where('role_id',37)
                        ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as dealer_name"),'div_code_main');


        return view('distributor.dmsIndex', [
            'records' => $data,
            'state' => $state,
            'town' => $town,
            'status_table' => $this->status_table,
            'current_menu'=>$this->current_menu,
            'location3' => $location3,
            'arr2' => $arr2,
            'ter_arr'=>$ter_arr,
            'location_view_arr'=>$location_view_arr,
            'depo_filter'=> $depo_filter,
            'email_content'=>$email_content,
            'data_email'=>$data_email,
            'dealer_name' => $dealer_name,
            'beat_count'=> $beat_count,
            'details_login'=> $details_login,
            'role'=> $role,
            'menu_status'=>$this->menu_status,
            'user_assign'=> $user_assign,


        ]);

    }
    public function edit_index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $dealer_name = $request->distributor;
        $state = $request->state;
        $town = $request->town;
        $status = $request->status;
        $depo_filter = $request->depo_filter;
        $location2_array = $request->location2_array;
        // dd($status);
        
        $data_email = DB::table('ACC_MAST')
                    // ->where('ACC_CODE',$dealer_code)
                    ->pluck('EMAIL','ACC_CODE');


        $email_content = DB::table('mail_content_dealer')
                    ->where('status',1)
                    ->first();


        // dd($data_email);
        $q=Dealer::leftJoin('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
            // ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
            ->where('dealer.dealer_status','!=',9);

         #Distributor filter
         if (!empty($request->distributor)) {
            $dealer = $request->distributor;
            $q->whereIn('dealer.id', $dealer);
        }
        #status filter
         if (!empty($status)) {
            if($status==2)
            {
                $q->where('dealer_status',0);
            }
            else
            {
                $q->where('dealer_status',$status);
            }
        }

        #state filter
        if(!empty($location2_array))
        {
            $q->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')->whereIn('l2_id',$location2_array);
            if(!empty($state))
            {
                $q->whereIn('l1_id',$state);
            }
        }
        else
        {
            if(!empty($state))
            {
                $q->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')->whereIn('l1_id',$state);
            }
        }
        #Town filter
        if(!empty($town))
        {
            $q->whereIn('dealer.town_id',$town);
        }

        $data = $q->groupBy('dealer.id')
            ->select('dealer.*','dealer_person_login.div_code_main','dealer_person_login.dealer_id as user_login','dealer_person_login.uname',DB::raw("AES_DECRYPT(pass,'".Lang::get('common.db_salt')."') AS person_password"))
            ->orderBy('dealer.id', 'desc')
           ->paginate($pagination);
        $state = Location1::where('status', '!=', '2')->pluck('name', 'id');
        $town = Location4::where('status', '!=', '2')->pluck('name', 'id'); 

        $dealer_name=DB::table('dealer')
        ->where('dealer_status',1)
        ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as name"),'id');
        $arr=[];

        foreach ($data as $k=>$a)
        {
            $arr[]=$a->id;
        }
        $dlrl=[];
        $arr2=[];
        if (!empty($arr))
        {
            $dlrl=DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id','inner')
                ->whereIn('dealer_id',$arr)
//                ->where('user_id',0)
                // ->groupBy('dealer_location_rate_list.dealer_id','dealer_location_rate_list.location_id','dealer_location_rate_list.user_id','l1_name','l2_name','l3_name','l4_name','l5_name')
                // ->select('dealer_location_rate_list.dealer_id','dealer_location_rate_list.location_id','dealer_location_rate_list.user_id','l1_name','l2_name','l3_name','l4_name','l5_name')
                ->groupBy('dealer_location_rate_list.location_id','dealer_id')->get();
        }
        if (!empty($dlrl))
        {
            foreach ($dlrl as $b=>$c)
            {
                $arr2[$c->dealer_id][]=$c;
            }
        }
        $location3=Location1::where('status',1)->pluck('name','id');
        $beat_count = DB::table('dealer_location_rate_list')->groupBy('dealer_id')->pluck(DB::raw("COUNT( DISTINCT location_id) as beat"),'dealer_id');

        $custom_filter = 'Dealer';
        $role = DB::table('_role')->where('status',1)->where('rolename', 'LIKE',  '%'.$custom_filter . '%')->pluck('rolename','role_id');


        
        $user_assign_data = DB::table('person')
                    // ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    // ->join('users','users.id','=','person.id')
                    // ->join('_role','_role.role_id','=','person.role_id')
                    ->where('person_status',1)
                    // ->where('dealer_status',1)
                    // ->where('is_admin','!=',1)
                    ->orderBy('person.role_id','ASC')
                    ->groupBy('dealer_id');
                    // if(!empty($user))
                    // {
                    //     $user_assign_data->whereIn('user_id',$user);
                    // }
                    // if(!empty($request->role_id))
                    // {
                    //     $user_assign_data->whereIn('person.role_id',$request->role_id);
                    // }
        $user_assign = $user_assign_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT person.first_name,' ',middle_name,' ',last_name) as user_name"),'dealer_id');

        // dd($this->menu_status);
        $details_login = DB::table('dealer_person_login')->pluck('lastlogout','dealer_id');
        $location2_array = DB::table('location_2')->where('status',1)->pluck('name','id');

        $depo_filter = DB::table('dealer')
                        ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                        ->where('dealer_status',1)
                        ->where('role_id',37)
                        ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as dealer_name"),'div_code_main');


        return view('distributor.dmsEdit', [
            'records' => $data,
            'state' => $state,
            'town' => $town,
            'status_table' => $this->status_table,
            'current_menu'=>$this->current_menu,
            'location3' => $location3,
            'location2_array'=>$location2_array,
            'arr2' => $arr2,
            'depo_filter'=> $depo_filter,
            'email_content'=>$email_content,
            'data_email'=>$data_email,
            'dealer_name' => $dealer_name,
            'beat_count'=> $beat_count,
            'details_login'=> $details_login,
            'role'=> $role,
            'menu_status'=>$this->menu_status,
            'user_assign'=> $user_assign,


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
        $location1 = Location1::where('status', '!=', '2')->pluck('name', 'id');
        $location2 = Location2::where('status', '!=', '2')->pluck('name', 'id');

        #Dealer Ownership Type
        $ownership=DB::table('_dealer_ownership_type')->pluck('ownership_type','id');

        $csa=DB::table('csa')->pluck('csa_name','c_id');

        $division = DB::table('_product_division')->pluck('type','id');
        $div_code_array = DB::table('_div_code_master')->pluck('name','id');

        return view('distributor.create',[
            'current_menu'=>$this->current_menu,
            'ownership' => $ownership,
            'csa' => $csa,
            'location1' => $location1,
            'location2' => $location2,
            'menu_status'=>$this->menu_status,
            'division' => $division,
            'div_code_array'=>$div_code_array,
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
            'code' => 'required|min:2|max:40',
            'firm_name' => 'required|min:2|max:90',
            'location_1' => 'required',
            'location_2' => 'required',
            'location_3' => 'required',
            'location_4' => 'required',
            'csa' => 'required',
            'address' => 'required',
            'mobile' => 'max:10',
            'email' => 'required',
            'pin_no' => 'required',
            'regis_status'=>'required',
            'div_code'=>'required',
            'username'=>'required',
            'user_password'=>'required'
            // 'contact_person' => 'min:2|max:50'
        ]);

        $beat=$request->location_5;
        $auth_id = !empty(Auth::user()->id)?Auth::user()->id:'0';
        /* Start Transaction*/
        DB::beginTransaction();

        $check = Dealer::where('dealer_code',$request->dealer_code)->get();
        if(COUNT($check)>0)
        {
            Session::flash('message', Lang::get('common.'.$this->current_menu).' Duplicate Dealer Check Properly');
            Session::flash('class', 'success');
            return redirect()->guest(url($this->current_menu));
            // return redirect()->intended($this->current_menu);
        }
        $check2 = DB::table('dealer_person_login')->where('uname',$request->username)->get();
        if(COUNT($check2)>0)
        {
            Session::flash('message', Lang::get('common.'.$this->current_menu).' Duplicate Dealer Check Properly');
            Session::flash('class', 'success');
            return redirect()->guest(url($this->current_menu));
            // return redirect()->intended($this->current_menu);
        }
        $myArr = [
            'name' => trim($request->firm_name),
            'contact_person' => trim($request->contact_person),
            'dealer_code' => trim($request->code),
            'address' => trim($request->address),
            'email' => trim($request->email),
            'landline' => trim($request->landline),
            'other_numbers' => trim($request->mobile),
            'tin_no' =>trim($request->tin_no),
            // 'fssai_no' =>'',
            'pin_no' => trim($request->pin_no),
            'ownership_type_id' => trim($request->ownership_type),
            'avg_per_month_pur' => trim($request->avg_per_month),           
            // 'state_id' => trim($request->location_3),
            'city_id' => trim($request->location_4),
            // 'general_modern_type_id' => $request->dealer_type,
            'csa_id' => trim($request->csa),
            'created_at'=>date('Y-m-d H:i:s'),
            'created_by'=>$auth_id,
            // 'date'=>date('Y-m-d'),
            // 'time'=>date('H:i:s'),
            // 'terms' => '',
            'dealer_status' => 1,
            'dms_status' => 0,
            'edit_stock' => 0
        ];


        $dealer=Dealer::create($myArr);

        $d2 = '';
        if (!empty($beat))
        {
            foreach ($beat as $k=>$d)
            {
                $myArr2[]=[
                    'dealer_id'=>$dealer->id,
                    'location_id'=>$d,
                    'user_id'=>0,
                    'company_id'=>0,
                    'server_date'=>date('Y-m-d H:i:s')
                ];
            }
            $d2=DealerLocation::insert($myArr2);
        }
         if (!empty($request->location_2_cus))
        {
            // $d2=DB::table('dealer_report_section_data')->where('dealer_id',$id)->where('product_divison_id',0)->delete();

            foreach ($request->location_2_cus as $k=>$d)
            {
                $myArr2[]=[
                    'dealer_id'=>$dealer->id,
                    'location_2'=>$d,
                    'product_divison_id'=>0,
                    'server_date_time'=>date('Y-m-d H:i:s')
                ];
            }
            $d2=DB::table('dealer_report_section_data')->insert($myArr2);
        }
        if (!empty($request->division_cus))
        {
            // $d2=DB::table('dealer_report_section_data')->where('dealer_id',$id)->where('location_2',0)->delete();
            foreach ($request->division_cus as $k=>$d)
            {
                $myArr2[]=[
                    'dealer_id'=>$dealer->id,
                    'location_2'=>0,
                    'product_divison_id'=>$d,
                    'server_date_time'=>date('Y-m-d H:i:s')
                ];
            }
            $d2=DB::table('dealer_report_section_data')->insert($myArr2);
        }

        if(!empty($request->user_password))
        {
            $myArr=[
                'person_name' => trim($request->firm_name),
                'uname' => trim($request->username),
                'dealer_id' => $dealer->id,
                'state_id' => $request->location_3,
                'role_id' => !empty($request->role_id)?$request->role_id:'5',
                'pass' => DB::raw("AES_ENCRYPT('$request->user_password', '".Lang::get('common.db_salt')."')"),
                'div_code_main' => $request->div_code,
                'regis_status' => $request->regis_status,
                'activestatus' => 1
            ];
            $dpl=DB::table('dealer_person_login')->insert($myArr);
        }

        if ($dealer) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->guest(url($this->current_menu));
        // return redirect()->intended($this->current_menu);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {   
        // dd($request);
         if(!empty($request->date_range_picker)){
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        }else{
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        }

        $mdate=date('Y-m');
        #decrypt id
        $uid = Crypt::decryptString($id);
        $date = date("Y-m-d");

        $beat_array = DB::table('dealer_location_rate_list')
                    ->where('dealer_id',$uid)
                    ->groupBy('location_id')
                    ->pluck('location_id')
                    ->toArray();

        $totalRetailer = DB::table('retailer')
                            ->select(DB::raw("COUNT(DISTINCT id) as retailer_count"))
                            ->whereIn('location_id',$beat_array)
                            ->where('retailer_status','=','1')
                            ->first();



        $sale_and_sku = DB::table('user_sales_order')
                   ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                   ->select(DB::raw('SUM(rate*quantity) as sale'),DB::raw("COUNT(DISTINCT product_id) as totalsku"))
                   ->where('dealer_id',$uid)
                   ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                   ->first();            
       

       $stockCategoryWise = DB::table('stock')
                            ->join('catalog_view','catalog_view.product_id','=','stock.product_id','inner')
                            ->select('c2_id as c0_id','color_code','c2_name as c0_name',DB::raw("ROUND(SUM(dealer_rate*qty),3) as stockQty"))
                            ->where('dealer_id',$uid)
                            ->groupBy('c2_id')
                            ->orderBY('c2_name','ASC')
                            ->get();   

        $user_details_data = DB::table('purchase_order');
        $user_details_data->join('purchase_order_details', function($join)
                 {
                   $join->on('purchase_order_details.order_id', '=', 'purchase_order.order_id');
                   $join->on('purchase_order_details.purchase_inv', '=', 'purchase_order.challan_no');

                 })
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->whereRaw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')<='$to_date'")
            ->where('dealer.id',$uid)
            ->select(DB::raw("ROUND(SUM(purchase_order_details.total_amount),3) AS total_primary_sale_value"));
        $user_details = $user_details_data->groupBy('dealer.id')->first();


        // dd($user_details);



        $dealerData=Dealer::where('id',$uid)->first();

        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        {
            $Store = date('Y-m-d', $currentDate);
            $datesArr[] = $Store;
            $datesDisplayArr[] = $Store;
        }

        $sale_date_wise = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->where('dealer_id',$uid)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('date')
                        ->pluck(DB::raw('SUM(rate*quantity) as sale'),'date')->toArray();




        foreach($datesArr as $dkey=>$dateVal)
        {
           
            $totalOrderValueArr[]=!empty($sale_date_wise[$dateVal])?$sale_date_wise[$dateVal]:'0';
        }


      
        foreach($stockCategoryWise as $skey=>$sVal)
        {
            $label=$sVal->c0_name;
            // $label=$sVal->c0_name.' [<b>'.$sVal->stockQty.'<b>]';
            $stockData[]=array('label'=>"$label",'data'=>$sVal->stockQty, 'color'=>"$sVal->color_code");
        }
        if(empty($stockData))
            $stockData=[];
            // dd($stockData);

        return view('distributor.view',[
            'id'=>$id,
            'totalRetailer'=>$totalRetailer,
            'totalOrderValueArr'=>$totalOrderValueArr,
            'dealerData'=>$dealerData,
            'datesArr'=>$datesDisplayArr,
            'stockCategoryWise'=>$stockData,
            'sale_and_sku'=>$sale_and_sku,
            'user_details'=>$user_details,
            'from_date'=>$from_date,
            'to_date'=>$to_date,
            'menu_status'=>$this->menu_status,
            'dashboard_dealer_id'=>$uid,
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
        $admin_dealer = $this->dealer_id;
        #decrypt id
        $uid = Crypt::decryptString($id);

        #Dealer Ownership Type
        $ownership=DB::table('_dealer_ownership_type')->pluck('ownership_type','id');
        $location2_id_set=DB::table('dealer_report_section_data')->where('dealer_id',$id)->pluck('location_2')->toArray();
        $product_divison_id_set=DB::table('dealer_report_section_data')->pluck('product_divison_id')->toArray();
        $dpd=DB::table('dealer_person_login')->select('dealer_person_login.*',DB::raw("AES_DECRYPT(pass,'".Lang::get('common.db_salt')."') AS person_password"))->where('dealer_id',$uid)->first();

        #Dealer
        $dealer=Dealer::find($uid);

// dd($dealer);
        $dlrl=DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
            ->where('dealer_id',$uid)
            ->pluck('l5_id')->toArray();
        $dlrl2=DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
            ->where('dealer_id',$uid)
            // ->where('user_id',0)
            ->first();
            // dd($dlrl2);

        $location2=[];
        $location3=[];
        $location4=[];
        $location5=[];
        $csa=[];

        #Location 1 data
        $location1 = Location1::where('status', '!=', '2')->pluck('name', 'id');
        $location2_array = Location2::where('status', '=', '1')->pluck('name', 'id');
        #Location 2 data
        if (!empty($dlrl2->l1_id))
        $location2 = Location2::where('status', '!=', '2')->where('location_1_id',$dlrl2->l1_id)->pluck('name', 'id');
        #Location 3 data
        if (!empty($dlrl2->l2_id))
        $location3 = Location3::where('status', '!=', '2')->where('location_2_id',$dlrl2->l2_id)->pluck('name', 'id');
        #Location 4 data
        if (!empty($dlrl2->l3_id)){
            $location4 = Location4::where('status', '!=', '2')->where('location_3_id',$dlrl2->l3_id)->pluck('name', 'id');
        }
        else
        {
            $location4 = Location4::where('status', '!=', '2')->where('id',$dealer->city_id)->pluck('name', 'id');
        }
        // dd($location4);
        #Location 5 data
        if (!empty($dlrl2->l4_id))
        $location5 = Location5::where('status', '!=', '2')->where('location_4_id',$dlrl2->l4_id)->pluck('name', 'id');

        // if (!empty($dlrl2->l3_id))
        $csa=DB::table('csa')->pluck('csa_name','c_id');
        //$csa=DB::table('csa')->where('state_id',$dlrl2->l3_id)->pluck('csa_name','c_id');

        $division = DB::table('_product_division')->pluck('type','id');


        $selecteddivision = DB::table('_product_division')
                ->join('beat_division_assign','beat_division_assign.division_id','=','_product_division.id')
                ->whereIn('beat_division_assign.location_5_id',$dlrl)
                ->groupBy('_product_division.id')
                ->pluck('_product_division.id')->toArray();

                // dd($selecteddivision);

        $div_code_array = DB::table('_div_code_master')->pluck('name','id');

        return view('distributor.edit',[
            'current_menu'=>$this->current_menu,
            'dealer' => $dealer,
            'dlrl2' => $dlrl2,
            'dlrl' => $dlrl,
            'ownership' => $ownership,
            'location1' => $location1,
            'location2' => $location2,
            'location3' => $location3,
            'location4' => $location4,
            'location5' => $location5,
            'location2_array'=>$location2_array,
            'location2_id_set'=>$location2_id_set,
            'product_divison_id_set'=>$product_divison_id_set,
            'csa' => $csa,
            'encrypt_id'=>$id,
            'dpd'=>$dpd,
            'division'=>$division,
            'div_code_array'=> $div_code_array,
            'menu_status'=>$this->menu_status,
            'selecteddivision'=>$selecteddivision,
            'admin_dealer'=>$admin_dealer,
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
        // dd($request);
        $validate = $request->validate([
            // 'code' => 'required|min:2|max:40',
            // 'firm_name' => 'required|min:2|max:50',
            // 'landline' => 'max:12',
            // 'mobile' => 'max:10',
            // 'contact_person' => 'min:2|max:50',
            // 'location_1' => 'required',
            // 'location_2' => 'required',
            // 'location_3' => 'required',
            // 'location_4' => 'required',

            'code' => 'required|min:2|max:40',
            'firm_name' => 'required|min:2|max:90',
            // 'location_1' => 'required',
            // 'location_2' => 'required',
            // 'location_3' => 'required',
            'location_4' => 'required',
            'csa' => 'required',
            'address' => 'required',
            'mobile' => 'max:10',
            'email' => 'required',
            'pin_no' => 'required',
            'regis_status'=>'required',
            'div_code'=>'required',
            'username'=>'required',
            'user_password'=>'required'

            // 'location_5' => 'required',
        ]);

        $id = Crypt::decryptString($id);
        $dealer_data = Dealer::findOrFail($id);

        $beat=$request->location_5;
        /* Start Transaction*/
        DB::beginTransaction();
        $myArr = [
            'name' => trim($request->firm_name),
            'contact_person' => trim($request->contact_person),
            'dealer_code' => trim($request->code),
            'address' => trim($request->address),
            'email' => trim($request->email),
            'landline' => trim($request->landline),
            'other_numbers' => trim($request->mobile),
            'tin_no' =>trim($request->tin_no),
            // 'fssai_no' =>'',
            'pin_no' => trim($request->pin_no),
            'ownership_type_id' => trim($request->ownership_type),
            'avg_per_month_pur' => trim($request->avg_per_month),
            // 'state_id' => trim($request->location_3),
            'city_id' => trim($request->location_4),
            'csa_id' => trim($request->csa),
            // 'terms' => '',
            'dealer_status' => 1,
            'dms_status' => 0,
            'edit_stock' => 0
        ];


        $dealer=$dealer_data->update($myArr);


        if(!empty($request->user_password))
        {
            $check = DB::table('dealer_person_login')->where('dealer_id',$id)->get();
            // dd($check);
            if(COUNT($check)>0)
            {

                $myArr=[
                    'person_name' => trim($request->firm_name),
                    'uname' => trim($request->username),
                    'state_id' => $request->location_3,
                    'role_id' => !empty($request->role_id)?$request->role_id:'5',
                    'pass' => DB::raw("AES_ENCRYPT('$request->user_password', '".Lang::get('common.db_salt')."')"),
        //            'pass' => $request->user_password,
                    'div_code_main' => $request->div_code,
                    'regis_status' => $request->regis_status,
                    'activestatus' => 1
                ];
                $dpl=DB::table('dealer_person_login')->where('dealer_id',$id)->update($myArr);

        //         $myArr=[
        //             'person_name' => trim($request->firm_name),
        //             'uname' => trim($request->username),
        //             'state_id' => $request->location_3,
        //             'role_id' => !empty($request->role_id)?$request->role_id:'5',
        //             'pass' => DB::raw("AES_ENCRYPT('$request->user_password', '".Lang::get('common.db_salt')."')"),
        // //            'pass' => $request->user_password,
        //             'activestatus' => 1
        //         ];
        //         $dpl=DB::table('dealer_person_login')->insert($myArr);
            }
            else
            {
                $myArr=[
                    'person_name' => trim($request->firm_name),
                    'uname' => trim($request->username),
                    'dealer_id' => $id,
                    'state_id' => $request->location_3,
                    'role_id' => !empty($request->role_id)?$request->role_id:'5',
                    'pass' => DB::raw("AES_ENCRYPT('$request->user_password', '".Lang::get('common.db_salt')."')"),
        //            'pass' => $request->user_password,
                    'div_code_main' => $request->div_code,
                    'regis_status' => $request->regis_status,
                    'activestatus' => 1
                ];
                $dpl=DB::table('dealer_person_login')->insert($myArr);
                
            }
            

        }

        if (!empty($request->location_2_cus))
        {
            $d2=DB::table('dealer_report_section_data')->where('dealer_id',$id)->where('product_divison_id',0)->delete();

            foreach ($request->location_2_cus as $k=>$d)
            {
                $myArr2[]=[
                    'dealer_id'=>$id,
                    'location_2'=>$d,
                    'product_divison_id'=>0,
                    'server_date_time'=>date('Y-m-d H:i:s')
                ];
            }
            // dd($myArr2);
            $d2=DB::table('dealer_report_section_data')->insert($myArr2);
        }
        if (!empty($request->division_cus))
        {
            $d2=DB::table('dealer_report_section_data')->where('dealer_id',$id)->where('location_2',0)->delete();
            foreach ($request->division_cus as $k=>$d)
            {
                $myArr2[]=[
                    'dealer_id'=>$id,
                    'location_2'=>0,
                    'product_divison_id'=>$d,
                    'server_date_time'=>date('Y-m-d H:i:s')
                ];
            }
            $d2=DB::table('dealer_report_section_data')->insert($myArr2);
        }


        if (!empty($beat))
        {
            $user_data = [];
            $existing_beat_id = [];
            $old_beat_array = DealerLocation::where('dealer_id',$id)->distinct('location_id')->pluck('location_id')->toArray();
            // dd($old_beat_array);
            $new_beat_array = $beat;
            // dd($new_beat_array);
            $diffrence_first = array_diff($old_beat_array,$new_beat_array);
            $diffrence_second = array_diff($new_beat_array,$old_beat_array);
            $array_diifrence_final = array_merge($diffrence_first,$diffrence_second);
            // dd($array_diifrence_final);
            $query_for_existing_value = DealerLocation::whereNotIn('location_id',$new_beat_array)
                                        ->where('dealer_id',$id)
                                        ->distinct('location_id')->get()->toArray();
            foreach ($query_for_existing_value as $key => $value) 
            {
                // dd($value);
                $existing_beat_id[] = $value["location_id"];
            }
            // dd($existing_beat_id);
            // DB::beginTransaction();
            $delete_edit_beat = DealerLocation::where('dealer_id',$id)
                                ->whereNotIn('location_id',$new_beat_array)
                                ->delete();
            // dd($delete_edit_beat);
            $diffrence_third = array_diff($array_diifrence_final,$existing_beat_id);
            // dd($diffrence_third);
            if(!empty($diffrence_third))
            {

                foreach ($diffrence_third as $key => $value) 
                {
                    $myArr = 
                    [
                        'dealer_id' => $id,
                        'location_id' => $value,
                        'user_id' => 0,
                        'company_id' => 1,
                        'server_date'=>date('Y-m-d H:i:s'),    
                    ];
                    $final_insertion = DealerLocation::insert($myArr); 
                }
                // dd($myArr);
                if($final_insertion)
                {
                    DB::commit();
                    Session::flash('message', Lang::get('common.'.$this->current_menu).' Updated successfully');
                    Session::flash('class', 'success');
                    return redirect()->guest(url($this->current_menu));
                    // return redirect()->intended($this->current_menu);

                }
                else
                {
                    DB::rollback();
                    Session::flash('message', 'Something went wrong!');
                    Session::flash('class', 'danger');
                    return redirect()->guest(url($this->current_menu));
                    // return redirect()->intended($this->current_menu);

                }
                
            }
            else
            {
                if($delete_edit_beat)
                {
                    DB::commit();
                    Session::flash('message', Lang::get('common.'.$this->current_menu).' Updatedd successfully');
                    Session::flash('class', 'success');
                    return redirect()->guest(url($this->current_menu));
                    // return redirect()->intended($this->current_menu);
                }
                else
                {
                       if ($dealer) 
                       {
                            DB::commit();
                            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
                            Session::flash('class', 'success');
                        } 
                        else 
                        {
                            DB::rollback();
                            Session::flash('message', 'Something went wrong!');
                        }
                    DB::rollback();
                    return redirect()->guest(url($this->current_menu));
                    // return redirect()->intended($this->current_menu);
                }

            }

        }
        

        if ($dealer) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->guest(url($this->current_menu));
        // return redirect()->intended($this->current_menu);
    }
    public function update_dealer_details_tab(Request $request)
    {
        // dd($request);
        $validate = $request->validate([
            'dealer_code' => 'required|min:2|max:40',
            'firm_name' => 'required|min:2|max:50',
            'other_numbers' => 'max:12',
            'email_to' => 'min:2|max:50',
            'user_name' => 'required',
            'user_pass' => 'required',
            'div_code_main' => 'required',
            // 'location_5' => 'required',
        ]);
        // dd($request);

        $id = $request->userid;
        $dealer_data = Dealer::findOrFail($request->userid);

        $beat=$request->location_5;
        /* Start Transaction*/
        DB::beginTransaction();
        $myArr = [
            'name' => trim($request->firm_name),
            'dealer_code' => trim($request->dealer_code),
            'email' => trim($request->email_to),
            'landline' => trim($request->other_numbers),
            'other_numbers' => trim($request->other_numbers),
            'dealer_status' => 1,
        ];


        $dealer=$dealer_data->update($myArr);

        $dealer_details_l3 = DB::table('dealer_location_rate_list')
                            ->select('l3_id')
                            ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                            ->where('dealer_id',$id)
                            ->first();
        $l3_id = !empty($dealer_details_l3->l3_id)?$dealer_details_l3->l3_id:'0';

        if(empty($l3_id))
        {
            dd('l3_id');
        }
        if(!empty($request->user_pass))
        {
            $check = DB::table('dealer_person_login')->where('dealer_id',$id)->get();
            // dd($check);
            if(COUNT($check)>0)
            {

                $myArr=[
                    'person_name' => trim($request->firm_name),
                    'uname' => trim($request->user_name),
                    'state_id' => $l3_id,
                    'role_id' => !empty($request->role_id)?$request->role_id:'5',
                    'pass' => DB::raw("AES_ENCRYPT('$request->user_pass', '".Lang::get('common.db_salt')."')"),
                    'div_code_main' => $request->div_code_main,
                    'regis_status' => 0,
                    'activestatus' => 1
                ];
                $dpl=DB::table('dealer_person_login')->where('dealer_id',$id)->update($myArr);


            }
            else
            {
                $myArr=[
                    'person_name' => trim($request->firm_name),
                    'uname' => trim($request->user_name),
                    'dealer_id' => $id,
                    'state_id' => $l3_id,
                    'role_id' => !empty($request->role_id)?$request->role_id:'5',
                    'pass' => DB::raw("AES_ENCRYPT('$request->user_pass', '".Lang::get('common.db_salt')."')"),
                    'div_code_main' => $request->div_code_main,
                    'regis_status' => 0,
                    'activestatus' => 1
                ];
                $dpl=DB::table('dealer_person_login')->insert($myArr);
                
            }
            

        }

        
        

        if ($dealer) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->guest(url($this->current_menu));
        // return redirect()->intended($this->current_menu);
    }

    /**
     * Add dealer user for login.
     *
     * @return \Illuminate\Http\Response
     */
    public function addDealerUser(Request $request)
    {
        $validate = $request->validate([
            'person_name' => 'required|min:2|max:80',
            'username' => 'required|min:2|max:20',
            'uuid' => 'required',
            'state' => 'required',
            'role_name' => 'required',
            'user_password' => 'required|min:2|max:20'
        ]);

        $myArr=[
            'person_name' => trim($request->person_name),
            'uname' => trim($request->username),
            'dealer_id' => $request->uuid,
            'state_id' => $request->state,
            'role_id' => $request->role_name,
            'pass' => DB::raw("AES_ENCRYPT('$request->user_password', '".Lang::get('common.db_salt')."')"),
//            'pass' => $request->user_password,
            'activestatus' => 1
        ];
        /* Start Transaction*/
        DB::beginTransaction();

        $dpl=DB::table('dealer_person_login')->insert($myArr);

//        DB::table('dealer_person_login')->insert([
//            ['email' => 'taylor@example.com', 'votes' => 0],
//            ['email' => 'dayle@example.com', 'votes' => 0]
//        ]);

        if ($dpl) {
            DB::commit();
            Session::flash('message', 'Dealer person login created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->guest(url($this->current_menu));
        // return redirect()->intended($this->current_menu);
    }

    /**
     * Update dealer user for login.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDealerUser(Request $request)
    {
        $validate = $request->validate([
            'person_name' => 'required|min:2|max:80',
            'username' => 'required|min:2|max:20',
            'uuid2' => 'required',
            'state' => 'required',
            'role_name' => 'required',
            'user_password' => 'max:20'
        ]);
        $dpl=DealerPersonLogin::where('dealer_id',$request->uuid2);
        $myArr=[
            'person_name' => trim($request->person_name),
            'uname' => trim($request->username),
            'email' => trim($request->email),
            'dealer_id' => $request->uuid2,
            'state_id' => $request->state,
            'role_id' => $request->role_name,
            'activestatus' => 1
        ];
        if (!empty($request->user_password))
        {
            $myArr['pass']=DB::raw("AES_ENCRYPT('$request->user_password', '".Lang::get('common.db_salt')."')");
        }
        /* Start Transaction*/
        DB::beginTransaction();

        $dpl=$dpl->update($myArr);


        if ($dpl) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' person login updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->guest(url($this->current_menu));
        // return redirect()->intended($this->current_menu);
    }


      public function dms_get_dealer_person_details(Request $request)
    {
        $dealer_id = $request->dealer_id;
        $details = DB::table('dealer_person_login')
                ->join('location_1','location_1.id','=','dealer_person_login.state_id')
                ->select('location_1.name as l3_name','dealer_person_login.person_name','dealer_person_login.uname','dealer_person_login.phone','dealer_person_login.email',DB::raw("AES_DECRYPT(pass,'".Lang::get('common.db_salt')."') AS person_password"))
                ->where('dealer_person_login.dealer_id',$dealer_id)
                ->first();
        // dd($details);
        if(!empty($details))
        {
            $data['code'] = 200;
            $data['result'] = $details;
            $data['message'] = 'success';
        } 
        else 
        {
            $data['code'] = 200;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function dms_dealer_send_mail(Request $request)
    {
        // dd($request);
        $msg = $request->body_content;
        $to_mail = $request->to_mail;
        $cc_mail = $request->cc_mail;
        $cc_mail_t = explode(',',$cc_mail);
        foreach ($cc_mail_t as $key => $value) {
            # code...
            $arr1[] = "'".$value."'";
        }
        // dd($arr1)
        $cc_mail = implode(',', $arr1);
        // $dealer_code = !empty($request->dealer_id)?$request->dealer_id:'20066';
        // $data_email = DB::table('ACC_MAST')
        //             ->where('ACC_CODE',$dealer_code)
        //             ->first();
        $mailId = !empty($to_mail)?$to_mail:'karan@manacleindia.com';
        $cc_mail = !empty($cc_mail)?$cc_mail:'karan@manacleindia.com';
        // $msg
        $send=Mail::raw($msg, function ($message) use($mailId,$cc_mail)
        {
          $message->to($mailId,$mailId)
            // ->cc($cc_mail)
            ->subject('Credentials');
        });
        // return true;
       
            $data['code'] = 200;

            $data['message'] = 'success';
       

        return json_encode($data);
    
        // return

    }
}
