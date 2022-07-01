<!-- yui keyboard enhance menu starts here -->
<style type="text/css">
#nav { 
	width: 100%;
	font-size:14px;
}
#nav a:hover, #nav li:hover, #nav a:focus, #nav li:focus { 
	background-color: #DF7401; 
	color: #FFFFFF;	
}
#nav .yui3-menu-label-active, #nav .yui3-menu-label-menuvisible {
	background: none; 
	background-color: #DF7401;
	color: #FFFFFF;
}
</style>
<script type="text/javascript" src="./widgets/yahoomenu/yui-min.js"></script>
<!-- Create a new YUI instance and populate it with the required modules.-->
<script type="text/javascript">
// Call the "use" method, passing in "node-menunav".  This will load the
// script and CSS for the MenuNav Node Plugin and all of the required
// dependencies.
YUI().use('node-menunav', function (Y) {

	// MenuNav Node Plugin is available and ready for use. 
// Now use the "contentready" event to initialize the menu when the subtree of
// element representing the root menu (#menu-1) is ready to
// be scripted.
Y.on("contentready", function () {

		// The scope of the callback will be a Node instance representing
		// the root menu (#nav).  Therefore, since "this"
		// represents a Node instance, it is possible to just call "this.plug"
		// passing in a reference to the MenuNav Node Plugin.
this.plug(Y.Plugin.NodeMenuNav, { 
	autoSubmenuDisplay: true,
	mouseOutHideDelay: 0
});

	}, "#nav");
});
</script>
<!-- yui keyboard enhanced menu ends here -->