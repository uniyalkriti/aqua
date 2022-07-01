<div class="control-group">
<label class="control-label bolder blue">Beat</label>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<label class="control-label bolder blue">Assign Senior also</label>
<input type="checkbox"  name = "senior_assing" value="{{1}}" >
<br>
<input type="hidden" name="user_id" value="{{$user_id}}">
<input type="checkbox" id="ckbCheckAll">Select All







<div class="widget-body">
    <div class="widget-main no-padding">
<div class="row">
    @if(!empty($rows))
        @foreach($rows as $k=>$d)
        <div class="col-md-6">
            <table class="table table-striped table-bordered table-hover" width="50%">
                <tr>
                    <td class="checkbox">
                       {{$k+1}}
                        <label class="control-label bolder blue">
                            <input type="hidden" name="distributor[]" value="{{$d->dealer_id}}" id="distributor">
                              {{$d->name}}
                       </label>
                    </td>
                    @if(!empty($dsr[$d->dealer_id]))
                        @foreach($dsr[$d->dealer_id] as $key=>$data)
                            <td class="checkbox">
                                {{$key+1}}
                                <label>
                                    <input id ="dist12" name="beat[{{$d->dealer_id}}][]" value="{{$data->l5_id}}" class="ace ace-checkbox-2 checkBoxClass"
                                           {{!empty($dlrl) && in_array($data->l5_id,$dlrl) &&  in_array($data->dealer_id,$dlrl_1)  ?'checked':''}} type="checkbox">  
                                    <span class="lbl"> {{$data->l5_name}}</span>      
                                </label>
                            </td>
                        @endforeach
                    @endif
                        <br/>
                </tr>
              </table>
          </div>
        @endforeach
       
    @endif
</div>
        <div align="center">
        <input type="submit" value="Proceed" class="btn btn-sm btn-primary">
        </div>
    </div>
  </div>
</div>   
    <script type="text/javascript">

$("#ckbCheckAll").click(function () {
    $(".checkBoxClass").prop('checked', $(this).prop('checked'));
});

$(".checkBoxClass").change(function(){
    if (!$(this).prop("checked")){
        $("#ckbCheckAll").prop("checked",false);
    }
});


</script>