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
use App\Location6;
use App\Location7;
use App\Retailer;
use App\Company;
use App\ChallanOrder;
use DB;
use Auth;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\Stock;
use App\UserSalesOrder;
use App\CatalogProduct;

class DealerController extends Controller
{
    public function __construct()
    {
        $this->current_menu='distributor';

        $this->status_table='dealer';
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
        $csa = $request->csa;
        $location_5 = $request->location_5;
        $location_6 = $request->location_6;

        $distributor_division = $request->distributor_division;
        $status = $request->status;
        $user = $request->user;
        $company_id = Auth::user()->company_id;
        // dd($status);
        $permissions = DB::table('company_web_module_permission')
                    ->where('company_id',$company_id)
                    ->where('role_id',Auth::user()->role_id)
                    ->where('module_id',7)
                    ->first();

        if($company_id == 52)
        {
            $q=Dealer::leftJoin('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                ->join('dealer_personal_details','dealer_personal_details.dealer_id','=','dealer.id')
                ->leftJoin('csa','csa.c_id','=','dealer.csa_id');
                // ->where('dealer.dealer_status','!=',9);

        }
        else
        {
            $q=Dealer::leftJoin('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                // ->join('csa','csa.c_id','=','dealer.csa_id')
                ->where('dealer.dealer_status','!=',9);

        }
       
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

        if(!empty($user))
        {
        	$q->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
            ->whereIn('user_id',$user);
        }

        #state filter
        if(!empty($state))
        {
            $q->whereIn('dealer.state_id',$state);
        }
        #Town filter
        if(!empty($town))
        {
            $q->whereIn('dealer.town_id',$town);
        }

        if(!empty($location_6) || !empty($location_5))
        {
            $q->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
            ->join('location_7','location_7.id','dealer_location_rate_list.location_id')
            ->join('location_6','location_6.id','=','location_7.location_6_id')
            ->join('location_5','location_5.id','=','location_6.location_5_id');
             if(!empty($location_6)){
                $q->whereIn('location_6.id',$location_6);
            }
             if(!empty($location_5)){
                $q->whereIn('location_5.id',$location_5);
            }

        }
   
    

        if(!empty($csa))
        {
            $q->whereIn('dealer.csa_id',$csa);
        }

        if($company_id ==52)
        {
            $data = $q->groupBy('dealer.id')
                ->select('dealer_personal_details.*','csa.csa_name as csa_name','dealer.*','dealer_person_login.dealer_id as user_login')
                ->where('dealer.company_id',$company_id)
                ->orderBy('dealer.id', 'desc')
               ->paginate($pagination);
        }
        else
        {
            $data = $q->groupBy('dealer.id')
                ->select('dealer.*','dealer_person_login.dealer_id as user_login')
                ->where('dealer.company_id',$company_id)
                ->orderBy('dealer.id', 'desc')
               ->paginate($pagination);
        }
        
        $state = Location3::where('status', '=', '1')->where('company_id',$company_id)->orderBy('location_3.name','ASC')->pluck('name', 'id');
        $town = Location6::where('status', '=', '1')->where('company_id',$company_id)->orderBy('location_6.name','ASC')->pluck('name', 'id'); 
        $csa_name_details = DB::table('csa')->where('active_status', '=', '1')->where('company_id',$company_id)->orderBy('csa.csa_name','ASC')->pluck('csa_name', 'c_id'); 
        $custom_filter = 'Distri';
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->where('rolename', 'LIKE',  '%'.$custom_filter . '%')->orderBy('rolename','ASC')->pluck('rolename','role_id');
        $role_array = DB::table('_role')->where('status',1)->where('company_id',$company_id)->orderBy('rolename','ASC')->pluck('rolename','role_id');

        $user_assign_data = DB::table('person')
		        	// ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
		          	->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
		          	->join('person_login','person_login.person_id','=','person.id')
                    ->join('users','users.id','=','person.id')
		          	// ->join('_role','_role.role_id','=','person.role_id')
		          	->where('person_status',1)
		          	// ->where('dealer_status',1)
        			->where('is_admin','!=',1)
		          	->where('dealer_location_rate_list.company_id',$company_id)
		          	->where('person.company_id',$company_id)
                    ->orderBy('person.role_id','ASC')
		          	->groupBy('dealer_id');
		          	if(!empty($user))
		          	{
		          		$user_assign_data->whereIn('user_id',$user);
		          	}
                    if(!empty($request->role_id))
                    {
                        $user_assign_data->whereIn('person.role_id',$request->role_id);
                    }
      	$user_assign = $user_assign_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT person.first_name,' ',middle_name,' ',last_name) as user_name"),'dealer_id');

        $dealer_name=DB::table('dealer')
        ->where('company_id',$company_id)
        ->where('dealer_status',1)
        ->orderBy('dealer.name','ASC')
        ->pluck('name','id');

        $user=DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->join('users','users.id','=','person.id')
        ->where('is_admin','!=',1)
        ->where('person.company_id',$company_id)
        ->where('person_status',1)
        ->orderBy('person.first_name','ASC')
        ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as id');
        $arr=[];

        foreach ($data as $k=>$a)
        {
            $arr[]=$a->id;
        }
        $dlrl=[];
        $arr2=[];
        if (!empty($arr))
        {
            $dlrl=DealerLocation::join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id','inner')
                ->whereIn('dealer_id',$arr)
                ->where('company_id',$company_id)
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
        $location3=Location3::where('status',1)->where('company_id',$company_id)->orderBy('location_3.name','ASC')->pluck('name','id');
        $location_5=Location5::where('status',1)->where('company_id',$company_id)->orderBy('location_5.name','ASC')->pluck('name','id');
        $location_4=Location4::where('status',1)->where('company_id',$company_id)->orderBy('location_4.name','ASC')->pluck('name','id');
        $location_6=Location6::where('status',1)->where('company_id',$company_id)->orderBy('location_6.name','ASC')->pluck('name','id');
        $csa=DB::table('csa')->where('active_status',1)->where('company_id',$company_id)->orderBy('csa_name','ASC')->pluck('csa_name','c_id');
        $beat_count = DB::table('dealer_location_rate_list')->where('company_id',$company_id)->groupBy('dealer_id')->pluck(DB::raw("COUNT( DISTINCT location_id) as beat"),'dealer_id');

        $assign_price_list = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',5)->count();
        $assigned_dealer_data = DB::table('template_product')
                            // ->join('template_product','template_product.id','=','product_rate_list.template_id')
                            ->where('template_product.company_id',$company_id)
                            // ->groupBy('template_product.id')
                            ->pluck('template_product.name as name','id');
        if($company_id == 52)
        {
            return view('distributor.patanjaliIndex', [
                'records' => $data,
                'state' => $state,
                'town' => $town,
                'status_table' => $this->status_table,
                'current_menu'=>$this->current_menu,
                'location3' => $location3,
                'arr2' => $arr2,
                'dealer_name' => $dealer_name,
                'location_5'=> $location_5,
                'location_6'=> $location_6,
                'csa'=> $csa,
                'user_assign'=> $user_assign,
                'beat_count'=> $beat_count,
                'location_4'=> $location_4,
                'user'=> $user,
                'assign_price_list'=> $assign_price_list,
                'assigned_dealer_data'=> $assigned_dealer_data,
                'is_admin'=>Auth::user()->is_admin,
                'role'=> $role,
                'permissions'=> $permissions,
                'role_array'=> $role_array,
            ]);
        }
        else
        {
            return view('distributor.index', [
                'records' => $data,
                'state' => $state,
                'town' => $town,
                'status_table' => $this->status_table,
                'current_menu'=>$this->current_menu,
                'location3' => $location3,
                'arr2' => $arr2,
                'dealer_name' => $dealer_name,
                'location_5'=> $location_5,
                'location_4'=> $location_4,
                'location_6'=> $location_6,
                'csa'=> $csa,
                'user_assign'=> $user_assign,
                'beat_count'=> $beat_count,
                'user'=> $user,
                'assign_price_list'=> $assign_price_list,
                'assigned_dealer_data'=> $assigned_dealer_data,
                'role'=> $role,
                'permissions'=> $permissions,
                'csa_name_details'=> $csa_name_details,
                'is_admin'=>Auth::user()->is_admin,
            ]);
        }
        


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
        $location1 = Location1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        $location6 = Location6::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        #Dealer Ownership Type
        $ownership=DB::table('_dealer_ownership_type')->where('company_id',$company_id)->pluck('ownership_type','id');

        $csa=DB::table('csa')->where('company_id',$company_id)->pluck('csa_name','c_id');

        $assign_price_list = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',5)->count();
        $template_product = DB::table('template_product')
                        ->join('product_rate_list_template','product_rate_list_template.template_type','=','template_product.id')
                        ->where('template_product.status',1)
                        ->where('template_product.company_id',$company_id)
                        ->groupBy('template_product.id')
                        ->pluck('template_product.name as name','template_product.id as id');

        $vehicle_details = DB::table('_vehicle_details')->where('company_id',$company_id)->where('status', '=', '1')->pluck('name','id');

        return view('distributor.create',[
            'current_menu'=>$this->current_menu,
            'ownership' => $ownership,
            'csa' => $csa,
            'company_id'=> $company_id,
            'location1' => $location1,
            'assign_price_list'=>$assign_price_list,
            'template_product'=>$template_product,
            'location6' => $location6,
            'vehicle_details'=> $vehicle_details,
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
            'firm_name' => 'required|min:2|max:50',
            'location_1' => 'required',
            'location_2' => 'required',
            'location_3' => 'required',
            'location_4' => 'required',
            'location_5' => 'required',
            'location_6' => 'required',
            'location_7' => 'required',
            'town_id' => 'required',
            'landline' => 'max:12',
            'mobile' => 'max:10',
            // 'email' => 'required',
            'contact_person' => 'min:2|max:50'
        ]);

        $beat=$request->location_7;
        $company_id = Auth::user()->company_id;
        /* Start Transaction*/
        DB::beginTransaction();
        $myArr = [
            'name' => trim($request->firm_name),
            'contact_person' => trim($request->contact_person),
            'dealer_code' => trim($request->code),
            'address' => trim($request->address),
            'email' => trim(!empty($request->email)?$request->email:'na'),
            'landline' => trim($request->landline),
            'other_numbers' => trim($request->mobile),
            'tin_no' =>trim(!empty($request->tin_no)?$request->tin_no:'0'),
            'fssai_no' =>'',
            'company_id' => $company_id,
            'pin_no' => trim($request->pin_no),
            'ownership_type_id' => trim($request->ownership_type),
            'avg_per_month_pur' => trim($request->avg_per_month),           
            'state_id' => trim($request->location_3),
            'town_id' => trim($request->town_id),
            'csa_id' => trim($request->csa),
            'template_id'=>!empty($request->template_product_id)?$request->template_product_id:'0',
            'terms' => '',
            'dealer_status' => 1,
            'dms_status' => 0,
            'created_at'=>date('Y-m-d H:i:s'),
            'edit_stock' => 0
        ];


        $dealer=Dealer::create($myArr);
        // if(!empty($request->template_product_id))
        // {

        //     $assign_data = DB::table('product_rate_list_template')
        //             ->where('template_type',$request->template_product_id)
        //             ->where('company_id',$company_id)
        //             ->where('status',1)
        //             ->get();

        //     foreach($assign_data as $key => $value)
        //     {
        //         $myArrAssign = [
        //             'product_id' => $value->product_id,
        //             'company_id' => $company_id,
        //             'template_id' => $value->template_type,
        //             'distributor_id' => $dealer->id,
        //             'mrp' => $value->mrp,
        //             'mrp_pcs' => $value->mrp_pcs,
        //             'dealer_rate' => $value->dealer_rate,
        //             'dealer_pcs_rate' => $value->dealer_pcs_rate,
        //             'ss_case_rate' => $value->ss_case_rate,
        //             'ss_pcs_rate' => $value->ss_pcs_rate,
        //             'retailer_rate' => $value->retailer_rate,
        //             'retailer_pcs_rate' => $value->retailer_pcs_rate,
        //             'other_retailer_rate' => $value->other_retailer_rate,
        //             'other_dealer_rate' => $value->other_dealer_rate,
        //             'other_ss_rate' => $value->other_ss_rate,
        //             'created_at'=>date('Y-m-d H:i:s'),
        //         ];

        //         $sumbit_query = DB::table('product_rate_list')->insert($myArrAssign);
        //     }
        // }
        if (!empty($beat))
        {
            foreach ($beat as $k=>$d)
            {
                $myArr2[]=[
                    'dealer_id'=>$dealer->id,
                    'location_id'=>$d,
                    'user_id'=>0,
                    'company_id'=>$company_id,
                    'server_date'=>date('Y-m-d H:i:s')
                ];
            }
        }
        $d2=DealerLocation::insert($myArr2);

        if ($dealer) {

            if($company_id == 52)
            {
                $save_further_details_patanjali = DB::table('dealer_personal_details')->insert([
                                                    'dealer_id'=>$dealer->id,
                                                    'company_id'=>$company_id,
                                                    'bank_name'=>!empty($request->bank_name)?$request->bank_name:'na',
                                                    'security_amt'=>!empty($request->security_amt)?$request->security_amt:'0',
                                                    'refrence_no'=>!empty($request->refrence_no)?$request->refrence_no:'0',
                                                    'security_date'=>!empty($request->security_date)?$request->security_date:'',
                                                    'reciept_issue_date'=>!empty($request->reciept_issue_date)?$request->reciept_issue_date:'',
                                                    'security_remarks'=>!empty($request->security_remarks)?$request->security_remarks:'na',
                                                    'commencement_date'=>!empty($request->commencement_date)?$request->commencement_date:'',
                                                    'termination_date'=>!empty($request->termination_date)?$request->termination_date:'',
                                                    'certificate_issue_date'=>!empty($request->certificate_issue_date)?$request->certificate_issue_date:'',
                                                    'agreement_remarks'=>!empty($request->agreement_remarks)?$request->agreement_remarks:'na',
                                                    'refund_amt'=>!empty($request->refund_amt)?$request->refund_amt:'0',
                                                    'refund_ref_no'=>!empty($request->refund_ref_no)?$request->refund_ref_no:'0',
                                                    'refund_date'=>!empty($request->refund_date)?$request->refund_date:'',
                                                    'refund_remarks'=>!empty($request->refund_remarks)?$request->refund_remarks:'na',
                                                    'food_license'=>!empty($request->food_license)?$request->food_license:'0',
                                                    'pan_no'=>!empty($request->pan_no)?$request->pan_no:'0',
                                                    'aadar_no'=>!empty($request->aadar_no)?$request->aadar_no:'0',
                                                    'created_at'=>date('Y-m-d H:i:s'),
                                                    'created_by'=>Auth::user()->id,

                                                ]); 
                foreach ($request->vehicle_details_array as $key => $value) 
                {
                    $vehicle_insert = DB::table('dealer_vechicle_assign')->insert([
                                    'company_id'=>$company_id,
                                    'dealer_id'=>$dealer->id,
                                    'vehicle_details_id'=>$value,
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::user()->id,
                                ]);
                }
                    

            }

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

        // dd($from_date);
        $mdate=date('Y-m');
        #decrypt id
        $uid = Crypt::decryptString($id);
        // $date = date("Y-m-d");
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        $beat_array = DB::table('dealer_location_rate_list')
                    ->where('dealer_id',$uid)
                    ->groupBy('location_id')
                    ->pluck('location_id')
                    ->toArray();

        $totalRetailer=Retailer::whereIn('location_id',$beat_array)->where('company_id',$company_id)->where('retailer_status','=','1')->count();

        // $totalCall=DB::table('user_sales_order')
        //         ->select('call_status')
        //         ->where('dealer_id',$uid)
        //         ->where('company_id',$company_id)
        //         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$date'")
        //         ->count();

        // $totalProductive=DB::table('user_sales_order')->select('call_status')->where('company_id',$company_id)->where('call_status',1)->where('dealer_id',$uid)->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$date'")->count();

        $totalSku = DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                    ->where('dealer_id',$uid)
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->distinct('product_id')
                    ->count('product_id');

      

        if(empty($check)){
       $total_sale_value = DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->where('dealer_id',$uid)
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->sum(DB::raw('rate*quantity'));
        }else{
            $total_sale_value = DB::table('user_sales_order')
            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            ->where('dealer_id',$uid)
            ->where('user_sales_order.company_id',$company_id)
            ->where('user_sales_order_details.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->sum(DB::raw('final_secondary_rate*final_secondary_qty'));
        }
       // $non_contacted_calls = DB::table('sale_reason_remarks')
       //                      ->join('user_sales_order','user_sales_order.user_id','=','sale_reason_remarks.user_id')
       //                      ->where('dealer_id',$uid)
       //                      ->where('user_sales_order.company_id',$company_id)
       //                      ->where('sale_reason_remarks.company_id',$company_id)
       //                      ->whereRaw("DATE_FORMAT(sale_reason_remarks.date,'%Y-%m-%d')='$date'")
       //                      ->count('sale_reason_remarks.id');

       
        $thresholdItem=Stock::join('threshold','threshold.dealer_id','=','stock.dealer_id','left')
                        ->join('catalog_view','catalog_view.product_id','=','stock.product_id','inner')
                        ->where('stock.dealer_id',$uid)
                        ->where('stock.company_id',$company_id)
                        ->where('threshold.company_id',$company_id)
                        ->whereRaw('stock.qty < threshold.qty')
                        ->whereRaw('stock.product_id=threshold.product_id')
                        ->select('catalog_view.product_name','stock.qty as stockQty','division')
                        ->groupBy('stock.id')
                        ->orderBy('product_name','ASC')
                        ->get();

        $stockCategoryWise=DB::table('dealer_balance_stock')
                        ->join('catalog_view','catalog_view.product_id','=','dealer_balance_stock.product_id','inner')
                        ->select('c2_id as c0_id','color_code','c2_name as c0_name',DB::raw("SUM(stock_qty) as stockQty"))
                        ->where('dealer_id',$uid)
                        ->where('dealer_balance_stock.company_id',$company_id)
                        ->groupBy('c2_id')
                        ->orderBY('c2_name','ASC')
                        ->get();

        // dd($stockCategoryWise);

        $dealerData=Dealer::join('location_view','location_view.l3_id','=','dealer.state_id','left')
                    ->where('company_id',$company_id)
                    ->where('dealer.id',$uid)
                    ->first();

        // ----------Target Achievment -------------//
        // for($i = 1; $i <=  date('t'); $i++)
        // {
        // // add the date to the dates array
        // $datesArr[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        // $datesDisplayArr[] =  date('M') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        // }
         // dd($datesArr);

        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        {
            $Store = date('Y-m-d', $currentDate);
            $datesArr[] = $Store;
            $datesDisplayArr[] = $Store;
        }

        foreach($datesArr as $dkey=>$dateVal)
        {
           
            // $totalChallanValueArr[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m'))"), "=", ''.$dateVal.'')
            // ->where('ch_dealer_id',$uid)->sum('amount');

            $totalOrderValueArr[]=UserSalesOrder::where(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')
            ->where('dealer_id',$uid)->where('company_id',$company_id)->sum('total_sale_value');
        }
        if(isset($lastChallan)){
            $ch_no=$lastChallan->ch_no;
        }else{
            $ch_no=0;
        }
        foreach($stockCategoryWise as $skey=>$sVal)
        {
            $label=$sVal->c0_name.' [<b>'.$sVal->stockQty.'<b>]';
            $stockData[]=array('label'=>"$label",'data'=>$sVal->stockQty, 'color'=>"$sVal->color_code");
        }
        if(empty($stockData))
            $stockData=[];
            // dd($stockData);

        return view('distributor.view',[
            'id'=>$id,
            'totalRetailer'=>$totalRetailer,
            // 'totalProductive'=>$totalProductive,
            // 'totalCall'=>$totalCall,
            'totalSku'=>$totalSku,
            'total_sale_value'=>$total_sale_value,
            // 'non_contacted_calls'=>$non_contacted_calls,
            'thresholdItem'=>$thresholdItem,
            'totalOrderValueArr'=>$totalOrderValueArr,
            // 'totalChallanValueArr'=>$totalChallanValueArr,
            'dealerData'=>$dealerData,
            'datesArr'=>$datesDisplayArr,
            'stockCategoryWise'=>$stockData,
              'from_date'=>$from_date,
            'to_date'=>$to_date,
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
        #decrypt id
        $uid = Crypt::decryptString($id);
        // dd($uid);
        $company_id = Auth::user()->company_id;

        #Dealer Ownership Type
        $ownership=DB::table('_dealer_ownership_type')->where('company_id',$company_id)->pluck('ownership_type','id');

        #Dealer
        $dealer=Dealer::where('company_id',$company_id)->find($uid);

// dd($dealer);
        $dlrl=DealerLocation::join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
            ->where('dealer_id',$uid)
            ->where('dealer_location_rate_list.company_id',$company_id)
            ->pluck('l7_id')->toArray();
        // dd($dlrl);
        $dlrl2=DealerLocation::join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
            ->where('dealer_id',$uid)
            ->where('dealer_location_rate_list.company_id',$company_id)
            // ->where('user_id',0)
            ->first();
            // dd($dlrl2);
        $town_id_array = DealerLocation::join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
        ->where('dealer_id',$uid)
        ->where('dealer_location_rate_list.company_id',$company_id)
        ->pluck('l6_id')->toArray();

        $l5_array = DealerLocation::join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
        ->where('dealer_id',$uid)
        ->where('dealer_location_rate_list.company_id',$company_id)
        ->pluck('l5_id')->toArray();


        $l4_array = DealerLocation::join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
        ->where('dealer_id',$uid)
        ->where('dealer_location_rate_list.company_id',$company_id)
        ->pluck('l4_id')->toArray();


        // dd($town_id_array);
        $location2=[];
        $location3=[];
        $location4=[];
        $location5=[];
        $csa=[];

        #Location 1 data
        $location1 = Location1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        #Location 2 data
        if (!empty($dlrl2->l1_id))
        $location2 = Location2::where('status', '=', '1')->where('company_id',$company_id)->where('location_1_id',$dlrl2->l1_id)->pluck('name', 'id');
        #Location 3 data
        if (!empty($dlrl2->l2_id))
        $location3 = Location3::where('status', '=', '1')->where('company_id',$company_id)->where('location_2_id',$dlrl2->l2_id)->pluck('name', 'id');
        #Location 4 data
        if (!empty($dlrl2->l3_id))
        $location4 = Location4::where('status', '=', '1')->where('company_id',$company_id)->where('location_3_id',$dlrl2->l3_id)->pluck('name', 'id');
        #Location 5 data
        if (!empty($dlrl2->l4_id))
        $location5 = Location5::where('status', '=', '1')->where('company_id',$company_id)->whereIn('location_4_id',$l4_array)->pluck('name', 'id');

        // dd($location5);
        if (!empty($dlrl2->l5_id)){
        $location6 = Location6::where('company_id',$company_id)->whereIn('location_5_id',$l5_array)->pluck('name', 'id');
         }
        else{
            $location6 = Location6::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        }

        if (!empty($dlrl2->l6_id)){
        $location7 = Location7::where('status', '=', '1')->where('company_id',$company_id)->whereIn('location_6_id',$town_id_array)->pluck('name', 'id');
        }
        else{
            $location7 = Location7::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        }

        // if (!empty($dlrl2->l3_id))
        $csa=DB::table('csa')->where('company_id',$company_id)->pluck('csa_name','c_id');
        //$csa=DB::table('csa')->where('state_id',$dlrl2->l3_id)->pluck('csa_name','c_id');

        $assign_price_list = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',5)->count();
        $template_product = DB::table('template_product')
                        // ->join('product_rate_list_template','product_rate_list_template.template_type','=','template_product.id')
                        ->where('template_product.status',1)
                        ->where('template_product.company_id',$company_id)
                        ->groupBy('template_product.id')
                        ->pluck('template_product.name as name','template_product.id as id');
        $assigned_dealer_data = DB::table('product_rate_list')->where('distributor_id',$uid)->where('company_id',$company_id)->first();
        $vehicle_details = DB::table('_vehicle_details')->where('company_id',$company_id)->where('status', '=', '1')->pluck('name','id');

        $dealer_personal_details = array();
        $vehicle_details_edit = array();
        if($company_id == 52)
        {
            $dealer_personal_details = DB::table('dealer_personal_details')->where('dealer_id',$uid)->first();
            $vehicle_details_edit = DB::table('dealer_vechicle_assign')->where('dealer_id',$uid)->pluck('vehicle_details_id')->toArray();
        }
        // dd($dealer);  
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
            'location6' => $location6,
            'location7' => $location7,
            'town_id_array'=> $town_id_array,
            'l4_array' => $l4_array,
            'l5_array' => $l5_array,
            'csa' => $csa,
            'encrypt_id'=>$id,
            'assign_price_list'=>$assign_price_list,
            'template_product'=> $template_product,
            'assigned_dealer_data'=> $assigned_dealer_data,
            'company_id'=>$company_id,
            'dealer_personal_details'=> $dealer_personal_details,
            'vehicle_details'=> $vehicle_details,
            'vehicle_details_edit'=> $vehicle_details_edit,
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
        $company_id = Auth::user()->company_id;
        $validate = $request->validate([
            'code' => 'required|min:2|max:40',
            'firm_name' => 'required|min:2|max:50',
            'landline' => 'max:12',
            'mobile' => 'max:10',
            'contact_person' => 'min:2|max:50',
            'location_1' => 'required',
            'location_2' => 'required',
            'location_3' => 'required',
            'location_4' => 'required',
            'location_5' => 'required',
            'location_6' => 'required',
            'location_7' => 'required',
        ]);
        DB::beginTransaction();
        $id = Crypt::decryptString($id);
        $dealer_data = Dealer::where('company_id',$company_id)->findOrFail($id);

        $beat=$request->location_7;
        /* Start Transaction*/
        
        $myArr = [
            'name' => trim($request->firm_name),
            'contact_person' => trim($request->contact_person),
            'dealer_code' => trim($request->code),
            'address' => trim($request->address),
            'email' => trim($request->email),
            'landline' => trim($request->landline),
            'other_numbers' => trim($request->mobile),
            'tin_no' =>trim($request->tin_no),
            'fssai_no' =>'',
            'pin_no' => trim($request->pin_no),
            'ownership_type_id' => trim($request->ownership_type),
            'avg_per_month_pur' => trim($request->avg_per_month),
            'state_id' => trim($request->location_3),
            'town_id' => trim($request->town_id),
            'csa_id' => trim($request->csa),
            'template_id'=>!empty($request->template_product_id)?$request->template_product_id:'0',
            'terms' => '',
            'dealer_status' => 1,
            'dms_status' => 0,
            'edit_stock' => 0
        ];


        $dealer=$dealer_data->update($myArr);
        if($company_id == 52)
        {
            $update_further_details_patanjali = DB::table('dealer_personal_details')->where('dealer_id',$id)->update([
                                               'bank_name'=>!empty($request->bank_name)?$request->bank_name:'na',
                                                'security_amt'=>!empty($request->security_amt)?$request->security_amt:'0',
                                                'refrence_no'=>!empty($request->refrence_no)?$request->refrence_no:'0',
                                                'security_date'=>!empty($request->security_date)?$request->security_date:'',
                                                'reciept_issue_date'=>!empty($request->reciept_issue_date)?$request->reciept_issue_date:'',
                                                'security_remarks'=>!empty($request->security_remarks)?$request->security_remarks:'na',
                                                'commencement_date'=>!empty($request->commencement_date)?$request->commencement_date:'',
                                                'termination_date'=>!empty($request->termination_date)?$request->termination_date:'',
                                                'certificate_issue_date'=>!empty($request->certificate_issue_date)?$request->certificate_issue_date:'',
                                                'agreement_remarks'=>!empty($request->agreement_remarks)?$request->agreement_remarks:'na',
                                                'refund_amt'=>!empty($request->refund_amt)?$request->refund_amt:'0',
                                                'refund_ref_no'=>!empty($request->refund_ref_no)?$request->refund_ref_no:'0',
                                                'refund_date'=>!empty($request->refund_date)?$request->refund_date:'',
                                                'refund_remarks'=>!empty($request->refund_remarks)?$request->refund_remarks:'na',
                                                'food_license'=>!empty($request->food_license)?$request->food_license:'0',
                                                'pan_no'=>!empty($request->pan_no)?$request->pan_no:'0',
                                                'aadar_no'=>!empty($request->aadar_no)?$request->aadar_no:'0',
                                                'updated_at'=>date('Y-m-d H:i:s'),
                                                'updated_by'=>Auth::user()->id,
                                            ]); 
            $delete_data = DB::table('dealer_vechicle_assign')->where('dealer_id',$id)->delete();

            foreach ($request->vehicle_details_array as $key => $value) 
            {
                $vehicle_insert = DB::table('dealer_vechicle_assign')->insert([
                                'company_id'=>$company_id,
                                'dealer_id'=>$id,
                                'vehicle_details_id'=>$value,
                                'created_at'=>date('Y-m-d H:i:s'),
                                'created_by'=>Auth::user()->id,
                            ]);
            }

        }

        // if(!empty($request->template_product_id))
        // {

        //     $assign_data = DB::table('product_rate_list_template')
        //             ->where('template_type',$request->template_product_id)
        //             ->where('company_id',$company_id)
        //             ->where('status',1)
        //             ->get();
        //     if(!empty($assign_data))
        //     {
        //         $delete_data = DB::table('product_rate_list')
        //                 ->where('distributor_id',$id)
        //                 ->where('company_id',$company_id)
        //                 // ->where('status',1)
        //                 ->delete();
        //         if($delete_data)
        //         {
        //             foreach($assign_data as $key => $value)
        //             {
        //                 $myArrAssign = [
        //                     'product_id' => $value->product_id,
        //                     'company_id' => $company_id,
        //                     'template_id' => $value->template_type,
        //                     'distributor_id' => $id,
        //                     'mrp' => $value->mrp,
        //                     'mrp_pcs' => $value->mrp_pcs,
        //                     'dealer_rate' => $value->dealer_rate,
        //                     'dealer_pcs_rate' => $value->dealer_pcs_rate,
        //                     'ss_case_rate' => $value->ss_case_rate,
        //                     'ss_pcs_rate' => $value->ss_pcs_rate,
        //                     'retailer_rate' => $value->retailer_rate,
        //                     'retailer_pcs_rate' => $value->retailer_pcs_rate,
        //                     'other_retailer_rate' => $value->other_retailer_rate,
        //                     'other_dealer_rate' => $value->other_dealer_rate,
        //                     'other_ss_rate' => $value->other_ss_rate,
        //                     'created_at'=>date('Y-m-d H:i:s'),
        //                 ];

        //                 $sumbit_query = DB::table('product_rate_list')->insert($myArrAssign);
        //             }    
        //         }
        //     }
            
        // }
        if (!empty($beat))
        {
            $user_data = [];
            $existing_beat_id = [];
            $old_beat_array = DealerLocation::where('dealer_id',$id)->where('company_id',$company_id)->distinct('location_id')->pluck('location_id')->toArray();
            // dd($old_beat_array);
            $new_beat_array = $beat;
            // dd($new_beat_array);
            $diffrence_first = array_diff($old_beat_array,$new_beat_array);
            $diffrence_second = array_diff($new_beat_array,$old_beat_array);
            $array_diifrence_final = array_merge($diffrence_first,$diffrence_second);
            // dd($array_diifrence_final);
            $query_for_existing_value = DealerLocation::whereNotIn('location_id',$new_beat_array)
                                        ->where('dealer_id',$id)
                                        ->where('company_id',$company_id)
                                        ->distinct('location_id')->get()->toArray();
            foreach ($query_for_existing_value as $key => $value) 
            {
                // dd($value);
                $existing_beat_id[] = $value["location_id"];
            }
            // dd($existing_beat_id);
            // DB::beginTransaction();
            $delete_edit_beat = DealerLocation::where('dealer_id',$id)
                                ->where('company_id',$company_id)
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
                        'company_id' => $company_id,
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
                    return redirect()->intended($this->current_menu);

                }
                else
                {
                    DB::rollback();
                    Session::flash('message', 'Something went wrong!');
                    Session::flash('class', 'danger');
                    return redirect()->intended($this->current_menu);

                }
                
            }
            else
            {
                if($delete_edit_beat)
                {
                    DB::commit();
                    Session::flash('message', Lang::get('common.'.$this->current_menu).' Updatedd successfully');
                    Session::flash('class', 'success');
                    return redirect()->intended($this->current_menu);
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
                        Session::flash('class', 'danger');
                    }
                    DB::rollback();
                    return redirect()->intended($this->current_menu);
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

        return redirect()->intended($this->current_menu);
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
        $company_id = Auth::user()->company_id;
        $company_name_data = Company::where('id',$company_id)->first();
        $company_name = $company_name_data->name;

        // $check = DB::table('retailer')
        //         ->where('username',$request->username)
        //         ->count();
        // if($check>0)
        // {
        //     Session::flash('message', ' Username Already exist please try again!!');
        //     Session::flash('class', 'warning');
        //     return redirect()->intended($this->current_menu);

        // }

        $check2 = DB::table('dealer_person_login')
                ->where('uname',trim(($request->username)))
                ->where('company_id',$company_id)
                ->count();
        if($check2>0)
        {
            Session::flash('message', 'Distributor Already Exist!!');
            Session::flash('class', 'danger');
            return redirect()->intended($this->current_menu);
        }


          $check2 = DB::table('dealer_person_login')
                ->where('uname',trim(($request->username).'@'.$company_name))
                ->where('company_id',$company_id)
                ->count();
        if($check2>0)
        {
            Session::flash('message', 'Distributor Already Exist!!');
            Session::flash('class', 'danger');
            return redirect()->intended($this->current_menu);
        }



        // $check = DB::table('dealer_person_login')
        //         ->where('uname',$request->username)
        //         ->count();
        // if($check>0)
        // {
        //     Session::flash('message', ' Username Already exist please try again!!');
        //     Session::flash('class', 'warning');
        //     return redirect()->intended($this->current_menu);

        // }

        $myArr=[
            'person_name' => trim($request->person_name),
            // 'uname' => trim($request->username),
            'uname' => trim(str_replace('@','_',$request->username).'@'.$company_name),
            'dealer_id' => $request->uuid,
            'state_id' => $request->state,
            'role_id' => $request->role_name,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'company_id' => $company_id,
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

        return redirect()->intended($this->current_menu);
    }

    /**
     * Update dealer user for login.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDealerUser(Request $request)
    {
        $validate = $request->validate([
            // 'person_name' => 'required|min:2|max:80',
            // 'username' => 'required|min:2|max:20',
            // 'uuid2' => 'required',
            // 'state' => 'required',
            // 'role_name' => 'required',
            'user_password' => 'max:20'
        ]);
        $company_id = Auth::user()->company_id;
        // $dpl=DealerPersonLogin::where('dealer_id',$request->uuid2)->where('company_id',$company_id);
         $dpl=DB::table('dealer_person_login')->where('dealer_id',$request->uuid2)->where('company_id',$company_id);
        // $myArr=[
        //     'person_name' => trim($request->person_name),
        //     'uname' => trim($request->username),
        //     'email' => trim($request->email),
        //     'dealer_id' => $request->uuid2,
        //     'state_id' => $request->state,
        //     'role_id' => $request->role_name,
        //     'activestatus' => 1
        // ];
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

        return redirect()->intended($this->current_menu);
    }
    public function get_dealer_person_details(Request $request)
    {
        $dealer_id = $request->dealer_id;
        $company_id = Auth::user()->company_id;
        $details = DB::table('dealer_person_login')
                ->join('location_3','location_3.id','=','dealer_person_login.state_id')
                ->select('location_3.name as l3_name','dealer_person_login.*',DB::raw("AES_DECRYPT(pass,'".Lang::get('common.db_salt')."') AS person_password"))
                ->where('dealer_person_login.dealer_id',$dealer_id)
                ->where('dealer_person_login.company_id',$company_id)
                ->first();
        if(!empty($details))
        {
            $data['code'] = 200;
            $data['result'] = $details;
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


     public function getDealerSecondarySales(Request $request)
    {
     
        $dealer_id = !empty($request->dealer_id)?explode(',',$request->dealer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


        $user_details = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                        ->select(DB::raw('SUM(rate*quantity) as sale_value'),'retailer.id as retailer_id','retailer.name as retailer_name','retailer.landline','retailer.track_address')
                        ->where('user_sales_order.dealer_id',$dealer_id)
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('retailer.id')
                        ->get();



    


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['sale_value'] = $value->sale_value;
                $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
                $out['retailer_name'] = $value->retailer_name;
                $out['landline'] = $value->landline;
                $out['track_address'] = $value->track_address;
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

     public function getDealerPrimarySales(Request $request)
    {
     
        $dealer_id = !empty($request->dealer_id)?explode(',',$request->dealer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


         $user_details_data = DB::table('purchase_order');
        $user_details_data->join('purchase_order_details', function($join)
                 {
                   $join->on('purchase_order_details.order_id', '=', 'purchase_order.order_id');
                   $join->on('purchase_order_details.purchase_inv', '=', 'purchase_order.challan_no');

                 })
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->where('dealer_id',$dealer_id)
            ->whereRaw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')<='$to_date'")
            ->select(DB::raw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d') as order_date"),DB::raw("ROUND(SUM(purchase_order_details.total_amount),3) AS total_sale_value"));
        $user_details = $user_details_data->groupBy('order_date')->get();



    


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['order_date'] = $value->order_date;
                $out['total_sale_value'] = $value->total_sale_value;
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

     public function getDealerSKUDetails(Request $request)
    {
     
        $dealer_id = !empty($request->dealer_id)?explode(',',$request->dealer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


        $user_details = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                        ->select(DB::raw('SUM(rate*quantity) as sale_value'),DB::raw('SUM(quantity) as total_qty'),'catalog_product.name as product_name')
                        ->where('user_sales_order.dealer_id',$dealer_id)
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('product_id')
                        ->get();


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['sale_value'] = $value->sale_value;
                $out['total_qty'] = $value->total_qty;
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

     public function getDealerRetailerDetails(Request $request)
    {
     
        $dealer_id = !empty($request->dealer_id)?explode(',',$request->dealer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


         $beat_array = DB::table('dealer_location_rate_list')
                    ->where('dealer_id',$dealer_id)
                    ->groupBy('location_id')
                    ->pluck('location_id')
                    ->toArray();

        $user_details = DB::table('retailer')
                        ->select('retailer.id as retailer_id','retailer.name as retailer_name','retailer.landline','retailer.track_address','retailer.address')
                        ->whereIn('retailer.location_id',$beat_array)
                        ->where('retailer_status','=','1')
                        ->groupBy('retailer.id')
                        ->get();



    


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['address'] = $value->address;
                $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
                $out['retailer_name'] = $value->retailer_name;
                $out['landline'] = $value->landline;
                $out['track_address'] = $value->track_address;
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
}
