
@if(!empty($rows))
    <div class="widget-box widget-color-blue">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-table"></i>
                 List
            </h5>
        </div>
        <div class="widget-body">
            <div class="widget-main no-padding">
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Landline</th>
                        <th>Other Number</th>
                        <th>Assign All <br> <input type="checkbox" onchange="checkAll(this)"></th>

                    </tr>
                    @foreach($rows as $key=>$data)
                   
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->address}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->landline}}</td>
                            <td>{{$data->other_numbers}}</td>
                            <td>
                                
                             
                                    @if(in_array($data->id,$dealer_assign))
                                    <input type="checkbox" name="dealer_check_test[]" value="{{$data->id}}" {{in_array($data->id,$dealer_assign)?'checked':''}}>
                                    @else
                                    <input type="checkbox" name="dealer_check_test[]" value="{{$data->id}}" >
                                    @endif
                                    

                               
                               
                            </td>
                        </tr>
                        
                    @endforeach
                </table>
                <input type="hidden" name="product_rate_list_template_type_distributor" value="{{$product_rate_list_template_type_distributor}}">
                
            </div>
        </div>
    </div>
@endif
<div align="center">
    <input type="submit" value="Proceed" style="background-color:black;color:white;width:150px; height:40px;">
</div>

 <script type="text/javascript">
      function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
 </script>