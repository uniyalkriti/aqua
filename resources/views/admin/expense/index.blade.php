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
                                <form action="#" method="GET" class="form-horizontal">
                                    <div class="card-body">
                                        <h4 class="card-title">Search by Expense type</h4>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <input type="text" name="search" class="form-control" id="fname" placeholder="Expense type">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-top">
                                        <div class="card-body">
                                            <a href="{{route('expense.create')}}" class="btn btn-md btn-danger">Apply Expense</a>
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
                                    <h5 class="card-title">Expense List</h5>
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>S.N.</th>
                                                <th>Employee name</th>
                                                <th>Expense type</th>
                                                <th>Date </th>
                                                <th>Amount</th>
                                                <th>Remarks</th>
                                                <th>Paid Amount</th>
                                                <th>Balance</th>
                                                <th>Bill Image</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($leaves))

                                            @foreach($leaves as $key => $leave)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$leave->user_name}}</td>
                                                    <td>{{$leave->expense_type_name}}</td>
                                                    <td>{{$leave->expense_date}}</td>
                                                    <td>{{$leave->expense_fare}}</td>
                                                    <td>{{$leave->expense_remarks}}</td>
                                                    <td>{{!empty($leave->paid_amount)?$leave->paid_amount:''}}</td>
                                                    <td>{{!empty($leave->balance)?$leave->balance:''}}</td>
                                                    <td><img src="{{asset($leave->expense_image)}}" class="img-responsive" width="100" height="200"></td>
                                                    <td>
                                                        @if(Auth::user()->is_admin=='1')

                                                            @if($leave->paid_status==0)
                                                                <form id="{{$leave->id}}" action="{{url('expense_paid?&id='.$leave->id)}}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" onclick="return confirm('Are you sure want to paid ?')" class="btn btn-sm btn-cyan" name="paid" value="1">Paid</button>
                                                                </form>
                                                                <form id="{{$leave->id}}" action="{{url('expense_paid&id='.$leave->id)}}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" onclick="return confirm('Are you sure want to cancel the expense ?')" class="btn btn-sm btn-danger" name="paid" value="2">Unpaid</button>
                                                                </form>
                                                            @elseif($leave->paid_status==1)
                                                                <span class="badge badge-pill badge-success">Paid</span>
                                                            @else
                                                                <span class="badge badge-pill badge-danger">Unpaid</span>
                                                            @endif
                                                            
                                                          
                                                        @else
                                                            @if($leave->paid_status==0)
                                                                <span class="badge badge-pill badge-warning">Pending</span>
                                                            @elseif($leave->paid_status==1)
                                                                <span class="badge badge-pill badge-success">Paid</span>
                                                            @else
                                                                <span class="badge badge-pill badge-danger">Unpaid</span>
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