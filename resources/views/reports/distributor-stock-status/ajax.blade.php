@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif
<style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }

    #simple-table th {
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr>
        <td colspan="10"><h3>Distributor Stock Status</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>CATEGORY</th>
        <th>SUBCATEGORY</th>
        <th>PRODUCT</th>
        <th>PRICE</th>
        <th>OPENING STOCK AS ON FIRST DATE OF THE MONTH</th>
        <th>PRIMARY SALES DURING THE MONTH</th>
        <th>TOTAL STOCK</th>
        <th>SECONDARY SALES DURING THE MONTH</th>
        <th>CLOSING STOCK AS ON LAST DAY OF THE MONTH</th>
    </tr>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $k=> $r)
        <tr>
            <td>{{$k+1}}</td>
            <td>{{$r['c1']}}</td>
            <td>{{$r['c2']}}</td>
            <td>{{$r['product']}}</td>
            <td>{{$r['pr_rate']}}</td>
            <td>{{$a=$r['opening']}}</td>
            <td>{{$b=$r['primary_sale']}}</td>
            <td>{{$total=$a+$b}}</td>
            <td>{{$c=$r['seconday_sale']}}</td>
            <td>{{$total-$c}}</td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="10">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>