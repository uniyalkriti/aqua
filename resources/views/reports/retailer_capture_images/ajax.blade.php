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

    #simple-table th {
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr><td colspan="16"><h3>Merchandiser Capture Report</h3></td></tr>
    <tr class="info" style="color: black;">
        <th>S.No.</th>
        <th>Zone</th>
        <th>Region</th>
        <th>Emp. Code</th>
        <th>Name</th>
        <th>Designation</th>
        <th>Distributor</th>
        <th>Date</th>
        <th>Time</th>
        <th>Image 1</th>
        <th>Image 2</th>
        <th>Image 3</th>
    </tr>
    <tbody>
    <?php $i = 1; ?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
        <?php  ?>
                <tr  class="">
                    <td>{{$i}}</td>
                    <td>{{$data->l1_name}}</td>
                    <td>{{$data->l2_name}}</td>
                    <td>{{$data->emp_code}}</td>
                    <td>{{$data->user_name}}</td>
                    <td>{{$data->role_name}}</td>
                    <td>{{$data->dealer_name}}</td>
                    <td>{{$data->date}}</td>
                    <td>{{$data->check_in}}</td>
                    <td>
                    <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                    src="http://bambinoagro.msell.in/bambinoagro_api/webservices/mobile_images/RetailerCaptureImages/{{!empty($data->image_name1)?$data->image_name1:'N/A'}}" 
                    onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/avatar2.png')}}';" />
                    </td>
                    <td>
                    <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                    src="http://bambinoagro.msell.in/bambinoagro_api/webservices/mobile_images/RetailerCaptureImages/{{!empty($data->image_name2)?$data->image_name2:'N/A'}}" 
                    onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/avatar2.png')}}';" />
                    </td>
                    <td>
                    <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                    src="http://bambinoagro.msell.in/bambinoagro_api/webservices/mobile_images/RetailerCaptureImages/{{!empty($data->image_name3)?$data->image_name3:'N/A'}}" 
                    onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/avatar2.png')}}';" />
                    </td>
                </tr>
           <?php $i++; ?>     
            @endforeach
    @endif

    </tbody>
</table>