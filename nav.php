<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div>
			<font size="22%" color="5053aa" style="float:left">
				<strong>
					MindPost
				</strong>
			</font>
			<?php
			if (ISSET($_SESSION['user_id'])) {
			?>
			<ul class="nav navbar-nav">
				<li style="float:left">
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?check_notifs=yes"><?php echo $n_msg; ?></a>
				</li>
			</ul>
			<?php
			}
			?>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" style="float:right">
			<ul class="nav navbar-nav">
				<?php
				if (!ISSET($_SESSION['user_id'])) {
				?>
					<li>
						<a class="fancybox" href="#signup" title="Create an account with MindPost">
							<strong>
								Create account
							</strong>
						</a>
						<div id="signup" style="width:400px;display: none;">
							<p>
								<?php
								include("signup.php");
								?>
							</p>
						</div>
					</li>
					<li>
						<a class="fancybox" href="#login" title="Login to make blogs and gain access to more features">
							<strong>
								Login
							</strong>
						</a>
						<div id="login" style="width:400px;display: none;">
							<p>
								<?php
								include("login.php");
								?>
							</p>
						</div>
					</li>
				<?php
				}
				else {
					include("logout.php");
				}
				?>
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
				<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<?php
				while ($row = mysqli_fetch_array($eight_categories)) {
					if ($_SESSION['category_id'] == $row['category_id']) {
						$bg = "background: #FFC60D";
					}
					else $bg = null;
				?>
					<button type="submit" class="cool" name="def_category" style="<?php echo $bg; ?>" value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></button>
				<?php
				}
				?>
				</form>
			</li>
		</ul>
		<ul style="float:right" class="nav navbar-nav">
			<li align="right">
				<?php
				if (ISSET($_SESSION['user_id'])) {
				?>
					<a href="make_post.php" class="link"><font size="5%"><strong>&#8594;Post something&#8592;</strong></font></a>
				<?php
				}
				else {
				?>
					<!--<p>Login to post</p>-->
				<?php
				}
				?>
			</li>
			<li align="right">
				<?php
				if (ISSET($_SESSION['level']) and $_SESSION['level'] == 1) {
				?>
					<a href="admin.php" class="link"><font size="5%"><strong>&#8594;Admin tools&#8592;</strong></font></a>
				<?php
				}
				?>
			</li>
		</ul>
</div>


<br style="clear:both" />