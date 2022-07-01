<?php

namespace App\Http\Controllers;

use App\Retailer;
use DB;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RetailerMoveCopyController extends Controller
{

    public function __construct()
    {
        $this->current_menu='retailermovecopy';

        $this->status_table='retailermovecopy';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if(!empty($request->distributor) && !empty($request->beat)){
        $beat = $request->beat;
        $dealer_name = $request->distributor;
       

        $q=Retailer::where('retailer.status','!=',2)
            ->join('location_view','location_view.l7_id','retailer.location_id')
            ->join('dealer','retailer.dealer_id','=','dealer.id');

       
        #Distributor filter
        if (!empty($request->distributor)) {
            $dealer_name = $request->distributor;
            $q->whereIn('dealer.id', $dealer_name);
        }

         #User filter
         if (!empty($request->beat)) {
            $beat_name = $request->beat;
            $q->whereIn('location_view.l7_id', $beat_name);
        }

        $data = $q->select('retailer.id as rid','retailer.name as rname','dealer.name as dealer_name','l6_name as l4_name','l7_name as l5_name')
            ->where('retailer.company_id',$company_id)
            ->orderBy('retailer.id', 'desc')
            ->get();

        $outlet_type=DB::table('_retailer_outlet_type')
            ->where('status',1)
            ->where('_retailer_outlet_type.company_id',$company_id)
            ->pluck('outlet_type','id');

            $beat=DB::table('location_7')
            ->where('status',1)
            ->where('location_7.company_id',$company_id)
            ->pluck('name','id');
            

            $dealer_name=DB::table('dealer')
            ->where('dealer_status',1)
            ->where('dealer.company_id',$company_id)
            ->pluck('name','id');


// dd($data);
        return view($this->current_menu.'.index', [
            'records' => $data,
            'status_table' => $this->status_table,
            'current_menu'=>$this->current_menu,
            'beat' => $beat,
            'dealer_name' => $dealer_name
           


        ]);
        }
        else{

            $beat=DB::table('location_7')
            ->where('status',1)
            ->where('location_7.company_id',$company_id)
            ->pluck('name','id');
            

            $dealer_name=DB::table('dealer')
            ->where('dealer_status',1)
            ->where('dealer.company_id',$company_id)
            ->pluck('name','id');

            return view($this->current_menu.'.index', [
                'current_menu'=>$this->current_menu, 
                 'beat' => $beat,
                'dealer_name' => $dealer_name,
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function moveRetailer(Request $request)
    {

        if(!empty($request->distributormove) && !empty($request->beatmove) && !empty($request->move)){

       $dealerid = $request->distributormove;
       $beat = $request->beatmove;
       $retailer = $request->move;
       $company_id = Auth::user()->company_id;
      
       
            if($_GET['action'] == "move")
            {
                            if (!empty($retailer))
                            {

                                foreach ($retailer as $k=>$d)
                                {
                                    $myArr=[
                                        'dealer_id'=>$dealerid[0],
                                        'location_id'=>$beat[0],
                                    ];

                                    $update = Retailer::where('id',$d)->where('retailer.company_id',$company_id)->update($myArr);
                                }
                                    if ($update) {
                                        Session::flash('message','Retailer Move Successfully');
                                        Session::flash('class', 'success');
                                    } else {
                                        Session::flash('message', 'Something went wrong!');
                                        Session::flash('class', 'danger');
                                    }
                            
                                    return redirect()->intended('retailermovecopy');
                            }
                            else {
                                return redirect()->intended('retailermovecopy');
                            }
            

            }
            elseif($_GET['action'] == "copy"){
                            if (!empty($retailer))
                            {

                                foreach ($retailer as $k=>$d)
                                {
                                    $retailerinfo = Retailer::where('id',$d)->where('retailer.company_id',$company_id)->select('retailer.*')->get();
                            
                                    foreach($retailerinfo as $info)
                                    {
                                        
                                        $myArr=[
                                            'retailer_code'=>$info->retailer_code,
                                            'name'=>$info->name,
                                            'class'=>$info->class,
                                            'image_name'=>$info->image_name,
                                            'dealer_id'=>$dealerid[0],
                                            'location_id'=>$beat[0],
                                            'company_id'=>$company_id,
                                            'address'=>$info->address,
                                            'beat_code'=>$info->beat_code,
                                            'email'=>$info->email,
                                            'contact_per_name'=>$info->contact_per_name,
                                            'landline'=>$info->landline,
                                            'other_numbers'=>$info->other_numbers,
                                            'tin_no'=>$info->tin_no,
                                            'pin_no'=>$info->pin_no,
                                            'outlet_type_id'=>$info->outlet_type_id,
                                            'card_swipe'=>$info->card_swipe,
                                            'bank_branch_id'=>$info->bank_branch_id,
                                            'current_account'=>$info->current_account,
                                            'avg_per_month_pur'=>$info->avg_per_month_pur,
                                            'lat_long'=>$info->lat_long,
                                            'mncmcclatcellid'=>$info->mncmcclatcellid,
                                            'track_address'=>$info->track_address,
                                            'created_on'=>date('Y-m-d H:i:s'),
                                            'created_by_person_id'=>$info->created_by_person_id,
                                            'status'=>$info->status,
                                            'sync_status'=>$info->sync_status,
                                            'retailer_status'=>$info->retailer_status,
                                            'deactivated_by_user'=>$info->deactivated_by_user,
                                            'deactivated_date_time'=>$info->deactivated_date_time,
                                            'battery_status'=>$info->battery_status,
                                            'gps_status'=>$info->gps_status,
                                        ];

                                        $insert=Retailer::create($myArr);
                                    }
                                }
                                        if ($insert) {
                                            Session::flash('message','Retailer Copy Successfully');
                                            Session::flash('class', 'success');
                                        } else {
                                            Session::flash('message', 'Something went wrong!');
                                            Session::flash('class', 'danger');
                                        }
                                
                                        return redirect()->intended('retailermovecopy');
                            }
                            else{
                                return redirect()->intended('retailermovecopy');
                            }

            }
            else{

                        return redirect()->intended('retailermovecopy');

            }
        }
        else {

            Session::flash('message', 'Please Select At Least One Retailer!');
            Session::flash('class', 'danger');

            return redirect()->intended('retailermovecopy');
        }
    }
}
