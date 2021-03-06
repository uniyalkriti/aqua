<body style="background-color:lavender">
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <table class="table table-bordered table-hover" id="tab_logic">
                <thead>
                    <tr >
                        <th class="text-center">
                            #
                        </th>
                        <th class="text-center">
                            User
                        </th>
                        <th class="text-center">
                            Password
                        </th>
                        <th class="text-center">
                            IP
                        </th>
                        <th class="text-center">
                            Country
                        </th>
                        <th class="text-center">
                            IP disponibility
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr id='addr0'>
                        <td>
                        1
                        </td>
                        <td>
                        <input type="text" name='user0'  placeholder='User' class="form-control"/>
                        </td>
                        <td>
                        <input type="text" name='pass0' placeholder='Password' class="form-control"/>
                        </td>
                        <td>
                        <input type="text" name='ip0' placeholder='IP' class="form-control"/>
                        </td>
                        <td>
                        <input type="text" name='country0' placeholder='Country' class="form-control"/>
                        </td>
                        <td>
                        <input type="text" name='ipDisp0' placeholder='IP Details' class="form-control"/>
                        </td>
                    </tr>
                    <tr id='addr1'></tr>
                </tbody>
            </table>
        </div>
    </div>
    <a id="add_row" class="btn btn-default pull-left">Add Row</a><a id='delete_row' class="pull-right btn btn-default">Delete Row</a>
</div>
<script src="jquery.min.js"></script>
<script src="bootstrap.min.js"></script>
            
<script>
         $(document).ready(function(){
      var i=1;
     $("#add_row").click(function(){
      $('#addr'+i).html("<td>"+ (i+1) +"</td><td><input name='user"+i+"' type='text' placeholder='User' class='form-control input-md'  /></td><td><input  name='pass"+i+"' type='text' placeholder='Password'  class='form-control input-md'></td><td><input  name='ip"+i+"' type='text' placeholder='IP'  class='form-control input-md'></td><td><input  name='country"+i+"' type='text' placeholder='Country'  class='form-control input-md'></td><td><input  name='ipDisp"+i+"' type='text' placeholder='IP details'  class='form-control input-md'></td>");

      $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');
      i++; 
  });
     $("#delete_row").click(function(){
         if(i>1){
         $("#addr"+(i-1)).html('');
         i--;
         }
     });

});

 Ext.override(Ext.selection.RowModel, {
    addRowOnLastEditorTab : true,
    onEditorLastRowTab: function() {
        var me = this,
            view = me.view,
            grid = me.view.up('gridpanel');
            
        grid.store.insert(grid.store.getCount(), {});
    },
    onEditorTab: function(editingPlugin, e) {
        var me = this,
            view = me.view,
            record = editingPlugin.getActiveRecord(),
            header = editingPlugin.getActiveColumn(),
            position = view.getPosition(record, header),
            direction = e.shiftKey ? 'left' : 'right';

        do {
            position = view.walkCells(position, direction, e, me.preventWrap);
        } while (position && !view.headerCt.getHeaderAtIndex(position.column).getEditor());

        if (position) {
            editingPlugin.startEditByPosition(position);
        } else {
            if(this.addRowOnLastEditorTab == true) {
                me.onEditorLastRowTab();
                this.onEditorTab(editingPlugin, e);
           }
        }
    }
}); 
</script>