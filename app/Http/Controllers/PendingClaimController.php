<?php

namespace App\Http\Controllers;

use App\Person;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use App\Claim;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class PendingClaimController extends Controller
{
    public function show(Request $request,$id)
    {
        $user = Crypt::decryptString($id);
        $query = Claim::leftJoin('location_4', 'location_4.id', '=', 'pending_claim.town_id')
            ->leftJoin('dealer', 'dealer.id', '=', 'pending_claim.distributor_id')
            ->where('user_id', $user)
            ->select('distributor_id','location_4.name as town', 'pending_claim.nature_of_claim', 'pending_claim.invoice_number', 'pending_claim.claim_paper', 'pending_claim.remark', 'pending_claim.expected_resolution_date', 'dealer.name as dealer_name', 'pending_claim.submission_date')
            ->get();
        return view('reports.pending_claim', [
            'records' => $query,
            'id'=>$id,
        ]);
    }
}
