
$(document).ready(function() {
 $("#datepicker").datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-1:+1",
			dateFormat : "dd/M/yy"
		});
 $("#qdatepicker").datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-1:+1",
			dateFormat : "dd/mm/yy"
		});	
		
  $(".datepicker" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-30:+1",
			dateFormat : "dd/m/yy"
		});
	
  $(".qdatepicker" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-1:+1",
			dateFormat : "dd/mm/yy"
		});	
	
  $(".datepicker_cd" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : false,
			yearRange : "-1:+1",
			dateFormat : "dd/mm/yy",
			minDate: -0, 
			maxDate: "+1M +1D"
		});
  $(".qdatepicker_cd" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : false,
			yearRange : "-1:+1",
			dateFormat : "dd/mm/yy",
			minDate: -0, 
			maxDate: "+1M +1D"
		});			
		
  $(".datepicker_pastdate" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-80:+1",
			dateFormat : "dd/M/yy",			
			maxDate: "+0M +0D"
		});
 $(".qdatepicker_pastdate" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-80:+1",
			dateFormat : "dd/mm/yy",			
			maxDate: "+0M +0D"
		});	
		
  $(".datepicker_pastdatefew" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-1:+1",
			dateFormat : "dd/M/yy",			
			maxDate: "+0M +0D"
		});
		
 $(".qdatepicker_pastdatefew" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: false,
			changeYear : true,
			yearRange : "-1:+1",
			dateFormat : "dd/mm/yy",			
			maxDate: "+0M +0D"
		});	
});
