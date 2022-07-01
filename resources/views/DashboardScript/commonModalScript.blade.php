<script type="text/javascript">
     window.onload = function () {
      
       var checkTrial = <?= Auth::user()->custom; ?>

       if(checkTrial == 1){
            $(document.getElementById('checkTrial')).html(''); //  for user , distributor , retailer, ss
            $(document.getElementById('checkTrial')).append('<a class=" input-sm form-control btn btn-sm btn-info" style="margin-top: 27px;" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus mg-r-10"></i> {{Lang::get('common.add')}} Details</a>');


              $(document.getElementById('checkTrialLocation')).html(''); // for location and catalog and settings
            $(document.getElementById('checkTrialLocation')).append('<a class=" btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus mg-r-10"></i> {{Lang::get('common.add')}} Details</a>');


              $(document.getElementById('checkTrialImport')).html(''); // for import master



            $("#dynamic-table th:last-child, #dynamic-table td:last-child").html('');


       }

    }
</script>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:5px; padding-right: 5px;border: none;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="margin: 10px 10px; margin-top: -27px;">
       <span style="font-weight: bold;font-size: 50px;"> Note:</span > <br><span style="font-size: 20px;">Your Trial License Consists of only Limited Functionality and is sufficient for a brief Understanding.
To access this functionality and Experience the full power of mSELL kindly contact 
Suraj Pratap Singh on +91- 84475 15262 
Also follow our Social Media profiles below for great content and regular updates.</span>
      </div>
   <!--  <div class="row">  
      <div class="col-md-12 mb-2" style="display: flex; justify-content: center;">
          <a href="#" target="blank" class="f-18 link color-main mr-30" style="margin-right: 30px;color: black;font-size: 22px;">
                      <i class="fab fa-linkedin-in"></i>

                  </a>
                  <a href="#" target="blank" class="f-18 link color-main mr-30" style="margin-right: 30px;color: black;font-size: 22px;">
                      <i class="fab fa-twitter">
                      </i>
                  </a>
                  <a href="#" target="blank" class="f-18 link color-main mr-30" style="color: black;font-size: 22px;">
                      <i class="fab fa-facebook-square">
                      </i>
                  </a>
      </div>
      </div> -->
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
   