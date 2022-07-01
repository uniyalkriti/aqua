<?php

namespace App\Http\Controllers;

use App\Company;
use App\Location1;
use App\Location3;
use App\PersonDetail;
use App\PersonLogin;
use DB;
use App\Person;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class UserJuniorController extends Controller
{
    public function __construct()
    {
        $this->current_menu='user';

        $this->status_table='person';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $datasenior_call=self::getJuniorUser($login_user);
        $datasenior = $request->session()->get('juniordata');
        if(empty($datasenior))
        {
            $datasenior[]=$login_user;
        }
        
        $q=Person::join('person_details','person_details.person_id','=','person.id','inner')
            ->join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            ->leftJoin('location_3','person.state_id','=','location_3.id')
            ->select('person.*','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,last_name)  FROM person AS p1 
                WHERE p1.id=person.person_id_senior LIMIT 1) as srname"))
            ->where('person.status','!=',2);

        if (!empty($datasenior)) 
            {
                $q->whereIn('person.id', $datasenior);
            }

        $data = $q->orderBy('person.id', 'desc')->get();

         $distributor_details=[];
        if (!empty($data))
        {
            foreach ($data as $k=>$v)
            {
                $uid=$v->id;
                $data[$k]['details']=DB::table('dealer_location_rate_list')
                ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                ->select('dealer_location_rate_list.user_id','dealer_location_rate_list.dealer_id','dealer.name AS dealer_name',DB::raw("count(location_id) AS beat_count"),DB::raw("(SELECT count(DISTINCT retailer_id) FROM user_dealer_retailer AS udr WHERE udr.user_id=dealer_location_rate_list.user_id AND dealer_location_rate_list.dealer_id=udr.dealer_id LIMIT 1) AS retailer_count"))
                ->where('dealer_location_rate_list.user_id',$uid)
                ->groupBy('dealer_name','retailer_count')
                ->get();
            }
        }
     //  dd($distributor_details);
        

        return view('user.junior', [
            'records' => $data,
            'status_table' => $this->status_table,
            'current_menu'=>$this->current_menu,
        
            'distributor_details' => $distributor_details
        ]);

    }
    public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = DB::table('person')->where('person_id_senior',$code)
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
public function submitPreviousData(Request $request)
    {
        $query = Db::table('person')->join('person_login','person_login.person_id','=','person.id')->select('first_name','middle_name','last_name','person.id as user_id','person_username','person_login.person_status as status','role_id',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"))->where('person.status',1)->where('person_status',1)->groupBy('user_id')->get();
        // dd($query);
        foreach ($query as $key => $value) {
            $user_id = $value->user_id;
            $first_name = $value->first_name;
            $middle_name = $value->middle_name;
            $last_name = $value->last_name;
            $person_username = $value->person_username;
            $status = $value->status;
            $person_password = $value->person_password;
            $role_id = $value->role_id;

            $myArr = [
                'id' => $user_id,
                'role_id'=> $role_id,
                'name'=> $first_name.$middle_name.$last_name,
                'email'=> $person_username,
                'password'=> bcrypt($person_password),
                'original_pass'=> $person_password,
                'status'=> $status,
                'created_at'=>date('Y-m-d'),
            ];
            $update_query = User::create($myArr);
        }
        if(!empty($update_query))
        {
            echo "submitted";
        }
        else
        {
            echo "not updated";
        }
    }
}
