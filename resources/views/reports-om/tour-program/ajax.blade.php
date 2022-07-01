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
    #simple-table th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <thead>
    <tr>
        <th rowspan="2">S.No.</th>
        <th rowspan="2">DAY</th>
        <th rowspan="2">USER</th>
        <th rowspan="2">DATE</th>
        <th rowspan="2">TOWN</th>
        <th rowspan="2">DISTRIBUTOR</th>
        <th rowspan="2">DISTRIBUTOR BEAT</th>
        <th rowspan="2">TASK FOR THE DAY</th>
        <th colspan="6">IN CASE OF RETAILING TARGET FOR THE DAY</th>
    </tr>
    <tr>
        <th>PC</th>
        <th>RD (RV LAKHS)</th>
        <th>COLLECTION (RV LAKHS)</th>
        <th>PRIMARY ORDER (RV LAKHS)</th>
        <th>NEW OUTLET OPENING</th>
        <th>ANY OTHER TASK</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($plans) && count($plans)>0)
        @foreach($plans as $key=>$plan)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{date('l',strtotime($plan->working_date))}}</td>
                <td>{{$plan->name}}</td>
                <td>{{$plan->working_date}}</td>
                <td>{{$plan->town_name}}</td>
                <td>{{$plan->dealer_name}}</td>
                <td>{{!empty($plan->bname)?$plan->bname:'N/A'}}</td>
                <td>{{isset($work_status[$plan->working_status_id])?$work_status[$plan->working_status_id]:'NA'}}</td>
                <td>{{$plan->pc}}</td>
                <td>{{$plan->rd}}</td>
                <td>{{$plan->collection}}</td>
                <td>{{$plan->primary_ord}}</td>
                <td>{{$plan->new_outlet}}</td>
                <td>{{$plan->any_other_task}}</td>
            </tr>
        @endforeach
        @else
        <tr>
            <td colspan="13">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
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