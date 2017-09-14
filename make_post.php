<?php
session_start();
if (!ISSET($_SESSION['user_id'])) {
	header("Location:index.php");
}
require_once("functions.php");
unset($_SESSION['writing']);
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
	<?php
	if ($_SESSION['edit_blog'] == -1) {
		$doing = "Whatcha want to post?";
	}
	else {
		$doing = "Editing post...";
	}
	echo '<h2><strong><font color="2B1440">' . $doing . '</font></strong></h2>';

	include("editor.php");
	?>
</div>

<!--Error message fancy popup-->
<div id="message" style="width:400px;display: none;">
	<p>
		<?php
		if (ISSET($_GET['check_notifs'])) {
			echo "<h2 class='impact'>Notifications:</h2>";
			if (count($my_notif_messages) != 0) {
				echo "<table>";
				foreach ($my_notif_messages as $key => $message) {
					if ($saw[$key] == 0) {
						echo "<tr bgcolor='B3E5E2'><td class='tdsick'>" . $message . "</td></tr><br />";
					}
					else {
						echo "<tr><td class='tdsick'>" . $message . "</td></tr><br />";
					}
				}
				echo "</table>";
			}
			else {
				echo "<font class='impact' color='red'>No notifications.</font>";
			}
		}
		?>
	</p>
</div>



</body>
</html>