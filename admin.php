<?php
session_start();
if ($_SESSION['level'] != 1) {
	header("Location:index.php");
}
if (!ISSET($_SESSION['admin_category'])) {
	$_SESSION['admin_category'] = "posts";
}
?>
<html>
<head>
	<?php
	include("styling.php");
	require_once("admin_functions.php");
	?>
	<title>Admin's hub</title>
	<script type="text/css">
		img{
			max-width: 100px;
			max-height: 100px;
		}
	</script>
</head>
<body>
<?php
include("admin_nav.php");

echo "<div class='container'><div class='well'>";

echo "<h2 class='impact'>Managing " . $_SESSION['admin_category'] . "</h2>";

echo "<p class='impact'>" . $admin_msg . "</p>";

//return post results
if ($_SESSION['admin_category'] == "posts") {
?>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="text" name="search2" placeholder="ID/title/content/user">
		<input type="text" name="category_search" placeholder="Search categories">
		<button type="submit" name="content_search" class='cool2'>Search</button>
		<button type="submit" class="cool2" name="refresh">&#x021BA;</button>
	</form>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type='submit' name='topost' value='+New post' class='cool' title="Make post">
	<input type="submit" name="toggle" value="Toggle publish status of checked posts" class='cool2'>
<?php
	echo "<br /><br /><p>Category: <font class='impact' size='4'>" . $category_name . "</font></p>";
	if (ISSET($_SESSION['searchfor']) and $_SESSION['searchfor'] != null) {
		echo "<p>Searching for <font class='impact' color='green'>" . $_SESSION['searchfor'] . "</font></p>";
	}
	if (mysqli_num_rows($search_result) != 0) {
		echo "<table>";
		echo "<tr><td></td><td class='impact' style='text-align: right'>Blog ID</td><td class='impact' style='text-align: center'>Posted by</td><td class='impact' style='text-align: center'>Blog title</td><td class='impact'>Post content</td><td class='impact'>Category</td><td class='impact'>Published</td></tr>";
		while ($row = mysqli_fetch_array($search_result)) {
			$row['category_id'] = explode(",", $row['category_id']);
			$row['category_id'] = array_unique($row['category_id']);
			$row['category_name'] = explode(",", $row['category_name']);
			$row['category_name'] = array_unique($row['category_name']);
			$row['blog_cats'] = explode(",", $row['blog_cats']);
			foreach ($row['category_id'] as $key => $val) {
				//echo $val;
				if (!in_array($row['category_id'][$key], $row['blog_cats'])) {
					unset($row['category_id'][$key]);
					unset($row['category_name'][$key]);
					unset($row['category_hide'][$key]);
				}
			}
			if (!in_array($_SESSION['category_id'], $row['blog_cats']) and $_SESSION['category_id'] != 1) {
				continue;
			}

			$content = $row['content'];
			$closed = false;
			if (strlen($content) > 200) {
				$closed = true;
				$content = substr($content, 0, 200);
			}
			/*
			//close up loose html tags
			$doc = new DOMDocument();
			$doc->loadHTML($content);
			$content = $doc->saveHTML();
			*/
			
			if ($closed == true) {
				$content = $content . " <strong><font color='FF0000'>[...]</font></strong>";
			}
			echo "<tr>";
			?>
			<td class='tdsick'>
				<input type="checkbox" name="select[]" value="<?php echo $row['blog_id']; ?>">
				<button type="submit" class="cool2" name="edit_blog" value="<?php echo $row['blog_id']; ?>" title="Edit">&#9998;</button>
				<button type="submit" class="cool2" name="open_blog" value="<?php echo $row['blog_id']; ?>" title="View">&#x021D2;</button>
				<button type="submit" class="cool2" name="add_comment" value="<?php echo $row['blog_id']; ?>" title="Add comment">📜</button>
			</td>
			<?php
			echo "<td class='tdsick'>" . $row['blog_id'] . "</td>";
			if ($row['banned'] == 1) {
				echo "<td class='tdsick' style='text-align: center'>" . htmlspecialchars($row['username']) . "</td>";
			}
			else {
				echo "<td class='tdsick' style='text-align: center'><a href='profile.php?user_prof=" . $row['user_id'] . "'>" . htmlspecialchars($row['username']) . "</a></td>";
			}
			echo "<td class='tdsick'>" . htmlspecialchars($row['title']) . "</td>";
			echo "<td class='tdsick'><div class='expandDiv'><font size='2'>" . closeDanglingTags($content) . "</font></div></td>";
			echo "<td class='tdsick'>";
			//$row['category_name'] = explode(",", $row['category_name']);
			//$row['category_id'] = explode(",", $row['category_id']);
			foreach ($row['category_name'] as $key => $name) {
				echo "<a href='" . $_SERVER['PHP_SELF'] . "?def_category=" . $row['category_id'][$key] . "'>" . $name . "</a> ";
			}
			echo "</td>";
			echo "<td class='tdsick'>";
			if ($row['hide'] == 0) {
				echo "<font class='impact' color='green'><strong>&check;</strong></font>";
			}
			else {
				echo "<font class='impact' color='red'><strong>&#10005;</strong></font>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";
	}
	else {
		echo "<font color='red'><strong>No search results found.</strong></font>";
		echo ' <form method="GET" action="' . $_SERVER['PHP_SELF'] . '"><button type="submit" class="cool2" name="refresh">&#x021BA;</button></form>';
	}
}

//return category results
if ($_SESSION['admin_category'] == "categories") {
?>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="text" name="search2" placeholder="Search ID/title/description">
		<button type="submit" name="content_search" class='cool2'>Search</button>
		<button type="submit" class="cool2" name="refresh">&#x021BA;</button>
	</form>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type='submit' name='new_category' value='+New category' class='cool' title="Make post">
	<input type="submit" name="toggle" value="Toggle publish status of checked posts" class='cool2'>
	<br /><br />
	<?php
	if (ISSET($_SESSION['searchfor']) and $_SESSION['searchfor'] != null) {
		echo "<p>Searching for <font class='impact' color='green'>" . $_SESSION['searchfor'] . "</font></p>";
	}
	if (mysqli_num_rows($search_result) != 0) {
		echo "<table>";
		echo "<tr><td></td><td class='impact' style='text-align: right'>Category ID</td><td class='impact' style='text-align: center'>Name</td><td class='impact'>Description</td><td class='impact'>Published</td></tr>";
		while ($row = mysqli_fetch_array($search_result)) {
			echo "<tr>";
			?>
			<td class='tdsick'>
				<?php if ($row['category_id'] != 1) { ?>
				<input type="checkbox" name="select[]" value="<?php echo $row['category_id']; ?>">
				<?php } ?>
				<button type="submit" class="cool2" name="edit_category" value="<?php echo $row['category_id']; ?>" title="Edit">&#9998;</button>
			</td>
			<?php
			echo "<td class='tdsick'>" . $row['category_id'] . "</td>";
			if ($row['hide'] == 0) {
				echo "<td class='tdsick' style='text-align: center'><a href='index.php?def_category=" . $row['category_id'] . "'>" . htmlspecialchars($row['category_name']) . "</a></td>";
			}
			else {
				echo "<td class='tdsick' style='text-align: center'>" . htmlspecialchars($row['category_name']) . "</td>";
			}
			echo "<td class='tdsick'><font size='2'>" . $row['category_desc'] . "</font></td>";
			echo "<td class='tdsick'>";
			if ($row['hide'] == 0) {
				echo "<font class='impact' color='green'><strong>&check;</strong></font>";
			}
			else {
				echo "<font class='impact' color='red'><strong>&#10005;</strong></font>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";
	}
	else {
		echo "<font color='red'><strong>No search results found.</strong></font>";
		echo ' <form method="GET" action="' . $_SERVER['PHP_SELF'] . '"><button type="submit" class="cool2" name="refresh">&#x021BA;</button></form>';
	}
}

//manage comments
if ($_SESSION['admin_category'] == "comments") {
?>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="text" name="search2" placeholder="Search ID/title/comment">
		<button type="submit" name="content_search" class='cool2'>Search</button>
		<button type="submit" class="cool2" name="refresh">&#x021BA;</button>
	</form>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="submit" name="toggle" value="Toggle publish status of checked posts" class='cool2'>
	<br /><br />
	<?php
	if (ISSET($_SESSION['searchfor']) and $_SESSION['searchfor'] != null) {
		echo "<p>Searching for <font class='impact' color='green'>" . $_SESSION['searchfor'] . "</font></p>";
	}
	if (mysqli_num_rows($search_result) != 0) {
		echo "<table>";
		echo "<tr><td></td><td class='impact' style='text-align: left'>Comment ID</td><td class='impact' style='text-align: right'>Blog ID</td><td class='impact' style='text-align: center'>Posted by</td><td class='impact' style='text-align: center'>Title</td><td class='impact'>Comment</td><td class='impact' style='text-align: left'>Published</td></tr>";
		while ($row = mysqli_fetch_array($search_result)) {
			$content = $row['comment'];
			$closed = false;
			if (strlen($content) > 200) {
				$closed = true;
				$content = substr($content, 0, 200);
			}
			/*
			//close up loose html tags
			$doc = new DOMDocument();
			$doc->loadHTML($content);
			$content = $doc->saveHTML();
			*/
			
			if ($closed == true) {
				$content = $content . " <strong><font color='FF0000'>[...]</font></strong>";
			}
			echo "<tr>";
			?>
			<td class='tdsick'>
				<input type="checkbox" name="select[]" value="<?php echo $row['comment_id']; ?>">
				<button type="submit" class="cool2" name="edit_comment" value="<?php echo $row['comment_id']; ?>" title="Edit">&#9998;</button>
				<button type="submit" class="cool2" name="open_blog" value="<?php echo $row['blog_id']; ?>" title="View blog containing this comment">&#x021D2;</button>
			</td>
			<?php
			echo "<td class='tdsick'>" . $row['comment_id'] . "</td>";
			echo "<td class='tdsick'>" . $row['blog_id'] . "</td>";
			if ($row['banned'] == 1) {
				echo "<td class='tdsick' style='text-align: center'>" . htmlspecialchars($row['username']) . "</td>";
			}
			else {
				echo "<td class='tdsick' style='text-align: center'><a href='profile.php?user_prof=" . $row['user_id'] . "'>" . htmlspecialchars($row['username']) . "</a></td>";
			}
			if ($row['title'] != null) {
				echo "<td class='tdsick' style='text-align: center'>" . htmlspecialchars($row['title']) . "</td>";
			}
			else {
				echo "<td class='tdsick' style='text-align: center'><i><font size='1'>No title<font></i></td>";
			}
			echo "<td class='tdsick'><font size='2'>" . closeDanglingTags($content) . "</font></td>";
			echo "<td class='tdsick' style='text-align: center'>";
			if ($row['hide'] == 0) {
				echo "<font class='impact' color='green'><strong>&check;</strong></font>";
			}
			else {
				echo "<font class='impact' color='red'><strong>&#10005;</strong></font>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";
	}
	else {
		echo "<font color='red'><strong>No search results found.</strong></font>";
		echo ' <form method="GET" action="' . $_SERVER['PHP_SELF'] . '"><button type="submit" class="cool2" name="refresh">&#x021BA;</button></form>';
	}
}
if ($_SESSION['admin_category'] == "users") {
?>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="text" name="search2" placeholder="Search ID/name/email">
		<button type="submit" name="content_search" class='cool2'>Search</button>
		<button type="submit" class="cool2" name="refresh">&#x021BA;</button>
	</form>
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="submit" name="toggle" value="Toggle publish status of checked users" class='cool2'>
	<br /><br />
	<?php
	if (ISSET($_SESSION['searchfor']) and $_SESSION['searchfor'] != null) {
		echo "<p>Searching for <font class='impact' color='green'>" . $_SESSION['searchfor'] . "</font></p>";
	}
	if (mysqli_num_rows($search_result) != 0) {
		echo "<table>";
		echo "<tr><td></td><td class='impact' style='text-align: left'>User ID</td><td class='impact' style='text-align: center'>Username</td><td class='impact' style='text-align: center'>Email</td><td class='impact'>Level</td><td class='impact'>Join date</td><td class='impact' style='text-align: left'>Able to login</td></tr>";
		while ($row = mysqli_fetch_array($search_result)) {
			echo "<tr><td class='tdsick'>";
			if ($_SESSION['user_id'] != $row['user_id'] and $row['xtreme_master'] != 1) {
				?>
					<input type="checkbox" name="select[]" value="<?php echo $row['user_id']; ?>">
					<?php
					}
					?>
					<button type="submit" class="cool2" name="edit_pwd" value="<?php echo $row['user_id']; ?>" title="Edit password">&#9998;</button>
			<?php
			echo "</td><td class='tdsick'>" . $row['user_id'] . "</td>";
			if ($row['banned'] == 1) {
				echo "<td class='tdsick' style='text-align: center'>" . htmlspecialchars($row['username']) . "</td>";
			}
			else {
				echo "<td class='tdsick' style='text-align: center'><a href='profile.php?user_prof=" . $row['user_id'] . "'>" . htmlspecialchars($row['username']) . "</a></td>";
			}
			echo "<td class='tdsick'><font size='2'>" . htmlspecialchars($row['email']) . "</font></td>";
			if ($row['level'] == 0) {
				$type = "<font color='CC5D26' class='impact'>User</font>";
			}
			else {
				$type = "<font color='blue' class='impact'>Admin</font>";
			}
			echo "<td class='tdsick'><font size='2'>" . $type . "</font></td>";
			echo "<td class='tdsick'><font size='2'>" . $row['join_date'] . "</font></td>";
			echo "<td class='tdsick' style='text-align: center'>";
			if ($row['banned'] == 0) {
				echo "<font class='impact' color='green'><strong>&check;</strong></font>";
			}
			else {
				echo "<font class='impact' color='red'><strong>&#10005;</strong></font>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";
	}
	else {
		echo "<font color='red'><strong>No search results found.</strong></font>";
		echo ' <form method="GET" action="' . $_SERVER['PHP_SELF'] . '"><button type="submit" class="cool2" name="refresh">&#x021BA;</button></form>';
	}
}
?>

</div></div>

<!--Error message fancy popup-->
<div id="message" style="width:1000px;display: none;">
	<p>
		<?php
		if (ISSET($blog_msg)) {
			echo $blog_msg;
		}
		if (ISSET($loop)) {
			echo "<font color='red'><strong>" . mysqli_num_rows($cats_result) . " categories found for search</strong></font><i> " . $_GET['search'] . "</i><br /><font class='impact'>Which one would you like to choose?</font>";
		?>
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<?php
			while ($row = mysqli_fetch_array($cats_result)) {
			?>
				<button type="submit" class="cool" name="def_category" value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></button>
			<?php
			}
			echo "</form>";
		}
		
		if (ISSET($cats)) {
		?>
			<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<?php
			while ($row = mysqli_fetch_array($cats_result)) {
			?>
				<button type="submit" class="cool" name="def_category" value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></button>
			<?php
			}
			echo "</form>";
		
		}
		if (ISSET($_SESSION['writing'])) {
			echo "<h2 class='impact'>Writing new post</h2>";
			include("editor.php");
		}
		elseif ($_SESSION['edit_blog'] != -1) {
			echo "<h2 class='impact'>Editing blog with ID " . $_SESSION['edit_blog'] . "</h2>";
			include("editor.php");
		}
		elseif (ISSET($_SESSION['blogs_id'])) {
			echo "<h2 class='impact'>Commenting on blog ID with " . $_SESSION['blogs_id'] . "</h2>";
			include("comments.php");
		}
		elseif (ISSET($_SESSION['new_category']) or ISSET($_SESSION['cat_id'])) {
			if (ISSET($_SESSION['new_category'])) {
				echo "<h2 class='impact'>Creating new category...</h2>";
			}
			else {
				if ($_SESSION['admin_category'] == "categories") {
					echo "<h2 class='impact'>Editing category with ID " . $_SESSION['cat_id'] . "</h2>";
				}
				else {
					echo "<h2 class='impact'>Editing comment with ID " . $_SESSION['cat_id'] . "</h2>";
				}
			}
			include("comments.php");
		}
		elseif (ISSET($_SESSION['passy'])) {
			?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="text" name="pwd_input" placeholder="New password"><br />
				<input type="submit" name="send_pwd" value="Do the new password, admin">
			</form>
			<?php
			if (ISSET($pwderror)) {
				echo "<br />" . $pwderror;
			}
		}
		?>
	</p>
</div>

</body>
</html>
