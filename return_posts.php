<div class="col-lg-8">
	<?php
	if (mysqli_num_rows($search_result) != 0) {
		//gets blog title and content, username of poster, time posted, category name blog was posted in
		while ($row = mysqli_fetch_array($search_result)) {
			if ($_SESSION['category_id'] == 3) {
				$in = " in " . $row['category_name'];
			}
			else {
				$in = null;
			}
		?>
			<!-- Blog Post Content Column -->
				<!-- Blog Post -->
				
				<!-- Title -->
				<h1><?php echo $row['title']; ?></h1>
				
				 <!-- Author and Category-->
				<p class="lead">
					by <?php echo $row['username'] . $in; ?>
				</p>
				<!-- Date/Time -->
				<p>
					<span class="glyphicon glyphicon-time">
					</span>
					Posted on <?php echo substr($row['timestamp'], 0, 10) . " at " . substr($row['timestamp'], -8); ?>
				</p>
				
				<hr>
				<?php echo $row['content']; ?>
				<hr>
		<?php
		}
	}
	else {
		if (ISSET($_GET['search2']) and $_GET['search2'] != null) {
			echo "<font color='red' size='4%'>Nothing was found for search<i> " . $_GET['search2'] . "</i></font><br />:(";
		}
		else {
			echo "<font color='red' size='4%'>No posts have been made here as of yet. Be the first, maybe?</font>";
			if (ISSET($_SESSION['user_id'])) {
		?>
				<div class="well">
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<input type="submit" name="topost" value="Make a post">
					</form>
				</div>
	<?php
			}
		}
	}
	?>
</div>