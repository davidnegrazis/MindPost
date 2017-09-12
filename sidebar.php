<!-- Blog Sidebar Widgets Column -->
<div class="col-md-4">
	
	<!-- Side Widget Well -->
	<div class="well">
		<h4>Currently viewing <?php echo $category_name; ?></h4>
		<p><?php echo $category_desc; ?></p>
	</div>

	<!-- Blog Search Well -->
	<div class="well">
		<h4>Blog content search</h4>
		<div class="input-group">
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="text" class="form-control" name="search2">
				<span class="input-group-btn">
					<input type="submit" class="btn btn-default" name="content_search" value="Search">
						<span class="glyphicon glyphicon-search"></span>
					</input>
				</span>
				<p><font color="1b6b1d"><strong>Sort by...</strong></font><p>
				<!--Search parameters-->
				<input type="radio" name="age" value="asc"> Oldest to newest<br />
				<input type="radio" name="age" value="desc" checked> Newest to oldest
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
		<h4>Blog Categories</h4>
		<div class="row">
			<div class="input-group">
				<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="text" class="form-control" name="search">
					<span class="input-group-btn">
						<input type="submit" class="btn btn-default" name="category_search" value="Search">
							<span class="glyphicon glyphicon-search"></span>
						</input>
					</span>
				</form>
			</div>
			<p><font color="1b6b1d"><strong>Check out some random categories</strong></font><p>
			<?php
			$c = 0;
			echo '<div class="col-lg-6"><ul class="list-unstyled">';
			while ($row = mysqli_fetch_array($rand_result)) {
				$c++;
				echo '<li><a href="#">' . $row['category_name'] . '</a></li>';
				if ($c == 4) {
					echo '</ul></div><div class="col-lg-6"><ul class="list-unstyled">';
				}
			}
			?>
			</ul></div>
		</div>
		<!-- /.row -->
	</div>

</div>