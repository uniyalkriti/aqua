<a onclick="fnExcelReport()" href="javascript:void(0)"
   class="nav-link"><i
            class="fa fa-file-excel-o "></i> Export Excel</a>
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
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr>
        <!-- <td colspan="23"><h3>TOUR PROGRAM [{{date('M-Y',strtotime($month.'-01'))}}]</h3></td> -->
        <td colspan="28"><h3>{{Lang::get('common.tour_program')}} [{{date('Y-M-d',strtotime($from_date))}}] TO [{{date('Y-M-d',strtotime($to_date))}}]</h3></td>
    </tr>
    <tr>
        <th rowspan="2">{{Lang::get('common.s_no')}}</th>
        <th rowspan="2">{{Lang::get('common.date')}}</th>
       <!--  <th rowspan="2">{{Lang::get('common.location1')}}</th>
        <th rowspan="2">{{Lang::get('common.location2')}}</th> -->
        <th rowspan="2">{{Lang::get('common.location3')}}</th>
        <th rowspan="2">{{Lang::get('common.location4')}}</th>
        <th rowspan="2">{{Lang::get('common.location5')}}</th>
        <th rowspan="2">{{Lang::get('common.location6')}}</th>
        <th rowspan="2">{{Lang::get('common.emp_code')}}</th>
        <th rowspan="2">{{Lang::get('common.username')}}</th>
        <th rowspan="2">{{Lang::get('common.role_key')}}</th>
        <th rowspan="2">{{Lang::get('common.user_contact')}}</th>
        <th rowspan="2">{{Lang::get('common.senior_name')}}</th>
        <th rowspan="2">{{Lang::get('common.status')}}</th>

        <th rowspan="2">{{Lang::get('common.day')}}</th>

        <!-- <th rowspan="2">{{Lang::get('common.user')}} {{Lang::get('common.location5')}}</th> -->
     
        <th rowspan="2">{{Lang::get('common.tour_prg')}} {{Lang::get('common.location6')}}</th>
        <th rowspan="2">{{Lang::get('common.distributor')}}</th>
        <th rowspan="2">{{Lang::get('common.distributor')}} {{Lang::get('common.location7')}}</th>
        <th rowspan="2">{{Lang::get('common.task_of_the_day')}}</th>
        <th rowspan="2">APPROVAL {{Lang::get('common.status')}}</th>
        <th colspan="6">IN CASE OF RETAILING TARGET FOR THE DAY</th>
    </tr>
    <tr>
        <th>{{Lang::get('common.task_of_the_day')}}</th>
        <th>{{Lang::get('common.secondary_sale')}} {{Lang::get('common.rv_lakh')}}</th>
        <th>{{Lang::get('common.collection')}} {{Lang::get('common.rv_lakh')}}</th>
        <th>{{Lang::get('common.primary_sale')}} {{Lang::get('common.rv_lakh')}}</th>
        <th>NEW {{Lang::get('common.retailer')}} OPENING</th>
        @if($company_id == 61)
        <th>Remarks</th>
        @else
        <th>ANY OTHER TASK</th>
        @endif
    </tr>
    <tbody>
    @if(!empty($plans) && count($plans)>0)
        @foreach($plans as $key=>$plan)
            <?php
              $user_id = Crypt::encryptString($plan->user_id); 
              $senior_id = Crypt::encryptString($plan->senior_id); 
              $dealerId = Crypt::encryptString($plan->dealer_id); 
            ?>
            @php
            $status = $plan->status;
          
                $arr=[];
                if (!empty($plan->town_loc))
                {
                    $arr=explode('|',$plan->town_loc);
                }
                else
                {
                    $arr[0]='';$arr[1]='';$arr[2]='';$arr[3]='';
                }
                if(!empty($plan->l3_name))
                {
                    $statename = $plan->l3_name;
                    $regionname = $plan->l2_name;
                    $zonename = $plan->l1_name;

                }
                else
                {
                    $state = DB::table('location_3')
                            ->join('location_2','location_2.id','=','location_3.location_2_id')
                            ->join('location_1','location_1.id','=','location_2.location_1_id')
                            ->select('location_3.id as state_id','location_3.name as statename','location_2.name as region','location_1.name as zone')->where('location_3.id',$plan->person_state)
                            ->first();
                    
                    $statename = $state['statename'];
                    $regionname = $state['region'];
                    $zonename = $state['zone'];
                }
            @endphp
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$plan->working_date}}</td>

               <!--  <td>{{ !empty($zonename)?$zonename:'NA' }}</td>
                <td>{{ !empty($regionname)?$regionname:'NA' }}</td> -->
                <td>{{ !empty($statename)?$statename:'NA' }}</td>
                <td>{{ !empty($plan->l4_name)?$plan->l4_name:'NA' }}</td>
                <td>{{ !empty($plan->l5_name)?$plan->l5_name:'NA' }}</td>
                <td>{{ !empty($plan->l6_name)?$plan->l6_name:'NA' }}</td>

                <td>{{!empty($plan->emp_code)?$plan->emp_code:'NA'}}</td>
                @if(!empty($plan->name))
                <td><a href="{{url('user/'.$user_id)}}">{{$plan->name}}</a></td>
                @else
                <td><a href="{{url('user/')}}">NA</a></td>
                @endif
              
                <td>{{!empty($plan->role)?$plan->role:'NA'}}</td>
                <td>{{!empty($plan->mobile)?$plan->mobile:''}}</td>
                   @if(!empty($plan->senior))
                <td><a href="{{url('user/'.$senior_id)}}">{{$plan->senior}}</a></td>
                   @if($status==1)
                <td>{{'Active'}}</td>
                @else
                <td>{{'De-Active'}}</td>
                @endif
                <td>{{date('l',strtotime($plan->working_date))}}</td>

                @else
                <td><a href="{{url('user/')}}">NA</a></td>
                @endif

             
                <?php
                    $town_array = explode(',',$plan->town);
                    $final_town = array();
                    foreach($town_array as $tk => $tv){
                        $final_town[] = !empty($location_6[$tv])?$location_6[$tv]:'';
                    }
                ?>

                <td>{{implode(',',$final_town)}}</td>




                 <?php
                    $dealer_array = explode(',',$plan->dealer_id);
                    $final_dealer = array();
                    foreach($dealer_array as $dk => $dv){
                        $final_dealer[] = !empty($dealer_name_array[$dv])?$dealer_name_array[$dv]:'';
                    }
                ?>
                <td>{{implode(',',$final_dealer)}}</td>

                  <?php
                    $beat_array = explode(',',$plan->locations);
                    $final_beat = array();
                    foreach($beat_array as $bk => $bv){
                        $final_beat[] = !empty($location_7[$bv])?$location_7[$bv]:'';
                    }
                ?>
                <td>{{implode(',',$final_beat)}}</td>


                <td>{{isset($work_status[$plan->working_status_id])?$work_status[$plan->working_status_id]:'NA'}}</td>
                <td>
                    @if($plan->admin_approved==1)
                        <span class="label label-lg label-success arrowed-in arrowed-in-right">Approved</span>
                    @elseif($plan->admin_approved==2)
                        <span class="label label-lg label-yellow arrowed-in arrowed-in-right">Modified</span>
                    @else
                        <span class="label label-lg label-danger arrowed-in arrowed-in-right">Not Approved</span>
                    @endif
                </td>
                <td>{{$a[]=$plan->pc}}</td>
                <td>{{$b[]=$plan->rd}}</td>
                <td>{{$c[]=$plan->collection}}</td>
                <td>{{$d[]=$plan->primary_ord}}</td>
                <td>{{$e[]=$plan->new_outlet}}</td>
                <td>{{$plan->any_other_task}}</td>
            </tr>
        @endforeach
        <tr>
            <td style="color: white;background-color: #7BB0FF;" colspan="18"><b>{{Lang::get('common.total')}}</b></td>
            <td>{{array_sum($a)}}</td>
            <td>{{array_sum($b)}}</td>
            <td>{{array_sum($c)}}</td>
            <td>{{array_sum($d)}}</td>
            <td>{{array_sum($e)}}</td>
            <td></td>
        </tr>
    @else
        <tr>
            <td colspan="26">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>
<script>
    function fnExcelReport() {
    var filename = "Tour Program Report"

        var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
        var textRange;
        var j = 0;
        tab = document.getElementById('simple-table'); // id of table

        for (j = 0; j < tab.rows.length; j++) {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            //tab_text=tab_text+"</tr>";
        }

        tab_text = tab_text + "</table>";
          var a = document.createElement('a');
        var data_type = 'data:application/vnd.ms-excel';
        a.href = data_type + ', ' + encodeURIComponent(tab_text);
        a.download = filename + '.xls';
        a.click();
        // tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        // tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
        // tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

        // var ua = window.navigator.userAgent;
        // var msie = ua.indexOf("MSIE ");

        // if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        // {
        //     txtArea1.document.open("txt/html", "replace");
        //     txtArea1.document.write(tab_text);
        //     txtArea1.document.close();
        //     txtArea1.focus();
        //     sa = txtArea1.document.execCommand("SaveAs", true, "file.xlxs");
        // }
        // else                 //other browser not tested on IE 11
        //     sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

        // return (sa);
    }
</script>