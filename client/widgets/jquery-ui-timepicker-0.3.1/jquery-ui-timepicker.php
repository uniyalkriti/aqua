<!--<link rel="stylesheet" href="widgets/jquery-ui-timepicker-0.3.1/include/jquery-ui-1.8.14.custom.css" type="text/css" />-->
<link rel="stylesheet" href="widgets/jquery-ui-timepicker-0.3.1/jquery.ui.timepicker.css?v=0.3.1" type="text/css" />
<script type="text/javascript" src="widgets/jquery-ui-timepicker-0.3.1/include/jquery-1.5.1.min.jss"></script><!--
<script type="text/javascript" src="widgets/jquery-ui-timepicker-0.3.1/include/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="widgets/jquery-ui-timepicker-0.3.1/include/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="widgets/jquery-ui-timepicker-0.3.1/include/jquery.ui.tabs.min.js"></script>-->
<script type="text/javascript" src="widgets/jquery-ui-timepicker-0.3.1/jquery.ui.timepicker.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#timepicker1').timepicker(
	{
		// Options
		timeSeparator: ':',           // The character to use to separate hours and minutes. (default: ':')
		showLeadingZero: true,        // Define whether or not to show a leading zero for hours < 10.  default: true)
		showPeriod: true,            // Define whether or not to show AM/PM with selected time. (default: false)s
		showMinutesLeadingZero: true,
		// custom hours and minutes
		hours: {
		starts: 0,                // First displayed hour
		ends: 23                 // Last displayed hour
		}
	}
  );
  $('#timepicker2').timepicker(
	  {
		  // Options
		  timeSeparator: ':',           // The character to use to separate hours and minutes. (default: ':')
		  showLeadingZero: true,        // Define whether or not to show a leading zero for hours < 10.  default: true)
		  showPeriod: true,            // Define whether or not to show AM/PM with selected time. (default: false)s
		  showMinutesLeadingZero: true,
		  // custom hours and minutes
		  hours: {
		  starts: 0,                // First displayed hour
		  ends: 23                 // Last displayed hour
		  }
	  }
	);
});
</script>
