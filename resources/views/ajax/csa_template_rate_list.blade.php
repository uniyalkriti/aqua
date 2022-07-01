
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
                        <th>{{Lang::get('common.location1')}}</th>
                        <th>{{Lang::get('common.location2')}}</th>
                        <th>{{Lang::get('common.location3')}}</th>
                        <th>{{Lang::get('common.csa')}} Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Assign All <br> <input type="checkbox" onchange="checkAll(this)"></th>

                    </tr>
                    @foreach($rows as $key=>$data)
                   
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->l1_name}}</td>
                            <td>{{$data->l2_name}}</td>
                            <td>{{$data->l3_name}}</td>
                            <td>{{$data->csa_name}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->mobile}}</td>
                           
                            <td>
                                
                                   
                             
                                    <input type="hidden" name="product_rate_list_template_type_csa" value="{{$product_rate_list_template_type_csa}}">

                                    <input type="checkbox" name="csa_check[]" value="{{$data->c_id}}">

                               
                               
                            </td>
                        </tr>
                        
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endif
<div align="center">
                     <input type="submit" value="Proceed" style="background-color:black;color:white;width:150px; height:40px;"  >
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