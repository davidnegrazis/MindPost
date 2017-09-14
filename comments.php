<script src="ckeditorlite/ckeditor.js"></script>
<!--Post comment-->
<div class="container">
	<div class="col-lg-8">
		<div class="well">
		<?php
		if (ISSET($_SESSION['admin_category']) and $_SESSION['admin_category'] == "categories") {
			$til = "Category title";
		}
		else {
			$til = "Comment title";
		}
		if (ISSET($_SESSION['user_id'])) {
		?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" value="<?php echo $_SESSION['comment_title']; ?>">
				<input type="text" name="comment_title" placeholder="<?php echo $til; ?>" class="form-control" maxlength="33" value="<?php echo $_SESSION['comment_title']; ?>"><br />
				<textarea name="my_comment" id="editor1" class="form-control cool5"><?php echo $_SESSION['comment_content']; ?></textarea><br />
				<input type="submit" name="post_comment" value="Post" class='cool'>
			</form>
			<script>
				CKEDITOR.replace( 'editor1' );
			</script>
			<?php echo $comment_msg; ?>
		<?php
		}
		else {
			echo '<p class="impact"><a class="fancybox impact" href="#login" title="Login to make blogs and gain access to more features">Login</a> to post a comment</p>';
		}
		?>
		</div>
	</div>
</div>