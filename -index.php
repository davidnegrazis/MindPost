<?php
session_start();
?>
<html>
<head>
	<?php
	include_once("styling.php");
	require_once("functions.php");
	?>
	<title>MindPost Home</title>
</head>
<body bgcolor="#CCFFFF">
<?php
include_once("nav.php");

echo "<div class='container'><div class='row'>";
include("return_posts.php");
include("sidebar.php");
echo "</div></div>";
?>

<!--Error message fancy popup-->
<div id="message" style="width:400px;display: none;">
	<p>
		<?php
		if (ISSET($blog_msg)) {
			echo $blog_msg;
		}
		?>
	</p>
</div>

</body>
</html>