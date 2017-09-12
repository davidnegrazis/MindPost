<?php
session_start();
if (!ISSET($_SESSION['user_id'])) {
	header("Location:-index.php");
}
require_once("functions.php");
?>
<html>
<head>
	<title>Make a post</title>
	<?php
	include_once("styling.php");
	?>
</head>
<body>
<?php
include_once("nav.php");
?>
<div class="container">
	<h2>Whatcha want to post?</h2>
	<?php

	include("editor.php");

	if (ISSET($post_msg)) {
		echo $post_msg;
	}
	?>
</div>



</body>
</html>