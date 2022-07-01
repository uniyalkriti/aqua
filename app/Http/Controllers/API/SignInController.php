<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Image;

class SignInController extends Controller
{
    public function user_dealer_beat_retailer_data_test(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'uname' => 'required',
            'imei' => 'required',   
            'v_name' => 'required',
            'v_code' => 'required',
            'pass' => 'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $user_name = $request->uname;
        $imei = $request->imei;
        $password = $request->pass;
        $v_name = $request->v_name;
        $v_code = $request->v_code;
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
        $final_data_beat = array();
        $beat_data_string= array();
        $final_dealer_data= array();
        $dealer_data_string= array();
        $dealer_id = array();
        $beat_id = array();
        $retailer_data = array();
        $payment_collection_query = '';
        $challan_data_query = '';
        $final_dat = array();
        ##................................................. return the user id ...................................................##
        $user_id_query = DB::table('person_login')->join('person','person.id','=','person_login.person_id')->where('imei_number',$imei)->where('person_username',$user_name)->first(); 
        $user_id = $user_id_query->id; // return user id 
        $myArr=['version_code_name'=>"Version: $v_name/$v_code"];
        $update_query = DB::table('person')->where('id',$user_id)->update($myArr);

        ##....................................... return the dealer details on the behalf of user id ................................##
       $user_dealer_retailer_query = DB::table('dealer')->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')->select('dealer.name as name','dealer.id as dealer_id','l4_name as lname','l4_id as lid')->where('dealer_location_rate_list.user_id',$user_id)->groupBy('dealer.id')->get();

        foreach ($user_dealer_retailer_query as $key => $value)
        {
            $dealer_id[]=$value->dealer_id;
            $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string 
            $dealer_data_string['lid'] = "$value->lid"; // return the data in string 
            $dealer_data_string['lname'] = $value->lname;
            $dealer_data_string['name'] = $value->name;
            $final_dealer_data[] = $dealer_data_string; // merge all data in one array 
        }
        ##.................................... return the beat details  on the behalf of dealer_id .....................................##
        $beat_data = DB::table('dealer_location_rate_list')->join('location_5','location_5.id','=','dealer_location_rate_list.location_id')->select('dealer_location_rate_list.location_id as beat_id','location_5.name as name','dealer_location_rate_list.dealer_id as dealer_id')->whereIn('dealer_location_rate_list.dealer_id',$dealer_id)->groupBy('dealer_location_rate_list.location_id')->get();
        
        foreach($beat_data as $key => $value) 
        {
            $beat_id[] = $value->beat_id;
            $beat_data_string['beat_id'] = "$value->beat_id"; // return the data in string 
            $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string 
            $beat_data_string['name'] = "$value->name"; // return the data in string 
            $final_data_beat[] = $beat_data_string; // merge all data in one array 
        }
        ##................................ return the retailer details on the behalf of beat id  .....................................##   
        $retailer_id_data = DB::table('retailer')->select('retailer.tin_no as tin_no','location_5.name as beat_name','retailer.id as id','lat_long','seq_id','retailer.name as retailer_name','location_id','address','email','contact_per_name','landline')->join('user_dealer_retailer','user_dealer_retailer.retailer_id','=','retailer.id')->join('location_5','location_5.id','=','retailer.location_id')->whereIn('retailer.location_id',$beat_id)->groupBy('retailer.id')->get();
        
        foreach($retailer_id_data as $key => $value) 
        {
            $retailer_id = $value->id;
            $payment_collection_query = DB::table('payment_collection')->select(DB::raw('sum(total_amount) as paid'))->where('retailer_id',$retailer_id)->first();
            $challan_data_query = DB::table('challan_order')->select(DB::raw('sum(amount) as ch_amt'))->where('ch_retailer_id',$retailer_id)->first();
            $retailer_amt  = DB::table('payment_collection')->select('total_amount')->where('retailer_id',$retailer_id)->orderBy('pay_date_time','DESC')->first();
            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['lat_long'] = $value->lat_long;
            $lat_lng = explode(',',$retailer_data['lat_long']);
            $lat = $lat_lng[0];
            $lng = $lat_lng[1];
            $retailer_data['lat'] = $lat;
            $retailer_data['lng'] = $lng;
            $retailer_data['location_id'] = "$value->location_id";
            $retailer_data['address'] = $value->address;
            $retailer_data['email'] = $value->email;
            $retailer_data['tin_no'] = $value->tin_no;
            $retailer_data['contact_per_name'] = $value->contact_per_name;
            $retailer_data['landline'] = $value->landline;
            $retailer_data['seq_id'] = "$value->seq_id";
            $retailer_data['beat_name'] = $value->beat_name;
            $outstanding = !empty($payment_collection_query)?($payment_collection_query->paid)-($challan_data_query->ch_amt):0;
            $retailer_data['outstanding'] = "$outstanding";
            $last_amt = !empty($retailer_amt)?$retailer_amt:0;
            $retailer_data['last_amt'] = "$last_amt";
            $retailer_data['achieved'] = $challan_data_query->ch_amt;
            $retailer_data['last_date'] = "no date";
            $final_dat[] = $retailer_data;
        }
        #.............................return dealer , beat and retailer array with all details .................................##
        if(!empty($user_id) && !empty($dealer_id) && !empty($beat_id) && !empty($retailer_id))
        {
            return response()->json([ 'response' =>"TRUE",'dealer'=>$final_dealer_data,'beat'=>$final_data_beat,'retailer'=>$final_dat,'message'=>'Success!!']);
        }
        else
        {
            return response()->json([ 'response' =>"FALSE",'dealer'=>$final_dealer_data,'beat'=>$final_data_beat,'retailer'=>$final_dat,'message'=>'!!No Record Found!!']);
        }   
    }

