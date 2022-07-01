<?php
namespace App\Http\Controllers;
use App\UserDetail;
use App\Expense;
use Illuminate\Http\Request;
use Auth;
use Image;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = Auth::user()->company_id;

        if(Auth::user()->is_admin=='1') {
            $leaves = DB::table('user_expense_details')->join('person','person.id','=','user_expense_details.user_id')
                    ->join('expense_type','expense_type.id','=','user_expense_details.expense_type')
                    ->select('user_expense_details.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'expense_type.name as expense_type_name')
                    ->where('expense_type.status',1)
                    ->where('user_expense_details.company_id',$company_id)
                    ->paginate(10);
        }else{
            $leaves =DB::table('user_expense_details')->join('person','person.id','=','user_expense_details.user_id')
                        ->join('expense_type','expense_type.id','=','user_expense_details.expense_type')
                    ->select('user_expense_details.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'expense_type.name as expense_type_name')
                    ->where('user_id',$user->id)
                    ->where('expense_type.status',1)
                    ->paginate(10);  
            

          
        }
        $data = array();
        $leaves_type_data = array();
        
        return view('admin.expense.index',compact('leaves','user','data','leaves_type_data'));
        
    }
    public function create(Request $request)
    {
        // $current_menu = 
        $expense_type = DB::table('expense_type')->where('status',1)->orderBy('sequence',1)->pluck('name','id');
        // dd($expense_type);
        return view('admin.expense.create',compact('expense_type'));
        
    }
    public function store(Request $request)
    {
        // dd($request);
        $expense_type = $request->expense_type;
        $company_id  = Auth::user()->company_id;
        $user_id  = Auth::user()->id;
        foreach ($expense_type as $key => $value) 
        {

            if ($request->hasFile('imageFile')) {
                // if($request->file('imageFile')->isValid()) {
                    try {
                        $file = $request->file('imageFile');
                        $expense_images = date('YmdHis') . '.' . $file[$key]->getClientOriginalExtension();

                        # save to DB
                        // $personImage = PersonLogin::where('person_id',$person->id)->update(['person_image' => 'users-profile/'.$name]);

                        $file[$key]->move("expense_image", $expense_images);
                    } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                    }
                // }
            }
            $arr = [
                'expense_type' => !empty($value)?$value:'',
                'company_id'=> $company_id,
                'user_id'=> $user_id,
                'expense_date' => !empty($request->expense_date[$key])?$request->expense_date[$key]:'',
                'expense_fare' => !empty($request->fare[$key])?$request->fare[$key]:'',
                'expense_remarks' => !empty($request->remarks[$key])?$request->remarks[$key]:'',
                'expense_image' => !empty($expense_images)?'expense_image/'.$expense_images:'',
                'created_at'=>date('Y-m-d H:i:s'),

            ];
            $data_insertion = DB::table('user_expense_details')->insert($arr);
        }
        return redirect()->intended('expense');
        // return redirect('expense');
    }
    public function paid(Request $request)
    {
        $paid_id = $request->id;
        $paid = $request->paid;
        $data_insertion = DB::table('user_expense_details')->where('id',$paid_id)->update(['paid_status'=>$paid]);

        return redirect()->intended('expense');
        // dd($request);
    }


	 public function show(Request $request,$id)
    {
       // dd('f');\
        $zone = $request->zone; 
        $region = $request->region;
        $state = $request->state;
        $user_id = Crypt::decryptString($id);
        // dd($user_id);
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-d');
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');

        $company_id = Auth::user()->company_id;


        // $arr = [1 => 'Bus', 2 => 'Train', 3 => 'Motorcycle', 4 => 'Taxi', 5 => 'Flight', 6 => 'Metro'];

        $arr = DB::table('_travelling_mode')
               ->where('company_id',$company_id)
               ->where('status',1) 
               ->pluck('mode','id')->toArray();


        $query = DB::table('travelling_expense_bill')
            ->leftJoin('person', 'person.id', '=', 'travelling_expense_bill.user_id')
            ->leftJoin('location_6 as d', 'd.id', '=', 'travelling_expense_bill.departureID')
            ->leftJoin('location_6 as a', 'a.id', '=', 'travelling_expense_bill.arrivalID');

        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
        }
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereRaw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') <='$to_date'");
        }
        $query_data = $query->select('a.name as aname', 'd.name as dname', 'travelling_expense_bill.*', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'))
            ->where('travelling_expense_bill.company_id',$company_id)
            ->orderBy('date_time', 'DESC')
            ->get();
            // dd($query_data);
        return view('reports.expense', [
            'records' => $query_data,
            'arr' => $arr,
            'id'=>$id
        ]);

    }
}