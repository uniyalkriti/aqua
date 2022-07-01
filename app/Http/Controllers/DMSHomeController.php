<?php

namespace App\Http\Controllers;

use App\_module;
use App\_subModule;
use App\Person;
use Illuminate\Http\Request;
use App\Dealer;
use App\Retailer;
use App\JuniorData;
use App\Location2;
use DB;
use DateTime;
use Storage;
use Response;
use Session;
use App\UserSalesOrder;
use App\SecondarySale;
use Illuminate\Support\Facades\Crypt;
use App\ChallanOrder;
use ZipArchive;
use Input;

class DMSHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        session_start();
        // dd($_SESSION);
        $auth_id = !empty($_SESSION['iclientdigimetid'])?$_SESSION['iclientdigimetid']:'0';
        $this->auth_id = $auth_id; 
        $this->dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
        $this->csa_id = !empty($_SESSION['iclientdigimetdata']['csa_id'])?$_SESSION['iclientdigimetdata']['csa_id']:'0';
        $this->dealer_code = !empty($_SESSION['iclientdigimetdata']['dealer_code'])?$_SESSION['iclientdigimetdata']['dealer_code']:'0';
        $data_role_id = DB::table('dealer_person_login')->where('dpId',$auth_id)->first();
        $this->role_id = !empty($data_role_id->role_id)?$data_role_id->role_id:'0';
        $this->menu = 'Dashboard';
        if($this->role_id != '1')
        {
            header('Location: http://demo.msell.in/client');
            dd('1');
        }
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function download_image(Request $request)
    {
        // $filepath = 'http://demo.msell.in/client/images/bdlogo.png';
        // // $content = Storage::get($filepath);
        // // return response($content)->header('Content-Type', $type);
        // // return $filepath->download('qwe.png');
        $files = array('20210210115739.png', '20210210115739.png', '20210210115739.png');
        $zip = new ZipArchive();
        $zip_name = "test.zip"; // Zip name
        $zip->open($zip_name,  ZipArchive::CREATE);
        foreach ($files as $file) {
          echo $path = "circular_image/".$file;
          if(file_exists($path)){
          $zip->addFromString(basename($path),  file_get_contents($path));  
          }
          else{
           echo"file does not exist";
          }
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zip_name);
        // header('Content-Length: ' . filesize($zipname));
        readfile($zip_name);
        
        
        // $zipname = 'file.zip';
        // $zip = new ZipArchive;
        // $zip->open($zipname, ZipArchive::CREATE);
        // foreach ($files as $file) {
        //   $zip->addFile($file);
        // }
        // $zip->close();

        // // return Response::download($zipname);
        // $path = public_path('circular_image/');
        // // Input::file('image')->move($path, $zipname);
        // // $request->file('imageFile')->move("users-profile", $name);

        // $filepath = public_path('circular_image/').$zipname;

        // header('Content-Type: application/zip');
        // header('Content-disposition: attachment; filename='.$zipname);
        // // header('Content-Length: ' . filesize($zipname));
        // readfile($zipname);
    }
    public function index(Request $request)
    {   

        // dd('1');

        $check_dashboard = Session::get('dms_dashboard');// this variable get the session values 
        // dd($check_dashboard[0]['is_set']);
        $current_menu='DASHBOARD';
        $role_id=$this->role_id;
        $user = '';
        $person_name = '';
        if(isset($request->date_range_picker))
        {
            Session::forget('dms_is_set');
            // dd($check_dashboard);
            // Session::forget();   
        }
        // dd($user->role_id);
        if( $role_id != '26' && $role_id != '1' )
        {
            header('Location: http://baidyanath.msell.in/client');
            dd('1');
        }
        // dd($junior_data);
            if(isset($request->date_range_picker))
            {
                // Session::forget('is_set');
                // dd($check_dashboard);
                // Session::forget();   
            }
            else
            {
                if($check_dashboard[0]['dms_is_set'] == 1 )  //This condition is check if the data were in session or not
                {
                    $dashboard = $check_dashboard[0];
                    // dd($dashboard);
                    return view('DMS.welcome',
                    [
                        'menu' => $this->menu,
                        'current_menu' => $current_menu,
                        'totalDistributor' => $dashboard['totalDistributor'],
                        'dealer_deactive_status' => $dashboard['dealer_deactive_status'],
                        'dealer_active_status' => $dashboard['dealer_active_status'],
                        'demand_order_count' => $dashboard['demand_order_count'],
                        'invoice_details_count' => $dashboard['invoice_details_count'],
                        'credit_note_count' => $dashboard['credit_note_count'],
                        'depo_count' => $dashboard['depo_count'],
                        'from_date' => date('Y-m-d'),
                        'to_date' => date('Y-m-d')
                    ]);
                }

            }
                
             // seesion check end here     
            $cdate=date('Y-m-d');
            $location_3_filter = $request->location3;
            $division_filter = $request->division;
            // dd($location_3_filter);
            if(isset($request->date_range_picker))
            {
                // dd($request);
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
                $mdate = date('Y-m',strtotime(trim($explodeDate[0])));
            }
            else
            {
                $mdate=date('Y-m');
               
                $from_date = date('Y-m-01');
                $to_date = date('Y-m-t');
                // $last = date('')
            }
            // dd($from_date);
        $new_from_date = str_replace('-','',$from_date);
        $new_to_date = date('Y-m-d',strtotime($to_date .' +1 day')); 
        $new_to_date = str_replace('-','',$new_to_date);
        // $new_to_date = 
        // dd($new_to_date);
        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $days = count($datearray);
        // dd(count($datearray));
        $from_final_date = date('Y-m-d');
        $to_final_date = date('Y-m-d');

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        // USER DETAILS
        $totalDistributor = DB::table('ACC_MAST')
                        ->count();

        $dealer_deactive_status = DB::table('ACC_MAST')
                                ->where('ACC_STATUS','')
                                ->count();

        $dealer_active_status = DB::table('ACC_MAST')
                                ->where('ACC_STATUS','!=','')
                                ->count();

        $demand_order_count = DB::table('demand_order')
                    ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')
                    ->whereRaw("DATE_FORMAT(order_date,'%Y-%m-%d')>='$from_final_date' AND DATE_FORMAT(order_date,'%Y-%m-%d')<='$to_final_date' ")
                    ->distinct('demand_order.order_id')->count('demand_order.order_id');


        $invoice_details_count = DB::table('ITEMTRAN_HEAD')
                    ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                    ->whereRaw("DATE_FORMAT(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_final_date' AND DATE_FORMAT(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_final_date' ")
                    ->distinct('ITEMTRAN_HEAD.VRNO')->count('ITEMTRAN_HEAD.VRNO');

        $credit_note_count = DB::table('ACCNOTE_TRAN')
                    ->whereRaw("DATE_FORMAT(ACCNOTE_TRAN.VRDATE_FILTER,'%Y-%m-%d')>='$from_final_date' AND DATE_FORMAT(ACCNOTE_TRAN.VRDATE_FILTER,'%Y-%m-%d')<='$to_final_date' ")
                    ->distinct('ACCNOTE_TRAN.VRNO')->count('ACCNOTE_TRAN.VRNO');

        $depo_count = DB::table('ACC_MAST')
                    ->where('DEPOT_FLAG',1)
                    ->count();


        // $dashboard_arr this array push the dashboard values in session
        $dashboard_arr=array('dms_is_set'=>1,
            
            'totalDistributor' => $totalDistributor,
            'dealer_deactive_status' => $dealer_deactive_status,
            'dealer_active_status' => $dealer_active_status,
            'demand_order_count' => $demand_order_count,
            'invoice_details_count' => $invoice_details_count,
            'credit_note_count' => $credit_note_count,
            'depo_count' => $depo_count,
            'to_date'=> $to_date,
            'from_date'=> $from_date

        );
        // dd($dashboard_arr);
        Session::push('dms_dashboard', $dashboard_arr);
        // $dashboard_arr array ends here

        return view('DMS.welcome',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'totalDistributor' => $totalDistributor,
                'dealer_deactive_status' => $dealer_deactive_status,
                'dealer_active_status' => $dealer_active_status,
                'demand_order_count' => $demand_order_count,
                'invoice_details_count' => $invoice_details_count,
                'credit_note_count' => $credit_note_count,
                'to_date'=> $to_date,
                'from_date'=> $from_date,
                'depo_count' => $depo_count
            ]);
  

        

    }
  
  


   



  

    
}
