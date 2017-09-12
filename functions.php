<?php
require_once("db_connect.php");

//signup
$msg = null;
if (ISSET($_POST['submit1'])) {
	if ($_POST['username'] != null and $_POST['pwd'] != null and $_POST['confirm_pwd'] != null) {
		if ($_POST['pwd'] == $_POST['confirm_pwd']) {
			$error = false;
			$select = "SELECT `username` FROM `users`";
			$result = mysqli_query($dbc, $select) or DIE("Query error!");
			while ($row = mysqli_fetch_array($result)) {
				if (trim($_POST['username']) == $row['username']) {
					$error = true;
					$msg = "<font color='red'><strong>That username is unavailable! Please choose another one.</strong></font>";
					?>
					<!--Open fancybox again if error in signup-->
					<script type="text/javascript">
						window.jQuery(document).ready(function() {
							$.fancybox.open('#signup');
						});
					</script>
					<?php
					break 1;
				}
			}
			if ($error == false) {
				$query = "INSERT INTO `blog`.`users` (`user_id`, `username`, `pwd`) VALUES (NULL, '" . trim($_POST['username']) . "', '" . md5($_POST['pwd']) . "')";
				mysqli_query($dbc, $query) or DIE("Dang! Query error2, bro");
				$_SESSION['user_id'] = mysqli_insert_id($dbc);
				$_SESSION['username'] = $_POST['username'];
			}
		}
		else {
			$msg = "<font color='red'><strong>Passwords do not match.</strong></font>";
			?>
			<!--Open fancybox again if error in signup-->
			<script type="text/javascript">
				window.jQuery(document).ready(function() {
					$.fancybox.open('#signup');
				});
			</script>
		<?php
		}
	
	}
	else {
		$msg = "<font color='red'><strong>Please enter all fields.</strong></font>";
		?>
		<!--Open fancybox again if error in signup-->
		<script type="text/javascript">
			window.jQuery(document).ready(function() {
				$.fancybox.open('#signup');
			});
		</script>
		<?php
	}
}

//login
$msg2 = null;
if (ISSET($_POST['submit2'])) {
	if ($_POST['username'] != null and $_POST['pwd'] != null) {
		$error = true;
		$select = "SELECT * FROM `users`";
		$result = mysqli_query($dbc, $select) or DIE("Query error!");
		while ($row = mysqli_fetch_array($result)) {
			if ($_POST['username'] == $row['username']) {
				if (md5($_POST['pwd']) == $row['pwd']) {
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['username'] = $row['username'];
					$error = false;
				}
			}
		}
		if ($error == true) {
			$msg2 = "<font color='red'><strong>Username and password did not match.</strong></font>";
			?>
			<script type="text/javascript">
				window.jQuery(document).ready(function() {
					$.fancybox.open('#login');
				});
			</script>
			<?php
		}
	}
	else {
		$msg2 = "<font color='red'><strong>Please enter all fields.</strong></font>";
		?>
		<!--Open fancybox again if error in login-->
		<script type="text/javascript">
			window.jQuery(document).ready(function() {
				$.fancybox.open('#login');
			});
		</script>
		<?php
	}
}

//logout
if (ISSET($_POST['logout'])) {
	unset($_SESSION);
	session_destroy();
	header("Location:-index.php");
}

//redirect to make a post screen if clicked
if (ISSET($_POST['topost'])) {
	header("Location:make_post.php");
}

//go back to index
if (ISSET($_POST['back'])) {
	header("Location:-index.php");
	unset($_SESSION['title']);
	unset($_SESSION['content']);
}
//make posts
if (ISSET($_POST['post'])) {
	if ($_POST['title'] != null and $_POST['editor1'] != null) {
		if (strlen($_POST['editor1'] > 2000)) {
			$error = true;
			$post_msg = "<font color='red'><strong>Your post is too long.</strong></font>";
		}
		if (!ISSET($error)) {
			$insert = "INSERT INTO `blog`.`blogs` (`user_id`, `category_id`, `title`, `content`, `timestamp`) VALUES ('" . $_SESSION['user_id'] . "', '" . $_POST['category'] . "', '" . $_POST['title'] . "', '" . $_POST['editor1'] . "', NOW())";
			mysqli_query($dbc, $insert) or DIE("COuld not insert blog into database.");
			//unset withstanding title and content
			if ($_SESSION['title'] != null) {
				unset($_SESSION['title']);
			}
			if ($_SESSION['content'] != null) {
				unset($_SESSION['content']);
			}
			$post_msg = "<font color='green'><strong>Your blog was posted!</strong></font>";
		}
	}
	else {
		//save what user wrote in case of error
		$_SESSION['content'] = $_POST['editor1'];
		$_SESSION['title'] = $_POST['title'];
		$post_msg = "<font color='red'><strong>Oops, it looks like you didn't input all information.</strong></font>";
	}
}

