<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top navbar-collapse" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div>
			<font size="22%" color="5053aa" style="float:left">
				<strong>
					MindPost
				</strong>
			</font>
			<ul class="nav navbar-nav">
				<li style="float:left">
					<p style="float:left" class="cool10 impact">&#9830; Admin's Lounge</p>
				</li>
			</ul>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" style="float:right">
			<ul class="nav navbar-nav">
				<li>
				<?php
				include("logout.php");
				?>
				</li>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container -->
</nav>
<!--secondary static nav bar-->
<div class="container bg">
		<ul class="nav navbar-nav">
			<li>
				<p class='cool11'><font size='4' class='impact'>Manage &#8658;</font></p>
			<li>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?admin_category=users" class="link impact">Users</a>
			</li>
			<li>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?admin_category=categories" class="link impact">Categories</a>
			</li>
			<li>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?admin_category=posts" class="link impact">Posts</a>
			</li>
			<li>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?admin_category=comments" class="link impact">Comments</a>
			</li>
		</ul>
		<ul style="float:right" class="nav navbar-nav">
			<li align="right">
				<?php
				if (ISSET($_SESSION['user_id'])) {
				?>
					<a href="index.php?def_category=1" class="link"><font size='4'><strong>&#8594;Home&#8592;</strong></font></a>
				<?php
				}
				else {
				?>
					<!--<p>Login to post</p>-->
				<?php
				}
				?>
			</li>
		</ul>
</div>


<br style="clear:both" />