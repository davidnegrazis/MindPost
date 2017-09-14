<?php
if (!ISSET($id_blog)) {
?>
	<div class="col-lg-8">
<?php
}
	if (ISSET($id_blog)) {
	?>
		<p><strong><font size='4'>Viewing single blog...</font></strong></p>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" name="back" value="Back" class="cool3">
		</form>
	<?php
	}
	if (mysqli_num_rows($search_result) != 0) {
		//gets blog title and content, username of poster, time posted, category name blog was posted in
		while ($row = mysqli_fetch_array($search_result)) {
			$row['category_id'] = explode(",", $row['category_id']);
			$row['category_id'] = array_unique($row['category_id']);
			$row['category_name'] = explode(",", $row['category_name']);
			$row['category_name'] = array_unique($row['category_name']);
			$row['categories_hide'] = explode(",", $row['categories_hide']);
			$row['categories_hide'] = array_unique($row['categories_hide']);
			$row['blog_cats'] = explode(",", $row['blog_cats']);
			foreach ($row['category_id'] as $key => $val) {
				//echo $val;
				if (!in_array($row['category_id'][$key], $row['blog_cats'])) {
					unset($row['category_id'][$key]);
					unset($row['category_name'][$key]);
					unset($row['category_hide'][$key]);
				}
			}
			if (!in_array($_SESSION['category_id'], $row['blog_cats']) and $_SESSION['category_id'] != 1) {
				continue;
			}
			//echo $row['blog_cats'];
			/*
			$_SESSION['id_category'] = $row['category_id'];
			if (strpos($row['blog_cats'], ",") !== false) {
				$others = " <form method='GET' action='" . $_SERVER['PHP_SELF'] . "' style='float:left'><button style='float:left' type='submit' class='cool6' name='expand_cats' value='" . $row['blog_cats'] . "'>+more</button></form>";
			}
			else {
				$others = null;
			}
			$in = " in <font class='impact'>" . $row['category_name'] . $others . "</font>";
			if ($_SESSION['category_id'] != 1 and !ISSET($id_blog)) {
				$in = null;
			}
			*/
		?>
			<!-- Blog Post Content Column -->
			<!-- Blog Post -->
			
			<!-- Title -->
			<h1><strong><?php echo htmlspecialchars($row['title']); ?></strong></h1>
			<?php 
			echo "<form method='GET' action='" . $_SERVER['PHP_SELF'] . "'>";
			//show categories posted in
			foreach ($row['category_name'] as $key => $name) {
				?>
				<a class ='coolm' href="<?php echo $_SERVER['PHP_SELF']; ?>?def_category=<?php echo $row['category_id'][$key]; ?>">&#8728;<?php echo $name; ?></a>
				<?php
			}
			echo "</form>";
			//edit blog if admin or poster
			if (ISSET($_SESSION['user_id']) and $row['user_id'] == $_SESSION['user_id'] or $_SESSION['level'] == 1) {
			?>
				<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" style='float:right'>
					<button type="submit" class="cool2" name="edit_blog" value="<?php echo $row['blog_id']; ?>">Edit &#9998;  </button>
				</form>
			<?php
			}
			
			if ($row['banned'] == 0) {
			?>
			
			 <!-- Author and Category-->
			<p class="lead" style='float:left'>
				by <a class='cool4' href="profile.php?user_prof=<?php echo $row['user_id']; ?>"><?php echo htmlspecialchars($row['username']); ?></a>
			</p>
			<?php
			}
			else {
			?>
			<p class="lead" style='float:left'>
				by <?php echo htmlspecialchars($row['username']); ?>
			</p>
			<?php
			}
			?>
			<br /><br /><br />
			<!-- Date/Time -->
			<p>
				<span class="glyphicon glyphicon-time">
				</span>
				Posted on <?php echo substr($row['timestamp'], 0, 10) . " at " . substr($row['timestamp'], -8); ?>
			</p>
			<?php
			if (substr($row['edited'], 0, 4) != "0000") {
			?>
				<font color='red'>Edited on <?php echo substr($row['edited'], 0, 10) . " at " . substr($row['edited'], -8); ?></font>
			<?php
			}
			?>
			
			<hr>
			<?php
			//limit content size, show content if not in show full blog page
			$content = $row['content'];
			/*
			if (!ISSET($id_blog)) {
				$closed = false;
				if (strlen($content) > 599) {
					$closed = true;
					$content = substr($content, 0, 599);
				}
			}
			else {
				$closed = false;
			}
			//close up loose html tags
			$doc = new DOMDocument();
			$doc->loadHTML($content);
			$content = $doc->saveHTML();
			
			if ($closed == true) {
				$content = $content . " <strong><font color='FF0000'>[...]</font></strong>";
			}
			*/
			
			
			echo '<div class="expandDiv"><p>' . $content . '</div></p>';
			?>
			<hr>
		<?php
			//only show expand option if not expanded
			if (!ISSET($id_blog)) {
			?>
			<a class ='cool2' href="<?php echo $_SERVER['PHP_SELF']; ?>?open_blog=<?php echo $row['blog_id']; ?>">Expand&#8594;</a>

			
			<?php
			//null is considered with count
			$row['comment_id'] = explode(",", $row['comment_id']);
			foreach ($row['comment_id'] as $key => $comment) {
				if ($comment == null) {
					unset($row['comment_id'][$key]);
				}
			}
			
			echo "<p style='float:right' class='impact'>" . count($row['comment_id']) . " comments</p><br /><br />";
			}
			
			//null is considered with count
			$row['likes'] = explode(",", $row['likes']);
			foreach ($row['likes'] as $key => $like) {
				if (!is_numeric($like)) {
					unset($row['likes'][$key]);
				}
			}
			$row['likes'] = array_unique($row['likes']);
			//get likes
			if (ISSET($_SESSION['user_id']) and $row['user_id'] != $_SESSION['user_id']) {
				if (!is_numeric(array_search($_SESSION['user_id'], $row['likes']))) {
			?>
					<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" style='float:left'>
						<button type="submit" class="cool2" name="like_blog" value="<?php echo $row['blog_id']; ?>" title="Like post">&#10084;</button>
					</form>
			<?php
				}
				else {
				?>
					<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" style='float:left'>
						<button type="submit" class="cool2" name="unlike_blog" value="<?php echo $row['blog_id']; ?>" title="Unlike post">&#128148;</button>
					</form>
				<?php
				}
			}
			echo "<p style='float:right' class='impact'><a href=" . $_SERVER['PHP_SELF'] . "?check_blog_likes=" . $row['blog_id'] . " class='impact'>" . count($row['likes']) . " &#10084;</a></p><br /><br />";
			echo "<br />";
		}
	}
	else {
		if (ISSET($_GET['search2']) and $_GET['search2'] != null or ($_SESSION['find_content'] != null)) {
			echo "<font color='red' size='4%'>Nothing was found for search<i> " . $_SESSION['search'] . "</i> in this category.</font><br />:(";
		}
		elseif (!ISSET($id_blog) and !ISSET($view)) {
			echo "<font color='7F0B1C' size='4%'><strong>No posts have been made here as of yet. Be the first, maybe?</strong></font><br /><br />";
			if (ISSET($_SESSION['user_id'])) {
		?>
				<font size="7">
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<input type="submit" name="topost" value="Make the first post!" class="cool2">
					</form>
				</font>
	<?php
			}
			
		}
		elseif (ISSET($view)) {
			echo "<font color='red'>We got nothin', yo. We got nuttin'.</font>";
		}
		else {
			echo "This post is hidden due to all of its categories being hidden.";
		}
	}
if (!ISSET($id_blog)) {
?>
</div>
<?php
}