    #testing purpose
    public function user_dealer_beat_retailer_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'uname' => 'required',
            'imei' => 'required',   
            'v_name' => 'required',
            'v_code' => 'required',
            'pass' => 'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $user_name = $request->uname;
        $imei = $request->imei;
        $company_id = !empty($request->company_id)?$request->company_id:'0';
        $password = $request->pass;
        $v_name = $request->v_name;
        $v_code = $request->v_code;
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
        $final_data_beat = array();
        $beat_data_string= array();
        $final_dealer_data= array();
        $dealer_data_string= array();
        $dealer_id = array();
        $beat_id = array();
        $retailer_data = array();
        $payment_collection_query = '';
        $challan_data_query = '';
        $final_dat = array();
        ##................................................. return the user id ...................................................##
        $user_id_query = DB::table('person_login')->join('person','person.id','=','person_login.person_id')->where('imei_number',$imei)->where('person_username',$user_name)->first(); 
        $user_id = $user_id_query->id; // return user id 
        $myArr=['version_code_name'=>"Version: $v_name/$v_code"];
        $update_query = DB::table('person')->where('id',$user_id)->update($myArr);

        ##....................................... return the dealer details on the behalf of user id ................................##
       $user_dealer_retailer_query = DB::table('dealer')->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')->select('dealer.name as name','dealer.id as dealer_id','l4_name as lname','l4_id as lid')->where('dealer_location_rate_list.user_id',$user_id)->groupBy('dealer.id')->get();

        foreach ($user_dealer_retailer_query as $key => $value)
        {
            $dealer_id[]=$value->dealer_id;
            $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string 
            $dealer_data_string['lid'] = "$value->lid"; // return the data in string 
            $dealer_data_string['lname'] = $value->lname;
            $dealer_data_string['name'] = $value->name;
            $final_dealer_data[] = $dealer_data_string; // merge all data in one array 
        }
        ##.................................... return the beat details  on the behalf of dealer_id .....................................##
        $beat_data = DB::table('dealer_location_rate_list')->join('location_5','location_5.id','=','dealer_location_rate_list.location_id')->select('dealer_location_rate_list.location_id as beat_id','location_5.name as name','dealer_location_rate_list.dealer_id as dealer_id')->whereIn('dealer_location_rate_list.dealer_id',$dealer_id)->groupBy('dealer_location_rate_list.location_id')->get();
        
