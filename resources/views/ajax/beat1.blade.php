@if(!empty($l5))
    <div class="widget-box widget-color-blue">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-table"></i>
                 List
            </h5>
        </div>
        <div class="widget-body">
            <div class="widget-main no-padding">
            <form method="get" id="" action="update-beat">
                <table class="table table-striped table-bordered table-hover">
                <input type="hidden" name="dealer_id" value="{{$dealer_id}}">
                <input type="hidden" name="l4id" value="{{$l4id}}">
                    <tr>
                        <th>S.No</th>
                        <th>Beat Name</th>
                        
                    </tr>
                    @foreach($l5 as $key=>$data)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->name}}</td> 
                            <td><input type="checkbox" {{!empty($dlrl) && in_array($data->id,$dlrl)?'checked':''}} value="{{$data->id}}" name="assignbeat[]"></td>   
 
                        </tr>
                    @endforeach
                </table>
                <div style="text-align:center">  
                <input type="submit" value="Assign">
              </div>
                </form>
            </div>
        </div>
    </div>
@endif