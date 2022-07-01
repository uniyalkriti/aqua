<form method="post" id="distributor-beat" action="distributor-beat">
    @csrf
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
                                
                                    <input type="hidden" name="dealer_id" value="{{$data->id}}">
                                    <input type="hidden" name="user_id" value="{{$uuid}}">

                                    <input type="checkbox" name="dealer_check[]" value="{{$data->id}}">

                               
                                {{--<a data-toggle="modal" href="#myModal2" class="btn btn-primary">Launch modal</a>--}}
                            </td>
                        </tr>
                        
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endif
<div align="center">
                     <input type="submit" value="Proceed" style="background-color:black;color:white;width:150px; height:40px;" data-toggle="modal" href="#myModal2" >
</div>
 </form>
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