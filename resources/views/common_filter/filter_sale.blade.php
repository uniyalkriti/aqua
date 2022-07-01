@php
    $var = 'location_3';
    $location3 = App\CommonFilter::comon_data('location_3');
    $location4 = App\CommonFilter::comon_data('location_4');
    $location5 = App\CommonFilter::comon_data('location_5');
    $location6 = App\CommonFilter::comon_data('location_6');
    $location7 = App\CommonFilter::comon_data('location_7');
    $product = App\CommonFilter::comon_data('catalog_product');
    $role = App\CommonFilter::role_name('_role');
    $users = App\CommonFilter::user_filter('person');
    $dealer = App\CommonFilter::dealer_filter('dealer');
@endphp


<?php  
$url = $_SERVER['REQUEST_URI'];
// echo $url;
$required = '';
if($url == '/public/skuWiseCounterSale'){
    $required = "required='required'";
}

?>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            
            
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">{{Lang::get('common.location3')}}</label>
                    <select multiple name="location_3[]" id="location_3" class="form-control chosen-select" {{$required}}>
                        <option disabled="disabled" value="">select</option>
                        @if(!empty($location3))
                            @foreach($location3 as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">{{Lang::get('common.location4')}}</label>
                    <select multiple name="location_4[]" id="location_4" class="form-control chosen-select">
                        <option disabled="disabled" value="">select</option>
                        @if(!empty($location4))
                            @foreach($location4 as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">{{Lang::get('common.location5')}}</label>
                    <select multiple name="location_5[]" id="location_5" class="form-control chosen-select">
                        <option disabled="disabled" value="">select</option>
                        @if(!empty($location5))
                            @foreach($location5 as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">{{Lang::get('common.location6')}}</label>
                    <select multiple name="location_6[]" id="location_6" class="form-control chosen-select">
                        <option disabled="disabled" value="">select</option>
                        @if(!empty($location6))
                            @foreach($location6 as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">User</label>
                    <select multiple name="user[]" id="user" class="form-control chosen-select">
                        <option disabled="disabled" value="">Select</option>
                        @if(!empty($users))
                            @foreach($users as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">Distributor</label>
                    <select multiple name="dealer[]" id="dealer" class="form-control chosen-select">
                        <option disabled="disabled" value="">Select</option>
                        @if(!empty($dealer))
                            @foreach($dealer as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            
        </div>
        <div class="row">
            
            
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">{{Lang::get('common.location7')}}</label>
                    <select multiple name="location_7[]" id="location_7" class="form-control chosen-select">
                        <option disabled="disabled" value="">select</option>
                        @if(!empty($location7))
                            @foreach($location7 as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">Role</label>
                    <select multiple name="role[]" id="role" class="form-control chosen-select" {{$required}}>
                        <option disabled="disabled" value="">Select</option>
                        @if(!empty($role))
                            @foreach($role as $k=>$r)
                                <option value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        
                  

 

                                        