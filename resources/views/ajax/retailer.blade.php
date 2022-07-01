@if(!empty($locationArr))
    <form method="post" action="retailer-assign" id="retailer-assign">
        <input type="hidden" name="distributor" value="{{$distributor_id}}">
        <input type="hidden" name="user_id" value="{{$user_id}}">
        @foreach($locationArr as $k=>$d)
            <div class="col-md-3">
                <input type="checkbox" name="beat[]" value="{{$d->l5_id}}" id="ckbCheckAll">
                <label class="control-label bolder blue">{{$d->l5_name}}</label>

                @if(!empty($retArr[$d->l5_id]))
                    @foreach($retArr[$d->l5_id] as $key=>$data)
                        <div class="checkbox">
                            <label>
                                <input name="retailer[]" value="{{$data->id}}" class="ace ace-checkbox-2 checkBoxClass"
                                       {{!empty($udr) && in_array($data->id,$udr)?'checked':''}} type="checkbox">
                                <span class="lbl"> {{$data->name}}</span>
                            </label>
                        </div>
                    @endforeach
                @endif


            </div>
        @endforeach
        <div class="row">
            <div class="col-md-offset-5 col-md-9">
                <button class="btn btn-sm btn-info" type="submit">
                    <i class="ace-icon fa fa-check bigger-110"></i>
                    Submit
                </button>
            </div>
        </div>
    </form>
    <div id="result4"></div>
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
@endif