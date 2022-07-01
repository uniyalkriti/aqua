<?php

namespace App;
use Auth;
use DB;
use Lang;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $guarded = array();
    protected $table='person';

    public static function user_details_master($company_id)
    {
        $user_data = Person::join('person_details','person_details.person_id','=','person.id','inner')
                    ->join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    ->join('salary_management','salary_management.user_id','=','person.id')
                    ->join('location_3','person.state_id','=','location_3.id')
                     ->select('person_details.created_on as created_on','person_details.address as personaddress','person.town_id as town_name','person.head_quater_id as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
                        WHERE p1.id=person.person_id_senior LIMIT 1) as srname"),'person_details.deleted_deactivated_on as deactivate_date','person_login.last_mobile_access_on as last_sync')
                    ->where('person.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person_status','=','1')
                    ->where('person.id','!=','1')
                    ->orderBy('first_name','ASC')
                    ->get();
        // dd($company_id);
        return $user_data;
    }
    public static function user_details_fetch($company_id)
    {
        $user_data = Person::join('person_details','person_details.person_id','=','person.id','inner')
                    ->join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    // ->join('salary_management','salary_management.user_id','=','person.id')
                    ->join('location_3','person.state_id','=','location_3.id')
                     ->select(DB::raw(" CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_details.created_on as created_on','person_details.address as personaddress','person.town_id as town_name','person.head_quater_id as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
                        WHERE p1.id=person.person_id_senior LIMIT 1) as srname"),'person_details.deleted_deactivated_on as deactivate_date','person_login.last_mobile_access_on as last_sync')
                    ->where('person.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person_status','=','1')
                    ->where('person.id','!=','1')
                    ->orderBy('first_name','ASC')
                    ->get();
        // dd($company_id);
        return $user_data;
    }
    public static function user_details_fetch_details($user_id,$company_id)
    {
        $user_data = Person::join('person_details','person_details.person_id','=','person.id','inner')
                    ->join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    ->join('location_3','person.state_id','=','location_3.id')
                     ->select('person.id as user_id',DB::raw(" CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_details.created_on as created_on','person_details.address as personaddress','person.town_id as town_name','person.head_quater_id as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
                        WHERE p1.id=person.person_id_senior LIMIT 1) as srname"),'person_details.deleted_deactivated_on as deactivate_date','person_login.last_mobile_access_on as last_sync')
                    ->where('person.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person.id',$user_id)
                    ->where('person_status','=','1')
                    ->where('person.id','!=','1')
                    ->orderBy('first_name','ASC')
                    ->get();
        $out_details = array();
        if(!empty($user_data)){
            foreach ($user_data as $key => $value) {
                // code...
                $mobile = !empty($value->mobile)?$value->mobile:'-';
                $out_details['contact_head_quar']= !empty($value->head_quar)?$mobile.'&'.$value->head_quar:$mobile;
                $out_details['user_id']= !empty($value->user_id)?$value->user_id:'';
                $out_details['user_name']= !empty($value->user_name)?$value->user_name:'';
                $out_details['created_on']= !empty($value->created_on)?$value->created_on:'';
                $out_details['personaddress']= !empty($value->personaddress)?$value->personaddress:'';
                $out_details['town_name']= !empty($value->town_name)?$value->town_name:'';
                $out_details['head_quater']= !empty($value->head_quater)?$value->head_quater:'';
                $out_details['id']= !empty($value->id)?$value->id:'';
                $out_details['position_id']= !empty($value->position_id)?$value->position_id:'';
                $out_details['first_name']= !empty($value->first_name)?$value->first_name:'';
                $out_details['middle_name']= !empty($value->middle_name)?$value->middle_name:'';
                $out_details['last_name']= !empty($value->last_name)?$value->last_name:'';
                $out_details['company_id']= !empty($value->company_id)?$value->company_id:'';
                $out_details['role_id']= !empty($value->role_id)?$value->role_id:'';
                $out_details['person_id_senior']= !empty($value->person_id_senior)?$value->person_id_senior:'';
                $out_details['mobile']= !empty($value->mobile)?$value->mobile:'';
                $out_details['email']= !empty($value->email)?$value->email:'';
                $out_details['imei_number']= !empty($value->imei_number)?$value->imei_number:'';
                $out_details['version_code_name']= !empty($value->version_code_name)?$value->version_code_name:'';
                $out_details['state_id']= !empty($value->state_id)?$value->state_id:'';
                $out_details['town_id']= !empty($value->town_id)?$value->town_id:'';
                $out_details['head_quater_id']= !empty($value->head_quater_id)?$value->head_quater_id:'';
                $out_details['weekly_off_data']= !empty($value->weekly_off_data)?$value->weekly_off_data:'';
                $out_details['manual_attendance']= !empty($value->manual_attendance)?$value->manual_attendance:'';
                $out_details['is_mtp_enabled']= !empty($value->is_mtp_enabled)?$value->is_mtp_enabled:'';
                $out_details['today_att_enabled']= !empty($value->today_att_enabled)?$value->today_att_enabled:'';
                $out_details['today_att_enabled_at']= !empty($value->today_att_enabled_at)?$value->today_att_enabled_at:'';
                $out_details['product_division']= !empty($value->product_division)?$value->product_division:'';
                $out_details['rate_list_flag']= !empty($value->rate_list_flag)?$value->rate_list_flag:'';
                $out_details['emp_code']= !empty($value->emp_code)?$value->emp_code:'';
                $out_details['joining_date']= !empty($value->joining_date)?$value->joining_date:'';
                $out_details['resigning_date']= !empty($value->resigning_date)?$value->resigning_date:'';
                $out_details['head_quar']= !empty($value->head_quar)?$value->head_quar:'';
                $out_details['status']= !empty($value->status)?$value->status:'';
                $out_details['created_by']= !empty($value->created_by)?$value->created_by:'';
                $out_details['region_txt']= !empty($value->region_txt)?$value->region_txt:'';
                $out_details['dms_token']= !empty($value->dms_token)?$value->dms_token:'';
                $out_details['is_holiday_enabled']= !empty($value->is_holiday_enabled)?$value->is_holiday_enabled:'';
                $out_details['is_holiday_updated_by']= !empty($value->is_holiday_updated_by)?$value->is_holiday_updated_by:'';
                $out_details['is_holiday_updated_at']= !empty($value->is_holiday_updated_at)?$value->is_holiday_updated_at:'';
                $out_details['fcm_token']= !empty($value->fcm_token)?$value->fcm_token:'';
                $out_details['env_flag']= !empty($value->env_flag)?$value->env_flag:'';
                $out_details['person_status']= !empty($value->person_status)?$value->person_status:'';
                $out_details['person_username']= !empty($value->person_username)?$value->person_username:'';
                $out_details['person_image']= !empty($value->person_image)?$value->person_image:'';
                $out_details['person_password']= !empty($value->person_password)?$value->person_password:'';
                $out_details['state']= !empty($value->state)?$value->state:'';
                $out_details['rolename']= !empty($value->rolename)?$value->rolename:'';
                $out_details['srname']= !empty($value->srname)?$value->srname:'';
                $out_details['deactivate_date']= !empty($value->deactivate_date)?$value->deactivate_date:'';
                $out_details['last_sync']= !empty($value->last_sync)?$value->last_sync:'';
                // $set_out = $out_details;
            }
        }
        
        // dd($company_id);
        return $out_details;
    }
    public static function getDistanceBetweentwopoint($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi')
    {
	     $theta = $longitude1 - $longitude2;
	     $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
	     $distance = acos($distance);
	     $distance = rad2deg($distance);
	     $distance = $distance * 60 * 1.1515; switch($unit) {
	          case 'Mi': break; case 'Km' : $distance = $distance * 1.609344;
	     }
	     return (round($distance,2));
	}
	public static function calculate_distance_matrix($lat1,$lat2)
	{
    //$url="https://maps.googleapis.com/maps/api/distancematrix/json?origins=28.56233,77.245977&destinations=28.6118655,77.3680864&mode=car&language=fr-FR";
    //$lat1='28.56233,77.245977';
 //    $lat2='28.6118655,77.3680864';
	// https://maps.googleapis.com/maps/api/distancematrix/json?origins=28.7129644,77.2772469&destinations=28.7098247%2077.2777547&mode=car&language=fr-FR&key=AIzaSyDRh65mFUb_M6gFDKnrBFJulYABgamRqE4
    $url="https://maps.googleapis.com/maps/api/distancematrix/json?origins=$lat1&destinations=$lat2&mode=car&language=fr-FR";
    $array=file_get_contents($url);
    $json=json_decode($array);
    // print_r($json);
    // dd($json);
    error_reporting(0);
    if(!empty($json))
    {
		    $data=$json->rows[0]->elements[0]->distance->value;
		    !empty($data)?$data:'';
    		$km=$data/1000;    	
    }
    else
    {
    	$km = 0;
    }

    
    return $km;
	}




     public static function checkReportJunior($role_id,$company_id,$is_admin)
    {

           
            $checkReportJuniorWise = DB::table('company_web_module_permission')
                                        ->where('company_web_module_permission.company_id',$company_id)
                                        ->where('role_id',$role_id)
                                        ->where('module_id','=','33')
                                        ->first();

            if($is_admin != 1){
                if(!empty($checkReportJuniorWise)){
                    if($checkReportJuniorWise->without_junior == 1){
                        $without_junior = '1'; // 1 means report run junior wise
                    }
                    else{
                        $without_junior = '0'; // 0 means report run without junior wise like admin
                    }
                }else{
                        $without_junior = '1'; // 1 means report run junior wise
                }

            }else{
                $without_junior = '0'; // 0 means report run without junior wise like admin
            }
            // $this->without_junior=$without_junior;

        return $without_junior;
    }


}
