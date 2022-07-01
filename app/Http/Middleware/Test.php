<?php

namespace App\Http\Middleware;
// namespace App\Http\Controllers;

USE DB;
USE Auth;
USE Closure;


class Test 
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next )
    {
         
    
       if(Auth::user()->role_id!==1){
          return redirect('user_public_data');
       }


        
    }
}
?>