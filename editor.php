<head>
	<meta charset="utf-8">
	<script src="ckeditor/ckeditor.js"></script>
</head>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="title" placeholder="Enter the title..." value="<?php echo $_SESSION['title']; ?>">
	<!--Category selection-->
	<?php
	$get_categs = "SELECT `category_id`, `category_name` FROM `categories`";
	$result = mysqli_query($dbc, $get_categs) or DIE("Could not get catgeories.");
	echo "<select name='category'>";
	while ($row = mysqli_fetch_array($result)) {
		echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
	}
	echo "</select><br />";
	?>
	<textarea name="editor1" id="editor1" rows="10" cols="80">
	<?php echo $_SESSION['content']; ?>
	</textarea>
	<script>
		CKEDITOR.replace( 'editor1' );
	</script><br />
	<input type="submit" name="post" value="Post blog!"><br /><br />
	<input type="submit" name="back" value="Back">
</form>