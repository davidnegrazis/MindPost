<?php
session_start();
require_once("db_connect.php");
?>
<html>
<head>
	<?php
		
	if (ISSET($_GET['user_prof'])) {
		$_SESSION['prof_id'] = $_GET['user_prof'];
	}
	if (!ISSET($_SESSION['view'])) {
		$_SESSION['view'] = "bio";
	}
	if (ISSET($_GET['view'])) {
		$_SESSION['view'] = $_GET['view'];
	}
	$view = $_SESSION['view'];
	//get reputation
	$blog_rep = "SELECT `blog_like_id` FROM `blog_likes` INNER JOIN `blogs` ON `blog_likes`.`blog_id`=`blogs`.`blog_id` WHERE `blogs`.`user_id`=" . $_SESSION['prof_id'];
	
	$rep_blog = mysqli_query($dbc, $blog_rep) or DIE("SHSSHS");	
	
	$comment_rep = "SELECT `comment_like_id` FROM `comment_likes` INNER JOIN `comments` ON `comment_likes`.`comment_id`=`comments`.`comment_id` WHERE `comments`.`user_id`=" . $_SESSION['prof_id'];
	//echo $comment_rep;
	$rep_comment = mysqli_query($dbc, $comment_rep) or DIE("SHSSHS");
	$reputation = mysqli_num_rows($rep_blog) + mysqli_num_rows($rep_comment);
	include_once("styling.php");
	require_once("functions.php");
	//get user info
	$select = "SELECT * FROM `users` WHERE `user_id`=" . $_SESSION['prof_id'];
	$result = mysqli_query($dbc, $select) or DIE("died.");
	while ($row = mysqli_fetch_array($result)) {
		$name = $row['username'];
		$bio = $row['bio'];
		$level = $row['level'];
		$master = $row['xtreme_master'];
	}
	?>
	<title>Viewing <?php echo $name; ?>'s profile</title>
</head>
<body>
<div class='container'>
	<?php include("nav.php"); ?>
	<div class='row'>
		<?php
		if (ISSET($_SESSION['username']) and $name == $_SESSION['username']) {
			echo "<h1 class='impact'>My profile</h1>";
			?>
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<button type="submit" class="cool2" name="edit_bio" value="okay" title="Edit password">&#9998; bio</button>
				<button type="submit" class="cool2" name="edit_pwd" value="sick" title="Edit password">&#9998; password</button>
			</form>
			<?php
		}
		else {
			echo "<h1 class='impact'>" . $name . "'s profile</h1>";
		}
		?>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" name="back" value="Back" class='cool3'>
		</form>
		<?php
		echo "<h2><font class='impact'>Reputation: <font class='impact' color='green'>" . $reputation . " likes received</font></font></h2>";
		
		if ($master == 1) {
			echo "<h1 class='impact'><font color='red' class='impact'>" . $name . " is a master admin</font></h1>";
		}
		elseif ($level == 1) {
			echo "<h1 class='impact'><font color='blue class='impact'>" . $name . " is an admin</font></h1>";
		}
		?>
		<h2>
			<a class='cool' href="<?php echo $_SERVER['PHP_SELF']; ?>?view=bio">Bio</a>
			|
			<a class='cool' href="<?php echo $_SERVER['PHP_SELF']; ?>?view=<?php echo $_SESSION['prof_id']; ?>">Posts</a><br />
		</h2>
		<?php
		
		if ($view == "bio") {
			if ($bio != null) {
				echo "<div class='col-lg-8'><div class='well'>" . htmlspecialchars($bio) . "</div></div>";
			}
			else {
				echo "<h3 class='impact'>No bio yet</h3>";
			}
		}
		elseif ($view == $_SESSION['prof_id']) {
			echo "<h3 class='impact'>Posts made by $name</h3>";
			include("return_posts.php");
			include("sidebar.php");
			
		}
		?>

	</div>
</div>

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
						echo "<tr bgcolor='B3E5E2'><td class='tdsick'>" . $message . "</td></tr><br />";
					}
					elseif (ISSET($saw[$key]) and $saw[$key] != null) {
						echo "<tr><td class='tdsick'>" . $message . "</td></tr><br />";
					}
				}
				echo "</table>";
			}
			else {
				echo "<font class='impact' color='red'>No notifications.</font>";
			}
		}
		elseif (ISSET($_SESSION['passy'])) {
			?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="text" name="pwd_input" placeholder="New password"><br />
				<input type="submit" name="send_pwd" value="Do the new password">
			</form>
			<?php
			if (ISSET($pwderror)) {
				echo "<br />" . $pwderror;
			}
		}
		elseif (ISSET($_SESSION['ha'])) {
			?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<textarea name="bio"><?php echo $bio; ?></textarea><br />
				<input type="submit" name="post_bio" value="Update!">
			</form>
			<?php
		}
		?>
	</p>
</div>

</body>
</html>
	