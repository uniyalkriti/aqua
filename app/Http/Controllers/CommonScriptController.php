<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Location2;
use App\Person;
use App\Location3;
use App\Dealer;
use App\UserDetail;
use App\MonthlyTourProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;



class CommonScriptController extends Controller
{
	public $successStatus=200;
	public $salt="Rajdhani";
	public $otpString = '0123456789';

	



    public function janakSalesOrderDetailsScript(Request $request)
    {
          $company_id = "50";
          // dd($company_id);


          $quantityPerCase = DB::table('catalog_product')
                            ->where('company_id',$company_id)
                            ->pluck('quantity_per_case','id')->toArray();



          $quantityPerOtherType = DB::table('catalog_product')
                            ->where('company_id',$company_id)
                            ->pluck('quantiy_per_other_type','id')->toArray();





          $salesData = DB::table('user_sales_order_details')
                      ->where('company_id',$company_id)
                      ->where('order_id','2021091720212929332933')
                      // ->where('final_secondary_qty','=','0')
                      // ->where('final_secondary_rate','=','0')
                      ->groupBy('id')
                      ->get();

                      // dd($salesData);

          // if( ($case_quantity != '0') || ($piece_quantity != '0')  || ($secondary_quantity != '0') ){


              foreach ($salesData as $key => $value) {
                $product_id = $value->product_id;

                $quantity_per_case = !empty($quantityPerCase[$product_id])?$quantityPerCase[$product_id]:'0';

                $quantity_per_other_type = !empty($quantityPerOtherType[$product_id])?$quantityPerOtherType[$product_id]:'0';


                  ////////////////////// for secondary quantity ////////////////////

                  $cases_converted_pcs_for_final = ($value->case_quantity*$quantity_per_case);

                  $pcs_for_final = $value->quantity;

                  $final_piece_qty = ($cases_converted_pcs_for_final)+($pcs_for_final);

                  if($quantity_per_other_type == 0){
                    $calculated_secondary_qty = 0;
                  }
                  else{
                    $calculated_secondary_qty = ($final_piece_qty/$quantity_per_other_type);
                  }


                  $final_secondary_quantity = ROUND(($calculated_secondary_qty+$value->secondary_quantity),3);



                  ////////////////////// for secondary rate ////////////////////
                  $cases_secondary_sale  = ($value->case_rate*$value->case_quantity);
                  $pcs_secondary_sale  = ($value->rate*$value->quantity);
                  $secondary_secondary_sale  = ($value->secondary_rate*$value->secondary_quantity);

                  if($final_secondary_quantity == 0){
                  $final_secondary_rate = 0;
                  }
                  else{
                  $final_secondary_rate = ROUND((($cases_secondary_sale+$pcs_secondary_sale+$secondary_secondary_sale)/($final_secondary_quantity)),3);
                  }

                  // dd($final_secondary_rate);

                  $updateOrder = DB::table('user_sales_order_details')
                                ->where('company_id',$company_id)
                                ->where('id',$value->id)
                                ->where('product_id',$product_id)  
                                ->where('order_id',$value->order_id)  
                                ->update([
                                  'final_secondary_qty' => $final_secondary_quantity,
                                  'final_secondary_rate' => $final_secondary_rate,
                                ]);

                  



              }






          // }

          




    }



    

	
    public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::join("person_login","person_login.person_id","=","person.id")->where('person_login.person_status',1)->where('person_id_senior',$code)
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





}
