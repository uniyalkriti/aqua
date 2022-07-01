@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.assign_module')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li>
                        <a href="{{url('weightType')}}">{{Lang::get('common.assign_module')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.assign_module')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                       
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        <form class="form-horizontal" action="{{route('Modules.store')}}" method="POST" id="location2-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}

                         <div class="row">
                            <div class="col-lg-12">

                                <div class="col-lg-2 col-sm-2">
                                    <label class="control-label no-padding-right" for="name">Company</label>
                                    <select name="company" id="company" class="form-control">
                                        <option value="">Select</option>
                                        @if(!empty($company))
                                            @foreach($company as $ck=>$cr) 
                                                <option {{Request::get('company')==$ck?'selected':''}} value="{{$ck}}" > {{$cr}} 
                                                </option>
                                            @endforeach 
                                        @endif
                                    </select>
                                </div>

                                <div class="col-lg-2 col-sm-2">
                                    <button type="submit" value="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                            style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
                                    </button>
                                </div>

                            </div>
                        </div>

                           <!--  -->
                           <div class="row">

                                @if(!empty($records))
                                    @foreach($records as $k=>$d)
                                    <div class="col-md-6">
                                        <table class="table table-striped table-bordered table-hover" width="50%">
                                            <tr>
                                                <td class="checkbox">
                                                {{$k+1}}
                                                    <label class="control-label bolder blue">
                                                        <input type="hidden" name="distributor[]" value="{{$d->id}}" id="distributor">
                                                        {{$d->name}}
                                                </label>
                                                </td>
                                                @if(!empty($arr2[$d->id]))
                                                    @foreach($arr2[$d->id] as $key=>$data)
                                                        <td class="checkbox">
                                                            {{$key+1}}
                                                            <label>
                                                                <input id ="dist12" name="beat[{{$d->id}}][]" value="{{$data->id}}" class="ace ace-checkbox-2 checkBoxClass"
                                                                    {{!empty($mlsm1) && in_array($data->id,$mlsm1) &&  in_array($data->id,$mlsm1)  ?'checked':''}} type="checkbox">  
                                                                <span class="lbl"> {{$data->title_name}}</span>      
                                                            </label>
                                                        </td>
                                                    @endforeach
                                                @endif
                                                    <br/>
                                            </tr>
                                        </table>
                                    </div>
                                    @endforeach
                                
                                @endif
                            </div>
                           <!--  -->

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn" type="button" onclick="document.location.href='{{url('weightType')}}'">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>

                        </form>

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection