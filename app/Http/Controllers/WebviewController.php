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
use App\SerwaUser;
use App\Vendor;
use DB;
use App\User;
use App\CivillianData;
use App\Person;
use App\_role;
use App\UserDetail;
use Session;
use Auth;
use App\UserTodaysAttendanceEnabledLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
// use Illuminate\Support\Facades\Session;
use PDF;


class WebviewController extends Controller
{
    public function __construct()
    {
        $this->current_menu='expenseWebview';
        $this->current_another='expense_webview';

        $this->status_table='expense_webview';
        $this->table = 'travelling_expense_bill';

    }


  
 
    public function expenseList(Request $request)
    {

        $user_id = $request->user_id;
        $company_id = $request->company_id;

        if(!empty($request->date_range_picker)){
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        }
        else{
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
        }

       


        Session::forget('juniordata');
        $datasenior_call=self::getJuniorUser($user_id);
        $datasenior = $request->session()->get('juniordata');
         if(empty($datasenior))
         {
             $expense_list = array();
          }
          else{

            $expense_list = DB::table('travelling_expense_bill')
                        ->join('person','person.id','=','travelling_expense_bill.user_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->select('travelling_expense_bill.*',DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'rolename','location_5.name as head_quarter')
                        ->where('travelling_expense_bill.company_id',$company_id)
                        ->where('travelling_expense_bill.status',0)
                         ->whereIn('travelling_expense_bill.user_id',$datasenior)
                         ->whereRaw("DATE_FORMAT(travelling_expense_bill.travellingDate, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(travelling_expense_bill.travellingDate, '%Y-%m-%d') <= '$to_date'")
                         ->groupBy('travelling_expense_bill.order_id')
                         ->get();


          }

      
        
        return view('expense_webview.expenseList',[
            'current_menu'=>$this->current_menu,
            'expense_list'=> $expense_list,
            'user_id'=> $user_id,
            'company_id'=> $company_id,
           
        ]);
    }

    public function expense_approve($id,$user_id,$company_id)
    {
    $expense_primary_id = $id;
    $login_user = $user_id;
    $company_id = $company_id;

    // return redirect('http://xotik.msell.in/public/ss_webview?user_id='.$login_user.'&company_id='.$company_id);

    $query = DB::table('travelling_expense_bill')
             ->where('company_id',$company_id)
            ->where('status',0)
            ->where('id',$expense_primary_id)
            ->get();

        return view('expense_webview.expenseEditApprove',[
            'current_menu'=>$this->current_menu,
            'query'=>$query,
            'expense_primary_id'=> $expense_primary_id,
            'login_user'=> $login_user,
            'company_id'=> $company_id,
            
        ]);


         
    }

    public function update_expense_approval(Request $request)
    {
        $expense_primary_id = $request->expense_primary_id;
        $login_user = $request->login_user;
        $company_id = $request->company_id;

        $fare = $request->fare;
        $da = $request->da;
        $telephoneExpense = $request->telephoneExpense;

        if($request->submit == "Approve"){
            $status = 1;
        }
        else{
            return redirect('http://btw.msell.in/public/expense_webview?user_id='.$login_user.'&company_id='.$company_id);
        }

        $previous_data = DB::table('travelling_expense_bill')
                        ->where('id',$expense_primary_id)
                        ->where('company_id',$company_id)
                        ->first();

        $total = $fare+$da+$telephoneExpense+$previous_data->hotel+$previous_data->postage+$previous_data->conveyance+$previous_data->misc;                

        $myArr = [
            "fare" => $fare,
            "da" => $da,
            "telephoneExpense" => $telephoneExpense,
            "total" => $total,
            "status" => $status,
            "expense_approved_by" => $login_user,
        ];

        $update_query = DB::table('travelling_expense_bill')
                        ->where('id',$expense_primary_id)
                        ->where('company_id',$company_id)
                        ->update($myArr);

    return redirect('http://btw.msell.in/public/expense_webview?user_id='.$login_user.'&company_id='.$company_id);


    }



    public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::where('person_id_senior',$code)
            ->select('id as user_id')->get();
            $num = count($details);  
            if($num>0)
            {
                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="")
                    {
                        //$product = collect([1,2,3,4]);
                        Session::push('juniordata', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        $this->getJuniorUser($res2->user_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="")
                    {
                        Session::push('juniordata', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            return 1;
    } 



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

  

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
     public function imagesWebview(Request $request)
    {

        $product_id = $request->product_id;
        $company_id = $request->company_id;


        $descriptionImage = DB::table('sku_description_images')
                            ->where('company_id',$company_id)
                            ->where('product_id',$product_id)
                            ->groupBy('id')
                            ->get()->toArray();

        $catalogImage = DB::table('catalog_product')
                        ->where('company_id',$company_id)
                        ->where('id',$product_id)
                        ->first();

        $firstImage = $catalogImage->image_name;
        $finalOutDesc = array();
        if(!empty($descriptionImage)){
            foreach ($descriptionImage as $dkey => $dvalue) {
                 $finalOutDesc[] = 'sku_description_images/'.$dvalue->image;
            }
        }
        $lastVideo = $catalogImage->video_name;  


        return view('expense_webview.productWebview',[
            'finalOutDesc'=> $finalOutDesc,
            'firstImage'=> $firstImage,
            'lastVideo'=> $lastVideo,
            'company_id'=> $company_id,
            'checkImg' => $firstImage,
            'checkLast' => $lastVideo,

           
        ]);
    }
    


      public function phpInfo(Request $request)
    {

        phpinfo();

    }
   
}

             