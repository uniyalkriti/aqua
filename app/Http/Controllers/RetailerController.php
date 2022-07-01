<?php

namespace App\Http\Controllers;

use App\Dealer;
use App\DealerLocation;
use App\DealerPersonLogin;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location6;
use App\Location7;
use App\Location5;
use App\PersonDetail;
use App\PersonLogin;
use App\Retailer;
use App\Stock;
use App\UserSalesOrder;
use App\CatalogProduct;
use DB;
use Auth;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class RetailerController extends Controller
{
    public function __construct()
    {
        $this->current_menu='retailer';

        $this->status_table='retailer';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;

        $retailer_name = $request->retailer;
        $outlet_type = $request->outlet_type;
        $dealer_name = $request->distributor;
        $status = $request->status;
        $beat_id = $request->beat;
        $company_id = Auth::user()->company_id;

        $from_date = '';
        $to_date = '';
        // dd($retailer_name);

        $q=Retailer::where('retailer.retailer_status','!=',9)
        // ->join('_retailer_outlet_category','_retailer_outlet_category.id','=','retailer.class')
        ->join('location_view','location_view.l7_id','=','retailer.location_id')
        ->leftJoin('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')
        ->leftJoin('person','person.id','=','retailer.created_by_person_id')
        ->join('dealer','retailer.dealer_id','=','dealer.id');
        #Retailer name filter
        if (!empty($request->search)) {
            $key = $request->search;
            $q->where(function ($subq) use ($key) {
                $subq->Where('retailer.name', 'LIKE',  '%'.$key.'%');
            });
        }
        #date filter 
        if(!empty($request->from_date) && !empty($request->from_date))
        {
            // $explodeDate = explode(" -", $request->date_range_picker);
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            // dd($to_date);
            $q->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'");
        }
        #Retailer type filter
        if (!empty($request->outlet)) {
        $outlet_type = $request->outlet;
        $q->whereIn('outlet_type_id', $outlet_type);
        }
        if (!empty($request->location_6)) {
        $q->whereIn('l6_id', $request->location_6);
        }
        if (!empty($request->location_5)) {
        $q->whereIn('l5_id', $request->location_5);
        }
        if (!empty($request->location_4)) {
        $q->whereIn('l4_id', $request->location_4);
        }
        if (!empty($request->location_3)) {
        $q->whereIn('l3_id', $request->location_3);
        }
        #Distributor filter
        if (!empty($request->distributor)) {
        $dealer_name = $request->distributor;
        $q->whereIn('dealer.id', $dealer_name);
        }
        #Retailer name filter
        if (!empty($status)) 
        {
            if($status==2)
            {
                $q->where('retailer.retailer_status', 0);                 
            }
            else
            {
                $q->where('retailer.retailer_status', $status); 
            }
        }

        #beat filter 
        if(!empty($beat_id))
        {
            $q->whereIn('retailer.location_id',$beat_id);
        }


        if (!empty($request->is_golden)) {
             if($request->is_golden==2)
            {
                $q->where('retailer.is_golden', 0);                 
            }
            else
            {
                $q->where('retailer.is_golden', $request->is_golden); 
            }
        }


         if (!empty($request->is_golden_approved)) {
             if($request->is_golden_approved==2)
            {
                $q->where('retailer.is_golden_approved', 0);                 
            }
            else
            {
                $q->where('retailer.is_golden_approved', $request->is_golden_approved); 
            }
        }

       

        #User filter
        if (!empty($request->user)) {
            $user_id = $request->user;
            $q->whereIn('retailer.created_by_person_id', $user_id);
            }

        if (!empty($request->retailer_id)) {
        $retailer_id = $request->retailer_id;
        $q->where('retailer.id', $retailer_id);
        }

        $data = $q->select('l3_name','l4_name','l5_name','l6_name','l7_name as beat_name','person.id as user_id','dealer_id','retailer.*','_retailer_outlet_type.outlet_type','person.first_name','person.middle_name','person.last_name','dealer.name as dealer_name')
        ->where('l7_company_id',$company_id)
        ->where('retailer.company_id',$company_id)
        // ->where('_retailer_outlet_type.company_id',$company_id)
        // ->orderBy('retailer.id', 'desc')
        ->orderByRaw('TRIM(retailer.name) ASC')
        ->paginate($pagination);
        //print_r($data);
        // $retailer_otp = DB::table('retailer_check_sms')->groupBy('retailer_id')->orderBy('id','DESC')->pluck('otp_number','retailer_id');

        $outlet_type=DB::table('_retailer_outlet_type')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->orderBy('_retailer_outlet_type.outlet_type','ASC')
        ->pluck('outlet_type','id');
        $retailer_name=DB::table('retailer')
        ->where('company_id',$company_id)
        ->where('retailer_status',1)
        ->orderBy('retailer.name','ASC')
        ->pluck('name','id');

        $dealer_name=DB::table('dealer')
        ->where('company_id',$company_id)
        ->where('dealer_status',1)
        ->orderBy('dealer.name','ASC')
        ->pluck('name','id'); 
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_status',1)
        ->orderBy('person.first_name','ASC')
        ->pluck('name', 'uid');

        $beat = DB::table('location_7')
        ->where('company_id',$company_id)
        ->orderBy('location_7.name','ASC')
        ->pluck('name', 'id');

        $location_6 = DB::table('location_6')
        ->where('company_id',$company_id)
        ->orderBy('location_6.name','ASC')
        ->pluck('name', 'id');

        $location_5 = DB::table('location_5')
        ->where('company_id',$company_id)
        ->orderBy('location_5.name','ASC')
        ->pluck('name', 'id');
        $location_4 = DB::table('location_4')
        ->where('company_id',$company_id)
        ->orderBy('location_4.name','ASC')
        ->pluck('name', 'id');

        $location_3 = DB::table('location_3')
        ->where('company_id',$company_id)
        ->orderBy('location_3.name','ASC')
        ->pluck('name', 'id');

        $class_outlet_category = DB::table('_retailer_outlet_category')
        ->where('company_id',$company_id)
        ->orderBy('_retailer_outlet_category.outlet_category','ASC')
        ->pluck('outlet_category', 'id');



        $sale_reaon_mark = DB::table('sale_reason_remarks')->where('company_id',$company_id)->groupBy('retailer_id')->pluck('sale_remarks','retailer_id');

        // dd($sale_reaon_mark);

        return view($this->current_menu.'.index', [
        'records' => $data,
        'status_table' => $this->status_table,
        'current_menu'=>$this->current_menu,
        'outlet_type' => $outlet_type,
        'retailer_name' => $retailer_name,
        'dealer_name' => $dealer_name,
        'beat' =>$beat,
        'user'=>$user,
        'sale_reaon_mark'=> $sale_reaon_mark,
        'location_5'=> $location_5,
        'location_6'=> $location_6,
        'from_date'=> $from_date,
        'to_date'=> $to_date,
        'location_3'=> $location_3,
        'location_4'=> $location_4,
        'class_outlet_category'=> $class_outlet_category,

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
        $location1 = Location1::where('status', '!=', '2')->where('company_id',$company_id)->pluck('name', 'id');

        #Dealer Ownership Type
        $ownership=DB::table('_dealer_ownership_type')->where('company_id',$company_id)->pluck('ownership_type','id');

        $outlet_type=DB::table('_retailer_outlet_type')
            ->where('status',1)
            ->where('company_id',$company_id)
            ->pluck('outlet_type','id');

        return view($this->current_menu.'.create',[
            'current_menu'=>$this->current_menu,
            'ownership' => $ownership,
            'location1' => $location1,
            'outlet_type' => $outlet_type
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
            'location_7' => 'required',
            'distributor' => 'required',
            'retailer_name' => 'required|min:2|max:30',
            'address' => 'required|min:2|max:200',
           
            'mobile' => 'required|min:10|max:10',
            'email' => 'required|max:50',
            'outlet_type' => 'required',
            'tin_no' => 'max:20',
            'pin_no' => 'max:20',
            'avg_per_month_pur' => 'max:11'
        ]);

        $beat=$request->location_7;
        $company_id = Auth::user()->company_id;
        // $sequence_data = Retailer::select('retailer_code')
        //             ->where('company_id',$company_id)
        //             ->orderBy('retailer_code','DESC')
        //             ->first();
        // $sequence_id = ($sequence_data->retailer_code)+2;

        $check = DB::table('retailer')
                ->where('username',$request->user_name)
                ->count();
        if($check>0)
        {
            Session::flash('message', Lang::get('common.'.$this->current_menu).'Username Already exist please try again!!');
            Session::flash('class', 'success');
            return redirect()->intended($this->current_menu);

        }
        /* Start Transaction*/
        DB::beginTransaction();
        $retailer_data = Retailer::orderBy('id','DESC')->where('company_id',$company_id)->first();
        $retailer_code = $retailer_data->retailer_code+1;
        $myArr = [
            'retailer_code' => $retailer_code,
            'location_id' => trim($request->location_7),
            'dealer_id' => trim($request->distributor),
            'name' => trim($request->retailer_name),
            'address' => trim($request->address),
            'track_address' => '',
            'landline' => trim($request->landline),
            'other_numbers' => trim($request->mobile),
            'email' =>trim($request->email),
            'outlet_type_id' => trim($request->outlet_type),
            'tin_no' =>trim($request->tin_no),
            'pin_no' => trim($request->pin_no),
            'company_id' => $company_id,
            'avg_per_month_pur' => trim($request->avg_per_month),
            'created_on' => date('Y-m-d H:i:s'),
            'created_by_person_id' => Auth::user()->id,
            'username' => $request->user_name,
            'is_golden' => $request->is_golden,
            'password'=>DB::raw("AES_ENCRYPT('".trim($request->password)."', '".Lang::get('common.db_salt')."')"),
            // 'password' => $request->password,
        ];
        

        $retailer=Retailer::create($myArr);


        if ($retailer) 
        {
            $mySequenceArr = [
                'sequence_id'=> $retailer_code,
                'retailer_id'=> $retailer->id,
                'user_id'=> Auth::user()->id,
                'dealer_id'=> $request->distributor,
                'company_id'=> $company_id,
                'created_at'=> date('Y-m-d H:i:s'),

            ];
            $sequence_retailer = DB::table('user_retailer_sequence')->insert($mySequenceArr);
            if($sequence_retailer && $retailer)
            {
                DB::commit();
                Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
                Session::flash('class', 'success');
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
        $date = date("Y-m-d");
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        $RetailerData=Retailer::leftJoin('location_view','location_view.l7_id','=','retailer.location_id','left')
        ->leftJoin('dealer','dealer.id','=','retailer.dealer_id')
        ->leftJoin('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')
        ->select('retailer.image_name as image_name','retailer.name as name','retailer.retailer_code as retailer_code','retailer.email as email','retailer.other_numbers as mobile','retailer.landline as landline','retailer.tin_no as tin_no','location_view.*','_retailer_outlet_type.outlet_type as outlet_type_name','dealer.name as dealer_name')
        ->where('retailer.company_id',$company_id)
        // ->where('dealer.company_id',$company_id)
        // ->where('_retailer_outlet_type.company_id',$company_id)
        ->where('retailer.id',$uid)->first();

        $lastVist=DB::table('user_sales_order')
        ->select('retailer_id','date','time')
        ->where('retailer_id',$uid)
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$date'")
        ->where('user_sales_order.company_id',$company_id)
        ->orderBY('date','time','DESC')->first();

        $lastVistTime = !empty($lastVist->time)?$lastVist->time:'';
        $lastVistDate = !empty($lastVist->date)?$lastVist->date:'';


        $totalSku = DB::table('user_sales_order')
        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        ->where('retailer_id',$uid)
        ->where('user_sales_order.company_id',$company_id)
        ->where('user_sales_order_details.company_id',$company_id)
        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        ->distinct('product_id')
        ->count('product_id');

        if(empty($check)){
       $total_sale_value = DB::table('user_sales_order')
               ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
               ->where('retailer_id',$uid)->where('user_sales_order.company_id',$company_id)
               ->where('user_sales_order_details.company_id',$company_id)
               ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
               ->sum(DB::raw('rate*quantity'));
        }else{
            $total_sale_value = DB::table('user_sales_order')
            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            ->where('retailer_id',$uid)
            ->where('user_sales_order.company_id',$company_id)
            ->where('user_sales_order_details.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->sum(DB::raw('final_secondary_rate*final_secondary_qty'));   
        }
       
        $stockCategoryWise=DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->join('catalog_view','catalog_view.product_id','=','user_sales_order_details.product_id','inner')
        ->select('c0_id','color_code','c0_name',DB::raw("SUM(rate*quantity) as stockQty"))
        ->where('user_sales_order.company_id',$company_id)
        ->where('user_sales_order_details.company_id',$company_id)
        ->where('retailer_id',$uid)
        ->groupBy('c0_id')->orderBY('c0_name','ASC')->get();
        // ----------sale stats -------------//
        // for($i = 1; $i <=  date('t'); $i++)
        // {
        // // add the date to the dates array
        // $datesArr[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        // $datesDisplayArr[] =  date('M') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        // }

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
            if(empty($check)){
            $totalOrderValueArr[]=UserSalesOrder::where('company_id',$company_id)->where(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')
            ->where('retailer_id',$uid)->sum('total_sale_value');
            }else{
                $totalOrderValueArr[]=UserSalesOrder::join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->where('user_sales_order.company_id',$company_id)->where(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')
                ->where('retailer_id',$uid)->sum(DB::raw('final_secondary_rate*final_secondary_qty'));   
            }
        }

        foreach($stockCategoryWise as $skey=>$sVal)
        {

            $label=$sVal->c0_name.' [<b>'.$sVal->stockQty.'<b>]';
            $stockData[]=array('label'=>"$label",'data'=>$sVal->stockQty, 'color'=>"$sVal->color_code");
        }
        if(empty($stockData))
            $stockData=[];

        return view($this->current_menu.'.view',[
            'id'=>$id,
            'lastVistTime'=>$lastVistTime,
            'lastVistDate'=>$lastVistDate,
            'totalSku'=>$totalSku,
            'total_sale_value'=>$total_sale_value,
            'totalOrderValueArr'=>$totalOrderValueArr,
            'RetailerData'=>$RetailerData,
            'datesArr'=>$datesDisplayArr,
            'stockCategoryWise'=>$stockData,
            'dashboard_retailer_id'=>$uid,
            'from_date'=>$from_date,
            'to_date'=>$to_date,
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
        $company_id = Auth::user()->company_id;

        $location1=[];
        $location2=[];
        $location3=[];
        $location4=[];
        $location5=[];
        $dealer=[];

        $retailer=Retailer::where('company_id',$company_id)->find($uid);
        $location=[];
        if (!empty($retailer->location_id))
        {
            $location=DB::table('location_view')
                ->where('l7_id',$retailer->location_id)
                ->first();
        }


            #Location 1 data
        $location1 = Location1::where('company_id',$company_id)->where('status', '=', '1')->pluck('name', 'id');

        #Dealer Ownership Type
        $ownership=DB::table('_dealer_ownership_type')->where('company_id',$company_id)->pluck('ownership_type','id');

        $outlet_type=DB::table('_retailer_outlet_type')
            ->where('status',1)
            ->where('company_id',$company_id)
            ->pluck('outlet_type','id');

        #Location 1 data
        $location1 = Location1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        #Location 2 data
        if (!empty($location->l1_id))
            $location2 = Location2::where('status', '=', '1')->where('company_id',$company_id)->where('location_1_id',$location->l1_id)->pluck('name', 'id');
        #Location 3 data
        if (!empty($location->l2_id))
            $location3 = Location3::where('status', '=', '1')->where('company_id',$company_id)->where('location_2_id',$location->l2_id)->pluck('name', 'id');
        #Location 4 data
        if (!empty($location->l3_id))
            $location4 = Location4::where('status', '=', '1')->where('company_id',$company_id)->where('location_3_id',$location->l3_id)->pluck('name', 'id');
        
        if (!empty($location->l4_id))
            $location5 = Location5::where('status', '=', '1')->where('company_id',$company_id)->where('location_4_id',$location->l4_id)->pluck('name', 'id');

        if (!empty($location->l5_id))
            $location6 = Location6::where('status', '=', '1')->where('company_id',$company_id)->where('location_5_id',$location->l5_id)->pluck('name', 'id');

        #Location 5 data
        if (!empty($location->l4_id))
            $location7 = Location7::where('status', '=', '1')->where('company_id',$company_id)->where('location_6_id',$location->l6_id)->pluck('name', 'id');

        if (!empty($location->l7_id))
        $dealer=DealerLocation::join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
            ->where('dealer.company_id',$company_id)
            ->where('dealer_location_rate_list.company_id',$company_id)
            ->where('location_id',$location->l7_id)
            ->where('user_id',0)
            ->groupBy('dealer_location_rate_list.dealer_id')
            ->pluck('dealer.name','dealer.id');

        return view($this->current_menu.'.edit',[
            'current_menu'=>$this->current_menu,
            'ownership' => $ownership,
            'location1' => $location1,
            'outlet_type' => $outlet_type,
            'retailer' => $retailer,
            'location' => $location,
            'encrypt_id'=>$id,
            'location2'=>$location2,
            'location3'=>$location3,
            'location4'=>$location4,
            'location5'=>$location5,
            'location6'=>$location6,
            'location7'=>$location7,
            'dealer'=>$dealer,
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
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;

        $validate = $request->validate([
            'location_7' => 'required',
            'distributor' => 'required',
            'retailer_name' => 'required|min:2|max:30',
            'address' => 'required|min:2|max:200',
            'mobile' => 'required|min:10|max:10',
            'email' => 'required|max:50',
            'outlet_type' => 'required',
            'tin_no' => 'max:20',
            'pin_no' => 'max:20',
            'avg_per_month_pur' => 'max:11'
        ]);

        $beat=$request->location_7;
        /* Start Transaction*/
        DB::beginTransaction();

        $check = DB::table('dealer_person_login')
                ->where('uname',$request->user_name)
                ->count();
        if($check>0)
        {
            Session::flash('message', Lang::get('common.'.$this->current_menu).' Username Already exist please try again!!');
            Session::flash('class', 'warning');
            return redirect()->intended($this->current_menu);

        }

        $myArr = [
           
            'location_id' => trim($request->location_7),
            'dealer_id' => trim($request->distributor),
            'name' => trim($request->retailer_name),
            'address' => trim($request->address),
            // 'track_address' => '',
            'landline' => trim($request->landline),
            'other_numbers' => trim($request->mobile),
            'email' =>trim($request->email),
            'outlet_type_id' => trim($request->outlet_type),
            'tin_no' =>trim($request->tin_no),
            'pin_no' => trim($request->pin_no),
            'created_by_person_id' => Auth::user()->id,
            'avg_per_month_pur' => trim($request->avg_per_month),
            // 'created_on' => date('Y-m-d H:i:s')'user_name' => $request->user_name,
            'username' => $request->user_name,
            'is_golden' => $request->is_golden,
            'password'=>DB::raw("AES_ENCRYPT('".trim($request->password)."', '".Lang::get('common.db_salt')."')"),
        ];

        $retailer=Retailer::where('company_id',$company_id)->find($uid);
        $update=$retailer->update($myArr);


        if ($update) {
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

    // public function retailerMap(Request $request)
    // {
    //     $arr = array();
    //     $retailer = Retailer::where('retailer.status','=',1)->where('retailer.lat_long','!=',null)->where('retailer.lat_long','!=','0.0,0.0')->select('lat_long','name')->get();
    // //  dd($retailer);
    // foreach($retailer as $key=>$value)
    // {
    //     $arr[]=$value->lat_long.",".$value->name;
        
    // }
    // $way= !empty($arr)?json_encode($arr):'';
    //     return view($this->current_menu.'.retailer_map', [
    //         'current_menu'=>$this->current_menu,
    //         'records' => $way
    //     ]);

    // }

    // public function retailerMap(Request $request)
    // {
    //     //$user_name = $request->user_name;
    //     $company_id = Auth::user()->company_id;
    //     $arr = array();
    //     //$retailer = _role::where('_role.status','=',1)->get();
    //     // $user=DB::table('user_dealer_retailer_view')
    //     //     ->select('retailer_lat_long as lat_long','retailer_name as name')
    //     //     ->where('retailer_status',1)
    //     //     ->where('p_company_id',$company_id)
    //     //     ->where('role_company_id',$company_id)
    //     //     ->where('r_company_id',$company_id)
    //     //     ->groupBy('retailer_id');

    //     $user=DB::table('retailer')
    //         ->join('location_view','location_view.l7_id','=','retailer.location_id')
    //         ->join('person','person.id','=','retailer.created_by_person_id')
    //         ->select('retailer.lat_long as lat_long','retailer.name as name')
    //         ->where('retailer_status',1)
    //         ->where('retailer.company_id',$company_id)
    //         ->where('l7_company_id',$company_id)
    //         ->groupBy('retailer.id');

    //     if (!empty($request->user)) 
    //     {
    //         $user_name = $request->user;
    //         $user->whereIn('person.id', $user_name);
    //     }
    //     if (!empty($request->region)) 
    //     {
    //         $region_name = $request->region;
    //         $user->whereIn('l3_id', $region_name);
    //     }
    //     $user_data=$user->get();
    //     // dd($user_data);
    //     if($user_data->count()<=0)
    //     {
    //         Session::flash("flash_notification", [
    //         "level" => "danger",
    //         "message" => "! No Record Found !"
    //         ]);
    //     }
    //     // $user_fltr=DB::table('user_dealer_retailer_view')
    //     // ->where('retailer_status',1)
    //     // ->where('p_company_id',$company_id)
    //     // ->where('role_company_id',$company_id)
    //     // ->where('r_company_id',$company_id)
    //     // ->pluck('user_name','user_id');

    //      $user_fltr=DB::table('person')
    //      ->join('person_login','person_login.person_id','=','person.id')
    //     ->where('person_status',1)
    //     ->where('person.company_id',$company_id)
    //     ->where('person_login.company_id',$company_id)
    //     ->pluck(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'),'person.id');

    //     $region_fltr=DB::table('location_3')
    //     ->where('company_id',$company_id)
    //     ->where('status',1)
    //     ->pluck('name','id');

    //     foreach($user_data as $key=>$value)
    //     { 
    //         $retailer_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->name);
    //         if(strpos($value->lat_long,',') !== false){
    //         $lat_lng1 = explode(',', $value->lat_long);
    //         $lat = $lat_lng1[0];
    //         $lng = $lat_lng1[1];
    //         if( $lng!=1 && $lng!=0 && $lng!=0.0)
    //         {
    //             $arr[]=$lat.",".$lng.",".$retailer_name;
    //         }
    //       }
    //     }
    //     // dd($arr);
    //     $way= !empty($arr)?json_encode($arr):'';
    //     // dd($way);
    //     return view($this->current_menu.'.retailer_map', [
    //     'current_menu'=>$this->current_menu,
    //     'records' => $way,
    //     'user_fltr' => $user_fltr,
    //     'region_fltr' => $region_fltr 

    //     ]);

    //  }

    public function retailerMap(Request $request)
    {
        //$user_name = $request->user_name;
        // $company_id = Auth::user()->company_id;
        $arr = array();
        //$retailer = _role::where('_role.status','=',1)->get();
        // $user=DB::table('user_dealer_retailer_view')
        //     ->select('retailer_lat_long as lat_long','retailer_name as name')
        //     ->where('retailer_status',1)
        //     ->where('p_company_id',$company_id)
        //     ->where('role_company_id',$company_id)
        //     ->where('r_company_id',$company_id)
        //     ->groupBy('retailer_id');
        $company_id = Auth::user()->company_id;

        $sale_data_custom = DB::table('user_sales_order')
                    ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->where('user_sales_order.company_id',$company_id);
                    if(!empty($request->month))
                    {
                        // $sale_data = DB::table('user_sales_order')
                        //             ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$request->month'")
                        //             ->pluck('retailer_id');
                        $sale_data_custom->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$request->month'");
                    }
                    if (!empty($request->user)) 
                    {
                        $user_name = $request->user;
                        $sale_data_custom->whereIn('user_sales_order.user_id', $user_name);
                    }
                    if (!empty($request->region)) 
                    {
                        $region_name = $request->region;
                        $sale_data_custom->where('l3_id', $region_name);
                    }
                    if (!empty($request->status)) 
                    {
                        if($request->status == 2)
                        {
                            $sale_data_custom->where('call_status', 0);
                        }
                        else
                        {
                            $status = $request->status;
                            $sale_data_custom->where('call_status', $status);
                        }
                        
                    }
        $sale_data = $sale_data_custom->pluck(DB::raw("CONCAT((rate*quantity),'|',call_status,'|',date,'|',time) as value"),'retailer_id');
        // dd($sale_data);
        $user=DB::table('retailer')
            ->join('location_view','location_view.l7_id','=','retailer.location_id')
            // ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
            // ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            ->join('person','person.id','=','retailer.created_by_person_id')
            ->select('retailer.id as retailer_id','retailer.lat_long as lat_long','retailer.name as name')
            ->where('retailer_status',1)
            ->where('retailer.company_id',$company_id)
            // ->where('user_sales_order.company_id',$company_id)
            ->groupBy('retailer.id');

        // if (!empty($request->user)) 
        // {
        //     $user_name = $request->user;
        //     $user->whereIn('user_sales_order.user_id', $user_name);
        // }
        if (!empty($request->region)) 
        {
            $region_name = $request->region;
            $user->where('l3_id', $region_name);
        }
       
        // if(!empty($request->month))
        // {
        //     // $sale_data = DB::table('user_sales_order')
        //     //             ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$request->month'")
        //     //             ->pluck('retailer_id');
        //     $user->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$request->month'");
        // }
        $user_data=$user->get();
        // dd($user_data);
        if($user_data->count()<=0)
        {
            Session::flash("flash_notification", [
            "level" => "danger",
            "message" => "! No Record Found !"
            ]);
        }
        // $user_fltr=DB::table('user_dealer_retailer_view')
        // ->where('retailer_status',1)
        // ->where('p_company_id',$company_id)
        // ->where('role_company_id',$company_id)
        // ->where('r_company_id',$company_id)
        // ->pluck('user_name','user_id');

        $user_fltr=DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id)
                ->pluck(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'),'person.id');

        $region_fltr=DB::table('location_3')
                    ->where('status',1)
                    ->where('company_id',$company_id)
                    ->pluck('name','id');
        $count_productive = array();
        $count_non_productive = array();
        // $retailer_data = 
        $first_value = '';
        $second_call_status = '';
        $third_date = '';
        $fourt_time = '';
        foreach($user_data as $key=>$value)
        { 
            
            $retailer_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->name);
            if(!empty($sale_data[$value->retailer_id]))
            {
                $array_custom = explode('|',$sale_data[$value->retailer_id]);

                if(COUNT($array_custom)>0)
                {
                    $first_value = $array_custom[0];
                    $second_call_status = $array_custom[1];
                    $third_date = $array_custom[2];
                    $fourt_time = $array_custom[3];
                }
                else
                {
                    $first_value = '0';
                    $second_call_status = '2';
                    $third_date = '';
                    $fourt_time = '00:00:00';
                }
            }
            else
            {
                    $first_value = '0';
                    $second_call_status = '2';
                    $third_date = '';
                    $fourt_time = '00:00:00';
            }
            


            if($second_call_status==1)
            {
                $count_productive[] = $value->retailer_id; 
            }
            else
            {
                $count_non_productive[] = $value->retailer_id; 
            }
            $sale_value = $first_value;
            if($second_call_status == 1)
            {
                $status_custom = 1;
            }
            else
            {
                $status_custom = 2;
            }
            if(strpos($value->lat_long,',') !== false){
            $lat_lng1 = explode(',', $value->lat_long);
            $lat = $lat_lng1[0];
            $lng = $lat_lng1[1];
            if( $lng!=1 && $lng!=0 && $lng!=0.0)
            {
                $arr[]=$lat.",".$lng.",".$retailer_name.",".$status_custom.",".$sale_value.",".$third_date." ".$fourt_time;
            }
          }
        }
        // dd($arr);
        $way= !empty($arr)?json_encode($arr):'';
        // dd($way);
        return view($this->current_menu.'.retailer_map', [
        'current_menu'=>$this->current_menu,
        'records' => $way,
        'user_fltr' => $user_fltr,
        'region_fltr' => $region_fltr ,
        'count_productive' => $count_productive,
        'count_non_productive' => $count_non_productive,

        ]);

    }

     public function getUser(Request $request){

        //$arr=array();
        $company_id = Auth::user()->company_id;
        $id = $request->id;
        $region = DB::table('person')
        ->join('location_3','location_3.id','=','person.state_id')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select('person.id as user_id',DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'))
        ->where('person.company_id',$company_id)
        ->where('location_3.id',$id)
        ->groupBy('user_id')
        ->get();
        $arr['result']=$region;
        // dd($arr);
        return json_encode($arr);
        


    }


      public function getRetailerSecondarySales(Request $request)
    {
     
        $retailer_id = !empty($request->retailer_id)?explode(',',$request->retailer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


        $user_details =  DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->select(DB::raw('SUM(rate*quantity) as sale'),'date')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->where('retailer_id',$retailer_id)
                        ->groupBy('date')
                        ->get();


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['date'] = $value->date;
                $out['sale'] = $value->sale;
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


     public function getRetailerSkuDetails(Request $request)
    {
     
        $retailer_id = !empty($request->retailer_id)?explode(',',$request->retailer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


        $user_details =  DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                        ->select(DB::raw('SUM(rate*quantity) as sale'),'catalog_product.id as product_id','catalog_product.name as product_name')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->where('retailer_id',$retailer_id)
                        ->groupBy('catalog_product.id')
                        ->get();


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['product_id'] = $value->product_id;
                $out['product_name'] = $value->product_name;
                $out['sale'] = $value->sale;
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


     public function getRetailerSecondarySalesDateSku(Request $request)
    {
     
        $retailer_id = !empty($request->retailer_id)?explode(',',$request->retailer_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';


        $user_details =  DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                        ->select(DB::raw('SUM(rate*quantity) as sale'),'date','catalog_product.name as product_name',DB::raw("SUM(quantity) as quantity"))
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->where('retailer_id',$retailer_id)
                        ->groupBy('date','user_sales_order_details.product_id')
                        ->get();


         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['date'] = $value->date;
                $out['sale'] = $value->sale;
                $out['product_name'] = $value->product_name;
                $out['quantity'] = $value->quantity;
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
