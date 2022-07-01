@extends('layouts.master')

@section('body')
<div class="main-content" >
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
            <ul class="breadcrumb">
                <li style="color: white">
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a style="color: white" href="{{url('home')}}">Home</a>
                </li>

                <li class="active" style="color: white">Leave Management</li>
            </ul><!-- /.breadcrumb -->
            <!-- /.nav-search -->
        </div>
        <div id="">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <form action="{{route('leave.search')}}" method="GET" class="form-horizontal">
                                    <div class="card-body">
                                        <h4 class="card-title">Search by leave type</h4>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <input type="text" name="search" class="form-control" id="fname" placeholder="Leave type">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-top">
                                        <div class="card-body">
                                            <button type="submit" class="btn btn-success">Search</button>
                                            <a href="{{route('leave')}}" class="btn btn-md btn-danger">Clear</a>
                                            <a href="{{route('leave.create')}}" class="btn btn-md btn-danger">Apply leave</a>
                                        </div>
                                        <div class="card-body">
                                            <h4 class="card-title">Leave Balance Sheet</h4>
                                            <table d="zero_config" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>S.N.</th>
                                                        @if(!empty($data))
                                                            @foreach($data as $key => $value)
                                                                <th>{{$value->name}}</th>
                                                            @endforeach
                                                        @endif
                                                        <th>Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id='hide'>
                                                    <td>1</td>
                                                    <?php 
                                                        $arr = [];
                                                    ?>
                                                    @if(!empty($data))
                                                        @foreach($data as $key => $value)
                                                            <?php
                                                                $balance = !empty($leaves_type_data[$value->id])?$leaves_type_data[$value->id]:'0'; 
                                                                $count = $value->count-$balance;

                                                            ?>
                                                            <td class="setall" id="{{'leaver_id'.$value->id}}">{{$arr[] = $count}}</td>
                                                        @endforeach
                                                    @endif
                                                    <td id="balance">{{array_sum($arr)}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                        
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            @if(Auth::user()->is_admin !='1')
                    {{-- @can('isEmployee') 
                            <a class="btn btn-lg btn-dark" href="{{route('leave.create')}}">Apply leave</a>--}}
                    {{-- @endcan --}}
                    @endif
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Leave List</h5>
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>S.N.</th>
                                                <th>Employee name</th>
                                                <th>Leave type</th>
                                                <th>Date from</th>
                                                <th>Date to</th>
                                                <th>No. of days</th>
                                                <th>Reason</th>
                                                <th>Leave type offer</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($leaves))

                                            @foreach($leaves as $key => $leave)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$leave->user_name}}</td>
                                                    <td>{{$leave->leave_type_name}}</td>
                                                    <td>{{$leave->date_from}}</td>
                                                    <td>{{$leave->date_to}}</td>
                                                    <td>{{$leave->days}}</td>
                                                    <td>{{$leave->reason}}</td>
                                                    <td>
                                                        @if(Auth::user()->is_admin=='1')
                                                            {{--{{$leave->is_approved}}--}}
                                                            @if($leave->leave_type_offer==0)
                                                                <form id="{{$leave->id}}" action="{{route('leave.paid',$leave->id)}}" method="POST">
                                                                    @csrf
                                                                    {{--<button type="button" onclick="approveLeave({{$leave->id}})" class="btn btn-sm btn-cyan" name="approve" value="1">Approve</button>--}}
                                                                    <button type="submit" onclick="return confirm('Are you sure want to paid for leave?')" class="btn btn-sm btn-cyan" name="paid" value="1">Paid</button>
                                                                </form>
                                                                <form id="{{$leave->id}}" action="{{route('leave.paid',$leave->id)}}" method="POST">
                                                                    @csrf
                                                                    {{--<button type="button" onclick="rejectLeave({{$leave->id}})" class="btn btn-sm btn-danger" name="approve" value="2">Reject</button>--}}
                                                                    <button type="submit" onclick="return confirm('Are you sure want to paid for leave?')" class="btn btn-sm btn-danger" name="paid" value="2">Unpaid</button>
                                                                </form>
                                                            @elseif($leave->leave_type_offer==1)

                                                                <form action="{{route('leave.paid',$leave->id)}}" method="POST">
                                                                    @csrf
                                                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure want to unpaid for leave?')" type="submit" name="paid" value="2">Unpaid</button>
                                                                </form>
                                                            @else
                                                                <form action="{{route('leave.paid',$leave->id)}}" method="POST">
                                                                    @csrf
                                                                    <button class="btn btn-sm btn-cyan" onclick="return confirm('Are you sure want to piad for leave?')" type="submit" name="paid" value="1">Paid</button>
                                                                </form>
                                                            @endif

                                                            {{--<a href="{{route('leave.approve',$leave->id)}}" class="btn btn-sm btn-cyan">Approve</a>--}}
                                                            {{--<a href="{{route('leave.pending',$leave->id)}}" class="btn btn-sm btn-warning">Pending</a>--}}
                                                            {{--<a href="{{route('leave.reject',$leave->id)}}" class="btn btn-sm btn-danger">Reject</a>--}}
                                                        @else
                                                            @if($leave->leave_type_offer==0)
                                                                <span class="badge badge-pill badge-warning">Pending</span>
                                                            @elseif($leave->leave_type_offer==1)
                                                                <span class="badge badge-pill badge-success">Paid</span>
                                                            @else
                                                                <span class="badge badge-pill badge-danger">Unpaid</span>
                                                            @endif
                                                        @endif
                                                    </td>

                                                            <td>
                                                                @if(Auth::user()->is_admin=='1')
                                                                {{--{{$leave->is_approved}}--}}
                                                                @if($leave->is_approved==0)
                                                                    <form id="approve-leave-{{$leave->id}}" action="{{route('leave.approve',$leave->id)}}" method="POST">
                                                                        @csrf
                                                                        {{--<button type="button" onclick="approveLeave({{$leave->id}})" class="btn btn-sm btn-cyan" name="approve" value="1">Approve</button>--}}
                                                                        <button type="submit" onclick="return confirm('Are you sure want to approve leave?')" class="btn btn-sm btn-cyan" name="approve" value="1">Approve</button>
                                                                    </form>
                                                                    <form id="reject-leave-{{$leave->id}}" action="{{route('leave.approve',$leave->id)}}" method="POST">
                                                                        @csrf
                                                                        {{--<button type="button" onclick="rejectLeave({{$leave->id}})" class="btn btn-sm btn-danger" name="approve" value="2">Reject</button>--}}
                                                                        <button type="submit" onclick="return confirm('Are you sure want to reject leave?')" class="btn btn-sm btn-danger" name="approve" value="2">Reject</button>
                                                                    </form>
                                                                @elseif($leave->is_approved==1)

                                                                    <form action="{{route('leave.approve',$leave->id)}}" method="POST">
                                                                        @csrf
                                                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure want to reject leave?')" type="submit" name="approve" value="2">Reject</button>
                                                                    </form>
                                                                @else
                                                                    <form action="{{route('leave.approve',$leave->id)}}" method="POST">
                                                                        @csrf
                                                                        <button class="btn btn-sm btn-cyan" onclick="return confirm('Are you sure want to approve leave?')" type="submit" name="approve" value="1">Approve</button>
                                                                    </form>
                                                                @endif

                                                                    {{--<a href="{{route('leave.approve',$leave->id)}}" class="btn btn-sm btn-cyan">Approve</a>--}}
                                                                    {{--<a href="{{route('leave.pending',$leave->id)}}" class="btn btn-sm btn-warning">Pending</a>--}}
                                                                    {{--<a href="{{route('leave.reject',$leave->id)}}" class="btn btn-sm btn-danger">Reject</a>--}}
                                                                    @else
                                                                    @if($leave->is_approved==0)
                                                                        <span class="badge badge-pill badge-warning">Pending</span>
                                                                    @elseif($leave->is_approved==1)
                                                                        <span class="badge badge-pill badge-success">Approved</span>
                                                                    @else
                                                                        <span class="badge badge-pill badge-danger">Rejected</span>
                                                                    @endif
                                                                @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                            @endforeach
                                            @endif
                                        </table>
                                        {{ $leaves->links() }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

     
                <!-- <footer class="footer text-center">
                    All Rights Reserved by Khoz Informatics Pvt. Ltd. Designed and Developed by <a href="https://khozinfo.com/">Khozinfo</a>.
                </footer> -->
            </div>
        </div>
    </div>
</div>

@endsection