        foreach($beat_data as $key => $value) 
        {
            $beat_id[] = $value->beat_id;
            $beat_data_string['beat_id'] = "$value->beat_id"; // return the data in string 
            $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string 
            $beat_data_string['name'] = "$value->name"; // return the data in string 
            $final_data_beat[] = $beat_data_string; // merge all data in one array 
        }
        ##................................ return the retailer details on the behalf of beat id  .....................................##   
        $retailer_id_data = DB::table('retailer')->select('sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_5.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline')
            ->join('location_5','location_5.id','=','retailer.location_id')
            ->join('person','person.id','=','retailer.created_by_person_id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->whereIn('retailer.location_id',$beat_id)
            ->where('retailer_status',1)
            ->groupBy('retailer.id')->get();


        $last_order_book = DB::table("user_sales_order")
                        ->groupBy('retailer_id')
                        ->select(DB::raw("CONCAT_WS(' ',date,time) as date_time"),'retailer_id')
                        ->orderBy('date_time','DESC')
                        ->pluck('date_time','retailer_id');

        $payment_collection_data = DB::table('payment_collection')->where('company_id',$company_id)->pluck(DB::raw('sum(total_amount) as paid'),'retailer_id');
        $challan_order_data = DB::table('challan_order')->where('company_id',$company_id)->pluck(DB::raw('sum(amount) as ch_amt'),'ch_retailer_id');
        $last_payment_collection_data = DB::table('payment_collection')->where('company_id',$company_id)->orderBy('pay_date_time','DESC')->pluck('total_amount','retailer_id');
        // dd($last_order_book);
        foreach($retailer_id_data as $key => $value) 
        {
            $retailer_id = $value->id;
            $payment_collection_query = !empty($payment_collection_data[$retailer_id])?$payment_collection_data[$retailer_id]:0;
            $challan_data_query = !empty($challan_order_data[$retailer_id])?$challan_order_data[$retailer_id]:0;
            $retailer_amt  = !empty($last_payment_collection_data[$retailer_id])?$last_payment_collection_data[$retailer_id]:0;
            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['lat_long'] = $value->lat_long;
            $lat_lng = explode(',',$retailer_data['lat_long']);
            $lat = $lat_lng[0];
            $lng = $lat_lng[1];
            $retailer_data['lat'] = $lat;
            $retailer_data['lng'] = $lng;
            $retailer_data['location_id'] = "$value->location_id";
            $retailer_data['address'] = $value->address;
            $retailer_data['email'] = $value->email;
            $retailer_data['tin_no'] = $value->tin_no;
            $retailer_data['contact_per_name'] = $value->contact_per_name;
            $retailer_data['landline'] = $value->landline;
            $retailer_data['seq_id'] = "$value->seq_id";

            $retailer_data['created_by'] = $value->user_name;
            $retailer_data['created_by_designation'] = $value->designation;
            $retailer_data['created_at'] = $value->created_on;
            $retailer_data['last_visit_date'] = !empty($last_order_book[$retailer_id])?$last_order_book[$retailer_id]:"No Oder book Yet";
            
            $retailer_data['beat_name'] = $value->beat_name;
            $outstanding = !empty($payment_collection_query)?($payment_collection_query->paid)-($challan_data_query->ch_amt):0;
            $retailer_data['outstanding'] = "$outstanding";
            $last_amt = !empty($retailer_amt)?$retailer_amt:0;
            $retailer_data['last_amt'] = "$last_amt";
            $retailer_data['achieved'] = $challan_data_query->ch_amt;
            $retailer_data['last_date'] = "no date";
            $final_dat[] = $retailer_data;
        }
        #.............................return dealer , beat and retailer array with all details .................................##
        if(!empty($user_id) && !empty($dealer_id) && !empty($beat_id) && !empty($retailer_id))
        {
            return response()->json([ 'response' =>"TRUE",'dealer'=>$final_dealer_data,'beat'=>$final_data_beat,'retailer'=>$final_dat,'message'=>'Success!!']);
        }
        else
        {
            return response()->json([ 'response' =>"FALSE",'dealer'=>$final_dealer_data,'beat'=>$final_data_beat,'retailer'=>$final_dat,'message'=>'!!No Record Found!!']);
        }   
    }

    // public function whtsapp_api(Request $request)
    // {
    //     require(__DIR__ . '/../../autoload.php');
    //       $messageBird = new MessageBirdClient('YOUR_ACCESS_KEY'); // Set your own API access key here.
    //       // Enable the whatsapp sandbox feature
    //       //$messageBird = new MessageBirdClient(
    //       //    'YOUR_ACCESS_KEY',
    //       //    null,
    //       //    [MessageBirdClient::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
    //       //);
    //       $content = new MessageBirdObjectsConversationContent();
    //       $hsm = new MessageBirdObjectsConversationHSMMessage();
    //       $hsmParamsName = new MessageBirdObjectsConversationHSMParams();
    //       $hsmParamsName->default = 'Bob';
    //       $hsmParamsWhen = new MessageBirdObjectsConversationHSMParams();
    //       $hsmParamsWhen->default = 'Tommorrow!';
    //       $hsmLanguage = new MessageBirdObjectsConversationHSMLanguage();
    //       $hsmLanguage->policy = MessageBirdObjectsConversationHSMLanguage::DETERMINISTIC_POLICY;
    //       //$hsmLanguage->policy = MessageBirdObjectsConversationHSMLanguage::FALLBACK_POLICY;
    //       $hsmLanguage->code = 'YOUR LANGUAGE CODE';
    //       $hsm->templateName = 'YOUR TEMPLATE NAME';
    //       $hsm->namespace = 'YOUR NAMESPACE';
    //       $hsm->params = array($hsmParamsName, $hsmParamsWhen);
    //       $hsm->language = $hsmLanguage;
    //       $content->hsm = $hsm;
    //       $message = new MessageBirdObjectsConversationMessage();
    //       $message->channelId = 'YOUR CHANNEL ID';
    //       $message->content = $content;
    //       $message->to = 'YOUR MSISDN';
    //       $message->type = 'hsm';
    //       try {
    //           $conversation = $messageBird->conversations->start($message);
    //           var_dump($conversation);
    //       } catch (Exception $e) {
    //           echo sprintf("%s: %s", get_class($e), $e->getMessage());
    //       }
    // }
}