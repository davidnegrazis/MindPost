<?php
session_start();
if (!ISSET($_SESSION['blog_id'])) {
	header("Location:index.php");
}
?>
<html>
<head>
	<?php
	$id_blog = $_SESSION['blog_id'];
	include_once("styling.php");
	require_once("functions.php");
	?>
	<title>Viewing post</title>
	<meta charset="utf-8">
</head>
<body>
	<div class="container">
		<?php
		include_once("nav.php");
		include_once("return_posts.php");
		echo '<br /><h4 class="impact">Comments...</h4>';
		include("comments.php");
		//show comments
		$get_comments = "SELECT `comments`.`comment_id`,`comments`.`user_id`, `comments`.`title`, `comments`.`comment`, `comments`.`timestamp`, `comments`.`hide`, `comments`.`edited`, `users`.`username`, `users`.`banned`,GROUP_CONCAT(`comment_likes`.`user_id` ORDER BY `comment_like_id` DESC) AS `likes` FROM `comments` INNER JOIN `users` ON `comments`.`user_id`=`users`.`user_id` LEFT JOIN `comment_likes` ON `comments`.`comment_id`=`comment_likes`.`comment_id` WHERE `comments`.`blog_id`=" . $id_blog . " GROUP BY `comments`.`comment_id` ORDER BY `comments`.`comment_id` DESC";
		//echo $get_comments;
		$comments_result = mysqli_query($dbc, $get_comments) or DIE("Could not get blogs.");
		if (mysqli_num_rows($comments_result) != 0) {
			while ($do = mysqli_fetch_array($comments_result)) {
			?>
			   <!-- Comment -->
				<div class="container">
					<div class="col-lg-8">
						<div class="well">
							<div class="media">
								<div class="media-body">
									<h4 class="media-heading">
										<?php
										if ($row['banned'] == 0) {
										?>
											<small>by <font class='impact'><a class='cool4' href="profile.php?user_prof=<?php echo $do['user_id']; ?>"><?php echo htmlspecialchars($do['username']); ?></a><?php echo "</font> on " . substr($do['timestamp'], 0, 10) . " at " . substr($do['timestamp'], -8); ?></small>
										<?php
										}
										else {
										?>
											<small>by <font class='impact'><?php echo htmlspecialchars($do['username']) . "</font> on " . substr($do['timestamp'], 0, 10) . " at " . substr($do['timestamp'], -8); ?></small>
										<?php
										}
										?>

									</h4>
									<?php
									if (substr($do['edited'], 0, 4) != "0000") {
									?>
										<br /><small><font color='red'>Edited on <?php echo substr($do['edited'], 0, 10) . " at " . substr($do['edited'], -8); ?></font></small><br />
									<?php
									}
									?>
									<hr class="lelz">
									<h4 class="media-heading"><strong><?php if ($do['title'] != null) echo htmlspecialchars($do['title']); ?></strong></h4>
									<?php
									if ($do['hide'] == 0) {
										echo $do['comment'];
									}
									else {
										echo "<p class='impact'><font color='red'>This comment was removed.</font></p>";
									}
									?>
								</div>
							</div>
							<?php
							//null is considered with count
							$do['likes'] = explode(",", $do['likes']);
							foreach ($do['likes'] as $key => $like) {
								if (!is_numeric($like)) {
									unset($do['likes'][$key]);
								}
							}
							$do['likes'] = array_unique($do['likes']);
							//get likes
							if (ISSET($_SESSION['user_id']) and $do['user_id'] != $_SESSION['user_id']) {
								if (!is_numeric(array_search($_SESSION['user_id'], $do['likes']))) {
							?>
									<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" style='float:left'>
										<button type="submit" class="cool2" name="like_comment" value="<?php echo $do['comment_id']; ?>" title="Like comment">&#10084;</button>
									</form>
							<?php
								}
								else {
								?>
									<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
										<button type="submit" class="cool2" name="unlike_comment" value="<?php echo $do['comment_id']; ?>" title="Unlike comment">&#128148;</button>
									</form>
								<?php
								}
							}
							echo "<p style='float:right' class='impact'><a href=" . $_SERVER['PHP_SELF'] . "?check_comment_likes=" . $do['comment_id'] . " class='impact'>" . count($do['likes']) . " &#10084;</a></p>";
							?>
						</div>
					</div>
				</div>
			<?php
			}
		}
		else {
			echo "<p class='impact'>No comments made on this blog post yet.<p>";
		}
		?>
	</div>
	<!--Error message fancy popup-->
	<div id="message" style="width:400px;display: none;">
		<p>
			<?php
			
			if (ISSET($no_cat)) {
				echo "The category you were trying to view is unavailable.";
			}
			
			//show likers
			if (ISSET($commy)) {
				echo "<h2 class='impact'>Comment liked by</h2>";
				$sg = "SELECT `username` FROM `users` INNER JOIN `comment_likes` ON `users`.`user_id`=`comment_likes`.`user_id` WHERE `comment_likes`.`comment_id`=" . $commy . " ORDER BY `comment_likes`.`comment_like_id` DESC";
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