<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\_role;
use App\User;
use App\PersonDetail;
use App\PersonLogin;
use App\Person;
use App\Url;
use App\Version;
use DB;
use Auth;
use Session;
use DateTime;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;



class CompanyController extends Controller
{
	public function index(Request $request)
	{
		if(Auth::user()->api_token == '5767')
		{
			$user_id = Auth::user()->id;
			$company_query = Company::join('users','users.company_id','=','company.id')
						->select('company.*','users.email as user_name','users.original_pass as original_pass')
						->where('is_admin',1)->where('company.status','!=',9)->where('created_by',$user_id)->get();
		}
		else
		{
			$company_query = Company::join('users','users.company_id','=','company.id')
						->select('company.*','users.email as user_name','users.original_pass as original_pass')
						->where('is_admin',1)->where('company.status','!=',9)->get();
		}

		
		return view('company.index',['company'=>$company_query]);
	}
	public function edit($id)
	{
		$cid = Crypt::decryptString($id);
		$company_query = Company::join('users','users.company_id','=','company.id')
						->select('company.*','users.email as user_name','users.original_pass as pass','users.id as user_id')
						->where('company.status','!=',9)
						->where('is_admin',1)
						->where('company.id',$cid)
						->first();
		// $user_details = User::where('status','!=',9)->where('id',$cid)->first();
		// dd($user_details);
		return view('company.edit',['company'=>$company_query,'encrypt_id'=>$id]);
	}
	public function create()
	{
		$company_query = Company::where('status','!=',9)->get();
		return view('company.create',['company'=>$company_query]);
	}
	public function store(Request $request)
	{
		// dd($request);
		$validate = $request->validate([
            'company_name' => 'required',
            'title' => 'required',
            'website' => 'required',
            'user_name' => 'required',
            'password' => 'required',
		]);
		DB::beginTransaction();		

		if(Auth::user()->api_token == '5767')
		{
			$message = " developed and powered by Aeris Communications India Pvt. Ltd";
			$message_link = "https://www.aeris.com/in/";
			$domain_url = "www.aeris-es.com";
		}
		else
		{
			$message = " mSELL, developed and powered by Manacle Technologies Pvt. Ltd";
			$message_link = "http://manacleindia.com";
			$domain_url = "demo.msell.in";
		}
		$myArr = [
			'name'=> trim(strtolower($request->company_name)),
			'title'=> $request->title,
			'website'=> $request->website,
			'status'=> $request->status,
			'email'=> $request->email,
			'address'=> $request->address,
			'other_numbers'=> $request->number,
			'contact_per_name'=>$request->contact_per_name,
			'domain_url'=>$domain_url,
			'landline'=> $request->landline,
			'footer_message'=>$message,
			'footer_link'=>$message_link,
			'created_by'=>Auth::user()->id,
			'created_at'=> date('Y-m-d H:i:s'),

		];
		$insert_query = Company::create($myArr);

		if(Auth::user()->api_token == '5767')
		{
			$url = Url::create([
	            'company_id' => $insert_query->id,
	            'signin_url' => 'login_demo',
	            'sync_post_url' => 'sync_post_v35.php',
	            'image_url'=>'image_sync',
	            'version_code' => '7.0.4',
	            'created_at' => date('Y-m-d H:i:s'),
	            'updated_at' => date('Y-m-d H:i:s'),
	            

	        ]);

	        $version = Version::create([
	            'company_id' => $insert_query->id,
	            // 'company_id' => $company_id,
	            'version_name' => '7.0.4',
	            'version_code' => '31',
	            'created_at' => date('Y-m-d H:i:s'),
	            'updated_at' => date('Y-m-d H:i:s'),

	        ]);
		}

		$role = _role::create(['company_id'=>$insert_query->id,'rolename'=>$request->role,'senior_role_id'=>'0','created_at'=>date('Y-m-d H:i:s')]);
		$userArr = 
		[
			'email'=>str_replace('@','_',$request->user_name).'@'.$request->company_name,
			'company_id'=>$insert_query->id,
			'is_admin'=>1,
            'original_pass'=>$request->password,
			'password'=>bcrypt($request->password),
			'role_id'=>$role->id,
			'status'=>1,
			'created_at'=>date('Y-m-d H:i:s'),
		];
		$user = User::create($userArr);

		$personArr = [
			'id'=>$user->id,
            'first_name' => trim($request->user_name),
            'last_name' => trim($request->user_name),
            'role_id' => trim($role->id),
            'person_id_senior' => trim($request->senior_person),
            'version_code_name' => '',
            'resigning_date' => date('Y-m-d'),
            'head_quar' => 'NA',
            'mobile' => trim($request->number),
            'email' => trim($request->email),
            'state_id' => 0,
            'emp_code' => 01,
            'company_id' => $insert_query->id,
			'joining_date' => date('Y-m-d'),
            'status' => 1,
		];
		$person=Person::create($personArr);

		$personLogArr=[
            'person_id'=>$user->id,
            'address'=>trim($request->address),
            'company_id' => $insert_query->id,
            'gender'=>'M',
            'created_on'=>date('Y-m-d H:i:s'),
        ];
        $person_log=PersonDetail::create($personLogArr);

		$person_login_arr=[
            'person_id'=>$user->id,
            'emp_id'=>'01',
            'company_id' => $insert_query->id,
            'person_username'=>trim(str_replace('@','_',$request->user_name).'@'.$request->company_name),
            'person_password'=>DB::raw("AES_ENCRYPT('".trim($request->password)."', '".Lang::get('common.db_salt')."')"),
            'person_status'=>1,
        ];
        $person_login=PersonLogin::create($person_login_arr);
		//you can use this code for upload image to `public/storage/ directory :

			if ($request->hasFile('imageFile')) {
		
				if($request->file('imageFile')->isValid()) {
					try {
						$file = $request->file('imageFile');
						$name = date('YmdHis') . '.' . $file->getClientOriginalExtension();
			
						# save to DB
						$companyImage = Company::where('id',$insert_query->id)->update(['company_image' => 'company-profile/'.$name]);
			
						$request->file('imageFile')->move("company-profile", $name);
					} catch (Illuminate\Filesystem\FileNotFoundException $e) {
			
					}
				}
			}


		if($insert_query && $user && $role && $person && $person_log && $person_login)
		{
			DB::commit();
			Session::flash('message', Lang::get('common.company').' created successfully');
            Session::flash('class', 'success');
		}
		else
		{
			DB::rollback();
			Session::flash('message', Lang::get('common.company').'Please try again later!');
            Session::flash('class', 'danger');
		}
		
        return redirect()->intended('company');


	}
	public function update(Request $request, $id)
	{
		// dd($request);
        $uid = Crypt::decryptString($id);

        $validate = $request->validate([
           	'company_name' => 'required',
			'title' => 'required',
			'website' => 'required',
        ]);

        $myArr = [
			'name'=> $request->company_name,
			'title'=> $request->title,
			'website'=> $request->website,
			'status'=> $request->status,
			'email'=> $request->email,
			'other_numbers'=> $request->number,
			'landline'=> $request->landline,
			'updated_at'=> date('Y-m-d H:i:s'),
			'contact_per_name' => $request->contact_per_name,
			'message_dynamic' => $request->message_dynamic,
			'manual_on_off_forcefully'=> !empty($request->manual_on_off_forcefully)?$request->manual_on_off_forcefully:'2',
			'address'=> $request->address,

		];
		$update_query = Company::where('id',$uid)->update($myArr);

		$userArr = 
		[
			'email'=>str_replace('@','_',$request->user_name).'@'.$request->company_name,
			
            'original_pass'=>$request->password,
			'password'=>bcrypt($request->password),
			
			'status'=>1,
			'updated_at'=>date('Y-m-d H:i:s'),
		];
		$user = User::where('id',$request->user_id)->update($userArr);

			//you can use this code for upload image to `public/storage/ directory :

				if ($request->hasFile('imageFile')) {
					if($request->file('imageFile')->isValid()) {
						try {
							$file = $request->file('imageFile');
							$name = date('YmdHis') . '.' . $file->getClientOriginalExtension();
				
							# save to DB
							$companyImage = Company::where('id',$uid)->update(['company_image' => 'company-profile/'.$name]);
				
							$request->file('imageFile')->move("company-profile", $name);
						} catch (Illuminate\Filesystem\FileNotFoundException $e) {
				
						}
					}
				}

		if($update_query)
		{
			Session::flash('message', Lang::get('common.company').' created successfully');
            Session::flash('class', 'success');
		}
		else
		{
			Session::flash('message', Lang::get('common.company').'Please try again later!');
            Session::flash('class', 'danger');
		}
        return redirect()->intended('company');

	}
}
