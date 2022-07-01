<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;


class PaymentDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        session_start();
        // dd($_SESSION);
        $this->signup_status = !empty($_SESSION['iclientdigimetsignup_status'])?$_SESSION['iclientdigimetsignup_status']:'0';
        // dd($this->signup_status);
        if($this->signup_status == 0 || $this->signup_status == '0')
        {
            header('Location: http://baidyanathjhansi.msell.in/public/Signup');
            dd('1');
        }
        $auth_id = !empty($_SESSION['iclientdigimetid'])?$_SESSION['iclientdigimetid']:'0';
        $this->auth_id = $auth_id; 
        $this->dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
        $this->csa_id = !empty($_SESSION['iclientdigimetdata']['csa_id'])?$_SESSION['iclientdigimetdata']['csa_id']:'0';
        $this->dealer_code = !empty($_SESSION['iclientdigimetdata']['dealer_code'])?$_SESSION['iclientdigimetdata']['dealer_code']:'0';
        $this->role_id = !empty($_SESSION['iclientdigimetdata']['urole'])?$_SESSION['iclientdigimetdata']['urole']:'0';
        // $this->dealer_code = '20602';
        $this->current_menu = 'dms-payment-details'; 
        $this->date = '2021-01-01';
        if($auth_id != '0' )
        {
            // dd('1');
            $auth_id = $auth_id;

        }
        else {
            # code...
            // dd('11');
            header('Location: http://baidyanathjhansi.msell.in/client');
            dd('1');
        }
        // dd($auth_id);   

        // if()
    }
    public function index()
    {
        // dd('yo');
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        
        if($this->role_id == '1')
        {
            $payment_mast = DB::table('payment_mast')
                ->join('dealer','dealer.dealer_code','=','payment_mast.dealer_code')
                ->select('payment_mast.*','dealer.name as dealer_name')
                // ->where('payment_mast.dealer_code',$this->dealer_code)
                ->orderBy('payment_mast.dealer_code', 'desc')
                ->paginate($pagination);
        }
        else
        {
            $payment_mast = DB::table('payment_mast')
                ->join('dealer','dealer.dealer_code','=','payment_mast.dealer_code')
                ->select('payment_mast.*','dealer.name as dealer_name')
                ->where('payment_mast.dealer_code',$this->dealer_code)
                ->orderBy('payment_mast.dealer_code', 'desc')
                ->paginate($pagination);
        }
        

        return view('DMS/PaymentDetailsView.index', [
            'current_menu'=>$this->current_menu,
            'role_id'=> $this->role_id,
            'payment_mast'=>$payment_mast
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $bank_detail_mast = DB::table('bank_detail_mast')->where('status',1)
                ->get();
        return view('DMS/PaymentDetailsView.create',[
            'current_menu'=>$this->current_menu,
            'bank_detail_mast'=>$bank_detail_mast
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
        //
        $validate = $request->validate([

            "amount" => 'required',
            "payment_mode" => 'required',
            "deposit_bank_name" => 'required',
            "transaction_no" => 'required',
            "bank_detail" => 'required',
            "cheque_draft_no" => 'required',
            "date" => 'required',

        ]);
        if ($request->payment_mode == 'RTGS/NEFT') {
            # code...
            $transaction_no = $request->transaction_no;
        }
        else{
            $transaction_no = 0;
        }

        $date = date('Y-m-d',strtotime($request->date));

        
        DB::beginTransaction();

       $myArr = [
            'amount' => !empty($request->amount)?$request->amount:' ',
            'payment_mode' => !empty($request->payment_mode)?$request->payment_mode:' ',
            'deposit_bank_name' => !empty($request->deposit_bank_name)?$request->deposit_bank_name:' ',
            'transaction_no' => $transaction_no,
            'bank_detail' => !empty($request->bank_detail)?$request->bank_detail:' ',
            'cheque_draft_no' => !empty($request->cheque_draft_no)?$request->cheque_draft_no:' ',
            'date' => $date,
            'remark' => !empty($request->remark)?$request->remark:' ',
            'server_date_time' => date('Y-m-d H:i:s'),
            'dealer_id' => $this->dealer_code,
            'dealer_code' => $this->dealer_code,
            ];

            $payment_mast=DB::table('payment_mast')->insert($myArr);
            if ($payment_mast) 
        {
            DB::commit();
            Session::flash('message', 'Payment created successfully');
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
}