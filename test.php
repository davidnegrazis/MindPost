<html>
	<head>
        <meta charset="utf-8">
        <title>A Simple Page with CKEditor</title>
        <script src="ckeditor/ckeditor.js"></script>
    </head>
    <body>
		<?php
		$msg = null;
		$blog_id = 6;
		if (ISSET($_POST['submit'])) {
			if ($_POST['editor1'] != null) {
				require("db_connect.php");
				$insert = "UPDATE `blog`.`blogs` SET `content` = '" . $_POST['editor1'] . "' WHERE `blogs`.`blog_id` =" . $blog_id;
				mysqli_query($dbc, $insert) or DIE("Query error.");
				$msg = "Blog updated.";
			}
			else {
				$msg = "You didn't enter anything in the box!";
			}
		}
		$select = "SELECT `content` FROM `blogs` WHERE `blog_id`=" . $blog_id;
		$content = mysqli_query($dbc, $select) or die("dang");
		echo "Here's the blog post to update:<br />";
		while ($row = mysqli_fetch_array($content)) {
			echo $row['content'];
		}
		?>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <textarea name="editor1" id="editor1" rows="10" cols="80">
                This is my textarea to be replaced with CKEditor.
            </textarea>
			<script>
				CKEDITOR.replace( 'editor1' );
			</script>
			<input type="submit" name="submit" value="Send">
		</form>
		<?php
		echo $msg;
		?>
	</body>
<html>