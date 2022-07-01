
@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif
<?php 
$query_string = Request::getQueryString() ? ('&' . Request::getQueryString()) : '';
?>
@if(!empty($records))
<!-- <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>

        <button onclick="window.location.href='DailyPerformanceReportExport?{{$query_string}}'" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel Dump Data</button> -->
@endif
<style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }

    #simple-table th {
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
    {{--<caption><h3></h3></caption>--}}
    <tr>
        <td colspan="150"><h3>{{Lang::get('common.daily_performance')}}</h3></td>
    </tr>
    <tr>
        <th rowspan="3">{{Lang::get('common.s_no')}}</th>
        <th rowspan="3">{{Lang::get('common.date')}}</th>
        <th rowspan="3">{{Lang::get('common.day')}}</th>


        <th rowspan="3">{{Lang::get('common.location3')}}</th>
        <th rowspan="3">{{Lang::get('common.location4')}}</th>
        <th rowspan="3">{{Lang::get('common.location5')}}</th>
        <th rowspan="3">{{Lang::get('common.location6')}}</th>

        <th rowspan="3">{{Lang::get('common.emp_code')}}</th>
        <th rowspan="3">{{Lang::get('common.username')}}</th>
        <th rowspan="3">{{Lang::get('common.role_key')}}</th>
        <th rowspan="3">{{Lang::get('common.user_contact')}}</th>
        <th rowspan="3">{{Lang::get('common.senior_name')}}</th>
        <th rowspan="3">{{Lang::get('common.working')}} With</th>

        <!-- <th rowspan="3">{{Lang::get('common.user')}} {{Lang::get('common.location5')}}</th> -->
          <th rowspan="3">{{Lang::get('common.location7')}}</th>
        <!-- <th rowspan="3">{{Lang::get('common.role_key')}}</th> -->
        <th rowspan="3">{{Lang::get('common.as_per_tour')}} ({{Lang::get('common.location6')}})</th>
        <th rowspan="3">{{Lang::get('common.as_per_tour')}} ({{Lang::get('common.distributor')}})</th>
        <th rowspan="3">{{Lang::get('common.as_per_tour')}} ({{Lang::get('common.location7')}})</th>
        <th rowspan="3">Start {{Lang::get('common.time')}}</th>

        <th rowspan="3">Check In Remarks</th>
        <th rowspan="3">Check Out Remarks</th>

        <th rowspan="3">Today's Task</th>
        <th rowspan="3">Actual({{Lang::get('common.location6')}})</th>
        <th rowspan="3">Actual({{Lang::get('common.distributor')}})</th>
        <th rowspan="3">Actual({{Lang::get('common.location7')}})</th>
        <th rowspan="3">Today's Tour Programme: FOLLOWED / CHANGED</th>
        <th rowspan="3">{{Lang::get('common.location7')}} Followed</th>
        <th rowspan="3">{{Lang::get('common.status')}}</th>
        <th rowspan="3">{{Lang::get('common.time')}} Of First Call</th>
        <th rowspan="3">{{Lang::get('common.time')}} Of Last Call</th>
        <th rowspan="3">End {{Lang::get('common.time')}}</th>
        <th rowspan="3">{{Lang::get('common.total')}} {{Lang::get('common.retailer')}}</th>
        <th rowspan="3">Visit Today</th>
        <th rowspan="3">Today's {{Lang::get('common.productive_call')}}</th>
        <th rowspan="3">New {{Lang::get('common.retailer')}} Added Today</th>
        <th rowspan="3">New Productive Counter</th>
        <th rowspan="2" colspan="3">{{Lang::get('common.check_out')}} DSR</th>
        <th rowspan="2" colspan="2">{{Lang::get('common.secondary_sale')}}</th>
        <th rowspan="2" colspan="2">INT. {{Lang::get('common.distributor')}} Sale</th>
        <th rowspan="2" colspan="2">{{Lang::get('common.total')}} Sale Sec + Id</th>
        <th colspan="{{!empty($catalog)?count($catalog)*3:10}}">Product Wise Productivity Detail</th>
    </tr>
    <tr>
        @if(!empty($catalog))
            @foreach($catalog as $key=>$data)
                <th colspan="3">{{$data}}</th>
            @endforeach
        @endif
    </tr>
    <tr>
    <th>{{Lang::get('common.total_call')}}</th>
    <th>{{Lang::get('common.total')}} {{Lang::get('common.productive_call')}}</th>
    <th>{{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}</th>
        <th>KG</th>
        <th>RV</th>
        <th>KG</th>
        <th>RV</th>
        <th>KG</th>
        <th>RV</th>
        @if(!empty($catalog))
            @foreach($catalog as $key=>$data)
                <th>CALLS</th>
                <th>RD KG</th>
                <th>RV</th>
            @endforeach
        @endif
    </tr>
    <?php $inc=1;
        $TOTAL_SALE_SEC_ID_sum_kg=0;
        $TOTAL_SALE_SEC_ID_sum_rv=0;
        $total_row1=[];
        $checkout_tc=array();
        $checkout_pc=array();
        $checkout_tsv=array();
        $total_kg=array();
        $total_rv=array();
        $total_outlet = array();
        $visit_count =  array();
        $productive_call =array();
        $new_outlet_total = array();
    
        $total_price1=[];
        
        $senior_name = App\CommonFilter::senior_name('person');
    
    ?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$record)
        <?php
        $uid = Crypt::encryptString($record['uid']);
        $senior_id = Crypt::encryptString($record['person_id_senior']);
        ?>

            <tr>
                <td>{{$inc++}}</td>
                <td>{{!empty($record['work_dates'])?$record['work_dates']:'N/A'}}</td>
                <td>{{date('l',strtotime($record['work_date']))}}</td>


                <td>{{!empty($record['l3_name'])?$record['l3_name']:''}}</td>
                <td>{{!empty($record['l4_name'])?$record['l4_name']:''}}</td>
                <td>{{!empty($record['l5_name'])?$record['l5_name']:''}}</td>
                <td>{{!empty($record['l6_name'])?$record['l6_name']:''}}</td>
                <td>{{$record['emp_code']}}</td>
                <td><a href="{{('user/'.$uid)}}">{{!empty($record['uname'])?$record['uname']:''}}</a></td>
                <td>{{$record['rolename']}}</td>
                <td>{{$record['mobile']}}</td>
                <td><a href="{{('user/'.$senior_id)}}">{{!empty($senior_name[$record['person_id_senior']])?$senior_name[$record['person_id_senior']]:''}}</a></td>
                <td>{{!empty($record['working_with'])?$record['working_with']:'SELF'}}</td>
                
                <!-- <td>{{$record['head_quar']}}</td> -->
                <td>{{$record['region_txt']}}</td>
                <!-- <td>{{$record['rolename']}}</td> -->
                <?php
                $mtp=array();
                $today_task=array();
                $first_call=array();
                $last_call=array();
                $checkout=array();

                $a='';
                ?>
    
                    <td>{{!empty($record["mtp"]->l4_name)?$a=$record["mtp"]->l4_name:'N/A'}}</td>
                    <td>{{!empty($record["mtp"]->dname)?$record["mtp"]->dname:'N/A'}}</td>
                    <td>{{!empty($record["mtp"]->l5_name)?$record["mtp"]->l5_name:'N/A'}}</td>
                
  
                <td>{{!empty($record["work_time"])?$record["work_time"]:'N/A'}}</td>
                
                <td>{{!empty($record["checkInRemarks"])?$record["checkInRemarks"]:'N/A'}}</td>
                <td>{{!empty($checkout_data[$key]['checkOutRemarks'])?$checkout_data[$key]['checkOutRemarks']:''}}</td>



                <td>{{!empty($record["w_s"])?$record["w_s"]:'N/A'}}</td>

                <td>{{$b=!empty($other_sale_arr[$key]['town'])?$other_sale_arr[$key]['town']:'N/A'}}</td>

                <td>{{!empty($other_sale_arr[$key]['dealer'])?$other_sale_arr[$key]['dealer']:'N/A'}}</td>

                <td>{{!empty($other_sale_arr[$key]['beat'])?$other_sale_arr[$key]['beat']:'N/A'}}</td>
             
                @if($b=='N/A')
                    <td style="background-color: #2add37">FOLLOWED</td>
                @else
                    @if ($a!=$b)
                        <td style="background-color: #d16429">CHANGED</td>
                    @else
                        <td style="background-color: #2add37">FOLLOWED</td>
                    @endif
                @endif

            @if(!empty($other_sale_arr[$key]['beat']) && !empty($mtp_beat[$key]) && $mtp_beat[$key]!=$other_sale_arr[$key]['beat'])
                    <td style="background-color: red">NO</td>
                @else
                    <td style="background-color: #00dd00">Yes</td>
                @endif
                @php
                $status = $record['status'];
                @endphp
                @if($status==1)
                <td>{{'Active'}}</td>
                @else
                <td>{{'De-Active'}}</td>
                @endif
                <td>{{!empty($time_of_first_call[$key])?$time_of_first_call[$key]:'N/A'}}</td>
                <td>{{empty($time_of_last_call[$key])?'N/A':$time_of_last_call[$key]}}</td>
                <td>{{!empty($checkout_data[$key]['work_date'])?date('H:i:s',strtotime($checkout_data[$key]['work_date'])):'N/A'}}</td>
                <td>{{!empty($other_sale_arr[$key]['total_outlet'])?$other_sale_arr[$key]['total_outlet']:0}}</td>
                <td>{{!empty($visit_count_data[$key])?$visit_count_data[$key]:0}}</td>
                <td>{{!empty($productive_calls[$key])?$productive_calls[$key]:0}}</td>
                <td>{{!empty($new_outlet[$key])?$new_outlet[$key]:0}}</td>
                <td>{{!empty($new_productive_outlet[$key])?$new_productive_outlet[$key]:0}}</td>
                <td>{{!empty($checkout_data[$key]['tc'])?$checkout_data[$key]['tc']:'0'}}</td>
                <td>{{!empty($checkout_data[$key]['tpc'])?$checkout_data[$key]['tpc']:'0'}}</td>
                <td>{{!empty($checkout_data[$key]['tsv'])?$checkout_data[$key]['tsv']:'0'}}</td>
                <td>{{$alpha=!empty($kg[$key])?$kg[$key]/1000:0}}</td>
                <td>{{$beta=!empty($rv[$key])?$rv[$key]:0}}</td>
                <td>{{$int_kg=0}}</td>
                <td>{{$int_rv=0}}</td>
                <td>{{$alpha+$int_kg}}</td>
                <td>{{$beta+$int_rv}}</td>

                <?php   
                    $total_outlet[] =    !empty($other_sale_arr[$key]['total_outlet'])?$other_sale_arr[$key]['total_outlet']:0;
                    $visit_count[] =     !empty($visit_count_data[$key])?$visit_count_data[$key]:0;
                    $productive_call[] = !empty($productive_calls[$key])?$productive_calls[$key]:0;
                    $new_outlet_total[] = !empty($new_outlet[$key])?$new_outlet[$key]:0;
                    $new_productive_outlet_total[] = !empty($new_productive_outlet[$key])?$new_productive_outlet[$key]:0;
                
                ?>

     
                    @foreach($catalog as $key5=>$data2)
                        <td>{{!empty($new_arr[$key][$key5]['total_row'])?$new_arr[$key][$key5]['total_row']:0}}</td>                       
                        <td>{{!empty($new_arr[$key][$key5]['total_weight'])?$new_arr[$key][$key5]['total_weight']/1000:0}}</td>
                        <td>{{!empty($new_arr[$key][$key5]['total_price'])?$new_arr[$key][$key5]['total_price']:0}}</td>
                        <?php
                       
                        $total_row1[$key5][]=!empty($new_arr[$key][$key5]['total_row'])?$new_arr[$key][$key5]['total_row']:0;
                        $total_price1[$key5][]=!empty($new_arr[$key][$key5]['total_price'])?$new_arr[$key][$key5]['total_price']:0;
                        ?>
                    
                    @endforeach
               

            </tr>
            <?php
             $checkout_tc[] = !empty($checkout_data[$key]['tc'])?$checkout_data[$key]['tc']:0;
             $checkout_pc[] = !empty($checkout_data[$key]['tpc'])?$checkout_data[$key]['tpc']:0;
             $checkout_tsv[] = !empty($checkout_data[$key]['tsv'])?$checkout_data[$key]['tsv']:0;
             $total_kg[] = !empty($kg[$key])?$kg[$key]/1000:0;
            $total_rv[] = !empty($rv[$key])?$rv[$key]:0;
            $TOTAL_SALE_SEC_ID_sum_kg+=$alpha+$int_kg;
            $TOTAL_SALE_SEC_ID_sum_rv+=$beta+$int_rv;
           
            ?>

        @endforeach
        <tr>
                <td colspan="30" bgcolor="#6666ff">{{Lang::get('common.grand')}} {{Lang::get('common.total')}}</td>
                <td>{{array_sum($total_outlet)}}</td>
                <td>{{array_sum($visit_count)}}</td>
                <td>{{array_sum($productive_call)}}</td>
                <td>{{array_sum($new_outlet_total)}}</td>
                <td>{{array_sum($new_productive_outlet_total)}}</td>
                <td>{{array_sum($checkout_tc)}}</td>
                <td>{{array_sum($checkout_pc)}}</td>
                <td>{{array_sum($checkout_tsv)}}</td>
                <td>{{array_sum($total_kg)}}</td>
                <td>{{array_sum($total_rv)}}</td>
                <td></td>
                <td></td>
                <td>{{$TOTAL_SALE_SEC_ID_sum_kg}}</td>
                <td>{{$TOTAL_SALE_SEC_ID_sum_rv}}</td>
              
                @if(!empty($catalog))
                    @foreach($catalog as $key2=>$data2)      
                <td>{{!empty($total_row1[$key2])?array_sum($total_row1[$key2]):0}}</td>             
                <td></td>
                <td>{{!empty($total_price1[$key2])?array_sum($total_price1[$key2]):0}}</td>
                
                @endforeach
                @endif
               

                  

                   
            </tr>
    @else

        <tr>
            <td colspan="74">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
</table>


<script>

function fnExcelReport() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('simple-table'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();
        sa = txtArea1.document.execCommand("SaveAs", true, "file.xlxs");
    }
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}



</script>
