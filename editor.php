<?php

?>
<head>
	<meta charset="utf-8">
	<script src="ckeditor/ckeditor.js"></script>
</head>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="title" placeholder="Enter the title..." value="<?php echo $_SESSION['title']; ?>" maxlength="60" class="cool4">
	<!--Category selection-->
	<?php
	$get_categs = "SELECT `category_id`, `category_name` FROM `categories` WHERE `hide`=0 ORDER BY `category_name` ASC";

	$result = mysqli_query($dbc, $get_categs) or DIE("Could not get catgeories.");
	//add/show categories in drop down boxes
	if (!ISSET($_SESSION['hold_cats'])) {
		for ($y = 0 ; $y < $_SESSION['numb_categories'] ; $y++) {
			echo "<select name='category[]' class='cool2'>";
			if ($y > 0) {
				echo "<option value='blank' selected='selected'>---</option>";
			}
			while ($row = mysqli_fetch_array($result)) {
				if ($_SESSION['category_id'] == 1 and $row['category_id'] == 1) {
					echo "<option value='blank' selected='selected'>---</option>";
				}
				if ($row['category_id'] != 1) {
					if ($row['category_id'] == $_SESSION['category_id'] and $y == 0) {
						$autofocus = "selected='selected'";
					}
					else {
						$autofocus = null;
					}
					echo "<option value='" . $row['category_id'] . "'" . $autofocus . ">" . $row['category_name'] . "</option>";
				}
			}
			mysqli_data_seek($result, 0);
			echo "</select>";
		}
	}
	else {
		//get categories
		$catties = explode(",", $_SESSION['hold_cats']);
		$catties = array_unique($catties);
		//workaround a very strange bug where the last value would be blank after adding cat
		$val = end($catties);
		$key = key($catties);
		if (!is_numeric($val)) {
			unset($catties[$key]);
		}
		foreach ($catties as $key => $id) {
			echo "<select name='category[]' class='cool2'>";
			echo "<option value='blank'>---</option>";
			while ($row = mysqli_fetch_array($result)) {
				if ($row['category_id'] == $id) {
					$autofocus = "selected='selected'";
				}
				else {
					$autofocus = null;
				}
				if ($row['category_id'] != 1) {
					echo "<option value='" . $row['category_id'] . "'" . $autofocus . ">" . $row['category_name'] . "</option>";
				}
			}
			echo "</select>";
			mysqli_data_seek($result, 0);
		}
		//extra categories ready to go
		for ($z = 0 ; $z < $_SESSION['numb_categories'] ; $z++) {
			echo "<select name='category[]' class='cool2'>";
			$x = 0;
			while ($row = mysqli_fetch_array($result)) {
				if ($x == 0) {
					echo "<option value='blank' selected='selected'>---</option>";
					$x++;
				}
				if ($row['category_id'] != 1) {
					echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
				}
			}
			echo "</select>";
			mysqli_data_seek($result, 0);
		}
	}
	?>
	<input type="submit" name="add_cat" value="+ category" class='cool2'>
	<?php
	echo "<br />";
	?>
	<textarea name="editor1" id="editor1" rows="10" cols="80">
	<?php echo $_SESSION['content']; ?>
	</textarea>
	<script>
		CKEDITOR.replace( 'editor1' );
	</script><br />
	<?php
	if ($_SESSION['edit_blog'] == -1) {
		$manny = "Post blog!";
	}
	else {
		$manny = "Update!";
	}
	?>
	<input type="submit" name="post" value="<?php echo $manny; ?>" class="cool2"><br /><br />
	<?php if (!ISSET($edit) and !ISSET($_SESSION['writing'])) { ?>
	<input type="submit" name="back" value="Back" class="cool3"><br />
	<?php
	}
	if (ISSET($post_msg)) {
		echo $post_msg;
	}
	?>
</form>