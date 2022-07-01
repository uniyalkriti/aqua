@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th rowspan="2">STATE</th>
        <th rowspan="2">OUTLET CATEGORY</th>
        <th colspan="6">APRIL</th>
        <th colspan="6">MAY</th>
        <th colspan="6">JUNE</th>
        <th colspan="6">JULY</th>
        <th colspan="6">AUGUST</th>
        <th colspan="6">SEPTEMBER</th>
        <th colspan="6">OCTOBER</th>
        <th colspan="6">NOVEMBER</th>
        <th colspan="6">DECEMBER</th>
        <th colspan="6">TOTAL</th>
    </tr>
    <tr>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
        <th>OUTLET AS ON 1ST OF OF THIS MONTH</th>
        <th>OUTLET ADDED DURING THE MONTH</th>
        <th>TOTAL OUTLET</th>
        <th>ACTIVE OUTLET</th>
        <th>% ACTIVE</th>
        <th>UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)

    @else
        <tr>
            <td colspan="62">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>