//set withstanding input data in make a post screen (in case of error or something)
if (!ISSET($_SESSION['title'])) {
	$_SESSION['title'] = null;
}
if (!ISSET($_SESSION['content'])) {
	$_SESSION['content'] = null;
}

if (!ISSET($_SESSION['category_id'])) {
	$_SESSION['category_id'] = 3;
}

//search blog posts builder

//category search
if (ISSET($_GET['category_search'])) {
	if ($_GET['search'] != null) {
		$search = mysqli_real_escape_string($dbc, $_GET['search']);
		$find_category = "SELECT `category_id`, `category_name` FROM `blog`.`categories` WHERE `category_name` LIKE '%" . $search . "%'";
		$result = mysqli_query($dbc, $find_category) or DIE("Could not find category.");
		$x = 0; //count number of results
		$ids = array();
		$names = array();
		while ($row = mysqli_fetch_array($result)) {
			$x++;
			$ids[] = $row['category_id'];
			$names[] = $row['category_name'];
		}
		if ($x == 1) { //set view to searched category when 1 result matched
			$_SESSION['category_id'] = reset($ids);
		}
		elseif ($x > 1) { //ask which category user wants to view containing search with multiple matches
		
		}
		else {
			$blog_msg = "<font color='red'><strong>Could not find any categories names containing</strong></font><i> " . $_GET['search'] . "</i><br /><br />Why not try searching for something else?";
			?>
			<!--activate blog message-->
			<script type="text/javascript">
				window.jQuery(document).ready(function() {
					$.fancybox.open('#message');
				});
			</script>
			<?php
		}
	}
}

//if viewing category that's not all, only show posts from that category using category id
if ($_SESSION['category_id'] != 3) {
	$where = "WHERE `categories`.`category_id`=" . $_SESSION['category_id'];
}
else {
	$where = null;
}

//content search
if (ISSET($_GET['content_search'])) {
	if ($_GET['search2'] != null) {
		$search = trim(mysqli_real_escape_string($dbc, $_GET['search2']));
		//append onto where clause
		if ($where == null) {
			$find_content = "WHERE `content` LIKE '%" . $search . "%'";
		}
		else {
			$find_content = " AND `content` LIKE '%" . $search . "%'";
		}
		$where = $where . $find_content;
	}
}

//order by
if (ISSET($_GET['age'])) {
	$order = $_GET['age'];
}
else {
	$order = "DESC";
}

$search_blogs = "SELECT `blogs`.`title`, `blogs`.`content`, `blogs`.`timestamp`, `users`.`username`, `categories`.`category_name` FROM `blog`.`blogs` INNER JOIN `blog`.`users` ON `blogs`.`user_id`=`users`.`user_id` INNER JOIN `categories` ON `blogs`.`category_id`=`categories`.`category_id` " . $where . " ORDER BY `blogs`.`blog_id` " . $order;
$search_result = mysqli_query($dbc, $search_blogs) or DIE("Could not get blogs.");

//get category info
$select_info = "SELECT `category_name`, `category_desc` FROM `categories` WHERE `category_id`=" . $_SESSION['category_id'];
$info_result = mysqli_query($dbc, $select_info) or DIE("Could not get category info.");
while ($row = mysqli_fetch_array($info_result)) {
	$category_name = $row['category_name'];
	$category_desc = $row['category_desc'];
}

//get 8 random categories for user to check out
$rand_select = "SELECT `category_name` FROM `categories` ORDER BY RAND() LIMIT 8";
$rand_result = mysqli_query($dbc, $rand_select) or DIE("could not get random categories");
?>