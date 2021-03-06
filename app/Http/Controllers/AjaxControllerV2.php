<?php

namespace App\Http\Controllers;

use App\_outletType;
use App\Area;
use App\Catalog1;
use App\Claim;
use App\competitorBrand;
use App\CompetitorsPriceLog;
use App\competitorsProduct;
use App\DailyStock;
use App\Dealer;
use App\DealerLocation;
use App\DealerRetailer;
use App\DistributorTarget;
use App\Feedback;
use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5;
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use Illuminate\Http\Request;
use DB;
use DateTime;

class AjaxControllerV2 extends Controller
{
    # it is for regions of state
    public function cities(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location3::where('location_2_id', $request->id)->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # it is for regions of state
    public function catalog_product(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = CatalogProduct::where('catalog_1_id', $request->id)->pluck('product_name', 'product_code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # city data using location_4 table
    public function cities_location4(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location4::where('location_3_id', $request->id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #country data
    public function country(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $data['result'] = Location1::where('status', 1)->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #state data
    public function state(Request $request)
    {
        if ($request->ajax() && ($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location2::where('status', 1)->where('location_1_id', $request->code)
                ->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }


    
    #location 4 data
    public function location4(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location4::where('location_3_id', $request->id)->pluck('name', 'code');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # location 5 data
    public function location5(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Area::where('location_4_id', $request->id)->pluck('name', 'code');
            if (isset($request->single_flag) && ($request->single_flag) == 1) {
            } else {
                $data['dealer'] = Dealer::where('status', '1')->where('location_4_id', $request->id)->pluck('name', 'id');
            }
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function location6(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location6::where('location_4_id', $request->id)->pluck('name', 'code');
            if (isset($request->single_flag) && ($request->single_flag) == 1) {
            } else {
                $data['dealer'] = Dealer::where('status', '1')->where('location_4_id', $request->id)->pluck('name', 'id');
            }
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function location7(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $f = explode(",", $request->id);
            $data['code'] = 200;
            $data['result'] = Location7::join('location_6', 'location_6.code', '=', 'location_7.location_6_id')
                ->join('location_5', 'location_5.code', '=', 'location_6.location_5_id')
                ->join('location_4', 'location_4.code', '=', 'location_5.location_4_id')
                ->whereIn('location_4.id', $f)->pluck('location_7.name', 'location_7.code');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #fetch location 7  data based on ID
    public function location7_id(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $f = !empty($request->id) ? explode(",", $request->id) : '';
            $data['code'] = 200;
//print_r($request->id);die;
            $check = ($request->id == 'null' || $request->id == null || $request->id == '') ? false : true;

            $query = Location5::join('location_4', 'location_4.id', '=', 'location_5.location_4_id');
            if ($check) {
                $query->whereIn('location_4.id', $f);
            }
            $data['result'] = $query->pluck('location_5.name', 'location_5.id');

            #Distributor
            $q2 = DB::table('location_5')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_5.id')
                ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id');
            if ($check) {
                $q2->whereIn('location_5.location_4_id', $f);
            }
            $data['result2'] = $q2->where('dealer.name', '!=', '')->pluck('dealer.name', 'dealer.id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function location7_dist(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $f = explode(",", $request->id);
            $data['code'] = 200;
            $data['result'] = Location7::join('location_6', 'location_6.code', '=', 'location_7.location_6_id')
                ->join('location_5', 'location_5.code', '=', 'location_6.location_5_id')
                ->join('location_4', 'location_4.code', '=', 'location_5.location_4_id')
                ->select(DB::raw("CONCAT(location_7.code,'-',location_4.id) AS compound_id"), 'location_7.name')
                ->whereIn('location_4.id', $f)->pluck('location_7.name', 'compound_id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function city_wise_distributor(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::join('location_view', 'location_view.l3_code', '=', 'dealers.location_3_id', 'INNER ')
                ->where('location_view.l4_code', $request->id)
                ->groupBy('dealers.id')
                ->pluck('dealers.name', 'dealers.dealer_code');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function senior_name(Request $request)
    {

        //echo"5435";die;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;

            $data['result'] = User::where('status', '1')->where('role_id', $request->id)->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function beat_wise_distributor(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::join('dealer_locations', 'dealer_locations.dealer_id', '=', 'dealers.id', 'INNER')
                ->where('dealer_locations.location_id', $request->id)
                ->groupBy('dealers.id')
                ->pluck('dealers.name', 'dealers.id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function district(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location3::where('location_2_id', $request->id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Multiple record on change on Region
    public function districtMultiple(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);
            $data['code'] = 200;
//            $data['result'] = Location3::whereIn('location_2_id', $id)->pluck('name', 'id');
            $query = DB::table('location_view');
            if (!empty($request->id) && $request->id != 'null') {
                $query->whereIn('l2_id', $id);
            }
            $data['result'] = $query->pluck('l3_name', 'l3_id');
            $data['result2'] = $query->pluck('l4_name', 'l4_id');
            $data['result3'] = $query->pluck('l5_name', 'l5_id');
            if (!empty($request->id) && $request->id != 'null') {
                $user_query = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l3_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->whereIn('location_view.l2_id', $id)
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid');
                $data['user'] = $user_query->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');
            }

            $dealer_query = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l3_id')
                ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id');
            if (!empty($request->id) && $request->id != 'null') {
                $dealer_query->whereIn('location_view.l2_id', $id);
            }
            $dealer_query->where('dealer_location_rate_list.user_id', '!=', '0')
                ->groupBy('dealer_location_rate_list.dealer_id')
                ->select('dealer.name', 'dealer.id');
            $data['dealers'] = $dealer_query->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function town(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location4::where('location_3_id', $request->id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Multiple state change
    public function townMultiple(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);
            if (!empty($request->id) && $request->id != 'null') {
                $query = DB::table('location_view')->whereIn('l3_id', $id);
                $data['towns'] = $query->pluck('l4_name', 'l4_id');
                $data['beats'] = $query->pluck('l5_name', 'l5_id');
            } else {
//                $region=Location2::where('status',1)->pluck('name','id');
//                $state=Location3::where('status',1)->pluck('name','id');
                $data['towns'] = Location4::where('status', 1)->pluck('name', 'id');
                $data['beats'] = Location5::where('status', 1)->pluck('name', 'id');
            }


            if (!empty($request->id) && $request->id != 'null') {
                $data['user_data'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l3_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->whereIn('location_view.l3_id', $id)
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->pluck('name', 'uid');
            } else {
                $data['user_data'] = DB::table('person')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            if (!empty($request->id) && $request->id != 'null') {
                $data['dealers'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l3_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->whereIn('location_view.l3_id', $id)
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->select('dealer.name', 'dealer.id')
                    ->pluck('name', 'id');
            } else {
                $data['dealers'] = Dealer::where('dealer_status', 1)->pluck('name', 'id');
            }

            $data['code'] = 200;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function beat(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location5::where('location_4_id', $request->id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #beat multiple
    public function beatMultiple(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);
            if (!empty($request->id) && $request->id != 'null') {
                $query = DB::table('location_view')->whereIn('l4_id', $id);
                $data['beats'] = $query->pluck('l5_name', 'l5_id');
            } else {
                $data['beats'] = Location5::where('status', 1)->pluck('name', 'id');
            }

            if (!empty($request->id) && $request->id != 'null') {
                $data['user_data'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l3_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->whereIn('location_view.l4_id', $id)
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->pluck('name', 'uid');
            } else {
                $data['user_data'] = DB::table('person')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            if (!empty($request->id) && $request->id != 'null') {
                $data['dealers'] = DB::table('location_view')
                    ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l3_id')
                    ->leftJoin('dealer', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                    ->whereIn('location_view.l4_id', $id)
                    ->where('dealer_location_rate_list.user_id', '!=', '0')
                    ->groupBy('dealer_location_rate_list.dealer_id')
                    ->select('dealer.name', 'dealer.id')
                    ->pluck('name', 'id');
            } else {
                $data['dealers'] = Dealer::where('dealer_status', 1)->pluck('name', 'id');
            }

            $data['code'] = 200;
//            $data['result'] = Location5::where('location_4_id', $request->id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Manage ajax on multiple select of dealer
    public function distributorMultiple(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);

            $beat_query = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id');
            if ($request->id != 'null') {
                $beat_query->whereIn('dealer_location_rate_list.dealer_id', $id);
            }
            $data['beats'] = $beat_query->pluck('l5_name', 'l5_id');

            $user_query = DB::table('dealer_location_rate_list')
                ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                ->where('dealer_location_rate_list.user_id', '!=', '0');
            if ($request->id != 'null') {
                $user_query->whereIn('dealer_location_rate_list.dealer_id', $id);
            }
            $user_query->groupBy('dealer_location_rate_list.user_id')
                ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid');
            $data['user_data'] = $user_query->pluck('name', 'uid');

            $data['code'] = 200;
//            $data['result'] = Location5::where('location_4_id', $request->id)->orderBy('name', 'ASC')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #get user data by role id
    public function getUserByRole(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $id = explode(',', $request->id);

            $data['user_data'] = DB::table('person')
                ->whereIn('person.role_id', $id)
                ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->pluck('name', 'uid');

            $data['code'] = 200;
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function state_name(Request $request)
    {
        //echo $request->id;die;
        if ($request->ajax() && ($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location2::where('status', 1)->where('location_1_id', $request->id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_hq(Request $request)
    {
        //echo "123";die;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Location3::where('location_2_id', $request->id)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function role_user(Request $request)
    {
        //echo "123";die;
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = User::where('role_id', $request->id)->pluck('name', 'role_id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function state_wise_distributor(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::where('status', '1')->where('location_2_id', $request->id)->pluck('name', 'dealer_code');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function beat_wise_retailer(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Retailer::where('beat_code', $request->id)->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #beat wise distributor list (Latest)
    public function beatWiseDistributor(Request $request)
    {

        if ($request->ajax() && !empty($request->id)) {
            $f = [];
            $f = explode(',', $request->id);
            $check = ($request->id == 'null' || $request->id == null || $request->id == '') ? false : true;
            $data['code'] = 200;
            $query = Dealer::join('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id', 'INNER');
            if ($check) {
                $query->whereIn('dealer_location_rate_list.location_id', $f);
            }
            $data['result'] = $query->groupBy('dealer.id')
                ->pluck('dealer.name', 'dealer.id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    # working query when outlet is selected
//select _outlet_types.name, COUNT(retailers.outlet_type_id) from _outlet_types LEFT JOIN retailers on (_outlet_types.id=retailers.outlet_type_id AND retailers.id=14) GROUP BY _outlet_types.id
    public function beat_route_report(Request $request)
    {
        if ($request->ajax()) {
//            $query = _outletType::leftJoin('retailer', function ($join) use ($request) {
//                $join->on('retailer.outlet_type_id', '=', '_retailer_outlet_type.id');
//                $join->on('retailer.id', '=', $request);
//
//            });
            $temp = [];
            if (!empty($request->belt)) {
                $beltArr = $request->belt;
                if (!empty($beltArr)) {
                    $temp = DB::table('location_view')->whereIn('l4_id', $beltArr)->pluck('l5_id');
                }
            }

            if (!empty($request->beat)) {
                $temp = [];
                $temp = $request->beat;
            }

            $query = _outletType::join('retailer', 'retailer.outlet_type_id', '=', '_retailer_outlet_type.id', 'LEFT')
                ->select("_retailer_outlet_type.outlet_type AS outlet_name", DB::raw('COUNT(retailer.outlet_type_id) as total'), '_retailer_outlet_type.id as outlet_id');
            $query->groupBy('_retailer_outlet_type.id');
            if (!empty($temp)) {
                $query->whereIn('retailer.location_id', $temp);
            }
            if (!empty($request->outlet)) {
                $query->whereIn('retailer.outlet_type_id', $request->outlet);
            }
//               if(!empty($request->outlet))
//               {
//                   $query->where('retailer.outlet_type_id',$request->outlet);
//               }
            $outlet_categories = $query->get();

            $platinum = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'));
            if (!empty($temp)) {
                $platinum->whereIn('user_sales_order.location_id', $temp);
            }
            $platinum->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) >= ?', [15000])
                ->first();
            $diamond = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'));
            if (!empty($temp)) {
                $diamond->whereIn('user_sales_order.location_id', $temp);
            }
            $diamond->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) > ?', [9999])
                ->havingRaw('SUM(user_sales_order.total_sale_value) < ?', [15000])
                ->first();
            $gold = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'));
            if (!empty($temp)) {
                $gold->whereIn('user_sales_order.location_id', $temp);
            }
            $gold->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) > ?', [7499])
                ->havingRaw('SUM(user_sales_order.total_sale_value) < ?', [10000])
                ->first();
            $silver = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'));
            if (!empty($temp)) {
                $silver->whereIn('user_sales_order.location_id', $temp);
            }
            $silver->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.total_sale_value) > ?', [4999])
                ->havingRaw('SUM(user_sales_order.total_sale_value) < ?', [7500])
                ->first();
            $semi_wholeseller = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'));
            if (!empty($temp)) {
                $semi_wholeseller->whereIn('user_sales_order.location_id', $temp);
            }
            $semi_wholeseller->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.case_qty) > ?', [7])
                ->havingRaw('SUM(user_sales_order.case_qty) < ?', [25])
                ->first();
            $wholeseller = UserSalesOrder::select(DB::raw('count(user_sales_order.retailer_id) as count'));
            if (!empty($temp)) {
                $wholeseller->whereIn('user_sales_order.location_id', $temp);
            }
            $wholeseller->groupBy('user_sales_order.retailer_id')
                ->havingRaw('SUM(user_sales_order.case_qty) > ?', [24])
                ->first();

            $query2 = Retailer::leftJoin('_retailer_outlet_type', '_retailer_outlet_type.id', '=', 'retailer.outlet_type_id')
                ->leftJoin('location_view', 'location_view.l5_id', '=', 'retailer.location_id')
                ->leftJoin('location_5', 'location_view.l5_id', '=', 'location_5.id')
                ->leftJoin('dealer', 'retailer.dealer_id', '=', 'dealer.id');
            if (!empty($request->belt)) {
                $beltArr = $request->belt;
                if (!empty($beltArr)) {
                    $temp = [];
                    $temp = DB::table('location_view')->whereIn('l4_id', $beltArr)->pluck('l5_id');
                }
                if (!empty($temp)) {
                    $query2->whereIn('retailer.location_id', $temp);
                }
            }
            if (!empty($request->beat)) {
                $beatArr = $request->beat;
                $query2->whereIn('retailer.location_id', $beatArr);
            }
            if (!empty($request->distributor)) {
                $distributorArr = $request->distributor;
                $query2->whereIn('dealer.id', $distributorArr);
            }
            if (!empty($request->outlet)) {
                $outletArr = $request->outlet;
                $query2->whereIn('retailer.outlet_type_id', $outletArr);
            }
            if (!empty($request->day)) {
                $dayArr = $request->day;
                $query2->whereIn('location_5.day', $dayArr);
            }
            $query2 = $query2->select('retailer.id', 'retailer.class as class', '_retailer_outlet_type.outlet_type as outlet_category', 'location_view.l5_name as beat', 'location_view.l1_name as zone', 'location_view.l2_name as region', 'location_view.l3_name as state', 'location_view.l4_name as town', 'retailer.retailer_code as outlet_id', 'retailer.name as outlet_name', 'dealer.name as dealer_name');
            $rows = $query2->get();

            return view('reports.beat-route-ajax', [
                'outlet_categories' => $outlet_categories,
                'platinum' => $platinum,
                'diamond' => $diamond,
                'gold' => $gold,
                'silver' => $silver,
                'semi_wholeseller' => $semi_wholeseller,
                'wholeseller' => $wholeseller,
                'rows' => $rows
            ]);
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function distributorsBeat(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $idArr = explode(',', $request->id);
            if ($request->id == 'null') {
                $data['result'] = Location5::where('status', 1)->pluck('name', 'id');
            } else {

                $query = DB::table('dealer_location_rate_list')
                    ->leftJoin('location_5', 'location_5.id', '=', 'dealer_location_rate_list.location_id');
                $query->where('dealer_id', $idArr);
                $query->groupBy('location_id');
                $data['result'] = $query->pluck('location_5.name', 'location_5.id');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function town_distributor(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = Dealer::where('location_3_id', $request->id)->pluck('name', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Ajax view page for market beat plan
    public function marketBeatPlanReport(Request $request)
    {
        if ($request->ajax() && !empty($request->month)) {
            $month = $request->month;
            $town = $request->belt;
            $distributor = $request->distributor;
            $beat = $request->beat;
            $year = date('Y', strtotime($month));

            #retialer wise month wise secondary sales data
//            $retailer_sales_data=DB::table('beat_classification')
//                ->where('beat_classification.month', $month);

            $dealer_query = Dealer::where('dealer.name', '!=', '');
            if (!empty($distributor)) {
                $dealer_query->whereIn('dealer.id', $distributor);
            }
            $dealer = $dealer_query->select('dealer.name', 'dealer.address', 'location_4.name as town', 'location_3.name as state', 'dealer_code')
                ->leftJoin('location_4', 'location_4.id', '=', 'dealer.town_id')
                ->leftJoin('location_3', 'location_3.id', '=', 'dealer.state_id')
                ->first();
            $dealer_target_q = DistributorTarget::where('dealer_id', '>', '0');
            if (!empty($distributor)) {
                $dealer_target_q->whereIn('dealer_id', $distributor);
            }
            $dealer_target = $dealer_target_q->where('session', 'Like', '%' . $year . '%')
                ->select('month', 'target', 'achievement')->get();
            //  print_r($dealer_target); exit;
            $outlet_q = UserDealerRetailer::where('dealer_id', '>', '0');
            if (!empty($distributor)) {
                $outlet_q->whereIn('dealer_id', $distributor);
            }
            $outlet = $outlet_q->select(DB::raw('COUNT(user_dealer_retailer.id) as total'))
                ->first();
            $noBeat = DB::table('location_5')->where('location_4_id', $town)
                ->select(DB::raw('COUNT(location_5.id) as total'))
                ->first();

            $isrCount_q = UserDealerRetailer::where('dealer_id', '>', '0');
            if (!empty($distributor)) {
                $isrCount_q->whereIn('dealer_id', $distributor);
            }
            $isrCount = $isrCount_q->where('person.role_id', 12)
                ->join('person', 'person.id', '=', 'user_dealer_retailer.user_id')
                ->select(DB::raw('COUNT(Distinct user_dealer_retailer.user_id) as total'))->first();
            $records_q = Dealer::leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->leftJoin('location_5', 'location_5.id', '=', 'dealer_location_rate_list.location_id')
                ->leftJoin('retailer', 'retailer.location_id', '=', 'location_5.id')
                ->select('location_5.name as market', 'location_5.id as lid');
            if (!empty($beat)) {
                $records_q->whereIn('location_5.id', $beat);
            }
            if (!empty($distributor)) {
                $records_q->whereIn('dealer_location_rate_list.dealer_id', $distributor);
            }
            $records_q->distinct('location_5.id');
            if (!empty($distributor)) {
                $records_q->where('dealer.id', $distributor);
            }
            $records = $records_q->get();
            $d = [];
            if (!empty($records)) {
                foreach ($records as $k => $raw) {
                    $d[$k]['market'] = $raw->market;

                    $d[$k]['platinum'] = DB::table('beat_classification')
                        ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->where('total_sale', '>', 10000)->count();
                    $d[$k]['diamond'] = DB::table('beat_classification')
                        ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=10000 and total_sale <= 12000')->count();
                    $d[$k]['gold'] = DB::table('beat_classification')
                        ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=7500 and total_sale <= 10000')->count();
                    $d[$k]['silver'] = DB::table('beat_classification')
                        ->where('beat_classification.month', $month)->where('l5_id', $raw->lid)->whereRaw('total_sale >=5000 and total_sale <= 7500')->count();
                    $d[$k]['sws'] = 0;
                    $d[$k]['ws'] = 0;

//                    $d[$k]['diamond'] = Retailer::where('location_id', $raw->lid)->where('class', 2)->count();
//                    $d[$k]['gold'] = Retailer::where('location_id', $raw->lid)->where('class', 3)->count();
//                    $d[$k]['silver'] = Retailer::where('location_id', $raw->lid)->where('class', 4)->count();
//                    $d[$k]['sws'] = Retailer::where('location_id', $raw->lid)->where('class', 5)->count();
//                    $d[$k]['ws'] = Retailer::where('location_id', $raw->lid)->where('class', 6)->count();

                    $d[$k]['categories'] = DB::table('_outlet_types')->leftJoin('retailer', '_outlet_types.id', '=', 'retailer.outlet_type_id')
                        ->where('retailer.location_id', $raw->lid)
                        ->select(DB::raw('count(retailer.id) as cc'), '_outlet_types.id')
                        ->groupBy('_outlet_types.id')
                        ->pluck('cc', 'id');
                    $d[$k]['t2'] = !empty($d[$k]['categories']) ? array_sum($d[$k]['categories']->toArray()) : 0;
                }
            }
            return view('reports.market-beat-plan.ajax', [
                'records' => $d,
                'dealer' => $dealer,
                'outlet' => $outlet,
                'isrCount' => $isrCount,
                'noBeat' => $noBeat,
                'dealer_target' => $dealer_target
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }
//    public function marketBeatPlanReport(Rbeequest $request)
//    {
//        if ($request->ajax() && !empty($request->town) && !empty($request->distributor)) {
//            $town = $request->town;
//            $distributor = $request->distributor;
//            $from_date = $request->from_date;
//            $to_date = $request->to_date;
//
//            $dealer = Dealer::where('id', $distributor)
//                ->select('dealer.name')
//                ->first();
//            $outlet = DealerRetailer::where('dealer_id', $distributor)
//                ->select(DB::raw('COUNT(dealer_retailers.id) as total'))
//                ->first();
//            $records = Dealer::leftJoin('dealer_locations', 'dealer_locations.dealer_id', '=', 'dealer.id')
//                ->leftJoin('location_7', 'location_7.id', '=', 'dealer_locations.location_id')
//                ->leftJoin('retailers', 'retailers.beat_code', '=', 'location_7.id')
//                ->select('location_7.name as market', 'location_7.id as lid')
//                ->groupBy('location_7.id')
//                ->where('dealer.id', $distributor)
//                ->get();
//            $d = [];
//            if (!empty($records)) {
//                foreach ($records as $k => $raw) {
//                    $d[$k]['market'] = $raw->market;
//                    $d[$k]['platinum'] = Retailer::where('beat_code', $raw->lid)->where('class', 1)->count();
//                    $d[$k]['diamond'] = Retailer::where('beat_code', $raw->lid)->where('class', 2)->count();
//                    $d[$k]['gold'] = Retailer::where('beat_code', $raw->lid)->where('class', 3)->count();
//                    $d[$k]['silver'] = Retailer::where('beat_code', $raw->lid)->where('class', 4)->count();
//                    $d[$k]['sws'] = Retailer::where('beat_code', $raw->lid)->where('class', 5)->count();
//                    $d[$k]['ws'] = Retailer::where('beat_code', $raw->lid)->where('class', 6)->count();
//
//                    $d[$k]['categories'] = _outletType::leftJoin('retailers', '_outlet_types.id', '=', 'retailers.outlet_type_id')
//                        ->where('retailers.beat_code', $raw->lid)
//                        ->select(DB::raw('count(retailers.id) as cc'), '_outlet_types.id')
//                        ->groupBy('_outlet_types.id')
//                        ->pluck('cc', 'id');
//                    $d[$k]['t2'] = !empty($d[$k]['categories']) ? array_sum($d[$k]['categories']->toArray()) : 0;
//                }
//            }
//            return view('reports.market-beat-plan.ajax', [
//                'records' => $d,
//                'dealer' => $dealer,
//                'outlet' => $outlet
//            ]);
//        } else {
//            echo '<p class="alert-danger">Do not hack the system</p>';
//        }
//    }

    #Fetch user based on belt
    public function getBeltUsers(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = UserDetail::where('location_6_id', $request->id)->select(DB::raw('CONCAT(first_name," ",last_name) as name'), 'user_id')->pluck('name', 'user_id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Fetch receipt
    public function getReceipt(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
//            $idArr = explode(',', $request->id);
            $user_id = $request->id;
            $date = $request->date;

            $data['code'] = 200;
            $query = DB::table('dealer_payments')
                ->where('user_id', $user_id);
            if (!empty($date)) {
                $query->whereRaw("DATE_FORMAT(dealer_payments.payment_recevied_date, '%Y-%m-%d') = '$date'");
            }
            $data['result'] = $query->pluck('payment_recevied_date', 'id');

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Fetch user based on beat
    public function getBeatUsers(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            if (!empty($request->id) && $request->id != 'null') {
                $data['result'] = DB::table('dealer_location_rate_list')
                    ->leftJoin('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                    ->where('dealer_location_rate_list.location_id', $request->id)
                    ->where('person.id', '!=', 0)
                    ->select(DB::raw('CONCAT(first_name," ",last_name) as name'), 'person.id as user_id')
                    ->pluck('name', 'user_id');
            } else {
                $data['result'] = DB::table('person')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #Ajax view page for tour program
//    public function tourProgramReport(Request $request)
//    {
//        if ($request->ajax() && !empty($request->month)) {
//
////            $user_id = $request->user;
//            $month = $request->month;
//
//            $work_status = DB::table('_task_of_the_day')->pluck('task', 'id');
//
//            $awsome_query = MonthlyTourProgram::leftJoin('location_view', 'location_view.l5_id', '=', 'monthly_tour_program.locations')
//                ->leftJoin('dealer', 'dealer.id', '=', 'monthly_tour_program.dealer_id')
//                ->leftJoin('person', 'monthly_tour_program.person_id', 'person.id')
//                ->leftJoin('location_5', 'monthly_tour_program.locations', '=', 'location_5.id')
//                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('person as p', 'p.id', '=', 'person.person_id_senior');
//            #Region filter
//            if (!empty($request->region)) {
//                $region = $request->region;
//                $awsome_query->whereIn('location_view.l2_id', $region);
//            }
//            #State filter
//            if (!empty($request->area)) {
//                $state = $request->area;
//                $awsome_query->whereIn('location_view.l3_id', $state);
//            }
//            #Town filter
//            if (!empty($request->territory)) {
//                $town = $request->territory;
//                $awsome_query->whereIn('location_view.l4_id', $town);
//            }
//            #Beat filter
//            if (!empty($request->belt)) {
//                $beat = $request->belt;
//                $awsome_query->whereIn('location_view.l5_id', $beat);
//            }
//            #Dealer filter
//            if (!empty($request->distributor)) {
//                $distributor = $request->distributor;
//                $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
//            }
//            #Role filter
//            if (!empty($request->role)) {
//                $role_id = $request->role;
//                $awsome_query->whereIn('person.role_id', $role_id);
//            }
//            #User Filter
//            if (!empty($request->user)) {
//                $ud = $request->user;
//                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
//            }
//            $awsome_query->select(DB::raw('CONCAT(p.first_name," ",p.last_name) as senior'), '_role.rolename as role', 'monthly_tour_program.id as mid', 'location_5.name as bname', DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as user_id', 'person.emp_code', 'person.head_quar', 'dealer.id as dealer_id', 'dealer.name as dealer_name', 'monthly_tour_program.working_date', 'location_view.l1_name', 'location_view.l2_name', 'location_view.l3_name', 'location_view.l4_name as town_name', 'monthly_tour_program.working_status_id', 'monthly_tour_program.pc', 'monthly_tour_program.rd', 'monthly_tour_program.arch', 'monthly_tour_program.collection', 'monthly_tour_program.primary_ord', 'monthly_tour_program.any_other_task', 'monthly_tour_program.new_outlet')
//                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m') = '$month'");
//            $plans = $awsome_query->orderBy('monthly_tour_program.working_date')->groupBy('working_date', 'person_id')->get();
//            $d = [];
//            foreach ($plans as $k => $p) {
//                $d[$k] = DealerLocation::leftJoin('location_view', 'location_view.l5_id', '=', 'dealer_location_rate_list.location_id')
//                    ->where('dealer_id', $p->dealer_id)
//                    ->select('location_view.l5_name')
//                    ->pluck('l5_name')->toArray();
//            }
//            return view('reports.tour-program.ajax', [
//                'plans' => $plans,
//                'work_status' => $work_status,
//                'beat_data' => $d,
//                'month' => $month
//            ]);
//        } else {
//            echo '<p class="alert-danger">Do not hack the system</p>';
//        }
//    }

#Ajax view page for tour program
    public function tourProgramReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->from_date)) {

// $user_id = $request->user;
            $month = $request->month;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $work_status = DB::table('_task_of_the_day')->pluck('task', 'id');

            $awsome_query = MonthlyTourProgram::leftJoin('location_view', 'location_view.l5_id', '=', 'monthly_tour_program.locations')
                ->leftJoin('dealer', 'dealer.id', '=', 'monthly_tour_program.dealer_id')
                ->leftJoin('person', 'monthly_tour_program.person_id', 'person.id')
                ->leftJoin('location_5', 'monthly_tour_program.locations', '=', 'location_5.id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
                ->leftJoin('person as p', 'p.id', '=', 'person.person_id_senior');
#Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $awsome_query->whereIn('location_view.l2_id', $region);
            }
#State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $awsome_query->whereIn('location_view.l3_id', $state);
            }
#Town filter
            if (!empty($request->territory)) {
                $town = $request->territory;
                $awsome_query->whereIn('location_view.l4_id', $town);
            }
#Beat filter
            if (!empty($request->belt)) {
                $beat = $request->belt;
                $awsome_query->whereIn('location_view.l5_id', $beat);
            }
#Dealer filter
            if (!empty($request->distributor)) {
                $distributor = $request->distributor;
                $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
            }
#Role filter
            if (!empty($request->role)) {
                $role_id = $request->role;
                $awsome_query->whereIn('person.role_id', $role_id);
            }
#User Filter
            if (!empty($request->user)) {
                $ud = $request->user;
                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
            }
#Date Filter
            if (!empty($from_date) && !empty($to_date)) {
                $awsome_query->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to_date'");
            }
            $awsome_query->select(DB::raw('CONCAT(p.first_name," ",p.last_name) as senior'), '_role.rolename as role', 'monthly_tour_program.id as mid', 'location_5.name as bname', DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as user_id', 'person.emp_code', 'person.head_quar', 'dealer.id as dealer_id', 'dealer.name as dealer_name', 'monthly_tour_program.working_date', 'location_view.l1_name', 'location_view.l2_name', 'location_view.l3_name', 'location_view.l4_name as town_name', 'monthly_tour_program.working_status_id', 'monthly_tour_program.pc', 'monthly_tour_program.rd', 'monthly_tour_program.arch', 'monthly_tour_program.collection', 'monthly_tour_program.primary_ord', 'monthly_tour_program.any_other_task', 'monthly_tour_program.new_outlet', DB::raw("(select CONCAT_WS('|',l1_name,l2_name,l3_name,l4_name) from location_view WHERE location_view.l4_id=monthly_tour_program.town GROUP BY l4_id limit 0,1) as town_loc"));

// $plans = $awsome_query->orderBy('monthly_tour_program.working_date')->get();
            $plans = $awsome_query->orderBy('monthly_tour_program.working_date')->groupBy('working_date', 'person_id')->get();
//            dd($plans);
            $d = [];
            foreach ($plans as $k => $p) {
                $d[$k] = DealerLocation::leftJoin('location_view', 'location_view.l5_id', '=', 'dealer_location_rate_list.location_id')
                    ->where('dealer_id', $p->dealer_id)
                    ->select('location_view.l5_name')
                    ->pluck('l5_name')->toArray();
            }
            return view('reports.tour-program.ajax', [
                'plans' => $plans,
                'work_status' => $work_status,
                'beat_data' => $d,
                'month' => $month
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

////// DAILY ATTENDANCE REPORT OLD//
    public function dailyAttendanceReportOld(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $zone = $request->region;
            $region = $request->area;
//            $town = $request->territory;
//            $beat = $request->belt;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = !empty($request->from_date) ? $request->from_date : '';
            $to_date = !empty($request->to_date) ? $request->to_date : date('Y-m-d');

            $data = UserDetail::leftJoin('user_daily_attendance', 'user_daily_attendance.user_id', '=', 'person.id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('check_out', 'check_out.user_id', '=', 'user_daily_attendance.user_id')
                ->leftJoin('check_out', function ($leftjoin) {
                    $leftjoin->on('check_out.user_id', '=', 'user_daily_attendance.user_id');
                    $leftjoin->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"));
                })
                ->leftJoin('_working_status', 'user_daily_attendance.work_status', '=', '_working_status.id')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.user_id', '=', 'user_daily_attendance.user_id')
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'");

            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $data->whereIn('location_view.l3_id', $state);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data->whereIn('user_daily_attendance.user_id', $user);
            }

            $data = $data->select(DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=person.id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id asc limit 0,1) as first_call1"),
                DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=person.id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id desc limit 0,1) as last_call"),
                DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),
                DB::raw("(select location_view.l4_name from monthly_tour_program left join location_view on location_view.l5_id=monthly_tour_program.locations WHERE monthly_tour_program.person_id=user_daily_attendance.user_id AND monthly_tour_program.working_date=DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')) as l"),
                '_role.rolename as role', '_working_status.name as work', 'user_daily_attendance.work_date as checkin', 'person.emp_code', 'location_view.l1_name as zone', 'location_view.l2_name as region', 'person.id',
                'check_out.work_date as checkout', 'check_out.attn_address', 'user_daily_attendance.track_addrs')
//                ->groupBy('user_daily_attendance.id')
                ->groupBy('user_daily_attendance.work_date', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', '_working_status.name', 'check_out.work_date', 'check_out.attn_address', 'user_daily_attendance.track_addrs', 'l', 'person.emp_code', 'location_view.l1_name', 'location_view.l2_name', 'person.id');
            $records = $data->get();
            $ids = $data->pluck('id');

            #for absent users
            $q2 = DB::table('person')
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            if (!empty($ids)) {
                $q2->whereNotIn('id', $ids);
            }
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $q2->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $q2->whereIn('location_view.l3_id', $state);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $q2->whereIn('person.id', $user);
            }
            $q2->select(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),
                '_role.rolename as role', 'person.emp_code', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                'person.id');
            $absent_records = $q2->groupBy('location_view.l3_id', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', 'person.emp_code', 'location_view.l1_name', 'location_view.l2_name', 'person.id')->get();

//            dd($absent_records[0]);

            $t = '';
            if (!empty($town)) {
                $t = Location4::where('id', $town)->pluck('name')->first();
            }
            return view('reports.daily-attendance.ajax', [
                'absentRecords' => $absent_records,
                'records' => $records,
                'town' => $t
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }
//////////////////// END OF OLD DAILY ATTENDANCE  //////////////////////////////
///////////////////// NEW DAILY ATTENDANCE ////////////////////////

    public function dailyAttendanceReport(Request $request)
    {
        //    if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date))
        {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data1 = UserDetail::select('person.id as person_id', 'person.emp_code as emp_code', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_view.l1_name as zone', 'location_view.l2_name as region', '_role.rolename as role')
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $data1->whereIn('location_view.l3_id', $state);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $record = $data1->get();

            //dd($record);
            $records = array();
            $i = 0;

            // print_r($value);
            while (strtotime($from_date) <= strtotime($to_date)) {
                foreach ($record as $key => $value) {
                    //  echo $from_date;
                    $data = DB::table('user_att_sale_view')
                        ->where('user_id', $value->person_id)
                        ->whereRaw("DATE_FORMAT(user_att_sale_view.work_date,'%Y-%m-%d') = '$from_date'")->first();
                    // dd($data);
                    if (!empty($data)) {
                        $records[$i]['person_id'] = $value->person_id;
                        $records[$i]['emp_code'] = $value->emp_code;
                        $records[$i]['uname'] = $value->uname;
                        $records[$i]['zone'] = $value->zone;
                        $records[$i]['region'] = $value->region;
                        $records[$i]['role'] = $value->role;
                        $records[$i]['track_addrs'] = $data->track_addrs;
                        $records[$i]['date'] = $from_date;
                        $records[$i]['work_date'] = date('d-M-YH:i:s', strtotime($data->work_date));
                        $records[$i]['work'] = $data->work;
                        $records[$i]['check_out_date'] = $data->check_out_date;
                        if (!empty($data->work_date) && !empty($data->check_out_date)) {
                            $c11 = new DateTime($data->work_date);
                            $c21 = new DateTime($data->check_out_date);
                            $interval1 = $c11->diff($c21);
                            $records[$i]['workinghrs'] = $interval1->format('%h') . " Hours " . $interval1->format('%i') . " Minutes";
                        } else {
                            $records[$i]['workinghrs'] = '0';
                        }

                        $records[$i]['first_call'] = $data->first_call;
                        $records[$i]['last_call'] = $data->last_call;
                        if (!empty($data->first_call) && !empty($data->last_call)) {
                            $c1 = new DateTime($data->first_call);
                            $c2 = new DateTime($data->last_call);
                            $interval = $c1->diff($c2);
                            $records[$i]['totalHrs'] = $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
                        } else {
                            $records[$i]['totalHrs'] = '0';
                        }
                        $i++;
                    } else {
                        $records[$i]['person_id'] = $value->person_id;
                        $records[$i]['emp_code'] = $value->emp_code;
                        $records[$i]['uname'] = $value->uname;
                        $records[$i]['zone'] = $value->zone;
                        $records[$i]['region'] = $value->region;
                        $records[$i]['role'] = $value->role;
                        $records[$i]['track_addrs'] = "N/A";
                        $records[$i]['date'] = $from_date;
                        $records[$i]['work_date'] = '0';
                        $records[$i]['work'] = "N/A";
                        $records[$i]['workinghrs'] = '0';
                        $records[$i]['check_out_date'] = "";
                        $records[$i]['first_call'] = "N/A";
                        $records[$i]['last_call'] = "N/A";
                        $records[$i]['totalHrs'] = '0';
                        $i++;
                    }
                }
                $from_date = date("Y-m-d", strtotime("+1 days", strtotime($from_date)));


            }
            echo $i;
            return view('reports.daily-attendance.ajax', [
                // 'absentRecords' => $absent_records,
                'records' => $records
                // 'town' => $t
            ]);

        }
        // else 
        // {
        //     echo '<p class="alert-danger">Do not hack the system</p>';
        // }
    }

    public function getBrands(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = competitorBrand::where('competitor_id', $request->id)->where('status', 1)->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function competitorsNewProductReport(Request $request)
    {
        if ($request->ajax() && !empty($request->user)) {

            $user = $request->user;

            $query = DB::table('competitors_launched_product')
                ->where('user_id', $user)
                ->get();

            return view('reports.competitors-new-product.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function TravellingExpensesReport(Request $request)
    {
        if ($request->ajax()) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $arr = [1 => 'Bus', 2 => 'Train', 3 => 'Motorcycle', 4 => 'Taxi', 5 => 'Flight', 6 => 'Metro'];


            $query = DB::table('travelling_expense_bill')
                ->leftJoin('person', 'person.id', '=', 'travelling_expense_bill.user_id')
                ->leftJoin('location_4 as d', 'd.id', '=', 'travelling_expense_bill.departureID')
                ->leftJoin('location_4 as a', 'a.id', '=', 'travelling_expense_bill.arrivalID');

            if (!empty($user_id)) {
                $query->whereIn('user_id', $user_id);
            }
            if (!empty($from_date) && !empty($to_date)) {
                $query->whereRaw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d') <='$to_date'");
            }
            $query_data = $query->select('a.name as aname', 'd.name as dname', 'travelling_expense_bill.*', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'))
                ->orderBy('date_time', 'DESC')
                ->get();
            return view('reports.travelling-expenses.ajax', [
                'records' => $query_data,
                'arr' => $arr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function pendingClaimReport(Request $request)
    {
        if ($request->ajax() && !empty($request->user)) {
            $user = $request->user;

            $query = Claim::leftJoin('location_4', 'location_4.id', '=', 'pending_claim.town_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'pending_claim.distributor_id')
                ->where('user_id', $user)
//                ->groupBy('location_view.l4_id','nature_of_claim','invoice_number','claim_paper','remark','expected_resolution_date')
                ->select('location_4.name as town', 'pending_claim.nature_of_claim', 'pending_claim.invoice_number', 'pending_claim.claim_paper', 'pending_claim.remark', 'pending_claim.expected_resolution_date', 'dealer.name as dealer_name', 'pending_claim.submission_date')
                ->get();
            return view('reports.pending-claim.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function getDealer(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
//            $data['result'] = Dealer::where('location_3_id', $request->id)->pluck('name', 'id');
            $data['result'] = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_location_rate_list.dealer_id')
                ->where('l3_id', $request->id)
                ->where('dealer.id', '!=', '')
                ->groupBy('dealer_location_rate_list.dealer_id')
                ->pluck('dealer.name', 'dealer.id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    /**
     * @function :agingReport
     * @desc: Will provide data from challan_order table with duration 30,31-45,46-60,61> days balances
     */
    public function agingReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region)) {

            $region = $request->region;

//            $query = Dealer::leftJoin('challan_orders', 'challan_orders.ch_dealer_id', '=', 'dealers.id')
//                ->leftJoin('location_3', 'location_3.id', '=', 'dealers.location_3_id')
//                ->leftJoin('dealer_locations', 'dealer_locations.dealer_id', '=', 'dealers.id')
//                ->where('dealers.location_3_id', $region);
//            if (!empty($request->distributor)) {
//                $query->where('dealers.id', $request->distributor);
//            }
//            $query->groupBy('dealers.id')
//                ->select(DB::raw('SUM(challan_orders.remaining) as total_remaining'), 'dealers.name', 'dealers.dealer_code',
//                    'location_3.name as state', 'dealers.name as dealer', 'dealer_locations.town');
//            $data = $query->get();

            $query = DB::table('location_view')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.location_id', '=', 'location_view.l5_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_location_rate_list.dealer_id')
                ->leftJoin('challan_order', 'challan_order.ch_dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats', 'dealer_pay_stats.dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats_45', 'dealer_pay_stats_45.dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats_60', 'dealer_pay_stats_60.dealer_id', '=', 'dealer.id')
                ->leftJoin('dealer_pay_stats_61', 'dealer_pay_stats_61.dealer_id', '=', 'dealer.id')
                ->where('l3_id', $region)
                ->where('dealer.id', '!=', '');
            if (!empty($request->distributor)) {
                $query->where('dealer.id', $request->distributor);
            }
            $query->groupBy('dealer_location_rate_list.dealer_id', 'location_view.l3_name', 'location_view.l4_name');
            $data = $query->select('dealer.id', 'dealer.dealer_code', 'dealer.name', 'location_view.l3_name as state', 'location_view.l4_name as town'
                , DB::raw("MAX(ch_date) as last_invoice"), 'dealer_pay_stats.amount_sum as amount1',
                'dealer_pay_stats_45.amount_sum as amount2', 'dealer_pay_stats_60.amount_sum as amount3',
                'dealer_pay_stats_61.amount_sum as amount4', DB::raw('SUM(challan_order.remaining) as total_remaining'))
                ->get();
//            dd($data);

            return view('reports.aging.ajax', [
                'records' => $data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function distributorStockStatusReport(Request $request)
    {
        if ($request->ajax() && !empty($request->distributor) && !empty($request->month)) {
            $month = date('Y-m-d', strtotime($request->month));

//            echo $month;die;
            $distributor = $request->distributor;

            $arr = [];

            $query = Catalog1::leftJoin('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
                ->leftJoin('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
//                ->leftJoin('daily_stock', 'daily_stock.product_id', '=', 'catalog_product.id')
                ->select(DB::raw("(select dealer_balance_stock.stock_qty from dealer_balance_stock WHERE dealer_id=$distributor AND DATE_FORMAT(submit_date_time,'%Y%m') =DATE_FORMAT(('$month' - INTERVAL 1 MONTH),'%Y%m') AND dealer_balance_stock.product_id=catalog_product.id ORDER BY id DESC LIMIT 0,1) as opening_stock"),
                    DB::raw("(select sum(user_primary_sales_order_details.quantity) from user_primary_sales_order left join user_primary_sales_order_details on user_primary_sales_order_details.order_id=user_primary_sales_order.order_id WHERE user_primary_sales_order.dealer_id=$distributor AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y%m') =DATE_FORMAT('$month','%Y%m') AND user_primary_sales_order_details.product_id=catalog_product.id LIMIT 0,1) as primary_sale"),
                    DB::raw("(select sum(user_sales_order_details.quantity) from user_sales_order left join user_sales_order_details on user_sales_order_details.order_id=user_sales_order.order_id WHERE user_sales_order.dealer_id=$distributor AND DATE_FORMAT(user_sales_order.date,'%Y%m') =DATE_FORMAT('$month','%Y%m') AND user_sales_order_details.product_id=catalog_product.id LIMIT 0,1) as ss"),
                    'catalog_1.name as c1', 'catalog_2.name as c2', 'catalog_product.name as product',
                    'catalog_product.id as cpid', 'catalog_product.base_price')
                ->get();

            foreach ($query as $k => $data) {
                $arr[$k]['cpid'] = $data->cpid;
                $arr[$k]['c1'] = $data->c1;
                $arr[$k]['c2'] = $data->c2;
                $arr[$k]['product'] = $data->product;
                $arr[$k]['pr_rate'] = $data->base_price;
                $arr[$k]['opening'] = !empty($data->opening_stock) ? $data->opening_stock : 0;
                $arr[$k]['primary_sale'] = !empty($data->primary_sale) ? $data->primary_sale : 0;
                $arr[$k]['seconday_sale'] = !empty($data->ss) ? $data->ss : 0;
            }

            return view('reports.distributor-stock-status.ajax', [
                'records' => $arr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function stockInHandReport(Request $request)
    {
        if ($request->ajax() && !empty($request->month)) {
            $month = date('Y-m-d', strtotime($request->month));

            $m1 = strtotime('-1 month', strtotime($request->month));
            $m2 = strtotime('-2 month', strtotime($request->month));
            $m3 = strtotime('-3 month', strtotime($request->month));

            $mn1 = date('M-Y', $m1);
            $mn2 = date('M-Y', $m2);
            $mn3 = date('M-Y', $m3);

            $mf1 = date('Y-m', $m1);
            $mf2 = date('Y-m', $m2);
            $mf3 = date('Y-m', $m3);

            $region = $request->region;
            $area = $request->area;
            $territory = $request->territory;
            $belt = $request->belt;


            if (!empty($belt)) {
                $subq = 'l5 IN(' . implode(',', $belt) . ')';
            } elseif (!empty($territory)) {
                $subq = 'l4 IN(' . implode(',', $territory) . ')';
            } elseif (!empty($area)) {
                $subq = 'l3 IN(' . implode(',', $area) . ')';
            } elseif (!empty($area)) {
                $subq = 'l3 IN(' . implode(',', $region) . ')';
            } else {
                $subq = '1=1';
            }

            $query = DB::table('catalog_0')
                ->leftJoin('catalog_1', 'catalog_1.catalog_0_id', '=', 'catalog_0.id')
                ->leftJoin('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
                ->leftJoin('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
                ->select('catalog_0.name as catalog_name', 'catalog_product.name as sku', 'catalog_product.id')
                ->get();

            $cal = [];
            foreach ($query as $k => $data) {
                $cal[$k] = DB::table('secondary_sale as ss')
                    ->select(DB::raw("(select sum(rate*quantity) from secondary_sale WHERE product_id='$data->id' and DATE_FORMAT(date,'%Y-%m')='$mf3' and $subq) as m3"),
                        DB::raw("(select sum(rate*quantity) from secondary_sale WHERE product_id='$data->id' and DATE_FORMAT(date,'%Y-%m')='$mf2' and $subq) as m2"),
                        DB::raw("(select sum(rate*quantity) from secondary_sale WHERE product_id='$data->id' and DATE_FORMAT(date,'%Y-%m')='$mf1' and $subq) as m1"))
                    ->first();
//                if ($data->id=='146')
//                {
//                    dd($cal[$k]);
//                }
            }
//            dd($cal);

//            $query = [];
            return view('reports.stock-in-hand.ajax', [
                'records' => $query,
                'm1' => $mn1,
                'm2' => $mn2,
                'm3' => $mn3,
                'cal' => $cal
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function getTownDistributor(Request $request)
    {
        if ($request->ajax() && !empty($request->id)) {
            $data['code'] = 200;
            $data['result'] = DealerLocation::leftJoin('dealers', 'dealers.id', '=', 'dealer_locations.dealer_id')
                ->where('town', $request->id)->select('dealers.id', 'dealers.name')->pluck('name', 'id');
            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function primarySecondarySales(Request $request)
    {
        if ($request->ajax() && !empty($request->distributor) && !empty($request->from_date) && !empty($request->to_date)) {
            $catalog = DB::table('catalog_0')->orderBy('sequence')->get();
            $query = DB::table('catalog_0')
                ->leftJoin('catalog_1', 'catalog_1.catalog_0_id', '=', 'catalog_0.id')
                ->leftJoin('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
                ->leftJoin('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
                ->select('catalog_0.name as cat_name', 'catalog_0.id as catid', 'catalog_product.name as sku', 'catalog_product.id', 'catalog_product.base_price')
                ->get();
            $dataArr = [];
            foreach ($query as $cat) {
                $dataArr[$cat->catid][] = $cat;
            }
            return view('reports.month-s-primary-and-secondary-sales-plan.ajax', [
                'catalog' => $catalog,
                'rows' => $dataArr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function ucdpReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region) && ($request->area) && ($request->territory) && !empty($request->month)) {
            $query = [];
            return view('reports.ucdp.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function boardReviewReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region) && ($request->area) && ($request->territory) && !empty($request->month)) {
            $query = [];
            return view('reports.board_review.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function rwssr(Request $request)
    {
        if ($request->ajax() && !empty($request->region) && ($request->area) && ($request->territory) && !empty($request->belt)) {
            $query = [];
            return view('reports.rs-wise-secondary-sales.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function isrSoTgtMonthReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $last_year = date('Y') - 1;
            $last_year_same_month = $last_year . '-' . date('m');
            $last_month_from_filter = date('M y', strtotime($to_date));
            $first_month_from_filter = date('M y', strtotime($from_date));

            $users = $request->user;

            $isr_so_role = [];
            $isr_so_role = DB::table('_role')
                ->where('filter', 1)
                ->pluck('role_id');

            $user = DB::table('person')
                ->leftJoin('monthly_tour_program', 'monthly_tour_program.person_id', '=', 'person.id')
                ->leftJoin('position_master', 'person.position_id', '=', 'position_master.id')
                ->leftJoin('location_3', 'location_3.id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #user wise filter
            if (!empty($users)) {
                $user->whereIn('person.id', $users);
            }
            #role wise filter
            if (!empty($isr_so_role)) {
                $user->whereIn('person.role_id', $isr_so_role);
            }

            $user_data = $user->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') <='$to_date'")
                ->select('person.id as pid', '_role.rolename', 'location_3.name as state', 'position_master.name as position_name', 'person.first_name', 'person.middle_name', 'person.last_name', 'person.position_id', DB::raw('SUM(monthly_tour_program.rd) as total_rd'), DB::raw('SUM(monthly_tour_program.arch) as total_arch'),
                    DB::raw("(select sum(arch) from monthly_tour_program WHERE DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m')=$last_year_same_month) as lysm"),
                    DB::raw('SUM(monthly_tour_program.collection) as total_collection'), DB::raw('SUM(monthly_tour_program.primary_ord) as total_primary_ord'))
                ->groupBy('monthly_tour_program.person_id', 'person.first_name', 'person.middle_name', 'person.last_name')
                ->get();

            $last_month_user = DB::table('person')
                ->leftJoin('monthly_tour_program', 'monthly_tour_program.person_id', '=', 'person.id')
                ->leftJoin('position_master', 'person.position_id', '=', 'position_master.id')
                ->leftJoin('location_3', 'location_3.id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #user wise filter
            if (!empty($users)) {
                $user->whereIn('person.id', $users);
            }
            #role wise filter
            if (!empty($isr_so_role)) {
                $last_month_user->whereIn('person.role_id', $isr_so_role);
            }
            $last_month_user_data = $last_month_user->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') ='$to_date'")
                ->select('person.id as pid', DB::raw('SUM(monthly_tour_program.rd) as total_rd'), DB::raw('SUM(monthly_tour_program.arch) as total_arch'),
                    DB::raw("(select sum(arch) from monthly_tour_program WHERE DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m')=$last_year_same_month) as lysm"),
                    DB::raw('SUM(monthly_tour_program.collection) as total_collection'), DB::raw('SUM(monthly_tour_program.primary_ord) as total_primary_ord'))
                ->groupBy('monthly_tour_program.person_id', 'person.first_name', 'person.middle_name', 'person.last_name')
                ->get();
            $arr = [];
            $myArr = [];
//            dd($last_month_user_data);
            if (!empty($last_month_user_data)) {
                foreach ($last_month_user_data as $lmu) {
                    $arr[$lmu->pid] = $lmu;
                    $myArr = $arr;
                }
            }

            return view('reports.isr-so-tgt-month.ajax', [
                'records' => $user_data,
                'last_month_from_filter' => $last_month_from_filter,
                'first_month_from_filter' => $first_month_from_filter,
                'last_month_user' => $myArr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function salesTrendsReport(Request $request)
    {
        if ($request->ajax() && !empty($request->year)) {
            $monthArr = array('APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEPT', 'OCT', 'NOV', 'DEC', 'JAN', 'FEB', 'MAR');
            $query = [];
            return view('reports.sales-trends.ajax', [
                'records' => $query,
                'monthArr' => $monthArr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function outletOpeningStatusReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region)) {
            $query = [];
            return view('reports.outlet-opening-status.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function salesReviewReport(Request $request)
    {
        if ($request->ajax() && !empty($request->region)) {
            $query = [];
            return view('reports.sales-review.ajax', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    function getList($parentId)
    {
        $retVal = array($parentId);

        $query = DB::table('position_master')->where('senior_position', $parentId)->pluck('id');
        foreach ($query as $key => $data) {
            $retVal = array_merge($retVal, $this->getList($data));
        }

        return $retVal;
    }

    public function competitivePriceIntelligenceReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $position = $request->position;
            $temp = [];
            $l_ids = [];

            #location based user
            if (!empty($zone) || !empty($region) || !empty($state)) {
                $location = DB::table('location_view');
                if (!empty($zone)) {
                    $location->whereIn('l1_id', $zone);
                }
                if (!empty($region)) {
                    $location->whereIn('l2_id', $region);
                }
                if (!empty($state)) {
                    $location->whereIn('l3_id', $state);
                }
                $loc_ids = $location->pluck('l3_id');

                if (!empty($loc_ids)) {
                    $l_ids = DB::table('person')->whereIn('state_id', $loc_ids)->pluck('id');
                }
            }

            #Position cal
            // if (!empty($position)) {
            //     foreach ($position as $pd) {
            //         $temp = $this->getList($pd);
            //     }
            // }
            // $junior = !empty($temp) ? array_unique($temp) : array();

            //     $user_ids = [];
            //     if (!empty($junior)) {
            //         $user_ids = DB::table('person')->whereIn('position_id', $junior)->pluck('id')->to_array();
            //      $unique_ids = !empty($user_ids) ? array_unique($user_ids) : '';
            //   // $unique_ids = !empty($Arr) ? array_unique($Arr) : '';
            //     }

            $query = DB::table('competitive_price_intelligence');
            if (!empty($unique_ids)) {
                $query->whereIn('user_id', $unique_ids);
            }
            if (!empty($request->user)) {
                $u = $request->user;
                $query->whereIn('user_id', $u);
            }
            if (!empty($l_ids)) {
                $u = $request->user;
                $query->whereIn('user_id', $l_ids);
            }

            $query_data = $query->whereRaw("DATE_FORMAT(competitive_price_intelligence.cur_date_time,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(competitive_price_intelligence.cur_date_time,'%Y-%m-%d') <='$to_date'")
                ->orderBy('cur_date_time', 'DESC')
                ->get();
            return view('reports.competitive-price-intelligence.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    public function getZoneUser(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $location = DB::table('location_view');
            if ($request->id != 'null') {
                $ids = explode(',', $request->id);
                $location->whereIn('l1_id', $ids);
            }
            $data['region'] = $location->pluck('l2_name', 'l2_id');
            $data['state'] = $location->pluck('l3_name', 'l3_id');

            $state_ids = $location->pluck('l3_id');

            if (!empty($state_ids)) {
                $data['user'] = DB::table('person')
                    ->whereIn('state_id', $state_ids)
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function getRegionUser(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $location = DB::table('location_view');
            if ($request->id != 'null') {
                $ids = explode(',', $request->id);
                $location->whereIn('l2_id', $ids);
            }
            $data['region'] = $location->pluck('l2_name', 'l2_id');
            $data['state'] = $location->pluck('l3_name', 'l3_id');

            $state_ids = $location->pluck('l3_id');

            if ($request->id != 'null') {
                $data['user'] = DB::table('person')
                    ->whereIn('state_id', $state_ids)
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function getStateUser(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;

            if (!empty($request->id != 'null')) {
                $state_ids = explode(',', $request->id);
            }

            if (!empty($state_ids)) {
                $data['user'] = DB::table('person')
                    ->whereIn('state_id', $state_ids)
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $data['user'] = DB::table('person')
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }

            $data['message'] = 'success';
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    #get state wise isr and so
    public function getStateIsrsSo(Request $request)
    {
        if ($request->ajax()) {
            $data['code'] = 200;
            $state_id = $request->id;
            $isr_so_role = DB::table('_role')
                ->where('filter', 1)
                ->pluck('role_id');
            if ($request->id != 'null') {
                $ids = explode(',', $state_id);
                $data['user'] = DB::table('person')
                    ->whereIn('role_id', $isr_so_role)
                    ->whereIn('state_id', $ids)
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            } else {
                $isr_so_role = [];
                $isr_so_role = DB::table('_role')
                    ->where('filter', 1)
                    ->pluck('role_id');
                $data['user'] = DB::table('person')
                    ->whereIn('role_id', $isr_so_role)
                    ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                    ->where('person.first_name', '!=', '')
                    ->pluck('name', 'uid');
            }
        } else {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function feedbackReport(Request $request)
    {
        if ($request->ajax() && !empty($request->user)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;


            $query = DB::table('feedbackSuggestion');

            $query_data = $query->orderBy('cur_date_time', 'DESC')
                ->where('user_id', $user_id)
                ->get();

            return view('reports.feedback.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }


    }

//    public function dailyPerformanceReport(Request $request)
//    {
//        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
//            $from = $request->from_date;
//            $to = $request->to_date;
//            $query = [];
//
//            #Working Status master data
//            $working_status = DB::table('_working_status')
//                ->pluck('name', 'id');
//
//            #Catalog2 master data
//            $catalog = DB::table('catalog_0')
//                ->pluck('catalog_0.name', 'catalog_0.id');
//
//            $awsome_query = MonthlyTourProgram::leftJoin('location_view', 'location_view.l5_id', '=', 'monthly_tour_program.locations')
//                ->leftJoin('dealer', 'dealer.id', '=', 'monthly_tour_program.dealer_id')
//                ->leftJoin('person', 'monthly_tour_program.person_id', 'person.id')
//                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('daily_reporting', function ($join) use ($request) {
//                    $join->on('daily_reporting.user_id', '=', 'person.id');
//                    $join->on(DB::raw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
//                })
//                ->leftJoin('user_daily_attendance', function ($join2) use ($request) {
//                    $join2->on('user_daily_attendance.user_id', '=', 'person.id');
//                    $join2->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
//                })
//                ->leftJoin('check_out', function ($join3) use ($request) {
////                    $join3->on('check_out.user_id', '=', 'person.id');
////                    $join3->on(DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
//                    $join3->on('check_out.user_id', '=', 'user_daily_attendance.user_id');
//                    $join3->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"));
//                })
//                ->leftJoin('location_5', 'monthly_tour_program.locations', '=', 'location_5.id');
//            #Region filter
//            if (!empty($request->region)) {
//                $region = $request->region;
//                $awsome_query->whereIn('location_view.l2_id', $region);
//            }
//            #State filter
//            if (!empty($request->area)) {
//                $state = $request->area;
//                $awsome_query->whereIn('location_view.l3_id', $state);
//            }
//            #Town filter
//            if (!empty($request->territory)) {
//                $town = $request->territory;
//                $awsome_query->whereIn('location_view.l4_id', $town);
//            }
//            #Beat filter
//            if (!empty($request->belt)) {
//                $beat = $request->belt;
//                $awsome_query->whereIn('location_view.l5_id', $beat);
//            }
//            #Dealer filter
//            if (!empty($request->distributor)) {
//                $distributor = $request->distributor;
//                $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
//            }
//            #Role filter
//            if (!empty($request->role)) {
//                $role_id = $request->role;
//                $awsome_query->whereIn('person.role_id', $role_id);
//            }
//            #User Filter
//            if (!empty($request->user)) {
//                $ud = $request->user;
//                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
//            }
//            $awsome_query->select(DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=user_daily_attendance.user_id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id asc limit 0,1) as first_call1"),
//                DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=user_daily_attendance.user_id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id desc limit 0,1) as last_call"),
//                DB::raw("(select sum(user_sales_order.total_sale_value) from user_sales_order WHERE user_sales_order.user_id=monthly_tour_program.person_id AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')=DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')) as total_sale_value"),
//                'person.id as pid', 'user_daily_attendance.work_status', 'check_out.work_date as checkout_time', 'user_daily_attendance.work_date as attendance_time', 'monthly_tour_program.working_date', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', 'daily_reporting.working_with', 'location_view.l5_name as beat_name', 'location_view.l4_name as town_name')
//                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d')>='$from' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to'");
//            $query = $awsome_query->orderBy('monthly_tour_program.working_date')->get();
//
//            $sa = [];
//            $beatArr = [];
//            $townArr = [];
//            $dealer_name = [];
//            $visit = [];
//            $productive = [];
//            $total_outlet = [];
//            $new_outlet = [];
//            $rv = [];
//            if (!empty($query)) {
//
//                foreach ($query as $key => $data) {
//                    #Total weight
//                    $j = DB::table('user_sales_order')
//                        ->leftJoin('user_sales_order_details', 'user_sales_order_details.order_id', '=', 'user_sales_order.order_id')
//                        ->leftJoin('catalog_product', 'catalog_product.id', '=', 'user_sales_order_details.product_id')
//                        ->where('user_id', $data->pid)
//                        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")
//                        ->select('catalog_product.weight', 'user_sales_order_details.case_qty', 'user_sales_order_details.quantity')
//                        ->get();
//                    $gm = [];
//                    foreach ($j as $jd) {
//                        $q1 = $jd->case_qty;
//                        $q2 = $jd->quantity;
//                        $w = $jd->weight;
//                        $gm[] = ($q1 + ($q2 * 12)) * $w;
//                    }
//                    $rv[$key] = (!empty($gm) ? array_sum($gm) : 0) / 1000;
//                    ####
//                    $k = DB::table('user_sales_order')
//                        ->leftJoin('location_view', 'location_view.l5_id', '=', 'user_sales_order.location_id')
//                        ->leftJoin('dealer', 'dealer.id', '=', 'user_sales_order.dealer_id')
//                        ->where('user_id', $data->pid)
//                        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")
//                        ->select('location_id', 'location_view.l5_name', 'location_view.l4_name', 'user_sales_order.dealer_id', 'dealer.name as dealer_name')
//                        ->distinct();
//                    $sa[$key] = $k->get();
//                    $beat_ids = [];
//                    if (!empty($sa[$key])) {
//                        foreach ($sa[$key] as $sk) {
//                            $beatArr[$key][] = $sk->l5_name;
//                            $townArr[$key][] = $sk->l4_name;
//                            $dealer_name[$key][] = $sk->dealer_name;
//                            $visit[$key] = $k->count();
//                            $productive[$key] = $k->where('call_status', 1)->count();
//                            $beat_ids[] = $sk->location_id;
//                        }
//                    }
//                    if (!empty($beat_ids)) {
//                        $outlet = DB::table('retailer')
//                            ->whereIn('location_id', $beat_ids);
//                        $total_outlet[$key] = $outlet->count();
//                        $new_outlet[$key] = $outlet->whereRaw("DATE_FORMAT(created_on, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")->count();
//                    } else {
//                        $total_outlet[$key] = 0;
//                    }
//
//                    #catalog product wise cal
//                    $arr = [];
//                    foreach ($catalog as $k => $dat) {
//                        $temp = DB::table('catalog_0')
//                            ->leftJoin('catalog_1', 'catalog_1.catalog_0_id', '=', 'catalog_0.id')
//                            ->leftJoin('catalog_2', 'catalog_2.catalog_1_id', '=', 'catalog_1.id')
//                            ->leftJoin('catalog_product', 'catalog_product.catalog_id', '=', 'catalog_2.id')
//                            ->leftJoin('user_sales_order_details', 'user_sales_order_details.product_id', '=', 'catalog_product.id')
//                            ->leftJoin('user_sales_order', 'user_sales_order.order_id', '=', 'user_sales_order_details.order_id')
//                            ->where('catalog_2.id', $k)
//                            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')");
//                        $temp_counter = $temp->select('catalog_product.weight', 'user_sales_order_details.case_qty', 'user_sales_order_details.quantity', 'user_sales_order.total_sale_value');
//                        $arr[$key] = $temp_counter->get();
//                        $gmm = [];
//                        $sal = [];
//                        $count = $temp_counter->count();
//                        foreach ($arr[$key] as $jd) {
//                            $q1 = $jd->case_qty;
//                            $q2 = $jd->quantity;
//                            $w = $jd->weight;
//                            $gmm[] = ($q1 + ($q2 * 12)) * $w;
//                            $sal[] = $jd->total_sale_value;
//                        }
//                        $rv_product[$key][$k]['rd'] = (!empty($gmm) ? array_sum($gmm) : 0) / 1000;
//                        $rv_product[$key][$k]['calls'] = !empty($count) ? $count : 0;
//                        $rv_product[$key][$k]['sale'] = !empty($sal) ? array_sum($sal) : 0;
//                    }
//                }
//            }
//
//
//            return view('reports.daily-performance.ajax  ', [
//                'records' => $query,
//                'working_status' => $working_status,
//                'actual_beat' => $sa,
//                'beatArr' => $beatArr,
//                'townArr' => $townArr,
//                'dealer_name' => $dealer_name,
//                'visit_count' => $visit,
//                'productive_call' => $productive,
//                'total_outlet' => $total_outlet,
//                'new_outlet' => $new_outlet,
//                'rv' => $rv,
//                'catalog' => $catalog,
//                'rv_product' => $rv_product
//            ]);
//        } else {
//            echo '<p class="alert-danger">Do not hack the system</p>';
//        }
//    }
#with total visiting outlet count
//    public function dailyPerformanceReport(Request $request)
//    {
//        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
//            $from = $request->from_date;
//            $to = $request->to_date;
//            $query = [];
//
//            #Working Status master data
//            $working_status = DB::table('_working_status')
//                ->pluck('name', 'id');
//
//            #Catalog2 master data
//            $catalog = DB::table('catalog_0')
//                ->orderBy('sequence')
//                ->pluck('catalog_0.name', 'catalog_0.id');
//
//            $awsome_query = MonthlyTourProgram::leftJoin('location_view', 'location_view.l5_id', '=', 'monthly_tour_program.locations')
//                ->leftJoin('dealer', 'dealer.id', '=', 'monthly_tour_program.dealer_id')
//                ->leftJoin('person', 'monthly_tour_program.person_id', 'person.id')
//                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('daily_reporting', function ($join) use ($request) {
//                    $join->on('daily_reporting.user_id', '=', 'person.id');
//                    $join->on(DB::raw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
//                })
//                ->leftJoin('user_daily_attendance', function ($join2) use ($request) {
//                    $join2->on('user_daily_attendance.user_id', '=', 'person.id');
//                    $join2->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
//                })
//                ->leftJoin('check_out', function ($join3) use ($request) {
////                    $join3->on('check_out.user_id', '=', 'person.id');
////                    $join3->on(DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
//                    $join3->on('check_out.user_id', '=', 'user_daily_attendance.user_id');
//                    $join3->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"));
//                })
//                ->leftJoin('location_5', 'monthly_tour_program.locations', '=', 'location_5.id');
//            #Region filter
//            if (!empty($request->region)) {
//                $region = $request->region;
//                $awsome_query->whereIn('location_view.l2_id', $region);
//            }
//            #State filter
//            if (!empty($request->area)) {
//                $state = $request->area;
//                $awsome_query->whereIn('location_view.l3_id', $state);
//            }
//            #Town filter
//            if (!empty($request->territory)) {
//                $town = $request->territory;
//                $awsome_query->whereIn('location_view.l4_id', $town);
//            }
//            #Beat filter
//            if (!empty($request->belt)) {
//                $beat = $request->belt;
//                $awsome_query->whereIn('location_view.l5_id', $beat);
//            }
//            #Dealer filter
//            if (!empty($request->distributor)) {
//                $distributor = $request->distributor;
//                $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
//            }
//            #Role filter
//            if (!empty($request->role)) {
//                $role_id = $request->role;
//                $awsome_query->whereIn('person.role_id', $role_id);
//            }
//            #User Filter
//            if (!empty($request->user)) {
//                $ud = $request->user;
//                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
//            }
//            $awsome_query->select(DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=user_daily_attendance.user_id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id asc limit 0,1) as first_call1"),
//                DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=user_daily_attendance.user_id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id desc limit 0,1) as last_call"),
//                DB::raw("(select sum(user_sales_order.total_sale_value) from user_sales_order WHERE user_sales_order.user_id=monthly_tour_program.person_id AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')=DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')) as total_sale_value"),
//                'person.id as pid', 'user_daily_attendance.work_status', 'check_out.work_date as checkout_time', 'user_daily_attendance.work_date as attendance_time', 'monthly_tour_program.working_date', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', 'daily_reporting.working_with', 'location_view.l5_name as beat_name', 'location_view.l4_name as town_name', 'person.head_quar', 'person.emp_code', 'location_view.l1_name', 'location_view.l2_name', 'location_view.l3_name')
//                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d')>='$from' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to'");
//            $query = $awsome_query->orderBy('monthly_tour_program.working_date')->get();
//
//            $sa = [];
//            $beatArr = [];
//            $townArr = [];
//            $dealer_name = [];
//            $visit = [];
//            $productive = [];
//            $total_outlet = [];
//            $new_outlet = [];
//            $rv = [];
//            $new_arr = [];
//            if (!empty($query)) {
//                foreach ($query as $key => $data) {
//                    if (!empty($catalog)) {
//                        $ww = [];
//                        foreach ($catalog as $cat_key => $cat_data) {
//                            $new_arr[$key][$cat_key] = $temp = DB::table('secondary_sale')
//                                ->where('user_id', $data->pid)
//                                ->where('c0_id', $cat_key)
//                                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")
//                                ->select(DB::raw("SUM(quantity*rate) as total_price"), DB::raw("SUM(quantity*weight) as total_weight"), DB::raw("COUNT(Distinct order_id) as total_row"))
//                                ->first();
//                            $ww[] = $temp->total_weight;
//                        }
//                        $rv[$key] = (!empty($ww) ? array_sum($ww) : 0) / 1000;
//                    }
//                    $k = DB::table('user_sales_order')
//                        ->leftJoin('location_view', 'location_view.l5_id', '=', 'user_sales_order.location_id')
//                        ->leftJoin('dealer', 'dealer.id', '=', 'user_sales_order.dealer_id')
//                        ->where('user_id', $data->pid)
//                        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")
//                        ->select('location_id', 'location_view.l5_name', 'location_view.l4_name', 'user_sales_order.dealer_id', 'dealer.name as dealer_name')
//                        ->distinct();
//                    $sa[$key] = $k->get();
//                    $beat_ids = [];
//                    if (!empty($sa[$key])) {
//                        foreach ($sa[$key] as $sk) {
//                            $beatArr[$key][] = $sk->l5_name;
//                            $townArr[$key][] = $sk->l4_name;
//                            $dealer_name[$key][] = $sk->dealer_name;
//                            $visit[$key] = $k->count();
//                            $productive[$key] = $k->where('call_status', 1)->count();
//                            $beat_ids[] = $sk->location_id;
//                        }
//                    }
//                    if (!empty($beat_ids)) {
//                        $$beat_ids=array_unique($beat_ids);
//                        $outlet = DB::table('retailer')
//                            ->whereIn('location_id', $beat_ids);
//                        $total_outlet[$key] = $outlet->count();
//                        $new_outlet[$key] = $outlet->whereRaw("DATE_FORMAT(created_on, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")->count();
//                    } else {
//                        $total_outlet[$key] = 0;
//                    }
//                }
//            }
//
//
//            return view('reports.daily-performance.ajax  ', [
//                'records' => $query,
//                'working_status' => $working_status,
//                'actual_beat' => $sa,
//                'beatArr' => $beatArr,
//                'townArr' => $townArr,
//                'dealer_name' => $dealer_name,
//                'visit_count' => $visit,
//                'productive_call' => $productive,
//                'total_outlet' => $total_outlet,
//                'new_outlet' => $new_outlet,
//                'rv' => $rv,
//                'catalog' => $catalog,
//                'new_arr' => $new_arr
////                'rv_product' => $rv_product
//            ]);
//        } else {
//            echo '<p class="alert-danger">Do not hack the system</p>';
//        }
//    }

#Date Rage Array
    function createDateRangeArray($strDateFrom, $strDateTo)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.

        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    public function dailyPerformanceReportv2(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {
            $from = $request->from_date;
            $to = $request->to_date;

            $person_data = DB::table('person')
                ->leftJoin('_role', 'person.role_id', '=', '_role.role_id')
                ->where('id', '>', 1)
                ->select('person.*', '_role.rolename')
                ->get();

            $dateArr = $this->createDateRangeArray($from, $to);

            $query = [];

            #Working Status master data
            $working_status = DB::table('_working_status')
                ->pluck('name', 'id');

            #Catalog2 master data
            $catalog = DB::table('catalog_0')
                ->orderBy('sequence')
                ->pluck('catalog_0.name', 'catalog_0.id');

            $awsome_query = MonthlyTourProgram::leftJoin('location_view', 'location_view.l5_id', '=', 'monthly_tour_program.locations')
                ->leftJoin('dealer', 'dealer.id', '=', 'monthly_tour_program.dealer_id')
                ->leftJoin('person', 'monthly_tour_program.person_id', 'person.id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
                ->leftJoin('daily_reporting', function ($join) use ($request) {
                    $join->on('daily_reporting.user_id', '=', 'person.id');
                    $join->on(DB::raw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
                })
                ->leftJoin('user_daily_attendance', function ($join2) use ($request) {
                    $join2->on('user_daily_attendance.user_id', '=', 'person.id');
                    $join2->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
                })
                ->leftJoin('check_out', function ($join3) use ($request) {
//                    $join3->on('check_out.user_id', '=', 'person.id');
//                    $join3->on(DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')"));
                    $join3->on('check_out.user_id', '=', 'user_daily_attendance.user_id');
                    $join3->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"));
                })
                ->leftJoin('location_5', 'monthly_tour_program.locations', '=', 'location_5.id');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $awsome_query->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $state = $request->area;
                $awsome_query->whereIn('location_view.l3_id', $state);
            }
            #Town filter
            if (!empty($request->territory)) {
                $town = $request->territory;
                $awsome_query->whereIn('location_view.l4_id', $town);
            }
            #Beat filter
            if (!empty($request->belt)) {
                $beat = $request->belt;
                $awsome_query->whereIn('location_view.l5_id', $beat);
            }
            #Dealer filter
            if (!empty($request->distributor)) {
                $distributor = $request->distributor;
                $awsome_query->whereIn('monthly_tour_program.dealer_id', $distributor);
            }
            #Role filter
            if (!empty($request->role)) {
                $role_id = $request->role;
                $awsome_query->whereIn('person.role_id', $role_id);
            }
            #User Filter
            if (!empty($request->user)) {
                $ud = $request->user;
                $awsome_query->whereIn('monthly_tour_program.person_id', $ud);
            }
            $awsome_query->select(DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=user_daily_attendance.user_id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id asc limit 0,1) as first_call1"),
                DB::raw("(select user_sales_order.time from user_sales_order WHERE user_sales_order.user_id=user_daily_attendance.user_id AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')=DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') order by id desc limit 0,1) as last_call"),
                DB::raw("(select CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) from person WHERE person.id=user_daily_attendance.working_with limit 0,1) as ww"),
                DB::raw("(select sum(user_sales_order.total_sale_value) from user_sales_order WHERE user_sales_order.user_id=monthly_tour_program.person_id AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')=DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')) as total_sale_value"),
                'person.id as pid', 'user_daily_attendance.work_status', 'check_out.work_date as checkout_time', 'user_daily_attendance.work_date as attendance_time', 'monthly_tour_program.working_date', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', 'daily_reporting.working_with', 'location_view.l5_id', 'location_view.l5_name as beat_name', 'location_view.l4_name as town_name', 'person.head_quar', 'person.emp_code', 'location_view.l1_name', 'location_view.l2_name', 'location_view.l3_name')
                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d')>='$from' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to'");
            $query = $awsome_query->orderBy('monthly_tour_program.working_date')->get();

            $sa = [];
            $beatArr = [];
            $townArr = [];
            $dealer_name = [];
            $visit = [];
            $productive = [];
            $total_outlet = [];
            $new_outlet = [];
            $rv = [];
            $new_arr = [];
            $mtp_beat = [];
            $working_beats = [];
            if (!empty($query)) {
                foreach ($query as $key => $data) {
                    if (!empty($catalog)) {
                        $ww = [];
                        foreach ($catalog as $cat_key => $cat_data) {
                            $new_arr[$key][$cat_key] = $temp = DB::table('secondary_sale')
                                ->where('user_id', $data->pid)
                                ->where('c0_id', $cat_key)
                                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")
                                ->select(DB::raw("SUM(quantity*rate) as total_price"), DB::raw("SUM(quantity*weight) as total_weight"), DB::raw("COUNT(Distinct order_id) as total_row"))
                                ->first();
                            $ww[] = $temp->total_weight;
                        }
                        $rv[$key] = (!empty($ww) ? array_sum($ww) : 0) / 1000;
                    }
                    $k = DB::table('user_sales_order')
                        ->leftJoin('location_view', 'location_view.l5_id', '=', 'user_sales_order.location_id')
                        ->leftJoin('dealer', 'dealer.id', '=', 'user_sales_order.dealer_id')
                        ->where('user_id', $data->pid)
                        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")
                        ->select('location_id', 'location_view.l5_name', 'location_view.l4_name', 'user_sales_order.dealer_id', 'dealer.name as dealer_name')
                        ->distinct();
                    $sa[$key] = $k->get();
                    $beat_ids = [];
                    if (!empty($sa[$key])) {
                        foreach ($sa[$key] as $sk) {
                            $beatArr[$key][] = $sk->l5_name;
                            $townArr[$key][] = $sk->l4_name;
                            $dealer_name[$key][] = $sk->dealer_name;
                            $visit[$key] = $k->count();
                            $productive[$key] = $k->where('call_status', 1)->count();
                            $beat_ids[] = $sk->location_id;
                        }
                    }
                    $mtp_beat[$key] = $data->l5_id;
                    if (!empty($data->l5_id)) {
                        $total_outlet[$key] = DB::table('retailer')
                            ->where('location_id', $data->l5_id)
                            ->count();
                    } else {
                        $total_outlet[$key] = 0;
                    }
                    $working_beats[$key] = !empty($beat_ids) ? $beat_ids[0] : '';
                    if (!empty($beat_ids)) {
                        $beat_ids = array_unique($beat_ids);
                        $outlet = DB::table('retailer')
                            ->whereIn('location_id', $beat_ids);
//                        $total_outlet[$key] = $outlet->count();
                        $new_outlet[$key] = $outlet->whereRaw("DATE_FORMAT(created_on, '%Y-%m-%d') = DATE_FORMAT('$data->working_date', '%Y-%m-%d')")->count();
                    } else {
//                        $total_outlet[$key] = 0;
                    }
                }
            }
            $arr = [];
            $personIdArr = [];
            foreach ($query as $kk => $v) {
                $personIdArr[$v->working_date][] = $v->pid;
                $arr[$v->working_date][$v->pid] = $v;
                $rvArr[$v->working_date][$v->pid] = $rv[$kk];
                $newOutletArr[$v->working_date][$v->pid] = !empty($new_outlet[$kk]) ? $new_outlet[$kk] : [];
                $totalOutletArr[$v->working_date][$v->pid] = $total_outlet[$kk];
                $mtpBeatArr[$v->working_date][$v->pid] = $mtp_beat[$kk];
                $actualBeatArr[$v->working_date][$v->pid] = $sa[$kk];
                $beatArr2[$v->working_date][$v->pid] = !empty($beatArr[$kk]) ? $beatArr[$kk] : [];
                $townArr2[$v->working_date][$v->pid] = !empty($townArr[$kk]) ? $townArr[$kk] : [];
                $dealerNameArr[$v->working_date][$v->pid] = !empty($dealer_name[$kk]) ? $dealer_name[$kk] : [];
                $visitArr[$v->working_date][$v->pid] = !empty($visit[$kk]) ? $visit[$kk] : [];
                $productiveArr[$v->working_date][$v->pid] = !empty($productive[$kk]) ? $productive[$kk] : [];
            }
            $cloneArr = [];
            foreach ($dateArr as $date_value) {
                $excludeId = !empty($personIdArr[$date_value]) ? $personIdArr[$date_value] : [];

                $clone = DB::table('person')
                    ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
                if (!empty($excludeId)) {
                    $clone->whereNotIn('person.id', $excludeId);
                }
                #User Filter
                if (!empty($request->user)) {
                    $ud = $request->user;
                    $awsome_query->whereIn('person.id', $ud);
                }
                $clone->leftJoin('user_daily_attendance', function ($join2) use ($date_value) {
                    $join2->on('user_daily_attendance.user_id', '=', 'person.id');
                    $join2->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw($date_value));
                })
                    ->leftJoin('check_out', function ($join3) use ($request) {
                        $join3->on('check_out.user_id', '=', 'user_daily_attendance.user_id');
                        $join3->on(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')"), '=', DB::raw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')"));
                    })
                    ->select('person.id as pid', 'user_daily_attendance.work_status', 'check_out.work_date as checkout_time', 'user_daily_attendance.work_date as attendance_time', 'person.first_name', 'person.middle_name', 'person.last_name', '_role.rolename', 'person.head_quar', 'person.emp_code');
                $clone_result = $clone->get();
                foreach ($clone_result as $cl)
                {
                    $cloneArr[$date_value][$cl->pid] = $cl;
                }
            }

            return view('reports.daily-performancev2.ajax  ', [
                'records' => $query,
                'working_status' => $working_status,
                'actual_beat' => $sa,
                'beatArr' => $beatArr,
                'townArr' => $townArr,
                'dealer_name' => $dealer_name,
                'visit_count' => $visit,
                'productive_call' => $productive,
                'total_outlet' => $total_outlet,
                'new_outlet' => $new_outlet,
                'rv' => $rv,
                'catalog' => $catalog,
                'working_beats' => $working_beats,
                'mtp_beat' => $mtp_beat,
                'new_arr' => $new_arr,
                'person_data' => $person_data,
                'dateArr' => $dateArr,
                'arr' => $arr,
                'cloneArr' => $cloneArr
//                'rv_product' => $rv_product
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    // GAnesh Work
    public function newSdDistProspectingReport(Request $request)
    {
        //echo"1212";die;
        if ($request->ajax()) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;
            $from = $request->from_date;
            $to = $request->to_date;


            $query = DB::table('daily_prospecting_working');
            if (!empty($user_id)) {
                $query->whereIn('user_id', $user_id);
            }
            if (!empty($from) && !empty($to)) {
                $query->whereRaw("DATE_FORMAT(daily_prospecting_working.cur_date_time,'%Y-%m-%d') >='$from' and DATE_FORMAT(daily_prospecting_working.cur_date_time,'%Y-%m-%d') <='$to'");
            }

            $query_data = $query->orderBy('cur_date_time', 'DESC')
                ->get();
            return view('reports.new-sd-dist-prospecting.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }


    }

    public function productInvestigationReport(Request $request)
    {
        //echo"1212";die;
        if ($request->ajax() && !empty($request->user)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;


            $query = DB::table('product_investigation_report')
                ->where('user_id', $user_id);

            $query_data = $query->orderBy('date_time', 'DESC')
                ->get();
            //  print_r($query_data );die;
            return view('reports.product-investigation.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }


    }

    public function complaintReport(Request $request)
    {

        if ($request->ajax() && !empty($request->user)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;

            $from_date = $request->from_date;
            $to_date = $request->to_date;


            $query = DB::table('Complaint_report')
                ->select('person.emp_code', 'person.head_quar', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'Complaint_report.*')
                ->leftJoin('person', 'person.id', '=', 'Complaint_report.user_id');

            $q = $query->orderBy('created_at', 'DESC');
            if (!empty($user_id)) {
                $q->where('user_id', $user_id);
            }
            if (!empty($from_date) && !empty($to_date)) {
                $q->whereRaw("DATE_FORMAT(Complaint_report.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(Complaint_report.date,'%Y-%m-%d') <='$to_date'");
            }
            $query_data = $q->get();
            // print_r($query_data );die;
            return view('reports.complaint-report.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system123</p>';
        }


    }

    public function paymentDetailsReport(Request $request)
    {

        if ($request->ajax()) {

            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;

            $user = $request->user;
            $date = $request->date;
            $arr = [1 => 'cash', 2 => 'Cheque', 3 => 'NEFT/RGTDS', 4 => 'Demand Draft'];


            $query_data = DB::table('dealer_payments')
                ->select('drawn_from_bank', 'deposited_bank', 'invoice_number', 'person.emp_code', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'), 'dealer.name as dealer_name', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                    'location_view.l4_name as town_name', 'dealer_payments.*')
                ->join('person', 'person.id', '=', 'dealer_payments.user_id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_payments.dealer_id')
                ->leftJoin('location_view', 'location_view.l4_id', '=', 'dealer_payments.town');
//                ->groupBy('payment_recevied_date', 'user_id','dealer_payments.drawn_from_bank','dealer_payments.deposited_bank','dealer_payments.invoice_number','dealer.name','location_view.l1_name','location_view.l2_name','location_view.l4_name','dealer_payments.id');

            $dealer_beat = DB::table('location_view');


            if (!empty($town)) {

                $dealer_beat->whereIn('l4_id', $town)->pluck('l5_id');
            } //Town Data
            elseif (!empty($state)) {

                $dealer_beat->whereIn('l3_id', $state)->pluck('l5_id');

            } //Beat Data
            elseif (!empty($region)) {
                $tr = [];
                $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l5_id');

            }


            if (!empty($request->distributor)) {

                $tr = [];
                $tr = $request->distributor;
            }

            if (!empty($tr)) {
                $query_data->whereIn('dealer_payments.dealer_id', $tr);
            }

// To find No of user under certain role
            $flag = [];


            if (!empty($user)) {

                $flag = [];
                $flag = $request->user;

            }


            if (!empty($flag)) {
                $query_data->where('dealer_payments.emp_id', $flag);
            }


            if (!empty($date)) {
                $query_data->whereRaw("DATE_FORMAT(payment_recevied_date, '%Y-%m-%d') = '$date'");
            }

            $query = $query_data->get();

            return view('reports.payment-details.ajax', [
                'records' => $query,
                'arr' => $arr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }

    }

//    public function paymentDetailsReport(Request $request)
//    {
//        if ($request->ajax() && !empty($request->user)) {
//
////            $receipt = $request->receipt;
//            $user = $request->user;
//            $date = $request->date;
//            $arr = [1 => 'cash', 2 => 'Cheque', 3 => 'NEFT/RGTDS', 4 => 'Demand Draft'];
//            $data = DB::table('dealer_payments')
//                ->leftJoin('person', 'person.id', '=', 'dealer_payments.user_id')
//                ->leftJoin('location_view', 'location_view.l4_id', '=', 'dealer_payments.town')
//                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id')
//                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_payments.dealer_id');
//            if (!empty($user)) {
//                $data->where('user_id', $user);
//            }
//            if (!empty($date)) {
//                $data->whereRaw("DATE_FORMAT(payment_recevied_date, '%Y-%m-%d') = '$date'");
//            }
////                ->where('dealer_payments.id', $receipt)
//            $data->select('dealer_payments.invoice_number', 'dealer_payments.payment_mode', 'dealer.name as dealer_name', DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'location_view.l1_name as zone', 'dealer_payments.emp_id', 'location_view.l2_name as hq', '_role.rolename', 'dealer.name as dealer_name', 'location_view.l4_name as town', 'dealer_payments.payment_recevied_date', 'dealer_payments.amount');
//            $q1 = $data->get();
//            return view('reports.payment-details.ajax', [
//                'records' => $q1,
//                'arr' => $arr
//            ]);
//        } else {
//            echo '<p class="alert-danger">Do not hack the system</p>';
//        }
//    }

    //Distributer Wise Secondry Sales Trends
    public function distributerWiseSecondarySalesTrendsReport(Request $request)
    {

        if ($request->ajax() && !empty($request->year)) {
            $dealer = $request->dealer;
            $month_with_year = explode('-', $request->year);
            $year = $month_with_year[0];
            $month = $month_with_year[1];

            $one_year_back = $year - 1;
            $two_year_back = $year - 2;

            $month_arr = ['04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03'];

            #Dealer
            $query_data = DB::table('dealer_location_l4');
            if (!empty($dealer)) {
                $query_data->whereIn('id', $dealer);
            }
            $records = $query_data->get();

            $arr = [];
            foreach ($records as $k => $d) {
                foreach ($month_arr as $mk => $md) {
                    if ($md < 4) {
                        $year = $year + 1;
                    }
                    $query = DB::table('user_sales_order_view')->where('dealer_id', $d->id);
                    $data = $query;
                    $d1 = $year . '-' . $md;
                    $d2 = ($year - 1) . '-' . $md;
                    $d3 = ($year - 2) . '-' . $md;
                    $arr[$d->id]['f1'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d1'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    $arr[$d->id]['f2'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d2'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    $arr[$d->id]['f3'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d3'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    if ($md < 4) {
                        $year = $year - 1;
                    }
                }
            }

//            dd($arr);

            return view('reports.distributer-wise-sales-trends.ajax', [
                'records' => $records,
                'arr' => $arr,
                'y1' => $year,
                'y2' => $one_year_back,
                'y3' => $two_year_back,
                'month' => $month,
                'monthArr' => $month_arr
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    

    

}
