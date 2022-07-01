$(function(){
  $("#imageContainer").on("mouseover", function(){
	 $("#imageContainer").photoZoom(
	  {
		  zoomStyle : {
		  "border":"1px solid #ccc",
		  "background-color":"#fff",
		  "box-shadow":"0 0 5px #888"
		  },
		  onMouseOver : function(currentImage){
			  if($(currentImage).attr('class') == 'nozoom');
				  return;
	  }
		  
	});
  });
});