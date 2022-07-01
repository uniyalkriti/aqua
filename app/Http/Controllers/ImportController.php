<?php

namespace App\Http\Controllers;


use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5;
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\Dealer;
use App\User;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Person;
use App\PersonLogin;
use App\PersonDetail;
use App\Company;
use App\SchemePlan;
use App\SchemePlanDetails;
use App\ProductWiseSchemePlan;

use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use App\DealerLocation;
use Illuminate\Http\Request;
use DB;
use Auth;
use DateTime;
use Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;

class ImportController extends Controller
{
    #showing filter for master data onl;y 
    public function ImportData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $domain_name_custom = $_SERVER['REQUEST_URI'];
        $table_name = str_replace('/public/','', $domain_name_custom);
        if($table_name == 'ImportData')
        {
            return view('Import.index', [
                'company_id'=> $company_id,
            ]);
        }
        else
        {
            return view('Import.vedio', [
                'company_id'=> $company_id,
            ]);
        }
        // dd($table_name);
        
    }



     public function UploadData12(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $csv_file = $request->excelFile;
        if (($getfile = fopen($csv_file, "r")) !== FALSE) {
        $data = fgetcsv($getfile, 1000, ",");
         $inum=2;
         $query = " ";
         $ch_data=array();
         DB::beginTransaction();
        while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {

            $result = $data;
            $result1  = str_replace(",","",$result);  
            $str = implode(",", $result1);
            $slice = explode(",", $str);
            // dd($slice);
            
            $product_id = $slice[0];
            $eng = $slice[1];
            $hin = $slice[2];

            // dd($hin);
            // dd($slice);

          
                     


            $check= DB::table('catalog_product')
                        ->where('itemcode',$product_id)
                        ->where('company_id',$company_id)
                        ->first();


            // if(empty($check))
            // {
            //     dd('1');
            //     $message = $product_id;
            //     DB::rollback();
            //     Session::flash('message', $message);
            //     Session::flash('alert-class', 'alert-danger');
            //     return redirect('ImportData');

            // }
                        
            $updateData = DB::table('catalog_product')
                        ->where('itemcode','=',$product_id)
                        ->where('company_id',$company_id)
                        ->update([
                            'description_eng'=> '"'.$eng.'"',
                            'description_hind'=> '"'.$hin.'"',
                        ]);

            // dd($updateData);

            if ($updateData) {
                DB::commit();
                // Session::flash('message', 'Uploaded Succesfully');
                // Session::flash('alert-class', 'alert-success');
            }
            else {
                DB::rollback();
                // Session::flash('message', 'Something went wrong!');
                // Session::flash('alert-class', 'alert-danger');
            }
                        

            $inum++;   
                
        }

      
    }
    return redirect('ImportData ');
    }













    #showing filter for transactional data onl;y 
    public function UploadData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        

        if(!empty($request->excelFile)){


            if($request->submit=="UploadCordinate"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 // print_r($result);die;

                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 3) //condition for check appropriate file
                 {
                                $beat_id = $slice[1];
                                $beat_no = $slice[2];

                            
                          
                            $query = DB::table('location_7')->where('id',$beat_id)->where('company_id',$company_id)->update([

                                'beat_no' => $beat_no,
                              
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Beat file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
       
        if($request->submit=="UploadDealer")
        {
            // dd($request);
       $csv_file = $request->excelFile;
       if (($getfile = fopen($csv_file, "r")) !== FALSE) {
        $data = fgetcsv($getfile, 1000, ",");
         $inum=2;
         $query = " ";
         $ch_data=array();
         DB::beginTransaction();
        while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
            $result = $data;
            $result1  = str_replace(",","",$result);  
            $str = implode(",", $result1);
            $slice = explode(",", $str);
                if(count($slice) == 15) //condition for check appropriate file
                {
                        // print_r($slice);die;
                        $name = $slice[0];
                        $contact_person = $slice[1];
                        $dealer_code = $slice[2];
                        $address = $slice[3];
                        $email = $slice[4];
                        $landline = $slice[5];
                        $other_numbers = $slice[6];
                        $tin_no = $slice[7];
                        $fssai_no = $slice[8];
                        $pin_no = $slice[9];
                        $ownership_type_id = $slice[10];
                        $avg_per_month_pur = $slice[11];
                        $state_id = $slice[12];
                        $town_id = $slice[13];
                        $csa_id = $slice[14];
                        // $user_name = $slice[15];
                        // $password = $slice[16];
                        $dealer_status = 1;
                        $date = date('Y-m-d');
                        $time = date('H:i:s');
                        $lng = 0;
                        $lat = 0;
                        $mcc_mnc_lac_cellid = 0;
                        $created_at = date('Y-m-d H:i:s');
                        $created_by = Auth::user()->id;
                    
                    $check_l3 = DB::table('location_3')
                                        ->where('id',$state_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                    // dd($check_l3);
                    if($check_l3 < 0)
                    {
                        // dd($check_l3);
                        if($check_l3 == 0)
                        {
                            Session::flash('message', 'Please Enter correct location 3 id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');    
                        }
                        
                    }

// dd('stop'); 
                    $check_l6 = DB::table('location_6')
                                        ->where('id',$town_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                    if($check_l6 < 0)
                    {
                        if($check_l6 == 0)
                        {
                            Session::flash('message', 'Please Enter correct location 6 id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }
                    }

                    $check_ownership = DB::table('_dealer_ownership_type')
                                        ->where('id',$ownership_type_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                    if($check_ownership< 0)
                    {
                       if($check_ownership == 0)
                        {
                            Session::flash('message', 'Please Enter correct check_ownership id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }
                    }

                    

                    $query = Dealer::create([

                        'company_id' => $company_id,
                        'name' => $name,
                        'contact_person' => $contact_person,
                        'dealer_code' => $dealer_code,
                        'address' => $address,
                        'email' => $email,
                        'landline' => $landline,
                        'other_numbers' => $other_numbers,
                        'tin_no' => $tin_no,
                        'fssai_no' => $fssai_no,
                        'pin_no' => $pin_no,
                        'ownership_type_id' => $ownership_type_id,
                        'avg_per_month_pur' => $avg_per_month_pur,
                        'state_id' => $state_id,
                        'town_id' => $town_id,
                        'csa_id' => $csa_id,
                        'dealer_status' => $dealer_status,
                        'date' => $date,
                        'time' => $time,
                        'lng' => $lng,
                        'lat' => $lat,
                        'mcc_mnc_lac_cellid' => $mcc_mnc_lac_cellid,
                        'created_at' => $created_at,
                        'created_by' => $created_by,
                    
                    ]);

                    // $query_login = DB::table('dealer_person_login')->insert([
                    //                     'company_id'=>$company_id,
                    //                     'dealer_id'=>$query->id,
                    //                     'uname'=>$user_name,
                    //                     'pass'=>DB::raw("AES_ENCRYPT('".trim($password)."', '".Lang::get('common.db_salt')."')"),
                    //                     'role_id'=>231,
                    //                     'state_id'=>$state_id,
                    //                     'activestatus'=>1,
                    //                     'phone'=>$other_numbers,
                    //                     'email'=>$email,

                    //                 ]);

                    $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Dealer file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
        }

        if ($query) {
            DB::commit();
            Session::flash('message', 'Uploaded Succesfully');
            Session::flash('alert-class', 'alert-success');
        }
        else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }
    }
    return redirect('ImportData ');
    }
    if($request->submit=="UploadDealerCredentials")
    {
        $csv_file = $request->excelFile;
        if (($getfile = fopen($csv_file, "r")) !== FALSE) 
        {
            $data = fgetcsv($getfile, 1000, ",");
            $inum=2;
            $query = " ";
            $ch_data=array();
            DB::beginTransaction();
            while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) 
            {
                $result = $data;
                $result1  = str_replace(",","",$result);  
                $str = implode(",", $result1);
                $slice = explode(",", $str);
                if(count($slice) == 9) //condition for check appropriate file
                {
                    $srno = $slice[0];
                    $dealer_id = $slice[1];
                    $dealer_state = $slice[2];
                    $dealer_name = $slice[3];
                    $dealer_user_name = $slice[4];
                    $dealer_pass = $slice[5];
                    $dealer_mobile = $slice[6];
                    $dealer_email = $slice[7];
                    $current_date = $slice[8];
                    $created_at = date('Y-m-d H:i:s');
                    $created_by = Auth::user()->id;
                    $check_l3 = DB::table('location_3')
                                        ->where('id',$dealer_state)
                                        ->where('company_id',$company_id)
                                        ->count();
                    // dd($check_l3);
                    if($check_l3 < 0)
                    {
                        // dd($check_l3);
                        if($check_l3 == 0)
                        {
                            Session::flash('message', 'Please Enter correct location 3 id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');    
                        }
                        
                    }
                    $check_l3 = DB::table('dealer')
                                        ->where('id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                    if($check_l3 < 0)
                    {
                        if($check_l3 == 0)
                        {
                            Session::flash('message', 'Please Enter correct Dealer id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');    
                        }
                    }
                    $check_l3 = DB::table('dealer_person_login')
                                        ->where('dealer_id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                    if($check_l3 > 0)
                    {
                        
                            Session::flash('message', 'Already Exist Please contact to Adminstrator!!');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');    
                     
                        
                    }
                    
                    $check_l3 = DB::table('dealer_person_login')
                                        ->where('uname',$dealer_user_name)
                                        ->where('company_id',$company_id)
                                        ->count();
                    if($check_l3 > 0)
                    {
                        
                            Session::flash('message', 'User Name Already Exist Please Change And Then Try Again!!');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');    
                        
                        
                    }
                    $query_login = DB::table('dealer_person_login')->insert([
                                        'company_id'=>$company_id,
                                        'dealer_id'=>$dealer_id,
                                        'person_name'=>$dealer_name,
                                        'uname'=>$dealer_user_name,
                                        'pass'=>DB::raw("AES_ENCRYPT('".trim($dealer_pass)."', '".Lang::get('common.db_salt')."')"),
                                        'role_id'=>231,
                                        'state_id'=>$dealer_state,
                                        'activestatus'=>1,
                                        'phone'=>$dealer_mobile,
                                        'email'=>$dealer_email,
                                        'created_date'=>date('Y-m-d H:i:s'),

                                    ]);

                    $inum++;   
                } // if clause eds heree
                else {
                    Session::flash('message', 'Please select Dealer Credentials file!!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
            } // while loop ends here

            if ($query) {
                DB::commit();
                Session::flash('message', 'Uploaded Succesfully');
                Session::flash('alert-class', 'alert-success');
            }
            else {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
        } // if file open clause ends here 
    } // if corret clause hoose or not ends here
    return redirect('ImportData ');
    }
    // scheme starts here 
    if($request->submit=="UploadSchemePlan")
    {
        $csv_file = $request->excelFile;
        if (($getfile = fopen($csv_file, "r")) !== FALSE) 
        {
            $data = fgetcsv($getfile, 1000, ",");
            $inum=2;
            $query = " ";
            $ch_data=array();
            DB::beginTransaction();
            while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) 
            {
                $result = $data;
                $result1  = str_replace(",","",$result);  
                $str = implode(",", $result1);
                $slice = explode(",", $str);
                if(count($slice) == 6) //condition for check appropriate file
                {
                    $srno = $slice[0];
                    $scheme_name = $slice[1];
                    $scheme_category_status = 2;
                    $vs_status = 2;
                    $item_status = 2;
                    $status = 1;
                    $created_at = date('Y-m-d H:i:s');

                    $product_id = $slice[2];
                    $sale_unit = 3;
                    $sale_range_first = $slice[3];
                    $sale_range_last = $slice[4];
                    $incentive_type = 3;
                    $value_amount_percentage = $slice[5];
                    $check_l3 = DB::table('catalog_product')
                                        ->where('id',$product_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                    // dd($check_l3);
                    if($check_l3 < 0)
                    {
                        // dd($check_l3);
                        if($check_l3 == 0)
                        {
                            Session::flash('message', 'Please Enter correct Product id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');    
                        }
                        
                    }
                    $check_l4 = DB::table('scheme_plan')
                                        ->where('scheme_name',$scheme_name)
                                        ->where('company_id',$company_id)
                                        ->count();
                    if($check_l4 > 0)
                    {
                        $scheme_plan_details = SchemePlanDetails::create([
                                        'scheme_id' => $scheme_plan->id,
                                        'product_id' => $product_id,
                                        'sale_unit' => $sale_unit,
                                        'sale_value_range_first' => $sale_range_first,
                                        'sale_value_range_last' => $sale_range_last,
                                        'incentive_type' => $incentive_type,
                                        'value_amount_percentage' => $value_amount_percentage,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'company_id'=> $company_id,
                                    ]);
                    }
                    else
                    {
                        
                        $scheme_plan = SchemePlan::create([
                                        'scheme_name'=>$scheme_name,
                                        'scheme_category_status'=>$scheme_category_status,
                                        'vs_status'=>$vs_status,
                                        'item_status'=>$item_status,
                                        // 'pass'=>DB::raw("AES_ENCRYPT('".trim($dealer_pass)."', '".Lang::get('common.db_salt')."')"),
                                        'status'=>$status,
                                        'created_at'=>$created_at,
                                        'company_id'=>$company_id,
                                    ]);
                        $scheme_plan_details = SchemePlanDetails::create([
                                        'scheme_id' => $scheme_plan->id,
                                        'product_id' => $product_id,
                                        'sale_unit' => $sale_unit,
                                        'sale_value_range_first' => $sale_range_first,
                                        'sale_value_range_last' => $sale_range_last,
                                        'incentive_type' => $incentive_type,
                                        'value_amount_percentage' => $value_amount_percentage,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'company_id'=> $company_id,
                                    ]);
                    }
                    

                    $inum++;   
                } // if clause eds heree
                else {
                    Session::flash('message', 'Please select Scheme  file!!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
            } // while loop ends here

            if ($scheme_plan && $scheme_plan_details) {
                DB::commit();
                Session::flash('message', 'Uploaded Succesfully');
                Session::flash('alert-class', 'alert-success');
            }
            else {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
        } // if file open clause ends here 
    } // if corret clause hoose or not ends here
    return redirect('ImportData ');
    }

    // scheme ends here 

            if($request->submit=="UploadRetailer")
            {
           $csv_file = $request->excelFile;
           if (($getfile = fopen($csv_file, "r")) !== FALSE) {
            $data = fgetcsv($getfile, 1000, ",");
            $inum=2;
            $query = " ";
            $ch_data=array();
            DB::beginTransaction();
            while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                $curr_date = date('Y-m-d');
                $result = $data;
                $result1  = str_replace(",","",$result);  
                $str = implode(",", $result1);
                $slice = explode(",", $str);
                // print_r($slice);die;
                if(count($slice) == 16) //condition for check appropriate file
                {
                            $retailer_code = $slice[0];
                            $name = $slice[1];
                            $dealer_id = $slice[2];
                            $location_id = $slice[3];
                            $address = $slice[4];
                            $email = $slice[5];
                            $contact_per_name = $slice[6];
                            $contact_number = $slice[7];
                            $retailer_class = $slice[8];
                            $landline = $slice[9];
                            $other_numbers = $slice[10];
                            $tin_no = $slice[11];
                            $pin_no = $slice[12];
                            $outlet_type_id = $slice[13];
                            $card_swipe = 0;
                            $bank_branch_id = 0;
                            $current_account = 0;
                            $avg_per_month_pur = $slice[14];
                            $lat_long = 0;
                            $mncmcclatcellid = "false";
                            $track_address = $slice[15];
                            $created_on = date("Y-m-d H:m:s");
                            $created_by_person_id = Auth::user()->id;
                            $status = 1;
                            $sequence_id = 1;
                            $sync_status = 1;
                            $retailer_status = 1;
                            $deactivated_by_user = 0;
                            $deactivated_date_time = "1970-01-01 00:00:00";
                            $battery_status = 0;
                            $gps_status = 0;

                        $check = Retailer::where('other_numbers',$other_numbers)
                                ->where('name',$name)
                                ->where('company_id',$company_id)
                                ->where('location_id',$location_id)
                                ->count();
                                // dd($check);
                        if($check>0)
                        {
                            Session::flash('message', 'Duplicate Retailer Exist! Retailer Name :'.$name.' '.'Number :'.$other_numbers.' '.'beat Id :'.$location_id);
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }


                        $check_l7 = DB::table('location_7')
                                        ->where('id',$location_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                        if($check_l7<=0)
                        {
                            Session::flash('message', 'Please Enter correct location 7 id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }

                        $check_dealer = DB::table('dealer')
                                        ->where('id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->count();

                        if($check_dealer<=0)
                        {
                            Session::flash('message', 'Please Enter correct dealer id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }

                        $check_outlet_type = DB::table('_retailer_outlet_type')
                                        ->where('id',$outlet_type_id)
                                        ->where('company_id',$company_id)
                                        ->count();

                        if($check_outlet_type<=0)
                        {
                            Session::flash('message', 'Please Enter correct _retailer_outlet_type id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }
                        
                        $query = Retailer::insert([

                            'retailer_code' => $retailer_code,
                            'name' => $name,
                            'class' => $retailer_class,
                            'dealer_id' => $dealer_id,
                            'location_id' => $location_id,
                            'company_id' => $company_id,
                            'address' => $address,
                            'email' => $email,
                            'contact_per_name' => $contact_per_name,
                       
                      
                            'landline' => $landline,
                            'other_numbers' => $other_numbers,
                            'tin_no' => $tin_no,
                            'pin_no' => $pin_no,
                            'outlet_type_id' => $outlet_type_id,
                            'card_swipe' => $card_swipe,
                            'bank_branch_id' => $bank_branch_id,
                            'current_account' => $current_account,
                            'avg_per_month_pur' => $avg_per_month_pur,
                            'lat_long' => $lat_long,
                            'mncmcclatcellid' => $mncmcclatcellid,
                            'track_address' => $track_address,
                            'created_on' => $created_on,
                            'created_by_person_id' => $created_by_person_id,
                            'status' => $status,
                            'sequence_id' => $sequence_id,
                            'sync_status' => $sync_status,
                            'retailer_status' => $retailer_status,
                            'deactivated_by_user' => $deactivated_by_user,
                            'deactivated_date_time' => $deactivated_date_time,
                            'battery_status' => $battery_status,
                            'gps_status' => $gps_status,
                        
                        ]);

                        $inum++;   
                }
                else {
                    Session::flash('message', 'Please select Retailer file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
            }

            if ($query) {
                DB::commit();
                Session::flash('message', 'Uploaded Succesfully');
                Session::flash('alert-class', 'alert-success');
            }
            else {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
            }
        }
        return redirect('ImportData');
        }

        if($request->submit=="UploadProduct"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 14) //condition for check appropriate file
                 {
                        $name = $slice[0];
                        $itemcode= $slice[1];
                        $hsn_code = $slice[2];
                        $division = 1;
                        $weight_type_id = $slice[3];
                        $weight = $slice[4];
                        $catalog_id = $slice[5];
                        $primary_unit = $slice[6];
                        $quantity_per_primary = $slice[7];
                        $gst_per_unit = $slice[8];
                        $secondary_unit = $slice[9];
                        $quantity_per_secondary = $slice[10];

                        $description_eng = $slice[11];
                        $description_hindi = $slice[12];
                        $brand_details = $slice[13];


                        $taxable = 5;
                        $status = 1;
                        $created_at = $curr_date;
                        $created_by = Auth::user()->id;
                        $updated_at = $curr_date;
                        
                        $check_category = DB::table('catalog_2')
                                        ->where('id',$catalog_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                        if($check_category<=0)
                        {
                            Session::flash('message', 'Please Enter correct product id of this company');
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }

                        $duplicated_check = DB::table('catalog_product')
                                        ->where('name',$name)
                                        ->where('catalog_id',$catalog_id)
                                        ->where('status',1)
                                        ->where('company_id',$company_id)
                                        ->first();
                        if(!empty($duplicated_check))
                        {
                            Session::flash('message', 'Duplicate Entry Please Check'.' Sku Name: '.$duplicated_check->name.' Product Category Id: '.$duplicated_check->catalog_id);
                            Session::flash('alert-class', 'alert-danger');
                            return redirect('ImportData ');
                        }

                            $query = CatalogProduct::insert([

                                'name' => $name,
                                'itemcode' => $itemcode,
                                'hsn_code' => $hsn_code,
                                'division' => $division,
                                'weight_type_id' => $weight_type_id,
                                'weight' => $weight,
                                'catalog_id' => $catalog_id,
                                'base_price' => 0,
                                'product_type_primary'=>$primary_unit,
                                'quantity_per_case'=>$quantity_per_primary,
                                'gst_percent'=>$gst_per_unit,
                                'product_type'=>$secondary_unit,
                                'quantiy_per_other_type'=>$quantity_per_secondary,
                                'taxable' => $taxable,
                                'company_id' => $company_id,
                                'status' => $status,
                                'created_at' => $created_at,
                                'created_by' => $created_by,
                                'updated_at' => $updated_at,

                                'description_eng' => $description_eng,
                                'description_hind' => $description_hindi,
                                'brand_details' => $brand_details,


                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Product file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        if($request->submit=="UploadProductRateList")
        {

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) 
            {
                 $data = fgetcsv($getfile, 1000, ",");
                 $inum=2;
                 $query = " ";
                 $ch_data=array();
                 DB::beginTransaction();
                 while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                     $curr_date = date('Y-m-d H:m:s');
                    //  dd($curr_date);
                     $result = $data;
                     $result1  = str_replace(",","",$result);  
                     $str = implode(",", $result1);
                     $slice = explode(",", $str);
                     // print_r($slice);die;


                     if(count($slice) == 14) //condition for check appropriate file
                     {
                            $srno = $slice[0];
                            $product_id = $slice[1];  
                            $state_id = $slice[2];

                            $mrp      = $slice[4];                           
                            $mrp_pcs    = $slice[5];                             
                            $dealer_rate   = $slice[6];                              
                            $dealer_pcs_rate = $slice[7];
                            $retailer_rate = $slice[8];
                            $retailer_pcs_rate = $slice[9];
                            $ss_case_rate = !empty($slice[10])?$slice[10]:'0';
                            $ss_pcs_rate = !empty($slice[11])?$slice[11]:'0';
                            $other_retailer_rate = !empty($slice[12])?$slice[12]:'0';
                            $other_dealer_rate = !empty($slice[13])?$slice[13]:'0';

                            $check_sku = DB::table('catalog_product')
                                        ->where('id',$product_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_sku<=0)
                            {
                                Session::flash('message', 'Please Enter correct sku id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }
                            $check_state = DB::table('location_3')
                                        ->where('id',$state_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_state<=0)
                            {
                                Session::flash('message', 'Please Enter correct State id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $check_data = DB::table('product_rate_list')
                                        ->where('state_id',$state_id)
                                        ->where('product_id',$product_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            // dd($check_data);
                            if($check_data>0)
                            {
                                // dd('1');
                                Session::flash('message', 'Rate List Already Exist On this state and Product'.'state_id'.$state_id.'product id'.$product_id);
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            // $update_check = DB::table('product_rate_list')
                            //             ->where('product_id',$product_id)

                            $query = DB::table('product_rate_list')->insert([

                                'state_id'=>$state_id,
                                'distributor_id'=>0,
                                'ss_id'=>0,
                                'product_id' => $product_id,
                                'mrp' => $mrp,
                                'mrp_pcs' => $mrp_pcs,
                                'dealer_rate' => $dealer_rate,
                                'dealer_pcs_rate' => $dealer_pcs_rate,
                                'retailer_rate' => $retailer_rate,
                                'retailer_pcs_rate' => $retailer_pcs_rate,
                                'ss_case_rate'=> $ss_case_rate,
                                'ss_pcs_rate'=> $ss_pcs_rate,
                                'other_retailer_rate'=> $other_retailer_rate,
                                'other_dealer_rate'=> $other_dealer_rate,
                                'created_at'=>$curr_date,
                                'company_id'=> $company_id,
                                // 'ss_id'=>1,
                            
                            ]);
                
                            $inum++;   
                     }
                    else 
                    {
                        Session::flash('message', 'Please select UploadProductRateList file!');
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }
                 }
     
                if ($query) 
                {
                    DB::commit();
                    Session::flash('message', 'Uploaded Succesfully');
                    Session::flash('alert-class', 'alert-success');
                }
                else 
                {
                    DB::rollback();
                    Session::flash('message', 'Something went wrong!');
                    Session::flash('alert-class', 'alert-danger');
                }
            }
            return redirect('ImportData');
        }

        if($request->submit=="UploadLocation7"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 3) //condition for check appropriate file
                 {
                                $name = $slice[0];
                                $location_4_id = $slice[1];
                                $beat_no = $slice[2];
                                $day = 1;
                                $status = 1;
                                $created_at = $curr_date;
                                $updated_at = $curr_date;
                                

                            $check_l6 = DB::table('location_6')
                                        ->where('id',$location_4_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l6<=0)
                            {
                                Session::flash('message', 'Please Enter correct location_6 id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }
                            $check_l7 = DB::table('location_7')
                                        ->where('name',$name)
                                        ->where('location_6_id',$location_4_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l7>0)
                            {
                                Session::flash('message', 'Duplicate Entry Please Check beat name:'.$name);
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }
                            
                            $query = Location7::insert([

                                'name' => $name,
                                'location_6_id' => $location_4_id,
                                'beat_no' => $beat_no,
                                'company_id' => $company_id,
                                'status' => $status,
                                'created_at' => $created_at,
                                'updated_at' => $updated_at,
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Beat file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }


        if($request->submit=="UploadLocation6"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 3) //condition for check appropriate file
                 {
                                $name = $slice[1];
                                $location_3_id = $slice[2];
                                $status = 1;
                                $updated_at = $curr_date;
                                $created_at = $curr_date;
                                
                            $check_l5 = DB::table('location_5')
                                        ->where('id',$location_3_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l5<=0)
                            {
                                Session::flash('message', 'Please Enter correct location 5 id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }
                            $duplicate_entry = DB::table('location_6')
                                            ->where('name',$name)
                                            ->where('location_5_id',$location_3_id)
                                            ->where('status',1)
                                            ->where('company_id',$company_id)
                                            ->first();
                            if(!empty($duplicate_entry))
                            {
                                Session::flash('message', 'Duplicate Entry Please check it!'.' Name: '.$duplicate_entry->name.' location_5_id: '.$duplicate_entry->location_5_id);
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $query = Location6::insert([

                                'name' => $name,
                                'location_5_id' => $location_3_id,
                                'company_id' => $company_id,
                                'status' => $status,
                                'updated_at' => $updated_at,
                                'created_at' => $created_at,
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Town file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        if($request->submit=="UploadLocation5"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 4) //condition for check appropriate file
                 {
                                $name = $slice[1];
                                $location_4_id = $slice[2];
                                $status = 1;
                                $updated_at = $curr_date;
                                $created_at = $curr_date;
                            $check= DB::table('location_5')
                                    ->where('name',$name)
                                    ->where('location_4_id',$location_4_id)
                                    ->where('status',1)
                                    ->count();
                            if($check>0)
                            {
                                Session::flash('message', 'Dupolicate Entry Exist Please correct it and the return back for upload!');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData');

                            }

                            $check_l4 = DB::table('location_4')
                                        ->where('id',$location_4_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l4<=0)
                            {
                                Session::flash('message', 'Please Enter correct location 4 id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }
                            
                            $query = Location5::insert([

                                'name' => $name,
                                'location_4_id' => $location_4_id,
                                'company_id' => $company_id,
                                'status' => $status,
                                'updated_at' => $updated_at,
                                'created_at' => $created_at,
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Town file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
         if($request->submit=="UploadLocation4"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 3) //condition for check appropriate file
                 {
                                $name = $slice[1];
                                $location_3_id = $slice[2];
                                $status = 1;
                                $updated_at = $curr_date;
                                $created_at = $curr_date;
                                
                            $check= DB::table('location_4')
                                    ->where('name',$name)
                                    ->where('location_3_id',$location_3_id)
                                    ->where('status',1)
                                    ->count();
                            if($check>0)
                            {
                                Session::flash('message', 'Dupolicate Entry Exist Please correct it and the return nack for upload!');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData');

                            }

                            $check_l3 = DB::table('location_3')
                                        ->where('id',$location_3_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l3<=0)
                            {
                                Session::flash('message', 'Please Enter correct location 3 id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }
                            $query = Location4::insert([

                                'name' => $name,
                                'location_3_id' => $location_3_id,
                                'company_id' => $company_id,
                                'status' => $status,
                                'updated_at' => $updated_at,
                                'created_at' => $created_at,
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select Division file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        if($request->submit=="UploadLocation3"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 4) //condition for check appropriate file
                 {
                                $name = $slice[1];
                                $location_2_id = $slice[2];
                                $status = 1;
                                $updated_at = $curr_date;
                                $created_at = $curr_date;

                            $check_l2 = DB::table('location_2')
                                        ->where('id',$location_2_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l2<=0)
                            {
                                Session::flash('message', 'Please Enter correct location 2 id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $check= DB::table('location_3')
                                    ->where('name',$name)
                                    ->where('location_2_id',$location_2_id)
                                    ->where('status',1)
                                    ->count();
                            if($check>0)
                            {
                                Session::flash('message', 'Dupolicate Entry Exist Please correct it and the return nack for upload!');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData');

                            }
                            $query = Location3::insert([

                                'name' => $name,
                                'location_2_id' => $location_2_id,
                                'company_id' => $company_id,
                                'status' => $status,
                                'updated_at' => $updated_at,
                                'created_at' => $created_at,
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select State file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }

        if($request->submit=="UploadDealerBeat"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 4) //condition for check appropriate file
                 {
                                $dealer_id = $slice[1];
                                $location_id = $slice[2];
                                $user_id = 0;
                                $server_date = $slice[3];                                
                            
                            $check_l7 = DB::table('location_7')
                                        ->where('id',$location_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l7<=0)
                            {
                                Session::flash('message', 'Please Enter correct location 7 id of this company'.$location_id);
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $check_dealer = DB::table('dealer')
                                        ->where('id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_dealer<=0)
                            {
                                Session::flash('message', 'Please Enter correct distributor id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $check_duplicate = DealerLocation::where('dealer_id',$dealer_id)
                                        ->where('location_id',$location_id)
                                        ->where('user_id',0)
                                        ->where('company_id',$company_id)
                                        ->first();
                            if(!empty($check_duplicate))
                            {
                                // Session::flash('message', 'Duplicate Entry Please check it first then upload !!'.'Dealer Id: '.$check_duplicate->dealer_id.' Beat ID : '.$check_duplicate->location_id);
                                // Session::flash('alert-class', 'alert-danger');
                                // return redirect('ImportData ');
                            }else{


                            $query = DealerLocation::insert([

                                'dealer_id' => $dealer_id,
                                'location_id' => $location_id,
                                'user_id' => $user_id,
                                'company_id' => $company_id,
                                'server_date' => $server_date,
                            
                            ]);
                            }

                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select UploadDealerBeat file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        if($request->submit=="UploadUserDealerBeat"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 5) //condition for check appropriate file
                 {
                                $user_id = $slice[1];
                                $dealer_id = $slice[2];
                                $location_id = $slice[3];
                                // $user_id = 0;
                                $server_date = $slice[4];                                
                            
                            $check_l7 = DB::table('location_7')
                                        ->where('id',$location_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l7<=0)
                            {
                                Session::flash('message', 'Please Enter correct location 7 id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $check_dealer = DB::table('dealer')
                                        ->where('id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_dealer<=0)
                            {
                                Session::flash('message', 'Please Enter correct distributor id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $check_duplicate = DealerLocation::where('dealer_id',$dealer_id)
                                        ->where('location_id',$location_id)
                                        ->where('user_id',$user_id)
                                        ->where('company_id',$company_id)
                                        ->first();
                            if(!empty($check_duplicate))
                            {
                                Session::flash('message', 'Duplicate Entry Please check it first then upload !!'.'User Id: '.$check_duplicate->user_id.'Dealer Id: '.$check_duplicate->dealer_id.' Beat ID : '.$check_duplicate->location_id);
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $query = DealerLocation::insert([

                                'dealer_id' => $dealer_id,
                                'location_id' => $location_id,
                                'user_id' => $user_id,
                                'company_id' => $company_id,
                                'server_date' => $curr_date,
                            
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select UploadUserDealerBeat file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        if($request->submit=="UploadDealerPersonalData"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 20) //condition for check appropriate file
                 {
                            $date = $slice[1];
                            $dealer_id = $slice[2];
                            $pan_no = $slice[3];
                            $aadar_no = $slice[4];
                            $food_license = $slice[5];
                            $bank_name = $slice[6];
                            $security_amt = $slice[7];
                            $refrence_no = $slice[8];
                            $security_date = $slice[9];
                            $reciept_issue_date = $slice[10];
                            $security_remarks = $slice[11];
                            $commencement_date = $slice[12];
                            $termination_date = $slice[13];
                            $certificate_issue_date = $slice[14];
                            $agreement_remarks = $slice[15];
                            $refund_amt = $slice[16];
                            $refund_ref_no = $slice[17];
                            $refund_date = $slice[18];
                            $refund_remarks = $slice[19];

                            
                            $check_l7 = DB::table('dealer')
                                        ->where('id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->count();
                            if($check_l7<=0)
                            {
                                Session::flash('message', 'Please Enter correct Distributor id of this company');
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }


                            $check_duplicate = DB::table('dealer_personal_details')->where('dealer_id',$dealer_id)
                                        ->where('company_id',$company_id)
                                        ->first();
                            if(!empty($check_duplicate))
                            {
                                Session::flash('message', 'Duplicate Entry Please check it first then upload !!'.'Distributor Id: '.$check_duplicate->dealer_id);
                                Session::flash('alert-class', 'alert-danger');
                                return redirect('ImportData ');
                            }

                            $query = DB::table('dealer_personal_details')->insert([
                                        'dealer_id'=>$dealer_id,
                                        'company_id'=>$company_id,
                                        'bank_name'=>$bank_name,
                                        'security_amt'=>$security_amt,
                                        'refrence_no'=>$refrence_no,
                                        'security_date'=>$security_date,
                                        'reciept_issue_date'=>$reciept_issue_date,
                                        'security_remarks'=>$security_remarks,
                                        'commencement_date'=>$commencement_date,
                                        'termination_date'=>$termination_date,
                                        'certificate_issue_date'=>$certificate_issue_date,
                                        'agreement_remarks'=>$agreement_remarks,
                                        'refund_amt'=>$refund_amt,
                                        'refund_ref_no'=>$refund_ref_no,
                                        'refund_date'=>$refund_date,
                                        'refund_remarks'=>$refund_remarks,
                                        'food_license'=>$food_license,
                                        'pan_no'=>$pan_no,
                                        'aadar_no'=>$aadar_no,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'created_by'=>Auth::user()->id,

                                    ]); 
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select UploadDealerPersonalData file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }

        if($request->submit=="UploadUser"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
            while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) 
            {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                if(count($slice) == 16) //condition for check appropriate file
                {
                    $first_name = $slice[1];                               
                    $middle_name  = !empty($slice[2])?$slice[2]:'';                              
                    $last_name  = $slice[3];                              
                    $role_id   = $slice[4];                             
                    $person_id_senior = $slice[5];
                    $resigning_date = date('Y-m-d');                               
                    $head_quater_id = $slice[6];
                    $mobile = $slice[7];
                    $email_person = !empty($slice[8])?$slice[8]:'';
                    $state_id = $slice[9];
                    $town_id = $slice[10];
                    $emp_code = !empty($slice[11])?$slice[11]:'';
                    $joining_date = $slice[12];
                    $status = 1;
                    $address = !empty($slice[13])?$slice[13]:'';
                    $gender = 'M';
                    $created_on = date('Y-m-d');
                    $person_username = $slice[14];
                    $password = $slice[15];
                    $person_status = 1;

                    $company_name_data = Company::where('id',$company_id)->first();
                    $company_name = $company_name_data->name;

                    $check = DB::table('person_login')
                            ->where('person_username',trim(str_replace('@','_',$person_username).'@'.$company_name))
                            ->where('company_id',$company_id)
                            ->count();
                    if($check>0)
                    {
                        Session::flash('message', 'User Already Exist!!');
                        Session::flash('class', 'danger');
                        return redirect('ImportData ');
                    }
                    $check2 = DB::table('person_login')
                            ->where('person_username',trim(($person_username).'@'.$company_name))
                            ->where('company_id',$company_id)
                            ->count();
                    if($check2>0)
                    {
                        Session::flash('message', 'User Already Exist!!');
                        Session::flash('class', 'danger');
                        return redirect('ImportData ');
                    }

                    $myArr = [
                        'first_name' => trim(ucfirst($first_name)),
                        'middle_name' => trim(ucfirst($middle_name)),
                        'last_name' => trim(ucfirst($last_name)),
                        'role_id' => trim($role_id),
                        'person_id_senior' => trim($person_id_senior),
                        'version_code_name' => '',
                        'resigning_date' => $resigning_date,
                        'head_quater_id' =>trim($head_quater_id),
                        'mobile' => trim($mobile),
                        'email' => trim($email_person),
                        'state_id' => trim($state_id),
                        'town_id'=> trim($town_id),
                        'emp_code' => trim($emp_code),
                        'company_id' => $company_id,
                        'joining_date' => trim($joining_date),
                        'created_by' => Auth::user()->id,
                        'status' => $status,
                    ];


                    $person=Person::create($myArr);

                    $myArr2=[
                        'person_id'=>$person->id,
                        'address'=>trim($address),
                        'company_id' => $company_id,
                        'gender'=>'M',
                        'created_on'=>$created_on
                    ];
                    $person2=PersonDetail::create($myArr2);

                    $myArr3=[
                        'person_id'=>$person->id,
                        'emp_id'=>trim($emp_code),
                        'company_id' => $company_id,
                        'person_username'=>trim(str_replace('@','_',$person_username).'@'.$company_name),
                        'person_password'=>DB::raw("AES_ENCRYPT('".trim($password)."', '".Lang::get('common.db_salt')."')"),
                        'person_status'=>$status
                    ];
                    $person3=PersonLogin::create($myArr3);

                    $myArr4=[
                        'id'=>$person->id,
                        'role_id'=>trim($role_id),
                        'email'=>trim(str_replace('@','_',$person_username).'@'.$company_name),
                        'password'=>bcrypt(trim($password)),
                        'company_id' => $company_id,
                        'original_pass'=>$password,
                        'status'=>$status,
                        'created_at'=>$created_on,

                    ];
                    $person4=User::create($myArr4);


                
                    $inum++;   
                }
                else 
                {
                    Session::flash('message', 'Please select UploadDealerBeat file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
            }
 
            if ($person && $person3 && $person2 && $person4) 
            {
                DB::commit();
                Session::flash('message', 'Uploaded Succesfully');
                Session::flash('alert-class', 'alert-success');
            }
            else 
            {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
            }
         }
         return redirect('ImportData');

        }

        ###########for target#########

        if($request->submit=="SuperStockiestSkuTarget"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 8) //condition for check appropriate file
                 {
                   $created_by = Auth::user()->id;
                    $state_id = $slice[1];
                    $ss_name = $slice[2];
                    $ss_id = $slice[3];
                    $product_name = $slice[4];
                    $product_id = $slice[5];
                    $quantity = $slice[6];
                    $month = $slice[7];
                    $created_at= date('Y-m-d H:i:s');  
                    $from_date = $month."-01";
                    $to_date = date("Y-m-t", strtotime($month));
                     

                    $check = DB::table('master_target')->where('state_id',$state_id)->where('csa_id',$ss_id)->where('product_id',$product_id)->first();
                    if(!empty($check))
                    {
                        Session::flash('message', 'Duplicate Error Please correct it then return back for upload :- state_id : '.$state_id.' SS Id  : '.$ss_id.' Product Id :'.$product_id);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }
                            $query = DB::table('master_target')->insert([

                                'company_id'=> $company_id,
                                'state_id' => $state_id,    
                                'csa_id' => $ss_id,    
                                'distributor_id' => 0,    
                                'user_id' => 0,    
                                'beat_id' => 0,    
                                'retailer_id' => 0,    
                                'product_id' => $product_id,     
                                'flag' => 2,    
                                'quantity_pcs' => 0,    
                                'quantity_cases' => $quantity,    
                                'quantity_other_unit' => 0,    
                                'sale_value' => 0,    
                                'from_date' => $from_date,    
                                'to_date' => $to_date,    
                                'month' => $month,    
                                'remarks' => "SS_Target",    
                                'status' => 1,    
                                'created_at' => $created_at,    
                                'created_by' => $created_by,    

                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select SuperStockiestSkuTarget file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }



         if($request->submit=="DistributorSkuTarget"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 10) //condition for check appropriate file
                 {
                    $created_by = Auth::user()->id;
                    $state_id = $slice[1];
                    $ss_name = $slice[2];
                    $ss_id = 0;
                    $db_name = $slice[4];
                    $db_id = $slice[5];
                    $product_name = $slice[6];
                    $product_id = $slice[7];
                    $quantity = $slice[8];
                    $month = $slice[9];
                    $created_at= date('Y-m-d H:i:s');  
                    $from_date = $month."-01";
                    $to_date = date("Y-m-t", strtotime($month));
                     

                    $check = DB::table('master_target')->where('state_id',$state_id)->where('csa_id',$ss_id)->where('distributor_id',$db_id)->where('product_id',$product_id)->first();
                    if(!empty($check))
                    {
                        Session::flash('message', 'Duplicate Error Please correct it then return back for upload :- state_id : '.$state_id.' DB Id  : '.$db_id.' Product Id :'.$product_id);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }
                            $query = DB::table('master_target')->insert([

                                'company_id'=> $company_id,
                                'state_id' => $state_id,    
                                'csa_id' => $ss_id,    
                                'distributor_id' => $db_id,    
                                'user_id' => 0,    
                                'beat_id' => 0,    
                                'retailer_id' => 0,    
                                'product_id' => $product_id,     
                                'flag' => 2,    
                                'quantity_pcs' => 0,    
                                'quantity_cases' => $quantity,    
                                'quantity_other_unit' => 0,    
                                'sale_value' => 0,    
                                'from_date' => $from_date,    
                                'to_date' => $to_date,    
                                'month' => $month,    
                                'remarks' => "DB_Target",    
                                'status' => 1,    
                                'created_at' => $created_at,    
                                'created_by' => $created_by,    

                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select DistributorSkuTarget file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }



         if($request->submit=="UserSkuTarget"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 11) //condition for check appropriate file
                 {
                    $created_by = Auth::user()->id;
                    $state_id = $slice[1];
                    $state_name = $slice[2];
                    $beat_name = $slice[3];
                    $beat_id = $slice[4];
                    $user_name = $slice[5];
                    $user_id = $slice[6];
                    $product_name = $slice[7];
                    $product_id = $slice[8];
                    $quantity = $slice[9];
                    $month = $slice[10];
                    $created_at= date('Y-m-d H:i:s');  
                    $from_date = $month."-01";
                    $to_date = date("Y-m-t", strtotime($month));
                     

                    $check = DB::table('master_target')->where('state_id',$state_id)->where('beat_id',$beat_id)->where('user_id',$user_id)->where('product_id',$product_id)->first();
                    if(!empty($check))
                    {
                        Session::flash('message', 'Duplicate Error Please correct it then return back for upload :- state_id : '.$state_id.' User Id  : '.$user_id.' Product Id :'.$product_id);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }
                            $query = DB::table('master_target')->insert([

                                'company_id'=> $company_id,
                                'state_id' => $state_id,    
                                'csa_id' => 0,    
                                'distributor_id' => 0,    
                                'user_id' => $user_id,    
                                'beat_id' => $beat_id,    
                                'retailer_id' => 0,    
                                'product_id' => $product_id,     
                                'flag' => 2,    
                                'quantity_pcs' => 0,    
                                'quantity_cases' => $quantity,    
                                'quantity_other_unit' => 0,    
                                'sale_value' => 0,    
                                'from_date' => $from_date,    
                                'to_date' => $to_date,    
                                'remarks' => "User_Target",    
                                'status' => 1,    
                                'created_at' => $created_at,    
                                'created_by' => $created_by,    

                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select UserSkuTarget file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }

        #########for target end #########


        ######## for scheme plan #######
         if($request->submit=="UploadSchemePlanNeha"){

            $csv_file = $request->excelFile;

            // test all products in excel
            $state = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->groupBy('id')
                    ->pluck('name','id')->toArray();

            $catalog_product = DB::table('catalog_product')
                            ->where('company_id',$company_id)
                            ->where('status',1)
                            ->groupBy('id')
                            ->pluck('name','id')->toArray();


            $database_data_count = 0;
            foreach ($state as $skey => $svalue) {
                foreach ($catalog_product as $ckey => $cvalue) {
                    $database_data_count++;
                }
            }


            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                 $data = fgetcsv($getfile, 1000, ",");
                 $excel_data_count=0;
                 DB::beginTransaction();
                 while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                    $excel_data_count++;
                 }
             }

             // dd($excel_data_count);
             

            if($database_data_count != $excel_data_count)
            {
                Session::flash('message', 'Dont Remove Data From Uploaded Format');
                Session::flash('alert-class', 'alert-danger');
                return redirect('ImportData ');
            }


            // test all products in excel

            $check = DB::table('product_wise_scheme_plan_details')
            ->select(DB::raw("DATE_FORMAT(valid_to_date, '%Y-%m-%d') as valid_to_date"))
            ->where('company_id',$company_id)
            ->orderBy('id','DESC')
            ->first();

            $created_at = date('Y-m-d H:i:s');

            $dateformat = date('Ymd');
            $company_name = DB::table('company')->where('id',$company_id)->first();
            $scheme_name = $company_name->title.'('.$dateformat.')';



            $mainArr = [

                'scheme_name' => $scheme_name,
                'scheme_category_status' => 2,
                'vs_status' => 2,
                'item_status' => 2,
                'status' => 1,
                'created_at' => $created_at,
                'updated_at' => $created_at,
                'company_id' => $company_id,
                ];



            $new_scheme_insert = ProductWiseSchemePlan::create($mainArr);






            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             // DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 9) //condition for check appropriate file
                 {
                    $created_by = Auth::user()->id;
                    $srno = $slice[0];
                    $state_id = $slice[1];
                    // $product_id = $slice[3];
                    $product_id = $slice[4];
                    $value_amount_in_percentage = $slice[6];
                    $valid_from = $slice[7];
                    $valid_to = $slice[8];
                    $sale_unit = 3;
                    $sale_value_range_first = 1;
                    $sale_value_range_last = 1;
                    $incentive_type = 1;

             



                     



                    $table_valid_to_date = strtotime($check->valid_to_date);

                    $excel_from_date = strtotime($valid_from);



                    // if($excel_from_date < $table_valid_to_date)
                    // {
                    //     dd('1');
                    // }else{
                    //     dd('2');
                    // }

                    // dd($excel_from_date);

                    if($table_valid_to_date > $excel_from_date)
                    {
                        Session::flash('message', 'Upload Correct From Date :- state_id : '.$state_id.' Product Id :'.$product_id);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }


                    // dd($new_scheme_insert->id);



                            $query = DB::table('product_wise_scheme_plan_details')->insert([

                                'scheme_id'=> $new_scheme_insert->id,
                                'company_id'=> $company_id,
                                'state_id' => $state_id,    
                                'product_id' => $product_id,    
                                'sale_unit' => $sale_unit,    
                                'sale_value_range_first' => $sale_value_range_first,    
                                'sale_value_range_last' => $sale_value_range_last,    
                                'incentive_type' => $incentive_type,    
                                'value_amount_percentage' => $value_amount_in_percentage,    
                                'created_at' => $created_at,    
                                'updated_at' => $created_at,    
                                'valid_from_date' => $valid_from,    
                                'valid_to_date' => $valid_to,    
                            ]);
                
                            $inum++;   
                 }
                 else {
                    Session::flash('message', 'Please select SchemePlan file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        ######## scheme plan ends ######

        ########## tour plan  #########

        if($request->submit=="UserTourPlan"){

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();
             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 13) //condition for check appropriate file
                 {
                    $created_by = Auth::user()->id;
                    $created_from = 2;
                    $user_id = $slice[1];
                    $work_date = $slice[2];
                    $day = date('l', strtotime($work_date));
                    $work_status = $slice[3];
                    $db_id = $slice[4];
                    $town_id = $slice[5];
                    $beat_id = $slice[6];
                    $pc = $slice[7];
                    $rd = $slice[8];
                    $collection = $slice[9];
                    $primary_ord= $slice[10];  
                    $new_outlet = $slice[11];
                    $remark = $slice[12];
                     

                    $check = DB::table('monthly_tour_program')->where('person_id',$user_id)->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d')='$work_date'")->where('company_id',$company_id)->first();
                    if(!empty($check))
                    {
                        Session::flash('message', 'Duplicate Error Please correct it then return back for upload :- person_id : '.$user_id.' Date  : '.$work_date);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }

                    $check_work_status = DB::table('_task_of_the_day')->where('company_id',$company_id)->where('id',$work_status)->first();


                    if($check_work_status->task == "RETAILING"){

                        $query = DB::table('monthly_tour_program')->insert([

                            'company_id'=> $company_id,
                            'person_id'=>$user_id,
                            'working_date'=>$work_date,
                            'dayname'=>$day,
                            'working_status_id'=>$work_status,
                            'dealer_id'=>$db_id,
                            'town'=>$town_id,
                            'locations'=>$beat_id,
                            'total_calls'=>0,
                            'total_sales'=>0.00,
                            'ss_id'=>0,
                            'travel_mode' => 0,
                            'mobile_save_date_time' => $work_date,
                            'upload_date_time' => date('Y-m-d H:i:s'),
                            'pc' => $pc,
                            'rd' => $rd,
                            'collection' => $collection,
                            'primary_ord' => $primary_ord,
                            'new_outlet' => $new_outlet,
                            'any_other_task' => $remark,
                            'submit_from' => 2,
                            'submit_by' => $created_by,

                        ]);

                    }
                    else{

                        $query = DB::table('monthly_tour_program')->insert([

                            'company_id'=> $company_id,
                            'person_id'=>$user_id,
                            'working_date'=>$work_date,
                            'dayname'=>$day,
                            'working_status_id'=>$work_status,
                            'dealer_id'=>0,
                            'town'=>$town_id,
                            'locations'=>" ",
                            'total_calls'=>0,
                            'total_sales'=>0.00,
                            'ss_id'=>0,
                            'travel_mode' => 0,
                            'mobile_save_date_time' => $work_date,
                            'upload_date_time' => date('Y-m-d H:i:s'),
                            'pc' => 0,
                            'rd' => 0,
                            'collection' => 0,
                            'primary_ord' => 0,
                            'new_outlet' => 0,
                            'any_other_task' => $remark,
                            'submit_from' => 2,
                            'submit_by' => $created_by,

                        ]);

                    }
                
                            $inum++;   

                 }
                 else {
                    Session::flash('message', 'Please select UserTourPlan file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        if($request->submit=="UploadVideo"){

            if ($request->hasFile('excelFile')) {
                if($request->file('excelFile')->isValid()) {
                    try {
                        $file = $request->file('excelFile');
                        $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                        # save to DB
                        $title = $request->title;
                        $query = DB::table('vedios_data')->insert([
                            'vedio_name'=>'/advertisement/'.$name,
                            'company_id'=>$company_id,
                            'title'=>$title,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'status'=> 1,
                        ]);

                        $request->file('excelFile')->move("advertisement", $name);
                    } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                    }
                }
            }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
                return redirect('VideoImportData');

             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }

         
        //  if($request->submit=="userAttendance"){

        //     $csv_file = $request->excelFile;
        //     if (($getfile = fopen($csv_file, "r")) !== FALSE) {
        //      $data = fgetcsv($getfile, 1000, ",");
        //      $inum=2;
        //      $query = " ";
        //      $ch_data=array();
        //      DB::beginTransaction();
        //      while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
        //          $curr_date = date('Y-m-d H:m:s');
        //          // dd($data);
        //          $result = $data;
        //          $result1  = str_replace(",","",$result);  
        //          $str = implode(",", $result1);
        //          $slice = explode(",", $str);
        //          // dd($slice);
        //          // print_r($slice);die;
        //          if(count($slice) == 39) //condition for check appropriate file
        //         {
        //             // dd($slice);
        //             $id = $slice[0];
        //             $company_id = $slice[1];
        //             $user_id = $slice[2];
        //             $order_id = $slice[3];
        //             $work_date = $slice[4];
        //             $working_with = $slice[5];
        //             $server_date = $slice[6];
        //             $work_status = $slice[7];
        //             $user_location = !empty($slice[8])?$slice[8]:'';
        //             $mnc_mcc_lat_cellid = !empty($slice[10])?$slice[10]:'';
        //             $lat_lng = !empty($slice[11])?$slice[11]:'';
        //             $track_addrs = !empty($slice[12])?$slice[12]:'';
        //             $remarks = !empty($slice[13])?$slice[13]:'';
        //             $image_name = !empty($slice[14])?$slice[14]:'';
        //             $checkout_date = !empty($slice[15])?$slice[15]:'';
        //             $checkout_server_date_time = !empty($slice[16])?$slice[16]:'';
        //             $checkout_status = !empty($slice[17])?$slice[17]:'';
        //             $checkout_location = !empty($slice[18])?$slice[18]:'';
        //             $checkout_mnc_mcc_lat_cellid = !empty($slice[19])?$slice[19]:'';
        //             $checkout_lat_lng = !empty($slice[20])?$slice[20]:'';
        //             $checkout_remarks = !empty($slice[21])?$slice[21]:'';
        //             $checkout_address = !empty($slice[22])?$slice[22]:'';
        //             $checkout_image = !empty($slice[23])?$slice[23]:'';
        //             $updated_at = !empty($slice[24])?$slice[24]:'';
        //             $att_status = !empty($slice[25])?$slice[25]:'';
        //             $in_out_status = !empty($slice[26])?$slice[26]:'';
        //             $reason_id = !empty($slice[27])?$slice[27]:'';
        //             $mtp_town_id = !empty($slice[28])?$slice[28]:'';
        //             $battery_status = !empty($slice[29])?$slice[29]:'';
        //             $gps_status = !empty($slice[30])?$slice[30]:'';
        //             $area_of_work_id = !empty($slice[31])?$slice[31]:'';
        //             $working_with_type_id = !empty($slice[32])?$slice[32]:'';
        //             $working_activity_id = !empty($slice[33])?$slice[33]:'';
        //             $distributor_id = !empty($slice[34])?$slice[34]:'';
        //             $new_area_name = !empty($slice[35])?$slice[35]:'';
        //             $leave_from_date = !empty($slice[36])?$slice[36]:'';
        //             $leave_to_date = !empty($slice[37])?$slice[37]:'';
        //             $leave_id = !empty($slice[38])?$slice[38]:'';
                   
        //             $check = DB::table('user_daily_attendance')
        //                     ->where('company_id',$company_id)
        //                     ->where('order_id',$order_id)
        //                     ->count();
        //                     // dd($check);
        //             if($check>0)
        //             {
        //                 // dd('q');
        //             }
        //             else
        //             {
        //                 $query = DB::table('user_daily_attendance')->insert([

        //                         'id'=> $id,
        //                         'company_id'=> $company_id,
        //                         'user_id'=> $user_id,
        //                         'order_id'=> $order_id,
        //                         'work_date'=> $work_date,
        //                         'working_with'=> $working_with,
        //                         'server_date'=> $server_date,
        //                         'work_status'=> $work_status,

        //                         'user_location'=> $user_location,
        //                         'mnc_mcc_lat_cellid'=> $mnc_mcc_lat_cellid,
        //                         'lat_lng'=> $lat_lng,
        //                         'track_addrs'=> $track_addrs,
        //                         'remarks'=> $remarks,
        //                         'image_name'=> $image_name,
        //                         'checkout_date'=> $checkout_date,
        //                         'checkout_server_date_time'=> $checkout_server_date_time,
        //                         'checkout_status'=> $checkout_status,
        //                         'checkout_location'=> $checkout_location,
        //                         'checkout_mnc_mcc_lat_cellid'=> $checkout_mnc_mcc_lat_cellid,
        //                         'checkout_lat_lng'=> $checkout_lat_lng,
        //                         'checkout_remarks'=> $checkout_remarks,
        //                         'checkout_address'=> $checkout_address,
        //                         'checkout_image'=> $checkout_image,
        //                         'updated_at'=> $updated_at,
        //                         'att_status'=> $att_status,
        //                         'in_out_status'=> $in_out_status,
        //                         'reason_id'=> $reason_id,
        //                         'mtp_town_id'=> $mtp_town_id,
        //                         'battery_status'=> $battery_status,
        //                         'gps_status'=> $gps_status,
        //                         'area_of_work_id'=> $area_of_work_id,
        //                         'working_with_type_id'=> $working_with_type_id,
        //                         'working_activity_id'=> $working_activity_id,
        //                         'distributor_id'=> $distributor_id,
        //                         'new_area_name'=> $new_area_name,
        //                         'leave_from_date'=> $leave_from_date,
        //                         'leave_to_date'=> $leave_to_date,
        //                         'leave_id'=> $leave_id,

        //                 ]);

        //             }
                    



                        

                   
        //          }
        //      }
 
        //      if ($query) {
        //          DB::commit();
        //          Session::flash('message', 'Uploaded Succesfully');
        //          Session::flash('alert-class', 'alert-success');
        //      }
        //      else {
        //          DB::rollback();
        //          Session::flash('message', 'Something went wrong!');
        //          Session::flash('alert-class', 'alert-danger');
        //      }
        //  }
        //  return redirect('ImportData');

        // }

        ########### tour plan ends #######

        if($request->submit=="DistributorTarget")
        {

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) 
            {
                // dd($getfile);
                $data = fgetcsv($getfile, 3000, ",");
                $data = fgetcsv($getfile, 3000, ",");
                // dd($data);
                if(empty($data[1]))
                {
                    Session::flash('message', 'Something went wrong!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData');
                }
                $month = $data[1];
                $from_date = $month.'-01';
                $to_date = date('Y-m-t', strtotime($month));
                // dd($from_date);
                $data = fgetcsv($getfile, 3000, ",");
                $data_c = fgetcsv($getfile, 3000, ",");
                // dd($data_c);
                $inum=3;
                $query = " ";
                $ch_data=array();
                // DB::beginTransaction();
                $category_data = DB::table('catalog_1')
                        ->where('status',1)
                        ->where('company_id',$company_id)
                        ->pluck('name','id');
                while (($data = fgetcsv($getfile, 3000, ",")) !== FALSE) 
                {
                    $tempArry = [];
                    $curr_date = date('Y-m-d H:m:s');
                    $result = $data;
                    $numOfCols = 9;
                    $rowCount = 0;

                    foreach ($data_c as $key => $value) // first layer heading  
                    {
                        // dd($data_c);
                        if($key>8)
                        {   
                            $str_value = explode('^',$value);
                            // dd($str_value[1]);
                            $check = DB::table('master_target')
                                    ->where('product_id',!empty($str_value[1])?$str_value[1]:'')
                                    ->where('distributor_id',$result[3])
                                    ->where('month',$month)
                                    ->where('from_date',$from_date)
                                    ->where('to_date',$to_date)
                                    ->delete();
                            // dd($value-);
                            $query = DB::table('master_target')->insert([

                                    'company_id'=> $company_id,
                                    'state_id' => 0,    
                                    'csa_id' => 0,    
                                    'distributor_id' => $result[3],    
                                    'user_id' => 0,    
                                    'beat_id' => 0,    
                                    'retailer_id' => 0,    
                                    'product_id' => !empty($str_value[1])?$str_value[1]:'',     
                                    'flag' => 2,    
                                    'quantity_pcs' => 0,    
                                    'quantity_cases' => $result[$key],    
                                    'quantity_other_unit' => 0,    
                                    'sale_value' => 0,    
                                    'from_date' => $from_date,    
                                    'to_date' => $to_date,    
                                    'month' => $month,    
                                    'remarks' => "Distributor Target",    
                                    'status' => 1,    
                                    'created_at' => date('Y-m-d H:i:s'),    
                                    'created_by' => date('Y-m-d H:i:s'),    

                                ]);
                        }
                    }
                }
     
                if ($query) 
                {
                    DB::commit();
                    Session::flash('message', 'Uploaded Succesfully');
                    Session::flash('alert-class', 'alert-success');
                }
                else 
                {
                    DB::rollback();
                    Session::flash('message', 'Something went wrong!');
                    Session::flash('alert-class', 'alert-danger');
                }
            }
            return redirect('ImportData');

        }
        // dd($request);
        if($request->submit=="RamanujanExcelSheet")
        {

            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) 
            {
                // dd($getfile);
                // $data = fgetcsv($getfile, 3000, ",");
                // $data = fgetcsv($getfile, 3000, ",");
                // dd($data);
                // if(empty($data[1]))
                // {
                //     Session::flash('message', 'Something went wrong!');
                //     Session::flash('alert-class', 'alert-danger');
                //     return redirect('ImportData');
                // }
                // $month = $data[1];
                // $from_date = $month.'-01';
                // $to_date = date('Y-m-t', strtotime($month));
                // dd($from_date);
                // $data = fgetcsv($getfile, 3000, ",");
                $data_c = fgetcsv($getfile, 3000, ",");
                // dd($data_c);
                $inum=3;
                $query = " ";
                $ch_data=array();
                DB::beginTransaction();
                
                while (($data = fgetcsv($getfile, 3000, ",")) !== FALSE) 
                {
                       $curr_date = date('Y-m-d H:m:s');
                     // dd($curr_date);
                     $result = $data;
                     $result1  = str_replace(",","",$result);  
                     $str = implode(",", $result1);
                     $slice = explode(",", $str);
                     // print_r($slice);die;
                     if(count($slice) == 4) //condition for check appropriate file
                     {
                        $created_by = Auth::user()->id;
                        $user_name = $slice[0];
                        $user_action = $slice[1];
                        $date = $slice[2];
                        $time = $slice[3];
                        // dd(date('Y-m-d',strtotime(str_replace(' ', '',trim(preg_replace('/[^A-Za-z0-9\/-]/', ' ', $date))))));
                        $check = DB::table('ramanujan_attaendance_sheet')
                                ->where('date',date('Y-m-d',strtotime(str_replace(' ', '',trim(preg_replace('/[^A-Za-z0-9\/-]/', ' ', $date))))))
                                ->where('user_name',trim(preg_replace('/[^A-Za-z0-9\/-]/', '', $user_name)))
                                ->get();
                        if(count($check)>0)
                        {
                            $submit_in = DB::table('ramanujan_attaendance_sheet')
                                ->where('date',date('Y-m-d',strtotime(str_replace(' ', '',trim(preg_replace('/[^A-Za-z0-9\/-]/', ' ', $date))))))
                                ->where('user_name',trim(preg_replace('/[^A-Za-z0-9\/-]/', '', $user_name)))
                                ->update([
                                    // 'user_name'=>$user_name,
                                    'user_action'=>$user_action,
                                    // 'date'=>$date,
                                    // 'time'=>$time,
                                    'status'=>'179',
                                    'updated_by'=>$created_by,
                                    'updated_at'=>$curr_date,
                            ]);
                        }
                        else
                        {
                            // dd();
                            $submit_in = DB::table('ramanujan_attaendance_sheet')->insert([
                                    'user_name'=>trim(preg_replace('/[^A-Za-z0-9\/-]/', '', $user_name)),
                                    'user_action'=>trim(preg_replace('/[^A-Za-z0-9\/-]/', '', $user_action)),
                                    'date'=>date('Y-m-d',strtotime(str_replace(' ', '',trim(preg_replace('/[^A-Za-z0-9\/-]/', ' ', $date))))),
                                    'time'=>$time,
                                    'status'=>'179',
                                    'created_at'=>$curr_date,
                                    'created_by'=>$created_by,
                            ]);
                        }
                        
                        


                    }
                }
     
                if ($query) 
                {
                    DB::commit();
                    Session::flash('message', 'Uploaded Succesfully');
                    Session::flash('alert-class', 'alert-success');
                }
                else 
                {
                    DB::rollback();
                    Session::flash('message', 'Something went wrong!');
                    Session::flash('alert-class', 'alert-danger');
                }
            }
            return redirect('ImportData');

        }

        // upload primary order starts

        if($request->submit=="UploadPrimarySale"){

            // dd('1');
            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();

            $quantityPerOtherType = DB::table('catalog_product')
                                ->where('company_id',$company_id)
                                ->pluck('quantiy_per_other_type','id');

             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                //  dd($curr_date);
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 7) //condition for check appropriate file
                 {
                    $created_by = Auth::user()->id;
                    $distributor_id = $slice[1];
                    $dispatch_through = $slice[2];
                    $destination = $slice[3];
                    $remarks = $slice[4];
                    $product_id = $slice[5];
                    $quantity_pcs = $slice[6];

                    $order_id = date('YmdHis');

                    $finalOrderId = $order_id.$distributor_id;

                    // rate details
                    $dealer_data = Dealer::where('id',$distributor_id)->where('dealer_status',1)->where('company_id',$company_id)->first();
                    $product_details = DB::table('catalog_product')
                                    ->join('product_type','product_type.id','=','catalog_product.product_type')
                                    ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                                    ->select('other_dealer_rate as rate','weight','product_type.name as type_name')
                                    ->where('catalog_product.id',$product_id)
                                    ->where('state_id',$dealer_data->state_id)
                                    ->where('catalog_product.company_id',$company_id)
                                    ->first();
                    if(empty($product_details))
                    {
                        $product_details = DB::table('catalog_product')
                                    ->join('product_type','product_type.id','=','catalog_product.product_type')
                                    ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                                    ->select('other_dealer_rate as rate','weight','product_type.name as type_name')
                                    ->where('distributor_id',$distributor_id)
                                    ->where('catalog_product.id',$product_id)
                                    ->where('catalog_product.company_id',$company_id)
                                    ->first();
                    }
                    //rate details ends

                     ///////////// final  qty for janak
                    $quantity_per_other_type = !empty($quantityPerOtherType[$product_id])?$quantityPerOtherType[$product_id]:'0';
                    $final_piece_qty = $quantity_pcs;

                    if($quantity_per_other_type == 0){
                    $calculated_secondary_qty = 0;
                    }
                    else{
                    $calculated_secondary_qty = ($final_piece_qty/$quantity_per_other_type);
                    }
                    $final_secondary_quantity = ROUND(($calculated_secondary_qty),3);
                    //////////// final rate for janak
                    $pcs_secondary_sale  = ($product_details->rate*$quantity_pcs);

                    if($final_secondary_quantity == 0){
                    $final_secondary_rate = 0;
                    }
                    else{
                    $final_secondary_rate = ROUND(($pcs_secondary_sale)/($final_secondary_quantity),3);
                    }
                    //////////

                    // janaak primary order seq start
                    if($company_id == 50){
                    $chk_uso = DB::table('user_primary_sales_order')->select('janak_order_sequence')->where('company_id',$company_id)->orderBy('janak_order_sequence','DESC')->first();

                        if(empty($chk_uso->janak_order_sequence)){
                            $sequence = '1';
                        }else{
                            $sequence = $chk_uso->janak_order_sequence+1;
                        }

                    }else{
                            $sequence = '';
                    }
                    // janaak primary order seq ends


                     $check = DB::table('user_primary_sales_order')
                                ->where('dealer_id',$distributor_id)
                                ->where('order_id',$finalOrderId)
                                ->count();

                   
                    if($check>0)
                    {
                            $second_layer2 = DB::table('user_primary_sales_order_details')->insert([
                                'company_id'=> $company_id,
                                'id'=> $finalOrderId,
                                'order_id'=> $finalOrderId,
                                'product_id'=> $product_id,
                                'primary_unit'=> $product_details->type_name,
                                'rate'=> $product_details->rate,
                                'quantity'=> $quantity_pcs,
                                'scheme_qty'=> '0',
                                'secondary_qty'=> '0',
                                'secondary_rate'=> '0',
                                'pr_rate'=> '0',
                                'cases'=> '0',
                                'final_secondary_qty'=> "$final_secondary_quantity",
                                'final_secondary_rate'=> $final_secondary_rate,
                            ]);
                       
                    }
                    else
                    {
                        $first_layer = DB::table('user_primary_sales_order')->insert([
                                'id'=> $finalOrderId,
                                'order_id'=> $finalOrderId,
                                'dealer_id'=> $distributor_id,
                                'created_date'=> date('Y-m-d H:i:s'),
                                'created_person_id'=> $created_by,
                                'sale_date'=> date('Y-m-d'),
                                'receive_date'=> date('Y-m-d H:i:s'),
                                'dispatch_date'=> date('Y-m-d H:i:s'),
                                'date_time'=> date('Y-m-d H:i:s'),
                                'company_id'=> $company_id,
                                'dms_order_reason_id'=> '0',
                                'cancel_order_reason_id'=> '0',
                                'amount_before_discount'=> '0',
                                'discount_type'=> '0',
                                'discount_value'=> '0',
                                'amount_after_discount'=> '0',
                                'remarks'=> $remarks,
                                'dispatch_through'=> $dispatch_through,
                                'destination'=> $destination,
                                // 'comment'=> $finalOrderId,
                                // 'ch_date'=> $finalOrderId,
                                // 'challan_no'=> $finalOrderId,
                                // 'csa_id'=> $finalOrderId,
                                // 'action'=> $finalOrderId,
                                // 'is_claim'=> $finalOrderId,
                                // 'sync_status'=> $finalOrderId,
                                // 'server_date_time'=> $finalOrderId,
                                // 'lat'=> $finalOrderId,
                                // 'lng'=> $finalOrderId,
                                // 'address'=> $finalOrderId,
                                // 'mcc_mnc_lac_cellid'=> $finalOrderId,
                                // 'battery_status'=> $finalOrderId,
                                // 'gps_status'=> $finalOrderId,
                                'order_from'=> '2',
                                'janak_order_sequence'=> $sequence,
                                
                        ]);

                        $second_layer = DB::table('user_primary_sales_order_details')->insert([
                                'company_id'=> $company_id,
                                'id'=> $finalOrderId,
                                'order_id'=> $finalOrderId,
                                'product_id'=> $product_id,
                                'primary_unit'=> $product_details->type_name,
                                'rate'=> $product_details->rate,
                                'quantity'=> $quantity_pcs,
                                'scheme_qty'=> '0',
                                'secondary_qty'=> '0',
                                'secondary_rate'=> '0',
                                'pr_rate'=> '0',
                                'cases'=> '0',
                                'final_secondary_qty'=> "$final_secondary_quantity",
                                'final_secondary_rate'=> $final_secondary_rate,
                            

                        ]);
                    }

                
                            $inum++;   

                 }
                 else {
                    Session::flash('message', 'Please select UserPrimaryOrder file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($query) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }

        // upload primary order ends




        ////////////////////////////////////////////////////////// upload CFA Stock ///////////////////////////////////////////////////////////////////////////////////////
        if($request->submit=="UploadCsaStock"){

            // dd('1');
            $csv_file = $request->excelFile;
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
             $data = fgetcsv($getfile, 1000, ",");
             $inum=2;
             $query = " ";
             $ch_data=array();
             DB::beginTransaction();


             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                 $curr_date = date('Y-m-d H:m:s');
                 $result = $data;
                 $result1  = str_replace(",","",$result);  
                 $str = implode(",", $result1);
                 $slice = explode(",", $str);
                 // print_r($slice);die;
                 if(count($slice) == 21) //condition for check appropriate file
                 {
                    $created_by = Auth::user()->id;
                    $sno = $slice[0];
                    $csa_code = $slice[1];
                    $csa_id = $slice[2];
                    $csa_name = $slice[3];
                    $erp_plant_code = $slice[4];
                    $div_code = $slice[5];
                    $div_name = $slice[6];
                    $item_name = $slice[7];
                    $packing = $slice[8];
                    $item_code = $slice[9];
                    $erp_product_code = $slice[10];
                    $item_type = $slice[11];
                    $item_group = $slice[12];
                    $item_category = $slice[13];
                    $hsn_code = $slice[14];
                    $gst = $slice[15];
                    $shipper_qty_item = $slice[16];
                    $item_lock = $slice[17];
                    $opening_stock = $slice[18];
                    $closing_stock = $slice[19];
                    $closing_stock_value = $slice[20];

                    $order_id = date('YmdHi');

                    $finalOrderId = $order_id.$csa_id;

                    // rate details
                    $dealer_data = DB::table('csa')->where('c_id',$csa_id)->where('active_status',1)->where('company_id',$company_id)->first();

                    if(empty($dealer_data)){
                        DB::rollback();
                        $message = "Select Correct CSA ID ". $csa_id;
                        Session::flash('message', $message);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }


                    $product_details = DB::table('catalog_product')
                                    ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                                    ->select('ss_pcs_rate as rate','mrp_pcs as mrp','mrp as mrp_case','catalog_product.id as product_id')
                                    ->where('catalog_product.itemcode',$item_code)
                                    ->where('state_id',$dealer_data->state_id)
                                    ->where('catalog_product.company_id',$company_id)
                                    ->first();

                     if(empty($product_details)){
                        DB::rollback();
                        $message = "Select Correct Item Code ". $item_code;
                        Session::flash('message', $message);
                        Session::flash('alert-class', 'alert-danger');
                        return redirect('ImportData ');
                    }

                    
                    //rate details ends
                  


                     $check = DB::table('ss_balance_stock')
                                ->where('csa_id',$csa_id)
                                ->where('order_id',$finalOrderId)
                                ->first();

                    if(!empty($check))
                    {

                            $second_layer = DB::table('ss_balance_stock')->insert([
                                'company_id'=> $company_id,
                                'order_id'=> $check->order_id,
                                'csa_id'=> $csa_id,
                                'user_id'=> $created_by,
                                'product_id'=> $product_details->product_id,
                                'mrp'=> $product_details->mrp_case,
                                'pcs_mrp'=> $product_details->mrp,
                                'stock_qty'=> $closing_stock,
                                'stock_case'=> '0',
                                'mfg_date'=> date('Y-m-d'),
                                'exp_date'=> date('Y-m-d'),
                                'cases'=> '0',
                                'submit_date_time'=> date('Y-m-d H:i:s'),
                                'server_date_time'=> date('Y-m-d H:i:s'),
                                'sstatus'=> 0,
                                'lat'=> '',
                                'lng'=> '',
                                'address'=> '',
                                'erp_plant_code'=> $erp_plant_code,
                                'div_code'=> $div_code,
                                'div_name'=> $div_name,
                                'item_name'=> $item_name,
                                'packing'=> $packing,
                                'erp_product_code'=> $erp_product_code,
                                'item_type'=> $item_type,
                                'item_group'=> $item_group,
                                'item_category'=> $item_category,
                                'hsn_code'=> $hsn_code,
                                'gst'=> $gst,
                                'shipper_qty_item'=> $shipper_qty_item,
                                'item_lock'=> $item_lock,
                                'opening_stock'=> $opening_stock,

                            ]);
                       
                    }
                    else
                    {
                        $second_layer = DB::table('ss_balance_stock')->insert([
                                'company_id'=> $company_id,
                                'order_id'=> $finalOrderId,
                                'csa_id'=> $csa_id,
                                'user_id'=> '2196',
                                'product_id'=> $product_details->product_id,
                                'mrp'=> $product_details->mrp_case,
                                'pcs_mrp'=> $product_details->mrp,
                                'stock_qty'=> $closing_stock,
                                'stock_case'=> '0',
                                'mfg_date'=> date('Y-m-d'),
                                'exp_date'=> date('Y-m-d'),
                                'cases'=> '0',
                                'submit_date_time'=> date('Y-m-d H:i:s'),
                                'server_date_time'=> date('Y-m-d H:i:s'),
                                'sstatus'=> 0,
                                'lat'=> '',
                                'lng'=> '',
                                'address'=> '',
                                'erp_plant_code'=> $erp_plant_code,
                                'div_code'=> $div_code,
                                'div_name'=> $div_name,
                                'item_name'=> $item_name,
                                'packing'=> $packing,
                                'erp_product_code'=> $erp_product_code,
                                'item_type'=> $item_type,
                                'item_group'=> $item_group,
                                'item_category'=> $item_category,
                                'hsn_code'=> $hsn_code,
                                'gst'=> $gst,
                                'shipper_qty_item'=> $shipper_qty_item,
                                'item_lock'=> $item_lock,
                                'opening_stock'=> $opening_stock,
                                
                            ]);
                    }

                
                            $inum++;   

                 }
                 else {
                    Session::flash('message', 'Please select UserPrimaryOrder file!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('ImportData ');
                }
             }
 
             if ($second_layer) {
                 DB::commit();
                 Session::flash('message', 'Uploaded Succesfully');
                 Session::flash('alert-class', 'alert-success');
             }
             else {
                 DB::rollback();
                 Session::flash('message', 'Something went wrong!');
                 Session::flash('alert-class', 'alert-danger');
             }
         }
         return redirect('ImportData');

        }
        ////////////////////////////////////////////////////////// Upload CFA Stock ///////////////////////////////////////////////////////////////////////////////////////






    } // !empty($request->excelFile) close

    else {
        Session::flash('message', 'Please select file first!');
        Session::flash('alert-class', 'alert-danger');
        return redirect('ImportData');
    }

 }
  
    
    public function RetailerData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Retailer_Query_data = DB::table('retailer')->where('company_id',$company_id)->select('retailer.name as retailer_name','retailer.id as retailer_id','retailer_code')->orderBy('retailer_id','ASC');

        $Retailer_Query = $Retailer_Query_data->get();

            $output .="S.No,Retailer id,Retailer Name,Retailer Code";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    
                  
                    $retailer_name = !empty($value->retailer_name)?str_replace(",","|",$value->retailer_name):'NA';

                    $output .=$i.',';
                    $output .=$value->retailer_id.',';
                    $output .=$retailer_name.',';    
                    $output .=$value->retailer_code.',';

                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RetailerExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


public function OwnerData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Retailer_Query_data = DB::table('_dealer_ownership_type')->where('company_id',$company_id)->where('status',1)->select('_dealer_ownership_type.ownership_type as ownership_type','_dealer_ownership_type.id as ot_id')->orderBy('id','ASC');

        $Retailer_Query = $Retailer_Query_data->get();

            $output .="S.No,ownership_type id,ownership_type Name";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    
                  
                    $ownership_type = !empty($value->ownership_type)?str_replace(",","|",$value->ownership_type):'NA';

                    $output .=$i.',';
                    $output .=$value->ot_id.',';
                    $output .=$ownership_type.',';    

                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=OwnerTypeExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


    public function CsaData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Retailer_Query_data = DB::table('csa')
                            ->join('location_3','location_3.id','=','csa.state_id')
                            ->select('csa.csa_name as csa_name','csa.c_id as c_id','location_3.name as state_name')
                            ->where('csa.company_id',$company_id)
                            ->where('active_status',1)
                            ->orderBy('c_id','ASC');

        $Retailer_Query = $Retailer_Query_data->get();

            $output .="S.No,CSA id,CSA Name,State Name";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    
                  
                    $csa_name = !empty($value->csa_name)?str_replace(",","|",$value->csa_name):'NA';
                    $state_name = !empty($value->state_name)?str_replace(",","|",$value->state_name):'NA';

                    $output .=$i.',';
                    $output .=$value->c_id.',';
                    $output .=$csa_name.',';    
                    $output .=$state_name.',';    

                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SuperStockistExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


    public function OutletTypeData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Retailer_Query_data = DB::table('_retailer_outlet_type')
                            ->select('_retailer_outlet_type.outlet_type as outlet_type','_retailer_outlet_type.id as id')
                            ->where('_retailer_outlet_type.company_id',$company_id)
                            ->where('status',1)
                            ->orderBy('id','ASC');

        $Retailer_Query = $Retailer_Query_data->get();

            $output .="S.No,Outlet Type id,Outlet Type Name";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    
                  
                    $outlet_type = !empty($value->outlet_type)?str_replace(",","|",$value->outlet_type):'NA';

                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$outlet_type.',';    

                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RetailerTypeExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


     public function OutletClassTypeData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Retailer_Query_data = DB::table('_retailer_outlet_category')
                            ->select('_retailer_outlet_category.outlet_category as outlet_category','_retailer_outlet_category.id as id')
                            ->where('_retailer_outlet_category.company_id',$company_id)
                            ->where('status',1)
                            ->orderBy('id','ASC');

        $Retailer_Query = $Retailer_Query_data->get();

            $output .="S.No,Outlet Class id,Outlet Class Name";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    
                  
                    $outlet_category = !empty($value->outlet_category)?str_replace(",","|",$value->outlet_category):'NA';

                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$outlet_category.',';    

                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RetailerClassTypeExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

     public function BeatData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Retailer_Query_data = DB::table('location_view')
                    ->where('l7_company_id',$company_id)
                    ->where('l6_company_id',$company_id)
                    ->where('l5_company_id',$company_id)
                    ->where('l4_company_id',$company_id)
                    ->where('l3_company_id',$company_id)
                    ->where('l2_company_id',$company_id)
                    ->where('l1_company_id',$company_id)
                    ->select('location_view.*')
                    ->orderBy('l7_id','ASC')
                    ->groupBy('l7_id');

        $Retailer_Query = $Retailer_Query_data->get();

            $output .="S.No,Beat id,Beat Name,Town Id,Town Name,HQ Id,HQ Name,Area Id,Area Name,State Id,State Name,Region Id,Region Name,Zone Id,Zone Name";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    
                  
                    $zone = !empty($value->l1_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l1_name):'NA';
                    $region = !empty($value->l2_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l2_name):'NA';
                    $state = !empty($value->l3_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l3_name):'NA';
                    $area = !empty($value->l4_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l4_name):'NA';
                    $headquarter = !empty($value->l5_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l5_name):'NA';
                    $towncity = !empty($value->l6_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l6_name):'NA';
                    $beat = !empty($value->l7_name)?preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->l7_name):'NA';

                    $output .=$i.',';
                    $output .=$value->l7_id.',';
                    $output .=$beat.',';    
                    $output .=$value->l6_id.',';
                    $output .=$towncity.',';
                    $output .=$value->l5_id.',';
                    $output .=$headquarter.',';
                    $output .=$value->l4_id.',';
                    $output .=$area.',';
                    $output .=$value->l3_id.',';
                    $output .=$state.',';
                    $output .=$value->l2_id.',';
                    $output .=$region.',';
                    $output .=$value->l1_id.',';
                    $output .=$zone.',';


                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=BeatExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


    public function RetailerFormat(Request $request)
    {
       
        $output ='';

            $output .="Retailer code,Retailer Name,dealer id,Beat id,address,email,contact person name,contact number,Retailer Class Id,landline,other numbers,tin no,pin no,outlet type id,average purchase,track address";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RetailerFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    
    public function SchemePlanFormat(Request $request)
    {
       
        $output ='';

            $output .="Sr.no,Scheme Name,Product Id,Sale Cases Range First,Sale Cases Range Last,Free Quantity";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SchemePlanFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function DealerCredentailsFormat(Request $request)
    {
       
        $output ='';

            $output .="Sr.no,dealer id,State id,Dealer Name,Username,Password,Mobile number,Dealer Email,Current Date Time";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=DealerCredentailsFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function CatalogFormat(Request $request)
    {
       
        $output ='';

        $output .="Product Name,Product Code,hsn_code,weight Type Id,Weight,Product Catagory Id,Primary Unit,Quanter Per Primary Unit,GST Per Unit,Secondary Unit,Quantity Per Secondary Unit,Description English,Description Hindi,Brand Details";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SKUNameFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function BeatFormat(Request $request)
    {
       
        $output ='';

        $output .="Beat Name,Town Id,Beat Number";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=BeatFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function TownFormat(Request $request)
    {
       
        $output ='';

        $output .="S.no,Town Name,HQ Id";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=TownFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


    public function DlrlFormat(Request $request)
    {
       
        $output ='';

        $output .="S.no,Dealer Id,Beat Id,Date";
            $output .="\n";
            $i=1;


                    $output .=$i.',';
                    $output .=' '.',';
                    $output .=' '.',';    
                    $output .=date('Y-m-d H:i:s').',';

                   
                    $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=DistributorBeatAssignFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function DealerFormat(Request $request)
    {
       
        $output ='';

        $output .="Dealer Name,Contact Person Name,dealer code,address,email,landline,other number,tin no,fssai no,pin no,ownership type id,average per month purchase,state id,town id,super stockist id";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=DealerFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

 
    public function DealerData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Dealer_Query_data = DB::table('dealer')
        ->join('location_3','location_3.id','=','dealer.state_id')
        // ->join('location_4','location_3_id','=','location_3.id')
        // ->join('location_5','location_4_id','=','location_4.id')
        ->join('location_6','location_6.id','=','dealer.town_id')
        ->select('dealer.id as dealer_id','dealer.name as dealer_name','location_3.name as state_name','location_6.name as town_name')
        ->where('dealer.company_id',$company_id)
        ->where('dealer_status',1)
        ->groupBy('dealer.id');
   
        $Dealer_Query = $Dealer_Query_data->get()->toarray();

            $output .="S.No,Distributor id,Distributor Name,State Name,Town Name";
            $output .="\n";
            $i=1;

            foreach ($Dealer_Query as $key => $value) 
            {

                $dealer_name = !empty($value->dealer_name)?str_replace(",","|",$value->dealer_name):'NA';

                    $final_town_name =  preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->town_name);
                    $final_state_name =  preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->state_name);
                    $final_dealer_name =  preg_replace('/[^A-Za-z0-9\-]/', ' ', $dealer_name);

                    $output .=$i.',';
                    $output .=''.$value->dealer_id.''.',';
                    $output .=$final_dealer_name.',';
                    $output .=$final_state_name.',';
                    $output .=$final_town_name.',';
                   
                
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=DistributorExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
      
    }

    public function StateData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Beat_Query_data = DB::table('location_5')
        ->select('id','name')
        ->where('company_id',$company_id)
        ->where('status',1)
        ->groupBy('id');
   
        $Beat_Query = $Beat_Query_data->get()->toarray();

            $output .="S.No,HQ id,HQ Name,";
            $output .="\n";
            $i=1;

            foreach ($Beat_Query as $key => $value) 
            {

             
                $state_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              

                    $output .=$i.',';
                    $output .=''.$value->id.''.',';
                    $output .=$state_name.',';
                  
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=HeadQuaterExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
      
    }


    public function StateId(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Beat_Query_data = DB::table('location_3')
        ->select('id','name')
        ->where('company_id',$company_id)
        ->where('status',1)
        ->groupBy('id');
   
        $Beat_Query = $Beat_Query_data->get()->toarray();

            $output .="S.No,State id,State Name,";
            $output .="\n";
            $i=1;

            foreach ($Beat_Query as $key => $value) 
            {

             
                $state_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              

                    $output .=$i.',';
                    $output .=''.$value->id.''.',';
                    $output .=$state_name.',';
                  
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=StateIdExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
      
    }

    public function TownData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Beat_Query_data = DB::table('location_6')
        ->select('id','name','location_5_id')
        ->where('company_id',$company_id)
        ->where('status',1)
        ->groupBy('id');
   
        $Beat_Query = $Beat_Query_data->get()->toarray();

            $output .="S.No,Town id,Town Name,HQ id,";
            $output .="\n";
            $i=1;

            foreach ($Beat_Query as $key => $value) 
            {

             
                $state_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              

                    $output .=$i.',';
                    $output .=''.$value->id.''.',';
                    $output .=$state_name.',';
                    $output .=''.$value->location_5_id.''.',';

                  
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=TownExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
      
    }

    public function ProductData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $Product_Query_data = DB::table('catalog_2')
        ->select('id','name')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->groupBy('id');
   
        $Product_Query = $Product_Query_data->get()->toarray();

            $output .="Catagory Product Id,Catagory Product Name";
            $output .="\n";
            $i=1;

            foreach ($Product_Query as $key => $value) 
            {

                $product_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              
                    $output .=''.$value->id.''.',';
                    $output .=$product_name.',';
                   
                
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=ProductNameExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
      
    }

    public function ProductRateListFormat(Request $request)
    {
       
        $output ='';

        $output .="Sr.no, SKU Id, State Id,State Name,Cases MRP,MRP,Dealer Case Rate,Dealer Pcs rate,Retailer Cases rate,Retailer Pcs rate,Super Stockist Case Rate,Super Stockist PCS Rate,Other Retailer Rate,Other Dealer Rate";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SKURateListFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }   
    public function location1_data(Request $request)
    {
        // dd('qwert');
        $company_id = Auth::user()->company_id;
        $output ='';
        $Beat_Query_data = DB::table('location_1')
        ->select('id','name')
        ->where('company_id',$company_id)
        ->where('status',1)
        ->groupBy('id')->get();
    // dd($Beat_Query_data);
        // $Beat_Query = $Beat_Query_data->get()->toarray();
        // dd($Beat_Query);
            $output .="S.No,Zone id,Zone Name";
            $output .="\n";
            $i=1;

            foreach ($Beat_Query_data as $key => $value) 
            {

             
                    $state_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              

                    $output .=$i.',';
                    $output .=''.$value->id.''.',';
                    $output .=$state_name.',';

                  
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=ZoneExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
    }
    public function location2_data(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $location2Query_data = DB::table('location_2')
        ->join('location_1','location_1.id','=','location_2.location_1_id')
        ->select('location_2.id as id','location_2.name as name','location_1_id')
        ->where('location_2.company_id',$company_id)
        ->where('location_2.status',1)
        ->groupBy('location_2.id');
   
        $location2_Query = $location2Query_data->get()->toarray();

            $output .="S.No,Region id,Region Name,Zone Id";
            $output .="\n";
            $i=1;

            foreach ($location2_Query as $key => $value) 
            {

             
                    $state_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              

                    $output .=$i.',';
                    $output .=''.$value->id.''.',';
                    $output .=$state_name.',';
                    $output .=$value->location_1_id.',';

                  
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=RegionExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
    }
     public function location4_data(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $location2Query_data = DB::table('location_4')
        ->join('location_3','location_3.id','=','location_4.location_3_id')
        ->join('location_2','location_2.id','=','location_3.location_2_id')
        ->join('location_1','location_1.id','=','location_2.location_1_id')
        ->select('location_4.id as id','location_4.name as name','location_1.id as l1_id','location_3.id as l3_id','location_2.id as l2_id')
        ->where('location_4.company_id',$company_id)
        ->where('location_4.status',1)
        ->groupBy('location_4.id');
   
        $location2_Query = $location2Query_data->get()->toarray();

            $output .="Sr.no,Area Id,Area Name,State Id,Region Id,Zone Id";
            $output .="\n";
            $i=1;

            foreach ($location2_Query as $key => $value) 
            {

             
                    $state_name = !empty($value->name)?str_replace(",","|",$value->name):'NA';
              

                    $output .=$i.',';
                    $output .=''.$value->id.''.',';
                    $output .=$state_name.',';
                    $output .=$value->l3_id.',';
                    $output .=$value->l2_id.',';
                    $output .=$value->l1_id.',';

                  
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=AreaExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
    }
    public function location5Format(Request $request)
    {
       
        $output ='';

        $output .="Sr.no,Head Quater Name,Area Id,current date";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=HeadQuarterFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function location4Format(Request $request)
    {
       
        $output ='';

        $output .="Sr.no,Area Name,State Id";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=AreaFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function location3Format(Request $request)
    {
       
        $output ='';

        $output .="Sr.no,state Name,Region Id,Current Date";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=StateFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function userDealerAssignFormat(Request $request)
    {
       
        $output ='';

        $output .="Sr.no,User Id,Distributor Id,Beat Id,Current Date Time";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=userDistributorAssignFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function location2Format(Request $request)
    {
       
        $output ='';

        $output .="Region Name,Zone Id";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=location2Format.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function SKUExport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $sku_data = DB::table('catalog_product')
                ->where('catalog_product.company_id',$company_id)
                ->select('catalog_product.id as id','catalog_product.name as name','itemcode')->where('catalog_product.status',1)->orderBy('catalog_product.id','ASC');

        $SKU_Query = $sku_data->get();

            $output .="S.No,Product id,Product Name,Product Code";
            $output .="\n";
            $i=1;

            foreach ($SKU_Query as $key => $value) 
            {
                // dd($value);
                    
                    $sku_name =  preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->name);
                  
                    // $retailer_name = !empty($value->retailer_name)?str_replace(",","|",$value->retailer_name):'NA';

                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$sku_name.',';    
                    $output .=$value->itemcode.',';
                  

                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SKU.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function userFormat(Request $request)
    {
       
        $output ='';


        $output .="Sr.no,First Name,Middle Name,Last Name,Designation Id,Senior Id,Head Quarter Id,Mobile,Email,State Id,Town Id,Emp Code,Joining Date,Address,Person Username,Person Password";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=UserFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function DistributorPersonalFormat(Request $request)
    {
       
        $output ='';


        $output .="Sr.no,Submit Date,Distributor Id,Pan No,Aadhar No,Food License,Bank Name,Security Amount,Reference no.,Security Date,Receipt Issue Date,Security Remarks,Commencement Date,Termination Date,Certificate Issue Date,Agreement Remarks,Refund Amount,Refund Ref No,Refund Date,Refund Remarks";
            $output .="\n";
               
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=DistributorPersonalDataFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    public function roleExport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $sku_data = DB::table('_role')
                ->where('_role.company_id',$company_id)
                ->where('_role.status',1)
                ->orderBy('_role.role_sequence','ASC');

        $SKU_Query = $sku_data->get();

            $output .="S.No,Role Id,Rolename";
            $output .="\n";
            $i=1;

            foreach ($SKU_Query as $key => $value) 
            {

                    $output .=$i.',';
                    $output .=$value->role_id.',';
                    $output .=$value->rolename.',';    
                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RoleExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }
    public function personExport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $sku_data = DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->select('person.*')
                ->where('person.company_id',$company_id)
                ->where('person_status',1)
                ->orderBy('person.id','DESC');

        $SKU_Query = $sku_data->get();

            $output .="S.No,Person Id,Person Name";
            $output .="\n";
            $i=1;

            foreach ($SKU_Query as $key => $value) 
            {

                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$value->first_name.' '.$value->middle_name.' '.$value->last_name.',';    
                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=UserExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }
    public function productUnitType(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $sku_data = DB::table('product_type')
                ->where('company_id',$company_id)
                ->where('status',1)
                ->orderBy('id','DESC');

        $SKU_Query = $sku_data->get();

            $output .="S.No,Unit Id,Unit Name,Type";
            $output .="\n";
            $i=1;

            foreach ($SKU_Query as $key => $value) 
            {
                    $type = ($value->flag_neha==1)?'Primary Unit':'Secondary Unit';
                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$value->name.',';    
                    $output .=$type.',';    
                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=unitExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }
    public function weightType(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';
        $sku_data = DB::table('weight_type')
                ->where('company_id',$company_id)
                ->where('status',1)
                ->orderBy('id','DESC');

        $SKU_Query = $sku_data->get();

            $output .="S.No,weight Id,weight Name,Value";
            $output .="\n";
            $i=1;

            foreach ($SKU_Query as $key => $value) 
            {
                    // $type = ($value->flag_neha==1)?'Primary Unit':'Secondary Unit';
                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$value->type.',';    
                    $output .=$value->value.',';    
                    // $output .=$type.',';    
                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=WeightTypeExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }

    public function superStockiestSkuWiseTargetFormat(Request $request)
    {
       
        $output ='';
         


            $output .="Sr.no,State Id,Super Stokiest Name,Super Stokiest Id,Product Name,Product Id,Quantity(Cases),Month(YYYY-MM)";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SuperStockiestSkuWiseTargetFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function distributorSkuWiseTargetFormat(Request $request)
    {
       
        $output ='';
         


            $output .="Sr.no,State Id,Super Stokiest Name,Super Stokiest Id,Distributor Name,Distributor Id,Product Name,Product Id,Quantity(Cases),Month(YYYY-MM)";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=DistributorSkuWiseTargetFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function TourPlanFormat(Request $request)
    {
       
        $output ='';
         


            $output .="Sr.no,User Id,Working Date(YYYY-mm-dd),Task Of The Id,Distributor Id,Town Id,Beat Id,Productive Calls,Secondary Sale(RV),Collection(RV),Primary Order(RV),New Outlet,Remarks";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=TourPlanFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


    public function UserSkuWiseTargetFormat(Request $request)
    {
       
        $output ='';
         


            $output .="Sr.no,State Id,State Name,Beat Name,Beat Id,User Name,User Id,Product Name,Product Id,Quantity(Cases),Month(YYYY-MM)";
            $output .="\n";

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=UserSkuWiseTargetFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }

    public function exportTargetFormat(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $category_data = DB::table('catalog_1')
                        ->where('status',1)
                        ->where('company_id',$company_id)
                        ->pluck('name','id');
        $output ='';
        $output .='Stockist wise SKU wise Target month';
        $output .="\n";
        $output .='Month,'.date('Y-m');
        $output .="\n";


        // in guruji stockiest is a  distriburor
        $output .=",,,,,,,,,";
        foreach ($category_data as $key => $value) 
        {
            $sku_data = DB::table('catalog_product')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->where('catalog_product.company_id',$company_id)
                        ->where('catalog_2.catalog_1_id',$key)
                        ->where('catalog_product.status',1)
                        ->orderBy('catalog_product.id','ASC')
                        ->pluck('catalog_product.name as name','catalog_product.id as id');
            $output .=$value;
            foreach ($sku_data as $s_key => $s_value) 
            {
                $output .=",";
            }
        }
        $output .="\n";
        $output .="Sr.no,user id ,user name,stockiest id,stockiest name,HQ id ,HQ Name,Town Id,Town Name,";
        foreach ($category_data as $key => $value) 
        {
            // $output .=",,,,,,,,,".$value.',';
            // dd($value);
            $sku_data = DB::table('catalog_product')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->where('catalog_product.company_id',$company_id)
                        ->where('catalog_2.catalog_1_id',$key)
                        ->where('catalog_product.status',1)
                        ->orderBy('catalog_product.id','ASC')
                        ->pluck(DB::raw("concat(catalog_product.name,' ^',catalog_product.id,'^') as data"),'catalog_product.id as id');
                        // ->pluck('name','id');
            // dd($sku_data);
            foreach ($sku_data as $s_key => $s_value) 
            {
                $output .=$s_value.',';
            }
        }
        $output .="\n";

        // header part end 

        // body data part start 

        $user_data = DB::table('dealer_location_rate_list')
                    ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                    ->join('person','person.id','=','dealer_location_rate_list.user_id')
                    ->join('users','users.id','=','dealer_location_rate_list.user_id')
                    ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                    ->select(DB::raw("group_concat(Distinct person.id  SEPARATOR '|') as user_id"),DB::raw("group_concat(Distinct CONCAT_WS(' ',first_name,middle_name,last_name) SEPARATOR '|') as user_name"),'dealer.id as dealer_id','dealer.name as dealer_name','l5_id','l5_name','l6_id','l6_name')
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('dealer_status',1)
                    ->where('is_admin','!=',1)
                    ->groupBy('dealer.id','dealer.name')
                    ->get();
        foreach ($user_data as $key => $value) 
        {
            $output .= ($key+1).',';
            $output .= $value->user_id.',';
            $output .= preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->user_name).',';
            $output .= $value->dealer_id.',';
            $output .= preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->dealer_name).',';
            $output .= $value->l5_id.',';
            $output .= preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->l5_name).',';
            $output .= $value->l6_id.',';
            $output .= preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->l6_name).',';
            foreach ($category_data as $key => $value) 
            {
                // $output .=",,,,,,,,,".$value.',';
                // dd($value);
                $sku_data = DB::table('catalog_product')
                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            ->where('catalog_product.company_id',$company_id)
                            ->where('catalog_2.catalog_1_id',$key)
                            ->where('catalog_product.status',1)
                            ->orderBy('catalog_product.id','ASC')
                            ->pluck('catalog_product.name as name','catalog_product.id as id');
                            // ->pluck('name','id');
                // dd($sku_data);
                foreach ($sku_data as $s_key => $s_value) 
                {
                    $output .='0'.',';
                }
            }
            $output .="\n";
           
        }
        // dd($output);
        // $output .="\n";



            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=DistributorSkuWiseTargetFormat.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;

    }

    public function SchemePlanUploadFormat(Request $request)
    {
       
        $output ='';
        $company_id = Auth::user()->company_id;

         
            $output .="Sr.no,State Id,State Name,Item Code,Product Id,Product Name,Value Amount(Percentage),Valid From(YYYY-mm-dd),Valid To(YYYY-mm-dd)";
            $output .="\n";


            $state = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->groupBy('id')
                    ->pluck('name','id')->toArray();

            $catalog_product = DB::table('catalog_product')
                            ->where('company_id',$company_id)
                            ->where('status',1)
                            ->groupBy('id')
                            ->pluck('name','id')->toArray();

            $catalog_product_item_code = DB::table('catalog_product')
                            ->where('company_id',$company_id)
                            ->where('status',1)
                            ->groupBy('id')
                            ->pluck('itemcode','id')->toArray();

            $valid_dates = DB::table('product_wise_scheme_plan_details')
                            ->select('valid_from_date','valid_to_date')
                            ->where('company_id',$company_id)
                            ->orderBy('id','DESC')
                            ->first();

                            // dd($valid_dates);

        $new_from_date = date('Y-m-d H:i:s',strtotime($valid_dates->valid_to_date .' +1 day')); 

        $new_to_date = date('Y-m-d H:i:s',strtotime($new_from_date .' +30 day')); 
                $key = 1;
            foreach ($state as $skey => $svalue) {
                foreach ($catalog_product as $ckey => $cvalue) {

                        $item_code = !empty($catalog_product_item_code[$ckey])?$catalog_product_item_code[$ckey]:'';
                        $output .= $key.',';
                        $output .= $skey.',';
                        $output .= $svalue.',';

                        $output .= $item_code.',';

                        $output .= $ckey.',';
                        $output .= $cvalue.',';
                        $output .= '0'.',';
                        $output .= $new_from_date.',';
                        $output .= $new_to_date.',';
                        $output .="\n";
                $key++;

                }

            }





                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=SchemePlanUploadFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }
    

    public function ExportSchemePlanDetailsMaster(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';

        $scheme_data = DB::table('product_wise_scheme_plan_details')
                        ->join('location_3','location_3.id','=','product_wise_scheme_plan_details.state_id')
                        ->join('catalog_product','catalog_product.id','=','product_wise_scheme_plan_details.product_id')
                        ->select('product_wise_scheme_plan_details.*','location_3.name as state_name','catalog_product.name as product_name')
                        ->where('product_wise_scheme_plan_details.company_id',$company_id)
                        ->groupBy('product_wise_scheme_plan_details.id');

        $scheme_query = $scheme_data->get();



            $output .="S.No,State Id,State Name,Product Id,Product Name,Value In Percentage,Valid From,Valid To";
            $output .="\n";
            $i=1;

            foreach ($scheme_query as $key => $value) 
            {
                    // $type = ($value->flag_neha==1)?'Primary Unit':'Secondary Unit';
                    $output .=$i.',';
                    $output .=$value->state_id.',';
                    $output .=$value->state_name.',';    
                    $output .=$value->product_id.',';    
                    $output .=$value->product_name.',';    
                    $output .=$value->value_amount_percentage.',';    
                    $output .=$value->valid_from_date.',';    
                    $output .=$value->valid_to_date.',';    
                    // $output .=$type.',';    
                   
                    $output .="\n";
                    $i++;
         

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=ExportSchemePlanDetailsMaster.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }
    public function ExportTaskOfTheDay(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';

        $scheme_data = DB::table('_task_of_the_day')
                        // ->select('_task_of_the_day.*','location_3.name as state_name','catalog_product.name as product_name')
                        ->where('_task_of_the_day.company_id',$company_id)
                        ->groupBy('_task_of_the_day.id');

        $scheme_query = $scheme_data->get();



            $output .="S.No, Id, Name";
            $output .="\n";
            $i=1;

            foreach ($scheme_query as $key => $value) 
            {
                    // $type = ($value->flag_neha==1)?'Primary Unit':'Secondary Unit';
                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$value->task.',';    
                     
                    // $output .=$type.',';    
                   
                    $output .="\n";
                    $i++;
         

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=ExportTaskOfTheDay.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }
    public function ExportWorkStatus(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';

        $scheme_data = DB::table('_working_status')
                        // ->select('_task_of_the_day.*','location_3.name as state_name','catalog_product.name as product_name')
                        ->where('_working_status.company_id',$company_id)
                        ->groupBy('_working_status.id');

        $scheme_query = $scheme_data->get();



            $output .="S.No, Id, Name";
            $output .="\n";
            $i=1;

            foreach ($scheme_query as $key => $value) 
            {
                    // $type = ($value->flag_neha==1)?'Primary Unit':'Secondary Unit';
                    $output .=$i.',';
                    $output .=$value->id.',';
                    $output .=$value->name.',';    
                     
                    // $output .=$type.',';    
                   
                    $output .="\n";
                    $i++;
         

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=ExportWorkStatus.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }



    public function ExportOutletDataForMaheshNamkeen(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';

      

        $query = DB::table('retailer')
                ->join('location_7','location_7.id','=','retailer.location_id')
                ->join('location_6','location_7.location_6_id','=','location_6.id')
                ->select('retailer.*','location_6.name as town','location_7.name as beat')
                ->where('retailer_status','=','1')
                ->where('retailer.company_id',$company_id)
                ->groupBy('retailer.id')
                ->get();


        $dealerName = DB::table('dealer')
                        ->where('company_id',$company_id)
                        ->pluck('name','id');

        
        $csaName = DB::table('dealer')
                    ->join('csa','csa.c_id','=','dealer.csa_id')
                    ->where('dealer.company_id',$company_id)
                    ->where('csa.company_id',$company_id)
                    ->groupBy('dealer.id')
                    ->pluck('csa_name','dealer.id');


        $roles_so_mahesh = array(291);
        $user_names_so = DB::table('retailer')
                ->join('location_7','location_7.id','=','retailer.location_id')
                ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                ->join('person','person.id','=','dealer_location_rate_list.user_id')
                ->join('person_login','person_login.person_id','=','person.id')
                ->where('retailer.company_id',$company_id)
                ->where('location_7.company_id',$company_id)
                ->where('dealer_location_rate_list.company_id',$company_id)
                ->where('person.company_id',$company_id)
                ->where('person_login.person_status',1)
                ->whereIn('person.role_id',$roles_so_mahesh)
                ->groupBy('retailer.id')
                ->pluck(DB::raw("group_concat(distinct(CONCAT_WS(' ',first_name,middle_name,last_name))) as name"),DB::raw("CONCAT(retailer.id) as data"))->toArray();


            $output .="S.No, SS Name,Distributor Name,Outlet Name,Owner Name,Address,Town/City,Mobile No.,Beat,SO Name,Approx Monthly Billing";
            $output .="\n";
            $i=1;

            foreach ($query as $key => $value) 
            {

                $dealer_name = !empty($dealerName[$value->dealer_id])?$dealerName[$value->dealer_id]:'';
                $csa_name = !empty($csaName[$value->dealer_id])?$csaName[$value->dealer_id]:'';


                $user_names_so_final =   !empty($user_names_so[$value->id])?str_replace(",","-",$user_names_so[$value->id]):'NA';




                    $output .=$i.',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$csa_name).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$dealer_name).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->name).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->contact_per_name).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->track_address).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->town).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->landline).',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->beat).',';
                    $output .=$user_names_so_final.',';
                    $output .=preg_replace('/[^A-Za-z0-9\-.@|]/', ' ',$value->avg_per_month_pur).',';



                     
                    // $output .=$type.',';    
                   
                    $output .="\n";
                    $i++;
         

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RetailTracker.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }




     public function PrimaryUploadFormat(Request $request)
    {
       
        $output ='';
        $company_id = Auth::user()->company_id;

         
            $output .="Sr.no,Distributor Id,Dispatch Through,Destination,Remarks,Product Id,Quantity(Pcs)";
            $output .="\n";


            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=PrimaryUploadFormat.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;    
    
        

    }




    public function CsaStockFormat(Request $request)
    {
       
        $output ='';
        $company_id = Auth::user()->company_id;

         
        $output .="Sr.no,CFA Code,CFA ID,CFA Name,ERP Plant Code,DIV Code,DIV Name,Item Name,Packing,Item Code,ERP Product Code,Item Type,Item Group,Item Category,HSN Code,GST%,Shipper Qty (Item),Item Lock,Opening Stock,Closing Stock Qty,Closing Stock Value";
        $output .="\n";


        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=CFAStockFormat.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $output;    

    }




    public function ExportStockFormat(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $output ='';

        $dealerAssign = DB::table('dealer_location_rate_list')
                        ->select('person.person_id_senior','_role.rolename','location_3.name as state_name','person.id as user_id','dealer.id as dealer_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'dealer.name as dealer_name','location_6.name as l6_name','location_5.name as l5_name','location_4.name as l4_name')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('location_3','location_3.id','=','person.state_id')

                        ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')

                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('person.company_id',$company_id)
                        ->where('dealer.company_id',$company_id)
                        ->where('person_status','=','1')
                        ->where('dealer_status','=','1')
                        ->where('dealer_location_rate_list.user_id','!=','0')
                        ->groupBy('dealer_location_rate_list.user_id','dealer_location_rate_list.dealer_id')
                        ->get();




      $sku_det = DB::table('catalog_product')
                ->where('company_id',$company_id)
                ->where('status','=','1')
                ->groupBy('id')
                ->pluck('name','id');

    $udet = DB::table('person')
            ->where('company_id',$company_id)
            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');


            $output .="S.No,State Name,Area Name,Head Quarter Name,Town/City Name, Dealer Name, Dealer Id,User Name, User Id,Role,Senior Name,SKU Name,SKU Id,mrp, pcs_mrp, stock_qty,mfg_date,exp_date,cases";
            $output .="\n";
            $i=1;

            foreach ($dealerAssign as $key => $value) 
            {
                foreach ($sku_det as $skey => $svalue) 
                {

                    $senName = !empty($udet[$value->person_id_senior])?$udet[$value->person_id_senior]:'';


                    $output .=$i.',';
                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->state_name).',';

                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->l4_name).',';
                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->l5_name).',';
                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->l6_name).',';


                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->dealer_name).',';
                    $output .=$value->dealer_id.',';    
                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->user_name).',';    
                    $output .=$value->user_id.',';    
                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $value->rolename).',';    
                    $output .=preg_replace('/[^A-Za-z0-9\/-]/', '', $senName).',';    

                    $output .=$svalue.',';    
                    $output .=$skey.',';    
                    $output .=',';    
                    $output .=',';    
                    $output .=',';    
                    $output .=',';    
                    $output .=',';    
                    $output .=',';    
                    $output .=',';    
                    $output .="\n";
                    $i++;
                }

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=ExportStockFormat.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;   
    }


}
