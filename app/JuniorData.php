<?php

namespace App;
use Session;
use DB;

use Illuminate\Database\Eloquent\Model;

class JuniorData extends Model
{
    public static function getJuniorUser($code,$company_id)
    {
        $res1="";
        $res2="";
        // dd($code);
        $details = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select('person.id as user_id','person.company_id as p_company_id')
            ->where('person_id_senior',$code)
            ->where('person.company_id',$company_id)
            ->where('person_login.company_id',$company_id)
            ->where('person_id_senior','!=',0)
            // ->where('is_admin',0)
            ->where('person_status',1)
            ->get();
            $num = count($details);  
            if($num>0)
            {

                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="" || $res2->user_id!="0")
                    {
                        // dd($res2);
                        //$product = collect([1,2,3,4]);
                        Session::push('juniordata', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        Self::getJuniorUser($res2->user_id,$res2->p_company_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="" || $res2->user_id!="0")
                    {
                        Session::push('juniordata', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            // dd(Session::get('juniordata'))
            
            return 1;
    }

    // public function 
    public static function getSeniorUser($code,$company_id)
    {
        $res1="";
        $res2="";
        // dd($code);
        $details = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select('person_id_senior as user_id','person.company_id as p_company_id')
            ->where('person.id',$code)
            ->where('person.company_id',$company_id)
            ->where('person_login.company_id',$company_id)
            ->where('person_id_senior','!=',0)
            ->where('is_admin','!=',1)
            ->where('person_status',1)
            ->get();
            $num = count($details);  
            if($num>0)
            {

                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="" || $res2->user_id!="0")
                    {
                        // dd($res2);
                        //$product = collect([1,2,3,4]);
                        Session::push('seniorData', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        Self::getSeniorUser($res2->user_id,$res2->p_company_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="" || $res2->user_id!="0")
                    {
                        Session::push('seniorData', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            // dd(Session::get('juniordata'))
            return 1;
        }
    
}
