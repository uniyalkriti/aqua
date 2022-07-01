<script src="widgets/barcode/jquery-barcode-2.0.1.js" type="text/javascript"></script>
<script type="text/javascript">
// BeginOAWidget_Instance_2489022: #bcContent
function generate_barcode(text,barid)
{
	var type = "code128";
			$("#"+barid).barcode(text, type,{
		    	barWidth: 1,
    			barHeight: 20,
    			moduleSize: 10,
				showHRI: true,
				addQuietZone: true,
				marginHRI: 5,
				bgColor: "#FFFFFF",
				color: "#000000",
				fontSize: 8,
				output: "css",
				posX: 0,
				posY: 0
			});
			 
			$("."+barid).barcode(text, type,{
		    	barWidth: 1,
    			barHeight: 20,
    			moduleSize: 10,
				showHRI: true,
				addQuietZone: true,
				marginHRI: 5,
				bgColor: "#FFFFFF",
				color: "#000000",
				fontSize: 10,
				output: "css",
				posX: 0,
				posY: 0
			});
}
</script>
<?php
function barcode($data,$barId = 'barcode')
{
	echo '<script type="text/javascript">
				generate_barcode("'.$data.'","'.$barId.'");
			</script>';
}
?>