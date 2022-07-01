@php
    $var = 'location_3';
    $location3 = App\CommonFilter::comon_data('location_3');
    $location4 = App\CommonFilter::comon_data('location_4');
    $location5 = App\CommonFilter::comon_data('location_5');
    $location6 = App\CommonFilter::comon_data('location_6');
    $location7 = App\CommonFilter::comon_data('location_7');
    $users = App\CommonFilter::user_filter('person');
    $role = App\CommonFilter::role_name('_role');

@endphp
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            
            
            <div class="col-xs-6 col-sm-6 col-lg-2">
                <div class="">
                    <label class="control-label no-padding-right"
                           for="name">{{Lang::get('common.location3')}}</label>
                    <select multiple name="location_3[]" id="location_3" class="form-control chosen-select">
                        <option disabled="disabled" value="">select</option>
                        @if(!empty($location3))
                            @foreach($location3 as $k=>$r)
                            <?php if(empty($_GET['location_3']))
                                $_GET['location_3']=array();
                            ?>
                                <option @if(in_array($k,$_GET['location_3'])){{"selected"}} @endif value="{{$k}}">{{$r}}</option>
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
                            <?php if(empty($_GET['location_4']))
                                $_GET['location_4']=array();
                            ?>
                                <option @if(in_array($k,$_GET['location_4'])){{"selected"}} @endif value="{{$k}}">{{$r}}</option>
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
                            <?php if(empty($_GET['location_5']))
                                $_GET['location_5']=array();
                            ?>
                                <option @if(in_array($k,$_GET['location_5'])){{"selected"}} @endif value="{{$k}}">{{$r}}</option>
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
                            <?php if(empty($_GET['location_6']))
                                $_GET['location_6']=array();
                            ?>
                                <option @if(in_array($k,$_GET['location_6'])){{"selected"}} @endif value="{{$k}}">{{$r}}</option>
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
                            <?php if(empty($_GET['user']))
                                $_GET['user']=array();
                            ?>
                                <option @if(in_array($k,$_GET['user'])){{"selected"}} @endif value="{{$k}}">{{$r}}</option>
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
                           for="name">Role</label>
                    <select multiple name="role[]" id="role" class="form-control chosen-select">
                        <option disabled="disabled" value="">Select</option>
                        @if(!empty($role))
                            @foreach($role as $k=>$r)
                            <?php if(empty($_GET['role']))
                                $_GET['role']=array();
                            ?>
                                <option @if(in_array($k,$_GET['role'])){{"selected"}} @endif value="{{$k}}">{{$r}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            
        



                                        