<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\UserDetail;
use App\Company;
use App\JuniorData;
use App\Retailer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\UserIncentiveDetails;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use Validator;
use DB;
use Image;

class SyncImagesControllers extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

    ####################for all type of images sync here ######
    public function image_sync(Request $request)
    {
    	// return response()->json(['response'=>True,'data'=>$_POST]);	
    	$validator=Validator::make($request->all(),[
	
          'image_name'=>'required',
          'order_id'=>'required',
          'user_id'=>'required',
          'company_id'=>'required',
          'module_id'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
		}
    	$image_name = $request->image_name;
    	$order_id = $request->order_id;
    	$user_id = $request->user_id;
    	$company_id = $request->company_id;
    	$module_id = $request->module_id;
    	

        if($module_id == 1) // for attendance
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
             	$str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/attendance_images/' . $imageName);

	           

	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image_name' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];


	        $update_image_attendance = DB::table('user_daily_attendance')
	        						->where('order_id',$order_id.$user_id)
	        						->where('user_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Attendance Image Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}

        }
        if($module_id == 2) // for dealer damage
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
	            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/dealer_damge_image/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('damage_replace')
	        						->where('replaceid',$order_id)
	        						->where('user_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Dealer Damge Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}

        }
        if($module_id == 3) // for retailer damage
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
	            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/retailer_damge_image/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('damage_replace_retailer')
	        						->where('replaceid',$order_id)
	        						->where('user_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Retailer Damage Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}

        }
        if($module_id == 4) // for dealer payment
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
	            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/dealer_payment/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('dealer_payments')
	        						->where('order_id',$order_id)
	        						->where('user_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Dealer Payment Image Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}

        }
        if($module_id == 5) // for expense
        {
        	if($request->hasFile('image_source'))
        	{
        		$files = $request->file('image_source');
		        $inc = 0;
		        foreach($files as $file)
	            {
	            	$name_random = date('YmdHis').$inc;
	                $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
	        		$random_no = substr($str, 0,2);  // return always a new string 
	        		$custom_image_name = date('YmdHis').$random_no.$user_id;
		            $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
	                $file_name[] = $imageName;
	                $destinationPath = public_path('/expense_image/');
	                $file->move($destinationPath , $imageName);
	                $inc++;
	                
	            }

            	$update_attendance_arr = [
	        	'image_name1' => !empty($file_name[0])?$file_name[0]:'',
	        	'image_name2' => !empty($file_name[1])?$file_name[1]:'',
	        	'image_name3' => !empty($file_name[2])?$file_name[2]:'',
	        	'updated_at' => date('Y-m-d H:i:s'),	
		        ];
		        $update_image_attendance = DB::table('travelling_expense_bill')
		        						->where('order_id',$order_id)
		        						->where('user_id',$user_id)
		        						->where('company_id',$company_id)
		        						->update($update_attendance_arr);
        	}
        	else
        	{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

        	}
	        
	       

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Expense Images Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}

        }

        if($module_id == 6) // order images
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
	            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/order_booking_image/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image_name' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('user_sales_order')
	        						->where('order_id',$order_id.$user_id)
	        						->where('user_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Order Booking Image Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}
        }

        if($module_id == 7) // retailer comment image
        {
        	if($request->hasFile('image_source'))
        	{
        		$files = $request->file('image_source');
		        $inc = 0;
		        foreach($files as $file)
	            {
	            	$name_random = date('YmdHis').$inc;
	                $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
	        		$random_no = substr($str, 0,2);  // return always a new string 
	        		$custom_image_name = date('YmdHis').$random_no.$user_id;
		            $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
	                $file_name[] = $imageName;
	                $destinationPath = public_path('/retailer_comment_images/');
	                $file->move($destinationPath , $imageName);
	                $inc++;
	                
	            }

            	$update_attendance_arr = [
	        	'image_name1' => !empty($file_name[0])?$file_name[0]:'',
	        	'image_name2' => !empty($file_name[1])?$file_name[1]:'',
	        	'image_name3' => !empty($file_name[2])?$file_name[2]:'',
	        	'updated_at' => date('Y-m-d H:i:s'),	
		        ];
		        $update_image_attendance = DB::table('retailer_comment')
		        						->where('order_id',$order_id)
		        						->where('user_id',$user_id)
		        						->where('company_id',$company_id)
		        						->update($update_attendance_arr);
        	}
        	else
        	{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

        	}
	        
	       

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Retailer Comment Images Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}

        }

        if($module_id == 8) // profile image
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
	            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/users-profile/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'person_image' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('person_login')
	        						->where('person_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Profile Image Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}
			// return response()->json(['response'=>False,'Message'=>"Not uploaded"]);
			
        }

        if($module_id == 9) // merchandise
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
	            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/merchandise_image/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('merchandise')
	        						->where('user_id',$user_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully Profile Image Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}
        }

        if($module_id == 10) // retailer
        {
        	if ($request->hasFile('image_source')) 
			{
	            $image = $request->file('image_source');
    			$str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
        		$random_no = substr($str, 0,2);  // return always a new string 
        		$custom_image_name = date('YmdHis').$random_no.$user_id;
	            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
	            $destinationPath = public_path('/retailer_image/' . $imageName);
	            Image::make($image)->save($destinationPath);
	        }
	        if (empty($imageName)) {
	            $imageName = NULL;
	        }

	        $update_attendance_arr = [
	        	'image_name' => $imageName,
	        	'updated_at' => date('Y-m-d H:i:s'),	
	        ];
	        $update_image_attendance = DB::table('retailer')
	        						->where('created_by_person_id',$user_id)
	        						->where('id',$order_id)
	        						->where('company_id',$company_id)
	        						->update($update_attendance_arr);

			if($update_image_attendance)
			{
				return response()->json(['response'=>True,'Message'=>"Successfully  Retailer Profile Image Uploaded"]);
			}
			else
			{
				return response()->json(['response'=>False,'Message'=>"Not uploaded"]);

			}
        }



    } // function ends here
    


}
