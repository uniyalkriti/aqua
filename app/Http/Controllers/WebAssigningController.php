<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\Company;
use App\JuniorData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use DB;
use Image;

class WebAssigningController extends Controller
{
	public function index( Request $request)
	{ 
		// dd(Auth::user()->api_token);
		$records = array();
		$web_module = [];
		$web_sub_module = [];
		$web_sub_sub_module = [];
		$flag = !empty($request->flag)?$request->flag:'';
		$module_bucket_query = [];
		$sub_web_module_bucket_query = [];
		$sub_sub_web_module_bucket_query = [];
		$company_id = $request->company_id;
		$mlsm1 = [];
		$check  = [];
		$check_2 = [];
		if($flag==1)
		{
			$module_bucket_query  = DB::table('modules_bucket')
							->where('status',1)
							->pluck('name','id');

			$sub_web_module_bucket_query  = DB::table('sub_web_module_bucket')
											->where('status',1)
											->get();
			$check  = DB::table('sub_web_module_bucket')
					->where('status',1)
					->pluck('sub_module_name','module_id');

			$sub_sub_web_module_bucket_query = DB::table('sub_sub_web_module_bucket')
										->where('status',1)
										->get();
			$check_2 = DB::table('sub_sub_web_module_bucket')
					->where('status',1)
					->pluck('sub_module_name','sub_web_module_id');

			if(Auth::user()->api_token == '5767')
			{
				$user_id = Auth::user()->id;
				$company_query = Company::where('status',1)->where('created_by',$user_id)->pluck('name','id');
			}
			else
			{
				$company_query = Company::where('status',1)->pluck('name','id');
			}	
			if(!empty($company_id))
			{
				$web_module = DB::table('web_module')->where('company_id',$company_id)->where('status',1)->pluck('module_id')->toArray();
				$web_sub_module = DB::table('sub_web_module')->where('company_id',$company_id)->where('status',1)->pluck('sub_module_id')->toArray();
				$web_sub_sub_module = DB::table('sub_sub_web_module')->where('company_id',$company_id)->where('status',1)->pluck('sub_sub_module_id')->toArray();
			}
			

		}
		else
		{
			if(Auth::user()->api_token == '5767')
			{
				$user_id = Auth::user()->id;
				$company_query = Company::where('status',1)->where('created_by',$user_id)->pluck('name','id');
			}
			else
			{
				$company_query = Company::where('status',1)->pluck('name','id');
			}	
		}
		
		

		return view('assign.WebIndex',[

				'module_bucket_query' => $module_bucket_query,
				'sub_web_module_bucket_query' => $sub_web_module_bucket_query,
				'sub_sub_web_module_bucket_query' => $sub_sub_web_module_bucket_query,
				'company' => $company_query,
				'records'=> $records,
				'flag'=> $flag,
				'check'=> $check,
				'check_2'=>$check_2,
				'company_id'=>$company_id,
				'web_sub_sub_module'=>$web_sub_sub_module,
				'web_sub_module'=> $web_sub_module,
				'web_module'=> $web_module,


			]);

	}
	public function SubmitWebAssigning(Request $request)
	{
		// dd($request);
		$company_id = $request->company_id;
		$module_id = $request->module_id;
		$sub_module_id = $request->sub_module_id;
		$sub_sub_module_id = $request->sub_sub_module_id;
		// dd($sub_sub_module_name);
		// $break_two_module = implode('-', $sub_sub_module_id);
		// dd($break_two_module);
		
        DB::beginTransaction();

		$delete_web_module = DB::table('web_module')->where('company_id',$company_id)->delete();
		foreach ($module_id as $m_key => $m_value) 
		{
			$module_name = DB::table('modules_bucket')
						->select('name')
						->where('id',$m_value)
						->first();
			$moduleArr = 
			[
				'module_id'=> $m_value,
				'created_at'=> date('Y-m-d H:i:s'),
				'company_id'=>$company_id,
				'title'=>$module_name->name,
				'sequence'=>$request->sequence[$m_key],
				'status' => 1

			]; 
			$fModuleArr[]=$moduleArr;
		}
		$delete_web_sub_module = DB::table('sub_web_module')->where('company_id',$company_id)->delete();
		foreach ($sub_module_id as $s_key => $s_value) 
		{
			$sub_module_name = DB::table('sub_web_module_bucket')
						->select('sub_module_name as name')
						->where('id',$s_value)
						->first();
			$subModuleArr = 
			[
				'sub_module_id'=> $s_value,
				'created_at'=> date('Y-m-d H:i:s'),
				'title'=>$sub_module_name->name,
				'company_id'=>$company_id,
				'status' => 1

			]; 
			$fSubModuleArr[]=$subModuleArr;
		}
		$delete_web_sub_sub_module = DB::table('sub_sub_web_module')->where('company_id',$company_id)->delete();
		foreach ($sub_sub_module_id as $ss_key => $ss_value) 
		{
			// dd($ss_value);
			$break_remaining_sub_module_id = explode('-',$ss_value);
			// dd($remaining_sub_module_id);
			$sub_sub_module_id_new = $break_remaining_sub_module_id[1]; 

			$remaining_sub_module_id[] = $break_remaining_sub_module_id[0];

			$sub_sub_module_name = DB::table('sub_sub_web_module_bucket')
						->select('sub_module_name as name')
						->where('id',$sub_sub_module_id_new)
						->first();
			$subSubModuleArr = 
			[
				'sub_sub_module_id'=> $sub_sub_module_id_new,
				'created_at'=> date('Y-m-d H:i:s'),
				'title'=>$sub_sub_module_name->name,
				'company_id'=>$company_id,
				'status' => 1

			]; 
			$fSubSubModuleArr[]=$subSubModuleArr;
		}
		foreach (array_unique($remaining_sub_module_id) as $key => $value) 
		{
			$sub_module_name = DB::table('sub_web_module_bucket')
						->select('sub_module_name as name')
						->where('id',$value)
						->first();
			$remaining_sub_module_id_new = 
			[
				'sub_module_id'=>  $value,
				'created_at'=> date('Y-m-d H:i:s'),
				'title'=>$sub_module_name->name,
				'company_id'=>$company_id,
				'status' => 1
			];
			$new_sub_module_data[] = $remaining_sub_module_id_new;
		}
		// dd();
		$new_sub_module = array_merge(($new_sub_module_data),$fSubModuleArr);
		$insert_web_module = DB::table('web_module')->insert($fModuleArr); 
		$insert_web_sub_module = DB::table('sub_web_module')->insert($new_sub_module); 
		$insert_web_sub_sub_module = DB::table('sub_sub_web_module')->insert($fSubSubModuleArr); 

		if( $insert_web_module & $insert_web_sub_module & $insert_web_sub_sub_module )
		{
			DB::commit();
			Session::flash('message', "Module Assign successfully");
	       Session::flash('alert-class', 'alert-success');
	       return redirect()->intended('webAssigning');
		}
		else
		{
			DB::rollback();
		}
	}
}