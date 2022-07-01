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

class WebRoleAssigningController extends Controller
{
	public function index( Request $request)
	{ 
		$company_id = Auth::user()->company_id;
		$records = array();
		$web_module = [];
		$web_sub_module = [];
		$web_sub_sub_module = [];
		$flag = !empty($request->flag)?$request->flag:'';
		$module_bucket_query = [];
		$sub_web_module_bucket_query = [];
		$sub_sub_web_module_bucket_query = [];

		$web_sub_sub_module_add = [];
		$web_sub_sub_module_edit = [];
		$web_sub_sub_module_delete = [];
		$web_sub_module_add = [];
		$web_sub_module_edit = [];
		$web_sub_module_delete = [];
		$web_module_delete = [];
		$web_module_edit = [];
		$web_module_add = [];
		$web_module_junior_status = [];


		$role_id = $request->role_id;
		$mlsm1 = [];
		$check  = [];
		$check_2 = [];
		if($flag==1)
		{

			# first layer checked part starts here 
			$web_module  = DB::table('modules_bucket')
							->join('web_module','web_module.module_id','=','modules_bucket.id')
							->join('company_web_module_permission','company_web_module_permission.module_id','=','modules_bucket.id')
							->where('modules_bucket.status',1)
							->where('web_module.company_id',$company_id)
							->where('company_web_module_permission.role_id',$role_id)
							->pluck('modules_bucket.id')->toArray();

			$web_module_add  = DB::table('modules_bucket')
							->join('web_module','web_module.module_id','=','modules_bucket.id')
							->join('company_web_module_permission','company_web_module_permission.module_id','=','modules_bucket.id')
							->where('modules_bucket.status',1)
							->where('web_module.company_id',$company_id)
							->where('company_web_module_permission.company_id',$company_id)
							->where('company_web_module_permission.role_id',$role_id)
							->where('company_web_module_permission.add_status',1)
							->pluck('modules_bucket.id')->toArray();

			$web_module_edit  = DB::table('modules_bucket')
							->join('web_module','web_module.module_id','=','modules_bucket.id')
							->join('company_web_module_permission','company_web_module_permission.module_id','=','modules_bucket.id')
							->where('modules_bucket.status',1)
							->where('web_module.company_id',$company_id)
							->where('company_web_module_permission.company_id',$company_id)
							->where('company_web_module_permission.edit_status',1)
							->pluck('modules_bucket.id')->toArray();
				// dd($web_module_edit);

			$web_module_delete  = DB::table('modules_bucket')
							->join('web_module','web_module.module_id','=','modules_bucket.id')
							->join('company_web_module_permission','company_web_module_permission.module_id','=','modules_bucket.id')
							->where('modules_bucket.status',1)
							->where('web_module.company_id',$company_id)
							->where('company_web_module_permission.company_id',$company_id)
							->where('company_web_module_permission.delete_status',1)
							->pluck('modules_bucket.id')->toArray();

			$web_module_junior_status  = DB::table('modules_bucket')
							->join('web_module','web_module.module_id','=','modules_bucket.id')
							->join('company_web_module_permission','company_web_module_permission.module_id','=','modules_bucket.id')
							->where('modules_bucket.status',1)
							->where('web_module.company_id',$company_id)
							->where('company_web_module_permission.company_id',$company_id)
							->where('company_web_module_permission.without_junior',1)
							->pluck('modules_bucket.id')->toArray();


			# first layer checked part ends here 

			# second layer checked part starts here 
			$web_sub_module  = DB::table('sub_web_module_bucket')
					->join('sub_web_module','sub_web_module.sub_module_id','=','sub_web_module_bucket.id')
					->join('company_sub_web_module_permission','company_sub_web_module_permission.sub_module_id','=','sub_web_module_bucket.id')
					->where('sub_web_module.company_id',$company_id)
					->where('company_sub_web_module_permission.company_id',$company_id)
					->where('company_sub_web_module_permission.role_id',$role_id)
					->where('sub_web_module_bucket.status',1)
					->groupBy('sub_web_module_bucket.id')
					->pluck('sub_web_module_bucket.id')->toArray();
		// dd($web_sub_module);

			$web_sub_module_add  = DB::table('sub_web_module_bucket')
								->join('sub_web_module','sub_web_module.sub_module_id','=','sub_web_module_bucket.id')
								->join('company_sub_web_module_permission','company_sub_web_module_permission.sub_module_id','=','sub_web_module_bucket.id')
								->where('sub_web_module.company_id',$company_id)
								->where('company_sub_web_module_permission.company_id',$company_id)
								->where('company_sub_web_module_permission.role_id',$role_id)
								->where('company_sub_web_module_permission.add_status',1)
								->where('sub_web_module_bucket.status',1)
								->pluck('sub_web_module_bucket.id')->toArray();

			$web_sub_module_edit  = DB::table('sub_web_module_bucket')
								->join('sub_web_module','sub_web_module.sub_module_id','=','sub_web_module_bucket.id')
								->join('company_sub_web_module_permission','company_sub_web_module_permission.sub_module_id','=','sub_web_module_bucket.id')
								->where('sub_web_module.company_id',$company_id)
								->where('company_sub_web_module_permission.company_id',$company_id)
								->where('company_sub_web_module_permission.role_id',$role_id)
								->where('company_sub_web_module_permission.edit_status',1)
								->where('sub_web_module_bucket.status',1)
								->pluck('sub_web_module_bucket.id')->toArray();
				// dd($web_module_edit);

			$web_sub_module_delete  = DB::table('sub_web_module_bucket')
								->join('sub_web_module','sub_web_module.sub_module_id','=','sub_web_module_bucket.id')
								->join('company_sub_web_module_permission','company_sub_web_module_permission.sub_module_id','=','sub_web_module_bucket.id')
								->where('sub_web_module.company_id',$company_id)
								->where('company_sub_web_module_permission.company_id',$company_id)
								->where('company_sub_web_module_permission.role_id',$role_id)
								->where('company_sub_web_module_permission.delete_status',1)
								->where('sub_web_module_bucket.status',1)
								->pluck('sub_web_module_bucket.id')->toArray();


			# second layer checked part ends here 


			# third layer checked part starts here 
			$web_sub_sub_module  = DB::table('sub_sub_web_module_bucket')
								->join('sub_sub_web_module','sub_sub_web_module.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
								->join('company_sub_sub_web_module_permission','company_sub_sub_web_module_permission.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
								->where('sub_sub_web_module.company_id',$company_id)
								->where('company_sub_sub_web_module_permission.company_id',$company_id)
								->where('company_sub_sub_web_module_permission.role_id',$role_id)
								->where('sub_sub_web_module_bucket.status',1)
								// ->where('company_sub_sub_web_module_permission.add_status',1)
								->where('sub_sub_web_module_bucket.ready_status',1)
								->pluck('sub_sub_web_module_bucket.id')->toArray();
		// dd($web_sub_module);

			$web_sub_sub_module_add  = DB::table('sub_sub_web_module_bucket')
									->join('sub_sub_web_module','sub_sub_web_module.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
									->join('company_sub_sub_web_module_permission','company_sub_sub_web_module_permission.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
									->where('sub_sub_web_module.company_id',$company_id)
									->where('company_sub_sub_web_module_permission.company_id',$company_id)
									->where('company_sub_sub_web_module_permission.role_id',$role_id)
									->where('sub_sub_web_module_bucket.status',1)
									->where('company_sub_sub_web_module_permission.add_status',1)
									->where('sub_sub_web_module_bucket.ready_status',1)
									->pluck('sub_sub_web_module_bucket.id')->toArray();

			$web_sub_sub_module_edit  = DB::table('sub_sub_web_module_bucket')
									->join('sub_sub_web_module','sub_sub_web_module.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
									->join('company_sub_sub_web_module_permission','company_sub_sub_web_module_permission.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
									->where('sub_sub_web_module.company_id',$company_id)
									->where('company_sub_sub_web_module_permission.company_id',$company_id)
									->where('company_sub_sub_web_module_permission.role_id',$role_id)
									->where('sub_sub_web_module_bucket.status',1)
									->where('company_sub_sub_web_module_permission.edit_status',1)
									->where('sub_sub_web_module_bucket.ready_status',1)
									->pluck('sub_sub_web_module_bucket.id')->toArray();
				// dd($web_module_edit);

			$web_sub_sub_module_delete  = DB::table('sub_sub_web_module_bucket')
									->join('sub_sub_web_module','sub_sub_web_module.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
									->join('company_sub_sub_web_module_permission','company_sub_sub_web_module_permission.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
									->where('sub_sub_web_module.company_id',$company_id)
									->where('company_sub_sub_web_module_permission.company_id',$company_id)
									->where('company_sub_sub_web_module_permission.role_id',$role_id)
									->where('sub_sub_web_module_bucket.status',1)
									->where('company_sub_sub_web_module_permission.delete_status',1)
									->where('sub_sub_web_module_bucket.ready_status',1)
									->pluck('sub_sub_web_module_bucket.id')->toArray();
				// dd($web_module_edit);


			# third layer checked part ends here 


			$module_bucket_query  = DB::table('modules_bucket')
							->join('web_module','web_module.module_id','=','modules_bucket.id')
							->where('modules_bucket.status',1)
							->where('web_module.company_id',$company_id)
							->pluck('modules_bucket.name','modules_bucket.id');


			$sub_web_module_bucket_query  = DB::table('sub_web_module_bucket')
											->join('sub_web_module','sub_web_module.sub_module_id','=','sub_web_module_bucket.id')
											->select('sub_web_module_bucket.*')
											->where('sub_web_module.company_id',$company_id)
											->where('sub_web_module_bucket.status',1)
											->get();
											// dd($sub_web_module_bucket_query);
			$check  = DB::table('sub_web_module_bucket')
					->join('sub_web_module','sub_web_module.sub_module_id','=','sub_web_module_bucket.id')
					->where('sub_web_module.company_id',$company_id)
					->where('sub_web_module_bucket.status',1)
					->pluck('sub_web_module_bucket.sub_module_name','sub_web_module_bucket.module_id');

			$sub_sub_web_module_bucket_query = DB::table('sub_sub_web_module_bucket')
										->join('sub_sub_web_module','sub_sub_web_module.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
										->select('sub_sub_web_module_bucket.*')
										->where('sub_sub_web_module.company_id',$company_id)
										->where('sub_sub_web_module_bucket.status',1)
										->where('sub_sub_web_module_bucket.ready_status',1)
										->get();
							// dd($sub_sub_web_module_bucket_query);
			$check_2 = DB::table('sub_sub_web_module_bucket')
					->join('sub_sub_web_module','sub_sub_web_module.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
					->where('sub_sub_web_module.company_id',$company_id)
					->where('sub_sub_web_module_bucket.status',1)
					->where('sub_sub_web_module_bucket.ready_status',1)
					->pluck('sub_sub_web_module_bucket.sub_module_name','sub_sub_web_module_bucket.sub_web_module_id');
// dd($check_2);
			$role_query = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
		
			

		}
		else
		{
			$role_query = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
		}
		
		

		return view('webroleassign.WebRoleIndex',[

				'module_bucket_query' => $module_bucket_query,
				'sub_web_module_bucket_query' => $sub_web_module_bucket_query,
				'sub_sub_web_module_bucket_query' => $sub_sub_web_module_bucket_query,
				'role' => $role_query,
				'records'=> $records,
				'flag'=> $flag,
				'check'=> $check,
				'check_2'=>$check_2,
				'role_id'=>$role_id,
				'web_sub_sub_module'=>$web_sub_sub_module,
				'web_sub_sub_module_add'=>$web_sub_sub_module_add,
				'web_sub_sub_module_edit'=>$web_sub_sub_module_edit,
				'web_sub_sub_module_delete'=>$web_sub_sub_module_delete,

				'web_sub_module'=> $web_sub_module,
				'web_sub_module_add'=> $web_sub_module_add,
				'web_sub_module_edit'=> $web_sub_module_edit,
				'web_sub_module_delete'=> $web_sub_module_delete,

				'web_module'=> $web_module,
				'web_module_delete'=> $web_module_delete,
				'web_module_edit'=> $web_module_edit,
				'web_module_add'=> $web_module_add,
				'web_module_junior_status'=> $web_module_junior_status,


			]);

	}
	public function SubmitWebRoleAssigning(Request $request)
	{
		// dd($request);
		$company_id = Auth::user()->company_id;
		$role_id = $request->role_id;
		$module_id = $request->module_id;
		$sub_module_id = $request->sub_module_id;
		$sub_sub_module_id = $request->sub_sub_module_id;

		$add = $request->add;
		$edit = $request->edit;
		$delete = $request->delete;

		$add_sub = $request->add_sub;
		$edit_sub = $request->edit_sub;
		$delete_sub = $request->delete_sub;

		$add_sub_sub = $request->add_sub_sub;
		$edit_sub_sub = $request->edit_sub_sub;
		$delete_sub_sub = $request->delete_sub_sub;
		$junior_status = $request->junior_status;

		// dd($sub_sub_module_name);
		// $break_two_module = implode('-', $sub_sub_module_id);
		// dd($break_two_module);
		$insert_web_module = 0;
		$insert_web_sub_module = 0;
		$insert_web_sub_sub_module = 0;
		$new_sub_module_data = array();
		$fSubModuleArr = array();
        DB::beginTransaction();

		$delete_web_module = DB::table('company_web_module_permission')->where('company_id',$company_id)->where('role_id',$role_id)->delete();
		foreach ($module_id as $m_key => $m_value) 
		{
			// dd($module_id)
			$module_name = DB::table('modules_bucket')
						->select('name')
						->where('id',$m_value)
						->first();

			// $explode_add = explode('|', $add[$key])

			$moduleArr = 
			[
				'module_id'=> $m_value,
				'without_junior'=> !empty($junior_status[$m_value])?$junior_status[$m_value]:'0',
				'add_status'=>!empty($add[$m_value])?$add[$m_value]:'0',
				'edit_status'=>!empty($edit[$m_value])?$edit[$m_value]:'0',
				'delete_status'=>!empty($delete[$m_value])?$delete[$m_value]:'0',
				'role_id' => $role_id,
				'created_at'=> date('Y-m-d H:i:s'),
				'company_id'=>$company_id,
				'title'=>$module_name->name,
				'sequence'=>$request->sequence[$m_key],
				'status' => 1

			]; 
			$fModuleArr[]=$moduleArr;

		}
		$insert_web_module = DB::table('company_web_module_permission')->insert($fModuleArr); 

		$delete_web_sub_module = DB::table('company_sub_web_module_permission')->where('company_id',$company_id)->where('role_id',$role_id)->delete();
		if(!empty($sub_module_id))
		{


			foreach ($sub_module_id as $s_key => $s_value) 
			{
				$sub_module_name = DB::table('sub_web_module_bucket')
							->select('sub_module_name as name')
							->where('id',$s_value)
							->first();
				$subModuleArr = 
				[
					'sub_module_id'=> $s_value,
					'add_status'=>!empty($add_sub[$s_value])?$add_sub[$s_value]:'0',
					'edit_status'=>!empty($edit_sub[$s_value])?$edit_sub[$s_value]:'0',
					'delete_status'=>!empty($delete_sub[$s_value])?$delete_sub[$s_value]:'0',
					'role_id' => $role_id,
					'created_at'=> date('Y-m-d H:i:s'),
					'title'=>$sub_module_name->name,
					'company_id'=>$company_id,
					'status' => 1

				]; 
				$fSubModuleArr[]=$subModuleArr;
			}
		}
		$delete_web_sub_sub_module = DB::table('company_sub_sub_web_module_permission')->where('company_id',$company_id)->where('role_id',$role_id)->delete();
		
		if(!empty($sub_sub_module_id))
		{

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
					'add_status'=>!empty($add_sub_sub[$sub_sub_module_id_new])?$add_sub_sub[$sub_sub_module_id_new]:'0',
					'edit_status'=>!empty($edit_sub_sub[$sub_sub_module_id_new])?$edit_sub_sub[$sub_sub_module_id_new]:'0',
					'delete_status'=>!empty($delete_sub_sub[$sub_sub_module_id_new])?$delete_sub_sub[$sub_sub_module_id_new]:'0',
					'role_id' => $role_id,
					'created_at'=> date('Y-m-d H:i:s'),
					'title'=>$sub_sub_module_name->name,
					'company_id'=>$company_id,
					'status' => 1

				]; 
				$fSubSubModuleArr[]=$subSubModuleArr;
			}
			$insert_web_sub_sub_module = DB::table('company_sub_sub_web_module_permission')->insert($fSubSubModuleArr); 
		}
		if(!empty($remaining_sub_module_id))
		{
			foreach (array_unique($remaining_sub_module_id) as $key => $value) 
			{
				$sub_module_name = DB::table('sub_web_module_bucket')
							->select('sub_module_name as name')
							->where('id',$value)
							->first();
				$remaining_sub_module_id_new = 
				[
					'sub_module_id'=>  $value,
					'add_status'=>!empty($add_sub[$value])?$add_sub[$value]:'0',
					'edit_status'=>!empty($edit_sub[$value])?$edit_sub[$value]:'0',
					'delete_status'=>!empty($delete_sub[$value])?$delete_sub[$value]:'0',
					'role_id' => $role_id,
					'created_at'=> date('Y-m-d H:i:s'),
					'title'=>$sub_module_name->name,
					'company_id'=>$company_id,
					'status' => 1
				];
				$new_sub_module_data[] = $remaining_sub_module_id_new;
			}
		}
		// dd();
		$new_sub_module = array_merge(($new_sub_module_data),$fSubModuleArr);
		if(!empty($new_sub_module))
		{
			$insert_web_sub_module = DB::table('company_sub_web_module_permission')->insert($new_sub_module); 
		}

		if( $insert_web_module || $insert_web_sub_module || $insert_web_sub_sub_module )
		{
			DB::commit();
			Session::flash('message', "Module Assign successfully");
	       Session::flash('alert-class', 'alert-success');
	       return redirect()->intended('webRoleAssigning');
		}
		else
		{
			DB::rollback();
			Session::flash('message', "Something Went Wrong");
	       	Session::flash('alert-class', 'alert-success');
	       	return redirect()->intended('webRoleAssigning');
		}
	}
}