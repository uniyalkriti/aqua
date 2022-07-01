<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
// use Session;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function login(Request $request)
    {
        // dd();


        $this->validate($request, [
            $this->username() => 'required',
            'password' => 'required',
            ]);
        $domain_name_custom = $_SERVER['HTTP_HOST'];
        if(!empty($domain_name_custom))
        {
            $domain_name = str_replace('www.', '', $domain_name_custom);
            if($domain_name == 'aeris-es.com')
            {
                $domain_name = 'www.aeris-es.com';
            }
        }
        else
        {
             throw ValidationException::withMessages([
                $this->username() => [trans('auth.domain')],
            ]);
            // return redirect('/login')->with('error', 'Invalid Email address or Password');

        }

        $email = explode('@',$request->email);
        // dd(COUNT($email));
        if(COUNT($email)<=1 )
        {
             throw ValidationException::withMessages([
                $this->username() => [trans('auth.user_name')],
            ]);
            // return redirect('/login')->with('error', 'Invalid Email address or Password');

        }
        // dd($email[1]);
        $company_id_data = DB::table('company')->select('id')->where('status',1)->where('name',$email[1])->where('domain_url',$domain_name)->first();
        // dd($company_id_data);
        if(!empty($company_id_data))
        {
            $company_id = !empty($company_id_data->id)?$company_id_data->id:'';

        }
        else
        {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.comapny')],
            ]);
        }
        if(!empty($company_id))
        {
            if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'company_id' =>$company_id ])
            )
            {
                // dd($company_id);
                if($company_id == 37)
                {
                    Session::forget('company_id_en');
                    Session::push('company_id_en', $company_id);
                    return redirect('/employeeDashboard');
                }
                else
                {
                    Session::forget('company_id_en');
                    Session::push('company_id_en', $company_id);
                    return redirect('/home');    
                }
                
            }

            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
            // return redirect('/login')->with('error', Lang::has('auth.failed'));
            
        }
        else
        {
            // Lang::get('auth.failed');
            // dd(Lang::get('auth.failed'));
            // echo $mesas = Lang::get('auth.failed');
            // Session::flash('message', Lang::get('auth.failed'));
            // Session::flash('class', 'success');
            // dd('134');
            // return redirect('/login');
               throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
            // return redirect('/login');
        }

    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $users=DB::table('users')
            ->where('original_pass','!=','')
            ->where('password_updated','!=','1')
            ->get();
        foreach ($users as $u)
        {
            $up=User::find($u->id);
            $up->password=bcrypt($u->original_pass);
            $up->password_updated='1';
            $up->save();
        }
    }

    public function logout(Request $request) {
        $request->session()->forget('dashboard');
        Auth::logout();
        return redirect('/login');
    }

}
