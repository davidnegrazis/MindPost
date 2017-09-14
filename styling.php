<!-- Bootstrap Core CSS -->
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="bootstrap/css/blog-post.css" rel="stylesheet">
<!--
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
-->

<!--FINALLY FIXED THE COLLISIONS WITH MULTIPLE JQUERY LIBRARIES!
The expander can use the same library as fancybox!
-->
<?php
include_once("font.html");
include_once("fancyboxhead.html");
?>
<!--
<script type="text/javascript">
	jQuery(function($) {
		.noConflict()
	})
</script>
-->

<!--<script src="more-less/lib.js"></script>-->
<script src="more-less/jquery.expander.js"></script>
<script>
$(document).ready(function() {

  $('div.expandDiv').expander({
    slicePoint: 280, //It is the number of characters at which the contents will be sliced into two parts.
    widow: 2,
    expandSpeed: 0, // It is the time in second to show and hide the content.
    userCollapseText: 'Read Less (-)' // Specify your desired word default is Less.
  });

  $('div.expandDiv').expander();
});

$(document).ready(function() {

  $('div.expandComment').expander({
    slicePoint: 0, //It is the number of characters at which the contents will be sliced into two parts.
    widow: 2,
    expandSpeed: 0, // It is the time in second to show and hide the content.
    userCollapseText: 'Close (-)' // Specify your desired word default is Less.
  });

  $('div.expandComment').expander();
});


</script>