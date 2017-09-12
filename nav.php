<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div>
			<font size="20%" color="5053aa" style="float:left">
				MindPost
			</font>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<?php
		if (ISSET($_SESSION['user_id'])) {
		?>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="float:left">
			<ul class="nav navbar-nav" style="float:left">
				<li>
					<a href="make_post.php"><font size="5%">Make a post!</font></a>
				</li>
			</ul>
		</div>
		<?php
		}
		?>
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
					echo "<font color='ad300d'>Welcome, " . $_SESSION['username'] . "</font>";
					include("logout.php");
				}
				?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container -->
</nav>


<br style="clear:both" />