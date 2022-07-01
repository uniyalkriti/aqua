<?php

namespace App\Http\Controllers;

use App\_module;
use App\_role;
use App\Competitor;
use App\Dealer;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Helpers\LocationArray;

class ReportNewController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Report';
    }

    /**
     * @function: beatRoute
     * @desc: index page of beat route
     */
    

    public function outletOpeningStatus()
    {
        $company_id = Auth::user()->company_id;
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        return view('reports.outlet-opening-status.index',
            [
                'state' => $state,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    


}
