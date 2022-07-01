<script>
  $(function() {
    $('.tabletoexcel').click(function(){
      var rep = $(this).attr('rep-type');
      
      if(rep=='')
      {
      	rep = 'excel-report';
      }

      $("#searchdata").table2excel({
        exclude: ".noExl",
        name: rep,
        filename: rep,
        fileext: ".xls",
        exclude_img: true,
        exclude_links: true,
        exclude_inputs: true
      });
    })
  });
</script>
<script type="text/javascript">
  $(document).ready(function() {
  $('select').focus(
  function(){
      $(this).css({'background-color' : '#F5D0A9'});
  });

  
  });
</script>
</div><!-- container div ends here -->
  </div><!-- wrapper div ends here -->
  <div id="footer">
    <p><?php echo FOOTER;?>
    Designed & Developed by <a href="http://www.manacleindia.com" target="_blank">Manacle Technologies Pvt. Ltd.</a>
    </p>
  </div><!-- wrapper div ends here -->
  <?php js_span_clear(); // To clear the span with class awm?>
</body>
</html>
<?php ob_end_flush(); ?>