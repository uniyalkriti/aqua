<?php

namespace App\Http\Controllers;

use App\Person;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class FeedbackController extends Controller
{
    public function show(Request $request,$id)
    {
        $user_id = Crypt::decryptString($id);
        $query = DB::table('feedbackSuggestion');

        $query_data = $query->orderBy('cur_date_time', 'DESC')
            ->where('user_id', $user_id)
            ->get();

        return view('reports.feedback', [
            'records' => $query_data,
            'id' => $id,
        ]);
    }
}
