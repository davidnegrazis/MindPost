<!-- Blog Sidebar Widgets Column -->
<div class="col-md-4">
	
	<!-- Side Widget Well -->
	<div class="well">
		<h4 class='impact'>Currently viewing <strong><font color='red'><?php echo htmlspecialchars($category_name); ?></font></strong></h4>
		<p><?php echo $category_desc; ?></p>
	</div>

	<!-- Blog Search Well -->
	<div class="well">
		<?php if (!ISSET($view)) { ?>
			<h4 class='impact'>Post/user search</h4>
		<?php } else { ?>
			<h4 class='impact'>Post search</h4>
		<?php } ?>
		<div class="input-group">
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<div class="input-group">
					<input type="text" class="form-control" name="search2">
					<span class="input-group-btn">
						<button class="btn btn-default" type="submit" name="content_search">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</span>
				</div>
				<br />
				<font class='impact'>
					<!--Search parameters-->
					<input type="radio" name="age" value="desc" checked> Newest to oldest
					<input type="radio" name="age" value="asc"> Oldest to newest<br />
					<button type="submit" class="cool2" name="refresh">&#x021BA;</button>
					<?php if (ISSET($_SESSION['search'])) {
						echo "<br />Searching for: <font color='green' class='impact'>" . $_SESSION['search'] . "</font>";
					}
					?>
				</font>
			</form>
		</div>
	</div>
	
	<!--Actions-->
	<?php
	/*
	if (ISSET($_SESSION['user_id'])) {
	?>
	<div class="well">
		<h4>To do...</h4>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" name="topost" value="Make a post">
		</form>
		
	</div>
	<?php
	}
	*/
	?>

	<!-- Blog Categories Well -->
	<div class="well">
	<h4 class='impact'>Blog categories</h4>
		<div class="input-group">
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<div class="input-group">
				<input type="text" class="form-control" name="search">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit" name="category_search">
						<span class="glyphicon glyphicon-search"></span>
					</button>
				</span>
				</div>
			</form>
		</div>
		
		<p><font color="1b6b1d"><strong>Check out some random categories</strong></font><p>
		<div class="row">
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<?php
			$c = 0;
			echo '<div class="col-lg-6"><ul class="list-unstyled">';
			while ($row = mysqli_fetch_array($rand_result)) {
				$c++;
				?>
				
				<li><a class='cool' href="<?php echo $_SERVER['PHP_SELF']; ?>?def_category=<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></a></li>
				<?php
				if ($c == 4) {
					echo '</ul></div><div class="col-lg-6"><ul class="list-unstyled">';
				}
			}
			?>
			</form></ul></div>
		</div>
	</div>

</div>