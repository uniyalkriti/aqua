@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
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
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
    <thead>
    <tr>
        {{--<th>S.No.</th>--}}
        <th>Month</th>
        <th>Year</th>
        <th colspan="4">FCT (IN CASES)</th>
        <th colspan="4">FPC (IN CASES)</th>
        <th colspan="4">Match Box (IN CASES)</th>
        <th colspan="4">ELAICHI (IN CASES)</th>
        <th colspan="4">B-KOOL (IN CASES)</th>
        <th colspan="4">PALLETS (IN CASES)</th>
        <th colspan="4">AGARBATTI (IN CASES)</th>
        <th colspan="4">SILVER LEAVES (IN CASES)</th>
        <th colspan="4">WAFFERS (IN CASES)</th>
        <th colspan="4">RINGS & PUFFS (IN CASES)</th>
        <th colspan="4">TEDA MEDA (IN CASES)</th>
        <th colspan="4">NAMKEEN (IN CASES)</th>
        <th>RV LAKHS Without Tax</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($monthArr) && count($monthArr)>0)
        @foreach($monthArr as $key=>$data)
    <tr><td rowspan="7">{{$data}}</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">MAY</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">JUN</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">JUL</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">AUG</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">SEPT</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">OCT</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">NOV</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">DEC</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">JAN</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">FEB</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td rowspan="7">MAR</td></tr>
    <tr><td>15 - 16</td></tr>
    <tr><td>16 - 17</td></tr>
    <tr><td>17 -18</td></tr>
    <tr><td>% GROWTH</td></tr>
    <tr><td>TGT 18-19</td></tr>
    <tr><td>% GROWTH</td></tr>
    @endforeach
    <tr><td>TOTAL</td></tr>

    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>