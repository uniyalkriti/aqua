@extends('layouts.master')

@section('title')
    <title>User</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <style>
        .modal-lg2{
            width: 1230px;
        }
        .modal-lg3{
            width: 700px;
        }

    </style>
@endsection

@section('body')
    <div class="main-content" style="overflow-x: scroll;">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="#">Users Master</a>
                    </li>

                    <li class="active" style="color: white">@Lang('common.'.$current_menu)</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content" style="padding-top: 0;">
                <form class="form-horizontal open collapse in" action="" method="get" id="user-search" role="form"
                                          enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-lg-12">
                        
                            <div class="col-lg-2 ">
                                <label class="control-label no-padding-right" for="name">Search By Name</label>
                                <div class="input-group" style="cursor: pointer;">
                                    @if(empty(Request::get('search')))
                                     <input type="text" placeholder="Search by Name" id="search"
                                               name="search" value="{{ Request::get('search') }}"
                                             class="form-control input-sm"/>
                                       <span onclick="search()" class="input-group-addon cursor">
                                           <i class="fa fa-search"></i>
                                     </span>
                                   @else
                                     <input type="text" readonly="readonly" placeholder="Search by name or email"
                                             id="search" name="search" value="{{ Request::get('search') }}"
                                               class="form-control input-sm"/>
                                      <span onclick="searchReset();" class="input-group-addon cursor">
                                           <i class="fa fa-times"></i>
                                       </span>
                                    @endif
                                </div>
                                {{-- <input type="text" id="myInput" class = "form-control" onkeyup="myFunction()" placeholder="Search for anything.." title="Type in a name"> --}}
                            </div>
                            <div class="col-lg-2">  
                                <label class="control-label no-padding-right" for="name">Role</label>
                                <select name="role[]" multiple id="role" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($role))
                                        @foreach($role as $rk=>$rr)
                                            <?php if(empty($_GET['role']))
                                                $_GET['role']=array();
                                            ?>
                                            <option @if(in_array($rk,$_GET['role'])){{"selected"}} @endif value="{{$rk}}"> {{$rr}} 
                                            </option>                                                        
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location3')}}</label>
                                <select name="state[]" multiple id="location3" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($state))
                                        @foreach($state as $sk=>$sr) 
                                            <?php if(empty($_GET['state']))
                                                $_GET['state']=array();
                                            ?>
                                          
                                            <option @if(in_array($sk,$_GET['state'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-lg-2">  
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location5')}}</label>
                                <select name="head_quater[]" multiple id="location5" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($head_quater))
                                        @foreach($head_quater as $rk=>$rr)
                                            <?php if(empty($_GET['head_quater']))
                                                $_GET['head_quater']=array();
                                            ?>
                                            <option @if(in_array($rk,$_GET['head_quater'])){{"selected"}} @endif   value="{{$rk}}"> {{$rr}} 
                                            </option>                                                         
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-2">  
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location6')}}</label>
                                <select name="location_6[]" multiple id="location6" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($location_6))
                                        @foreach($location_6 as $rk=>$rr)
                                            <?php if(empty($_GET['location_6']))
                                                $_GET['location_6']=array();
                                            ?>
                                            <option @if(in_array($rk,$_GET['location_6'])){{"selected"}} @endif   value="{{$rk}}"> {{$rr}} 
                                            </option>                                                         
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-2">  
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.user')}} App Version</label>
                                <select name="app_version[]" multiple id="app_version" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($version))
                                        @foreach($version as $rk=>$rr)
                                             <?php if(empty($_GET['app_version']))
                                                $_GET['app_version']=array();
                                            ?>
                                            <option @if(in_array($rk,$_GET['app_version'])){{"selected"}} @endif   value="{{$rk}}"> {{$rr}} 
                                            </option>                                                        
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-2">  
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.user')}}</label>
                                <select name="user[]" multiple id="user" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($user))
                                        @foreach($user as $rk=>$rr)
                                            <?php if(empty($_GET['division']))
                                                $_GET['division']=array();
                                            ?>
                                            <option @if(in_array($rk,$_GET['division'])){{"selected"}} @endif   value="{{$rk}}"> {{$rr}} 
                                            </option>                                                        
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-2">  
                                <label class="control-label no-padding-right" for="name">Status</label>
                                <select name="status"  id="status" class="form-control input-sm ">
                                    <option {{Request::get('status')==1?'selected':''}} value=1>Active</option>                     
                                    <option {{Request::get('status')==2?'selected':''}} value=2>De-Active</option>                                             
                                </select>
                            </div>
                           
                            <div class="col-lg-2">
                                   <select name="perpage" id="perpage" class="form-control cursor input-sm "
                                           onchange="form.submit()" style="margin-top: 28px;">
                                        <option value="">Per Page</option>
                                        <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                        <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                        <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                        <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                        <option {{ Request::get('perpage')==500?'selected':'' }} value="500">500</option>
                                        <option {{ Request::get('perpage')==1000?'selected':'' }} value="1000">1000</option>
                                   </select>
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class=" input-sm btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                    Find
                                </button>
                            </div>
                           
                        
                        </div>
                    </div>
                </form>
                <br> 
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" style="overflow-x: scroll;">
                                <div class="table-header center">
                                    {{Lang::get('common.'.$current_menu)}} Details
                                    <div class="pull-left">
                                        {{-- <a title="Junior Details"  data-toggle="modal" data-target="#employee_salary_form" class="employee_salary_form">
                                            <button type="button" class=" btn btn-sm btn-grey btn-block mg-b-10"><i class="fa fa-plus mg-r-10"></i>
                                                <b class="blink"> Employee Salary Form</b>
                                            </button>
                                        </a> --}}
                                    </div>
                                    <div class="pull-right tableTools-container"></div>
                                   
                                </div>
                                <table id="dynamic-table" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center">
                                            S.No.
                                        </th>
                                        <th>Image</th>
                                        <th>Emp Code</th>
                                        <th>{{Lang::get('common.'.$current_menu)}}</th>
                                        <th class="detail-col">Details</th>
                                        <th>{{Lang::get('common.location3')}}</th>
                                        <th>{{Lang::get('common.location5')}}</th>
                                        <th>{{Lang::get('common.location6')}}</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Role</th>
                                        <th>Senior Name</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Email</th>
                                        <th>Created Date</th>
                                        <th>App Version</th>
                                        <th>IMEI</th>
                                        {{--<th>--}}
                                        {{--<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>--}}
                                        {{--Created On--}}
                                        {{--</th>--}}
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($records as $key=>$data)

                                        <?php 
                                            $encid = Crypt::encryptString($data->id);
                                            $sencid = Crypt::encryptString($data->person_id_senior);
                                            $is_mtp_enabled = $data->is_mtp_enabled ;
                                            $total_beat = 0;
                                            $total_retailer = 0;
                                            $url_image = 'demo.msell.in/demo_api/webservices/mobile_images/profile/'.$data->person_image;
                                            $todayAttandenceMtp = $data->todayMtp($data->id);
                                        ?>
                                        <tr class="search_from_this">
                                            <td class="center">
                                                
                                                {{  $key+1 }}
                                            </td>
                                            <td class="profile-activity clearfix">
                                            <!-- <a class=“cboxElement” href="{{asset('users-profile/')}}{{'/'.$data->person_image}}" data-rel=“colorbox”> -->

                                                  <a id="user_image" class="“cboxElement”" href="#user<?=$key?>" data-toggle="modal" data-rel="“colorbox”">

                                            <img id="user_image" width="50" height="50"  src="{{asset('users-profile/')}}{{'/'.$data->person_image}}" alt=" " />

                                           
                                          </a>
                                            </td>
                                            <td>{{!empty($data->emp_code)?$data->emp_code:'N/A'}}</td>
                                            <td>
                                                <a href="{{url('user/'.$encid)}}">{{$data->first_name.' '.$data->middle_name.' '.$data->last_name}}</a>
                                            </td>
                                            <td class="center">
                                                <div class="action-buttons">
                                                    <a>
                                                        <i class="ace-icon fa fa-angle-double-down"></i>
                                                        <span class="sr-only">Details</span>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{!empty($data->state)?$data->state:'N/A'}}</td>
                                            <td>{{$data->head_quater}}</td>
                                            <td>{{!empty($data->town_name)?$data->town_name:''}}</td>
                                            <td>{{$data->person_username}}</td>
                                            <td>{{$data->person_password}}</td>
                                            <td>{{$data->rolename}}</td>
                                            <td><a href="{{url('user/'.$sencid)}}">{{$data->srname}}</a></td>
                                            <td>{{$data->mobile}}</td>
                                            
                                            <td>{{!empty($data->personaddress)?$data->personaddress:'N/A'}}</td>
                                            <td>{{!empty($data->email)?$data->email:'N/A'}}</td>
                                            <td>{{!empty($data->created_on)?date("d-M-Y H:i:s", strtotime($data->created_on)):'N/A'}}</td>
                                            <td>{{!empty($data->version_code_name)?$data->version_code_name:'N/A'}}</td>
                                            <td id="{{$data->id.'imei'}}">{{!empty($data->imei_number)?$data->imei_number:'N/A'}}</td>

                                            <td class="hidden-480" id="{{$data->id.'status_written'}}">
                                                @if($data->person_status==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                @elseif($data->person_status==9)
                                                    <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                
                                                    
                                                    <!-- for active and inactive -->
                                                    <div id="{{$data->id.'active_incative'}}"> 
                                                        @if($data->person_status==1)
                                                            <a title="Inactive" class="btn btn-xs btn-warning" onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','inactive');">
                                                                <i class="ace-icon fa fa-ban bigger-120"></i>
                                                            </a>
                                                            
                                                        @else
                                                            <a title="Active" class="btn btn-xs btn-success" onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','active');">
                                                                <i class="ace-icon fa fa-check bigger-120"></i>
                                                            </a>
                                                            
                                                        @endif
                                                    </div>
                                                    <!-- for edit  -->
                                                  <!-- for delete -->
                                                    <!-- <button class="btn btn-xs btn-danger"
                                                            onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','delete');">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </button> -->

                                                    
                                                  

                                                     <!-- for attendance with new process  comment because below code use in future so sure-->
                                                    {{--  @if($data->today_att_enabled==1 && $data->today_att_enabled_at==date('Y-m-d'))
                                                    <button id="hi" value="open" class="btn btn-xs btn-primary"
                                                            onclick="MtpAction('{{"Mtp-Enable"}}','{{"Today-Enable"}}','{{$data->id}}','{{$table}}','Disable');" title="Click Here For Today's Attendance Disable" >
                                                        <i class="fa fa-hand-pointer-o aria-hidden="true" " ></i>
                                                    </button>
                                                    @elseif($data->today_att_enabled==2 || $data->today_att_enabled==1 || $data->today_att_enabled_at==date('Y-m-d'))
                                                        <button id="hi" value="close" class="btn btn-xs btn-default"
                                                                onclick="MtpAction('{{"Mtp-Enable"}}','{{"Today-Enable"}}','{{$data->id}}','{{$table}}','Enable');" title="Click Here For Today's Attendance Enable" >
                                                            <i class="ace-icon fa fa-clock-o bigger-120" ></i>
                                                        </button>
                                                    @endif   
                                                    @if($data->is_mtp_enabled==2)
                                                        <button class="btn btn-xs btn-info"
                                                                onclick="MtpAction('{{"Mtp-Enable"}}','{{"Mtp-Enable"}}','{{$data->id}}','{{$table}}','Enable');" title="Click Here For MTP Enable" >
                                                            <i class="ace-icon fa fa-toggle-off bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-xs btn-success" 
                                                                onclick="MtpAction('{{"Mtp-Enable"}}','{{"Mtp-Enable"}}','{{$data->id}}','{{$table}}','Disable');" title="Click Here For MTP Disable">
                                                           <i class="ace-icon fa fa-toggle-on bigger-120"></i>

                                                        </button>
                                                    @endif --}}
                                                    <?php echo $data->user_id;  ?>
                                                     <!-- for assign distributor button -->
                                                 
                                                    <!-- for assign distributor button  -->

                                                </div>

                                                <div class="hidden-md hidden-lg">
                                                    <div class="inline pos-rel">
                                                        <button class="btn btn-minier btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" data-position="auto">
                                                            <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                        </button>
                                                    </div>
                                                 </div>
                                            </td>
                                        </tr>
                                        


                                        <!-- START MODEL BOX -->
                                        <div id="user<?=$key?>" class="modal fade" role="dialog">
                                            <div class="modal-dialog">

                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">User Image</h4>
                                                    </div>

                                                    <div class="modal-body">

                                                        <img class="center19" id="user_image" style="height: 500px;width: 500px;" class="nav-user-photo" 
                                                        src="{{asset('users-profile/')}}{{'/'.$data->person_image}}" 
                                                        onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/avatar2.png')}}';" /> 
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- END MODEL BOX -->




                                    @endforeach

                                    </tbody>
                                </table>
                            </div><!-- /.span -->
                        </div><!-- /.row -->
                        <div class="col-xs-6">
                            <div class="dataTables_info">
                                Showing {{($records->currentpage()-1)*$records->perpage()+1}}
                                to {{(($records->currentpage()-1)*$records->perpage())+$records->count()}}
                                of {{$records->total()}} entries
                            </div>
                        </div>
                        <div class="col-xs-6">
                           <div class="dataTables_paginate paging_simple_numbers">
                           {{$records->appends(request()->except('page'))}}
                           </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

    <!-- distributor Modal starts here  -->
         <div class="modal fade" data-backdrop="static" data-keyboard="false" id="myModal" role="dialog" style="overflow:scroll;">
            <div class="modal-dialog modal-lg2">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header widget-header widget-header-small">
                        {{--<div class="widget-header widget-header-small">--}}
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="smaller">Assign @Lang('common.dealer_module')</h4>
                        {{--</div>--}}
                    </div>
                    <div class="modal-body ui-dialog-content ui-widget-content">
                        <form method="post" id="filter_distributor" action="filter_distributor" enctype="multipart/form-data">
                            {!! csrf_field() !!}

                            <input type="hidden" id="uuid" name="uuid" value="">
                            <div class="row">
                           
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="location_1">{{Lang::get('common.location1')}}</label>
                                        <select multiple name="location_1[]" id="location_1" class="form-control chosen-select-modal">
                                            <option value="">select</option>
                                            @if(!empty($location1))
                                                @foreach($location1 as $k=>$r)
                                                    <option value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_2"> {{Lang::get('common.location2')}} </label>
                                        <select multiple  name="location_2[]"
                                                id="location_2" class="form-control input-sm chosen-select-modal">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="name">{{Lang::get('common.location3')}}</label>
                                        <select multiple name="location_3[]"
                                                id="location_3" class="form-control input-sm chosen-select-modal">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_4"> {{Lang::get('common.location4')}} </label>
                                        <select multiple name="location_4[]"
                                                id="location_4" class="form-control input-sm chosen-select-modal">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_5"> {{Lang::get('common.location5')}} </label>
                                        <select multiple name="location_5[]"
                                                id="location_5" class="form-control input-sm chosen-select-modal">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <label class="control-label no-padding-right"
                                           for="location_6"> {{Lang::get('common.location6')}} </label>
                                    <select multiple name="location_6[]" id="location_7" class="form-control input-sm chosen-select-modal">
                                         <option value="">Select</option>
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                               
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        {{--<label class="control-label no-padding-right"></label>--}}
                                        <button type="submit" class="btn btn-xs btn-purple mt-5"
                                                style="margin-top: 25px">Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12" id="result">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
         <!-- distributor Modal ends here  -->
 <!-- beat Modal starts here  -->
         <div class="modal fade rotate" data-backdrop="static" data-keyboard="false" id="myModal2">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header widget-header widget-header-small" >
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            ×
                        </button>
                        <h4 class="modal-title">Beat Details</h4>

                    </div>
                    <div class="modal-body ui-dialog-content ui-widget-content">
                        <form method="post" id="assign-beat" action="assign-beat">
                            <div id="result2"></div>
                        </form>
                        <div id="result3"></div>
                    </div>
                </div>
            </div>
        </div>
 <!-- beat Modal ends here  -->
<!-- targetr modal starts here  -->
<div class="modal fade" id="call_from_jquery" role="dialog">
    <div class="modal-dialog" style="width:800px;">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Junior User List</h4>
                <br>
                <h6 style="color: red;">NOTE : **  Firstly assign the senior for junior list of user you going to delete  **</h6>
            </div>
            <div class="modal-body">
                <form method="get" action="junior_list_submit_for_new_user">
                    <table class="table table-bordered table-hover">
                        <thead class = "mythead">
                            <th>Sr.no</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Senior List</th>
                        </thead>
                      
                            <tbody class="mytbody">
                            
                            </tbody>
                
                    </table>
                    <button id="button" name="submit" >Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="user_assign_distributor" role="dialog">
    <div class="modal-dialog modal-lg2">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >User Assign Distributor List</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    <div class="col-xs-12 col-md-2">

                        <div class="text-center" >
                                <img height="150" id="image_user" class="thumbnail inline no-margin-bottom" alt="Domain Owner's Avatar" src="" alt=" "  onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/profile-pic.jpg')}}';" />

                            <br>
                            <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                <div class="inline position-relative">
                                    <a class="user-title-label" href="#">
                                        <i class="ace-icon fa fa-circle light-green"></i>
                                        &nbsp;
                                        <span class="white" id="user_name_span"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-10">
                        <div class="table-header center">
                            User Assign Distributor List
                            <div class="pull-right" id="junior_list_show_on_modal">
                               
                                
                                <a title="Junior Details"  data-toggle="modal" data-target="#junior_list_modal_on_modal" class="junior_list_modal_on_modal">
                                
                                    <button type="button" class=" btn btn-sm btn-grey btn-block mg-b-10"><i class="fa fa-plus mg-r-10"></i>
                                        <b class="blink">Junior Details</b>
                                    </button>
                                </a>
                                
                            </div>
                           
                        </div>
                        <table class="table table-bordered table-hover" style="overflow-x: scroll;">
                            <thead class = "mythead_distibutor_list">
                                <th>Sr.No</th>
                                <th>Distributor Name</th>
                                <th>Beat Count</th>
                                <th>Dealer Wise Retailer Count</th>
                                <th>Beat Wise Retailer Count</th>
                                <th>Action</th>
                            </thead>
                          
                            <tbody class="mytbody_distibutor_list">
                            
                            </tbody>
                    
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- junior details modal box starts here which intiate on click of above modal box starts here  -->
<div class="modal fade" id="junior_list_modal_on_modal" role="dialog">
    <div class="modal-dialog modal-lg3">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" > Junior Details</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    
                    <div class="col-xs-12 col-md-12">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <th>Sr.No</th>
                                <th>{{Lang::get('common.location3')}} Name</th>
                                <th>User Name</th>
                                <th>Role</th>
                                <th>Mobile Number</th>
                            </thead>
                          
                                <tbody class="mytbody_junior_details">
                                </tbody>
                    
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- junior details modal box starts here which intiate on click of above modal box ends here  -->

<div class="modal fade" id="beat_modal_details" role="dialog">
    <div class="modal-dialog modal-lg3">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" > Beat Details</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    
                    <div class="col-xs-12 col-md-12">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <th>Sr.No</th>
                                <th>Beat Name</th>
                                <th>Retailer_count</th>
                            </thead>
                          
                                <tbody class="mytbody_beat_details">
                                </tbody>
                    
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="retailer_modal_details" role="dialog">
    <div class="modal-dialog modal-lg3">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" > Retailer Details</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    
                    <div class="col-xs-12 col-md-12">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <th>Sr.No</th>
                                <th>Distributor Name</th>
                                <th>Retailer Name</th>
                                <th>Beat Name</th>
                            </thead>
                          
                                <tbody class="mytbody_retailer_details">
                                </tbody>
                    
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- salary form data append starts here  -->
<div class="modal fade" id="employee_salary_form" role="dialog">
    <div class="modal-dialog modal-lg3">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" > Salary Information</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content" id="append_here">
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- salary form data append starts here  -->


@endsection

@section('js')

    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('msell/page/user-management.js')}}"></script>

    <script src="{{asset('nice/js/jszip.min.js')}}"></script>
    <script src="{{asset('nice/js/vfs_fonts.js')}}"></script>
    
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>


    <script>

        $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $('#myModal').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });
        $('.employee_salary_form').click(function() {
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    target.html(data); // show response from the php script.
                }
            });
        });
        $('.immediate_junior_list').click(function() {
            var user_id = $(this).attr('user_id');
            var role_id = $(this).attr('role_id');
            var name = 'Users';
            var action_id = user_id;
            var tab = 'person';
            var act = 'delete';
            var heading = 'Users';
            $('.mytbody').html('');
          
            if (user_id != '') 
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/get_immediate_junior_list',
                    dataType: 'json',
                    data: "user_id=" + user_id,
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                            $.confirm({
                                title: heading,
                                content: 'Are you sure want to ?',
                                buttons: {
                                    confirm: function () {
                                        takeAction(name, action_id, tab, act);
                                        $.alert({
                                            title: 'Alert!',
                                            content: 'Sccessfully Done!',
                                            buttons: {
                                                ok: function () {
                                                    setTimeout("window.parent.location = ''", 50);
                                                }
                                            }   
                                        });
                                    },
                                    cancel: function () {
                                        $.alert('Canceled!');
                                    }
                                }
                            });
                        }
                        else if (data.code == 200) 
                        {
                            $('#call_from_jquery').modal('toggle');
                            var Sno = 1;
                            var user_filter;
                            var selected = '';
                            var role_selected = '';

                             $.each(data.person, function (p_key, p_value) {
                                selected =  p_value.user_id==user_id?'selected':'';
                                 user_filter += "<option "+selected+" value=" + p_value.user_id +"  > "+ stripslashes(p_value.user_name) +" </option>";
                            });
                            var role_filter;
                             $.each(data.role, function (r_key, r_value) {
                                 role_selected = r_key==role_id?'selected':'';
                                 role_filter += "<option "+role_selected+" value=" +r_key +"  > "+ stripslashes(r_value) +" </option>";
                            });
                            // console.log(selected);
                            $.each(data.result, function (r_key, r_value) {

                                    $('.mytbody').append("<tr><td>"+Sno+"</td><input type='hidden' name='delete_user_id' value="+user_id+"><input type='hidden' name='junior_id[]' value="+r_value.user_id+"><td>"+r_value.user_name+"</td><td><select name='role_id[]' class='form-control input-sm chosen-select'>"+role_filter+"</select></td><td><select name='assign_user_id[]' class='form-control input-sm chosen-select'>"+user_filter+"</select></td></tr>");
                                Sno++;
                            });   
                            
                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
        });
    </script>
    <script>
    $('.user_assign_distributor').click(function() {
            var user_id = $(this).attr('user_id');
          // alert(user_id);
           
            $('.mytbody_distibutor_list').html('');
            $('.mytbody_junior_details').html('');
            // $('.mytbody_beat_details').html('');
            
            if (user_id != '') 
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/get_user_assign_distributor',
                    dataType: 'json',
                    data: "user_id=" + user_id,
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                           
                        }
                        else if (data.code == 200) 
                        {
                            var Sno = 1;
                            var Sno2 = 1;
                            $('#user_name_span').html('');
                            // $('#image_user').html('');
                            var dealer_retailer_total = 0;
                            var beat_retailer_total = 0;
                            var beat_total = 0;
                            var template = '';
                            var junior_template = '';
                            var retailer_count_beat_wise = '';
                            // var template_beat = '';
                            $.each(data.result, function (u_key, u_value) {
                                // console.log(u_value);
                                    beat_total += u_value.beat_count;
                                    dealer_retailer_total += u_value.retailer_count;
                                    beat_retailer_total += (data.actual_retailer_count[u_value.dealer_id])?data.actual_retailer_count[u_value.dealer_id]:0;
                                    if(data.actual_retailer_count[u_value.dealer_id])
                                    {
                                        retailer_count_beat_wise = data.actual_retailer_count[u_value.dealer_id];
                                    }
                                    else
                                    {
                                        retailer_count_beat_wise = 0;
                                    }
                                    template += ('<tr><td>'+Sno+'</td><td>'+u_value.dealer_name+'</td><td onclick=beat_details('+u_value.dealer_id+','+u_value.user_id+'); ><a class="beat_modal_details" title="beat details" data-toggle="modal" data-target="#beat_modal_details" >'+u_value.beat_count+'</a></td><td >'+u_value.retailer_count+'</td><td onclick=retailer_details('+u_value.dealer_id+','+u_value.user_id+');><a class="beat_modal_details" title="retailer details" data-toggle="modal" data-target="#retailer_modal_details" >'+retailer_count_beat_wise+'</a></td><td><button class="btn btn-xs btn-danger" onclick=delete_dealer('+u_value.dealer_id+',"dealer","delete",'+u_value.user_id+'); ><i class="ace-icon fa fa-trash-o bigger-120"></i></button></td></tr>');
                                Sno++;

                                
                            });   
                            template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                            $('.mytbody_distibutor_list').append(template);

                            $.each(data.junior_Details, function (j_key, j_value) {
             
                                    junior_template += ('<tr><td>'+Sno2+'</td><td>'+j_value.state_name+'</td><td>'+j_value.user_name+'</td><td>'+j_value.rolename+'</td><td>'+j_value.mobile+'</td></tr>');
                                Sno2++;

                                
                            });   
                          
                            $('.mytbody_junior_details').append(junior_template);

                            var div12 = document.getElementById('user_name_span');
                            div12.innerHTML +=  data.user_name_details;

                            


                            var image_name = domain+'/users-profile/'+data.person_image ;

                            $("img#image_user").attr('src' , image_name);


                            
                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
        });
        
    </script>


    <script type="text/javascript">
        function beat_details(dealer_id,user_id) {
                // var user_id = dealer_id;
                // var dealer_id = user_id;
              // alert(user_id);
               
                $('.mytbody_beat_details').html('');
              
                if (user_id != '') 
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: domain + '/get_beat_details_dealer',
                        dataType: 'json',
                        data: "user_id=" + user_id+"&dealer_id="+dealer_id,
                        success: function (data) 
                        {
                            var template_beat ='';

                            if (data.code == 401) 
                            {
                               
                            }
                            else if (data.code == 200) 
                            {
                                var Sno_beat = 1;
                                var total_retailer_count =0;
                                var retailer_count ='';
                            
                                $.each(data.result_data, function (b_key, b_value) {
                                    // console.log(b_value);
                                    if(data.retailer_count_beat[b_value.location_id])
                                    {
                                         total_retailer_count += data.retailer_count_beat[b_value.location_id];
                                         retailer_count = data.retailer_count_beat[b_value.location_id];
                                    }
                                    else
                                    {
                                        retailer_count = 0;
                                    }

                                   
                                        template_beat += ("<tr><td>"+Sno_beat+"</td><td>"+b_value.l7_name+"</td><td>"+retailer_count+"</td></tr>");
                                    Sno_beat++;
                                });   
                                template_beat += "<tr><td colspan = '2'>Grand Total</td><td>"+total_retailer_count+"</td></tr>";
                                
                                $('.mytbody_beat_details').append(template_beat);

                                
                            }

                        },
                        complete: function () {
                            // $('#loading-image').hide();
                        },
                        error: function () {
                        }
                    });
                }       
            };
            function retailer_details(dealer_id,user_id) {
                // var user_id = dealer_id;
                // var dealer_id = user_id;
              // alert(user_id);
               
                $('.mytbody_retailer_details').html('');
              
                if (user_id != '') 
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: domain + '/get_retailer_details',
                        dataType: 'json',
                        data: "user_id=" + user_id+"&dealer_id="+dealer_id,
                        success: function (data) 
                        {
                            var template_retailer ='';

                            if (data.code == 401) 
                            {
                               
                            }
                            else if (data.code == 200) 
                            {
                                var Sno_beat = 1;
                                // var total_retailer_count =0;
                            
                                $.each(data.result_data, function (r_key, r_value) {
                                    // console.log(b_value);
                                    // total_retailer_count += b_value.retailer_count
                                        template_retailer += ("<tr><td>"+Sno_beat+"</td><td>"+r_value.dealer_name+"</td><td>"+r_value.retailer_name+"</td><td>"+r_value.l7_name+"</td></tr>");
                                    Sno_beat++;
                                });   
                                // template_beat += "<tr><td colspan = '2'>Grand Total</td><td>"+total_retailer_count+"</td></tr>";
                                
                                $('.mytbody_retailer_details').append(template_retailer);

                                
                            }

                        },
                        complete: function () {
                            // $('#loading-image').hide();
                        },
                        error: function () {
                        }
                    });
                }       
            };
    </script>
   <script>

        $(document).on('change', '#location_1', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

        $(document).on('change', '#location_3', function () {
            _current_val = $(this).val();
            location_data(_current_val,4);
        });
        $(document).on('change', '#location_4', function () {
            _current_val = $(this).val();
            location_data(_current_val,5);
        }); 
        $(document).on('change', '#location_5', function () {
            _current_val = $(this).val();
            location_data(_current_val,6);
        }); 


        function location_data(val,level) {
            _append_box=$('#location_'+level);
            location_6 = $('#location_7');
            // console.log(location_6);
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/getLocationForAssign',
                    dataType: 'json',
                    data: "id=" + val+"&type="+level,
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                            //Location 3
                            template = '<option value="" >Select</option>';
                            $.each(data.result, function (key, value) {
                               
                                    template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                             
                            });
                            _append_box.empty();
                            location_6.empty();
                            if(level == 6)
                            {
                                
                                location_6.append(template).trigger("chosen:updated");
                            }
                            else
                            {
                                 _append_box.empty();
                                _append_box.append(template).trigger("chosen:updated");
                            }
                            

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }
            else{
                _append_box.empty();
            }
        }

   </script>
      <!-- END MODAL -->
       
    
    <script>
        function delete_dealer(action_id, tab, act,user_id)
        {

            // alert(action_id);//45 id 
            // alert(tab);//person
            // alert(user_id);//delete
            $.confirm({
                title: tab,
                content: 'Are you sure want to ' + act + ' ' + tab + '?',
                buttons: {
                    confirm: function () {
                        deleteAction(action_id, tab, act,user_id);
                        $.alert({
                            title: 'Alert!',
                            content: 'Sccessfully Done!',
                            buttons: {
                                ok: function () {
                                    // alert('Successfully Deleted!');
                                    $.ajax({
                                    type: "POST",
                                    url: domain + '/get_user_assign_distributor',
                                    dataType: 'json',
                                    data: "user_id=" + user_id,
                                    success: function (data) 
                                    {
                                        if (data.code == 401) 
                                        {
                                           
                                        }
                                        else if (data.code == 200) 
                                        {
                                            var Sno = 1;
                                            $('#user_name_span').html('');
                                            $('.mytbody_distibutor_list').html('');

                                            // $('#image_user').html('');
                                            var dealer_retailer_total = 0;
                                            var beat_retailer_total = 0;
                                            var beat_total = 0;
                                            var template = '';
                                            // var template_beat = '';
                                            $.each(data.result, function (u_key, u_value) {
                                                // console.log(u_value);
                                                    beat_total += u_value.beat_count;
                                                    dealer_retailer_total += u_value.retailer_count;
                                                    beat_retailer_total += data.actual_retailer_count[u_value.dealer_id];
                                                    if(data.actual_retailer_count[u_value.dealer_id])
                                                    {
                                                        retailer_count_beat_wise = data.actual_retailer_count[u_value.dealer_id];
                                                    }
                                                    else
                                                    {
                                                        retailer_count_beat_wise = 0;
                                                    }
                                                    template += ('<tr><td>'+Sno+'</td><td>'+u_value.dealer_name+'</td><td onclick=beat_details('+u_value.dealer_id+','+u_value.user_id+'); ><a class="beat_modal_details" title="beat details" data-toggle="modal" data-target="#beat_modal_details" >'+u_value.beat_count+'</a></td><td >'+u_value.retailer_count+'</td><td onclick=retailer_details('+u_value.dealer_id+','+u_value.user_id+');><a class="beat_modal_details" title="retailer details" data-toggle="modal" data-target="#retailer_modal_details" >'+retailer_count_beat_wise+'</a></td><td><button class="btn btn-xs btn-danger" onclick=delete_dealer('+u_value.dealer_id+',"dealer","delete",'+u_value.user_id+'); ><i class="ace-icon fa fa-trash-o bigger-120"></i></button></td></tr>');
                                                Sno++;

                                                
                                            });   
                                            template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                            $('.mytbody_distibutor_list').append(template);
                                            // $('.mytbody_beat_details').append(template_beat);

                                            var div12 = document.getElementById('user_name_span');
                                            div12.innerHTML +=  data.user_name_details;

                                            


                                            var image_name = domain+'/users-profile/'+data.person_image ;

                                            $("img#image_user").attr('src' , image_name);


                                            
                                        }

                                    },
                                    complete: function () {
                                        // $('#loading-image').hide();
                                    },
                                    error: function () {
                                    }
                                });
                                }
                            }   
                        });
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });

        }

        function deleteAction(action_id,tab,act,user_id)
        {
            // alert(action_id);
            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/deleteAction',
                    dataType: 'json',
                    data: {'action_id': action_id, 'tab': tab, 'act': act ,'user_id':user_id},
                    success: function (data) {
                        // console.log(data);
                        if (data.code == 401) 
                        {

                        }
                        else if (data.code == 200) 
                        {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }
         function MtpAction(heading, name, action_id, tab, act) {
         

            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        EnableAction(name, action_id, tab, act);
                        $.alert({
                            title: 'Alert!',
                            content: 'Sccessfully Done!',
                            buttons: {
                                ok: function () {
                                    setTimeout("window.parent.location = ''", 50);
                                }
                            }   
                        });
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function EnableAction(module, action_id, tab, act) {
         

            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/EnableAction',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        // console.log(data);
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }

        function confirmAction(heading, name, action_id, tab, act) {
            // alert(heading);
            // alert(name);
            // alert(action_id);
            // alert(tab);
            // alert(act);
            var concat = '';
            var concat_status = '';
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
                        $.alert({
                            title: 'Alert!',
                            content: 'Sccessfully Done!',
                            buttons: {
                                ok: function () {
                                    if(heading == 'Clear IMEI')
                                    {
                                        // alert('Successfully '+heading);
                                        concat = action_id+'imei';
                                        // alert(concat);
                                        $('#'+concat).empty('');
                                        $('#'+concat).append('N/A');
                                        // console.log(qwerty);

                                    }
                                    else if(heading == 'Users')
                                    {
                                        // alert(heading);
                                        concat = action_id+'active_incative';
                                        concat_status = action_id+'status_written';

                                        $('#'+concat).empty('');
                                        $('#'+concat_status).empty('');
                                        

                                        if(act == 'inactive')
                                        {
                                            $('#'+concat).append("<button title='active' class='btn btn-xs btn-success' onclick=confirmAction('Users','Users',"+action_id+",'person','active'); > <i class='ace-icon fa fa-check bigger-120'></i> </button>");
                                            $('#'+concat_status).append("<span class='label label-sm label-warning'>In-active</span>");
                                        }
                                        else if(act == 'active')
                                        {
                                            $('#'+concat).append("<button title='Inactive' class='btn btn-xs btn-warning' onclick=confirmAction('Users','Users',"+action_id+",'person','inactive'); > <i class='ace-icon fa fa-ban bigger-120'></i> </button>");
                                            $('#'+concat_status).append("<span class='label label-sm label-success'>Active</span>");

                                        } 
                                       
                                        


                                       


                                    }
                                    else
                                    {
                                        setTimeout("window.parent.location = ''", 50);

                                    }
                                }
                            }   
                        });
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function takeAction(module, action_id, tab, act) {
            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeAction',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        // console.log(data);
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }

        function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }

        function search() {
            if ($('#search').val() != '') {
                $('#user-search').submit();
            }
        }
    </script>

    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif

    <script type="text/javascript">
        jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": false},
                            null,   null, null, null, null, null, null,
                             null,null,null,null,null,null,null,null,null,null,null,
                            {"bSortable": false}
                        ],
                        "aaSorting": [],
                        "sScrollY": "1000px",
                        //"bPaginate": false,

                        "sScrollX": "100%",
                        "sScrollXInner": "120%",
                        "bScrollCollapse": true,
                        "iDisplayLength": 10000,


                        select: {
                            style: 'multi'
                        }
                    });


            $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

            new $.fn.dataTable.Buttons(myTable, {
                buttons: [
                    {
                        "extend": "colvis",
                        "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        columns: ':not(:first):not(:last)'
                    },
                    {
                        "extend": "copy",
                        "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "csv",
                        "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "excel",
                        "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    
                    {
                        "extend": "print",
                        "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        autoPrint: true,
                        message: 'This print was produced using the Print button for DataTables'
                    }
                ]
            });
            myTable.buttons().container().appendTo($('.tableTools-container'));

            //style the message box
            var defaultCopyAction = myTable.button(1).action();
            myTable.button(1).action(function (e, dt, button, config) {
                defaultCopyAction(e, dt, button, config);
                $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
            });


            var defaultColvisAction = myTable.button(0).action();
            myTable.button(0).action(function (e, dt, button, config) {

                defaultColvisAction(e, dt, button, config);


                if ($('.dt-button-collection > .dropdown-menu').length == 0) {
                    $('.dt-button-collection')
                        .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                        .find('a').attr('href', '#').wrap("<li />")
                }
                $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
            });

            ////

            setTimeout(function () {
                $($('.tableTools-container')).find('a.dt-button').each(function () {
                    var div = $(this).find(' > div').first();
                    if (div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
                    else $(this).tooltip({container: 'body', title: $(this).text()});
                });
            }, 500);


            myTable.on('select', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });
            myTable.on('deselect', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                }
            });


            /////////////////////////////////
            //table checkboxes
            $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

            //select/deselect all rows according to table header checkbox
            $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $('#dynamic-table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) myTable.row(row).select();
                    else myTable.row(row).deselect();
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                var row = $(this).closest('tr').get(0);
                if (this.checked) myTable.row(row).deselect();
                else myTable.row(row).select();
            });


            $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                e.stopImmediatePropagation();
                e.stopPropagation();
                e.preventDefault();
            });


            //And for the first simple table, which doesn't have TableTools or dataTables
            //select/deselect all rows according to table header checkbox
            var active_class = 'active';
            $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $(this).closest('table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                    else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                var $row = $(this).closest('tr');
                if ($row.is('.detail-row ')) return;
                if (this.checked) $row.addClass(active_class);
                else $row.removeClass(active_class);
            });


            /********************************/
            //add tooltip for small view action buttons in dropdown menu
            $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

            //tooltip placement on right or left
            function tooltip_placement(context, source) {
                var $source = $(source);
                var $parent = $source.closest('table')
                var off1 = $parent.offset();
                var w1 = $parent.width();

                var off2 = $source.offset();
                //var w2 = $source.width();

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }


            /***************/
            $('.show-details-btn').on('click', function (e) {
                e.preventDefault();
                $(this).closest('tr').next().toggleClass('open');
                $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
            });
            /***************/


            /**
             //add horizontal scrollbars to a simple table
             $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
             {
               horizontal: true,
               styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
               size: 2000,
               mouseWheelLock: true
             }
             ).css('padding-top', '12px');
             */


        })
  // /***************/
    $('.show-details-btn').on('click', function (e) {
        e.preventDefault();
        $(this).closest('tr').next().toggleClass('open');
        $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    });
    /***************/
        
    </script>
    <script>
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".search_from_this").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
</script>
@endsection