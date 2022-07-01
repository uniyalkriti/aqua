<a onclick="fnExcelReport()" href="javascript:void(0)"
   class="nav-link"><i
            class="fa fa-file-excel-o "></i> Export Excel</a>
<style>
    table {
        border-collapse: collapse !important;
    }

    table, th, td {
        border: 1px solid black !important;
    }
    th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr style="background-color: #7BB0FF !important;">
        <td colspan="7"><strong>DISTRIBUTOR NAME: </strong> {{$dealer->name}}</td>
        <td colspan="6"><strong> TOTAL NO.OF OUTLETS IN DISTRIBUTOR AREA: <b>{{$outlet->total}}</b><br> NO.OF ISR
                : {{ $isrCount->total }} (@ 1 ISR ON
                EVERY 300 OUTLETS)</strong>
        </td>
        <td colspan="6" rowspan="4">DISTRIBUTOR TARGET V/S ACHIEVEMENT V/S GROWTH OVER PREVIOUS PERIOD</td>
        <!-- TARGET -->
    </tr>
    <tr style="background-color: #7BB0FF !important;">
        <td colspan="7"><strong>DISTRIBUTOR ADDRESS : </strong>{{ $dealer->address }}, {{ $dealer->town }}
            , {{ $dealer->state }}</td>
        <td colspan="6"><strong>THIS UNIT / No. OF BEAT:</strong>&nbsp;&nbsp;{{ $noBeat->total }}
            <br> <strong>NO.OF OUTLETS:</strong> &nbsp;&nbsp;{{$outlet->total}}
        </td>
        <!-- TARGET -->
    </tr>
    <tr style="background-color: #7BB0FF !important;">
        <td colspan="7">POPULATION OF DISTRIBUTOR AREA : _______________TOWN : _______________</td>
        <td colspan="6">READY STOCK UNIT:_____________________________
            <br><strong>REGISTRATION NO: </strong> {{ $dealer->dealer_code }}
        </td>
        <!--TARGET --->
    </tr>
    <tr style="background-color: #7BB0FF !important;">
        <td colspan="7">NO.OF OUTLETS IN DISTRIBUTOR AREA : ( POPULATION÷200 ) ________________</td>
        <td colspan="6">READY STOCK UNIT BRANDING: DONE / NOT DONE; IF NOT DONE THEN TARGETTED DATE OF
            BRANDING:________________
        </td>
        <!--TARGET -->
    </tr>
    <tr style="background-color: #7BB0FF !important;">
        <th colspan="3"> #</th>
        <th> APR (RV LACS)</th>
        <th> MAY (RV LACS)</th>
        <th> JUN (RV LACS)</th>
        <th> JULY (RV LACS)</th>
        <th> AUG (RV LACS)</th>
        <th> SEPT (RV LACS)</th>
        <th> OCT (RV LACS)</th>
        <th> NOV (RV LACS)</th>
        <th> DEC (RV LACS)</th>
        <th> JAN (RV LACS)</th>
        <th> FEB (RV LACS)</th>
        <th> MAR (RV LACS)</th>
        <th colspan="4"> TOTAL</th>

    </tr>
    <tr>
        <th colspan="3">TARGET</th>
        <?php $dti = 0;
        $total_target = 0;
        ?>
        @foreach ( $dealer_target as $value_target )
            <td>{{ $value_target->target }}</td>
            <?php $total_target = $total_target + $value_target->target; ?>
        @endforeach


        <th colspan="4">{{ $total_target }}</th>
    </tr>
    <tr>
        <th colspan="3">ACHIEVEMENT</th>
        <?php $dta = 0;
        $total_ach = 0;
        ?>
        @foreach ( $dealer_target as $value_target )
            <td>{{ $value_target->achievement }}</td>
            <?php $total_ach = $total_ach + $value_target->achievement; ?>
        @endforeach


        <th colspan="4">{{ $total_ach }}</th>

    </tr>
    <!--NEW TARGET END-->
    <tr style="background-color: #7BB0FF !important;">
        <td rowspan="3">Beat No.</td>
        <td rowspan="3">Name of Market</td>
        <td colspan="17">NO. OF RETAILER</td>
    </tr>
    <tr style="background-color: #7BB0FF !important;">
        <td colspan="7">CLASSIFICATION NOs.</td>
        <td colspan="10">CATEGORISATION OF OUTLETS</td>
    </tr>
    <tr style="background-color: #7BB0FF !important;">
        <td>TOTAL</td>
        <td>PLATINUM</td>
        <td>DIAMOND</td>
        <td>GOLD</td>
        <td>SILVER</td>
        <td>SEMI WS</td>
        <td>WS</td>
        <td>TOTAL</td>
        <td>PAAN</td>
        <td>CONFECTIONARY</td>
        <td>KIRYANA</td>
        <td>GENERAL STORE</td>
        <td>CHEMIST</td>
        <td>SELF SERVICE</td>
        <td>FOOD COURT</td>
        <td>DHABA</td>
        <td>CATERERS</td>
    </tr>
    <?php $countRecord = 1; ?>
    @if(!empty($records))
        @foreach($records as $key=>$d)
            <tr>
                <td>
                    {{ $countRecord }}
                </td>
                <td>{{$d['market']}}</td>
                <td>{{$d['platinum']+$d['diamond']+$d['gold']+$d['silver']}}</td>
                <td>{{$d['platinum']}}</td>
                <td>{{$d['diamond']}}</td>
                <td>{{$d['gold']}}</td>
                <td>{{$d['silver']}}</td>
                <td>{{$d['sws']}}</td>
                <td>{{$d['ws']}}</td>
                <td>{{$d['t2']}}</td>
                <td>{{isset($d['categories'][1])?$d['categories'][1]:0}}</td>
                <td>{{isset($d['categories'][2])?$d['categories'][2]:0}}</td>
                <td>{{isset($d['categories'][3])?$d['categories'][3]:0}}</td>
                <td>{{isset($d['categories'][4])?$d['categories'][4]:0}}</td>
                <td>{{isset($d['categories'][5])?$d['categories'][5]:0}}</td>
                <td>{{isset($d['categories'][6])?$d['categories'][6]:0}}</td>
                <td>{{isset($d['categories'][7])?$d['categories'][7]:0}}</td>
                <td>{{isset($d['categories'][8])?$d['categories'][8]:0}}</td>
                <td>{{isset($d['categories'][9])?$d['categories'][9]:0}}</td>
            </tr>
            <?php $countRecord++; ?>
        @endforeach
    @endif
    <tr>
        <td colspan="19">

        </td>
    </tr>
    <tr>
        <td colspan="4">RETAILER RATING CRITERIA</td>
        <td colspan="3">NAME</td>
        <td colspan="2">SIGN</td>
        <td colspan="2">DATE</td>
        <td colspan="8">DISTRIBUTOR RUBBER STAMP, SIGN & DATE</td>
    </tr>
    <tr>
        <td colspan="2">CLASS</td>
        <td colspan="2">PURCHASE VALUE DURING MONTH</td>
        <td>SO / SE</td>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td colspan="8" rowspan="7  "></td>
    </tr>
    <tr>
        <td colspan="2">PLATINUM</td>
        <td colspan="2">RS.10000/- & ABOVE + PROMINENT DISPLAY</td>
        <td>ASM</td>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">DIAMOND</td>
        <td colspan="2">RS. 10000/- & ABOVE</td>
        <td>RSM</td>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">GOLD</td>
        <td colspan="2">RS. 7500/- TO RS. 9999/-</td>
        <td>AGM - GM</td>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">SILVER</td>
        <td colspan="2">RS. 5000/- TO RS. 7499/-</td>
        <td colspan="7" rowspan="3"></td>
    </tr>
    <tr>
        <td colspan="2">SEMI WS</td>
        <td colspan="2">BUYING MINIMUM 8 CASES OF OUR PRODUCTS</td>
    </tr>
    <tr>
        <td colspan="2">WS</td>
        <td colspan="2">BUYING MINIMUM 25 CASES OF OUR PRODUCTS</td>
    </tr>
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