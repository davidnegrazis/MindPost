<?php
session_start();
$_SESSION['numb_categories'] = 3;
$_SESSION['edit_blog'] = -1;
if (ISSET($_SESSION['hold_cats'])) {
	unset($_SESSION['hold_cats']);
}
?>
<html>
<head>
	<?php
	//get styles and functions necessary for site functionality
	if (ISSET($id_blog)) {
		unset($id_blog);
	}
	include_once("styling.php");
	require_once("functions.php");
	?>
	<title>MindPost Home</title>
</head>
<body bgcolor="#CCFFFF">
<?php
//nav bar
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
		if (ISSET($loop)) {
			echo "<font color='red'><strong>" . mysqli_num_rows($cats_result) . " categories found for search</strong></font><i> " . $_GET['search'] . "</i><br /><font class='impact'>Which one would you like to choose?</font>";
		?>
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<?php
			while ($row = mysqli_fetch_array($cats_result)) {
			?>
				<button type="submit" class="cool" name="def_category" value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></button>
			<?php
			}
			echo "</form>";
		}
		
		if (ISSET($cats)) {
		?>
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<?php
			while ($row = mysqli_fetch_array($cats_result)) {
			?>
				<button type="submit" class="cool" name="def_category" value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></button>
			<?php
			}
			echo "</form>";
		
		}
		
		if (ISSET($no_cat)) {
			echo "The category you were trying to view is unavailable.";
		}
		
		//show likers
		if (ISSET($bloggy)) {
			echo "<h2 class='impact'>Post liked by</h2>";
			$sg = "SELECT `username` FROM `users` INNER JOIN `blog_likes` ON `users`.`user_id`=`blog_likes`.`user_id` WHERE `blog_likes`.`blog_id`=" . $bloggy . " ORDER BY `blog_likes`.`blog_like_id` DESC";
			$b = mysqli_query($dbc, $sg) or DIE("b failed.");
			if (mysqli_num_rows($b) != 0) {
				echo "<font class='impact'>";
				while ($row = mysqli_fetch_array($b)) {
					echo $row['username'] . "<br />";
				}
				echo "</font>";
			}
			else {
				echo "<font color='red'>Nobody... :(</font>";
			}
		}
		
		if (ISSET($banned)) {
			echo $msg4;
		}
		
		if (ISSET($_GET['check_notifs'])) {
			echo "<h2 class='impact'>Notifications:</h2>";
			if (count($my_notif_messages) != 0) {
				echo "<table>";
				foreach ($my_notif_messages as $key => $message) {
					if (ISSET($saw[$key]) and $saw[$key] == 0) {
						echo "<tr bgcolor='B3E5E2'><td class='tdsick'>" . $message . "</td></tr>";
					}
					elseif (ISSET($saw[$key])) {
						echo "<tr><td class='tdsick'>" . $message . "</td></tr>";
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